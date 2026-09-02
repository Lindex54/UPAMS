<?php

namespace App\Providers;

use App\Models\Approval;
use App\Models\Beneficiary;
use App\Models\Invoice;
use App\Models\Notification;
use App\Models\NotificationTemplate;
use App\Models\Payment;
use App\Models\User;
use App\Models\UtilityBilling;
use App\Models\UtilityMeter;
use App\Models\UtilityType;
use App\Observers\AuditableObserver;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        foreach ([User::class, Beneficiary::class, Invoice::class, Payment::class, UtilityType::class, UtilityMeter::class, UtilityBilling::class, Approval::class, NotificationTemplate::class, Notification::class] as $auditableModel) {
            $auditableModel::observe(AuditableObserver::class);
        }
    }
}
