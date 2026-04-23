    @extends('layouts.app')

    @section('content')

    <style>
        /* Match dashboard cards */
        .cc-chat-card {
            border-radius: 12px;
            border: 1px solid rgba(0,0,0,0.08);
            background: #ffffff;
            box-shadow: 0 2px 6px rgba(0,0,0,0.05);
        }

        /* Header */
        .cc-chat-header {
            padding: 14px 20px;
            border-bottom: 1px solid rgba(0,0,0,0.08);
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 1.05rem;
        }

        /* Chat box */
        #ccChatBox {
            height: calc(100vh - 260px);
            overflow-y: auto;
            padding: 20px;
            background: #f7f8fc;
        }

        /* Message bubble */
        .cc-msg {
            max-width: 70%;
            padding: 10px 14px;
            margin-bottom: 14px;
            border-radius: 12px;
            line-height: 1.45;
            font-size: 0.92rem;
            white-space: pre-wrap;
        }

        .cc-user {
            background: #4f46e5;
            color: white;
            margin-left: auto;
            border-bottom-right-radius: 4px;
        }

        .cc-ai {
            background: white;
            color: #222;
            border: 1px solid rgba(0,0,0,0.1);
            margin-right: auto;
            border-bottom-left-radius: 4px;
        }

        /* Avatars */
        .cc-avatar {
            width: 34px;
            height: 34px;
            border-radius: 50%;
            background: #4f46e5;
            color: #fff;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.85rem;
            margin-left: 8px;
        }

        .cc-avatar-ai {
            background: #00bcd4;
            font-size: 1rem;
            margin-right: 8px;
        }

        /* Input */
        .cc-chat-input {
            border-top: 1px solid rgba(0,0,0,0.08);
            padding: 12px 16px;
            background: #ffffff;
        }

        /* Typing dots */
        .cc-dots span {
            width: 6px;
            height: 6px;
            background: #888;
            border-radius: 50%;
            display: inline-block;
            animation: blink 1s infinite alternate;
        }
        .cc-dots span:nth-child(2){ animation-delay: .2s; }
        .cc-dots span:nth-child(3){ animation-delay: .4s; }

        @keyframes blink {
            from { opacity: .4; transform: translateY(0); }
            to   { opacity: 1; transform: translateY(-2px); }
        }
    </style>

    <div class="container-fluid mt-3">
        <div class="cc-chat-card">

            <!-- HEADER -->
            <div class="cc-chat-header">
                <i class="bi bi-stars text-primary"></i>
                AI Career Mentor
            </div>

            <!-- CHAT BOX -->
            <div id="ccChatBox">
                @foreach($messages as $m)
                    @if($m->role == 'user')
                    <div class="d-flex justify-content-end align-items-end mb-2">
                        <div class="cc-msg cc-user">{{ $m->message }}</div>
                        <div class="cc-avatar">{{ strtoupper(substr($user->name,0,1)) }}</div>
                    </div>
                    @else
                    <div class="d-flex justify-content-start align-items-end mb-2">
                        <div class="cc-avatar cc-avatar-ai"><i class="bi bi-stars"></i></div>
                        <div class="cc-msg cc-ai">{{ $m->message }}</div>
                    </div>
                    @endif
                @endforeach
            </div>

            <!-- INPUT AREA -->
            <div class="cc-chat-input">
                <form id="ccChatForm" class="d-flex gap-2">
                    <input id="ccChatInput" type="text" class="form-control"
                        placeholder="Write a message..." autocomplete="off">
                    <button class="btn btn-primary">
                        <i class="bi bi-send-fill"></i>
                    </button>
                </form>
            </div>

        </div>
    </div>

    <script>
    const chatBox = document.getElementById('ccChatBox');
    const chatForm = document.getElementById('ccChatForm');
    const chatInput = document.getElementById('ccChatInput');

    function scrollDown() {
        chatBox.scrollTop = chatBox.scrollHeight;
    }

    function addMessage(role, text) {
        let id = "msg-" + Date.now();

        let html = '';

        if(role === 'user'){
            html = `
            <div class="d-flex justify-content-end align-items-end mb-2" id="${id}">
                <div class="cc-msg cc-user"></div>
                <div class="cc-avatar">{{ strtoupper(substr($user->name,0,1)) }}</div>
            </div>`;
        } else {
            html = `
            <div class="d-flex justify-content-start align-items-end mb-2" id="${id}">
                <div class="cc-avatar cc-avatar-ai"><i class="bi bi-stars"></i></div>
                <div class="cc-msg cc-ai"></div>
            </div>`;
        }

        chatBox.insertAdjacentHTML('beforeend', html);
        scrollDown();

        return document.querySelector(`#${id} .cc-msg`);
    }

    function streamText(el, txt) {
        let i = 0;
        const interval = setInterval(() => {
            el.textContent += txt.charAt(i);
            i++;
            scrollDown();
            if (i >= txt.length) clearInterval(interval);
        }, 15);
    }

    chatForm.addEventListener('submit', async e => {
        e.preventDefault();

        const msg = chatInput.value.trim();
        if (!msg) return;

        const userBubble = addMessage('user', "");
        userBubble.textContent = msg;

        chatInput.value = "";

        // Typing indicator
        const typingId = "typing-" + Date.now();
        chatBox.insertAdjacentHTML('beforeend', `
            <div class="d-flex align-items-center mb-2" id="${typingId}">
                <div class="cc-avatar cc-avatar-ai"><i class="bi bi-stars"></i></div>
                <div class="cc-msg cc-ai">
                    <div class="cc-dots">
                        <span></span><span></span><span></span>
                    </div>
                </div>
            </div>
        `);
        scrollDown();

        const response = await fetch("/career/chat/send", {
        method: "POST",
        headers: {
            "Content-Type":"application/json",
            "X-CSRF-TOKEN":"{{ csrf_token() }}"
        },
        body: JSON.stringify({ message: msg })
    });


        const data = await response.json();

        document.getElementById(typingId)?.remove();

        if (data.reply) {
            const aiBubble = addMessage('ai', "");
            streamText(aiBubble, data.reply);
        }
    });
    </script>

    @endsection
