<?php

namespace Tests\Feature;

use App\Models\MediationSession;
use App\Models\Participant;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RoomsPageTest extends TestCase
{
    use RefreshDatabase;

    public function test_logged_in_user_sees_their_rooms(): void
    {
        $user = User::factory()->create([
            'is_premium' => true,
        ]);

        $s1 = MediationSession::create([
            'code' => 'AAAAAA',
            'status' => MediationSession::STATUS_WAITING,
            'conflict_type' => 'other',
        ]);
        Participant::create([
            'session_id' => $s1->id,
            'user_id' => $user->id,
            'name' => 'Premium User',
            'role' => 'user1',
        ]);

        $s2 = MediationSession::create([
            'code' => 'BBBBBB',
            'status' => MediationSession::STATUS_USER1_TURN,
            'conflict_type' => 'family',
        ]);
        Participant::create([
            'session_id' => $s2->id,
            'user_id' => $user->id,
            'name' => 'Premium User',
            'role' => 'user2',
        ]);

        $this->actingAs($user)
            ->get(route('rooms', absolute: false))
            ->assertOk()
            ->assertSee('AAAAAA')
            ->assertSee('BBBBBB');
    }

    public function test_guest_sees_recent_browser_rooms_when_session_has_mapping(): void
    {
        $session = MediationSession::create([
            'code' => 'CCCCC1',
            'status' => MediationSession::STATUS_WAITING,
            'conflict_type' => 'roommate',
        ]);

        $participant = Participant::create([
            'session_id' => $session->id,
            'user_id' => null,
            'name' => 'Guest',
            'role' => 'user1',
        ]);

        $this->withSession([
            'participants' => [
                'CCCCC1' => $participant->id,
            ],
        ])->get(route('rooms', absolute: false))
            ->assertOk()
            ->assertSee('CCCCC1')
            ->assertSee('Guest');
    }
}

