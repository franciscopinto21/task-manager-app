<?php

namespace Tests\Feature;

use App\Models\Task;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TaskControllerTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_can_list_tasks()
    {
        Task::factory()->count(5)->create();

        $response = $this->getJson('/api/tasks');

        $response->assertStatus(200)
                 ->assertJsonStructure([
                     'data',
                     'meta',
                     'links',
                 ]);
    }

    /** @test */
    public function it_can_create_a_task()
    {
        $payload = [
            'title' => 'New Task',
            'description' => 'Testing creation',
        ];

        $response = $this->postJson('/api/tasks', $payload);

        $response->assertCreated()
                 ->assertJsonPath('data.title', 'New Task');
    }

    /** @test */
    public function it_can_update_a_task()
    {
        $task = Task::factory()->create([
            'title' => 'Old Title',
        ]);

        $payload = ['title' => 'Updated Title'];

        $response = $this->putJson("/api/tasks/{$task->id}", $payload);

        $response->assertOk()
                 ->assertJsonPath('data.title', 'Updated Title');
    }

    /** @test */
    public function it_can_delete_a_task()
    {
        $task = Task::factory()->create();

        $response = $this->deleteJson("/api/tasks/{$task->id}");

        $response->assertNoContent();

        $this->assertDatabaseMissing('tasks', [
            'id' => $task->id,
        ]);
    }

    /** @test */
    public function it_can_toggle_task_done_status()
    {
        $task = Task::factory()->create(['is_done' => false]);

        $response = $this->putJson("/api/tasks/{$task->id}", [
            'title' => $task->title,
            'description' => $task->description,
            'is_done' => true,
        ]);

        $response->assertOk()
                ->assertJsonPath('data.is_done', true);
    }

    /** @test */
    public function it_can_edit_a_task_completely()
    {
        $task = Task::factory()->create([
            'title' => 'Old Title',
            'description' => 'Old Description',
        ]);

        $response = $this->putJson("/api/tasks/{$task->id}", [
            'title' => 'New Title',
            'description' => 'New Description',
            'is_done' => true,
        ]);

        $response->assertOk()
                ->assertJsonPath('data.title', 'New Title')
                ->assertJsonPath('data.description', 'New Description')
                ->assertJsonPath('data.is_done', true);
    }

}
