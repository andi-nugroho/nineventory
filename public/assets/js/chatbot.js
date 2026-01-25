class ChatBot {
    constructor() {
        this.chatBox = document.getElementById('chatBox');
        this.chatLogs = document.getElementById('chatLogs');
        this.chatInput = document.getElementById('chatInput');
        this.chatForm = document.getElementById('chatForm');
        this.typingIndicator = document.getElementById('typingIndicator');

        this.init();
    }

    init() {

        document.getElementById('chatLauncher').addEventListener('click', () => {
            this.toggleChat();
        });


        document.getElementById('chatClose').addEventListener('click', () => {
            this.toggleChat();
        });

        // Form submit
        this.chatForm.addEventListener('submit', (e) => {
            e.preventDefault();
            this.sendMessage();
        });


        this.addMessage('bot', 'Halo! 👋 Saya adalah asisten AI NINEVENTORY. Ada yang bisa saya bantu?');
        this.addQuickReplies();
    }

    addQuickReplies() {
        const suggestions = [
            '📦 Berapa stok laptop yang tersedia?',
            '🔍 Barang apa saja yang ada?',
            '📊 Berapa total barang yang sedang dipinjam?',
            '📍 Dimana lokasi proyektor?'
        ];

        const quickRepliesDiv = document.createElement('div');
        quickRepliesDiv.className = 'quick-replies';
        quickRepliesDiv.id = 'quickReplies';

        suggestions.forEach(suggestion => {
            const btn = document.createElement('button');
            btn.className = 'quick-reply-btn';
            btn.textContent = suggestion;
            btn.onclick = () => {
                this.chatInput.value = suggestion;
                this.sendMessage();

                document.getElementById('quickReplies')?.remove();
            };
            quickRepliesDiv.appendChild(btn);
        });

        this.chatLogs.appendChild(quickRepliesDiv);
        this.scrollToBottom();
    }

    toggleChat() {
        this.chatBox.classList.toggle('active');
        if (this.chatBox.classList.contains('active')) {
            this.chatInput.focus();
        }
    }

    async sendMessage() {
        const message = this.chatInput.value.trim();

        if (!message) return;


        this.addMessage('user', message);


        this.chatInput.value = '';


        this.showTyping();

        try {

            const response = await fetch('api/chatbot.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({ message })
            });

            const data = await response.json();


            this.hideTyping();

            if (data.success) {
                this.addMessage('bot', data.message);
            } else {
                this.addMessage('bot', 'Maaf, terjadi kesalahan. Silakan coba lagi.');
            }

        } catch (error) {
            this.hideTyping();
            this.addMessage('bot', 'Maaf, terjadi kesalahan koneksi. Silakan coba lagi.');
            console.error('Chatbot error:', error);
        }
    }

    addMessage(sender, text) {
        const messageDiv = document.createElement('div');
        messageDiv.className = `chat-message ${sender}`;

        const now = new Date();
        const time = now.toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' });

        messageDiv.innerHTML = `
            <div class="message-avatar">${sender === 'bot' ? '🤖' : '👤'}</div>
            <div class="message-content">
                <div class="message-bubble">${this.escapeHtml(text)}</div>
                <div class="message-time">${time}</div>
            </div>
        `;

        this.chatLogs.appendChild(messageDiv);
        this.scrollToBottom();
    }

    showTyping() {
        this.typingIndicator.classList.add('active');
        this.scrollToBottom();
    }

    hideTyping() {
        this.typingIndicator.classList.remove('active');
    }

    scrollToBottom() {
        setTimeout(() => {
            this.chatLogs.scrollTop = this.chatLogs.scrollHeight;
        }, 100);
    }

    escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML.replace(/\n/g, '<br>');
    }
}


document.addEventListener('DOMContentLoaded', () => {
    new ChatBot();
});
