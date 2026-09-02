<?php

namespace App\Http\Controllers\Governance;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreNotificationRequest;
use App\Http\Requests\StoreNotificationTemplateRequest;
use App\Models\Campus;
use App\Models\Notification;
use App\Models\NotificationTemplate;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class NotificationController extends Controller
{
    public function index(Request $request): View
    {
        return view('admin.notifications.index', ['notifications' => Notification::query()->with(['recipient', 'campus', 'creator'])->latest('sent_at')->paginate(15)->withQueryString(), 'templates' => NotificationTemplate::query()->orderBy('name')->get(), 'failedCount' => Notification::query()->where('status', 'Failed')->count()]);
    }

    public function create(): View
    {
        return view('admin.notifications.form', ['templates' => NotificationTemplate::query()->where('is_active', true)->orderBy('name')->get(), 'users' => User::query()->where('is_active', true)->orderBy('name')->get(), 'campuses' => Campus::query()->orderBy('name')->get(), 'roles' => Role::query()->orderBy('name')->get()]);
    }

    public function store(StoreNotificationRequest $request): RedirectResponse
    {
        $channels = $request->validated('channels');
        $channelStatus = collect($channels)->mapWithKeys(fn (string $channel): array => [$channel => $channel === 'in_system' ? 'Sent' : 'Queued for future connector'])->all();
        Notification::query()->create($request->validated() + ['reference' => 'NTF-'.str()->upper(str()->random(8)), 'channel_status' => $channelStatus, 'status' => 'Sent', 'sent_at' => now(), 'created_by' => $request->user()->id, 'updated_by' => $request->user()->id]);

        return redirect()->route('notifications.index')->with('success', 'In-system notification sent. External channels are preserved for later integration.');
    }

    public function storeTemplate(StoreNotificationTemplateRequest $request): RedirectResponse
    {
        NotificationTemplate::query()->create($request->validated() + ['is_active' => $request->boolean('is_active', true), 'created_by' => $request->user()->id, 'updated_by' => $request->user()->id]);

        return back()->with('success', 'Notification template created.');
    }

    public function resend(Request $request, Notification $notification): RedirectResponse
    {
        $notification->update(['status' => 'Sent', 'failure_message' => null, 'sent_at' => now(), 'updated_by' => $request->user()->id, 'channel_status' => collect($notification->channels)->mapWithKeys(fn (string $channel): array => [$channel => $channel === 'in_system' ? 'Sent' : 'Queued for future connector'])->all()]);

        return back()->with('success', 'Notification resent.');
    }
}
