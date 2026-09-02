<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('approvals', function (Blueprint $table) {
            $table->id();
            $table->string('reference')->unique();
            $table->foreignId('campus_id')->nullable()->constrained()->nullOnDelete();
            $table->string('approval_type');
            $table->string('subject_reference');
            $table->string('title');
            $table->text('request_details')->nullable();
            $table->string('status')->default('Pending');
            $table->foreignId('reviewer_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('decision_at')->nullable();
            $table->text('decision_comments')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index(['status', 'approval_type']);
            $table->index(['campus_id', 'status']);
        });

        Schema::create('notification_templates', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->string('event_type');
            $table->string('subject');
            $table->text('body');
            $table->json('channels');
            $table->boolean('is_active')->default(true);
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        Schema::create('system_notifications', function (Blueprint $table) {
            $table->id();
            $table->string('reference')->unique();
            $table->foreignId('notification_template_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('recipient_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('campus_id')->nullable()->constrained()->nullOnDelete();
            $table->string('target_role')->nullable();
            $table->string('event_type');
            $table->string('title');
            $table->text('message');
            $table->json('channels');
            $table->json('channel_status')->nullable();
            $table->string('status')->default('Sent');
            $table->text('failure_message')->nullable();
            $table->timestamp('sent_at')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index(['status', 'event_type']);
            $table->index(['campus_id', 'sent_at']);
            $table->index(['recipient_user_id', 'sent_at']);
        });

        Schema::create('audit_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('user_name')->nullable();
            $table->foreignId('campus_id')->nullable()->constrained()->nullOnDelete();
            $table->string('action');
            $table->string('auditable_type')->nullable();
            $table->unsignedBigInteger('auditable_id')->nullable();
            $table->text('description');
            $table->json('old_values')->nullable();
            $table->json('new_values')->nullable();
            $table->ipAddress('ip_address')->nullable();
            $table->text('user_agent')->nullable();
            $table->timestamp('created_at')->useCurrent();

            $table->index(['action', 'created_at']);
            $table->index(['auditable_type', 'auditable_id']);
            $table->index(['campus_id', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('audit_logs');
        Schema::dropIfExists('system_notifications');
        Schema::dropIfExists('notification_templates');
        Schema::dropIfExists('approvals');
    }
};
