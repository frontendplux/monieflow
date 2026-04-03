import React, { useState } from 'react';

const ChooseAvatarPage = () => {
  const [selectedAvatar, setSelectedAvatar] = useState(null);
  const [hovered, setHovered] = useState(null);

  // Generate 8 diverse professional seeds
  const avatarSeeds = ["Felix", "Aneka", "Jocelyn", "George", "Sophie", "Toby", "Willow", "Jasper"];

  return (
    <div className="container-fluid min-vh-100 d-flex flex-column" style={{ backgroundColor: "#f0f2f5" }}>
      
      {/* 1. PREMIUM NAV (Amazon Dark) */}
      <nav className="py-2 px-4 bg-white border-bottom shadow-sm">
        <div className="mx-auto d-flex align-items-center justify-content-between" style={{ maxWidth: "1000px" }}>
          <h2 className="fw-bold mb-0" style={{ color: "#232f3e", fontSize: "1.4rem", letterSpacing: "-1px" }}>
            amazon<span className="fw-light">social</span>
          </h2>
          <span className="small text-muted fw-bold">Step 3 of 3</span>
        </div>
      </nav>

      {/* 2. MAIN SELECTION AREA */}
      <div className="flex-grow-1 d-flex align-items-center justify-content-center p-4">
        <div className="card border-0 shadow-sm" style={{ width: "100%", maxWidth: "600px", borderRadius: "8px" }}>
          
          <div className="card-header bg-white p-4 border-bottom text-center">
            <h4 className="fw-bold mb-1" style={{ color: "#232f3e" }}>Choose your profile picture</h4>
            <p className="text-muted small mb-0">Select an avatar or upload your own to help friends recognize you.</p>
          </div>

          <div className="card-body p-4">
            {/* AVATAR GRID */}
            <div className="row g-3 mb-5">
              {avatarSeeds.map((seed, index) => {
                const url = `https://api.dicebear.com/7.x/avataaars/svg?seed=${seed}`;
                const isSelected = selectedAvatar === url;
                
                return (
                  <div key={index} className="col-3 text-center">
                    <div 
                      onClick={() => setSelectedAvatar(url)}
                      onMouseEnter={() => setHovered(index)}
                      onMouseLeave={() => setHovered(null)}
                      style={{ 
                        cursor: "pointer",
                        transition: "all 0.2s ease",
                        position: "relative",
                        transform: hovered === index ? "translateY(-5px)" : "none"
                      }}
                    >
                      <img 
                        src={url} 
                        alt="Avatar" 
                        className="rounded-circle bg-light shadow-sm" 
                        style={{ 
                          width: "100%", 
                          aspectRatio: "1/1",
                          border: isSelected ? "4px solid #ff9900" : "2px solid #eee",
                          padding: "4px"
                        }} 
                      />
                      {isSelected && (
                        <div 
                          className="position-absolute bottom-0 end-0 bg-success rounded-circle d-flex align-items-center justify-content-center"
                          style={{ width: "24px", height: "24px", border: "2px solid white" }}
                        >
                          <small className="text-white fw-bold">✓</small>
                        </div>
                      )}
                    </div>
                  </div>
                );
              })}
            </div>

            {/* UPLOAD ALTERNATIVE */}
            <div className="p-3 rounded mb-4 text-center border-dashed" style={{ border: "2px dashed #ddd", backgroundColor: "#fafafa" }}>
              <button className="btn btn-link text-decoration-none fw-bold" style={{ color: "#007185" }}>
                📷 Upload from computer
              </button>
            </div>

            {/* SUBMIT ACTIONS (Amazon Signature Style) */}
            <div className="d-flex justify-content-end gap-2 pt-4 border-top">
              <button 
                className="btn btn-light fw-bold px-4" 
                style={{ borderRadius: "4px", color: "#232f3e", backgroundColor: "#e4e6eb" }}
              >
                Skip
              </button>
              <button 
                disabled={!selectedAvatar}
                className="btn fw-bold px-5" 
                style={{ 
                  backgroundColor: selectedAvatar ? "#f0c14b" : "#f7fafd", 
                  borderColor: selectedAvatar ? "#a88734" : "#ddd", 
                  borderRadius: "4px",
                  color: selectedAvatar ? "#111" : "#999"
                }}
              >
                Finish Setup
              </button>
            </div>
          </div>
        </div>
      </div>

      {/* FOOTER */}
      <footer className="py-4 text-center mt-auto text-muted small">
        <div className="d-flex justify-content-center gap-3 mb-1">
          <span>Privacy</span> <span>Terms</span> <span>Cookies</span>
        </div>
        <div>Amazon Social © 2026</div>
      </footer>
    </div>
  );
};

export default ChooseAvatarPage;