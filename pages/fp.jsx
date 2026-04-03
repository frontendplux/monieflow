import React, { useState } from 'react';
import { Link } from 'react-router-dom';

const ForgotPassword = () => {
  const [step, setStep] = useState(1); // 1: Identify, 2: Verification Sent

  return (
    <div className="container-fluid p-0" style={{ backgroundColor: "#f0f2f5" }}>
      
      {/* SIMPLE HEADER (Amazon Style) */}
      <nav className="py-2 px-4 shadow-sm bg-white border-bottom">
        <div className="d-flex align-items-center justify-content-between mx-auto" style={{ maxWidth: "1000px" }}>
          <h2 className="fw-bold mb-0 m-0" style={{ color: "#232f3e", fontSize: "medium" }}>
            <span className='bi bi-chevron-left btn btn-light fw-bold' onClick={()=>history.back()}></span> monie<span className="fw-bold text-warning">Flow.</span>
          </h2>
          <Link 
            className="btn fw-bold px-3 py-1" 
            style={{ backgroundColor: "#ffd814", borderRadius: "4px", fontSize: "0.9rem" }}
            to="/login"
          >
            Log In
          </Link>
        </div>
      </nav>

      <div className="flex-grow-1 d-flex align-items-center justify-content-center p-3">
        <div className="card border-0 shadow-sm" style={{ width: "100%", maxWidth: "500px", borderRadius: "8px" }}>
          
          <div className="card-header bg-white p-3 border-bottom">
            <h5 className="fw-bold mb-0">Find Your Account</h5>
          </div>

          <div className="card-body p-4">
            {step === 1 ? (
              <>
                <p className="text-muted" style={{ fontSize: "0.95rem" }}>
                  Please enter your email address or mobile number to search for your account.
                </p>
                <div className="mb-4">
                  <input 
                    type="text" 
                    className="form-control form-control-lg border-1" 
                    placeholder="Email or mobile number"
                    style={{ fontSize: "1rem", borderRadius: "4px" }}
                  />
                </div>
                
                <div className="d-flex justify-content-end gap-2 pt-3 border-top">
                  <Link to="/login"
                    className="btn btn-light fw-bold px-4" 
                    style={{ borderRadius: "4px", backgroundColor: "#e4e6eb" }}
                  >
                    Cancel
                  </Link>
                  <button 
                    className="btn fw-bold px-4" 
                    style={{ backgroundColor: "#f0c14b", borderColor: "#a88734", borderRadius: "4px" }}
                    onClick={() => setStep(2)}
                  >
                    Search
                  </button>
                </div>
              </>
            ) : (
              <div className="text-center py-3">
                <div className="fs-1 mb-3">📧</div>
                <h5 className="fw-bold">Check your inbox</h5>
                <p className="text-muted">
                  We've sent a 6-digit security code to <b>j***@email.com</b>. Enter it below to verify your identity.
                </p>
                <div className="mb-4 mx-auto" style={{ maxWidth: "200px" }}>
                  <input 
                    type="text" 
                    className="form-control form-control-lg text-center fw-bold" 
                    placeholder="000000"
                    maxLength="6"
                    style={{ letterSpacing: "5px", borderRadius: "4px" }}
                  />
                </div>
                <button 
                  className="btn w-100 fw-bold mb-3" 
                  style={{ backgroundColor: "#f0c14b", borderRadius: "4px" }}
                >
                  Continue
                </button>
                <div className="small">
                  <a href="#" className="text-decoration-none fw-bold" style={{ color: "#007185" }}>Didn't get a code?</a>
                </div>
              </div>
            )}
          </div>
          
          {/* HELP BOX (Amazon Service Style) */}
          <div className="p-3 bg-light border-top text-center" style={{ borderBottomLeftRadius: "8px", borderBottomRightRadius: "8px" }}>
            <span className="small text-muted">Need more help? </span>
            <a href="#" className="small fw-bold text-decoration-none" style={{ color: "#007185" }}>
               Contact Amazon Support 🛠️
            </a>
          </div>
        </div>
      </div>

      {/* MINIMAL FOOTER */}
      <footer className="py-4 text-center mt-auto" style={{ fontSize: "0.75rem", color: "#888" }}>
        <div className="d-flex justify-content-center gap-4 mb-2">
          <span>Privacy Notice</span>
          <span>Conditions of Use</span>
          <span>Help</span>
        </div>
        <div>© 2026, Amazon Social, Inc.</div>
      </footer>
    </div>
  );
};

export default ForgotPassword;