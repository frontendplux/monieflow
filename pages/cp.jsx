import React, { useState, useRef, useEffect } from 'react';

const ConfirmPasscodePage = () => {
  // State for the 6-digit code
  const [code, setCode] = useState(new Array(6).fill(""));
  const [timer, setTimer] = useState(60);
  const inputRefs = useRef([]);

  // Timer logic for "Resend Code"
  useEffect(() => {
    let interval = null;
    if (timer > 0) {
      interval = setInterval(() => {
        setTimer((prev) => prev - 1);
      }, 1000);
    } else {
      clearInterval(interval);
    }
    return () => clearInterval(interval);
  }, [timer]);

  // Handle typing and auto-focus next
  const handleChange = (e, index) => {
    const value = e.target.value;
    if (isNaN(value)) return; // Only allow numbers

    const newCode = [...code];
    // Take only the last character entered
    newCode[index] = value.substring(value.length - 1);
    setCode(newCode);

    // Move to next input if value is entered
    if (value && index < 5) {
      inputRefs.current[index + 1].focus();
    }
  };

  // Handle backspace to move focus back
  const handleKeyDown = (e, index) => {
    if (e.key === "Backspace" && !code[index] && index > 0) {
      inputRefs.current[index - 1].focus();
    }
  };

  const handleSubmit = (e) => {
    e.preventDefault();
    const finalCode = code.join("");
    console.log("Verifying Code:", finalCode);
    alert(`Verifying Code: ${finalCode}`);
  };

  return (
    <div className="container-fluid vh-100 d-flex flex-column" style={{ backgroundColor: "#f0f2f5", fontFamily: 'Arial, sans-serif' }}>
      
      {/* 1. PREMIUM HEADER (Amazon Navy) */}
      <nav className="py-2 px-4 bg-white border-bottom shadow-sm">
        <div className="mx-auto d-flex align-items-center justify-content-between" style={{ maxWidth: "1000px" }}>
          <h2 className="fw-bold mb-0" style={{ color: "#232f3e", fontSize: "1.4rem", letterSpacing: "-1px" }}>
            amazon<span className="fw-light">social</span>
          </h2>
          <div className="d-flex align-items-center gap-2">
            <span className="badge bg-light text-dark border">Step 2 of 3</span>
            <button className="btn btn-link text-decoration-none small fw-bold" style={{ color: "#007185" }}>Help</button>
          </div>
        </div>
      </nav>

      {/* 2. MAIN CONTENT AREA */}
      <div className="flex-grow-1 d-flex align-items-center justify-content-center p-3">
        <div className="card border-0 shadow-sm" style={{ width: "100%", maxWidth: "450px", borderRadius: "8px" }}>
          
          <div className="card-header bg-white p-3 border-bottom text-center">
            <h5 className="fw-bold mb-0" style={{ color: "#232f3e" }}>Security Verification</h5>
          </div>

          <div className="card-body p-4 text-center">
            {/* Security Icon */}
            <div className="mb-4">
              <div 
                className="d-inline-flex align-items-center justify-content-center mb-3 shadow-sm" 
                style={{ width: "65px", height: "65px", backgroundColor: "#f7fafd", borderRadius: "50%", border: "1px solid #d5e9f1" }}
              >
                <span style={{ fontSize: "1.8rem" }}>🛡️</span>
              </div>
              <h5 className="fw-bold text-dark">Check your device</h5>
              <p className="text-muted small px-3">
                For your security, we've sent a 6-digit verification code to <b>+1 (***) ***-8829</b>.
              </p>
            </div>

            {/* 6-DIGIT INPUT GROUP */}
            <form onSubmit={handleSubmit}>
              <div className="d-flex justify-content-center gap-2 mb-4">
                {code.map((digit, index) => (
                  <input
                    key={index}
                    type="text"
                    ref={(el) => (inputRefs.current[index] = el)}
                    className="form-control text-center fw-bold fs-4"
                    style={{ 
                      width: "48px", 
                      height: "58px", 
                      borderRadius: "6px", 
                      border: "2px solid #ced4da",
                      backgroundColor: "#fff",
                      transition: "border-color 0.2s",
                      outline: "none"
                    }}
                    value={digit}
                    onChange={(e) => handleChange(e, index)}
                    onKeyDown={(e) => handleKeyDown(e, index)}
                    onFocus={(e) => e.target.style.borderColor = "#ff9900"}
                    onBlur={(e) => e.target.style.borderColor = "#ced4da"}
                  />
                ))}
              </div>

              {/* VERIFY BUTTON (Amazon Gold) */}
              <button 
                type="submit"
                className="btn w-100 fw-bold py-2 mb-3 shadow-sm" 
                style={{ 
                  backgroundColor: "#f0c14b", 
                  borderColor: "#a88734", 
                  borderRadius: "4px",
                  color: "#111",
                  fontSize: "1rem"
                }}
              >
                Verify and Continue
              </button>
            </form>

            {/* RESEND LOGIC */}
            <div className="pt-3 border-top">
              {timer > 0 ? (
                <p className="small text-muted">
                  Resend code in <span className="fw-bold">{timer}s</span>
                </p>
              ) : (
                <button 
                  className="btn btn-link small fw-bold text-decoration-none p-0" 
                  style={{ color: "#007185" }}
                  onClick={() => setTimer(60)}
                >
                  Resend Code via SMS
                </button>
              )}
            </div>
          </div>

          {/* SECURITY FOOTER NOTE */}
          <div className="p-3 bg-light border-top text-center" style={{ borderBottomLeftRadius: "8px", borderBottomRightRadius: "8px" }}>
            <small className="text-muted d-block" style={{ fontSize: "0.7rem" }}>
              Confirming your identity helps protect your Amazon Social account. 
              If you didn't request this, <a href="#" style={{ color: "#007185" }}>let us know</a>.
            </small>
          </div>
        </div>
      </div>

      {/* 3. GLOBAL FOOTER */}
      <footer className="py-4 text-center mt-auto" style={{ fontSize: "0.75rem", color: "#888" }}>
        <div className="d-flex justify-content-center gap-4 mb-2">
          <span>Privacy Notice</span>
          <span>Conditions of Use</span>
          <span>Security Hub</span>
        </div>
        <div className="opacity-75">© 2026, Amazon Social, Inc. or its affiliates</div>
      </footer>
    </div>
  );
};

export default ConfirmPasscodePage;