<?php

use App\Models\Task;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Carbon\Carbon;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->user = User::factory()->create();
});

it('lists all tasks', function () {
    Task::factory()->count(3)->create();

    $response = $this->actingAs($this->user)->getJson('/api/tasks');

    $response->assertOk()
        ->assertJsonCount(3, 'data');
});

it('creates a task', function () {
    $data = [
        'title' => 'Test Task',
        'description' => 'Optional description',
        'status' => true,
        'due' => Carbon::now()->addDays(7)->toDateString(),
    ];

    $response = $this->actingAs($this->user)->postJson('/api/tasks', $data);

    $response->assertCreated()
        ->assertJsonFragment(['title' => 'Test Task']);

    $this->assertDatabaseHas('tasks', [
        'title' => 'Test Task',
        'description' => 'Optional description',
        'status' => 1, // use 1 instead of true
        'due' => '2025-10-07 00:00:00', // full DB format
    ]);
});

it('shows a task', function () {
    $task = Task::factory()->create([
        'due' => Carbon::now()->addDays(3),
        'status' => false,
    ]);

    $response = $this->actingAs($this->user)->getJson("/api/tasks/{$task->id}");

    $response->assertOk()
        ->assertJsonFragment(['id' => $task->id]);
});

it('updates a task', function () {
    $task = Task::factory()->create([
        'due' => Carbon::now()->addDays(3),
        'status' => false,
    ]);

    $data = [
        'title' => 'Updated Task',
        'description' => 'Updated description',
        'status' => true,
        'due' => Carbon::now()->addDays(10)->toDateString(),
    ];

    $response = $this->actingAs($this->user)->putJson("/api/tasks/{$task->id}", $data);

    $response->assertOk()
        ->assertJsonFragment(['title' => 'Updated Task']);

    $this->assertDatabaseHas('tasks', [
        'title' => 'Updated Task',
        'description' => 'Updated description',
        'status' => 1, // use integer 1 for boolean
        'due' => Carbon::now()->addDays(10)->format('Y-m-d 00:00:00'),
    ]);

});

it('deletes a task', function () {
    $task = Task::factory()->create([
        'due' => Carbon::now()->addDays(5),
        'status' => true,
    ]);

    $response = $this->actingAs($this->user)->deleteJson("/api/tasks/{$task->id}");

    $response->assertNoContent();

    $this->assertDatabaseMissing('tasks', ['id' => $task->id]);
});
