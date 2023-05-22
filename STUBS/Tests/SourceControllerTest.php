<?php

use App\Jobs\ProcessSourceJob;
use App\Models\Project;
use App\Models\Source;
use App\Models\User;
use Illuminate\Support\Facades\Queue;
use function Pest\Laravel\assertDatabaseCount;

it('should show the form for URL Source [RESOURCE_NAME]', function () {
    $user = User::factory()->withPersonalTeam()
        ->create();

    $user = $this->createTeam($user);

    $project = Project::factory()->create([
        'team_id' => $user->current_team_id,
    ]);

    $this->actingAs($user)
        ->get(route('sources.[RESOURCE_KEY].create', [
            'project' => $project->id,
        ]))
        ->assertOk();
});

it('should allow you to edit [RESOURCE_NAME]', function () {
    $user = User::factory()->withPersonalTeam()
        ->create();

    $user = $this->createTeam($user);

    $project = Project::factory()->create([
        'team_id' => $user->current_team_id,
    ]);

    $source = Source::factory()->create([
        'project_id' => $project->id,
    ]);

    $this->actingAs($user)
        ->get(route('sources.[RESOURCE_KEY].edit', [
            'project' => $project->id,
            'source' => $source->id,
        ]))
        ->assertOk();
});

it('should run [RESOURCE_NAME]', function () {
    Queue::fake();
    $user = User::factory()->withPersonalTeam()
        ->create();

    $user = $this->createTeam($user);

    $project = Project::factory()->create([
        'team_id' => $user->current_team_id,
    ]);

    $source = Source::factory()->create([
        'project_id' => $project->id,
    ]);

    $this->actingAs($user)
        ->post(route('sources.[RESOURCE_KEY].run', [
            'project' => $project->id,
            'source' => $source->id,
        ]))
        ->assertRedirectToRoute('projects.show', [
            'project' => $project->id,
        ]);

    Queue::assertPushed(ProcessSourceJob::class);
});

it('should allow you to update [RESOURCE_NAME]', function () {
    $user = User::factory()->withPersonalTeam()
        ->create();

    $user = $this->createTeam($user);

    $project = Project::factory()->create([
        'team_id' => $user->current_team_id,
    ]);

    $source = Source::factory()->create([
        'project_id' => $project->id,
    ]);

    $this->actingAs($user)
        ->put(route('sources.[RESOURCE_KEY].update', [
            'project' => $project->id,
            'source' => $source->id,
        ]), [
            'name' => 'Foo',
            'meta_data' => [
                'url' => 'https://foo.bar',
            ],
            'description' => 'Bar',
        ])
        ->assertRedirectToRoute('projects.show', [
            'project' => $project->id,
        ]);

    expect($source->refresh()->name)->toBe('Foo');
});

it('should create [RESOURCE_NAME]', function () {
    $user = User::factory()->withPersonalTeam()
        ->create();

    $user = $this->createTeam($user);

    $project = Project::factory()->create([
        'team_id' => $user->current_team_id,
    ]);

    assertDatabaseCount('sources', 0);

    $this->actingAs($user)
        ->post(route('sources.[RESOURCE_KEY].store', [
            'project' => $project->id,
        ]), [
            'name' => 'Foo',
            'description' => 'Bar',
            'meta_data' => [
                'url' => 'https://foo.bar',
            ],
        ])
        ->assertRedirectToRoute('projects.show', [
            'project' => $project->id,
        ]);
    assertDatabaseCount('sources', 1);
});
