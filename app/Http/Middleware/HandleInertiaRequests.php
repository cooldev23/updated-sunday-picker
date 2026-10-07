<?php

namespace App\Http\Middleware;

use Illuminate\Http\Request;
use Inertia\Middleware;

class HandleInertiaRequests extends Middleware
{
    /**
     * The root template that's loaded on the first page visit.
     *
     * @see https://inertiajs.com/server-side-setup#root-template
     *
     * @var string
     */
    protected $rootView = 'app';

    /**
     * Determines the current asset version.
     *
     * @see https://inertiajs.com/asset-versioning
     */
    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    /**
     * Define the props that are shared by default.
     *
     * @see https://inertiajs.com/shared-data
     *
     * @return array<string, mixed>
     */
    public function share(Request $request): array
    {
        return [
            ...parent::share($request),
            'name' => config('app.name'),
            'auth' => [
                'user' => $request->user()
                ? $request->user()->only('id', 'fullName', 'display_name', 'email', 'timezone')
                : null,
                'can' => $request->user()?->loadMissing('roles')
                    ->roles->flatMap(function ($role) {
                        return $role->permissions;
                    })->map(function ($permission) {
                        return [
                            str_replace('-', '_', $permission->name) => auth()->user()->can($permission->name),
                        ];
                    })->collapse()->all(),
                'roles' => $request->user()?->loadMissing('roles')
                    ->roles->map(function ($role) {
                        return [
                            strtolower(str_replace(' ', '_', $role->name)) => strtolower(str_replace(' ', '_', $role->name)),
                        ];
                    })->collapse()->all(),
            ],
            'unread_notifications_count' => $request->user ? $request->user()->unreadNotifications()->count() : 0,
            'notifications' => $request->user ? $request->user()->unreadNotifications : [],
            'sidebarOpen' => ! $request->hasCookie('sidebar_state') || $request->cookie('sidebar_state') === 'true',
            'currentWeek' => app('currentWeek'),
            'flash' => [
                'message' => $request->session()->get('message'),
                'success' => $request->session()->get('success'),
                'error' => $request->session()->get('error')
            ]
        ];
    }
}
