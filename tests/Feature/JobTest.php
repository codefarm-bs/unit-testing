<?php

namespace Tests\Feature;

use App\Jobs\TaskJob;
use App\Mail\TaskMail;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Mail;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class JobTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function mocking_task_job_mailing()
    {
        Sanctum::actingAs($user = User::factory()->create());

        $response = $this->post('api/tasks', [
            'title' => 'some title',
            'description' => 'some description',
            'ended_at' => now()->addSecond()
        ]);

        $response->assertOk();

        sleep(1);

        Mail::fake();

        TaskJob::dispatch();

        Mail::assertSent(TaskMail::class);

        $this->assertEquals(1, $user->tasks()->whereNull('ended_at')->count());
    }

    /** @test */
    public function mocking_task_job()
    {
        Sanctum::actingAs($user = User::factory()->create());

        $response = $this->post('api/tasks', [
            'title' => 'some title',
            'description' => 'some description',
            'ended_at' => now()->addSecond()
        ]);

        $response->assertOk();

        Bus::fake();

        TaskJob::dispatch();

        Bus::assertDispatched(TaskJob::class);

        $this->assertEquals(0, $user->tasks()->whereNull('ended_at')->count());
    }
}
