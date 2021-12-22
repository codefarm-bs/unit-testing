<?php

namespace App\Http\Controllers;

use App\Http\Requests\TaskRequest;
use Illuminate\Support\Facades\Auth;

class TaskController extends Controller
{
    public function index()
    {
        return $this->success(Auth::user()->tasks);
    }

    public function show($task_id)
    {
        return $this->success(Auth::user()->tasks()->where('id', $task_id)->firstOrFail());
    }

    public function store(TaskRequest $request)
    {
        Auth::user()->tasks()->create($request->validated());

        return $this->success('', 'Your task created successfully');
    }

    public function update(TaskRequest $request, $task_id)
    {
        Auth::user()->tasks()->where('id', $task_id)->firstOrFail()->update($request->validated());

        return $this->success('', 'Your task updated successfully');
    }

    public function destroy($task_id)
    {
        Auth::user()->tasks()->where('id', $task_id)->firstOrFail()->delete();

        return $this->success('', 'Your task deleted successfully');
    }
}
