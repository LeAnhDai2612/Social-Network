document.addEventListener('DOMContentLoaded', () => {
    const chatBubble = document.getElementById('ai-chat-bubble');
    const chatWindow = document.getElementById('ai-chat-window');
    const closeButton = document.getElementById('ai-chat-close');
    const messagesArea = document.getElementById('ai-chat-messages');
    const chatForm = document.getElementById('ai-chat-form');
    const chatInput = document.getElementById('ai-chat-input');
    const sendButton = document.getElementById('ai-chat-send');

    function scrollToBottom() {
        messagesArea.scrollTop = messagesArea.scrollHeight;
    }

    function addMessage(message, sender) {
        const messageDiv = document.createElement('div');
        messageDiv.classList.add(sender === 'user' ? 'user-message' : 'ai-message');
        if (sender === 'error') {
            messageDiv.classList.add('error');
            messageDiv.classList.remove('ai-message');
        }
        messageDiv.textContent = message;
        messagesArea.appendChild(messageDiv);
        scrollToBottom();
    }

    if (chatBubble) {
        chatBubble.addEventListener('click', () => {
            chatWindow.classList.toggle('show');
             if (chatWindow.classList.contains('show')) {
                chatInput.focus();
            }
        });
    }

    if (closeButton) {
        closeButton.addEventListener('click', () => {
            chatWindow.classList.remove('show');
        });
    }

    if (chatForm) {
        chatForm.addEventListener('submit', async (e) => {
            e.preventDefault();
            const userInput = chatInput.value.trim();
            if (!userInput) return;

            addMessage(userInput, 'user');
            chatInput.value = '';
            sendButton.disabled = true;

            const loadingDiv = document.createElement('div');
            loadingDiv.classList.add('ai-message');
            loadingDiv.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Đang xử lý...';
            messagesArea.appendChild(loadingDiv);
            scrollToBottom();

            try {
                const response = await fetch('call_api.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({ input: userInput })
                });

                 messagesArea.removeChild(loadingDiv);

                if (!response.ok) {
                    let errorMsg = `Lỗi mạng: ${response.status}`;
                    try {
                        const errData = await response.json();
                        errorMsg = errData.error || errorMsg;
                    } catch(parseError) {
                        try {
                           const textError = await response.text();
                           if(textError) errorMsg = textError;
                        } catch(textReadError){}
                    }
                     throw new Error(errorMsg);
                }

                const data = await response.json();

                if (data.success) {
                    addMessage(data.response, 'ai');
                } else {
                    addMessage(data.error || 'AI không thể trả lời.', 'error');
                }

            } catch (error) {
                console.error("Error fetching AI response:", error);
                 // Kiểm tra xem loadingDiv có còn tồn tại không trước khi xóa
                 if (messagesArea.contains(loadingDiv)) {
                    messagesArea.removeChild(loadingDiv);
                 }
                addMessage(`Lỗi: ${error.message}`, 'error');
            } finally {
                 sendButton.disabled = false;
                 chatInput.focus();
            }
        });
    }
});