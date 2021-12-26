<?php

namespace Tests\Feature;

use App\Models\Task;
use App\Models\User;
use Carbon\Carbon;
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

    /** @test */
    public function new_task_with_deadline()
    {
        $user = User::factory()->create();

        Sanctum::actingAs($user);

        $response = $this->post('api/tasks', [
            'title' => 'some title',
            'description' => 'some description',
            'ended_at' => now()->addDays(5)
        ]);

        $response->assertOk();

        $task = $user->tasks->first();

        $this->assertNotNull($task->ended_at);
    }

    /** @test */
    public function new_task_with_wrong_deadline()
    {
        $user = User::factory()->create();

        Sanctum::actingAs($user);

        $response = $this->post('api/tasks', [
            'title' => 'some title',
            'description' => 'some description',
            'ended_at' => 'wrong date format'
        ]);

//        dd($user->tasks->first());

        $response->assertSessionHasErrors('ended_at');
    }

    /** @test */
    public function new_task_with_wrong_past_deadline()
    {
        $user = User::factory()->create();

        Sanctum::actingAs($user);

        $response = $this->post('api/tasks', [
            'title' => 'some title',
            'description' => 'some description',
            'ended_at' => now()->subDays(3)
        ]);

        $response->assertSessionHasErrors('ended_at');
    }

    /** @test */
    public function new_task_with_deadline_has_right_format()
    {
        $user = User::factory()->create();

        Sanctum::actingAs($user);

        $response = $this->post('api/tasks', [
            'title' => 'some title',
            'description' => 'some description',
            'ended_at' => now()->addDays(5)
        ]);

        $response->assertOk();

        $task = $user->tasks->first();

//        dd($task->ended_at . ' is a ' . gettype($task->ended_at));

        $this->assertInstanceOf(Carbon::class, $task->ended_at);
    }

    /** @test */
    public function update_task_with_deadline()
    {
        $user = User::factory()->create();

        Sanctum::actingAs($user);

        $this->post('api/tasks', [
            'title' => 'some title',
            'description' => 'some description',
        ]);
        $this->patch('api/tasks/' . Task::first()->id, [
            'title' => 'new title',
            'description' => 'new description',
            'ended_at' => now()->addDays(5)
        ]);

        $task = $user->tasks->first();

        $this->assertEquals('new title', $task->title);
        $this->assertEquals('new description', $task->description);
        $this->assertNotNull($task->ended_at);

    }

    /** @test */
    public function update_task_with_wrong_deadline()
    {
        $user = User::factory()->create();

        Sanctum::actingAs($user);

        $this->post('api/tasks', [
            'title' => 'some title',
            'description' => 'some description',
        ]);
        $response = $this->patch('api/tasks/' . Task::first()->id, [
            'title' => 'new title',
            'description' => 'new description',
            'ended_at' => 'wrong date format'
        ]);

        $response->assertSessionHasErrors('ended_at');
    }

    /** @test */
    public function update_task_with_wrong_past_deadline()
    {
        $user = User::factory()->create();

        Sanctum::actingAs($user);

        $this->post('api/tasks', [
            'title' => 'some title',
            'description' => 'some description',
        ]);
        $response = $this->patch('api/tasks/' . Task::first()->id, [
            'title' => 'new title',
            'description' => 'new description',
            'ended_at' => now()->subDays(3)
        ]);

        $response->assertSessionHasErrors('ended_at');
    }
}
