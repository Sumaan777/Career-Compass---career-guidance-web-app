@extends('layouts.app')

@section('content')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">

<style>
    body {
        background: #eef1f7 !important;
        font-family: 'Inter', sans-serif;
    }

    /* HEADER */
    .cc-header {
        background: linear-gradient(135deg, #4f46e5, #6366f1, #8b5cf6);
        padding: 45px;
        border-radius: 28px;
        color: white;
        box-shadow: 0 20px 45px rgba(0,0,0,0.18);
        backdrop-filter: blur(14px);
        animation: fadeInDown .7s ease;
    }

    .cc-header h2 {
        font-weight: 800;
        letter-spacing: -1px;
    }

    /* GRID CARD */
    .cc-card {
        background: rgba(255,255,255,0.72);
        backdrop-filter: blur(18px);
        padding: 26px;
        border-radius: 22px;
        border: 1px solid rgba(255,255,255,0.35);
        transition: .35s ease;
        cursor: pointer;
        height: 200px;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        box-shadow: 0 8px 22px rgba(0,0,0,0.08);
    }

    .cc-card:hover {
        transform: translateY(-8px);
        box-shadow: 0 12px 28px rgba(0,0,0,0.15);
    }

    .cc-icon {
        font-size: 42px;
        color: #4f46e5;
    }

    .cc-card-title {
        font-size: 18px;
        font-weight: 700;
    }

    .cc-card-text {
        font-size: 14px;
        color: #6c6c6c;
    }

    .cc-entry-btn {
        text-decoration: none;
        font-weight: 600;
        font-size: 14px;
        color: #4f46e5;
        display: inline-block;
        margin-top: 10px;
    }

    /* ANIMATIONS */
    @keyframes fadeInUp {
        from { opacity: 0; transform: translateY(20px); }
        to   { opacity: 1; transform: translateY(0); }
    }

    @keyframes fadeInDown {
        from { opacity: 0; transform: translateY(-20px); }
        to   { opacity: 1; transform: translateY(0); }
    }

    .animate-card {
        animation: fadeInUp .7s ease;
    }
</style>

<div class="container mt-4">

    {{-- HEADER --}}
    <div class="cc-header mb-5">
        <h2>👋 Hello, {{ $user->name }}</h2>
        <p class="opacity-75 mb-0">
            Your personalized AI-powered dashboard — designed for your career journey.
        </p>
    </div>

    @php $type = $user->user_type; @endphp

    <div class="row g-4">

        {{-- AI Career Quiz - High School OR Student --}}
        @if($type === 'high_school' || $type === 'student')
        <div class="col-md-4 animate-card">
            <a href="{{ route('ai.quiz.start') }}" style="text-decoration:none;">
                <div class="cc-card">
                    <div>
                        <i class="bi bi-question-circle cc-icon"></i>
                        <h5 class="cc-card-title mt-3">AI Career Quiz</h5>
                        <p class="cc-card-text">Let AI discover your ideal career direction.</p>
                    </div>
                    <span class="cc-entry-btn">Start →</span>
                </div>
            </a>
        </div>
        @endif

        {{-- Skill Gap Analyzer - University --}}
        @if($type === 'university')
        <div class="col-md-4 animate-card">
            <a href="{{ route('skill-gap.index') }}" style="text-decoration:none;">
                <div class="cc-card">
                    <div>
                        <i class="bi bi-bar-chart-line cc-icon"></i>
                        <h5 class="cc-card-title mt-3">Skill Gap Analyzer</h5>
                        <p class="cc-card-text">Instantly find missing skills for your target job.</p>
                    </div>
                    <span class="cc-entry-btn">Analyze →</span>
                </div>
            </a>
        </div>
        @endif

        {{-- Undecided – Career Discovery --}}
        @if($type === 'undecided')
        <div class="col-md-4 animate-card">
            <a href="{{ route('ai.quiz.start') }}" style="text-decoration:none;">
                <div class="cc-card">
                    <div>
                        <i class="bi bi-compass cc-icon"></i>
                        <h5 class="cc-card-title mt-3">Discover Your Path</h5>
                        <p class="cc-card-text">AI-powered exploration to help choose your future.</p>
                    </div>
                    <span class="cc-entry-btn">Begin →</span>
                </div>
            </a>
        </div>
        @endif

        {{-- Interview Simulator – Fresh Graduate --}}
        @if($type === 'fresh_grad')
        <div class="col-md-4 animate-card">
            <a href="{{ route('interview.page') }}" style="text-decoration:none;">
                <div class="cc-card">
                    <div>
                        <i class="bi bi-chat-left-text cc-icon"></i>
                        <h5 class="cc-card-title mt-3">Interview Simulator</h5>
                        <p class="cc-card-text">Practice tough interview questions using AI.</p>
                    </div>
                    <span class="cc-entry-btn">Start →</span>
                </div>
            </a>
        </div>
        @endif

        {{-- Career Switch Roadmap --}}
        @if($type === 'switcher')
        <div class="col-md-4 animate-card">
            <a href="{{ route('career.roadmap') }}" style="text-decoration:none;">
                <div class="cc-card">
                    <div>
                        <i class="bi bi-diagram-3 cc-icon"></i>
                        <h5 class="cc-card-title mt-3">Career Switch Roadmap</h5>
                        <p class="cc-card-text">AI-generated roadmap for your next transition.</p>
                    </div>
                    <span class="cc-entry-btn">Generate →</span>
                </div>
            </a>
        </div>
        @endif

    </div>
</div>


{{-- ================================================================= --}}
{{-- FLOATING AI CHAT BUTTON + POPUP ADDED BELOW (FULL WORKING)        --}}
{{-- ================================================================= --}}

<style>
#aiFloatBtn {
    position: fixed;
    bottom: 26px;
    right: 26px;
    width: 60px;
    height: 60px;
    border-radius: 50%;
    background: #4f46e5;
    color: white;
    display: flex;
    justify-content: center;
    align-items: center;
    font-size: 28px;
    cursor: pointer;
    box-shadow: 0 6px 20px rgba(0,0,0,0.25);
    z-index: 99999;
}
#aiFloatBtn:hover {
    background: #3c35c5;
}

#aiChatPopup {
    position: fixed;
    bottom: 95px;
    right: 28px;
    width: 360px;
    height: 530px;
    background: white;
    border-radius: 16px;
    box-shadow: 0 10px 30px rgba(0,0,0,0.25);
    display: none;
    flex-direction: column;
    z-index: 999999;
    overflow: hidden;
}

#aiChatHeader {
    background: #4f46e5;
    color: white;
    padding: 14px 18px;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

#aiChatBody {
    flex: 1;
    padding: 14px;
    overflow-y: auto;
    background: #f6f7fb;
}

.ai-msg {
    max-width: 75%;
    padding: 10px 14px;
    margin-bottom: 10px;
    border-radius: 14px;
    font-size: 0.9rem;
    white-space: pre-wrap;
}
.ai-user { background: #4f46e5; color: white; margin-left: auto; }
.ai-bot { background: white; border: 1px solid #ddd; margin-right: auto; }

#aiInputArea {
    padding: 10px;
    border-top: 1px solid #ddd;
    background: white;
}
</style>

<div id="aiFloatBtn">
    <i class="bi bi-chat-dots-fill"></i>
</div>

<div id="aiChatPopup">

    <div id="aiChatHeader">
        <h5><i class="bi bi-stars"></i> Career AI</h5>
        <i id="aiClose" class="bi bi-x-lg" style="cursor:pointer;"></i>
    </div>

    <div id="aiChatBody"></div>

    <div id="aiInputArea">
        <form id="aiChatForm" class="d-flex gap-2">
            <input id="aiInput" class="form-control" placeholder="Type a message...">
            <button class="btn btn-primary"><i class="bi bi-send-fill"></i></button>
        </form>
    </div>
</div>

<script>
document.getElementById("aiFloatBtn").onclick = () => {
    document.getElementById("aiChatPopup").style.display = "flex";
};
document.getElementById("aiClose").onclick = () => {
    document.getElementById("aiChatPopup").style.display = "none";
};

function scrollAI() {
    const body = document.getElementById("aiChatBody");
    body.scrollTop = body.scrollHeight;
}

function addAIMsg(role, text) {
    const body = document.getElementById("aiChatBody");
    let div = document.createElement("div");
    div.classList.add("ai-msg", role === "user" ? "ai-user" : "ai-bot");
    div.innerText = text;
    body.appendChild(div);
    scrollAI();
}

document.getElementById("aiChatForm").addEventListener("submit", async e => {
    e.preventDefault();

    const input = document.getElementById("aiInput");
    const msg = input.value.trim();
    if (!msg) return;

    addAIMsg("user", msg);
    input.value = "";

    let typing = document.createElement("div");
    typing.classList.add("ai-msg", "ai-bot");
    typing.innerText = "Typing...";
    document.getElementById("aiChatBody").appendChild(typing);
    scrollAI();

    const response = await fetch("/career/chat/send", {
        method: "POST",
        headers: {
            "Content-Type":"application/json",
            "X-CSRF-TOKEN":"{{ csrf_token() }}"
        },
        body: JSON.stringify({ message: msg })
    });

    const data = await response.json();
    typing.remove();

    addAIMsg("bot", data.reply);
});
</script>

@endsection
