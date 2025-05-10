<?php

namespace App\Http\Controllers;


use Illuminate\Http\Request;

use App\Models\Task;
use App\Models\TaskColumn;

class TaskController extends Controller
{
  public function __construct()
  {
    $this->middleware(['auth', 'verified']);
  }
  public function index()
  {
    $tasks = Task::all();
    $columns = TaskColumn::all();

    return view('tasks.index', compact('tasks', 'columns'));
  }

  public function create()
  {
    return view('tasks.create');
  }

  public function store(Request $request)
  {
    $validated = $request->validate([
      'title' => 'required|string|max:255',
      'description' => 'nullable|string',
      'project_id' => 'required|exists:projects,id',
      'task_category_id' => 'nullable|exists:task_categories,id',
      'task_priority_id' => 'nullable|exists:task_priorities,id',
    ]);

    Task::create($validated);

    return redirect()->route('tasks.index')->with('success', 'Tâche ajoutée avec succès.');
  }

  public function updateStatus(Request $request, Task $task)
  {
    $this->authorize('update', $task);


    $request->validate([
      'status' => 'required|in:to_do,in_progress,done,cancelled',
    ]);

    $task->status = $request->input('status');
    $task->save();

    return response()->noContent();
  }
  public function moveColumn(Request $request, Task $task)
  {
    $this->authorize('update', $task);

    $request->validate([
      'task_column_id' => 'required|exists:task_columns,id',
    ]);

    $task->update(['task_column_id' => $request->task_column_id]);

    return response()->noContent();
  }

}
