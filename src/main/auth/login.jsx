import { useState } from "react";
import { Link, useNavigate } from "react-router-dom";
// Make sure you have Remix Icon CSS imported globally in your project
// e.g. in index.html: <link href="https://cdn.jsdelivr.net/npm/remixicon/fonts/remixicon.css" rel="stylesheet">
// export const server = window.location.hostname === "localhost"
//   ? "http://localhost:3000"
//   : window.location.origin;

export default function Login({server}) {
  const [email, setEmail] = useState("");
  const [password, setPassword] = useState("");
  const [errors, setErrors] = useState({ email: "", password: "", validation:"" });
  const [loading, setLoading] = useState(false);
  const navigation=useNavigate();

  const validate = () => {
    let newErrors = { email: "", password: "", validate:""};
    setErrors(prev =>({...prev, validation :''}));
    if (!email) {
      newErrors.email = "Email is required";
    } else if (!/\S+@\S+\.\S+/.test(email)) {
      newErrors.email = "Invalid email format";
    }

    if (!password) {
      newErrors.password = "Password is required";
    } else if (password.length < 6) {
      newErrors.password = "Password must be at least 6 characters";
    }
    setErrors(newErrors);
    return !newErrors.email && !newErrors.password;
  };

 const handleLogin = async () => {
  if (!validate()) return;
  setLoading(true);

  try {
    const response = await fetch(`${server}/api/auth/`, {
      method: "POST",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify({ email, password }), // send an object
    });

    // handle non-2xx responses
    if (!response.ok) {
      const text = await response.text().catch(() => "");
      throw new Error(text || "Network response was not ok");
    }

    const data = await response.json();
    console.log(data);

    if (data.success === true) {
      navigation("/member");
    } else {
      setErrors(prev => ({ ...prev, validation: data.message || "Invalid credentials" }));
    }
  } catch (err) {
    setErrors(prev => ({ ...prev, validation: err.message }));
  } finally {
    setLoading(false);
  }
};


  const isFormValid = email && password && !errors.email && !errors.password;

  return (
    <div className="position-fixed top-0 start-0 w-100 h-100 overflow-auto bg-light">
      

      <div className="container d-sm-flex h-100 justify-content-center align-items-center">
        <div className="d-sm-flex mx-auto col-sm-10">
          <div className="col-sm-7 p-3">
            <h1
              className="text-primary text-center text-sm-start fw-bolder text-capitalize"
              style={{ fontSize: "xx-large" }}
            >
              monieflow
            </h1>
            <div className="fs-3 text-center text-sm-start">
              Monieflow helps you connect and share with the people in your life.
            </div>
          </div>
          <div className="col-sm-5">
            <div className="col-sm-11 mx-auto p-3 bg-white shadow rounded">
              {/* Email input */}
              <div className="mb-3 position-relative">
                <input
                  type="text"
                  className={`form-control p-3 ${
                    errors.email ? "border border-danger" : ""
                  }`}
                  placeholder="Email or phone number"
                  value={email}
                  onChange={(e) => setEmail(e.target.value)}
                  onBlur={validate}
                />
                {errors.email && (
                  <small className="text-danger d-flex align-items-center mt-1">
                    <i className="ri-information-line me-1"></i>
                    {errors.email}
                  </small>
                )}
              </div>

              {/* Password input */}
              <div className="mb-3 position-relative">
                <input
                  type="password"
                  className={`form-control p-3 ${
                    errors.password ? "border border-danger" : ""
                  }`}
                  placeholder="Password"
                  value={password}
                  onChange={(e) => setPassword(e.target.value)}
                  onBlur={validate}
                />
                {errors.password && (
                  <small className="text-danger d-flex align-items-center mt-1">
                    <i className="ri-information-line me-1"></i>
                    {errors.password}
                  </small>
                )}
              </div>

              {/* Login button with loading state */}
              <button
                className="btn btn-primary fw-bold text-capitalize w-100 p-3 mb-3"
                onClick={handleLogin}
                disabled={!isFormValid || loading}
              >
                {loading ? (
                  <>
                    <i className="spinner-border spinner-border-sm ri-spin me-2"></i> Logging in...
                  </>
                ) : (
                  "Log In"
                )}
              </button>
              <div className="text-center text-danger"> 
                {errors.validation && (
                  <small className="text-danger  mt-1">
                    <i className="ri-information-line me-1"></i>
                    {errors.validation}
                  </small>
                )}
              </div>

              <div className="text-center">
                <Link
                  to="/forget-password"
                  className="text-center text-decoration-none text-primary mb-3 mx-auto"
                  style={{ cursor: "pointer" }}
                >
                  Forgotten password?
                </Link>
              </div>
              <hr />
              <div className="text-center">
                <Link to="/signup" className="btn btn-success px-5 p-3 mt-3">
                  Create New Account
                </Link>
              </div>
            </div>
            <div className="text-center mt-3 text-muted">
              <Link
                className="text-decoration-none text-dark fw-medium"
                to="/create-page"
              >
                Create a Page
              </Link>{" "}
              for a celebrity, brand or business.
            </div>
          </div>
        </div>
      </div>
    </div>
  );
}
