<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\CareerProgress;
use App\Models\ProgressTask;
use Illuminate\Support\Facades\Auth;

class CareerProgressController extends Controller
{
    /**
     * --------------------------------------------------------------
     * SHOW TRACKER PAGE
     * --------------------------------------------------------------
     */
    public function index()
    {
        $user = Auth::user();

        /* -----------------------------------------------------------
         | 1) Extract clean career name from AI suggestion
         ------------------------------------------------------------*/
        $careerSuggestion = $user->career_suggestion ?? '';
        $career = "My Career";

        if ($careerSuggestion) {
            $lines = preg_split('/\r\n|\r|\n/', trim($careerSuggestion));

            foreach ($lines as $line) {
                $clean = trim($line);

                if (
                    strlen($clean) > 3 &&
                    !str_contains(strtolower($clean), 'top') &&
                    !str_contains(strtolower($clean), 'recommend')
                ) {
                    $career = $clean;
                    break;
                }
            }

            // avoid DB error
            if (strlen($career) > 100) {
                $career = substr($career, 0, 100);
            }
        }

        /* -----------------------------------------------------------
         | 2) Load or create progress record
         ------------------------------------------------------------*/
        $progress = CareerProgress::firstOrCreate(
            ['user_id' => $user->id],
            ['career_name' => $career, 'progress_percent' => 0]
        );

        /* -----------------------------------------------------------
         | 3) Load tasks (ordered)
         ------------------------------------------------------------*/
        $tasks = $progress->tasks()->orderBy('order_number')->get();

        $completed = $tasks->where('is_completed', true)->count();
        $total = $tasks->count();
        $pending = $total - $completed;

        $percent = ($total > 0)
            ? round(($completed / $total) * 100)
            : 0;

        $progress->update(['progress_percent' => $percent]);

        return view('dashboard.features.tracker', [
            'progress'       => $progress,
            'tasks'          => $tasks,
            'totalTasks'     => $total,
            'completedTasks' => $completed,
            'pendingTasks'   => $pending,
        ]);
    }



    /**
     * --------------------------------------------------------------
     * ADD A NEW MANUAL TASK
     * --------------------------------------------------------------
     */
    public function addTask(Request $request)
    {
        $request->validate([
            'progress_id'      => 'required|exists:career_progresses,id',
            'task_title'       => 'required|string|max:255',
            'task_description' => 'nullable|string',
            'phase_name'       => 'nullable|string|max:255',
            'order_number'     => 'nullable|numeric',
        ]);

        $order = $request->order_number ??
            (ProgressTask::where('progress_id', $request->progress_id)->max('order_number') + 1);

        ProgressTask::create([
            'progress_id'      => $request->progress_id,
            'task_title'       => ucfirst(trim($request->task_title)),
            'task_description' => trim($request->task_description),
            'phase_name'       => trim($request->phase_name),
            'order_number'     => $order,
            'is_completed'     => false,
        ]);

        return back()->with('success', 'Task added successfully!');
    }



    /**
     * --------------------------------------------------------------
     * TOGGLE TASK (COMPLETED / PENDING)
     * --------------------------------------------------------------
     */
    public function toggleTask($id)
    {
        $task = ProgressTask::findOrFail($id);
        $task->is_completed = !$task->is_completed;
        $task->save();

        return back()->with('success', 'Task updated successfully!');
    }
    /**
 * --------------------------------------------------------------
 * GENERATE AI TASKS FROM ROADMAP JSON
 * --------------------------------------------------------------
 */
public function generateTasksFromAI(Request $request)
{
    $request->validate([
        'roadmap_id' => 'required|exists:roadmaps,id'
    ]);

    $user = Auth::user();

    // Load roadmap
    $roadmap = \App\Models\Roadmap::findOrFail($request->roadmap_id);

    if (!$roadmap->roadmap_json) {
        return back()->with('error', 'Roadmap has no data.');
    }

    $roadmapArray = json_decode($roadmap->roadmap_json, true);

    if (!$roadmapArray) {
        return back()->with('error', 'Invalid roadmap data.');
    }

    // Extract phases for AI
    $phaseList = $roadmapArray['phases'] ?? [];

    if (empty($phaseList)) {
        return back()->with('error', 'No phases found in roadmap.');
    }

    // -------------------------------
    // BUILD AI PROMPT
    // -------------------------------
    $prompt = "
You are an expert career mentor.

Convert this roadmap into a clean, structured list of practical tasks.

Return ONLY JSON:

{
  \"tasks\": [
     {
        \"title\": \"...\",
        \"description\": \"...\",
        \"phase\": \"...\"
     }
  ]
}

Roadmap data:
" . json_encode($phaseList);

    // -------------------------------
    // CALL AI (Groq API)
    // -------------------------------
    $apiKey = env('GROQ_API_KEY');
    $model = env('GROQ_MODEL', 'openai/gpt-oss-20b');

    if (!$apiKey) {
        return back()->with('error', 'Missing AI API key.');
    }

    try {
        $response = \Illuminate\Support\Facades\Http::withHeaders([
            "Content-Type" => "application/json",
            "Authorization" => "Bearer {$apiKey}"
        ])
        ->timeout(40)
        ->post("https://api.groq.com/openai/v1/chat/completions", [
            "model" => $model,
            "messages" => [
                ["role" => "system", "content" => "Return only JSON."],
                ["role" => "user", "content" => $prompt]
            ],
            "temperature" => 0.2
        ]);

        if (!$response->successful()) {
            return back()->with('error', 'AI request failed.');
        }

        $jsonText = $response->json()['choices'][0]['message']['content'] ?? '';
        $jsonText = trim(preg_replace('/```json|```/i', '', $jsonText));

        $taskData = json_decode($jsonText, true);

        if (!$taskData || empty($taskData['tasks'])) {
            return back()->with('error', 'AI returned invalid task data.');
        }

    } catch (\Exception $e) {
        return back()->with('error', 'AI error: ' . $e->getMessage());
    }

    // -------------------------------
    // STORE TASKS IN DATABASE
    // -------------------------------
    $progress = CareerProgress::firstOrCreate(
        ['user_id' => $user->id],
        ['career_name' => 'My Career', 'progress_percent' => 0]
    );

    $order = ProgressTask::where('progress_id', $progress->id)->max('order_number') + 1;

    foreach ($taskData['tasks'] as $task) {

        ProgressTask::create([
            'progress_id'      => $progress->id,
            'task_title'       => ucfirst($task['title']),
            'task_description' => $task['description'] ?? null,
            'phase_name'       => $task['phase'] ?? 'General',
            'order_number'     => $order++,
            'is_completed'     => 0,
        ]);
    }

    return back()->with('success', 'AI-generated tasks added to your tracker!');
}




    /**
     * --------------------------------------------------------------
     * ADD TASKS FROM ROADMAP (IMPORTED PHASE)
     * --------------------------------------------------------------
     */
    public function addFromRoadmap(Request $request)
    {
        $request->validate([
            'phase_name' => 'required|string',
            'tasks'      => 'required'
        ]);

        $user = Auth::user();

        $progress = CareerProgress::firstOrCreate(
            ['user_id' => $user->id],
            ['career_name' => 'My Career', 'progress_percent' => 0]
        );

        $taskList = json_decode($request->tasks, true);

        if (!$taskList || !is_array($taskList)) {
            return back()->with('error', 'Invalid task list received.');
        }

        $order = ProgressTask::where('progress_id', $progress->id)->max('order_number') + 1;

        foreach ($taskList as $raw) {

            $clean = trim(preg_replace('/\s+/', ' ', $raw));

            // Split long text into title + description
            if (strlen($clean) > 70) {
                $parts = explode('.', $clean, 2);
                $title = trim($parts[0]);
                $desc = isset($parts[1]) ? trim($parts[1]) : null;
            } else {
                $title = $clean;
                $desc = null;
            }

            ProgressTask::create([
                'progress_id'      => $progress->id,
                'task_title'       => ucfirst($title),
                'task_description' => $desc,
                'phase_name'       => $request->phase_name,
                'order_number'     => $order++,
                'is_completed'     => false,
            ]);
        }

        return back()->with('success', 'Roadmap tasks added successfully!');
    }
    /**
 * ---------------------------------------------------------
 * DELETE A TASK
 * ---------------------------------------------------------
 */
public function deleteTask($id)
{
    $task = ProgressTask::findOrFail($id);

    // Security: ensure the logged-in user owns the task
    if ($task->progress->user_id !== Auth::id()) {
        return back()->with('error', 'Unauthorized action.');
    }

    $progress = $task->progress;

    // Delete
    $task->delete();

    // Recalculate progress
    $tasks = $progress->tasks;
    $completed = $tasks->where('is_completed', 1)->count();
    $total = $tasks->count();

    $percent = ($total > 0)
        ? round(($completed / $total) * 100)
        : 0;

    $progress->update(['progress_percent' => $percent]);

    return back()->with('success', 'Task deleted successfully!');
}

}
