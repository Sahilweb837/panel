<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\User;
use Carbon\Carbon;

class StaffPingTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function unauthenticated_user_receives_401()
    {
        $response = $this->postJson('/api/staff/ping');
        $response->assertStatus(401);
        $response->assertJson(['success' => false]);
    }

    /** @test */
    public function authenticated_user_updates_last_seen_and_receives_online_staff()
    {
        // Create a staff user
        $user = User::factory()->create([
            'role_id' => 2, // assuming role 2 is staff
        ]);

        $this->actingAs($user);

        $response = $this->postJson('/api/staff/ping');
        $response->assertStatus(200);
        $response->assertJson(['success' => true, 'current_user_id' => $user->id]);

        // Verify last_seen_at updated
        $this->assertNotNull($user->fresh()->last_seen_at);
        $this->assertTrue(Carbon::parse($user->fresh()->last_seen_at)->greaterThanOrEqualTo(Carbon::now()->subMinutes(1)));
    }
}
