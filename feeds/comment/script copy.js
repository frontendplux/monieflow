
  window.like_comment = async function like_comment(feedId, status,parent_id,type) {
    try {
      const res = await fetch('/feeds/comment/req.php', {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({ action: "like_comment", feed_id: feedId, status: status, parent_id: parent_id, type:type })
      });

      const req = await res.text(); // or res.json() if you return JSON

      // Update the UI if you have an element like <span id="like-123"></span>
      const target = document.querySelector(`#like-comes-${parent_id}`);
      if (target) {
        target.innerHTML = req;
      }

      console.log(req);
    } catch (err) {
      console.error("Error liking post:", err);
    }
  }

  

window.replyTo = (feedId, status, comment_id, parent_id, avatar, username) => {
    const sendBtn = document.getElementById('sendCommentBtn');
    const input   = document.getElementById('commentInput');

    // attach metadata
    sendBtn.dataset.feed       = feedId;
    sendBtn.dataset.status     = status;
    sendBtn.dataset.parent_id  = parent_id;
    sendBtn.dataset.avatar     = avatar;
    sendBtn.dataset.comment_id = comment_id;
    sendBtn.dataset.username   = username;

    // update placeholder
    input.placeholder = `Reply to @${username}`;

    // scroll into view and focus
    input.scrollIntoView({ behavior: 'smooth', block: 'center' });
    input.focus();
};




   window.like_post = async function like_post(feedId, status) {
    try {
      const res = await fetch('/feeds/req.php', {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({ action: "like_post", feed_id: feedId, status: status })
      });

      const req = await res.text(); // or res.json() if you return JSON

      // Update the UI if you have an element like <span id="like-123"></span>
      const target = document.querySelector(`#like-${feedId}`);
      if (target) {
        target.innerHTML = req;
      }

      console.log(req);
    } catch (err) {
      console.error("Error liking post:", err);
    }
  }



function loadMoreReplies(feedId, commentId) {
  // target container for replies under this comment
  const commentContainer = document.querySelector(`#comment-${commentId} .reply-thread-container`);

  fetch('/feeds/comment/req.php', {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify({
      action: 'getReplies',
      feed_id: feedId,
      parent_id: commentId
    })
  })
  .then(res => res.json())
  
  .then(data => {
  console.log(data);
    if (Array.isArray(data.replies)) {
      data.replies.forEach(r => {
        const replyEl = document.createElement('div');
        replyEl.className = 'reply-thread mt-2';
        replyEl.innerHTML = `
          <div class="d-flex gap-2">
            <img src="/uploads/${r.profile_pic}" class="rounded-circle" width="24" height="24">
            <div class="flex-grow-1">
              <div class="comment-bubble py-1 px-3">
                <div class="fw-bold small">
                  <a href="#">${r.username}</a>
                </div>
                <div class="small">${r.text}</div>
              </div>
              <div class="small ms-2 mt-1">
                <a href="javascript:;" 
                   onclick="like_comment(${r.feed_id}, '${r.status}', '${r.reply_id}', '${r.type}')"
                   class="text-secondary fw-bold text-decoration-none me-3">
                   <span id="like-comes-${r.reply_id}">${r.like_count}</span> .Like
                </a>
                <a href="javascript:;" 
                   onclick="replyTo(${r.feed_id}, '${r.status}', '${r.reply_id}', '${commentId}', '${r.first_name}-${r.last_name}', '${r.username}')"
                   class="text-secondary fw-bold text-decoration-none me-3">
                   <span>${r.reply_count}</span> .Reply
                </a>
                <span class="text-muted">${r.created_at}</span>
              </div>
            </div>
          </div>
        `;
        commentContainer.appendChild(replyEl);
      });
    }
  })
  .catch(err => console.error('Error loading replies:', err));
}