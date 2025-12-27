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

<title>MonieFlow – Create Account</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

<style>
*{
    margin:0;
    padding:0;
    box-sizing:border-box;
    font-family:"Segoe UI",Arial,sans-serif;
}

html,body{
    height:100%;
}

body{
    background:
        linear-gradient(rgba(0,0,0,.75),rgba(110,15,15,.6)),
        url("1.jpg") center/cover no-repeat;
}

/* Center Box */
.wrapper{
    height:100%;
    display:flex;
    align-items:center;
    justify-content:center;
    padding:15px;
}

.login-box{
    width:100%;
    max-width:480px;
    background:rgba(22,22,22,.92);
    padding:30px 25px;
    border-radius:18px;
    box-shadow:0 15px 40px rgba(0,0,0,.6);
    animation:fadeUp 1s ease;
}

.login-box h2{
    text-align:center;
    color:#fff;
    margin-bottom:20px;
}

.login-box input{
    width:100%;
    padding:14px;
    border-radius:10px;
    border:none;
    background:#222;
    color:#fff;
    margin-bottom:15px;
    outline:none;
}

.login-box input::placeholder{
    color:#aaa;
}

.login-box button{
    width:100%;
    padding:14px;
    border-radius:30px;
    border:none;
    background:linear-gradient(135deg,#ff4d4d,#b30000);
    color:#fff;
    font-weight:600;
    cursor:pointer;
    transition:.3s;
}

.login-box button:hover{
    transform:translateY(-2px);
    box-shadow:0 8px 20px rgba(255,77,77,.45);
}

.login-box .small{
    margin-top:15px;
    text-align:center;
    font-size:.85rem;
    color:#bbb;
}

.login-box a{
    color:#ff4d4d;
    text-decoration:none;
}

/* Animation */
@keyframes fadeUp{
    from{opacity:0;transform:translateY(25px);}
    to{opacity:1;transform:translateY(0);}
}
</style>
</head>

<body>

<div class="wrapper">
    <div class="login-box">

        <h2>Create Account</h2>

        <input type="text" id="username" placeholder="Username">
        <input type="email" id="email" placeholder="Email address">
        <input type="password" id="password" placeholder="Password">
        <input type="password" id="confirmPassword" placeholder="Confirm Password">

        <div class="form-check text-white mb-3">
            <input class="form-check-input me-1" style="width:max-content; height: max-content; padding: 8px;" type="checkbox" id="agree">
            <label class="form-check-label" for="agree">
                I agree to the <a href="#">Terms & Conditions</a>
            </label>
        </div>

        <button onclick="signup()">Create Account</button>

        <div class="small mt-3 fs-5 d-flex justify-content-center gap-4">
            <a href="#">Forgot Password?</a> ·
            <a href="/main.php">Log in</a>
        </div>

    </div>
</div>

<script src="/script.js"></script>

<script>
function signup(){
    showLoading?.();

    const username = document.getElementById("username").value.trim();
    const email    = document.getElementById("email").value.trim();
    const password = document.getElementById("password").value.trim();
    const confirm  = document.getElementById("confirmPassword").value.trim();
    const agree    = document.getElementById("agree").checked;

    if(!username || !email || !password || !confirm){
        alert("All fields are required");
        hideLoading?.();
        return;
    }

    if(password.length < 6){
        alert("Password must be at least 6 characters");
        hideLoading?.();
        return;
    }

    if(password !== confirm){
        alert("Passwords do not match");
        hideLoading?.();
        return;
    }

    if(!agree){
        alert("You must agree to the Terms & Conditions");
        hideLoading?.();
        return;
    }

    const fd = new FormData();
    fd.append("username",username);
    fd.append("email",email);
    fd.append("password",password);
    fd.append("action","signup");

    fetch("/req.php",{
        method:"POST",
        body:fd
    })
    .then(res=>res.json())
    .then(data=>{
        if(data.success){
            window.location.href="/confirm-pass.php";
        }else{
            alert(data.message || "Signup failed");
        }
    })
    .catch(()=>{
        alert("Network error. Try again.");
    })
    .finally(()=>{
        hideLoading?.();
    });
}
</script>

</body>
</html>
