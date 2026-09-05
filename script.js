/**
 * Triggers a custom dropdown alert notification.
 * @param {string} message - The message text to display.
 * @param {string} type - 'success', 'error', 'warning', or 'info' (default: 'info').
 * @param {number} duration - Time in milliseconds before auto-closing (default: 4000ms).
 */
function showDropdownAlert(message, type = 'info', duration = 4000) {
    // 1. Define color schemes based on alert type (#008cc3 integrated for info)
    const themes = {
        success: { bg: '#10b981', border: '#059669', icon: '✓' },
        error:   { bg: '#ef4444', border: '#dc2626', icon: '✕' },
        warning: { bg: '#f59e0b', border: '#d97706', icon: '⚠' },
        info:    { bg: '#008cc3', border: '#00719e', icon: 'ℹ' }
    };

    const theme = themes[type] || themes.info;

    // 2. Create the alert element
    const alertBox = document.createElement('div');
    
    // 3. Inject content structure
    alertBox.innerHTML = `
        <span style="font-size: 1.1rem; font-weight: bold;">${theme.icon}</span>
        <span style="flex-grow: 1;">${message}</span>
        <button style="background: none; border: none; color: white; font-size: 1.2rem; cursor: pointer; padding: 0 0 0 10px; line-height: 1; opacity: 0.8;" onclick="this.parentElement.removeAlert()">&times;</button>
    `;

    // 4. Apply modern floating dropdown styling
    Object.assign(alertBox.style, {
        position: 'fixed',
        top: '-100px',
        left: '50%',
        transform: 'translateX(-50%)',
        backgroundColor: theme.bg,
        color: '#ffffff',
        padding: '12px 30px',
        // borderRadius: '12px',
        // boxShadow: '0 10px 25px -5px rgba(0, 0, 0, 0.2), 0 8px 10px -6px rgba(0, 0, 0, 0.1)',
        fontFamily: "'Plus Jakarta Sans', system-ui, -apple-system, sans-serif",
        fontSize: '0.95rem',
        fontWeight: '500',
        display: 'flex',
        alignItems: 'center',
        gap: '12px',
        zIndex: '99999',
        minWidth: '380px',
        maxWidth: '400px',
        width: '50%',
        // border: `1px solid ${theme.border}`,
        transition: 'top 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275), opacity 0.3s ease',
        opacity: '0'
    });

    // 5. Append to body
    document.body.appendChild(alertBox);

    // 6. Handle slide-up removal animation
    alertBox.removeAlert = () => {
        alertBox.style.top = '-100px';
        alertBox.style.opacity = '0';
        setTimeout(() => alertBox.remove(), 400);
    };

    // 7. Trigger entrance animation
    requestAnimationFrame(() => {
        alertBox.style.top = '20px';
        alertBox.style.opacity = '1';
    });

    // 8. Auto dismiss timer
    if (duration > 0) {
        setTimeout(() => alertBox.removeAlert(), duration);
    }
}

