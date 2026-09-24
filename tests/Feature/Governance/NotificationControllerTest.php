<?php

namespace Tests\Feature\Governance;

use App\Models\Notification;
use App\Models\NotificationTemplate;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class NotificationControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_administrator_can_see_active_notification_templates(): void
    {
        $administrator = User::factory()->create();
        NotificationTemplate::factory()->create([
            'name' => 'Agreement Expiry Reminder',
            'event_type' => 'Expiry',
            'subject' => 'Agreement expiry reminder',
            'channels' => ['in_system', 'email'],
            'is_active' => true,
        ]);
        NotificationTemplate::factory()->create([
            'name' => 'Inactive Internal Template',
            'is_active' => false,
        ]);

        $response = $this->actingAs($administrator)->get(route('notifications.index'));

        $response
            ->assertSee('Available notification templates')
            ->assertSee('Agreement Expiry Reminder')
            ->assertSee('Agreement expiry reminder')
            ->assertSee('In-system')
            ->assertSee('Email-ready')
            ->assertDontSee('Inactive Internal Template');
    }

    public function test_administrator_can_see_sent_notifications(): void
    {
        $administrator = User::factory()->create(['name' => 'UPAMS Administrator']);
        Notification::factory()
            ->for($administrator, 'creator')
            ->create([
                'reference' => 'NTF-AVAILABLE1',
                'campus_id' => null,
                'target_role' => 'Finance Officer',
                'title' => 'Outstanding arrears require attention',
                'message' => 'Please review the outstanding balance and begin follow-up.',
            ]);

        $response = $this->actingAs($administrator)->get(route('notifications.index'));

        $response
            ->assertSee('Sent notification history')
            ->assertSee('NTF-AVAILABLE1')
            ->assertSee('Outstanding arrears require attention')
            ->assertSee('Finance Officer')
            ->assertSee('UPAMS Administrator');
    }

    public function test_notification_centre_displays_clear_empty_states(): void
    {
        $administrator = User::factory()->create();

        $response = $this->actingAs($administrator)->get(route('notifications.index'));

        $response
            ->assertSee('No notification templates are available.')
            ->assertSee('No notifications sent.');
    }
}
