<?php

use App\Models\Point;
use App\Models\User;

describe('PointController', function () {

    beforeEach(function () {
        $this->user = User::factory()->create();
    });

    describe('Index', function () {
        it('can list user points', function () {
            Point::factory()->count(3)->create(['user_id' => $this->user->id]);
            Point::factory()->create(); // Another user's point

            $response = $this->actingAs($this->user)
                ->getJson('/api/points');

            $response->assertStatus(200)
                ->assertJsonCount(3, 'data');
        });

        it('returns paginated results', function () {
            Point::factory()->count(20)->create(['user_id' => $this->user->id]);

            $response = $this->actingAs($this->user)
                ->getJson('/api/points');

            $response->assertStatus(200)
                ->assertJsonStructure([
                    'data',
                    'links',
                    'meta',
                ]);
        });

        it('requires authentication', function () {
            $response = $this->getJson('/api/points');

            $response->assertStatus(401);
        });
    });

    describe('Store', function () {
        it('can create a new point', function () {
            $data = [
                'name' => 'Test Point',
                'address' => 'Test Address',
                'location' => [
                    'latitude' => '55.755800',
                    'longitude' => '37.617300',
                ],
                'note' => 'Test note',
            ];

            $response = $this->actingAs($this->user)
                ->postJson('/api/points', $data);

            $response->assertStatus(200)
                ->assertJsonFragment([
                    'name' => 'Test Point',
                    'address' => 'Test Address',
                ]);

            $this->assertDatabaseHas('points', [
                'name' => 'Test Point',
                'address' => 'Test Address',
                'user_id' => $this->user->id,
            ]);
        });

        it('validates required fields', function () {
            $response = $this->actingAs($this->user)
                ->postJson('/api/points', []);

            $response->assertStatus(422)
                ->assertJsonValidationErrors(['name', 'location.longitude', 'location.latitude']);
        });

        it('requires authentication', function () {
            $response = $this->postJson('/api/points', [
                'name' => 'Test Point',
                'address' => 'Test Address',
            ]);

            $response->assertStatus(401);
        });
    });

    describe('Show', function () {
        it('can show a specific point', function () {
            $point = Point::factory()->create(['user_id' => $this->user->id]);

            $response = $this->actingAs($this->user)
                ->getJson("/api/points/{$point->uuid}");

            $response->assertStatus(200)
                ->assertJson([
                    'uuid' => $point->uuid,
                    'name' => $point->name,
                ]);
        });

        it('cannot show another user point', function () {
            $otherUser = User::factory()->create();
            $point = Point::factory()->create(['user_id' => $otherUser->id]);

            $response = $this->actingAs($this->user)
                ->getJson("/api/points/{$point->uuid}");

            $response->assertStatus(404);
        });

        it('returns 404 for non-existent point', function () {
            $response = $this->actingAs($this->user)
                ->getJson('/api/points/non-existent-uuid');

            $response->assertStatus(404);
        });

        it('requires authentication', function () {
            $point = Point::factory()->create();

            $response = $this->getJson("/api/points/{$point->uuid}");

            $response->assertStatus(401);
        });
    });

    describe('Update', function () {
        it('can update a point', function () {
            $point = Point::factory()->create(['user_id' => $this->user->id]);

            $data = [
                'name' => 'Updated Name',
                'address' => 'Updated Address',
                'location' => [
                    'latitude' => '55.755800',
                    'longitude' => '37.617300',
                ],
            ];

            $response = $this->actingAs($this->user)
                ->putJson("/api/points/{$point->uuid}", $data);

            $response->assertStatus(200)
                ->assertJson([
                    'name' => 'Updated Name',
                    'address' => 'Updated Address',
                ]);

            $this->assertDatabaseHas('points', [
                'uuid' => $point->uuid,
                'name' => 'Updated Name',
                'address' => 'Updated Address',
            ]);
        });

        it('cannot update another user point', function () {
            $otherUser = User::factory()->create();
            $point = Point::factory()->create(['user_id' => $otherUser->id]);

            $response = $this->actingAs($this->user)
                ->putJson("/api/points/{$point->uuid}", [
                    'name' => 'Updated Name',
                    'address' => 'Updated Address',
                    'location' => [
                        'latitude' => '55.755800',
                        'longitude' => '37.617300',
                    ],
                ]);

            $response->assertStatus(404);
        });

        it('validates required fields', function () {
            $point = Point::factory()->create(['user_id' => $this->user->id]);

            $response = $this->actingAs($this->user)
                ->putJson("/api/points/{$point->uuid}", []);

            $response->assertStatus(422)
                ->assertJsonValidationErrors(['name', 'location.longitude', 'location.latitude']);
        });

        it('requires authentication', function () {
            $point = Point::factory()->create();

            $response = $this->putJson("/api/points/{$point->uuid}", [
                'name' => 'Updated Name',
            ]);

            $response->assertStatus(401);
        });
    });

    describe('Destroy', function () {
        it('can delete a point', function () {
            $point = Point::factory()->create(['user_id' => $this->user->id]);

            $response = $this->actingAs($this->user)
                ->deleteJson("/api/points/{$point->uuid}");

            $response->assertStatus(204);

            $this->assertDatabaseMissing('points', [
                'uuid' => $point->uuid,
            ]);
        });

        it('cannot delete another user point', function () {
            $otherUser = User::factory()->create();
            $point = Point::factory()->create(['user_id' => $otherUser->id]);

            $response = $this->actingAs($this->user)
                ->deleteJson("/api/points/{$point->uuid}");

            $response->assertStatus(404);

            $this->assertDatabaseHas('points', [
                'uuid' => $point->uuid,
            ]);
        });

        it('requires authentication', function () {
            $point = Point::factory()->create();

            $response = $this->deleteJson("/api/points/{$point->uuid}");

            $response->assertStatus(401);
        });
    });

    describe('Tags', function () {
        it('can create a point with tags', function () {
            $tag1 = \App\Models\Tag::factory()->create(['user_id' => $this->user->id]);
            $tag2 = \App\Models\Tag::factory()->create(['user_id' => $this->user->id]);

            $data = [
                'name' => 'Point with Tags',
                'address' => 'Test Address',
                'location' => [
                    'latitude' => '55.755800',
                    'longitude' => '37.617300',
                ],
                'tags' => [$tag1->uuid, $tag2->uuid],
            ];

            $response = $this->actingAs($this->user)
                ->postJson('/api/points', $data);

            $response->assertStatus(200)
                ->assertJsonFragment(['name' => 'Point with Tags'])
                ->assertJsonCount(2, 'tags');

            $this->assertDatabaseHas('point_tag', [
                'tag_uuid' => $tag1->uuid,
            ]);
        });

        it('can update point tags', function () {
            $point = Point::factory()->create(['user_id' => $this->user->id]);
            $tag1 = \App\Models\Tag::factory()->create(['user_id' => $this->user->id]);
            $tag2 = \App\Models\Tag::factory()->create(['user_id' => $this->user->id]);
            $tag3 = \App\Models\Tag::factory()->create(['user_id' => $this->user->id]);

            $point->tags()->attach([$tag1->uuid, $tag2->uuid]);

            $data = [
                'name' => 'Updated Point',
                'address' => 'Updated Address',
                'location' => [
                    'latitude' => '55.755800',
                    'longitude' => '37.617300',
                ],
                'tags' => [$tag2->uuid, $tag3->uuid],
            ];

            $response = $this->actingAs($this->user)
                ->putJson("/api/points/{$point->uuid}", $data);

            $response->assertStatus(200)
                ->assertJsonCount(2, 'tags');

            $this->assertDatabaseHas('point_tag', [
                'point_uuid' => $point->uuid,
                'tag_uuid' => $tag2->uuid,
            ]);

            $this->assertDatabaseHas('point_tag', [
                'point_uuid' => $point->uuid,
                'tag_uuid' => $tag3->uuid,
            ]);

            $this->assertDatabaseMissing('point_tag', [
                'point_uuid' => $point->uuid,
                'tag_uuid' => $tag1->uuid,
            ]);
        });

        it('can show point with tags', function () {
            $point = Point::factory()->create(['user_id' => $this->user->id]);
            $tag = \App\Models\Tag::factory()->create(['user_id' => $this->user->id]);
            $point->tags()->attach($tag->uuid);

            $response = $this->actingAs($this->user)
                ->getJson("/api/points/{$point->uuid}");

            $response->assertStatus(200)
                ->assertJsonCount(1, 'tags')
                ->assertJsonFragment(['uuid' => $tag->uuid]);
        });

        it('validates tags exist', function () {
            $data = [
                'name' => 'Test Point',
                'address' => 'Test Address',
                'location' => [
                    'latitude' => '55.755800',
                    'longitude' => '37.617300',
                ],
                'tags' => ['non-existent-uuid'],
            ];

            $response = $this->actingAs($this->user)
                ->postJson('/api/points', $data);

            $response->assertStatus(422)
                ->assertJsonValidationErrors(['tags.0']);
        });

        it('can remove all tags from point', function () {
            $point = Point::factory()->create(['user_id' => $this->user->id]);
            $tag = \App\Models\Tag::factory()->create(['user_id' => $this->user->id]);
            $point->tags()->attach($tag->uuid);

            $data = [
                'name' => 'Updated Point',
                'address' => 'Updated Address',
                'location' => [
                    'latitude' => '55.755800',
                    'longitude' => '37.617300',
                ],
                'tags' => [],
            ];

            $response = $this->actingAs($this->user)
                ->putJson("/api/points/{$point->uuid}", $data);

            $response->assertStatus(200)
                ->assertJsonCount(0, 'tags');

            $this->assertDatabaseMissing('point_tag', [
                'point_uuid' => $point->uuid,
            ]);
        });
    });
});
