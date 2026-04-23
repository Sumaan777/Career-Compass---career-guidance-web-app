@extends('layouts.app')

@section('content')

<style>
/* ===============================
   INTERVIEW PAGE STYLING ONLY
   (Chat styles untouched)
================================ */

.interview-wrapper {
    max-width: 900px;
    margin: auto;
}

.interview-header {
    background: linear-gradient(135deg, #0d6efd, #6610f2);
    color: #fff;
    padding: 30px;
    border-radius: 16px;
    text-align: center;
    box-shadow: 0 8px 25px rgba(0,0,0,0.15);
    margin-bottom: 30px;
}

.interview-header h2 {
    font-weight: 700;
}

.interview-header p {
    opacity: 0.9;
    margin-bottom: 0;
}

.field-card {
    border-radius: 16px;
    border: none;
    box-shadow: 0 6px 20px rgba(0,0,0,0.08);
}

.field-card label {
    font-weight: 600;
}

.note-box {
    background: #f8f9fa;
    border-left: 5px solid #0d6efd;
    padding: 15px;
    border-radius: 10px;
    font-size: 14px;
}

.start-btn {
    font-size: 18px;
    font-weight: 600;
    padding: 12px;
    border-radius: 12px;
}

.chat-heading {
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.timer-badge {
    background: #212529;
    color: #fff;
    padding: 6px 12px;
    border-radius: 20px;
    font-size: 14px;
}
</style>

<div class="container mt-4 interview-wrapper">

    <!-- HEADER -->
    <div class="interview-header">
        <h2>🎤 AI Interview Simulator</h2>
        <p>
            Practice real interview questions with AI-powered feedback,
            scoring, and a final performance report.
        </p>
    </div>

    <!-- FIELD SELECTION -->
    <div class="card p-4 field-card" id="fieldSection">
        <h5 class="mb-3"><strong>Select Interview Field</strong></h5>

        <label class="mt-2">Choose from list</label>
        <select id="fieldSelect" class="form-control">
            <option value="">-- Select a field --</option>
            <option>Software Engineer</option>
            <option>Graphic Designer</option>
            <option>Digital Marketer</option>
            <option>Teacher</option>
            <option>Doctor</option>
            <option>Accountant</option>
            <option>Customer Support</option>
        </select>

        <label class="mt-3">Or enter custom field</label>
        <input id="customField"
               type="text"
               class="form-control"
               placeholder="e.g., Data Scientist, Game Developer">

        <!-- NOTE -->
        <div class="note-box mt-3">
            <strong>Interview Notes:</strong>
            <ul class="mb-0 ps-3">
                <li>One question at a time</li>
                <li>Answer honestly and clearly</li>
                <li>Interview ends after 5–7 questions</li>
            </ul>
        </div>

        <button id="startBtn"
                class="btn btn-success mt-4 w-100 start-btn"
                disabled>
            ▶ Start Interview
        </button>
    </div>

    <hr class="my-4">

    <!-- CHAT SECTION (UNCHANGED STYLES) -->
    <div id="chatSection" style="display:none;">
        <div class="chat-heading mb-3">
            <h4><strong>Live Interview Chat</strong></h4>
            <span class="timer-badge">
                ⏱ <span id="timer">00:00</span>
            </span>
        </div>

        <div id="chatBox"
            class="border rounded p-3 mb-3 bg-light"
            style="height: 350px; overflow-y:auto;">
            <p class="text-muted">
                Interview will start now. Please wait for the AI interviewer...
            </p>
        </div>

        <textarea id="answerInput"
                  class="form-control"
                  rows="3"
                  placeholder="Type your answer..."></textarea>

        <button id="sendBtn" class="btn btn-primary mt-2 w-100">
            Send Answer
        </button>
    </div>
    <!-- INTERVIEW RESULT SECTION -->
<div id="resultSection" style="display:none;" class="mt-5">
    <div class="card shadow-lg border-0">
        <div class="card-body p-4">

            <h4 class="mb-3 text-center">
                🎉 Interview Completed
            </h4>

            <div class="row text-center mb-4">
                <div class="col-md-4">
                    <h6 class="text-muted">Total Score</h6>
                    <h3 class="text-success" id="resultScore">0</h3>
                </div>

                <div class="col-md-4">
                    <h6 class="text-muted">Time Taken</h6>
                    <h3 id="resultTime">00:00</h3>
                </div>

                <div class="col-md-4">
                    <h6 class="text-muted">Status</h6>
                    <span class="badge bg-success fs-6">
                        Completed
                    </span>
                </div>
            </div>

            <hr>

            <h5 class="mb-2">📄 Final Interview Report</h5>
            <p id="resultReport" class="text-muted mb-0"></p>

        </div>
    </div>
</div>


</div>

<script>
let sessionId = null;
let seconds = 0;
let timerInterval = null;

// Enable Start button
function checkField() {
    let dropdown = fieldSelect.value.trim();
    let custom = customField.value.trim();
    startBtn.disabled = (dropdown === "" && custom === "");
}

fieldSelect.addEventListener("change", checkField);
customField.addEventListener("input", checkField);

// Start interview
startBtn.addEventListener("click", function () {

    let finalField = customField.value.trim() || fieldSelect.value.trim();

    fetch("{{ route('interview.start') }}", {
        method: "POST",
        headers: {
            "Content-Type":"application/json",
            "X-CSRF-TOKEN":"{{ csrf_token() }}"
        },
        body: JSON.stringify({ field: finalField })
    })
    .then(res => res.json())
    .then(data => {

        sessionId = data.session_id;

        fieldSection.style.display = "none";
        chatSection.style.display = "block";

        startTimer();
        askAI("");
    });
});

// Timer
function startTimer() {
    timerInterval = setInterval(() => {
        seconds++;
        let m = String(Math.floor(seconds / 60)).padStart(2,'0');
        let s = String(seconds % 60).padStart(2,'0');
        timer.innerText = `${m}:${s}`;
    }, 1000);
}

// Send answer
sendBtn.addEventListener("click", function () {
    let answer = answerInput.value.trim();
    if (!answer) return;

    appendMessage("You", answer);
    answerInput.value = "";
    askAI(answer);
});

// AI interaction
function askAI(answer) {
    fetch(`/interview/chat/${sessionId}`, {
        method: "POST",
        headers: {
            "Content-Type":"application/json",
            "X-CSRF-TOKEN":"{{ csrf_token() }}"
        },
        body: JSON.stringify({ answer })
    })
    .then(res => res.json())
    .then(data => {

        if (data.feedback) {
            appendMessage("Interviewer", `<i>${data.feedback}</i>`);
        }

        if (data.question) {
            appendMessage("Interviewer", data.question);
        }

        if (data.finished) {
            clearInterval(timerInterval);

            appendMessage("Interviewer",
                `<b>Final Report:</b><br>${data.final_report}
                 <br><b>Total Score:</b> ${data.total_score}
                 <br><b>Time Taken:</b> ${timer.innerText}`
            );

            answerInput.disabled = true;
            sendBtn.disabled = true;
        }
    });
}

// Append messages
function appendMessage(sender, text) {
    let div = document.createElement("div");
    div.innerHTML = `<p><strong>${sender}:</strong> ${text}</p>`;
    chatBox.appendChild(div);
    chatBox.scrollTop = chatBox.scrollHeight;
}
</script>

@endsection
