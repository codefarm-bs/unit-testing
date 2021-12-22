<?php

namespace Tests\Feature;

use App\Models\Task;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class TaskTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function add_new_task()
    {
        Sanctum::actingAs(User::factory()->create());

        $response = $this->post('api/tasks', [
            'title' => 'sample title',
            'description' => 'something'
        ]);

        $response->assertOk();
    }

    /** @test */
    public function new_task_request_validation()
    {
        Sanctum::actingAs(User::factory()->create());

        $response = $this->post('api/tasks', [
            'title' => '',
            'description' => ''
        ]);

        $response->assertSessionHasErrors();
    }

    /** @test */
    public function update_a_task()
    {
        $this->withoutExceptionHandling();

        Sanctum::actingAs(User::factory()->create());

        $this->post('api/tasks', [
            'title' => 'sample title',
            'description' => 'something'
        ]);

        $this->patch('api/tasks/' . Task::first()->id, [
            'title' => 'new title',
            'description' => 'new description'
        ]);

        $this->assertEquals('new title', Task::first()->title);
        $this->assertEquals('new description', Task::first()->description);

    }

    /** @test */
    public function delete_a_task()
    {
        Sanctum::actingAs(User::factory()->create());

        $this->post('api/tasks', [
            'title' => 'sample title',
            'description' => 'something'
        ]);

        $this->delete('api/tasks/' . Task::first()->id);

        $this->assertCount(0, Task::all());
    }

    /** @test */
    public function get_user_task()
    {
        $user = User::factory()->create();
        $task = $user->tasks()->create([
            'title' => 'sample title',
            'description' => 'something'
        ]);

        Sanctum::actingAs($user);

        $response = $this->get('api/tasks/' . $task->id);

        $response->assertOk();
    }

    /** @test */
    public function handle_user_task_unacceptable()
    {
        $user = User::factory()->create();
        $task = $user->tasks()->create([
            'title' => 'sample title',
            'description' => 'something'
        ]);

        Sanctum::actingAs(User::factory()->create());

        $response = $this->get('api/tasks/' . $task->id);

        $response->assertStatus(404);
    }

    /** @test */
    public function get_user_all_tasks()
    {
        $this->withoutExceptionHandling();

        $user = User::factory()->create();
        $user->tasks()->create([
            'title' => 'sample title',
            'description' => 'something'
        ]);

        Sanctum::actingAs($user);

        $response = $this->get('api/tasks');

        $response->assertOk();
    }
}
