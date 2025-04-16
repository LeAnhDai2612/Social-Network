window.addEventListener("load", function () {
    document.querySelectorAll(".post-chat-wrapper").forEach(wrapper => {
        wrapper.addEventListener("click", function () {
            const postId = this.dataset.postId;
            const commentSection = document.querySelector(`#comment-section-${postId}`);
            if (!commentSection) return;

            const isVisible = commentSection.style.display === "block";
            commentSection.style.display = isVisible ? "none" : "block";

            if (!isVisible) {
                loadComments(postId, commentSection);
            }
        });
    });

    function loadComments(postId, container) {
        fetch(`../core/get_comments.php?post_id=${postId}`)
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    container.innerHTML = renderCommentBox(postId) + renderComments(data.comments);
                    attachCommentHandlers(postId, container);
                    const toggleBtn = container.querySelector(".toggle-comments-btn");
                    if (toggleBtn) {
                        toggleBtn.addEventListener("click", function () {
                            const more = container.querySelector(".more-comments");
                            if (more) {
                                more.classList.remove("d-none");
                                toggleBtn.remove();
                            }
                        });
                    }

                    const replyToggles = container.querySelectorAll(".toggle-replies-btn");
                    replyToggles.forEach(btn => {
                        btn.addEventListener("click", function () {
                            const moreReplies = btn.nextElementSibling;
                            if (moreReplies && moreReplies.classList.contains("more-replies")) {
                                moreReplies.classList.remove("d-none");
                                btn.remove();
                            }
                        });
                    });
    
                } else {
                    container.innerHTML = `<p class="text-danger">${data.message}</p>`;
                }
            });
    }
    

    function renderCommentBox(postId) {
        return `
            <form class="add-comment-form d-flex gap-2 mt-2" data-post-id="${postId}">
                <input type="text" name="content" class="form-control" placeholder="Viết bình luận..." required />
                <button type="submit" class="btn btn-primary">Gửi</button>
            </form>
            <div class="comment-list mt-3"></div>
        `;
    }

    function renderComments(comments) {
        const MAX_COMMENTS_VISIBLE = 2;
        const visibleComments = comments.slice(0, MAX_COMMENTS_VISIBLE);
        const hiddenComments = comments.slice(MAX_COMMENTS_VISIBLE);

        let html = visibleComments.map(comment => renderSingleComment(comment)).join("");

        if (hiddenComments.length > 0) {
            html += `
                <button class="btn btn-sm btn-link toggle-comments-btn px-0">Xem thêm ${hiddenComments.length} bình luận</button>
                <div class="more-comments d-none">
                    ${hiddenComments.map(comment => renderSingleComment(comment)).join("")}
                </div>
            `;
        }

        return html;
    }

    function renderSingleComment(comment) {
        return `
<div class="comment-item" id="comment-${comment.id}" data-id="${comment.id}">
                <img src="${comment.profile_picture_path}" class="comment-avatar" alt="${comment.username}">
                <div class="flex-grow-1">
                    <div class="d-flex justify-content-between">
                        <strong class="me-2">${comment.username}</strong>
                        <div>
                            ${comment.is_owner ? `
                                <button class="btn btn-sm btn-link text-primary edit-btn">Sửa</button>
                                <button class="btn btn-sm btn-link text-danger delete-btn">Xoá</button>
                            ` : ''}
                            <button class="btn btn-sm btn-link text-secondary reply-btn">Trả lời</button>
                        </div>
                    </div>
                    <p class="mb-1 content">${comment.content}</p>
                    <small class="text-muted">${comment.time_ago}</small>
                    <div class="replies ps-4 mt-2">
                        ${Array.isArray(comment.replies) ? renderReplies(comment.replies) : ""}
                    </div>
                </div>
            </div>
        `;
    }

    function renderReplies(replies) {
        if (!Array.isArray(replies)) return "";
    
        const MAX_REPLIES_VISIBLE = 2;
        const visible = replies.slice(0, MAX_REPLIES_VISIBLE);
        const hidden = replies.slice(MAX_REPLIES_VISIBLE);
    
        let html = visible.map(reply => renderSingleReply(reply)).join("");
    
        if (hidden.length > 0) {
            html += `
                <button class="btn btn-sm btn-link text-secondary px-0 toggle-replies-btn">Xem thêm ${hidden.length} phản hồi</button>
                <div class="more-replies d-none">
                    ${hidden.map(reply => renderSingleReply(reply)).join("")}
                </div>
            `;
        }
    
        return html;
    }
    
    

    function renderSingleReply(reply) {
        return `
            <div class="reply-item mt-3 d-flex gap-2" data-id="${reply.id}">
                <img src="${reply.profile_picture_path}" class="comment-avatar" alt="${reply.username}">
                <div class="flex-grow-1">
                    <div class="d-flex justify-content-between">
                        <strong class="me-2">${reply.username}</strong>
                        <div>
                            ${reply.is_owner ? `
                                <button class="btn btn-sm btn-link text-primary edit-btn">Sửa</button>
                                <button class="btn btn-sm btn-link text-danger delete-btn">Xoá</button>
                            ` : `
                                <button class="btn btn-sm btn-link text-secondary reply-btn">Trả lời</button>
                            `}
                        </div>
                    </div>
                    <p class="mb-1 content">${reply.content}</p>
                    <small class="text-muted">${reply.time_ago}</small>
                </div>
            </div>
        `;
    }
    


    function attachCommentHandlers(postId, container) {
        const form = container.querySelector(".add-comment-form");

        form.addEventListener("submit", function (e) {
            e.preventDefault();
            const content = form.querySelector("input[name='content']").value.trim();
            if (!content) return;

            const formData = new FormData();
            formData.append("post_id", postId);
            formData.append("content", content);

            fetch("../core/add_comment.php", {
                method: "POST",
                body: formData
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    loadComments(postId, container);
                } else {
                    alert(data.message);
                }
            });
        });

        container.addEventListener("click", function (e) {
            const item = e.target.closest(".comment-item, .reply-item");
            if (!item) return;
            const commentId = item.dataset.id;

            if (e.target.classList.contains("delete-btn")) {
                if (confirm("Bạn có chắc muốn xoá bình luận này?")) {
                    const formData = new FormData();
                    formData.append("comment_id", commentId);

                    fetch("../core/delete_comment.php", {
                        method: "POST",
                        body: formData
                    })
                    .then(res => res.json())
                    .then(data => {
                        if (data.success) {
                            loadComments(postId, container);
                        } else {
                            alert(data.message);
                        }
                    });
                }
            }

            if (e.target.classList.contains("edit-btn")) {
                const span = item.querySelector(".content");
                const oldContent = span.textContent;
                const newContent = prompt("Chỉnh sửa bình luận:", oldContent);

                if (newContent && newContent.trim() !== "" && newContent !== oldContent) {
                    const formData = new FormData();
                    formData.append("comment_id", commentId);
                    formData.append("content", newContent);

                    fetch("../core/edit_comment.php", {
                        method: "POST",
                        body: formData
                    })
                    .then(res => res.json())
                    .then(data => {
                        if (data.success) {
                            loadComments(postId, container);
                        } else {
                            alert(data.message);
                        }
                    });
                }
            }

            if (e.target.classList.contains("reply-btn")) {
                if (item.querySelector(".reply-form")) return;

                const replyForm = document.createElement("form");
                replyForm.className = "reply-form d-flex gap-2 mt-2";
                replyForm.innerHTML = `
                    <input type="text" class="form-control form-control-sm" name="reply_content" placeholder="Viết phản hồi..." required />
                    <button type="submit" class="btn btn-sm btn-outline-primary">Gửi</button>
                `;
                item.appendChild(replyForm);

                replyForm.addEventListener("submit", function (ev) {
                    ev.preventDefault();
                    const content = replyForm.querySelector("input").value.trim();
                    if (!content) return;

                    const formData = new FormData();
                    formData.append("post_id", postId);
                    formData.append("content", content);
                    formData.append("parent_comment_id", commentId);

                    fetch("../core/add_comment.php", {
                        method: "POST",
                        body: formData
                    })
                    .then(res => res.json())
                    .then(data => {
                        if (data.success) {
                            loadComments(postId, container);
                        } else {
                            alert(data.message);
                        }
                    });
                });
            }
        });
    }
});
