<?php

namespace App\Http\Controllers;

use App\Models\Task;
use Illuminate\Http\Request;

class TaskController extends Controller
{
    public function store()
    {
        Task::create($this->getValidate());
    }

    public function update(Task $task)
    {
        $task->update($this->getValidate());
    }

    /**
     * @return array
     */
    public function getValidate(): array
    {
        return request()->validate([
            'title' => 'required',
            'description' => 'sometimes'
        ]);
    }
}
