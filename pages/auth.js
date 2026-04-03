import { Auth } from "../helperJs/helperauth.js";
import { posturlserver } from "../helperJs/helperfeeds.js";
import router from "../helperJs/helperRouter.js";
import { toggleNav } from "../helperJs/mainhelper.js";

export function visibilityToggle() {
   let visibilityToggle = false;
            document.getElementById('togglePassword').addEventListener('click', () => {
            const input = document.getElementById('password');
            const icon = document.getElementById('toggleIcon');

            visibilityToggle = !visibilityToggle;

            input.type = visibilityToggle ? 'text' : 'password';

            icon.classList.toggle('bi-eye');
            icon.classList.toggle('bi-eye-slash');
            });
}


export async function isloggedin(){
    const id= localStorage.getItem('id') ?? 0 ;
    const token = sessionStorage.getItem('token') ?? '' ;
    const req=await fetch(posturlserver,{
        method:"POST",
        headers:{"Content-Type":"application/json"},
        body:JSON.stringify({action:'isloggedin',id: id,token: token})
      }).then(res=>res.text());
      // console.log(req);
      const res=JSON.parse(req);
     return res.success;
}


export default function Login() {
  // Attach logic after DOM is rendered

  window.Logins = async function () {
    const auth = new Auth();
    const email = document.getElementById('email').value.trim();
    const password = document.getElementById('password').value.trim();
    const btn = document.getElementById('btn-login');
    btn.disabled = true;
    btn.innerHTML = '<i class="spinner-border spinner-border-sm"></i> Logging in...';
    // Handle login
      if (!email || !password) {
        toggleNav('cyan', 'authentication error', 'Please fill in all fields');
        return;
      }
      const res=await auth.loginer(email, password);
      if (res.success) {
          localStorage.setItem('user_id', res.user);
          sessionStorage.setItem('token', res.user.split('-')[0]); // Store token in sessionStorage
          localStorage.setItem('id', res.user.split('-')[1]); // Store user ID in localStorage
          window.location.href = '/home';
        } else {
          toggleNav('cyan', 'authentication error', res.message || 'Login failed');
        }
        btn.disabled = false;
        btn.innerHTML = 'Log In';
  };


  // Return HTML string
  return `
    <div class="container-fluid h-100 p-0 overflow-hidden">
      <div class="row g-0 h-100">
        <!-- LEFT SIDE -->
        <div class="col-lg-6 d-none d-lg-block position-relative">
          <div style="position:absolute;top:0;left:0;width:100%;height:100%;
                      background:linear-gradient(45deg,rgba(35, 47, 62, 0.04),rgba(35, 47, 62, 0));z-index:1"></div>
          <img src="/uploads/nm.webp"
               alt="Workspace" class="w-100 h-100 object-fit-cover">
          <div class="position-absolute bottom-0 start-0 p-5 text-white" style="z-index:2">
           <!-- <h1 class="display-4 fw-bold mb-3">Professional <span style="color:#febd69">Connect</span></h1> 
            <p class="fs-5 opacity-75">Access your corporate ecosystem and social network in one premium dashboard.</p>-->
          </div>
        </div>

        <!-- RIGHT SIDE -->
        <div class="col-lg-6 d-flex align-items-center justify-content-center p-4">
          <div class="w-100" style="max-width:420px">
            <div class="text-center mb-5">
              <h2 class="fw-light" style="color:#232f3e;letter-spacing:-1px">
                monie<span class="fw-bold text-warning">Flow.</span>
              </h2>
            </div>

            <div class="card border-0 shadow-sm p-4 pt-5" style="border-radius:8px">
              <div id="loginForm">
                <div class="mb-3">
                  <input type="text" id="email" class="form-control p-3 form-control-lg border-1"
                         placeholder="Username, email or phone" style="font-size:1rem;border-radius:4px">
                </div>
                <div class="mb-3 position-relative">
                  <input type="password" id="password" class="form-control p-3 form-control-lg border-1"
                         placeholder="Password" style="font-size:1rem;border-radius:4px">
               <span id="togglePassword"
                      class="position-absolute top-50 end-0 translate-middle-y me-3"
                      style="cursor:pointer">
                  <i id="toggleIcon" class="bi bi-eye"></i>
                </span>
                </div>
                <div id="errorBox" class="alert alert-danger py-2 small d-none"></div>

                <button id="btn-login" onclick="Logins()" class="btn btn-lg w-100 p-3 fw-bold mb-3"
                        style="background-color:#f0c14b;border-color:#a88734 #9c7e31 #846a29;
                               color:#111;font-size:1rem;border-radius:4px">
                  Log In
                </button>

                <div class="text-center mb-3">
                  <a data-link href="/forgot-password" class="text-decoration-none p-3 small" style="color:#007185">Forgotten password?</a>
                </div>

                <hr class="my-4">

                <div class="text-center">
                  <a data-link href="/register" class="btn fw-bold px-4 p-3"
                     style="background-color:#42b72a;color:white;border-radius:6px">
                    Create New Account
                  </a>
                </div>
              </div>
            </div>

            <div class="mt-4 text-center">
              <p class="small text-muted">
                <a href="/create-page" class="text-decoration-none text-dark"><strong>Create a Page</strong></a>
                for a celebrity, brand or business.
              </p>
            </div>
          </div>
        </div>
      </div>
    </div>
  `;
}



export const SignupPage = () => {

  window.handleSignup = async () => {
    const AuthAPI = new Auth();
    const firstname = document.getElementById('firstname').value.trim();
    const lastname = document.getElementById('lastname').value.trim();
    const email = document.getElementById('email').value.trim();
    const phone = document.getElementById('phone').value.trim();
    const password = document.getElementById('password').value.trim();
    const gender = document.querySelector('input[name="gender"]:checked')?.value;

    const dob = {
      day: document.getElementById('dob-day').value,
      month: document.getElementById('dob-month').value,
      year: document.getElementById('dob-year').value
    };
    const btn = document.getElementById('btn-signup');
    btn.disabled = true;
    btn.innerHTML = `<span class="spinner-border spinner-border-sm"></span> Loading...`;

    if (!firstname || !lastname || !email || !phone || !password || !gender || !dob.day || !dob.month || !dob.year) {
      toggleNav('red', 'Authentication Error', 'Please fill in all fields');
      btn.disabled = false;
      btn.innerHTML = 'Sign Up';
      return;
    }

    if (!/\S+@\S+\.\S+/.test(email)) {
      toggleNav('red', 'Authentication Error', 'Invalid email format');
      btn.disabled = false;
      btn.innerHTML = 'Sign Up';
      return;
    }

   if (!/^\+\d{10,15}$/.test(phone)) {
  toggleNav('red', 'Authentication Error', 'Invalid phone number');
  btn.disabled = false;
  btn.innerHTML = 'Sign Up';
  return;
}

    if (password.length < 6) {
      toggleNav('red', 'Authentication Error', 'Password must be at least 6 characters');
      btn.disabled = false;
      btn.innerHTML = 'Sign Up';
      return;
    }

    const result = await AuthAPI.register(firstname,lastname, email,phone, password,`${dob.day}-${dob.month}-${dob.year}`,gender);
      if (result.success) {
            toggleNav('green', 'Success', 'Account created successfully');
              localStorage.setItem('user_id', result.user);
              sessionStorage.setItem('token', result.user.split('-')[0]);
              localStorage.setItem('id', result.user.split('-')[1]);
             router('/home');
      }
        else {
             toggleNav('red', 'Authentication Error', result.message || 'Signup failed');
        }
      btn.disabled = false;
      btn.innerHTML = 'Sign Up';
  };

  const days = Array.from({ length: 31 }, (_, i) => `<option value="${i+1}">${i+1}</option>`).join('');
  const years = Array.from({ length: 100 }, (_, i) => `<option value="${2026 - i}">${2026 - i}</option>`).join('');
  const months = [
    ["Jan","01"],["Feb","02"],["Mar","03"],["Apr","04"],["May","05"],["Jun","06"],
    ["Jul","07"],["Aug","08"],["Sep","09"],["Oct","10"],["Nov","11"],["Dec","12"]
  ].map(([l,v]) => `<option value="${v}">${l}</option>`).join('');

  const genders = ['Female','Male','Custom'].map(label => `
    <div class="flex-grow-1 border p-2 rounded bg-light d-flex justify-content-between align-items-center">
      <label class="small fw-bold mb-0">${label}</label>
      <input value="${label}" type="radio" name="gender" />
    </div>
  `).join('');

  return `
    <div class="container-fluid p-3 min-vh-100 d-flex flex-column align-items-center justify-content-center" style="background-color:#f0f2f5;">
      
      <div class="mb-4 text-center">
        <h1 class="fw-light" style="color:#232f3e;">
          monie<span class="fw-bold text-warning">Flow.</span>
        </h1>
      </div>

      <div class="card border-0 shadow-sm w-100" style="max-width:450px;">
        <div class="card-body p-4">

          <div class="text-center mb-4">
            <h4 class="fw-bold mb-1">Create a new account</h4>
            <p class="text-muted small">It's quick and easy.</p>
          </div>

          <hr class="mb-4"/>

          <div class="row g-2 mb-3">
            <div class="col">
              <input id="firstname" class="form-control bg-light" placeholder="Firstname"/>
            </div>
            <div class="col">
              <input id="lastname" class="form-control bg-light" placeholder="Surname"/>
            </div>
          </div>

          <div class="mb-3">
            <input id="email" type="email" class="form-control bg-light" placeholder="Email"/>
          </div>

          <div class="mb-3">
            <input id="phone" type="tel" class="form-control bg-light" placeholder="Phone"/>
          </div>

          <div class="mb-3 position-relative">
            <input id="password" type="password" class="form-control bg-light" placeholder="Password"/>
            <span id="togglePassword" class="position-absolute top-50 end-0 translate-middle-y me-3" style="cursor:pointer">
              <i class="bi bi-eye" id="toggleIcon"></i>
            </span>
          </div>

          <div class="mb-3">
            <label class="small fw-bold text-muted">Date of birth</label>
            <div class="row g-2">
              <div class="col">
                <select id="dob-day" class="form-select bg-light">
                  <option value="">Day</option>${days}
                </select>
              </div>
              <div class="col">
                <select id="dob-month" class="form-select bg-light">
                  <option value="">Month</option>${months}
                </select>
              </div>
              <div class="col">
                <select id="dob-year" class="form-select bg-light">
                  <option value="">Year</option>${years}
                </select>
              </div>
            </div>
          </div>

          <div class="mb-4">
            <label class="small fw-bold text-muted">Gender</label>
            <div class="d-flex gap-2">${genders}</div>
          </div>

          <div class="mb-4 small text-muted text-center">
            By clicking Sign Up, you agree to Terms, Privacy Policy and Cookies Policy.
          </div>

          <div class="text-center">
            <button id="btn-signup" onclick="handleSignup()" class="btn btn-warning w-75 fw-bold">
              Sign Up
            </button>
          </div>

          <div class="text-center mt-4 pt-3 border-top">
            <a data-link href="/login" class="fw-bold text-decoration-none">Already have an account?</a>
          </div>

        </div>
      </div>
      <footer class="mt-5 text-center" style="max-width:800px;font-size:0.75rem;color:#888">
        <div class="d-flex flex-wrap justify-content-center gap-3 mb-2">
          <span>&copy; 2023 MonieFlow. All rights reserved.</span>
          <!--<span>English (UK)</span><span>Français (France)</span><span>Português (Brasil)</span><span>Italiano</span>-->
        </div>
      </footer>
    </div>
  `;
};


export const ForgotPassword = () => {

  let step = 1;

  // expose globally (like your other pages)
  window.goToStep = (nextStep) => {
    step = nextStep;
    document.getElementById('forgot-root').innerHTML = renderContent();
  };

  const renderContent = () => {
    if (step === 1) {
      return `
        <p class="text-muted" style="font-size:0.95rem">
          Please enter your email address or mobile number to search for your account.
        </p>

        <div class="mb-4">
          <input type="text" class="form-control form-control-lg" placeholder="Email or mobile number">
        </div>

        <div class="d-flex justify-content-end gap-2 pt-3 border-top">
          <a data-link href="/login" class="btn btn-light fw-bold px-4">Cancel</a>
          <button class="btn fw-bold px-4" style="background-color:#f0c14b" onclick="goToStep(2)">
            Search
          </button>
        </div>
      `;
    }

    return `
      <div class="text-center py-3">
        <div class="fs-1 mb-3">📧</div>
        <h5 class="fw-bold">Check your inbox</h5>
        <p class="text-muted">
          We've sent a 6-digit security code to <b>j***@email.com</b>.
        </p>

        <div class="mb-4 mx-auto" style="max-width:200px">
          <input type="text" class="form-control form-control-lg text-center fw-bold" 
                 placeholder="000000" maxlength="6" style="letter-spacing:5px">
        </div>

        <button class="btn w-100 fw-bold mb-3" style="background-color:#f0c14b">
          Continue
        </button>

        <div class="small">
          <a href="#" class="text-decoration-none fw-bold">Didn't get a code?</a>
        </div>
      </div>
    `;
  };

  return `
    <div class="container-fluid p-0 h-100 d-flex flex-column" style="background-color:#f0f2f5">
      
      <!-- HEADER -->
      <nav class="py-2 px-4 shadow-sm bg-white border-bottom sticky-top">
        <div class="d-flex align-items-center justify-content-between mx-auto" style="max-width:1000px">
          
          <h2 class="fw-bold m-0" style="color:#232f3e; font-size:medium">
            <span class="bi bi-chevron-left btn btn-light fw-bold" onclick="history.back()"></span>
           <!-- monie<span class="fw-bold text-warning">Flow.</span> -->
          </h2>

          <a data-link href="/login" class="btn fw-bold px-3 py-1" style="background:#ffd814">
            Log In
          </a>
        </div>
      </nav>

      <!-- BODY -->
      <div class="d-flex align-items-center justify-content-center p-3">
        <div class="card border-0 shadow-sm w-100" style="max-width:500px">
          
          <div class="card-header bg-white p-3 border-bottom">
            <h5 class="fw-bold mb-0">Find Your Account</h5>
          </div>

          <div id="forgot-root" class="card-body p-4">
            ${renderContent()}
          </div>

          <div class="p-3 bg-light border-top text-center">
            <span class="small text-muted">Need more help? </span>
            <a href="#" class="small fw-bold text-decoration-none">
              Contact Support 🛠️
            </a>
          </div>

        </div>
      </div>

      <!-- FOOTER -->
      <footer class="text-center  small text-muted">
        <div class="d-flex justify-content-center gap-4 mb-2">
          <span>Privacy Notice</span>
          <span>Conditions of Use</span>
          <span>Help</span>
        </div>
        <div>© 2026, monieFlow</div>
      </footer>

    </div>
  `;
};