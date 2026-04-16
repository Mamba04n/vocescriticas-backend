<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Post;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ApiIntegrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_register_and_login()
    {
        $response = $this->postJson('/api/register', [
            'name' => 'Miguel',
            'email' => 'miguel@ejemplo.com',
            'password' => 'password123',
            'role' => 'student'
        ]);

        $response->assertStatus(201)->assertJsonStructure(['token', 'user']);

        $loginResponse = $this->postJson('/api/login', [
            'email' => 'miguel@ejemplo.com',
            'password' => 'password123'
        ]);

        $loginResponse->assertStatus(200)->assertJsonStructure(['token']);
    }

    public function test_user_can_update_profile()
    {
        $user = User::factory()->create();
        Storage::fake('public');

        $response = $this->actingAs($user)->postJson('/api/user/profile', [
            'bio' => 'Estudiante de Arquitectura de Software',
            'avatar' => UploadedFile::fake()->image('avatar.jpg'),
        ]);

        $response->assertStatus(200);
        $this->assertEquals('Estudiante de Arquitectura de Software', $user->fresh()->bio);
        $this->assertNotNull($user->fresh()->avatar_url);
    }

    public function test_user_can_create_post_with_document()
    {
        $user = User::factory()->create();
        Storage::fake('public');

        $file = UploadedFile::fake()->create('investigacion.pdf', 100, 'application/pdf');

        $response = $this->actingAs($user)->postJson('/api/posts', [
            'body' => 'Mi primera investigación subida a la red.',
            'file' => $file
        ]);

        $response->assertStatus(201);
        $this->assertDatabaseHas('posts', [
            'user_id' => $user->id,
            'file_name' => 'investigacion.pdf'
        ]);
    }

    public function test_user_can_like_and_unlike_post()
    {
        $user = User::factory()->create();
        $post = Post::factory()->create(); // Requires logic or just manual create

        $post = Post::create(['user_id' => $user->id, 'body' => 'Post de prueba']);

        // Damos like
        $response1 = $this->actingAs($user)->postJson("/api/posts/{$post->id}/like");
        $response1->assertStatus(200)->assertJson(['liked' => true]);

        // Quitamos like mandando de nuevo
        $response2 = $this->actingAs($user)->postJson("/api/posts/{$post->id}/like");
        $response2->assertStatus(200)->assertJson(['liked' => false]);
    }

    public function test_users_can_send_and_accept_requests()
    {
        $follower = User::factory()->create();
        $followed = User::factory()->create();

        // Enviar solicitud
        $response = $this->actingAs($follower)->postJson("/api/users/{$followed->id}/request");
        $response->assertStatus(200);
        $this->assertDatabaseHas('follows', [
            'follower_id' => $follower->id,
            'followed_id' => $followed->id,
            'status' => 'pending'
        ]);

        // Aceptar solicitud
        $response2 = $this->actingAs($followed)->postJson("/api/users/{$follower->id}/accept");
        $response2->assertStatus(200);
        $this->assertDatabaseHas('follows', [
            'follower_id' => $follower->id,
            'followed_id' => $followed->id,
            'status' => 'accepted'
        ]);
    }

    public function test_user_can_create_group()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->postJson('/api/groups', [
            'name' => 'Grupo de Programación',
            'description' => 'Debates sobre código',
        ]);

        $response->assertStatus(201);
        $this->assertDatabaseHas('groups', ['name' => 'Grupo de Programación']);
        $this->assertDatabaseHas('group_user', [
            'user_id' => $user->id,
            'role' => 'admin'
        ]);
    }
}
