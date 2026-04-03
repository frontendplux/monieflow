import React, { useState } from 'react';

const NewPasswordPage = () => {
  const [showPassword, setShowPassword] = useState(false);

  return (
    <div className="container-fluid vh-100 d-flex flex-column" style={{ backgroundColor: "#f0f2f5" }}>
      
      {/* MINIMAL NAV */}
      <nav className="py-2 px-4 bg-white border-bottom shadow-sm">
        <div className="mx-auto d-flex align-items-center" style={{ maxWidth: "1000px" }}>
          <h2 className="fw-bold mb-0" style={{ color: "#232f3e", fontSize: "1.4rem" }}>
            amazon<span className="fw-light">social</span>
          </h2>
        </div>
      </nav>

      <div className="flex-grow-1 d-flex align-items-center justify-content-center p-3">
        <div className="card border-0 shadow-sm" style={{ width: "100%", maxWidth: "450px", borderRadius: "8px" }}>
          
          <div className="card-header bg-white p-3 border-bottom">
            <h5 className="fw-bold mb-0">Choose a New Password</h5>
          </div>

          <div className="card-body p-4">
            <p className="text-muted small mb-4">
              Create a new password that is at least 8 characters long. A strong password has a mix of letters, numbers and symbols.
            </p>

            <form>
              {/* NEW PASSWORD INPUT */}
              <div className="mb-3 position-relative">
                <label className="small fw-bold text-muted mb-1">New Password</label>
                <input 
                  type={showPassword ? "text" : "password"} 
                  className="form-control form-control-lg border-1" 
                  placeholder="Minimum 8 characters"
                  style={{ fontSize: "1rem", borderRadius: "4px" }}
                />
                <button 
                  type="button"
                  onClick={() => setShowPassword(!showPassword)}
                  className="btn btn-link position-absolute end-0 top-50 mt-1 text-decoration-none small"
                  style={{ color: "#007185" }}
                >
                  {showPassword ? "Hide" : "Show"}
                </button>
              </div>

              {/* CONFIRM PASSWORD */}
              <div className="mb-4">
                <label className="small fw-bold text-muted mb-1">Confirm New Password</label>
                <input 
                  type="password" 
                  className="form-control form-control-lg border-1" 
                  placeholder="Re-type password"
                  style={{ fontSize: "1rem", borderRadius: "4px" }}
                />
              </div>

              {/* PASSWORD STRENGTH INDICATOR (Amazon Quality Detail) */}
              <div className="mb-4 p-2 rounded" style={{ backgroundColor: "#f7fafd", border: "1px solid #d5e9f1" }}>
                <div className="d-flex justify-content-between mb-1">
                  <span className="small fw-bold" style={{ color: "#232f3e" }}>Password Strength:</span>
                  <span className="small fw-bold text-success">Strong</span>
                </div>
                <div className="progress" style={{ height: "6px" }}>
                  <div className="progress-bar bg-success" role="progressbar" style={{ width: "75%" }}></div>
                </div>
              </div>

              {/* SUBMIT ACTION */}
              <div className="d-flex justify-content-end gap-2 pt-3 border-top">
                <button 
                  className="btn btn-light fw-bold px-4" 
                  style={{ borderRadius: "4px", backgroundColor: "#e4e6eb" }}
                >
                  Skip
                </button>
                <button 
                  className="btn fw-bold px-4" 
                  style={{ 
                    backgroundColor: "#f0c14b", 
                    borderColor: "#a88734 #9c7e31 #846a29", 
                    borderRadius: "4px",
                    color: "#111"
                  }}
                >
                  Update Password
                </button>
              </div>
            </form>
          </div>

          {/* SECURITY NOTE */}
          <div className="p-3 text-center bg-light border-top" style={{ borderBottomLeftRadius: "8px", borderBottomRightRadius: "8px" }}>
            <small className="text-muted">
              🔒 <b>Security Tip:</b> Don't use a password you use for other sites.
            </small>
          </div>
        </div>
      </div>
    </div>
  );
};

export default NewPasswordPage;