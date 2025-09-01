<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Task;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class TaskController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'roadmap_id' => 'required|exists:roadmaps,id',
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'duration_in_days' => 'required|integer|min:1',
            'required_volunteers' => 'required|integer|min:1',
        ]);

        // (من المفترض أن يكون لديك منطق للتحقق من أن المستخدم هو مشرف على الرودماب)

        $task = Task::create($request->all());

        return response()->json($task, 201);
    }

    // Volunteer chooses a task
    public function chooseTask(Task $task)
    {
        $user = Auth::user();

        return DB::transaction(function () use ($task, $user) {
            // Lock the task row for update to prevent race conditions
            $task = Task::where('id', $task->id)->lockForUpdate()->firstOrFail();

            if ($task->required_volunteers <= 0) {
                return response()->json(['message' => 'No more volunteers needed for this task.'], 400);
            }

            // Check if user is already assigned to this task
            if ($task->volunteers()->where('volunteer_id', $user->id)->exists()) {
                return response()->json(['message' => 'You are already assigned to this task.'], 400);
            }

            // Assign volunteer to the task
            $task->volunteers()->attach($user->id);

            // Decrement required volunteers count
            $task->decrement('required_volunteers');

            // Add task hours to volunteer's total hours
            // (نفترض أن اليوم 8 ساعات عمل)
            $hours_to_add = $task->duration_in_days * 8;
            $user->volunteerDetails()->increment('total_hours_volunteered', $hours_to_add);

            return response()->json(['message' => 'Task assigned successfully.']);
        });
    }
}
