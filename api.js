/**
 * DroppySammy - A stackable, beautiful alert system
 * @param {string} type - bootstrap color (danger, success, info, warning, primary)
 * @param {string} header - Bold title text
 * @param {string} message - Description text
 */
function droppySammy(type, header, message) {
    // 1. Inject Styles only once
    if (!document.getElementById('droppy-styles')) {
        const style = document.createElement('style');
        style.id = 'droppy-styles';
        style.innerHTML = `
            #droppy-container {
                position: fixed; top: 20px; left: 50%; transform: translateX(-50%);
                z-index: 10000; width: 100%; max-width: 400px; padding:10px;
                display: flex; flex-direction: column; gap: 12px; pointer-events: none;
            }
            .droppy-alert {
                pointer-events: auto; background: rgba(255, 255, 255, 0.9);
                backdrop-filter: blur(12px); -webkit-backdrop-filter: blur(12px);
                border-radius: 16px; border: 1px solid rgba(0,0,0,0.05);
                border-left: 6px solid; padding: 16px 20px; position: relative;
                box-shadow: 0 15px 30px -5px rgba(0,0,0,0.1);
                animation: droppyIn 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275) forwards;
                transition: all 0.3s ease; cursor: default;
            }
            @keyframes droppyIn {
                from { opacity: 0; transform: translateY(-30px) scale(0.9); }
                to { opacity: 1; transform: translateY(0) scale(1); }
            }
            .droppy-exit { opacity: 0; transform: scale(0.8); margin-top: -70px; }
            .droppy-header { 
                font-weight: 800; font-size: 0.85rem; text-transform: uppercase; 
                letter-spacing: 0.5px; display: flex; justify-content: space-between; align-items: center;
            }
            .droppy-body { font-size: 0.9rem; color: #4b5563; margin-top: 2px; line-height: 1.4; }
            .droppy-close { cursor: pointer; font-size: 1.4rem; line-height: 1; opacity: 0.3; transition: 0.2s; }
            .droppy-close:hover { opacity: 1; color: #000; }
            .droppy-progress { position: absolute; bottom: 0; left: 0; height: 4px; width: 100%; background: rgba(0,0,0,0.03); }
            .droppy-bar { height: 100%; width: 100%; transition: width linear; }
        `;
        document.head.appendChild(style);
    }

    // 2. Ensure Container exists
    let container = document.getElementById('droppy-container');
    if (!container) {
        container = document.createElement('div');
        container.id = 'droppy-container';
        document.body.appendChild(container);
    }

    // 3. Setup Colors
    const colors = {
        primary: '#6366f1', success: '#10b981', danger: '#ef4444', 
        warning: '#f59e0b', info: '#3b82f6', dark: '#1f2937'
    };
    const themeColor = colors[type] || colors.primary;

    // 4. Create Alert Instance
    const id = 'ds_' + Math.random().toString(36).substr(2, 9);
    const alertDiv = document.createElement('div');
    alertDiv.className = 'droppy-alert';
    alertDiv.id = id;
    alertDiv.style.borderColor = themeColor;
    
    alertDiv.innerHTML = `
        <div class="droppy-header" style="color: ${themeColor}">
            <span>${header}</span>
            <span class="droppy-close">&times;</span>
        </div>
        <div class="droppy-body">${message}</div>
        <div class="droppy-progress"><div class="droppy-bar" style="background: ${themeColor}"></div></div>
    `;

    // 5. Add to screen
    container.prepend(alertDiv);

    // 6. Handle Interaction & Auto-close
    const duration = 5000;
    const bar = alertDiv.querySelector('.droppy-bar');
    const closeBtn = alertDiv.querySelector('.droppy-close');
    
    // Animation for progress bar
    setTimeout(() => {
        bar.style.transition = `width ${duration}ms linear`;
        bar.style.width = '0%';
    }, 50);

    const autoClose = setTimeout(() => dismiss(id), duration);

    // Manual Cancel
    closeBtn.onclick = () => {
        clearTimeout(autoClose);
        dismiss(id);
    };

    function dismiss(alertId) {
        const target = document.getElementById(alertId);
        if (target) {
            target.classList.add('droppy-exit');
            setTimeout(() => target.remove(), 300);
        }
    }
}



/**
 * SammyEmojiPicker - All-in-one logic for monieFlow
 * @param {string} triggerId - ID of the button to open the picker
 * @param {string} inputId - ID of the textarea/input to receive the emoji
 */
function initSammyPicker(triggerId, inputId) {
    const trigger = document.getElementById(triggerId);
    const targetInput = document.getElementById(inputId);
    
    // 1. Database of Custom Sammy Emojis
    const sammyLibrary = [
        { name: 'sammy_cool', url: 'https://cdn-icons-png.flaticon.com/512/4712/4712035.png' },
        { name: 'sammy_coin', url: 'https://cdn-icons-png.flaticon.com/512/2454/2454282.png' },
        { name: 'sammy_fire', url: 'https://cdn-icons-png.flaticon.com/512/1356/1356479.png' },
        { name: 'sammy_party', url: 'https://cdn-icons-png.flaticon.com/512/2933/2933116.png' },
        { name: 'sammy_love', url: 'https://cdn-icons-png.flaticon.com/512/2584/2584606.png' }
    ];

    // 2. Inject Styles (Scoped to .sammy-wrapper)
    if (!document.getElementById('sammy-picker-styles')) {
        const style = document.createElement('style');
        style.id = 'sammy-picker-styles';
        style.innerHTML = `
            .sammy-picker-box {
                position: absolute; width: 260px; background: #ffffff;
                border-radius: 12px; box-shadow: 0 8px 30px rgba(0,0,0,0.2);
                display: none; padding: 12px; z-index: 9999; border: 1px solid #ddd;
            }
            .sammy-grid {
                display: grid; grid-template-columns: repeat(4, 1fr); gap: 8px;
                max-height: 180px; overflow-y: auto;
            }
            .sammy-emoji-item {
                cursor: pointer; transition: transform 0.1s ease;
                padding: 4px; border-radius: 6px; text-align: center;
            }
            .sammy-emoji-item:hover { background: #f0f2f5; transform: scale(1.15); }
            .sammy-emoji-item img { width: 32px; height: 32px; object-fit: contain; }
            .sammy-title { font-size: 11px; font-weight: 700; color: #999; margin-bottom: 8px; text-transform: uppercase; }
        `;
        document.head.appendChild(style);
    }

    // 3. Create and Append Picker UI
    const picker = document.createElement('div');
    picker.className = 'sammy-picker-box';
    picker.innerHTML = `<div class="sammy-title">Sammy Customs</div><div class="sammy-grid"></div>`;
    document.body.appendChild(picker);

    const grid = picker.querySelector('.sammy-grid');

    // 4. Populate Emojis & Add Click Logic
    sammyLibrary.forEach(emoji => {
        const item = document.createElement('div');
        item.className = 'sammy-emoji-item';
        item.innerHTML = `<img src="${emoji.url}" title=":${emoji.name}:">`;
        
        item.onclick = () => {
            const start = targetInput.selectionStart;
            const end = targetInput.selectionEnd;
            const currentText = targetInput.value;
            const emojiCode = ` :${emoji.name}: `;
            
            // Insert at cursor position
            targetInput.value = currentText.slice(0, start) + emojiCode + currentText.slice(end);
            
            // Move cursor after the emoji
            targetInput.selectionStart = targetInput.selectionEnd = start + emojiCode.length;
            
            picker.style.display = 'none';
            targetInput.focus();
        };
        grid.appendChild(item);
    });

    // 5. Toggle Picker Position
    trigger.addEventListener('click', (e) => {
        e.stopPropagation();
        const isVisible = picker.style.display === 'block';
        
        if (!isVisible) {
            const rect = trigger.getBoundingClientRect();
            picker.style.display = 'block';
            // Position above the trigger
            picker.style.top = (rect.top + window.scrollY - picker.offsetHeight - 10) + 'px';
            picker.style.left = rect.left + 'px';
        } else {
            picker.style.display = 'none';
        }
    });

    // Close on clicking anywhere else
    document.addEventListener('click', () => { picker.style.display = 'none'; });
    picker.addEventListener('click', (e) => e.stopPropagation());
}



/**
 * SammyEmojiPicker
 * @param {string} triggerId - The ID of the button that opens the picker
 * @param {string} inputId - The ID of the input/textarea where emoji will be inserted
 */
function initSammyPickers(triggerId, inputId) {
    const trigger = document.getElementById(triggerId);
    const targetInput = document.getElementById(inputId);

    // Define emojis directly
    const customEmojis = ["👋", "💰", "🚀", "🤑", "❤️", "📍"];

    // Picker Styles
    const style = document.createElement('style');
    style.innerHTML = `
        .sammy-picker {
            position: absolute; width: 250px; background: #fff;
            border-radius: 15px; box-shadow: 0 10px 25px rgba(0,0,0,0.15);
            display: none; padding: 15px; z-index: 2000; border: 1px solid #eee;
        }
        .sammy-grid {
            display: grid; grid-template-columns: repeat(4, 1fr); gap: 10px;
            max-height: 200px; overflow-y: auto;
        }
        .sammy-item {
            cursor: pointer; transition: 0.2s; padding: 5px; border-radius: 8px;
            font-size: 24px; text-align: center;
        }
        .sammy-item:hover { background: #f0f2f5; transform: scale(1.1); }
        .sammy-header { font-size: 12px; font-weight: 800; color: #888; margin-bottom: 10px; }
    `;
    document.head.appendChild(style);

    // Picker Element
    const picker = document.createElement('div');
    picker.className = 'sammy-picker';
    picker.innerHTML = `
        <div class="sammy-header">SAMMY EMOJIS</div>
        <div class="sammy-grid" id="sammyGrid"></div>
    `;
    document.body.appendChild(picker);

    // Populate Grid
    const grid = picker.querySelector('#sammyGrid');
    customEmojis.forEach(emoji => {
        const div = document.createElement('div');
        div.className = 'sammy-item';
        div.textContent = emoji;
        div.onclick = () => {
            const start = targetInput.selectionStart;
            const end = targetInput.selectionEnd;
            const text = targetInput.value;
            targetInput.value = text.slice(0, start) + emoji + text.slice(end);
            picker.style.display = 'none';
            targetInput.focus();
        };
        grid.appendChild(div);
    });

    // Trigger Logic
    trigger.addEventListener('click', (e) => {
        e.stopPropagation();
        const rect = trigger.getBoundingClientRect();
        picker.style.top = `${rect.bottom + window.scrollY}px`;
        picker.style.left = `${rect.left}px`;
        picker.style.display = picker.style.display === 'block' ? 'none' : 'block';
    });

    // Close when clicking outside
    document.addEventListener('click', () => picker.style.display = 'none');
    picker.addEventListener('click', (e) => e.stopPropagation());

    // Typing replacement: replace shortcuts like #wave with 👋
    targetInput.addEventListener('input', () => {
        let text = targetInput.value;
        text = text.replaceAll("#wave", "👋")
                   .replaceAll("#gold", "💰")
                   .replaceAll("#rocket", "🚀")
                   .replaceAll("#rich", "🤑")
                   .replaceAll("#love", "❤️")
                   .replaceAll("#pin", "📍");
        targetInput.value = text;
    });
}









/**
 * TikTok-Style Interaction Engine
 * Handles Emojis and Gifts for any input group
 */
const initInteractionEngine = () => {
    // 1. Inject Global Styles (Keep CSS inside the JS for true portability)
    const style = document.createElement('style');
    style.innerHTML = `
        .interaction-popover {
            position: absolute; bottom: 70px; right: 10px; margin:5px 10px;
            background: rgba(255, 255, 255, 0.98); backdrop-filter: blur(10px);
            border: 1px solid #eee; border-radius: 1.25rem;
            box-shadow: 0 15px 35px rgba(0,0,0,0.1); z-index: 2000;
            padding: 12px; min-width: 180px;
            transform-origin: bottom right;
            animation: popUp 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.2);
        }
        @keyframes popUp { 
            0% { transform: scale(0.5) translateY(20px); opacity: 0; }
            100% { transform: scale(1) translateY(0); opacity: 1; }
        }
        .pop-item { transition: transform 0.2s; cursor: pointer; padding: 5px; border-radius: 8px; }
        .pop-item:hover { transform: scale(1.25); background: #f8f9fa; }
        .gift-tag { font-size: 0.65rem; font-weight: 800; color: #0d6efd; margin-top: -5px; }
    `;
    document.head.appendChild(style);

    // 2. The Master Click Listener
    document.addEventListener('click', (e) => {
        const trigger = e.target.closest('.pop-trigger');
        const activePopover = document.querySelector('.interaction-popover');

        // Close logic: If we click away, destroy the popover
        if (!trigger && activePopover && !e.target.closest('.interaction-popover')) {
            activePopover.remove();
            return;
        }

        if (trigger) {
            const type = trigger.dataset.type;
            const parent = trigger.closest('.custom-interaction-group');
            const input = parent.querySelector('.main-input');

            // If a popover is already open here, close it and stop
            if (activePopover && activePopover.parentElement === parent && activePopover.dataset.type === type) {
                activePopover.remove();
                return;
            }

            // Clean up any other open popovers
            if (activePopover) activePopover.remove();

            // 3. Generate Content
            const popover = document.createElement('div');
            popover.className = 'interaction-popover';
            popover.dataset.type = type;

            if (type === 'emoji') {
                const emojis = ['🔥', '🙌', '🎸', '❤️', '🎶', '✨', '💯', '🤩', '🎹', '⚡'];
                popover.innerHTML = `<div class="d-flex flex-wrap gap-2">
                    ${emojis.map(em => `<div class="pop-item emoji-select fs-4">${em}</div>`).join('')}
                </div>`;
            } else if (type === 'gift') {
                const gifts = [
                    {img: '🌹', val: '1'}, {img: '🔥', val: '5'}, {img: '💎', val: '50'}, {img: '👑', val: '99'}
                ];
                popover.innerHTML = `
                    <h6 class="text-muted fw-bold mb-2" style="font-size:10px">SEND A GIFT</h6>
                    <div class="row g-2 text-center">
                        ${gifts.map(g => `
                            <div onclick="alert('${g.img}')" class="col-3 pop-item gift-select">
                                <div class="fs-3">${g.img}</div>
                                <div class="gift-tag">${g.val}</div>
                            </div>
                        `).join('')}
                    </div>`;
            }

            // 4. Attach & Position
            parent.appendChild(popover);

            // 5. Click Logic inside Popover
            popover.onclick = (pe) => {
                const emoji = pe.target.closest('.emoji-select');
                const gift = pe.target.closest('.gift-select');

                if (emoji) {
                    input.value += emoji.innerText;
                    input.focus();
                }
                if (gift) {
                    // Trigger a custom event for your backend
                    const event = new CustomEvent('giftSent', { detail: { type: gift.innerText } });
                    document.dispatchEvent(event);
                    popover.remove();
                }
            };
        }
    });
};

// Start the engine
initInteractionEngine();