<?php

use App\Models\Tag;
use App\Models\User;

describe('TagController', function () {

    beforeEach(function () {
        $this->user = User::factory()->create();
    });

    describe('Index', function () {
        it('can list user tags', function () {
            Tag::factory()->count(3)->create(['user_id' => $this->user->id]);
            Tag::factory()->create(); // Another user's tag

            $response = $this->actingAs($this->user)
                ->getJson('/api/tags');

            $response->assertStatus(200)
                ->assertJsonCount(3, 'data');
        });

        it('returns paginated results', function () {
            Tag::factory()->count(20)->create(['user_id' => $this->user->id]);

            $response = $this->actingAs($this->user)
                ->getJson('/api/tags');

            $response->assertStatus(200)
                ->assertJsonStructure([
                    'data',
                    'links',
                    'meta',
                ]);
        });

        it('requires authentication', function () {
            $response = $this->getJson('/api/tags');

            $response->assertStatus(401);
        });
    });

    describe('Store', function () {
        it('can create a new tag', function () {
            $data = [
                'name' => 'Test Tag',
                'icon' => 'test-icon',
                'color' => '#FF0000',
            ];

            $response = $this->actingAs($this->user)
                ->postJson('/api/tags', $data);

            $response->assertStatus(200)
                ->assertJsonFragment([
                    'name' => 'Test Tag',
                    'icon' => 'test-icon',
                    'color' => '#FF0000',
                ]);

            $this->assertDatabaseHas('tags', [
                'name' => 'Test Tag',
                'icon' => 'test-icon',
                'user_id' => $this->user->id,
            ]);
        });

        it('validates required fields', function () {
            $response = $this->actingAs($this->user)
                ->postJson('/api/tags', []);

            $response->assertStatus(422)
                ->assertJsonValidationErrors(['name', 'icon']);
        });

        it('requires authentication', function () {
            $response = $this->postJson('/api/tags', [
                'name' => 'Test Tag',
                'icon' => 'test-icon',
            ]);

            $response->assertStatus(401);
        });
    });

    describe('Show', function () {
        it('can show a specific tag', function () {
            $tag = Tag::factory()->create(['user_id' => $this->user->id]);

            $response = $this->actingAs($this->user)
                ->getJson("/api/tags/{$tag->uuid}");

            $response->assertStatus(200)
                ->assertJson([
                    'uuid' => $tag->uuid,
                    'name' => $tag->name,
                ]);
        });

        it('cannot show another user tag', function () {
            $otherUser = User::factory()->create();
            $tag = Tag::factory()->create(['user_id' => $otherUser->id]);

            $response = $this->actingAs($this->user)
                ->getJson("/api/tags/{$tag->uuid}");

            $response->assertStatus(404);
        });

        it('returns 404 for non-existent tag', function () {
            $response = $this->actingAs($this->user)
                ->getJson('/api/tags/non-existent-uuid');

            $response->assertStatus(404);
        });

        it('requires authentication', function () {
            $tag = Tag::factory()->create();

            $response = $this->getJson("/api/tags/{$tag->uuid}");

            $response->assertStatus(401);
        });
    });

    describe('Update', function () {
        it('can update a tag', function () {
            $tag = Tag::factory()->create(['user_id' => $this->user->id]);

            $data = [
                'name' => 'Updated Tag',
                'icon' => 'updated-icon',
                'color' => '#00FF00',
            ];

            $response = $this->actingAs($this->user)
                ->putJson("/api/tags/{$tag->uuid}", $data);

            $response->assertStatus(200)
                ->assertJson([
                    'name' => 'Updated Tag',
                    'icon' => 'updated-icon',
                ]);

            $this->assertDatabaseHas('tags', [
                'uuid' => $tag->uuid,
                'name' => 'Updated Tag',
                'icon' => 'updated-icon',
            ]);
        });

        it('cannot update another user tag', function () {
            $otherUser = User::factory()->create();
            $tag = Tag::factory()->create(['user_id' => $otherUser->id]);

            $response = $this->actingAs($this->user)
                ->putJson("/api/tags/{$tag->uuid}", [
                    'name' => 'Updated Tag',
                    'icon' => 'updated-icon',
                ]);

            $response->assertStatus(404);
        });

        it('validates required fields', function () {
            $tag = Tag::factory()->create(['user_id' => $this->user->id]);

            $response = $this->actingAs($this->user)
                ->putJson("/api/tags/{$tag->uuid}", []);

            $response->assertStatus(422)
                ->assertJsonValidationErrors(['name', 'icon']);
        });

        it('requires authentication', function () {
            $tag = Tag::factory()->create();

            $response = $this->putJson("/api/tags/{$tag->uuid}", [
                'name' => 'Updated Tag',
            ]);

            $response->assertStatus(401);
        });
    });

    describe('Destroy', function () {
        it('can delete a tag', function () {
            $tag = Tag::factory()->create(['user_id' => $this->user->id]);

            $response = $this->actingAs($this->user)
                ->deleteJson("/api/tags/{$tag->uuid}");

            $response->assertStatus(204);

            $this->assertDatabaseMissing('tags', [
                'uuid' => $tag->uuid,
            ]);
        });

        it('cannot delete another user tag', function () {
            $otherUser = User::factory()->create();
            $tag = Tag::factory()->create(['user_id' => $otherUser->id]);

            $response = $this->actingAs($this->user)
                ->deleteJson("/api/tags/{$tag->uuid}");

            $response->assertStatus(404);

            $this->assertDatabaseHas('tags', [
                'uuid' => $tag->uuid,
            ]);
        });

        it('requires authentication', function () {
            $tag = Tag::factory()->create();

            $response = $this->deleteJson("/api/tags/{$tag->uuid}");

            $response->assertStatus(401);
        });
    });
});
