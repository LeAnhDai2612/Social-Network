<?php
session_name('USER_SESSION');
session_start();
require_once '../core/utility_functions.php';
redirect_if_not_logged_in();

$my_id = $_SESSION['user_id'];
$active_page = 'inbox';
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hộp thư</title>
    <link rel="icon" type="image/x-icon" href="images/favicon.ico">
    <script>
      (function() {
        const storedTheme = localStorage.getItem('theme') ||
          (window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light');
        if (storedTheme) {
          document.documentElement.setAttribute('data-bs-theme', storedTheme);
        }
      })();
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="css/style.css">

    <script src="https://cdn.jsdelivr.net/npm/minisearch@6.1.0/dist/umd/index.min.js"></script>

    <script type="module" src="scripts/show-search-suggestions.js" defer></script>
    <script src="scripts/lazy-load.js" defer></script>

    <style>
        /* CSS Inbox Specific */
        .main-content {
            height: 100vh;
            overflow-y: hidden;
        }
        main.flex-grow-1 {
        height: 100%;
        overflow: hidden;
        padding-top: 80px;
    }
         .inbox-sidebar .list-group-item {
            cursor: pointer;
        }
         .chat-main {
            display: flex;
            flex-direction: column;
            height: 100%;
        }
        .chat-messages {
           /* Uses overflow-auto from Bootstrap */
        }
        .chat-input textarea {
            max-height: 120px;
            min-height: 40px;
            height: auto;
        }
        .message-wrapper {
            margin-bottom: 0.5rem;
        }
        .message-bubble {
            display: inline-block;
            word-break: break-word;
        }
        .list-group-item-action:hover,
        .list-group-item-action:focus {
            background-color: var(--bs-tertiary-bg);
        }
        @media (max-width: 991.98px) {
            .inbox-sidebar.d-flex {
                height: 100%;
                position: absolute;
                width: 100%;
                z-index: 10;
                background-color: var(--bs-body-bg);
                overflow-y: auto;
            }
             .chat-main {
                 height: 100%;
             }
             .main-content.flex-grow-1 {
                 overflow-y: hidden;
             }
        }
        .read-status {
            font-size: 0.75em;
            clear: both;
        }
         .delete-btn {
             opacity: 0.5;
             transition: opacity 0.2s ease-in-out;
             font-size: 0.9em;
         }
         .message-bubble:hover .delete-btn {
             opacity: 1;
         }
    </style>
    <!-- Bootstrap JS phải được tải TRƯỚC script của bạn -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous" defer></script>
</head>
<body class="bg-body-tertiary">
    <div class="d-flex flex-column flex-lg-row h-100">
        <?php include('partials/sidebar.php'); ?>

        <div class="main-content flex-grow-1 d-flex flex-column">
            <?php include('partials/header.php'); ?>
            <main class="flex-grow-1 overflow-hidden">
                <div class="container-fluid h-100">
                    <div class="row h-100">
                        <div class="col-12 col-lg-4 col-xl-3 border-end border-subtle p-0 d-flex flex-column inbox-sidebar" id="inbox-sidebar">
                            <div class="p-3 border-bottom border-subtle">
                                <h5 class="mb-0 fw-semibold">Tin nhắn</h5>
                            </div>
                            <div class="list-group list-group-flush overflow-auto flex-grow-1" id="conversation-list">
                                <div class="p-5 text-center text-muted" id="conversation-loading">
                                    <div class="spinner-border spinner-border-sm" role="status">
                                        <span class="visually-hidden">Đang tải...</span>
                                    </div>
                                    <span class="ms-2">Đang tải cuộc trò chuyện...</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-12 col-lg-8 col-xl-9 p-0 d-flex flex-column h-100 position-relative">
                            <div class="chat-main flex-grow-1 d-flex flex-column" id="chat-main-area" style="display: none;">
                                <div class="chat-header p-3 border-bottom border-subtle d-flex align-items-center bg-body">
                                    <button class="btn btn-link text-decoration-none link-body-emphasis me-2 d-lg-none p-0 border-0" id="back-to-list-btn" title="Quay lại danh sách">
                                        <i class="bi bi-arrow-left fs-5"></i>
                                    </button>
                                    <img src="https://res.cloudinary.com/dy6o43c27/image/upload/v1743922855/socialnetwork/profile-pictures/hx5eeq7gbw70pjef0ugm.jpg" id="chat-header-avatar" alt="Avatar" class="rounded-circle me-2" style="width: 40px; height: 40px; object-fit: cover;">
                                    <div class="flex-grow-1">
                                        <h6 class="mb-0 fw-semibold text-truncate" id="chat-header-name">Chọn một cuộc trò chuyện</h6>
                                        <small class="text-muted" id="chat-header-status"></small>
                                    </div>
                                    <a href="#" class="text-decoration-none link-secondary ms-2 flex-shrink-0" id="receiver-profile-link" target="_blank" title="Xem hồ sơ" style="display: none;">
                                        <i class="bi bi-person-circle fs-5"></i>
                                    </a>
                                </div>
                                <div class="chat-messages flex-grow-1 p-3 overflow-auto bg-body-secondary" id="chat-box">
                                    <div class="text-center text-muted p-5" id="message-placeholder">
                                        Chọn một cuộc trò chuyện để bắt đầu nhắn tin.
                                    </div>
                                </div>
                                <form id="message-form" class="chat-input p-3 border-top border-subtle bg-body d-flex align-items-center" enctype="multipart/form-data">
                                    <label for="file-input" class="btn btn-outline-secondary me-2" title="Đính kèm file">
                                        <i class="bi bi-paperclip"></i>
                                    </label>
                                    <input type="file" id="file-input" name="media" accept="image/*,video/*,.pdf,.doc,.zip" hidden>

                                    <textarea id="msg-input" name="content" class="form-control me-2" placeholder="Nhập tin nhắn..." rows="1" style="resize: none;" disabled></textarea>

                                    <button type="submit" id="send-button" class="btn btn-primary flex-shrink-0" title="Gửi tin nhắn" disabled>
                                        <i class="bi bi-send-fill"></i>
                                    </button>
                                </form>

                            </div>
                            <div class="d-none d-lg-flex flex-column justify-content-center align-items-center h-100 text-muted text-center" id="select-chat-placeholder">
                                <i class="bi bi-chat-left-dots-fill display-1 text-body-tertiary mb-3"></i>
                                <p class="fs-5 mb-1">Chọn một cuộc trò chuyện</p>
                                <p class="small">Bắt đầu nhắn tin với bạn bè của bạn.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>


    <div class="modal fade" id="deleteConfirmModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
     <div class="modal-dialog modal-dialog-centered">
         <div class="modal-content">
             <div class="modal-header border-0">
                 <h5 class="modal-title" id="deleteModalLabel">Xác nhận xóa tin nhắn</h5>
                 <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
             </div>
             <div class="modal-body pt-0">
                 Bạn muốn xóa tin nhắn này như thế nào?
             </div>
             <div class="modal-footer border-0 justify-content-center">
                 <button type="button" class="btn btn-outline-secondary" id="delete-for-me-btn">Chỉ xóa ở phía bạn</button>
                 <button type="button" class="btn btn-danger" id="delete-for-both-btn">Thu hồi tin nhắn</button>
             </div>
         </div>
     </div>
 </div>

    <!-- Bọc TOÀN BỘ script trong DOMContentLoaded -->
    <script>
      document.addEventListener('DOMContentLoaded', () => {
        const myId = <?= json_encode($my_id) ?>;
        let currentReceiverId = null;
        let currentReceiverInfo = null;
        let messagePollingInterval = null;
        let conversationPollingInterval = null;
        const POLLING_INTERVAL_MS = 5000;

        const conversationList = document.getElementById('conversation-list');
        const conversationLoading = document.getElementById('conversation-loading');
        const chatBox = document.getElementById('chat-box');
        const messageInput = document.getElementById('msg-input');
        const sendButton = document.getElementById('send-button');
        const chatHeaderAvatar = document.getElementById('chat-header-avatar');
        const chatHeaderName = document.getElementById('chat-header-name');
        const chatHeaderStatus = document.getElementById('chat-header-status');
        const receiverProfileLink = document.getElementById('receiver-profile-link');
        const chatMainArea = document.getElementById('chat-main-area');
        const messagePlaceholder = document.getElementById('message-placeholder');
        const selectChatPlaceholder = document.getElementById('select-chat-placeholder');
        const inboxSidebar = document.getElementById('inbox-sidebar');
        const backToListBtn = document.getElementById('back-to-list-btn');

        const deleteConfirmModalElement = document.getElementById('deleteConfirmModal');
        // Kiểm tra trước khi khởi tạo Modal
        const deleteConfirmModal = deleteConfirmModalElement ? bootstrap.Modal.getOrCreateInstance(deleteConfirmModalElement) : null;
        let deleteTargetId = null;

        const deleteForMeBtn = document.getElementById('delete-for-me-btn');
        const deleteForBothBtn = document.getElementById('delete-for-both-btn');
        document.getElementById('message-form')?.addEventListener('submit', sendMessage);

        if (deleteForMeBtn) {
            deleteForMeBtn.addEventListener('click', () => {
                deleteMessage('me'); // Gọi hàm deleteMessage trực tiếp
            });
        }

        if (deleteForBothBtn) {
            deleteForBothBtn.addEventListener('click', () => {
                deleteMessage('both'); // Gọi hàm deleteMessage trực tiếp
            });
        }
        function sanitizeHTML(str) {
            if (str === null || str === undefined) return '';
            const temp = document.createElement('div');
            temp.textContent = str;
            return temp.innerHTML;
        }

        function formatTime(dateString) {
            if (!dateString) return '';
            try {
                const date = new Date(dateString);
                if (isNaN(date.getTime())) return '';
                return date.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit', hour12: false });
            } catch (e) {
                console.error("Error formatting time:", e);
                return '';
            }
        }

        function scrollToBottom(element) {
            if (element) {
                element.scrollTop = element.scrollHeight;
            }
        }

        function updateChatHeader(user) {
             const defaultAvatar = 'https://res.cloudinary.com/dy6o43c27/image/upload/v1743922855/socialnetwork/profile-pictures/hx5eeq7gbw70pjef0ugm.jpg'; // Đường dẫn đúng
             if(!user) {
                 chatHeaderAvatar.src = defaultAvatar;
                 chatHeaderName.textContent = 'Chọn cuộc trò chuyện';
                 chatHeaderStatus.textContent = '';
                 receiverProfileLink.style.display = 'none';
                 messageInput.disabled = true;
                 sendButton.disabled = true;
                 return;
             }
            const displayName = user.name || user.display_name; // Ưu tiên 'name' từ backend suggestion
            chatHeaderAvatar.src = user.profile_picture_path || defaultAvatar;
            chatHeaderAvatar.onerror = () => { chatHeaderAvatar.src = defaultAvatar; };
            chatHeaderName.textContent = sanitizeHTML(displayName);
            chatHeaderStatus.textContent = ''; // Implement real status later
            receiverProfileLink.href = `user_profile.php?user_id=${user.id}`;
            receiverProfileLink.style.display = 'inline-block';
            messageInput.disabled = false;
            sendButton.disabled = false;
        }

         function activateConversationUI(userId) {
            currentReceiverId = userId;

            document.querySelectorAll('.list-group-item').forEach(el => el.classList.remove('active'));
            const activeElement = document.querySelector(`.list-group-item[data-userid="${userId}"]`);
            if (activeElement) {
                activeElement.classList.add('active');
            }

            chatMainArea.style.display = 'flex';
            messagePlaceholder.style.display = 'none';
            selectChatPlaceholder.classList.add('d-none');
            selectChatPlaceholder.classList.remove('d-lg-flex');


             if (window.innerWidth < 992) {
                 inboxSidebar.classList.remove('d-flex');
                 inboxSidebar.classList.add('d-none');
                 // Reset styles khi sidebar ẩn đi
                 inboxSidebar.style.position = '';
                 inboxSidebar.style.width = '';
                 inboxSidebar.style.zIndex = '';
                 inboxSidebar.style.backgroundColor = '';
             }

            loadMessages();
            startMessagePolling();
        }

        function showConversationList() {
             if (window.innerWidth < 992) {
                 chatMainArea.style.display = 'none';
                 inboxSidebar.classList.remove('d-none');
                 inboxSidebar.classList.add('d-flex');
                  // Add styles when sidebar is shown on small screens
                 inboxSidebar.style.position = 'absolute';
                 inboxSidebar.style.width = '100%';
                 inboxSidebar.style.zIndex = '10';
                 inboxSidebar.style.backgroundColor = 'var(--bs-body-bg)';
             }
        }

        function loadSuggestions() {
            conversationLoading.style.display = 'block';
            conversationList.innerHTML = '';
            fetch('../core/get_follow_suggestions.php')
                .then(res => res.ok ? res.json() : Promise.reject({ status: res.status, statusText: res.statusText }))
                .then(suggestions => {
                    conversationLoading.style.display = 'none';
                    if (!Array.isArray(suggestions)) {
                         throw new Error("Invalid data format for suggestions.");
                    }
                    if (suggestions.length === 0) {
                         conversationList.innerHTML = '<p class="text-center text-muted p-3 mb-0">Không có gợi ý nào hoặc bạn chưa theo dõi ai.</p>';
                         return;
                    }

                    const header = document.createElement('div');
                    header.className = 'p-3 border-bottom border-subtle';
                    header.innerHTML = '<h6 class="mb-0 text-muted small text-uppercase">Gợi ý liên hệ</h6>';
                    conversationList.appendChild(header);

                    const defaultAvatar = 'https://res.cloudinary.com/dy6o43c27/image/upload/v1743922855/socialnetwork/profile-pictures/hx5eeq7gbw70pjef0ugm.jpg'; // Đường dẫn đúng
                    suggestions.forEach(user => {
                        const displayName = user.name || user.display_name;
                        const avatarUrl = user.profile_picture_path || defaultAvatar;

                        const div = document.createElement('a');
                        div.href = "#";
                        div.className = `list-group-item list-group-item-action d-flex align-items-center p-3`;
                        div.dataset.userid = user.id;

                        div.innerHTML = `
                            <img src="${avatarUrl}" onerror="this.onerror=null; this.src='${defaultAvatar}';"
                                 alt="Avatar" class="rounded-circle me-3 flex-shrink-0" style="width: 45px; height: 45px; object-fit: cover;">
                            <div class="flex-grow-1 text-truncate">
                                <strong class="mb-1 text-truncate d-block">${sanitizeHTML(displayName)}</strong>
                                <small class="text-muted text-truncate d-block w-100">@${sanitizeHTML(user.username)}</small>
                            </div>
                            <button class="btn btn-sm btn-outline-primary ms-2 flex-shrink-0 start-chat-btn" style="white-space: nowrap;">Nhắn tin</button>
                        `;

                        div.onclick = (e) => {
                            e.preventDefault();
                            currentReceiverInfo = user;
                            updateChatHeader(user);
                            activateConversationUI(user.id);
                            // Optional: Immediately load conversations again to show the new (empty) chat in the list
                            // loadConversations();
                        };
                        conversationList.appendChild(div);
                    });

                })
                .catch(error => {
                    console.error('Error loading suggestions:', error);
                    conversationLoading.style.display = 'none';
                    conversationList.innerHTML = '<p class="text-center text-danger p-3 mb-0">Lỗi tải gợi ý.</p>';
                });
        }


         function loadConversations() {
            conversationLoading.style.display = 'block';
            fetch('../core/get_conversations.php')
                .then(res => res.ok ? res.json() : Promise.reject({ status: res.status, statusText: res.statusText }))
                .then(data => {
                    if (!Array.isArray(data)) {
                        throw new Error("Invalid data format for conversations.");
                    }
                    conversationLoading.style.display = 'none';
                    conversationList.innerHTML = '';

                    if (data.length === 0) {
                         loadSuggestions();
                         return;
                    }

                    const defaultAvatar = 'https://res.cloudinary.com/dy6o43c27/image/upload/v1743922855/socialnetwork/profile-pictures/hx5eeq7gbw70pjef0ugm.jpg'; // Đường dẫn đúng
                    data.forEach(user => {
                        const isActive = user.id === currentReceiverId;
                        const displayName = user.name || user.display_name;
                        const avatarUrl = user.profile_picture_path || defaultAvatar;
                        const lastMessage = user.last_message ? sanitizeHTML(user.last_message) : '<i>Chưa có tin nhắn</i>';
                        const lastMessageTime = formatTime(user.last_message_time);

                        const div = document.createElement('a');
                        div.href = "#";
                        div.className = `list-group-item list-group-item-action d-flex align-items-center p-3 ${isActive ? 'active' : ''}`;
                        div.dataset.userid = user.id;

                        div.innerHTML = `
                            <img src="${avatarUrl}" onerror="this.onerror=null; this.src='${defaultAvatar}';"
                                 alt="Avatar" class="rounded-circle me-3 flex-shrink-0" style="width: 45px; height: 45px; object-fit: cover;">
                            <div class="flex-grow-1 text-truncate">
                                <div class="d-flex justify-content-between w-100">
                                     <strong class="mb-1 text-truncate d-block">${sanitizeHTML(displayName)}</strong>
                                     ${lastMessageTime ? `<small class="text-muted flex-shrink-0 ms-2">${lastMessageTime}</small>` : ''}
                                </div>
                                <small class="text-muted text-truncate d-block w-100">${lastMessage}</small>
                            </div>
                        `;

                        div.onclick = (e) => {
                            e.preventDefault();
                            if (currentReceiverId !== user.id) {
                                currentReceiverInfo = user;
                                updateChatHeader(user);
                                activateConversationUI(user.id);
                            } else {
                                if (window.innerWidth < 992) {
                                     activateConversationUI(user.id);
                                }
                            }
                        };
                        conversationList.appendChild(div);
                    });
                })
                .catch(error => {
                    console.error('Error loading conversations:', error);
                    conversationLoading.style.display = 'none';
                    conversationList.innerHTML = '<p class="text-center text-danger p-3 mb-0">Lỗi tải cuộc trò chuyện.</p>';
                });
        }

        function loadMessages() {
             if (!currentReceiverId) return;

            const shouldScrollAfterLoad = (chatBox.scrollHeight - chatBox.scrollTop - chatBox.clientHeight) < 100;

            fetch(`../core/get_messages.php?user_id=${currentReceiverId}`)
                .then(res => res.ok ? res.json() : Promise.reject({ status: res.status, statusText: res.statusText }))
                .then(data => {
                    if (!data || !Array.isArray(data.messages)) {
                        throw new Error("Invalid data format for messages.");
                    }

                    const lastUiMsgElement = chatBox.querySelector('.message-wrapper:last-child .message-bubble');
                    const lastUiMsgId = lastUiMsgElement ? lastUiMsgElement.dataset.id : null;
                    const lastDbMsg = data.messages[data.messages.length - 1];
                    const lastDbMsgId = lastDbMsg ? lastDbMsg.id : null;
                    if (lastDbMsgId && String(lastDbMsgId) === String(lastUiMsgId) && !lastDbMsgId.toString().startsWith('temp_')) {
                        const lastUiStatus = chatBox.querySelector('.read-status');
                        if (lastDbMsg.sender_id == myId && lastDbMsg.seen && !lastUiStatus) {
                            const statusDiv = document.createElement('div');
                            statusDiv.className = 'read-status text-muted small text-end mt-1';
                            statusDiv.textContent = 'Đã xem';
                            const lastWrapper = chatBox.querySelector('.message-wrapper:last-child');
                            if(lastWrapper) lastWrapper.appendChild(statusDiv);
                            if (shouldScrollAfterLoad) scrollToBottom(chatBox);
                        } else if (lastDbMsg.sender_id == myId && !lastDbMsg.seen && lastUiStatus) {
                            lastUiStatus.remove();
                        }
                        return;
                    }


                    chatBox.innerHTML = '';
                    messagePlaceholder.style.display = 'none';

                    if (data.messages.length === 0) {
                        messagePlaceholder.textContent = 'Bắt đầu cuộc trò chuyện nào!';
                        messagePlaceholder.style.display = 'block';
                        messagePlaceholder.classList.remove('text-danger');
                    } else {
                         data.messages.forEach((msg, index) => {
                            const isMe = msg.sender_id == myId;
                            const messageWrapper = document.createElement('div');
                            messageWrapper.className = `message-wrapper d-flex ${isMe ? 'justify-content-end' : 'justify-content-start'}`;

                            const bubble = document.createElement('div');
                            bubble.className = `message-bubble p-2 rounded ${isMe ? 'bg-primary text-white' : 'bg-body-secondary'}`;
                            bubble.dataset.id = msg.id;
                            bubble.style.maxWidth = '70%';

                            const contentDiv = document.createElement('div');
                            if (msg.content) {
                                contentDiv.innerHTML = sanitizeHTML(msg.content);
                            }

                            if (msg.media_url && msg.media_type) {
                                const media = document.createElement(msg.media_type.startsWith('image/') ? 'img' : msg.media_type.startsWith('video/') ? 'video' : 'a');

                                if (media.tagName === 'IMG') {
                                    media.src = msg.media_url;
                                    media.style.maxWidth = '100%';
                                    media.className = 'mt-2 rounded';
                                } else if (media.tagName === 'VIDEO') {
                                    media.controls = true;
                                    media.src = msg.media_url;
                                    media.className = 'mt-2 rounded';
                                    media.style.maxWidth = '100%';
                                } else {
                                    media.href = msg.media_url;
                                    media.target = '_blank';
                                    media.textContent = 'Tải file đính kèm';
                                    media.className = 'd-block mt-2';
                                }

                                contentDiv.appendChild(media);
                            }

                            bubble.appendChild(contentDiv);

                            const timeDiv = document.createElement('div');
                            timeDiv.className = `message-time text-end small ${isMe ? 'text-white-50' : 'text-muted'} mt-1`;
                            timeDiv.textContent = formatTime(msg.created_at) + ' ';

                            if (isMe) {
                                const deleteIcon = document.createElement('i');
                                deleteIcon.className = 'bi bi-trash ms-2 delete-btn';
                                deleteIcon.style.cursor = 'pointer';
                                deleteIcon.title = 'Xóa tin nhắn';

                                deleteIcon.addEventListener('click', () => {
                                    openDeletePopup(msg.id);
                                });

                                timeDiv.appendChild(deleteIcon);
                            }

                            bubble.appendChild(timeDiv);

                            messageWrapper.appendChild(bubble);

                            if (isMe && index === data.messages.length - 1 && msg.seen) {
                                const statusDiv = document.createElement('div');
                                statusDiv.className = 'read-status text-muted small text-end mt-1';
                                statusDiv.textContent = 'Đã xem';
                                bubble.appendChild(statusDiv);
                            }

                            chatBox.appendChild(messageWrapper);
                        });
                    }


                    if (shouldScrollAfterLoad || chatBox.children.length > 0) {
                        scrollToBottom(chatBox);
                    }
                })
                .catch(error => {
                    console.error('Error loading messages:', error);
                     messagePlaceholder.textContent = 'Lỗi tải tin nhắn.';
                     messagePlaceholder.style.display = 'block';
                     messagePlaceholder.classList.add('text-danger');
                     chatBox.innerHTML = '';
                });
        }
        function sendMessage(e) {
    if (e) e.preventDefault();

    const content = messageInput.value.trim();
    const fileInput = document.getElementById('file-input');
    const file = fileInput.files[0];

    if (!content && !file) return;

    const formData = new FormData();
    formData.append('receiver_id', currentReceiverId);
    if (content) formData.append('content', content);
    if (file) formData.append('media', file);

    // Gửi formData tới send_message.php
    fetch('../core/send_message.php', {
        method: 'POST',
        body: formData
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            loadMessages();
            loadConversations();
            messageInput.value = '';
            fileInput.value = '';
        } else {
            alert("Lỗi gửi tin nhắn: " + (data.error || 'Không xác định'));
        }
    })
    .catch(error => {
        console.error("Send error:", error);
    });
}


        function openDeletePopup(messageId) {
             // Chỉ mở nếu modal đã được khởi tạo
             if(deleteConfirmModal) {
                deleteTargetId = messageId;
                deleteConfirmModal.show();
             } else {
                 console.error("Delete confirmation modal not initialized");
             }
        }

        function deleteMessage(mode) {
             if (!deleteTargetId || !deleteConfirmModal) return; // Thêm kiểm tra modal
             const messageIdToDelete = deleteTargetId;
             deleteTargetId = null;
             deleteConfirmModal.hide();

            fetch('../core/delete_message.php', {
                method: 'POST',
                headers: {'Content-Type': 'application/json', 'Accept': 'application/json'},
                body: JSON.stringify({ message_id: messageIdToDelete, mode: mode })
            })
            .then(res => res.ok ? res.json() : res.text().then(text => Promise.reject(text || 'Failed to delete')))
            .then(data => {
                if (data.success) {
                    location.reload(); // Tải lại trang để cập nhật danh sách tin nhắn
                } else {
                     throw new Error(data.error || 'Xóa tin nhắn thất bại');
                }
            })
             .catch(error => {
                 console.error('Error deleting message:', error);
             });
        }

         function startMessagePolling() {
            stopMessagePolling();
            if (currentReceiverId) {
                messagePollingInterval = setInterval(loadMessages, POLLING_INTERVAL_MS);
            }
        }

        function stopMessagePolling() {
            if (messagePollingInterval) {
                clearInterval(messagePollingInterval);
                messagePollingInterval = null;
            }
        }
         function startConversationPolling() {
             stopConversationPolling();
             conversationPollingInterval = setInterval(loadConversations, POLLING_INTERVAL_MS * 2);
         }

         function stopConversationPolling() {
             if (conversationPollingInterval) {
                 clearInterval(conversationPollingInterval);
                 conversationPollingInterval = null;
             }
         }

        // --- Event Listeners (giữ nguyên) ---
        if(sendButton) sendButton.addEventListener('click', sendMessage);

        if(messageInput) {
            messageInput.addEventListener('keypress', function (e) {
                if (e.key === 'Enter' && !e.shiftKey) {
                    e.preventDefault();
                    sendMessage();
                }
            });
            messageInput.addEventListener('input', () => {
                messageInput.style.height = 'auto';
                const scrollHeight = messageInput.scrollHeight;
                const maxHeight = 120;
                messageInput.style.height = Math.min(scrollHeight, maxHeight) + 'px';
                // Chỉ bật nút gửi khi input không rỗng và đã chọn người nhận
                sendButton.disabled = messageInput.value.trim().length === 0 || !currentReceiverId;
            });
        }


         if(backToListBtn) {
             backToListBtn.addEventListener('click', (e) => {
                 e.preventDefault();
                 stopMessagePolling();
                 currentReceiverId = null;
                 currentReceiverInfo = null;
                 updateChatHeader(null);
                 chatBox.innerHTML = '';
                 messagePlaceholder.textContent = 'Chọn một cuộc trò chuyện để bắt đầu nhắn tin.';
                 messagePlaceholder.classList.remove('text-danger');
                 messagePlaceholder.style.display = 'block';
                 showConversationList();
             });
         }

        // --- Initial Load (giữ nguyên) ---
        loadConversations();
        startConversationPolling();

        // --- Resize Listener (giữ nguyên) ---
        window.addEventListener('resize', () => {
            const isLargeScreen = window.innerWidth >= 992;
            if (isLargeScreen) {
                inboxSidebar.classList.remove('d-none');
                inboxSidebar.classList.add('d-flex');
                 inboxSidebar.style.position = '';
                 inboxSidebar.style.width = '';
                 inboxSidebar.style.zIndex = '';
                 inboxSidebar.style.backgroundColor = '';

                if (!currentReceiverId) {
                    chatMainArea.style.display = 'none';
                    selectChatPlaceholder.classList.remove('d-none');
                    selectChatPlaceholder.classList.add('d-lg-flex');
                } else {
                     chatMainArea.style.display = 'flex';
                     selectChatPlaceholder.classList.add('d-none');
                     selectChatPlaceholder.classList.remove('d-lg-flex');
                }
            } else {
                 selectChatPlaceholder.classList.add('d-none');
                 selectChatPlaceholder.classList.remove('d-lg-flex');
                if (!currentReceiverId) {
                    chatMainArea.style.display = 'none';
                    inboxSidebar.classList.remove('d-none');
                    inboxSidebar.classList.add('d-flex');
                    inboxSidebar.style.position = 'absolute';
                    inboxSidebar.style.width = '100%';
                    inboxSidebar.style.zIndex = '10';
                    inboxSidebar.style.backgroundColor = 'var(--bs-body-bg)';
                } else {
                    inboxSidebar.classList.remove('d-flex');
                    inboxSidebar.classList.add('d-none');
                    inboxSidebar.style.position = '';
                    inboxSidebar.style.width = '';
                    inboxSidebar.style.zIndex = '';
                    inboxSidebar.style.backgroundColor = '';
                    chatMainArea.style.display = 'flex';
                }
            }
        });
        window.dispatchEvent(new Event('resize'));

      });
    </script>
<script type="module" src="scripts/utility-functions.js"></script>

</body>
</html>