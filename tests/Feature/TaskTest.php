<?php

namespace Tests\Feature;

use App\Models\Task;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TaskTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function add_new_task()
    {
        $this->withoutExceptionHandling();

        $response = $this->post('tasks', [
            'title' => 'sample title',
            'description' => 'something'
        ]);

        $response->assertOk();
    }

    /** @test */
    public function new_task_request_validation()
    {
        $response = $this->post('tasks', [
            'title' => '',
            'description' => ''
        ]);

        $response->assertSessionHasErrors();
    }

    /** @test */
    public function update_a_task()
    {
        $this->withoutExceptionHandling();

        $this->post('tasks', [
            'title' => 'sample title',
            'description' => 'something'
        ]);

        $this->patch('tasks/' . Task::first()->id, [
            'title' => 'new title',
            'description' => 'new description'
        ]);

        $this->assertEquals('new title', Task::first()->title);
        $this->assertEquals('new description', Task::first()->description);

    }
}
