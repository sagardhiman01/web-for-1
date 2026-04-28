<div id="cai-chatbot" class="cai-chatbot">
    <button type="button" id="cai-chatbot-toggle" class="cai-chatbot-toggle" aria-label="Open chat assistant">
        <span class="cai-chatbot-ring"></span>
        <span class="cai-chatbot-icon"><i class="las la-robot"></i></span>
        <span class="cai-chatbot-text">AI Support</span>
    </button>

    <div id="cai-chatbot-panel" class="cai-chatbot-panel d-none">
        <div class="cai-chatbot-header">
            <div>
                <h6 class="mb-0">Core AI Assistant</h6>
                <small>Register, login, plans, deposit, withdrawal help</small>
            </div>
            <button type="button" id="cai-chatbot-close" class="cai-chatbot-close" aria-label="Close chat">
                <i class="las la-times"></i>
            </button>
        </div>

        <div id="cai-chatbot-messages" class="cai-chatbot-messages"></div>

        <form id="cai-chatbot-form" class="cai-chatbot-form">
            <input type="text" id="cai-chatbot-input" maxlength="500" placeholder="Ask: register, login, plan, deposit, withdraw, 2FA..." required>
            <button type="submit" id="cai-chatbot-send" class="cai-chatbot-send">Send</button>
        </form>
    </div>
</div>

<style>
    .cai-chatbot {
        position: fixed;
        right: 22px;
        bottom: 22px;
        z-index: 99999;
    }

    .cai-chatbot-toggle {
        position: relative;
        display: inline-flex;
        align-items: center;
        gap: 10px;
        height: 72px;
        padding: 0 22px 0 16px;
        border: 0;
        border-radius: 999px;
        background: linear-gradient(135deg, #ffb347 0%, #ff7b22 45%, #ff4d1f 100%);
        color: #ffffff;
        font-size: 15px;
        font-weight: 700;
        letter-spacing: 0.2px;
        box-shadow: 0 16px 34px rgba(255, 90, 30, 0.45), inset 0 1px 0 rgba(255, 255, 255, 0.35);
        transition: transform 0.2s ease, box-shadow 0.2s ease;
        animation: caiFloat 3s ease-in-out infinite;
    }

    .cai-chatbot-toggle:hover {
        transform: translateY(-2px) scale(1.01);
        box-shadow: 0 20px 40px rgba(255, 90, 30, 0.52), inset 0 1px 0 rgba(255, 255, 255, 0.4);
    }

    .cai-chatbot-toggle:focus {
        outline: 0;
    }

    .cai-chatbot-ring {
        position: absolute;
        inset: -6px;
        border-radius: 999px;
        border: 2px solid rgba(255, 170, 90, 0.55);
        animation: caiPulse 1.9s ease-out infinite;
        pointer-events: none;
    }

    .cai-chatbot-icon {
        width: 42px;
        height: 42px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 21px;
        background: rgba(12, 17, 29, 0.32);
        border: 1px solid rgba(255, 255, 255, 0.28);
    }

    .cai-chatbot-text {
        white-space: nowrap;
    }

    .cai-chatbot-panel {
        width: 390px;
        max-width: calc(100vw - 26px);
        height: 540px;
        background: #0f1624;
        color: #e8eefc;
        border: 1px solid rgba(255, 153, 51, 0.35);
        border-radius: 16px;
        box-shadow: 0 20px 44px rgba(0, 0, 0, 0.55);
        overflow: hidden;
        display: flex;
        flex-direction: column;
        margin-bottom: 14px;
    }

    .cai-chatbot-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 13px 15px;
        border-bottom: 1px solid rgba(255, 255, 255, 0.08);
        background: linear-gradient(90deg, rgba(255, 153, 51, 0.2), rgba(255, 77, 31, 0.2));
    }

    .cai-chatbot-close {
        border: 0;
        background: transparent;
        color: #ffffff;
        font-size: 20px;
    }

    .cai-chatbot-messages {
        flex: 1;
        padding: 12px;
        overflow-y: auto;
        display: flex;
        flex-direction: column;
        gap: 10px;
    }

    .cai-chat-line {
        max-width: 87%;
        padding: 10px 11px;
        border-radius: 11px;
        font-size: 13px;
        line-height: 1.5;
        white-space: pre-line;
    }

    .cai-chat-line.bot {
        background: rgba(255, 255, 255, 0.08);
        align-self: flex-start;
    }

    .cai-chat-line.user {
        background: linear-gradient(135deg, #ffb347, #ff7b22);
        color: #181818;
        align-self: flex-end;
        font-weight: 600;
    }

    .cai-chatbot-form {
        display: flex;
        gap: 8px;
        padding: 10px;
        border-top: 1px solid rgba(255, 255, 255, 0.08);
        background: #0b1220;
    }

    .cai-chatbot-form input {
        flex: 1;
        min-width: 0;
        border-radius: 9px;
        border: 1px solid rgba(255, 255, 255, 0.2);
        background: #1a2232;
        color: #ffffff;
        padding: 8px 10px;
        font-size: 13px;
    }

    .cai-chatbot-form input::placeholder {
        color: rgba(255, 255, 255, 0.58);
    }

    .cai-chatbot-send {
        border: 0;
        border-radius: 9px;
        padding: 0 14px;
        font-size: 13px;
        font-weight: 700;
        background: linear-gradient(135deg, #ffb347, #ff7b22);
        color: #171717;
    }

    @keyframes caiPulse {
        0% {
            transform: scale(1);
            opacity: 0.75;
        }
        70% {
            transform: scale(1.08);
            opacity: 0;
        }
        100% {
            transform: scale(1.08);
            opacity: 0;
        }
    }

    @keyframes caiFloat {
        0%, 100% {
            transform: translateY(0);
        }
        50% {
            transform: translateY(-4px);
        }
    }

    @media (max-width: 575px) {
        .cai-chatbot {
            right: 12px;
            bottom: 12px;
        }

        .cai-chatbot-toggle {
            height: 62px;
            padding: 0 16px 0 12px;
            font-size: 14px;
            gap: 8px;
        }

        .cai-chatbot-icon {
            width: 36px;
            height: 36px;
            font-size: 18px;
        }

        .cai-chatbot-panel {
            width: 95vw;
            height: 66vh;
        }
    }
</style>

<script>
    (function () {
        "use strict";

        const panel = document.getElementById('cai-chatbot-panel');
        const toggle = document.getElementById('cai-chatbot-toggle');
        const closeBtn = document.getElementById('cai-chatbot-close');
        const form = document.getElementById('cai-chatbot-form');
        const input = document.getElementById('cai-chatbot-input');
        const sendBtn = document.getElementById('cai-chatbot-send');
        const messages = document.getElementById('cai-chatbot-messages');
        let greeted = false;

        if (!panel || !toggle || !closeBtn || !form || !input || !sendBtn || !messages) {
            return;
        }

        const appendMessage = (type, text) => {
            const div = document.createElement('div');
            div.className = `cai-chat-line ${type}`;
            div.textContent = text;
            messages.appendChild(div);
            messages.scrollTop = messages.scrollHeight;
        };

        const openChat = () => {
            panel.classList.remove('d-none');
            if (!greeted) {
                appendMessage('bot', 'Hello! I am Core AI assistant. You can ask about register, login, plans, deposit, withdrawal, 2FA, password change, and company details.');
                greeted = true;
            }
            input.focus();
        };

        const closeChat = () => panel.classList.add('d-none');

        toggle.addEventListener('click', () => {
            if (panel.classList.contains('d-none')) {
                openChat();
            } else {
                closeChat();
            }
        });

        closeBtn.addEventListener('click', closeChat);

        form.addEventListener('submit', async (e) => {
            e.preventDefault();
            const text = input.value.trim();
            if (!text) {
                return;
            }

            appendMessage('user', text);
            input.value = '';
            sendBtn.disabled = true;

            try {
                const response = await fetch("{{ route('chatbot.message') }}", {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': "{{ csrf_token() }}",
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: JSON.stringify({ message: text })
                });

                const data = await response.json();

                if (response.ok && data.reply) {
                    appendMessage('bot', data.reply);
                } else {
                    appendMessage('bot', 'Sorry, I could not process that. Please try again.');
                }
            } catch (error) {
                appendMessage('bot', 'Network issue. Please try again in a moment.');
            } finally {
                sendBtn.disabled = false;
                input.focus();
            }
        });
    })();
</script>
