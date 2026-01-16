<!-- Glamori Chat Widget -->
<style>
    .glamori-widget {
        position: fixed;
        bottom: 24px;
        right: 24px;
        z-index: 9999;
        font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
    }

    .glamori-toggle {
        width: 60px;
        height: 60px;
        border-radius: 50%;
        background: linear-gradient(135deg, #000000, #333333);
        border: none;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.3);
        transition: all 0.3s ease;
    }

    .glamori-toggle:hover {
        transform: scale(1.1);
        box-shadow: 0 6px 30px rgba(0, 0, 0, 0.4);
    }

    .glamori-toggle img {
        width: 32px;
        height: 32px;
        border-radius: 50%;
    }

    .glamori-toggle .glamori-icon {
        font-size: 24px;
        color: #ffffff;
    }

    .glamori-toggle .chat-icon {
        display: block;
    }

    .glamori-toggle .close-icon {
        display: none;
    }

    .glamori-widget.open .glamori-toggle .chat-icon {
        display: none;
    }

    .glamori-widget.open .glamori-toggle .close-icon {
        display: block;
    }

    .glamori-badge {
        position: absolute;
        top: -4px;
        right: -4px;
        width: 20px;
        height: 20px;
        background: #ef4444;
        border-radius: 50%;
        color: white;
        font-size: 11px;
        font-weight: 700;
        display: flex;
        align-items: center;
        justify-content: center;
        display: none;
    }

    .glamori-window {
        position: absolute;
        bottom: 80px;
        right: 0;
        width: 380px;
        max-width: calc(100vw - 48px);
        height: 520px;
        max-height: calc(100vh - 120px);
        background: #000000;
        border-radius: 20px;
        box-shadow: 0 10px 50px rgba(0, 0, 0, 0.4);
        border: 2px solid #333333;
        display: none;
        flex-direction: column;
        overflow: hidden;
    }

    .glamori-widget.open .glamori-window {
        display: flex;
        animation: glamoriSlideIn 0.3s ease;
    }

    @keyframes glamoriSlideIn {
        from {
            opacity: 0;
            transform: translateY(20px) scale(0.95);
        }
        to {
            opacity: 1;
            transform: translateY(0) scale(1);
        }
    }

    .glamori-header {
        background: linear-gradient(135deg, #000000, #333333);
        color: #ffffff;
        padding: 16px 20px;
        display: flex;
        align-items: center;
        gap: 12px;
        position: relative;
    }

    .glamori-close {
        position: absolute;
        right: 16px;
        top: 50%;
        transform: translateY(-50%);
        width: 36px;
        height: 36px;
        border-radius: 50%;
        background: rgba(255, 255, 255, 0.1);
        border: none;
        color: #ffffff;
        cursor: pointer;
        display: none;
        align-items: center;
        justify-content: center;
        font-size: 18px;
        transition: all 0.2s ease;
    }

    .glamori-close:hover {
        background: rgba(255, 255, 255, 0.2);
    }

    @media (max-width: 480px) {
        .glamori-close {
            display: flex;
        }
    }

    .glamori-avatar {
        width: 44px;
        height: 44px;
        border-radius: 50%;
        background: #ffffff;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 20px;
    }

    .glamori-info h4 {
        margin: 0;
        font-size: 16px;
        font-weight: 600;
    }

    .glamori-info span {
        font-size: 12px;
        opacity: 0.8;
    }

    .glamori-status {
        width: 8px;
        height: 8px;
        background: #22c55e;
        border-radius: 50%;
        display: inline-block;
        margin-right: 4px;
    }

    .glamori-messages {
        flex: 1;
        overflow-y: auto;
        padding: 16px;
        display: flex;
        flex-direction: column;
        gap: 12px;
        background: #111111;
    }

    .glamori-message {
        display: flex;
        gap: 8px;
        max-width: 85%;
    }

    .glamori-message.user {
        align-self: flex-end;
        flex-direction: row-reverse;
    }

    .glamori-message-avatar {
        width: 32px;
        height: 32px;
        border-radius: 50%;
        background: #000000;
        color: #ffffff;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 14px;
        flex-shrink: 0;
    }

    .glamori-message.user .glamori-message-avatar {
        background: #6b7280;
    }

    .glamori-message-content {
        background: #262626;
        padding: 12px 16px;
        border-radius: 16px;
        border-bottom-left-radius: 4px;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.2);
        color: #ffffff;
    }

    .glamori-message.user .glamori-message-content {
        background: #ffffff;
        color: #000000;
        border-radius: 16px;
        border-bottom-right-radius: 4px;
    }

    .glamori-message-content p {
        margin: 0;
        font-size: 14px;
        line-height: 1.5;
    }

    .glamori-message-content .glamori-link {
        color: #ffffff;
        font-weight: 600;
        text-decoration: underline;
    }

    .glamori-message.user .glamori-message-content .glamori-link {
        color: #000000;
    }

    .glamori-message-time {
        font-size: 11px;
        color: rgba(255, 255, 255, 0.6);
        margin-top: 4px;
    }

    .glamori-message.user .glamori-message-time {
        color: rgba(0, 0, 0, 0.5);
    }

    .glamori-suggestions {
        display: flex;
        flex-wrap: wrap;
        gap: 8px;
        padding: 12px 16px;
        background: #000000;
        border-top: 1px solid #333333;
    }

    .glamori-suggestion {
        padding: 8px 14px;
        background: #262626;
        border: 1px solid #333333;
        border-radius: 20px;
        font-size: 13px;
        color: #ffffff;
        cursor: pointer;
        transition: all 0.2s ease;
    }

    .glamori-suggestion:hover {
        background: #ffffff;
        color: #000000;
        border-color: #ffffff;
    }

    .glamori-input-area {
        display: flex;
        gap: 8px;
        padding: 16px;
        background: #000000;
        border-top: 1px solid #333333;
    }

    .glamori-input {
        flex: 1;
        padding: 12px 16px;
        background: #262626;
        border: 2px solid #333333;
        border-radius: 24px;
        font-size: 14px;
        color: #ffffff;
        outline: none;
        transition: border-color 0.2s ease;
    }

    .glamori-input::placeholder {
        color: rgba(255, 255, 255, 0.5);
    }

    .glamori-input:focus {
        border-color: #ffffff;
    }

    .glamori-send {
        width: 44px;
        height: 44px;
        border-radius: 50%;
        background: #ffffff;
        border: none;
        color: #000000;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: all 0.2s ease;
    }

    .glamori-send:hover {
        background: #e5e5e5;
        transform: scale(1.05);
    }

    .glamori-send:disabled {
        background: #333333;
        color: #666666;
        cursor: not-allowed;
    }

    .glamori-typing {
        display: flex;
        gap: 4px;
        padding: 12px 16px;
        background: #262626;
        border-radius: 16px;
        border-bottom-left-radius: 4px;
        width: fit-content;
    }

    .glamori-typing span {
        width: 8px;
        height: 8px;
        background: rgba(255, 255, 255, 0.5);
        border-radius: 50%;
        animation: glamoriTyping 1.4s infinite;
    }

    .glamori-typing span:nth-child(2) {
        animation-delay: 0.2s;
    }

    .glamori-typing span:nth-child(3) {
        animation-delay: 0.4s;
    }

    @keyframes glamoriTyping {
        0%, 100% { transform: translateY(0); }
        50% { transform: translateY(-4px); }
    }

    /* Dark mode */
    [data-theme="dark"] .glamori-window {
        background: #1a1a1a;
    }

    [data-theme="dark"] .glamori-messages {
        background: #111111;
    }

    [data-theme="dark"] .glamori-message-content {
        background: #262626;
        color: #ffffff;
    }

    [data-theme="dark"] .glamori-message.user .glamori-message-content {
        background: #ffffff;
        color: #000000;
    }

    [data-theme="dark"] .glamori-suggestions {
        background: #1a1a1a;
        border-top-color: #333333;
    }

    [data-theme="dark"] .glamori-suggestion {
        background: #262626;
        border-color: #333333;
        color: #ffffff;
    }

    [data-theme="dark"] .glamori-input-area {
        background: #1a1a1a;
        border-top-color: #333333;
    }

    [data-theme="dark"] .glamori-input {
        background: #262626;
        border-color: #333333;
        color: #ffffff;
    }

    [data-theme="dark"] .glamori-input:focus {
        border-color: #ffffff;
    }

    /* Mobile */
    @media (max-width: 480px) {
        .glamori-widget {
            bottom: 16px;
            right: 16px;
        }

        .glamori-window {
            position: fixed;
            bottom: 0;
            right: 0;
            left: 0;
            width: 100%;
            max-width: 100%;
            height: calc(100vh - 60px);
            max-height: none;
            border-radius: 20px 20px 0 0;
        }

        .glamori-toggle {
            width: 56px;
            height: 56px;
        }
    }
</style>

<div class="glamori-widget" id="glamoriWidget">
    <button class="glamori-toggle" onclick="toggleGlamori()" aria-label="Open chat">
        <img src="/images/gs-logo-white.svg" alt="GS" class="chat-icon" style="width:32px;height:32px;">
        <i class="fas fa-times glamori-icon close-icon"></i>
        <span class="glamori-badge" id="glamoriBadge">1</span>
    </button>

    <div class="glamori-window">
        <div class="glamori-header">
            <div class="glamori-avatar">
                <img src="/images/gs-logo.svg" alt="GS" style="width:28px;height:28px;">
            </div>
            <div class="glamori-info">
                <h4>Glamori</h4>
                <span><span class="glamori-status"></span>Online - Altijd beschikbaar</span>
            </div>
            <button class="glamori-close" onclick="toggleGlamori()" aria-label="Sluiten">
                <i class="fas fa-times"></i>
            </button>
        </div>

        <div class="glamori-messages" id="glamoriMessages">
            <!-- Messages will be added here -->
        </div>

        <div class="glamori-suggestions" id="glamoriSuggestions">
            <!-- Suggestion buttons will be added here -->
        </div>

        <div class="glamori-input-area">
            <input type="text"
                   class="glamori-input"
                   id="glamoriInput"
                   placeholder="Typ je bericht..."
                   onkeypress="if(event.key === 'Enter') sendGlamoriMessage()">
            <button class="glamori-send" onclick="sendGlamoriMessage()" id="glamoriSendBtn">
                <i class="fas fa-paper-plane"></i>
            </button>
        </div>
    </div>
</div>

<script>
let glamoriOpen = false;
let glamoriInitialized = false;

function toggleGlamori() {
    glamoriOpen = !glamoriOpen;
    document.getElementById('glamoriWidget').classList.toggle('open', glamoriOpen);

    if (glamoriOpen && !glamoriInitialized) {
        initGlamori();
    }

    if (glamoriOpen) {
        document.getElementById('glamoriInput').focus();
        document.getElementById('glamoriBadge').style.display = 'none';
    }
}

function initGlamori() {
    glamoriInitialized = true;

    // Get welcome message
    fetch('/api/glamori/welcome')
        .then(res => res.json())
        .then(data => {
            addGlamoriMessage(data, 'assistant');
            updateSuggestions(data.suggestions || []);
        })
        .catch(err => {
            console.error('Glamori init error:', err);
            addGlamoriMessage({
                message: 'Hallo! Ik ben Glamori. Waarmee kan ik je helpen?',
                timestamp: new Date().toLocaleTimeString('nl-NL', {hour: '2-digit', minute: '2-digit'})
            }, 'assistant');
        });
}

function sendGlamoriMessage() {
    const input = document.getElementById('glamoriInput');
    const message = input.value.trim();

    if (!message) return;

    // Add user message
    addGlamoriMessage({
        message: message,
        timestamp: new Date().toLocaleTimeString('nl-NL', {hour: '2-digit', minute: '2-digit'})
    }, 'user');

    input.value = '';
    input.focus();

    // Show typing indicator
    showTyping();

    // Send to API
    fetch('/api/glamori/chat', {
        method: 'POST',
        headers: {'Content-Type': 'application/json'},
        body: JSON.stringify({message: message})
    })
    .then(res => res.json())
    .then(data => {
        hideTyping();
        addGlamoriMessage(data, 'assistant');
        updateSuggestions(data.suggestions || []);

        // Handle actions
        if (data.action && data.action.startsWith('redirect:')) {
            setTimeout(() => {
                window.location.href = data.action.replace('redirect:', '');
            }, 1500);
        }
    })
    .catch(err => {
        hideTyping();
        console.error('Glamori error:', err);
        addGlamoriMessage({
            message: 'Sorry, er ging iets mis. Probeer het opnieuw.',
            timestamp: new Date().toLocaleTimeString('nl-NL', {hour: '2-digit', minute: '2-digit'})
        }, 'assistant');
    });
}

function addGlamoriMessage(data, role) {
    const container = document.getElementById('glamoriMessages');
    const div = document.createElement('div');
    div.className = 'glamori-message ' + role;

    const avatar = role === 'assistant'
        ? '<img src="/images/gs-logo.svg" alt="GS" style="width:20px;height:20px;">'
        : '<i class="fas fa-user"></i>';

    div.innerHTML = `
        <div class="glamori-message-avatar">${avatar}</div>
        <div class="glamori-message-content">
            <p>${data.message}</p>
            <div class="glamori-message-time">${data.timestamp || ''}</div>
        </div>
    `;

    container.appendChild(div);
    container.scrollTop = container.scrollHeight;
}

function updateSuggestions(suggestions) {
    const container = document.getElementById('glamoriSuggestions');
    container.innerHTML = '';

    suggestions.forEach(s => {
        const btn = document.createElement('button');
        btn.className = 'glamori-suggestion';
        btn.textContent = s.text;
        btn.onclick = () => {
            document.getElementById('glamoriInput').value = s.value;
            sendGlamoriMessage();
        };
        container.appendChild(btn);
    });
}

function showTyping() {
    const container = document.getElementById('glamoriMessages');
    const typing = document.createElement('div');
    typing.id = 'glamoriTypingIndicator';
    typing.className = 'glamori-message';
    typing.innerHTML = `
        <div class="glamori-message-avatar"><img src="/images/gs-logo.svg" alt="GS" style="width:20px;height:20px;"></div>
        <div class="glamori-typing">
            <span></span><span></span><span></span>
        </div>
    `;
    container.appendChild(typing);
    container.scrollTop = container.scrollHeight;
}

function hideTyping() {
    const typing = document.getElementById('glamoriTypingIndicator');
    if (typing) typing.remove();
}

// Show badge after 5 seconds if chat not opened
setTimeout(() => {
    if (!glamoriOpen) {
        document.getElementById('glamoriBadge').style.display = 'flex';
    }
}, 5000);
</script>
