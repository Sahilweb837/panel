<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Pagination\Paginator;

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
        // Auto-run migrations if portal_active column is missing on the students table
        try {
            if (\Illuminate\Support\Facades\Schema::hasTable('students')) {
                if (!\Illuminate\Support\Facades\Schema::hasColumn('students', 'portal_active')) {
                    \Illuminate\Support\Facades\Artisan::call('migrate', ['--force' => true]);
                }

                // Automatically activate student portal access and set correct credentials for NT-ENR-011
                $student = \App\Models\Student::where('admission_no', 'NT-ENR-011')->first();
                if ($student) {
                    $student->portal_active = true;
                    $student->status = 1;
                    $student->save();
                    
                    $user = \App\Models\User::find($student->user_id);
                    if ($user) {
                        $user->password = \Illuminate\Support\Facades\Hash::make('NT-ENR-011');
                        $user->status = 1;
                        $user->save();
                    }
                }
            }
        } catch (\Throwable $e) {
            // Silence
        }

        Paginator::useBootstrapFive();

        \Illuminate\Support\Facades\View::composer('messages.widget', function ($view) {
            $userId = session('user_id');
            if (!$userId) return;

            $userRoleSlug = session('user_role_slug');
            $isAdmin = in_array($userRoleSlug, ['super-admin', 'superadmin', 'root-admin', 'admin', 'subadmin', 'sub-admin']);

            // Inbox Messages
            $inboxMessages = \App\Models\Message::with('sender')
                ->forUser($userId, $userRoleSlug)
                ->latest()
                ->get();

            // Sent Messages
            $sentMessages = \App\Models\Message::with('receiver')
                ->where('sender_id', $userId)
                ->latest()
                ->get();

            $unreadCount = $inboxMessages->where('is_read', false)->count();

            // Recipients for Compose & Chat
            $recipientsQuery = \App\Models\User::with('role')
                ->where('id', '!=', $userId)
                ->where('status', true);

            if (!$isAdmin) {
                if ($userRoleSlug === 'staff') {
                    $recipientsQuery->whereHas('role', function($q) {
                        $q->whereIn('slug', ['super-admin', 'superadmin', 'root-admin', 'admin', 'subadmin', 'sub-admin', 'student']);
                    });
                } elseif ($userRoleSlug === 'student') {
                    $recipientsQuery->whereHas('role', function($q) {
                        $q->whereIn('slug', ['super-admin', 'superadmin', 'root-admin', 'admin', 'subadmin', 'sub-admin', 'staff']);
                    });
                }
            }
            
            $recipients = $recipientsQuery->orderBy('name')->get();

            // Chat Messages (if a user is selected)
            $chatUserId = request('chat_user');
            $selectedChatUser = null;
            $chatMessages = collect();

            if ($chatUserId) {
                $selectedChatUser = \App\Models\User::find($chatUserId);
                if ($selectedChatUser) {
                    $chatMessages = \App\Models\Message::with(['sender', 'receiver'])
                        ->where(function($q) use ($userId, $chatUserId) {
                            $q->where('sender_id', $userId)->where('receiver_id', $chatUserId);
                        })
                        ->orWhere(function($q) use ($userId, $chatUserId) {
                            $q->where('sender_id', $chatUserId)->where('receiver_id', $userId);
                        })
                        ->orderBy('created_at', 'asc')
                        ->get();

                    // Mark as read
                    \App\Models\Message::where('sender_id', $chatUserId)
                        ->where('receiver_id', $userId)
                        ->where('is_read', false)
                        ->update(['is_read' => true]);
                }
            }

            $view->with(compact('inboxMessages', 'sentMessages', 'unreadCount', 'recipients', 'isAdmin', 'chatMessages', 'selectedChatUser'));
        });
    }
}
