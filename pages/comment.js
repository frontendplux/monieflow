import { Feed } from "../helperJs/helperfeeds.js";
import { Footer } from "./feeds.js";

export default async function Comments(postId = null) {
    // 1. DATA STATE
    const state = {
        originalPost: {
            author: "Luna Star",
            handle: "@luna_crypto",
            content: "Just added a rare Pink Amethyst to my flow! Who else is collecting crystals today? 💎✨",
            avatar: "https://api.dicebear.com/7.x/avataaars/svg?seed=Luna",
            time: "2h ago"
        },
        comments: [
            { id: 1, user: "King", avatar: "https://api.dicebear.com/7.x/avataaars/svg?seed=King", text: "That color is insane! How much was the gas fee?", likes: 12, time: "45m", liked: false },
            { id: 2, user: "Sarah J.", avatar: "https://api.dicebear.com/7.x/avataaars/svg?seed=Sarah", text: "I'm looking for a Citrine one to match my setup.", likes: 5, time: "10m", liked: true },
            { id: 3, user: "Alex Flow", avatar: "https://api.dicebear.com/7.x/avataaars/svg?seed=Alex", text: "Welcome to the club! 🚀", likes: 2, time: "2m", liked: false }
        ]
    };

    // 2. LOGIC
    window.toggleCommentLike = (id) => {
        const comment = state.comments.find(c => c.id === id);
        if (comment) {
            comment.liked = !comment.liked;
            comment.likes += comment.liked ? 1 : -1;
            renderCommentsList();
        }
    };

    window.postComment = () => {
        const input = document.getElementById('comment-input');
        if (!input.value.trim()) return;
        
        const newComment = {
            id: Date.now(),
            user: "Felix (You)",
            avatar: "https://api.dicebear.com/7.x/avataaars/svg?seed=Felix",
            text: input.value,
            likes: 0,
            time: "Just now",
            liked: false
        };
        
        state.comments.unshift(newComment);
        input.value = '';
        renderCommentsList();
    };

    // 3. UI FRAGMENTS
    const CommentItem = (c) => `
        <div class="d-flex gap-3 mb-4 animate-slide-in">
            <img src="${c.avatar}" class="rounded-circle shadow-sm" width="40" height="40">
            <div class="flex-grow-1">
                <div class="bg-white p-3 rounded-4 shadow-sm border border-light">
                    <div class="d-flex justify-content-between align-items-center mb-1">
                        <span class="fw-black small">${c.user}</span>
                        <small class="text-muted" style="font-size: 0.7rem;">${c.time}</small>
                    </div>
                    <p class="mb-0 small text-secondary" style="line-height: 1.4;">${c.text}</p>
                </div>
                <div class="d-flex gap-4 mt-2 px-2">
                    <button onclick="toggleCommentLike(${c.id})" class="btn btn-link p-0 text-decoration-none small fw-bold ${c.liked ? 'text-danger' : 'text-muted'}">
                        <i class="bi ${c.liked ? 'bi-heart-fill' : 'bi-heart'} me-1"></i> ${c.likes}
                    </button>
                    <button class="btn btn-link p-0 text-muted text-decoration-none small fw-bold">Reply</button>
                    <button class="btn btn-link p-0 text-muted text-decoration-none small fw-bold">Share</button>
                </div>
            </div>
        </div>
    `;

    // 4. LIVE RENDER
    const renderCommentsList = () => {
        const container = document.getElementById('comments-container');
        if (container) container.innerHTML = state.comments.map(CommentItem).join('');
    };

    // 5. MAIN TEMPLATE
    return /* html */ `
    <div class="container-fluid py-4 bg-light" style="min-height: 100vh;">
        <div class="row justify-content-center">
            <div class="col-lg-6 col-md-8">
                
                <button onclick="window.history.back()" class="btn btn-white bg-white shadow-sm rounded-pill mb-4 px-3 fw-bold border-0">
                    <i class="bi bi-arrow-left me-2 text-primary"></i> Post Thread
                </button>

                <div class="card border-0 shadow-sm mb-4 overflow-hidden" style="border-radius: 25px;">
                    <div class="card-body p-4">
                        <div class="d-flex align-items-center gap-3 mb-3">
                            <img src="${state.originalPost.avatar}" class="rounded-circle border" width="50">
                            <div>
                                <h6 class="fw-black mb-0">${state.originalPost.author}</h6>
                                <small class="text-primary fw-bold">${state.originalPost.handle} • ${state.originalPost.time}</small>
                            </div>
                        </div>
                        <h5 class="fw-bold text-dark mb-0" style="line-height: 1.5;">${state.originalPost.content}</h5>
                    </div>
                </div>

                <hr class="opacity-10 my-4">

                <div class="card border-0 shadow-lg p-3 mb-5" style="border-radius: 25px;">
                    <div class="d-flex gap-3 align-items-center">
                        <img src="https://api.dicebear.com/7.x/avataaars/svg?seed=Felix" class="rounded-circle" width="40">
                        <div class="flex-grow-1 position-relative">
                            <textarea id="comment-input" class="form-control border-0 bg-light rounded-4 py-3 shadow-none fw-bold" 
                                      placeholder="Add a comment to this flow..." rows="1" style="resize: none;"></textarea>
                            <button onclick="postComment()" class="btn btn-warning rounded-circle position-absolute end-0 top-50 translate-middle-y me-2 d-flex align-items-center justify-content-center shadow-sm" style="width: 35px; height: 35px;">
                                <i class="bi bi-send-fill fs-6"></i>
                            </button>
                        </div>
                    </div>
                </div>

                <div id="comments-container">
                    ${state.comments.map(CommentItem).join('')}
                </div>

            </div>
        </div>
    </div>

    <style>
        .fw-black { font-weight: 900; }
        .animate-slide-in {
            animation: slideUp 0.4s ease forwards;
        }
        @keyframes slideUp {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        #comment-input:focus {
            background-color: #fff !important;
            border: 1px solid #FF9900 !important;
        }
    </style>
    ${Footer({page: 'home'})}
    `;
}