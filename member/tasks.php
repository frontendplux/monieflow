<?php
session_start();
include __DIR__ . '/../conn.php';

// 1. Session & Authentication Guard
$suid = $_SESSION['suid'] ?? null;
$token = $_SESSION['token'] ?? null;

if (!$suid || !$token) {
    header("Location: /index.php");
    exit();
}

// Fetch user data
$stmt = $conn->prepare("SELECT * FROM users WHERE uid = ? AND token = ? AND is_active = TRUE");
$stmt->bind_param("ss", $suid, $token);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();

if (!$user) {
    session_unset();
    session_destroy();
    header("Location: /index.php");
    exit();
}

// -----------------------------------------------------------------------------
// IP Geolocation & Exchange Rate Lookup
// -----------------------------------------------------------------------------
function getUserCountryCode() {
    $ip = $_SERVER['REMOTE_ADDR'] ?? '';
    if ($ip === '127.0.0.1' || $ip === '::1' || strpos($ip, '192.168.') === 0) {
        return 'NG';
    }
    
    $geoUrl = "http://ip-api.com/json/" . $ip . "?fields=countryCode";
    $ctx = stream_context_create(['http' => ['timeout' => 2]]);
    $geoData = @file_get_contents($geoUrl, false, $ctx);
    
    if ($geoData) {
        $json = json_decode($geoData, true);
        if (isset($json['countryCode'])) {
            return strtoupper($json['countryCode']);
        }
    }
    return 'NG';
}

$userCountryCode = getUserCountryCode();

$rateStmt = $conn->prepare("SELECT * FROM monieflow_coin_values WHERE country_code = ? LIMIT 1");
$rateStmt->bind_param("s", $userCountryCode);
$rateStmt->execute();
$rateData = $rateStmt->get_result()->fetch_assoc();

if (!$rateData) {
    $fallbackStmt = $conn->query("SELECT * FROM monieflow_coin_values WHERE country_code = 'NG' LIMIT 1");
    $rateData = $fallbackStmt->fetch_assoc();
}

$currencyCode = $rateData['currency_code'] ?? 'NGN';
$mfExchangeRate = floatval($rateData['amount'] ?? 1.00);

// Fetch User Wallet Balance
$walletStmt = $conn->prepare("SELECT balance FROM wallets WHERE uid = ?");
$walletStmt->bind_param("s", $user['uid']);
$walletStmt->execute();
$wallet = $walletStmt->get_result()->fetch_assoc();
$userBalanceMF = floatval($wallet['balance'] ?? 0);

// Handle Actions
$msg = '';
$msgType = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    // -------------------------------------------------------------------------
    // Action 1: Create Task (Client locks total escrow)
    // -------------------------------------------------------------------------
    if ($action === 'create_task') {
        $title = trim($_POST['title'] ?? '');
        $description = trim($_POST['description'] ?? '');
        $proof_required = trim($_POST['proof_required'] ?? '');
        $fiat_reward = floatval($_POST['fiat_reward'] ?? 0);
        $max_submissions = intval($_POST['max_submissions'] ?? 1);

        if (!empty($title) && !empty($description) && !empty($proof_required) && $fiat_reward > 0 && $max_submissions > 0) {
            $reward_mf = $fiat_reward / $mfExchangeRate;
            $total_escrow_mf = $reward_mf * $max_submissions;

            if ($userBalanceMF >= $total_escrow_mf) {
                $conn->begin_transaction();
                try {
                    // Deduct Total Escrow Balance from Client
                    $deductStmt = $conn->prepare("UPDATE wallets SET balance = balance - ? WHERE uid = ? AND balance >= ?");
                    $deductStmt->bind_param("dsd", $total_escrow_mf, $user['uid'], $total_escrow_mf);
                    $deductStmt->execute();

                    if ($deductStmt->affected_rows > 0) {
                        $taskStmt = $conn->prepare("INSERT INTO tasks (client_uid, title, description, proof_required, fiat_reward, currency_code, reward_mf, max_submissions, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, 'active')");
                        $taskStmt->bind_param("ssssdsdi", $user['uid'], $title, $description, $proof_required, $fiat_reward, $currencyCode, $reward_mf, $max_submissions);
                        $taskStmt->execute();

                        $txDesc = "Escrow locked for Task: " . $title;
                        $txStmt = $conn->prepare("INSERT INTO transaction (uid, amount, type, description) VALUES (?, ?, 'debit', ?)");
                        $txStmt->bind_param("sds", $user['uid'], $total_escrow_mf, $txDesc);
                        $txStmt->execute();

                        $conn->commit();
                        $msg = "Task created! Escrow funds locked until you verify worker submissions.";
                        $msgType = "success";
                    } else {
                        throw new Exception("Insufficient wallet balance.");
                    }
                } catch (Exception $e) {
                    $conn->rollback();
                    $msg = "Failed to create task: " . $e->getMessage();
                    $msgType = "danger";
                }
            } else {
                $msg = "Insufficient MF balance. Required: " . number_format($total_escrow_mf, 4) . " MF";
                $msgType = "danger";
            }
        } else {
            $msg = "Please fill in all required task details correctly.";
            $msgType = "warning";
        }
    }

    // -------------------------------------------------------------------------
    // Action 2: Submit Proof (Enters 'pending' state awaiting promoter review)
    // -------------------------------------------------------------------------
    if ($action === 'submit_task') {
        $task_id = intval($_POST['task_id'] ?? 0);
        $proof_text = trim($_POST['proof_text'] ?? '');

        if ($task_id > 0 && !empty($proof_text)) {
            $chkStmt = $conn->prepare("
                SELECT t.*, 
                (SELECT COUNT(*) FROM task_submissions WHERE task_id = t.id) as completed_count
                FROM tasks t WHERE t.id = ? AND t.status = 'active'
            ");
            $chkStmt->bind_param("i", $task_id);
            $chkStmt->execute();
            $taskData = $chkStmt->get_result()->fetch_assoc();

            if ($taskData) {
                if ($taskData['client_uid'] === $user['uid']) {
                    $msg = "You cannot perform tasks created by yourself.";
                    $msgType = "danger";
                } else {
                    $subCheck = $conn->prepare("SELECT id FROM task_submissions WHERE task_id = ? AND member_uid = ?");
                    $subCheck->bind_param("is", $task_id, $user['uid']);
                    $subCheck->execute();

                    if ($subCheck->get_result()->num_rows > 0) {
                        $msg = "You have already submitted proof for this task.";
                        $msgType = "warning";
                    } elseif ($taskData['completed_count'] >= $taskData['max_submissions']) {
                        $msg = "This task has reached its maximum completion limit.";
                        $msgType = "warning";
                    } else {
                        // Record submission as PENDING approval
                        $subStmt = $conn->prepare("INSERT INTO task_submissions (task_id, member_uid, proof_text, status) VALUES (?, ?, ?, 'pending')");
                        $subStmt->bind_param("iss", $task_id, $user['uid'], $proof_text);
                        
                        if ($subStmt->execute()) {
                            $msg = "Proof submitted successfully! Funds will be released once the promoter confirms your proof.";
                            $msgType = "success";
                        } else {
                            $msg = "Failed to submit proof. Please try again.";
                            $msgType = "danger";
                        }
                    }
                }
            } else {
                $msg = "Invalid or inactive task.";
                $msgType = "danger";
            }
        }
    }

    // -------------------------------------------------------------------------
    // Action 3: Promoter Confirms & Releases Funds to Member
    // -------------------------------------------------------------------------
    if ($action === 'approve_submission') {
        $submission_id = intval($_POST['submission_id'] ?? 0);

        if ($submission_id > 0) {
            $apprStmt = $conn->prepare("
                SELECT ts.*, t.client_uid, t.reward_mf, t.title, t.max_submissions, t.id as tid
                FROM task_submissions ts 
                JOIN tasks t ON ts.task_id = t.id 
                WHERE ts.id = ? AND ts.status = 'pending'
            ");
            $apprStmt->bind_param("i", $submission_id);
            $apprStmt->execute();
            $subData = $apprStmt->get_result()->fetch_assoc();

            if ($subData && $subData['client_uid'] === $user['uid']) {
                $conn->begin_transaction();
                try {
                    // Update Submission Status to Approved
                    $upSub = $conn->prepare("UPDATE task_submissions SET status = 'approved' WHERE id = ?");
                    $upSub->bind_param("i", $submission_id);
                    $upSub->execute();

                    // Credit Member Wallet from Escrow
                    $rewardMF = floatval($subData['reward_mf']);
                    $creditStmt = $conn->prepare("UPDATE wallets SET balance = balance + ? WHERE uid = ?");
                    $creditStmt->bind_param("ds", $rewardMF, $subData['member_uid']);
                    $creditStmt->execute();

                    // Transaction Record for Member
                    $txDesc = "Task reward received for: " . $subData['title'];
                    $txStmt = $conn->prepare("INSERT INTO transaction (uid, amount, type, description) VALUES (?, ?, 'credit', ?)");
                    $txStmt->bind_param("sds", $subData['member_uid'], $rewardMF, $txDesc);
                    $txStmt->execute();

                    // Check if all slots are filled and approved
                    $countApproved = $conn->prepare("SELECT COUNT(*) as approved_count FROM task_submissions WHERE task_id = ? AND status = 'approved'");
                    $countApproved->bind_param("i", $subData['tid']);
                    $countApproved->execute();
                    $appCount = $countApproved->get_result()->fetch_assoc()['approved_count'] ?? 0;

                    if ($appCount >= $subData['max_submissions']) {
                        $markDone = $conn->prepare("UPDATE tasks SET status = 'completed' WHERE id = ?");
                        $markDone->bind_param("i", $subData['tid']);
                        $markDone->execute();
                    }

                    $conn->commit();
                    $msg = "Submission approved! Reward of " . number_format($rewardMF, 4) . " MF released to worker.";
                    $msgType = "success";
                } catch (Exception $e) {
                    $conn->rollback();
                    $msg = "Approval failed: " . $e->getMessage();
                    $msgType = "danger";
                }
            } else {
                $msg = "Unauthorized or submission is no longer pending.";
                $msgType = "danger";
            }
        }
    }

    // -------------------------------------------------------------------------
    // Action 4: Promoter Rejects Submission
    // -------------------------------------------------------------------------
    if ($action === 'reject_submission') {
        $submission_id = intval($_POST['submission_id'] ?? 0);

        if ($submission_id > 0) {
            $rejStmt = $conn->prepare("
                SELECT ts.*, t.client_uid 
                FROM task_submissions ts 
                JOIN tasks t ON ts.task_id = t.id 
                WHERE ts.id = ? AND ts.status = 'pending'
            ");
            $rejStmt->bind_param("i", $submission_id);
            $rejStmt->execute();
            $subData = $rejStmt->get_result()->fetch_assoc();

            if ($subData && $subData['client_uid'] === $user['uid']) {
                $upSub = $conn->prepare("UPDATE task_submissions SET status = 'rejected' WHERE id = ?");
                $upSub->bind_param("i", $submission_id);
                
                if ($upSub->execute()) {
                    $msg = "Submission rejected.";
                    $msgType = "info";
                }
            }
        }
    }

    // -------------------------------------------------------------------------
    // Action 5: Open Dispute
    // -------------------------------------------------------------------------
    if ($action === 'open_dispute') {
        $submission_id = intval($_POST['submission_id'] ?? 0);
        $reason = trim($_POST['reason'] ?? '');

        if ($submission_id > 0 && !empty($reason)) {
            $dispChk = $conn->prepare("
                SELECT ts.*, t.client_uid 
                FROM task_submissions ts 
                JOIN tasks t ON ts.task_id = t.id 
                WHERE ts.id = ?
            ");
            $dispChk->bind_param("i", $submission_id);
            $dispChk->execute();
            $subData = $dispChk->get_result()->fetch_assoc();

            if ($subData && ($subData['client_uid'] === $user['uid'] || $subData['member_uid'] === $user['uid'])) {
                $existDisp = $conn->prepare("SELECT id FROM task_disputes WHERE submission_id = ?");
                $existDisp->bind_param("i", $submission_id);
                $existDisp->execute();

                if ($existDisp->get_result()->num_rows === 0) {
                    $conn->begin_transaction();
                    try {
                        $updateSub = $conn->prepare("UPDATE task_submissions SET status = 'disputed' WHERE id = ?");
                        $updateSub->bind_param("i", $submission_id);
                        $updateSub->execute();

                        $dispStmt = $conn->prepare("INSERT INTO task_disputes (submission_id, opened_by_uid, reason) VALUES (?, ?, ?)");
                        $dispStmt->bind_param("iss", $submission_id, $user['uid'], $reason);
                        $dispStmt->execute();

                        $conn->commit();
                        $msg = "Dispute opened. Admin will inspect proof and render a decision.";
                        $msgType = "info";
                    } catch (Exception $e) {
                        $conn->rollback();
                        $msg = "Dispute error: " . $e->getMessage();
                        $msgType = "danger";
                    }
                } else {
                    $msg = "Dispute case already pending for this submission.";
                    $msgType = "warning";
                }
            }
        }
    }
}

// Fetch Active Tasks
$tasksStmt = $conn->prepare("
    SELECT t.*, u.username as client_name,
    (SELECT COUNT(*) FROM task_submissions WHERE task_id = t.id) as current_submissions,
    (SELECT COUNT(*) FROM task_submissions WHERE task_id = t.id AND member_uid = ?) as user_completed
    FROM tasks t 
    JOIN users u ON t.client_uid = u.uid 
    WHERE t.status = 'active'
    ORDER BY t.created_at DESC
");
$tasksStmt->bind_param("s", $user['uid']);
$tasksStmt->execute();
$availableTasks = $tasksStmt->get_result()->fetch_all(MYSQLI_ASSOC);

// Fetch User Submissions (Worker View)
$mySubmissionsStmt = $conn->prepare("
    SELECT ts.*, t.title, t.reward_mf
    FROM task_submissions ts 
    JOIN tasks t ON ts.task_id = t.id 
    WHERE ts.member_uid = ? 
    ORDER BY ts.submitted_at DESC
");
$mySubmissionsStmt->bind_param("s", $user['uid']);
$mySubmissionsStmt->execute();
$mySubmissions = $mySubmissionsStmt->get_result()->fetch_all(MYSQLI_ASSOC);

// Fetch Created Tasks & Incoming Submissions (Promoter View)
$myTasksStmt = $conn->prepare("
    SELECT t.*, 
    (SELECT COUNT(*) FROM task_submissions WHERE task_id = t.id) as total_submissions
    FROM tasks t 
    WHERE t.client_uid = ? 
    ORDER BY t.created_at DESC
");
$myTasksStmt->bind_param("s", $user['uid']);
$myTasksStmt->execute();
$myCreatedTasks = $myTasksStmt->get_result()->fetch_all(MYSQLI_ASSOC);

$incomingSubmissionsStmt = $conn->prepare("
    SELECT ts.*, t.title as task_title, t.reward_mf, u.username as member_name
    FROM task_submissions ts
    JOIN tasks t ON ts.task_id = t.id
    JOIN users u ON ts.member_uid = u.uid
    WHERE t.client_uid = ? AND ts.status = 'pending'
    ORDER BY ts.submitted_at ASC
");
$incomingSubmissionsStmt->bind_param("s", $user['uid']);
$incomingSubmissionsStmt->execute();
$pendingReviewSubmissions = $incomingSubmissionsStmt->get_result()->fetch_all(MYSQLI_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MonieFlow - Tasks & Earn</title>
    
    <link rel="icon" type="image/png" href="/logo.png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">

    <style>
        :root {
            --bg-whitesmoke: #f4f9fc;
            --brand-skyblue: #00a8e8;
            --brand-skyblue-hover: #008cc3;
            --card-white: #ffffff;
            --text-dark: #1e293b;
            --text-muted: #64748b;
        }

        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: var(--bg-whitesmoke);
            color: var(--text-dark);
            min-height: 100vh;
            padding-bottom: 80px;
        }

        @media (min-width: 992px) { body { padding-bottom: 20px; } }

        .custom-card {
            background-color: var(--card-white);
            border: 1px solid rgba(0, 168, 232, 0.15);
            border-radius: 1rem;
            padding: 1.25rem;
        }

        .btn-skyblue {
            background-color: var(--brand-skyblue);
            color: #ffffff; font-weight: 600;
        }

        .btn-skyblue:hover {
            background-color: var(--brand-skyblue-hover);
            color: #ffffff;
        }

        .nav-pills .nav-link.active {
            background-color: var(--brand-skyblue);
        }

        .mobile-bottom-nav {
            position: fixed;
            bottom: 0; left: 0; right: 0;
            background: #ffffff;
            border-top: 1px solid rgba(0, 168, 232, 0.15);
            z-index: 1030;
        }

        .mobile-bottom-nav .nav-link { color: var(--text-muted); font-size: 0.72rem; padding: 8px 0; text-align: center; }
        .mobile-bottom-nav .nav-link.active { color: var(--brand-skyblue); }
        .mobile-bottom-nav i { font-size: 1.25rem; display: block; }
    </style>
</head>
<body>

    <nav class="navbar navbar-expand-lg bg-white border-bottom sticky-top">
        <div class="container">
            <a class="navbar-brand d-flex align-items-center gap-2 fw-bold" href="/member/index.php">
                <img src="/logo.png" alt="MonieFlow" style="width: 40px; height: 40px; background: #008cc3; border-radius: 50%; padding: 4px;">
                <span style="color: var(--brand-skyblue-hover);">MonieFlow</span>
            </a>
            <a href="/member/index.php" class="btn btn-outline-secondary btn-sm rounded-pill px-3">
                <i class="bi bi-arrow-left me-1"></i> Dashboard
            </a>
        </div>
    </nav>

    <main class="container py-4">

        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-3">
            <div>
                <h1 class="h3 fw-bold mb-1">Tasks Marketplace</h1>
                <p class="text-muted small mb-0">Complete tasks to earn MF coins, or promote your tasks with escrow protection.</p>
            </div>
            <div>
                <button class="btn btn-skyblue rounded-3 px-4 py-2" data-bs-toggle="modal" data-bs-target="#createTaskModal">
                    <i class="bi bi-plus-lg me-1"></i> Create Task
                </button>
            </div>
        </div>

        <?php if (!empty($msg)): ?>
            <div class="alert alert-<?= $msgType ?> alert-dismissible fade show mb-4" role="alert">
                <?= htmlspecialchars($msg) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <ul class="nav nav-pills mb-4 gap-2 border-bottom pb-2" id="taskTabs" role="tablist">
            <li class="nav-item">
                <button class="nav-link active fw-semibold" id="browse-tab" data-bs-toggle="tab" data-bs-target="#browse" type="button">
                    <i class="bi bi-grid me-1"></i> Browse Tasks
                </button>
            </li>
            <li class="nav-item">
                <button class="nav-link fw-semibold" id="history-tab" data-bs-toggle="tab" data-bs-target="#history" type="button">
                    <i class="bi bi-clock-history me-1"></i> My Submissions
                </button>
            </li>
            <li class="nav-item">
                <button class="nav-link fw-semibold position-relative" id="my-created-tab" data-bs-toggle="tab" data-bs-target="#my-created" type="button">
                    <i class="bi bi-folder2-open me-1"></i> Promoter Center
                    <?php if (count($pendingReviewSubmissions) > 0): ?>
                        <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                            <?= count($pendingReviewSubmissions) ?>
                        </span>
                    <?php endif; ?>
                </button>
            </li>
        </ul>

        <div class="tab-content" id="taskTabsContent">

            <!-- TAB 1: AVAILABLE TASKS -->
            <div class="tab-pane fade show active" id="browse" role="tabpanel">
                <div class="row g-3">
                    <?php if (!empty($availableTasks)): ?>
                        <?php foreach ($availableTasks as $t): ?>
                            <div class="col-12 col-md-6">
                                <div class="custom-card h-100 d-flex flex-column justify-content-between">
                                    <div>
                                        <div class="d-flex justify-content-between align-items-start mb-2">
                                            <h5 class="fw-bold mb-0 text-truncate" style="max-width: 70%;"><?= htmlspecialchars($t['title']) ?></h5>
                                            <span class="badge bg-success-subtle text-success border border-success-subtle fw-semibold fs-6">
                                                +<?= number_format($t['reward_mf'], 4) ?> MF
                                            </span>
                                        </div>
                                        <div class="small text-muted mb-2">
                                            <i class="bi bi-person me-1"></i> Promoter: <?= htmlspecialchars($t['client_name']) ?> | 
                                            <i class="bi bi-cash me-1"></i> Value: <?= htmlspecialchars($t['currency_code']) ?> <?= number_format($t['fiat_reward'], 2) ?>
                                        </div>
                                        <p class="small text-secondary mb-3"><?= nl2br(htmlspecialchars($t['description'])) ?></p>
                                        
                                        <div class="p-2 bg-light rounded-2 mb-3">
                                            <small class="fw-semibold text-dark d-block">Proof Required:</small>
                                            <small class="text-muted d-block"><?= htmlspecialchars($t['proof_required']) ?></small>
                                        </div>
                                    </div>

                                    <div class="d-flex align-items-center justify-content-between border-top pt-3 mt-2">
                                        <span class="small text-muted">
                                            Submissions: <?= $t['current_submissions'] ?> / <?= $t['max_submissions'] ?>
                                        </span>
                                        <?php if ($t['user_completed'] > 0): ?>
                                            <button class="btn btn-sm btn-secondary" disabled><i class="bi bi-check-all"></i> Submitted</button>
                                        <?php elseif ($t['client_uid'] === $user['uid']): ?>
                                            <button class="btn btn-sm btn-outline-secondary" disabled>Your Task</button>
                                        <?php else: ?>
                                            <button class="btn btn-sm btn-skyblue px-3" data-bs-toggle="modal" data-bs-target="#submitTaskModal<?= $t['id'] ?>">
                                                Submit Proof
                                            </button>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>

                            <!-- Submit Task Proof Modal -->
                            <div class="modal fade" id="submitTaskModal<?= $t['id'] ?>" tabindex="-1" aria-hidden="true">
                                <div class="modal-dialog modal-dialog-centered">
                                    <div class="modal-content">
                                        <form method="POST" action="">
                                            <input type="hidden" name="action" value="submit_task">
                                            <input type="hidden" name="task_id" value="<?= $t['id'] ?>">

                                            <div class="modal-header">
                                                <h5 class="modal-title fw-bold">Submit Task Proof</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body">
                                                <p class="small text-muted mb-3"><strong>Task:</strong> <?= htmlspecialchars($t['title']) ?></p>
                                                <div class="mb-3">
                                                    <label class="form-label fw-semibold small">Required Proof Guidelines:</label>
                                                    <p class="small text-secondary bg-light p-2 rounded"><?= htmlspecialchars($t['proof_required']) ?></p>
                                                </div>
                                                <div class="mb-3">
                                                    <label for="proof_text_<?= $t['id'] ?>" class="form-label fw-semibold small">Your Proof Details:</label>
                                                    <textarea class="form-control" name="proof_text" id="proof_text_<?= $t['id'] ?>" rows="4" placeholder="Provide link, handle, screenshot info..." required></textarea>
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                                                <button type="submit" class="btn btn-skyblue">Submit Proof</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>

                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="col-12 text-center py-5">
                            <i class="bi bi-inbox fs-1 text-muted mb-2 d-block"></i>
                            <p class="text-muted mb-0">No active tasks available. Check back soon!</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- TAB 2: MY WORKER SUBMISSIONS -->
            <div class="tab-pane fade" id="history" role="tabpanel">
                <div class="custom-card">
                    <h6 class="fw-bold mb-3"><i class="bi bi-journal-check me-2 text-primary"></i>My Submitted Tasks</h6>
                    <?php if (!empty($mySubmissions)): ?>
                        <div class="table-responsive">
                            <table class="table align-middle mb-0">
                                <thead>
                                    <tr class="text-muted small">
                                        <th>Task</th>
                                        <th>Submitted Proof</th>
                                        <th>Reward</th>
                                        <th>Status</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($mySubmissions as $sub): ?>
                                        <tr>
                                            <td class="fw-semibold"><?= htmlspecialchars($sub['title']) ?></td>
                                            <td class="small text-muted" style="max-width: 200px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
                                                <?= htmlspecialchars($sub['proof_text']) ?>
                                            </td>
                                            <td class="fw-bold text-success">+<?= number_format($sub['reward_mf'], 4) ?> MF</td>
                                            <td>
                                                <?php if ($sub['status'] === 'approved'): ?>
                                                    <span class="badge bg-success-subtle text-success border border-success-subtle">Approved & Paid</span>
                                                <?php elseif ($sub['status'] === 'pending'): ?>
                                                    <span class="badge bg-warning-subtle text-warning border border-warning-subtle">Awaiting Promoter Approval</span>
                                                <?php elseif ($sub['status'] === 'disputed'): ?>
                                                    <span class="badge bg-info-subtle text-info border border-info-subtle">Under Admin Dispute</span>
                                                <?php else: ?>
                                                    <span class="badge bg-danger-subtle text-danger">Rejected</span>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <?php if ($sub['status'] !== 'disputed'): ?>
                                                    <button class="btn btn-sm btn-outline-danger" data-bs-toggle="modal" data-bs-target="#disputeModal<?= $sub['id'] ?>">
                                                        Raise Dispute
                                                    </button>
                                                <?php else: ?>
                                                    <span class="small text-muted">In Dispute</span>
                                                <?php endif; ?>
                                            </td>
                                        </tr>

                                        <!-- Dispute Modal -->
                                        <div class="modal fade" id="disputeModal<?= $sub['id'] ?>" tabindex="-1" aria-hidden="true">
                                            <div class="modal-dialog modal-dialog-centered">
                                                <div class="modal-content">
                                                    <form method="POST" action="">
                                                        <input type="hidden" name="action" value="open_dispute">
                                                        <input type="hidden" name="submission_id" value="<?= $sub['id'] ?>">

                                                        <div class="modal-header">
                                                            <h5 class="modal-title fw-bold">Open Submission Dispute</h5>
                                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                        </div>
                                                        <div class="modal-body">
                                                            <p class="small text-muted mb-3">Task: <strong><?= htmlspecialchars($sub['title']) ?></strong></p>
                                                            <div class="mb-3">
                                                                <label class="form-label fw-semibold small">Dispute Reason:</label>
                                                                <textarea class="form-control" name="reason" rows="3" placeholder="Explain why this proof should be approved..." required></textarea>
                                                            </div>
                                                        </div>
                                                        <div class="modal-footer">
                                                            <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                                                            <button type="submit" class="btn btn-danger">Submit Dispute</button>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>

                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php else: ?>
                        <div class="text-center py-4">
                            <i class="bi bi-card-checklist fs-2 text-muted mb-2 d-block"></i>
                            <p class="text-muted mb-0 small">No submission history found.</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- TAB 3: PROMOTER CENTER (Verify Submissions & Manage Tasks) -->
            <div class="tab-pane fade" id="my-created" role="tabpanel">
                
                <!-- Pending Approvals Section -->
                <div class="custom-card mb-4 border-primary">
                    <h6 class="fw-bold text-primary mb-3"><i class="bi bi-clock-history me-2"></i>Submissions Requiring Your Confirmation</h6>
                    <?php if (!empty($pendingReviewSubmissions)): ?>
                        <div class="table-responsive">
                            <table class="table align-middle">
                                <thead>
                                    <tr class="text-muted small">
                                        <th>Task</th>
                                        <th>Worker</th>
                                        <th>Proof Details</th>
                                        <th>Reward</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($pendingReviewSubmissions as $ps): ?>
                                        <tr>
                                            <td class="fw-semibold"><?= htmlspecialchars($ps['task_title']) ?></td>
                                            <td><?= htmlspecialchars($ps['member_name']) ?></td>
                                            <td class="small bg-light p-2 rounded" style="max-width: 250px;"><?= nl2br(htmlspecialchars($ps['proof_text'])) ?></td>
                                            <td class="fw-bold text-primary"><?= number_format($ps['reward_mf'], 4) ?> MF</td>
                                            <td>
                                                <div class="d-flex gap-2">
                                                    <form method="POST" action="" onsubmit="return confirm('Confirm and release payout to this member?');">
                                                        <input type="hidden" name="action" value="approve_submission">
                                                        <input type="hidden" name="submission_id" value="<?= $ps['id'] ?>">
                                                        <button type="submit" class="btn btn-sm btn-success">
                                                            <i class="bi bi-check-lg"></i> Approve & Pay
                                                        </button>
                                                    </form>
                                                    
                                                    <form method="POST" action="" onsubmit="return confirm('Reject this submission?');">
                                                        <input type="hidden" name="action" value="reject_submission">
                                                        <input type="hidden" name="submission_id" value="<?= $ps['id'] ?>">
                                                        <button type="submit" class="btn btn-sm btn-outline-danger">
                                                            <i class="bi bi-x-lg"></i> Reject
                                                        </button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php else: ?>
                        <p class="text-muted small mb-0">No pending submissions awaiting your approval.</p>
                    <?php endif; ?>
                </div>

                <!-- Created Tasks List -->
                <div class="custom-card">
                    <h6 class="fw-bold mb-3"><i class="bi bi-briefcase me-2 text-primary"></i>My Published Tasks</h6>
                    <?php if (!empty($myCreatedTasks)): ?>
                        <div class="table-responsive">
                            <table class="table align-middle mb-0">
                                <thead>
                                    <tr class="text-muted small">
                                        <th>Title</th>
                                        <th>Fiat Value</th>
                                        <th>Reward per Worker</th>
                                        <th>Progress</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($myCreatedTasks as $ct): ?>
                                        <tr>
                                            <td class="fw-semibold"><?= htmlspecialchars($ct['title']) ?></td>
                                            <td><?= htmlspecialchars($ct['currency_code']) ?> <?= number_format($ct['fiat_reward'], 2) ?></td>
                                            <td class="fw-bold text-primary"><?= number_format($ct['reward_mf'], 4) ?> MF</td>
                                            <td><?= $ct['total_submissions'] ?> / <?= $ct['max_submissions'] ?></td>
                                            <td>
                                                <span class="badge bg-<?= $ct['status'] === 'active' ? 'success' : 'secondary' ?>-subtle text-<?= $ct['status'] === 'active' ? 'success' : 'secondary' ?>">
                                                    <?= ucfirst($ct['status']) ?>
                                                </span>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php else: ?>
                        <div class="text-center py-4">
                            <i class="bi bi-folder-x fs-2 text-muted mb-2 d-block"></i>
                            <p class="text-muted mb-0 small">You haven't created any tasks yet.</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

        </div>

    </main>

    <!-- Create Task Modal -->
    <div class="modal fade" id="createTaskModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <form method="POST" action="">
                    <input type="hidden" name="action" value="create_task">

                    <div class="modal-header">
                        <h5 class="modal-title fw-bold"><i class="bi bi-plus-circle me-2 text-primary"></i>Create New Task</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label fw-semibold small">Task Title</label>
                            <input type="text" name="title" class="form-control" placeholder="e.g., Follow Instagram Page & Like Post" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold small">Task Description & Instructions</label>
                            <textarea name="description" class="form-control" rows="3" placeholder="Explain what the worker must do step-by-step..." required></textarea>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold small">Required Proof</label>
                            <input type="text" name="proof_required" class="form-control" placeholder="e.g., Send your username & screenshot link" required>
                        </div>

                        <div class="row g-2 mb-3">
                            <div class="col-6">
                                <label class="form-label fw-semibold small">Reward Worth (<?= htmlspecialchars($currencyCode) ?>)</label>
                                <input type="number" step="0.01" name="fiat_reward" id="fiatRewardInput" class="form-control" placeholder="0.00" oninput="calculateMFReward()" required>
                            </div>
                            <div class="col-6">
                                <label class="form-label fw-semibold small">Max Workers / Submissions</label>
                                <input type="number" name="max_submissions" id="maxSubmissionsInput" class="form-control" value="10" min="1" oninput="calculateMFReward()" required>
                            </div>
                        </div>

                        <div class="p-3 bg-light rounded-3">
                            <div class="d-flex justify-content-between small mb-1">
                                <span class="text-muted">Reward per Worker:</span>
                                <span class="fw-bold text-dark" id="calcRewardMF">0.0000 MF</span>
                            </div>
                            <div class="d-flex justify-content-between small border-top pt-1 mt-1">
                                <span class="fw-bold text-dark">Total Escrow Required:</span>
                                <span class="fw-bold text-primary" id="calcTotalEscrowMF">0.0000 MF</span>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-skyblue">Publish Task</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Mobile Navigation Bar -->
    <div class="mobile-bottom-nav d-lg-none">
        <div class="container">
            <div class="row text-center g-0">
                <div class="col"><a href="/member/index.php" class="nav-link"><i class="bi bi-house-door"></i><span>Home</span></a></div>
                <div class="col"><a href="/member/chart.php" class="nav-link"><i class="bi bi-graph-up"></i><span>Chart</span></a></div>
                <div class="col"><a href="/member/tasks.php" class="nav-link active"><i class="bi bi-check2-square"></i><span>Tasks</span></a></div>
                <div class="col"><a href="/member/referral.php" class="nav-link"><i class="bi bi-person-plus"></i><span>Referral</span></a></div>
                <div class="col"><a href="/member/settings.php" class="nav-link"><i class="bi bi-gear"></i><span>Settings</span></a></div>
            </div>
        </div>
    </div>

    <script>
        const exchangeRate = <?= json_encode($mfExchangeRate) ?>;

        function calculateMFReward() {
            const fiatReward = parseFloat(document.getElementById('fiatRewardInput').value) || 0;
            const maxSubmissions = parseInt(document.getElementById('maxSubmissionsInput').value) || 1;

            if (fiatReward > 0 && exchangeRate > 0) {
                const rewardMF = fiatReward / exchangeRate;
                const totalEscrow = rewardMF * maxSubmissions;

                document.getElementById('calcRewardMF').innerText = rewardMF.toFixed(4) + " MF";
                document.getElementById('calcTotalEscrowMF').innerText = totalEscrow.toFixed(4) + " MF";
            } else {
                document.getElementById('calcRewardMF').innerText = "0.0000 MF";
                document.getElementById('calcTotalEscrowMF').innerText = "0.0000 MF";
            }
        }
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>