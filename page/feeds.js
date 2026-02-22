import { loader_board } from "../pages.js";
import friendsMEmoS from "./friends.js";
import timeAgo from "./waget.js";

export default async function feeder(mainDataREQ){
   Window.pop_emojis_likes=function(e,post_id){
    const pop_emojis_likes=document.getElementById(`pop_emojis_likes-${post_id}`);
    pop_emojis_likes ? pop_emojis_likes.remove() : 
    e.insertAdjacentHTML('beforebegin',
    `
    <div id="pop_emojis_likes-${post_id}"  class="position-absolute p-2  start-0" style="bottom:50px; transition:1s">
      <div class="bg-white bg-opacity-75 d-flex p-1 shadow rounded-3 position-relative justify-content-between">
         <div class="w-100">
         <span class="ri-heart-fill text-danger fs-1 p-2"></span>
         <span class="ri-gift-2-fill text-info fs-1 p-2 "></span>
         <span class="ri-heart-2-fill text-secondary fs-1 p-2 "></span>
         <span class="ri-heart-2-fill fs-1 p-2 "></span>
         <span class="ri-heart-2-fill fs-1 p-2 "></span>
         <span class="ri-heart-2-fill fs-1 p-2 "></span>
         </div>
      </div>
    </div>
    `);
   }
    
       
   window.pop_emojis_share=function(e,post_id){
    const pop_emojis_likes=document.getElementById(`pop_emojis_likes-${post_id}`);
    pop_emojis_likes ? pop_emojis_likes.remove() : 
    e.insertAdjacentHTML('beforebegin',
    `
    <div id="pop_emojis_likes-${post_id}"  class="position-absolute p-2  end-0" style="bottom:50px; transition:1s">
      <div class="bg-white bg-opacity-75 d-flex p-1 shadow rounded-3 position-relative justify-content-between">
         <div class="w-100">
         <span class="ri-facebook-fill shadow-lg mx-1 btn btn-primary fs-4 p-2 py-1"></span>
         <span class="ri-whatsapp-fill shadow-lg mx-1 btn btn-success fs-4 p-2 py-1"></span>
         <span class="ri-instagram-fill shadow-lg mx-1 btn btn-danger fs-4 p-2 py-1"></span>
         <span class="ri-twitter-fill shadow-lg mx-1 btn btn-dark fs-4 p-2 py-1"></span>
         </div>
         <!--<div class="align-self-center"><span onclick="document.getElementById('pop_emojis_likes-${post_id}').remove()" class="ri-close-fill px-2 py-1  rounded-circle btn btn-light fw-bold "></span></div>-->
      </div>
    </div>
    `);
   }



   window.likePost=(feedId)=> {
  const formData = new FormData();
  formData.append('action', 'like');
  formData.append('feed_id', feedId);

  fetch(loader_board, {
    method: 'POST',
    body: formData
  })
  .then(res => res.text())
  .then(datas => {
    console.log(datas);
    const data=JSON.parse(datas);
    if (data.success) {
      // Update the like count in the UI
      document.querySelector(`#like-count-${feedId}`).textContent = `${data.likes_count}. Like`;
    } else {
      // console.error(data.message);
       droppySammy('info', 'Auth Error', data.message);
    }
  })
  .catch(err => droppySammy('info', 'Auth Error', "Error liking post: "+ err));
}


window.fetchComments=(feedId,total_comment)=>{
  const input = document.querySelector(`#comment-input-post-loader`);
  document.querySelector('.receive_from_click_comment_count').innerHTML=total_comment
  document.querySelector('.comment_modal').setAttribute('data-feed-id', feedId);
document.querySelector('.comment_modal').setAttribute('data-feed-reply-id', '');
input.value='';
input.placeholder='write a comment...'

  const fd = new FormData();
  fd.append('action', 'get_comments');
  fd.append('feed_id', feedId);

  fetch(loader_board, { method: 'POST', body: fd })
    .then(res => res.text())
    .then(data => {
      console.log(data);
      var data=JSON.parse(data);
      
      if (data.success) {
        // const container = document.querySelector(`#comments-${feedId}`);
        const container = document.querySelector(`#comments-modal-loads`);
       container.innerHTML = data.comments.map(c => `
  <div class="comment mb-3">
    <div class="d-flex">
      <div><img src="/uploads/${c.profile_pic}" class="rounded-circle me-2" width="35px" height="35px"></div>
      <div class="flex-grow-1">
        <div class="bg-light p-2 rounded-3">
          <p class="mb-0 fw-bold small">${c.username}</p>
          <p class="mb-0 small">${c.comment}</p>
        </div>
        <div class="d-flex align-items-center mt-1 ps-1" style="font-size:0.75rem;">
          <button onclick="toggleCommentLike(${c.id})" class="btn btn-link btn-sm text-muted p-0 me-3 fw-bold" id="comment-like-${c.id}">Like (${c.like_count})</button>
          <button onclick="postReply(${feedId}, ${c.id}, '${c.username}')" class="btn btn-link btn-sm text-muted p-0 fw-bold">Reply (${c.reply_count})</button>
        </div>
        <div class="ms-4 mt-2 border-start ps-3">
          ${c.replies.map(r => `
            <div class="d-flex mb-2">
              <img src="/uploads/${r.profile_pic}" class="rounded-circle me-2" width="25" height="25">
              <div class="flex-grow-1">
                <div class="bg-light p-2 rounded-3">
                  <p class="mb-0 fw-bold small">${r.username}</p>
                  <p class="mb-0 small">${r.comment}</p>
                </div>
              </div>
            </div>
          `).join('')}
          ${c.reply_count > c.replies.length ? `<button onclick="loadMoreReplies(${c.id})" class="btn btn-link btn-sm text-muted p-0 fw-bold">See more replies</button>` : ''}
        </div>
      </div>
    </div>
  </div>
`).join('');

      }
    });
}



window.postComment = function () {
  const input = document.querySelector(`#comment-input-post-loader`);
  const feedId = document.querySelector('.comment_modal').getAttribute('data-feed-id');
  const parentId = document.querySelector('.comment_modal').getAttribute('data-feed-reply-id');
  const commentText = input.value.trim();
  if (!commentText) return;
  const formData = new FormData();
  formData.append('action', 'comment');
  formData.append('feed_id', feedId);
  formData.append('parent_id', parentId); // use parent_id for replies
  formData.append('comment', commentText);
  input.value='';
  input.placeholder='write a comment...'
  fetch(loader_board, {
    method: 'POST',
    body: formData
  })
  .then(res => res.text())
  .then(data => {
    console.log(data);
    var data=JSON.parse(data);
    
    if (data.success) {
      document.querySelector(`#comments-modal-loads`).scrollTop=0
      fetchComments(feedId,parseInt(document.querySelector('.receive_from_click_comment_count').innerHTML) + 1)
    } else {
      console.error(data.message);
    }
  })
  .catch(err => console.error("Error posting comment:", err));
}



window.postReply =function(feedId, parentId, username) {
  const input = document.querySelector(`#comment-input-post-loader`);
  document.querySelector('.comment_modal').setAttribute('data-feed-id', feedId);
document.querySelector('.comment_modal').setAttribute('data-feed-reply-id', parentId);
input.value='';
input.placeholder='Replying... to '+username;
}


window.toggleCommentLike=function (commentId) {
  const fd = new FormData();
  fd.append('action', 'like_comment');
  fd.append('comment_id', commentId);

  fetch(loader_board, { method: 'POST', body: fd })
    .then(res => res.json())
    .then(data => {
      if (data.success) {
        const btn = document.querySelector(`#comment-like-${commentId}`);
        btn.textContent = `Like (${data.likes_count})`;
        btn.classList.toggle("text-primary", data.action === "liked");
      } else {
        console.error(data.message);
      }
    })
    .catch(err => console.error("Error liking comment:", err));
}




    return  `
<header class="sticky-top bg-white py-2 d-lg-none">
  <div class="container d-flex justify-content-between align-items-center">
    <div>
       <span><i style="width:40px; height:40px" class="ri-more-fill p-2 border border-5 rounded-circle"></i></span>
       <span><i style="width:40px; height:40px" class="ri-search-fill p-2 border border-5 rounded-circle"></i></span>
    </div>
    <h1 class="fs-4 fw-bold cursor-pointer" data.href="member">monieflow</h1>
    <div>
       <span   class="cursor-pointer" data-href="friends"><i style="width:40px; height:40px" class="ri-group-fill p-2 border border-5 rounded-circle"></i></span>
       <span class="cursor-pointer" data-href="profile"><img style="width:40px; height:40px" class="border border-5 rounded-circle" src="/uploads/pp_698d782591acb9.64066928.jpeg" /></span>
    </div>
  </div>
</header>


<header class="sticky-top bg-white p-2 d-none d-lg-block">
  <div class="container d-flex justify-content-between align-items-center">
    <h1 class="fs-4 fw-bold cursor-pointer">
         <span data-href="create-post" class="fs-6"><i style="width:40px; height:40px" class="ri-add-circle-fill p-2 border border-5 rounded-circle"></i></span>
          <span data-href="member">monieflow</span>
    </h1>
    <div class="d-flex w-50 gap-3 align-items-center">
       <input type="search" placeholder="search MonieFlow" class="form-control rounded-pill"  /> 
       <span><i style="width:40px; height:40px" class="ri-search-fill p-2 border border-5 rounded-circle"></i></span>
    </div>
    <div>
        <span class="cursor-pointer"  data-href="coin"><i style="width:40px; height:40px" class="ri-copper-diamond-fill p-2 border border-5 rounded-circle"></i></span>
        <span  class="cursor-pointer"  data-href="notification"><i style="width:40px; height:40px" class="ri-notification-2-fill p-2 border border-5 rounded-circle"></i></span>
       <span  class="cursor-pointer" data-href="friends"><i style="width:40px; height:40px" class="ri-group-fill p-2 border border-5 rounded-circle"></i></span>
       <span  class="cursor-pointer" data-href="profile"><img style="width:40px; height:40px" class="border border-5 rounded-circle" src="/uploads/pp_698d782591acb9.64066928.jpeg" /></span>
    </div>
  </div>
</header>

<footer class="position-fixed bg-white py-3 bottom-0 start-0 end-0 d-lg-none" style="z-index:40">
 <div class="container justify-content-between d-flex">
    <span data-href="member"><i style="width:40px; height:40px" class="ri-home-fill p-2 border border-5 rounded-circle"></i></span>
    <span><i style="width:40px; height:40px" class="ri-exchange-2-fill p-2 border border-5 rounded-circle"></i></span>
    <span data-href="create-post"><i style="width:40px; height:40px" class="ri-add-fill p-2 border border-5 rounded-circle"></i></span>
    <span><i style="width:40px; height:40px" class="ri-notification-2-fill p-2 border border-5 rounded-circle"></i></span>
    <span><i style="width:40px; height:40px" class="ri-message-2-fill p-2 border border-5 rounded-circle"></i></span>
 </div>
</footer>

<div class="container p-0 d-flex mt-2">
   <div class="col-3 d-none d-lg-block"></div>
   <div class="w-100 mb-3 pb-5">
    ${await friendsMEmoS()}
    ${chunckfeed(mainDataREQ,0, 3)}

   </div>
   <div class="col-3 d-none d-lg-block"></div>
</div>
<!-- Bottom Modal -->
<div class="modal fade" id="likeCommentModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-bottom">
        <div class="modal-content">
            <div class="modal-header sticky-top bg-white border-bottom">
                <div class="d-flex align-items-center">
                    <!--<span class="badge bg-primary-soft text-primary me-2"><i class="bi bi-heart-fill"></i> 1.2k</span>-->
                    <h5 class="modal-title h6 fw-bold mb-0">Comments <b class="receive_from_click_comment_count">loading...</b></h5>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <div class="modal-body p-3" id="comments-modal-loads">
                
                <div class="comment-group mb-4">
                    <div class="d-flex">
                        <img src="https://i.pravatar.cc/40?img=11" class="rounded-circle me-2" width="35" height="35">
                        <div class="flex-grow-1">
                            <div class="bg-light p-2 rounded-3">
                                <p class="mb-0 fw-bold small">James Wilson</p>
                                <p class="mb-0 small">This masonry layout is exactly what I needed for my portfolio! <span class="collapse" id="more1">Does it support lazy loading?</span> 
                                <a href="#more1" data-bs-toggle="collapse" class="text-primary text-decoration-none fw-bold d-block mt-1" style="font-size: 0.75rem;">Read more</a></p>
                            </div>
                            <div class="d-flex align-items-center mt-1 ps-1" style="font-size: 0.75rem;">
                                <button class="btn btn-link btn-sm text-muted p-0 me-3 text-decoration-none fw-bold">Like (12)</button>
                                <button class="btn btn-link btn-sm text-muted p-0 text-decoration-none fw-bold">Reply (2)</button>
                            </div>

                            <div class="ms-4 mt-3 border-start ps-3">
                                <div class="d-flex mb-2">
                                    <img src="https://i.pravatar.cc/40?img=12" class="rounded-circle me-2" width="25" height="25">
                                    <div class="flex-grow-1">
                                        <div class="bg-light p-2 rounded-3">
                                            <p class="mb-0 fw-bold small">Admin</p>
                                            <p class="mb-0 small">Yes, just add loading="lazy" to the images!</p>
                                        </div>
                                        <button class="btn btn-link btn-sm text-muted p-0 text-decoration-none fw-bold" style="font-size: 0.7rem;">Reply</button>
                                    </div>
                                </div>
                                <a href="#" class="small text-muted text-decoration-none fw-bold mt-1 d-block" style="font-size: 0.75rem;">— View more replies (5)</a>
                            </div>
                        </div>
                    </div>
                </div>

            </div>

            <div class="modal-footer bg-white border-top p-2 comment_modal">
                <div class="input-group">
                    <input id="comment-input-post-loader" type="text" class="form-control border-0 bg-light fw-bold" placeholder="Write a comment..." aria-label="Comment">
                    <button data-feed-id="" data-feed-reply-id=""  onclick="postComment()" class="btn btn-primary rounded-circle ms-2" type="button">
                        <i class="bi bi-send-fill"></i> →
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
`;
}


function chunckfeed(mainDataREQ,limit, off){
   return `${mainDataREQ.users.splice(limit,off).map(user => {
  let media = [];
  try {
    const parsed = JSON.parse(user.media_url || "{}");
    media = parsed.images || [];
  } catch (err) {
    console.error("Invalid media_url JSON", err);
  }

  const galleryHTML = media.map((img, idx) => `
    <div class="gallery-item ${idx === 0 || idx === 3 ? 'full-width' : 'col-2'}">
      <img src="${img}" alt="${idx === 0 || idx === 3  ? 'Featured' : 'Portrait'}">
    </div>
  `).join('');

  return `
    <div class="bg-white mb-2 m-2 rounded-3 shadow-lg">
      <div class="d-flex p-2 justify-content-between">
        <div class="d-flex gap-2">
          <div>
            <img style="width:50px;height:50px" 
                 class="rounded-circle border-2 border" 
                 src="/uploads/${user.source_pic}" />
          </div>
          <div>
            <strong class="text-capitalize fs-6">${user.source_name}</strong>
            <div class="text-secondary" style="font-size:small;">
              ${timeAgo(user.created_at)}. ago 
            </div>
          </div>
        </div>
        <span class="ri-close-fill" style="font-size:35px"></span>
      </div>
      <p class="ps-5 fw-medium">${user.content || ''}</p>
      <div class="gallery-container">
        ${galleryHTML}
      </div>
      <div class="px-3 d-flex justify-content-between">
        <div><b id="like-count-${user.id}">${user.likes_count || 0}</b>. Like, <b>${user.shares_count || 0}</b>. Share</div>
        <div><b>${user.comments_count || 0}</b>. Comment</div>
      </div>
      <div class="p-2 d-flex gap-2  align-items-center position-relative">
        <span onclick="likePost(${user.id})"  class="btn btn-light ri-thumb-up-fill rounded-pill"> Like </span>
        <span onclick="fetchComments(${user.id}, ${user.comments_count || 0})" data-bs-toggle="modal" data-bs-target="#likeCommentModal" class="btn btn-light rounded-pill ri-chat-3-fill gap-2 d-flex">  Comment</span>
        <span onclick="pop_emojis_share(this,${user.id})" class="btn btn-light rounded-pill ri-share-fill"> Share</span>
      </div>
    </div>
  `;
}).join('')}`
} 
