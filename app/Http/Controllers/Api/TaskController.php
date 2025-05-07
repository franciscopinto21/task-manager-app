<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Task;
use Illuminate\Http\Request;
use App\Http\Resources\TaskResource;

class TaskController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Task::query();
    
        if ($request->has('is_done')) {
            $query->where('is_done', filter_var($request->input('is_done'), FILTER_VALIDATE_BOOLEAN));
        }
    
        // Search
        if ($request->has('search')) {
            $query->where('title', 'ILIKE', '%' . $request->search . '%');
        }
    
        switch ($request->input('sort', 'created_desc')) {
            case 'created_asc':
                $query->orderBy('created_at', 'asc');
                break;
            case 'created_desc':
                $query->orderBy('created_at', 'desc');
                break;
            case 'title_asc':
                $query->orderBy('title', 'asc');
                break;
            case 'title_desc':
                $query->orderBy('title', 'desc');
                break;
            default:
                $query->orderBy('created_at', 'desc');
                break;
        }
    
        $perPage = $request->input('per_page', 10);
        $tasks = $query->paginate($perPage)->withQueryString();
    
        return TaskResource::collection($tasks);
    }
    


    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        $task = Task::create($data);
        return new TaskResource($task);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Task $task)
    {
        $data = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'is_done' => 'boolean',
        ]);

        $task->update($data);
        return new TaskResource($task);
    }

    public function toggleDone(Task $task)
    {
        $task->is_done = !$task->is_done;
        $task->save();
    
        return response()->json([
            'message' => 'Task status updated.',
            'data' => $task,
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Task $task)
    {
        $task->delete();
        return response()->json(['message' => 'Deleted successfully.']);
    }
}
