<!-- Floating AI Assistant Widget -->
<div x-data="{
    isChatOpen: false,
    message: '',
    charCount: 0,
    maxChars: 2000,
    messages: [],
    isTyping: false,
    
    init() {
        // No auto welcome message - will be shown in UI
    },
    
    handleInputChange(e) {
        this.message = e.target.value;
        this.charCount = e.target.value.length;
    },
    
    async handleSend() {
        if (!this.message.trim()) return;
        
        const userMessage = this.message;
        this.addUserMessage(userMessage);
        this.message = '';
        this.charCount = 0;
        
        // Show typing
        this.isTyping = true;
        
        try {
            const response = await fetch('api/chatbot.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ message: userMessage })
            });
            
            const data = await response.json();
            this.isTyping = false;
            
            if (data.success) {
                this.addBotMessage(data.message);
            } else {
                this.addBotMessage('Maaf, terjadi kesalahan. Silakan coba lagi.');
            }
        } catch (error) {
            this.isTyping = false;
            this.addBotMessage('Maaf, terjadi kesalahan koneksi.');
        }
    },
    
    handleKeyDown(e) {
        if (e.key === 'Enter' && !e.shiftKey) {
            e.preventDefault();
            this.handleSend();
        }
    },
    
    addUserMessage(text) {
        this.messages.push({ sender: 'user', text, time: new Date().toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' }) });
        this.$nextTick(() => this.scrollToBottom());
    },
    
    addBotMessage(text) {
        this.messages.push({ sender: 'bot', text, time: new Date().toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' }) });
        this.$nextTick(() => this.scrollToBottom());
    },
    
    scrollToBottom() {
        const container = this.$refs.chatLogs;
        if (container) {
            container.scrollTop = container.scrollHeight;
        }
    },
    
    formatMessage(text) {
        if (!text) return '';
        // Convert newlines to <br> tags
        let formatted = text.replace(/\n/g, '<br>');
        // Convert **text** to bold
        formatted = formatted.replace(/\*\*(.+?)\*\*/g, '<strong>$1</strong>');
        // Convert - list items to bullet points
        formatted = formatted.replace(/^- (.+)$/gm, '• $1');
        return formatted;
    }
}" 
x-init="init()"
@click.outside="isChatOpen = false"
class="fixed bottom-6 right-6 z-50">
 
    <!-- Floating 3D Glowing AI Button -->
    <button 
        @click="isChatOpen = !isChatOpen"
        class="floating-ai-button relative w-16 h-16 rounded-full flex items-center justify-center transition-all duration-500 transform shadow-2xl"
        :class="isChatOpen ? 'rotate-90' : 'rotate-0'"
        style="background: linear-gradient(135deg, rgba(255,102,38,0.9) 0%, rgba(239,68,68,0.9) 100%); box-shadow: 0 0 20px rgba(255,102,38,0.7), 0 0 40px rgba(239,68,68,0.5), 0 0 60px rgba(220,38,38,0.3); border: 2px solid rgba(255, 255, 255, 0.2);">
        
        <!-- 3D effect -->
        <div class="absolute inset-0 rounded-full bg-gradient-to-b from-white/20 to-transparent opacity-30"></div>
        
        <!-- Inner glow -->
        <div class="absolute inset-0 rounded-full border-2 border-white/10"></div>
        
        <!-- AI Icon -->
        <div class="relative z-10">
            <i x-show="!isChatOpen" data-lucide="sparkles" class="w-8 h-8 text-white"></i>
            <i x-show="isChatOpen" data-lucide="x" class="w-8 h-8 text-white"></i>
        </div>
        
        <!-- Glowing animation -->
        <div class="absolute inset-0 rounded-full animate-ping opacity-20 bg-orange-500"></div>
    </button>

    <!-- Chat Interface -->
    <div x-show="isChatOpen"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 scale-80 translate-y-5"
         x-transition:enter-end="opacity-100 scale-100 translate-y-0"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100 scale-100"
         x-transition:leave-end="opacity-0 scale-80"
         class="absolute bottom-20 right-0 w-[420px] sm:w-[500px] origin-bottom-right"
         style="display: none;">
        
        <div class="relative flex flex-col rounded-3xl bg-gradient-to-br from-zinc-800/95 to-zinc-900/95 border border-zinc-500/50 shadow-2xl backdrop-blur-3xl overflow-hidden">
            
            <!-- Header -->
            <div class="flex items-center justify-between px-6 pt-4 pb-2">
                <div class="flex items-center gap-1.5">
                    <div class="w-2 h-2 rounded-full bg-green-500 animate-pulse"></div>
                    <span class="text-xs font-semibold text-zinc-400" style="font-family: 'Inter', 'Segoe UI', system-ui, sans-serif;">AI Assistant</span>
                </div>
                <div class="flex items-center gap-2">
                    <span class="px-2 py-1 text-xs font-semibold bg-zinc-800/60 text-zinc-300 rounded-2xl" style="font-family: 'Inter', 'Segoe UI', system-ui, sans-serif;">NINEVENTORY</span>
                    <span class="px-2 py-1 text-xs font-semibold bg-orange-500/10 text-orange-400 border border-orange-500/20 rounded-2xl" style="font-family: 'Inter', 'Segoe UI', system-ui, sans-serif;">AI</span>
                    <button @click="isChatOpen = false" class="p-1.5 rounded-full hover:bg-zinc-700/50 transition-colors">
                        <i data-lucide="x" class="w-4 h-4 text-zinc-400"></i>
                    </button>
                </div>
            </div>

            <!-- Chat Messages -->
            <div x-ref="chatLogs" class="px-6 py-3 overflow-y-auto space-y-3 scrollbar-thin scrollbar-thumb-zinc-700 scrollbar-track-transparent" style="max-height: 350px;">
                
                <!-- Welcome Message (always shown first) -->
                <div class="flex gap-3 justify-start">
                    <div class="w-8 h-8 rounded-full bg-gradient-to-br from-orange-500 to-red-600 flex items-center justify-center flex-shrink-0 shadow-lg">
                        <i data-lucide="bot" class="w-5 h-5 text-white"></i>
                    </div>
                    <div class="flex flex-col items-start">
                        <div class="px-4 py-2.5 rounded-2xl max-w-xs break-words shadow-md bg-zinc-700/50 text-zinc-100">
                            <p class="text-sm leading-relaxed" style="font-family: 'Inter', 'Segoe UI', system-ui, sans-serif;">Halo! 👋 Saya adalah asisten AI NINEVENTORY. Ada yang bisa saya bantu?</p>
                        </div>
                    </div>
                </div>
                
                <!-- Quick Reply Suggestions (shown when no user messages) -->
                <div x-show="messages.filter(m => m.sender === 'user').length === 0" class="space-y-3">
                    <p class="text-xs font-semibold text-zinc-400 mb-3" style="font-family: 'Inter', 'Segoe UI', system-ui, sans-serif;">💡 Pertanyaan yang sering diajukan:</p>
                    <button @click="message = 'Bagaimana cara meminjam barang?'; handleSend()" class="w-full text-left px-4 py-3 bg-zinc-800/50 hover:bg-zinc-700/60 border border-zinc-700/50 rounded-xl transition-all duration-200 group">
                        <p class="text-sm text-zinc-200 font-medium" style="font-family: 'Inter', 'Segoe UI', system-ui, sans-serif;">📦 Bagaimana cara meminjam barang?</p>
                    </button>
                    <button @click="message = 'Apa saja barang yang tersedia?'; handleSend()" class="w-full text-left px-4 py-3 bg-zinc-800/50 hover:bg-zinc-700/60 border border-zinc-700/50 rounded-xl transition-all duration-200 group">
                        <p class="text-sm text-zinc-200 font-medium" style="font-family: 'Inter', 'Segoe UI', system-ui, sans-serif;">🔍 Apa saja barang yang tersedia?</p>
                    </button>
                    <button @click="message = 'Bagaimana cara mengembalikan barang?'; handleSend()" class="w-full text-left px-4 py-3 bg-zinc-800/50 hover:bg-zinc-700/60 border border-zinc-700/50 rounded-xl transition-all duration-200 group">
                        <p class="text-sm text-zinc-200 font-medium" style="font-family: 'Inter', 'Segoe UI', system-ui, sans-serif;">↩️ Bagaimana cara mengembalikan barang?</p>
                    </button>
                    <button @click="message = 'Berapa lama durasi peminjaman?'; handleSend()" class="w-full text-left px-4 py-3 bg-zinc-800/50 hover:bg-zinc-700/60 border border-zinc-700/50 rounded-xl transition-all duration-200 group">
                        <p class="text-sm text-zinc-200 font-medium" style="font-family: 'Inter', 'Segoe UI', system-ui, sans-serif;">⏱️ Berapa lama durasi peminjaman?</p>
                    </button>
                </div>
                
                <template x-for="(msg, index) in messages" :key="index">
                    <div class="flex gap-3" :class="msg.sender === 'user' ? 'justify-end' : 'justify-start'">
                        <!-- Bot Avatar with Lucide Icon -->
                        <div x-show="msg.sender === 'bot'" class="w-8 h-8 rounded-full bg-gradient-to-br from-orange-500 to-red-600 flex items-center justify-center flex-shrink-0 shadow-lg">
                            <i data-lucide="bot" class="w-5 h-5 text-white"></i>
                        </div>
                        
                        <div class="flex flex-col" :class="msg.sender === 'user' ? 'items-end' : 'items-start'">
                            <div class="px-4 py-2.5 rounded-2xl max-w-xs break-words shadow-md"
                                 :class="msg.sender === 'user' ? 'bg-gradient-to-r from-orange-500 to-red-600 text-white' : 'bg-zinc-700/50 text-zinc-100'">
                                <p class="text-sm leading-relaxed whitespace-pre-wrap" style="font-family: 'Inter', 'Segoe UI', system-ui, sans-serif;" x-html="formatMessage(msg.text)"></p>
                            </div>
                            <span class="text-xs text-zinc-500 mt-1.5 font-medium" style="font-family: 'Inter', 'Segoe UI', system-ui, sans-serif;" x-text="msg.time"></span>
                        </div>
                        
                        <!-- User Avatar with Lucide Icon -->
                        <div x-show="msg.sender === 'user'" class="w-8 h-8 rounded-full bg-zinc-700 flex items-center justify-center flex-shrink-0 shadow-lg">
                            <i data-lucide="user" class="w-5 h-5 text-white"></i>
                        </div>
                    </div>
                </template>
                
                <!-- Typing Indicator -->
                <div x-show="isTyping" class="flex gap-3">
                    <div class="w-8 h-8 rounded-full bg-gradient-to-br from-orange-500 to-red-600 flex items-center justify-center flex-shrink-0 shadow-lg">
                        <i data-lucide="bot" class="w-5 h-5 text-white"></i>
                    </div>
                    <div class="px-4 py-3 rounded-2xl bg-zinc-700/50 shadow-md">
                        <div class="flex gap-1">
                            <div class="w-2 h-2 bg-zinc-400 rounded-full animate-bounce" style="animation-delay: 0ms;"></div>
                            <div class="w-2 h-2 bg-zinc-400 rounded-full animate-bounce" style="animation-delay: 150ms;"></div>
                            <div class="w-2 h-2 bg-zinc-400 rounded-full animate-bounce" style="animation-delay: 300ms;"></div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Input Area (Compact Single Row) -->
            <div class="px-4 py-3 border-t border-zinc-700/50 bg-zinc-800/30 backdrop-blur-sm">
                <div class="flex items-end gap-2 bg-zinc-900/50 rounded-2xl p-2 border border-zinc-700/50 focus-within:border-orange-500/50 focus-within:ring-1 focus-within:ring-orange-500/20 transition-all duration-300 shadow-inner">
                    <textarea
                        x-model="message"
                        @input="handleInputChange($event)"
                        @keydown="handleKeyDown($event)"
                        rows="1"
                        class="flex-1 max-h-32 px-3 py-2 bg-transparent border-none outline-none resize-none text-[15px] font-normal leading-relaxed text-zinc-100 placeholder-zinc-500 min-h-[40px]"
                        placeholder="Tanya apa saja tentang inventaris..."
                        style="font-family: 'Inter', 'Segoe UI', system-ui, sans-serif;"></textarea>
                    
                    <div class="flex items-center gap-3 pb-1.5 pr-2">
                        <!-- Character Counter -->
                        <div class="text-[10px] font-medium text-zinc-500 select-none whitespace-nowrap" style="font-family: 'Inter', 'Segoe UI', system-ui, sans-serif;">
                            <span x-text="message.length"></span>/2000
                        </div>

                        <!-- Send Button -->
                        <button 
                            @click="handleSend()"
                            :disabled="!message.trim() || isTyping"
                            class="group relative w-9 h-9 rounded-xl flex items-center justify-center transition-all duration-300 transform hover:scale-105 disabled:opacity-50 disabled:cursor-not-allowed disabled:hover:scale-100 overflow-hidden"
                            :class="message.trim() ? 'bg-gradient-to-br from-orange-500 to-red-600 shadow-lg shadow-orange-500/20' : 'bg-zinc-700/50 text-zinc-400'">
                            
                            <div class="absolute inset-0 bg-white/20 translate-y-full group-hover:translate-y-0 transition-transform duration-300"></div>
                            <i data-lucide="send" class="w-4 h-4 text-white relative z-10 transition-transform duration-300 group-hover:-translate-y-0.5 group-hover:translate-x-0.5"></i>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Floating Overlay -->
            <div class="absolute inset-0 rounded-3xl pointer-events-none" style="background: linear-gradient(135deg, rgba(255,102,38,0.05), transparent, rgba(239,68,68,0.05));"></div>
        </div>
    </div>

    <!-- Custom Styles -->
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap');
        
        .floating-ai-button:hover {
            transform: scale(1.1) rotate(5deg);
            box-shadow: 0 0 30px rgba(255,102,38,0.9), 0 0 50px rgba(239,68,68,0.7), 0 0 70px rgba(220,38,38,0.5);
        }
        
        .scrollbar-thin::-webkit-scrollbar {
            width: 6px;
        }
        
        .scrollbar-thin::-webkit-scrollbar-track {
            background: transparent;
        }
        
        .scrollbar-thin::-webkit-scrollbar-thumb {
            background: rgb(63 63 70);
            border-radius: 3px;
        }
        
        .scrollbar-thin::-webkit-scrollbar-thumb:hover {
            background: rgb(82 82 91);
        }
    </style>
</div>
