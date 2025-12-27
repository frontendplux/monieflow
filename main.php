<!DOCTYPE html>
<?php
include __DIR__."/config/function.php";
$main = new Main($conn);
if ($main->isLoggedIn()) {
    header("Location: /home.php");
    exit;
} 
?>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<title>MonieFlow</title>

<style>
* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
    font-family: "Segoe UI", Arial, sans-serif;
}

html, body {
    height: 100%;
}

/* Layout */
.container {
    display: flex;
    height: 100%;
}

.brand {
    max-width: 560px;
    animation: fadeUp 1.2s ease;
}

.brand h1 {
    font-size: 2.8rem;
    margin-bottom: 12px;
}

.brand p {
    font-size: 1rem;
    line-height: 1.6;
    color: #eee;
    margin-bottom: 25px;
}

/* CTA BUTTON */
.brand .cta {
    display: inline-block;
    padding: 13px 38px;
    background: linear-gradient(135deg, #ff4d4d, #b30000);
    color: white;
    text-decoration: none;
    border-radius: 30px;
    font-weight: 600;
    transition: .3s;
}

.brand .cta:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 20px rgba(255,77,77,.45);
}

/* RIGHT SIDE */
.right {
    background:
        linear-gradient(rgba(0,0,0,.75), rgba(110,15,15,.6)),
        url("1.jpg") center/cover no-repeat;
    display: flex;
    align-items: center;
    justify-content: center;
    flex: 1;
}

.login-box {
    width: 100%;
    max-width: 480px;
    background: #161616df;
    padding:20px 15px;
    border-radius: 18px;
    box-shadow: 0 15px 40px rgba(0,0,0,.6);
    animation: fadeUp 1.3s ease;
}

.login-box h2 {
    color: white;
    margin-bottom: 22px;
    text-align: center;
}

.login-box input {
    width: 100%;
    padding: 14px;
    margin-bottom: 15px;
    border-radius: 10px;
    border: none;
    background: #222;
    color: white;
    outline: none;
}

.login-box input::placeholder {
    color: #aaa;
}

.login-box button {
    width: 100%;
    padding: 14px;
    border-radius: 30px;
    border: none;
    background: linear-gradient(135deg, #ff4d4d, #b30000);
    color: white;
    font-weight: 600;
    cursor: pointer;
    transition: .3s;
}

.login-box button:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 20px rgba(255,77,77,.45);
}

.login-box .small {
    margin-top: 15px;
    text-align: center;
    font-size: .85rem;
    color: #bbb;
}

.login-box .small a {
    color: #ff4d4d;
    text-decoration: none;
}


/* Animation */
@keyframes fadeUp {
    from { opacity: 0; transform: translateY(30px); }
    to   { opacity: 1; transform: translateY(0); }
}
</style>
</head>

<body>
<div class="container-fluid  d-flex h-100 p-0">

    <div class="right p-2">
        <div class="login-box">
            <h2>Welcome Back</h2>
            <input type="email" id="username" placeholder="Email address">
            <input type="password" id="password" placeholder="Password">
            <button onclick="login(this)">Login</button>
            <div class="small">
                Don’t have an account? <a href="/signup.php">Sign up</a>
            </div>
        </div>
    </div>

</div>
</body>
<script src="/script.js"></script>
<script>
function login(e) {
    showLoading();
    const username = document.getElementById('username').value.trim();
    const password = document.getElementById('password').value.trim();

    if (!username || !password) {
        alert("Both fields are required!");
        hideLoading();
        return;
    }

    const fd = new FormData();
    fd.append("username", username);
    fd.append("password", password);
    fd.append("action", "login");

    fetch("/req.php", {
        method: "POST",
        body: fd
    })
    .then(res => res.json())
    .then(data => {
        if (data[0]) {
            window.location.href = "/home.php";
        } else {
            alert(data[1] || "Login failed");
        }
    })
    .catch(err => console.error("Error:", err))
    .finally(() => hideLoading());
}
</script>
</html>
