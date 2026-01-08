<?php

namespace Tests\Feature;

use App\Models\MediationSession;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RoomLimitTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_can_only_have_one_active_room(): void
    {
        $this->from('/')->post(route('session.store', absolute: false), [
            'name' => 'Guest One',
            'conflict_type' => 'other',
        ])->assertRedirect();

        $this->assertSame(1, MediationSession::count());

        $this->from('/')->post(route('session.store', absolute: false), [
            'name' => 'Guest One',
            'conflict_type' => 'other',
        ])->assertSessionHasErrors('room_limit');

        $this->assertSame(1, MediationSession::count());
    }

    public function test_premium_user_can_have_multiple_active_rooms(): void
    {
        $user = User::factory()->create([
            'is_premium' => true,
        ]);

        $this->actingAs($user)->from('/')->post(route('session.store', absolute: false), [
            'name' => 'Premium User',
            'conflict_type' => 'other',
        ])->assertRedirect();

        $this->actingAs($user)->from('/')->post(route('session.store', absolute: false), [
            'name' => 'Premium User',
            'conflict_type' => 'other',
        ])->assertRedirect();

        $this->assertSame(2, MediationSession::count());
    }
}

