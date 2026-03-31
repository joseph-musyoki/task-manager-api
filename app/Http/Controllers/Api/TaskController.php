<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreTaskRequest;
use App\Http\Requests\UpdateTaskStatusOnlyRequest;
use App\Http\Requests\UpdateTaskStatusRequest;
use App\Models\Task;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class TaskController extends Controller
{
    
     // POST /api/tasks
    
    public function store(StoreTaskRequest $request): JsonResponse
    {
        $task = Task::create($request->validated());

        return response()->json([
            'success' => true,
            'message' => 'Task created successfully.',
            'data'    => $task,
        ], 201);
    }

    
     //GET /api/tasks
     
    public function index(Request $request): JsonResponse
    {
        // Validate optional status filter
        $request->validate([
            'status' => ['nullable', Rule::in(['pending', 'in_progress', 'done'])],
        ]);

        $tasks = Task::query()
            ->filterByStatus($request->query('status'))
            ->sortByPriorityAndDate()
            ->get();

        if ($tasks->isEmpty()) {
            return response()->json([
                'success' => true,
                'message' => 'No tasks found.',
                'data'    => [],
            ]);
        }

        return response()->json([
            'success' => true,
            'data'    => $tasks,
        ]);
    }

   
     // PUT /api/tasks/{id}
     
    public function update(UpdateTaskStatusRequest $request, int $id): JsonResponse
    {
        $task = Task::findOrFail($id);

        $task->update($request->validated());

        return response()->json([
            'success' => true,
            'message' => 'Task updated successfully.',
            'data'    => $task->fresh(),
        ]);
    }


   
     //PATCH /api/tasks/{id}/status
     
    


    /**
     * DELETE /api/tasks/{id}
     * Delete a task — only allowed if status is 'done'.
     */
    public function destroy(int $id): JsonResponse
    {
        $task = Task::findOrFail($id);

        if ($task->status !== 'done') {
            return response()->json([
                'success' => false,
                'message' => 'Forbidden. Only tasks with status "done" can be deleted.',
            ], 403);
        }

        $task->delete();

        return response()->json([
            'success' => true,
            'message' => 'Task deleted successfully.',
        ]);
    }

    /**
     * GET /api/tasks/report?date=YYYY-MM-DD
     * Bonus: Return task counts grouped by priority and status for a given date.
     */
    public function report(Request $request): JsonResponse
    {
        $request->validate([
            'date' => ['required', 'date', 'date_format:Y-m-d'],
        ]);

        $date = $request->query('date');

        $tasks = Task::whereDate('due_date', $date)->get();

        $priorities = ['high', 'medium', 'low'];
        $statuses   = ['pending', 'in_progress', 'done'];

        // Build a zeroed-out summary scaffold
        $summary = array_fill_keys(
            $priorities,
            array_fill_keys($statuses, 0)
        );

        // Populate counts
        foreach ($tasks as $task) {
            $summary[$task->priority][$task->status]++;
        }

        return response()->json([
            'success' => true,
            'date'    => $date,
            'summary' => $summary,
        ]);
    }

    public function updateStatus(UpdateTaskStatusOnlyRequest $request, int $id): JsonResponse
    {
        $task = Task::findOrFail($id);
        $newStatus = $request->validated()['status'];

        if (!$task->canTransitionTo($newStatus)) {
         $allowed = Task::STATUS_TRANSITIONS[$task->status] ?? null;

        return response()->json([
            'success' => false,
            'message' => $allowed
                ? "Invalid transition. '{$task->status}' can only move to '{$allowed}'."
                : "Task is already '{$task->status}' and cannot be updated further.",
        ], 422);
    }

    $task->update(['status' => $newStatus]);

    return response()->json([
        'success' => true,
        'message' => 'Task status updated successfully.',
        'data'    => $task->fresh(),
    ]);
}
}