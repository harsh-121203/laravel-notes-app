<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    use RefreshDatabase;
    /**
     * A basic test example.
     */
    public function test_the_application_returns_a_successful_response(): void
    {
        $response = $this->get('/');
        $response->assertStatus(200);

        $notesResponse = $this->get('/notes');
        $notesResponse->assertRedirect('/');
    }

    public function test_can_create_task_for_today(): void
    {
        $response = $this->post(route('tasks.store'), [
            'title' => 'Today Task',
            'task_date' => now()->toDateString(),
        ]);

        $response->assertSessionHas('success');
        $this->assertDatabaseHas('tasks', [
            'title' => 'Today Task',
            'task_date' => now()->toDateString(),
        ]);
    }

    public function test_can_create_task_for_future_date(): void
    {
        $futureDate = now()->addDays(3)->toDateString();
        $response = $this->post(route('tasks.store'), [
            'title' => 'Future Task',
            'task_date' => $futureDate,
        ]);

        $response->assertSessionHas('success');
        $this->assertDatabaseHas('tasks', [
            'title' => 'Future Task',
            'task_date' => $futureDate,
        ]);
    }

    public function test_cannot_create_task_for_past_date(): void
    {
        $pastDate = now()->subDay()->toDateString();
        $response = $this->post(route('tasks.store'), [
            'title' => 'Past Task',
            'task_date' => $pastDate,
        ]);

        $response->assertSessionHasErrors('task_date');
        $this->assertDatabaseMissing('tasks', [
            'title' => 'Past Task',
        ]);
    }

    public function test_can_toggle_and_delete_task_on_past_date(): void
    {
        $pastDate = now()->subDays(2)->toDateString();
        $task = \App\Models\Task::create([
            'title' => 'Existing Past Task',
            'task_date' => $pastDate,
            'is_completed' => false,
        ]);

        // Toggle task
        $toggleResponse = $this->patch(route('tasks.toggle', $task));
        $toggleResponse->assertSessionHas('success');
        $this->assertTrue($task->fresh()->is_completed);

        // Delete task
        $deleteResponse = $this->delete(route('tasks.destroy', $task));
        $deleteResponse->assertSessionHas('success');
        $this->assertDatabaseMissing('tasks', ['id' => $task->id]);
    }
}
