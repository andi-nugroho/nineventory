<!-- Chatbot Widget - Include this in any page -->
<!-- Chat Launcher Button -->
<div id="chatLauncher" class="chat-launcher">
    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
        <path d="M20 2H4c-1.1 0-2 .9-2 2v18l4-4h14c1.1 0 2-.9 2-2V4c0-1.1-.9-2-2-2zm0 14H6l-2 2V4h16v12z"/>
    </svg>
</div>

<!-- Chat Box -->
<div id="chatBox" class="chat-box">
    <!-- Chat Header -->
    <div class="chat-header">
        <div class="chat-header-info">
            <div class="chat-avatar">🤖</div>
            <div class="chat-header-text">
                <h4>AI Assistant</h4>
                <p>Online</p>
            </div>
        </div>
        <button id="chatClose" class="chat-close">&times;</button>
    </div>
    
    <!-- Chat Logs -->
    <div id="chatLogs" class="chat-logs">
        <!-- Messages will be added here dynamically -->
        
        <!-- Typing Indicator -->
        <div id="typingIndicator" class="chat-message">
            <div class="message-avatar">🤖</div>
            <div class="message-content">
                <div class="typing-indicator">
                    <div class="typing-dot"></div>
                    <div class="typing-dot"></div>
                    <div class="typing-dot"></div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Chat Input -->
    <div class="chat-input">
        <form id="chatForm" class="chat-input-form">
            <input type="text" id="chatInput" placeholder="Ketik pesan..." autocomplete="off">
            <button type="submit" class="chat-send-btn">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="white">
                    <path d="M2.01 21L23 12 2.01 3 2 10l15 2-15 2z"/>
                </svg>
            </button>
        </form>
    </div>
</div>
