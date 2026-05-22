<?php

namespace App\Http\Middleware;

use Illuminate\Http\Request;
use Inertia\Middleware;
use Tighten\Ziggy\Ziggy;

class HandleInertiaRequests extends Middleware
{
    /**
     * The root template that is loaded on the first page visit.
     *
     * @var string
     */
    protected $rootView = 'app';

    /**
     * Determine the current asset version.
     *
     * @param Request $request
     * @return string|null
     */
    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    /**
     * Define the props that are shared by default across Inertia views.
     *
     * @param Request $request
     * @return array<string, mixed>
     */
    public function share(Request $request): array
    {
        // Fetch the configuration number from environment variables with a safe fallback
        $whatsappNumber = env('WHATSAPP_ADMIN_NUMBER', '(75) 90000-0000');

        // Remove non-numeric characters to format standard URL links
        $whatsappClean = preg_replace('/\D/', '', $whatsappNumber);

        return array_merge(parent::share($request), [
            'auth' => [
                'user' => $request->user(),
            ],
            // Flash messages for success, error, and WhatsApp URL payloads to be consumed by frontend components
            'flash' => [
                'success'      => fn() => $request->session()->get('success'),
                'error'        => fn() => $request->session()->get('error'),
                'whatsapp_url' => fn() => $request->session()->get('whatsapp_url'),
            ],
            // Global contact configurations accessible by all frontend layouts
            'contact' => [
                'whatsapp'     => $whatsappNumber,
                'whatsappLink' => '55' . $whatsappClean, // Appends Brazil's country code (55)
            ],
            'ziggy' => function () use ($request) {
                return array_merge((new Ziggy)->toArray(), [
                    'location' => $request->url(),
                ]);
            },
        ]);
    }
}
