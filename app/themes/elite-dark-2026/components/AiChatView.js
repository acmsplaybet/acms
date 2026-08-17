// ==============================================================================
// 2026 DARK ELITE - AI CHAT ASSISTANT COMPONENT (GENIUS AI)
// Interactive Value Bet Scanner, Odds Analyzer & Chatbot Simulator
// ==============================================================================

window.EliteAiChatView = {
    template: `
        <div class="elite-view-container elite-ai-chat-view">
            <div class="elite-view-header" style="padding-bottom:8px;">
                <div style="display:flex; align-items:center; gap:8px;">
                    <div class="ai-bot-avatar">🤖</div>
                    <div>
                        <h2 style="font-size:16px; font-weight:800; margin:0;">Genius AI Analytics</h2>
                        <span style="font-size:11px; color:var(--elite-emerald); font-weight:700;">● Online • Scanning 40+ Leagues</span>
                    </div>
                </div>
            </div>

            <!-- Quick Questions Carousel -->
            <div class="ai-prompts-scroll">
                <button v-for="prompt in quickPrompts" :key="prompt" 
                        class="ai-prompt-pill elite-tap"
                        @click="sendPrompt(prompt)">
                    {{ prompt }}
                </button>
            </div>

            <!-- Messages Log -->
            <div class="ai-chat-messages" ref="chatBox">
                <div v-for="msg in messages" :key="msg.id" 
                     class="ai-msg-row" 
                     :class="msg.sender">
                    <div class="ai-msg-bubble">
                        <div class="msg-author" v-if="msg.sender === 'bot'">🤖 Genius AI</div>
                        <p class="msg-text" v-html="msg.text"></p>
                        <span class="msg-time">{{ msg.time }}</span>
                    </div>
                </div>
                
                <div v-if="isTyping" class="ai-msg-row bot">
                    <div class="ai-msg-bubble typing">
                        <span>Thinking...</span>
                    </div>
                </div>
            </div>

            <!-- Chat Input Bar -->
            <div class="ai-chat-input-bar">
                <input type="text" 
                       class="ai-input" 
                       v-model="inputQuery" 
                       @keyup.enter="handleSend"
                       placeholder="Ask about any match or market...">
                <button class="ai-send-btn elite-tap" @click="handleSend" :disabled="!inputQuery.trim()">
                    ➤
                </button>
            </div>
        </div>
    `,
    setup() {
        const inputQuery = Vue.ref('');
        const isTyping = Vue.ref(false);
        const chatBox = Vue.ref(null);

        const quickPrompts = [
            '🔥 Best Banker Today',
            '⚽ High Probability Goals',
            '🛡️ Safest Double Chance',
            '📊 Explain Lanus vs Independiente'
        ];

        const messages = Vue.value || Vue.ref([
            {
                id: 1,
                sender: 'bot',
                text: 'Welcome to Genius AI 2026! I analyze live xG, team strength metrics, and market variance across 40+ leagues to find value bets. How can I assist your betslip today?',
                time: 'Just now'
            }
        ]);

        function sendPrompt(text) {
            inputQuery.value = text;
            handleSend();
        }

        function handleSend() {
            const query = inputQuery.value.trim();
            if (!query) return;

            const userMsg = {
                id: Date.now(),
                sender: 'user',
                text: query,
                time: new Date().toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' })
            };
            messages.value.push(userMsg);
            inputQuery.value = '';
            isTyping.value = true;

            setTimeout(() => {
                let reply = '';
                const lower = query.toLowerCase();

                if (lower.includes('banker') || lower.includes('best')) {
                    reply = '🎯 <strong>Top Banker Pick Today:</strong><br><strong>Lanus vs Independiente</strong><br>Prediction: <strong>Home Win (1) @ 1.72</strong><br>Confidence: <strong>86%</strong> (High xG home dominance).';
                } else if (lower.includes('goal') || lower.includes('btts')) {
                    reply = '⚽ <strong>High Probability Goals:</strong><br><strong>Inter Milan vs Atalanta</strong><br>Prediction: <strong>Over 2.5 Goals @ 1.85</strong> (Both teams have averaged 2.8 total goals in their last 5 matches).';
                } else if (lower.includes('double') || lower.includes('safe')) {
                    reply = '🛡️ <strong>Safest Double Chance:</strong><br><strong>Galatasaray vs Hatayspor</strong><br>Prediction: <strong>1X @ 1.18</strong> (Galatasaray undefeated at home in 14 matches).';
                } else {
                    reply = `🔍 <strong>Analysis for "${query}":</strong><br>Our algorithm detects strong home defensive stability and favorable value odds on <strong>Home Win / Draw No Bet</strong>.`;
                }

                messages.value.push({
                    id: Date.now() + 1,
                    sender: 'bot',
                    text: reply,
                    time: new Date().toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' })
                });
                isTyping.value = false;
            }, 600);
        }

        return {
            inputQuery,
            isTyping,
            chatBox,
            quickPrompts,
            messages,
            sendPrompt,
            handleSend
        };
    }
};
