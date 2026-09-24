<?php

namespace Database\Seeders;

use App\Models\NotificationTemplate;
use Illuminate\Database\Seeder;

class NotificationTemplateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $templates = [
            ['name' => 'Agreement Expiry Reminder', 'event_type' => 'Expiry', 'subject' => 'Agreement expiry reminder', 'body' => 'An agreement is approaching its expiry date. Please review the agreement and take the required renewal or closure action.', 'channels' => ['in_system', 'email']],
            ['name' => 'Outstanding Arrears Alert', 'event_type' => 'Arrears', 'subject' => 'Outstanding payment arrears', 'body' => 'An account has an outstanding balance that requires follow-up. Please review the arrears record and take the appropriate action.', 'channels' => ['in_system', 'email', 'sms']],
            ['name' => 'Vehicle Service Due', 'event_type' => 'Servicing', 'subject' => 'Vehicle service is due', 'body' => 'A university vehicle is due for scheduled servicing. Please arrange the service and update the vehicle record.', 'channels' => ['in_system', 'email']],
            ['name' => 'Equipment Calibration Due', 'event_type' => 'Calibration', 'subject' => 'Equipment calibration is due', 'body' => 'Laboratory equipment is approaching its calibration date. Please schedule calibration and update the equipment record.', 'channels' => ['in_system', 'email']],
            ['name' => 'Property Inspection Due', 'event_type' => 'Inspection', 'subject' => 'Property inspection is due', 'body' => 'A university property is due for inspection. Please assign an inspector and record the inspection findings.', 'channels' => ['in_system', 'email']],
            ['name' => 'Maintenance Work Update', 'event_type' => 'Maintenance', 'subject' => 'Maintenance work update', 'body' => 'A maintenance request has been updated. Please review the current work status and any required follow-up actions.', 'channels' => ['in_system']],
            ['name' => 'Approval Decision Notice', 'event_type' => 'Approval', 'subject' => 'Approval decision recorded', 'body' => 'A decision has been recorded for an approval request. Please review the decision and any accompanying comments.', 'channels' => ['in_system', 'email']],
            ['name' => 'General UPAMS Notice', 'event_type' => 'General', 'subject' => 'UPAMS notification', 'body' => 'A new UPAMS notice is available. Please review the notification details and take action where required.', 'channels' => ['in_system']],
        ];

        foreach ($templates as $template) {
            NotificationTemplate::query()->firstOrCreate(
                ['name' => $template['name']],
                $template + ['is_active' => true],
            );
        }
    }
}
