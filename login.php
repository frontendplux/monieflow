<?php include __DIR__."/config/function.php"; 
$main=new main($conn);
if($main->isLoggedIn())return header('location:/home.php');
?>
<?php include __DIR__."/headers/header.php"; ?>
<style>


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
    /* background:
        linear-gradient(rgba(0,0,0,.75), rgba(110,15,15,.6)),
        url("1.jpg") center/cover no-repeat; */
    display: flex;
    align-items: center;
    justify-content: center;
    flex: 1;
    padding: 10px 20px;
}

.login-box {
    width: 100%;
    max-width: 580px;
    background: white;
    padding:20px 15px;
    border-radius: 18px;
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
    background: whitesmoke;
    color: black;
    outline: none;
}

.login-box input::placeholder {
    color: black;
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
</style>
<div>
    <div class="right p-2">
        <div class="login-box mt-3 mt-sm-5">
            <h2 class="text-black">Welcome Back</h2>
            <input type="email" id="username" placeholder="Email address">
            <input type="password" id="password" placeholder="Password">
            <button onclick="login(this)">Login</button>
            <div class="small">
                Don’t have an account? <a href="/signup.php">Sign up</a>
            </div>
        </div>
    </div>
</div>
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
