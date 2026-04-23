@extends('layouts.app')

@section('content')
<div class="container mt-5">

    <div class="card shadow-lg p-4">
        <h2 class="text-center mb-4">AI Career Quiz Questions</h2>

        <div id="loading" class="text-center text-muted mb-3" style="display:none;">
            <div class="spinner-border text-primary"></div>
            <p>Loading questions...</p>
        </div>

        <form id="quizForm" style="display:none;">
            <div id="questionArea"></div>

            <div class="text-center mt-4">
                <button type="submit" class="btn btn-success btn-lg" id="submitBtn">
                    Submit Answers
                </button>
            </div>
        </form>

        <div id="responseMsg" class="mt-4 text-center"></div>
    </div>
</div>


<script>
document.addEventListener("DOMContentLoaded", function() {
    loadQuestions();
});


/* --------------------------------------------------------------------------
    1) FETCH QUESTIONS FROM BACKEND
-------------------------------------------------------------------------- */
function loadQuestions() {

    document.getElementById("loading").style.display = "block";

    fetch("{{ route('ai.quiz.fetch') }}")
        .then(res => res.json())
        .then(data => {

            document.getElementById("loading").style.display = "none";
            document.getElementById("quizForm").style.display = "block";

            let qArea = document.getElementById("questionArea");
            qArea.innerHTML = "";

            // Already taken?
            if (data.already_taken) {
                document.getElementById("responseMsg").innerHTML =
                    "<div class='alert alert-warning'>You have already completed this quiz. Your answers are shown below.</div>";

                document.querySelector("#submitBtn").style.display = "none";
            }

            // Render questions
            data.questions.forEach((q, index) => {
                qArea.innerHTML += `
                    <div class="mb-4">
                        <label class="form-label fw-bold">${index + 1}. ${q.question_text}</label>
                        <textarea 
                            class="form-control"
                            data-qid="${q.id}"
                            rows="2"
                            ${data.already_taken ? "disabled" : ""}
                        ></textarea>
                    </div>
                `;
            });

            // If already taken, fetch stored answers
            if (data.already_taken) {
                fetch("/debug/answers")
                    .then(r => r.json())
                    .then(ans => {
                        ans.forEach(a => {
                            let box = document.querySelector(`[data-qid='${a.ai_quiz_id}']`);
                            if (box) box.value = a.answer_text;
                        });
                    });
            }
        })
        .catch(() => {
            document.getElementById("responseMsg").innerHTML =
                "<div class='alert alert-danger'>Failed to load questions.</div>";
        });
}



/* --------------------------------------------------------------------------
    2) SUBMIT ANSWERS
-------------------------------------------------------------------------- */
document.getElementById("quizForm").addEventListener("submit", function(e) {
    e.preventDefault();

    let answers = [];
    const textareas = document.querySelectorAll("#questionArea textarea");

    textareas.forEach(t => {
        let qid = t.getAttribute("data-qid");
        let text = t.value.trim();

        if (qid && text.length > 0) {
            answers.push({
                question_id: Number(qid),
                answer: text
            });
        }
    });

    // Debug
    console.log("Sending answers → ", answers);

    fetch("{{ route('ai.quiz.answers') }}", {
        method: "POST",
        headers: {
            "Content-Type": "application/json",
            "Accept": "application/json",
            "X-CSRF-TOKEN": "{{ csrf_token() }}"
        },
        body: JSON.stringify({ answers: answers })
    })
    .then(res => res.json())
    .then(data => {

        if (data.status === "error") {
            document.getElementById("responseMsg").innerHTML =
                `<div class='alert alert-danger'>${data.message}</div>`;
            return;
        }

        // SUCCESS
        document.getElementById("responseMsg").innerHTML =
            `<div class='alert alert-success'>${data.message}</div>`;

        setTimeout(() => {
            window.location.href = "{{ route('career.suggestions') }}";
        }, 1000);
    })
    .catch(err => {
        alert("Submit Error: " + err);
    });
});
</script>
@endsection
