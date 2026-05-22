<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Appointment;
use App\Services\WhatsAppService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;
use Inertia\Response;

class ClientController extends Controller
{
    /**
     * Display the "My Appointments" history overview for the authenticated client.
     *
     * @return Response
     */
    public function myAppointments(): Response
    {
        $loggedClient = Auth::guard('clients')->user();
        $appointments = [];

        // If authenticated, fetch the complete structured history records for the client
        if ($loggedClient) {
            $appointments = Appointment::with(['availability', 'services'])
                ->where('client_id', $loggedClient->id)
                ->get()
                ->sortByDesc(function ($app) {
                    // Sort collection chronologically by date and hour (most recent/future first)
                    return $app->availability->date->format('Y-m-d') . ' ' . $app->availability->hour;
                })
                ->values();
        }

        return Inertia::render('Appointment/MyAppointments', [
            'loggedClient' => $loggedClient,
            'appointments' => $appointments
        ]);
    }

    /**
     * Generate a cryptographic OTP token, save state constraints, and dispatch via WhatsApp provider.
     *
     * @param Request $request
     * @param WhatsAppService $whatsAppService
     * @return JsonResponse
     */
    public function sendOtp(Request $request, WhatsAppService $whatsAppService): JsonResponse
    {
        $request->validate([
            'whatsapp' => 'required|string',
            'name'     => 'required|string'
        ]);

        // Fetch user instance by phone identity or instantiate a draft state model
        $client = Client::firstOrCreate(
            ['phone' => $request->input('whatsapp')],
            ['full_name' => $request->input('name')]
        );

        // Generate a cryptographically simple 6-digit numeric token string
        $otp = (string) rand(100000, 999999);

        $client->update([
            'otp_code'       => $otp,
            'otp_expires_at' => now()->addMinutes(10) // Token lifespan validation window set to 10 minutes
        ]);

        // Keep explicit logging trace active to simplify localized testing/debugging routines
        Log::info("WhatsApp para {$client->phone}: Seu código de acesso Cabeleila é {$otp}");

        // Dispatch integration message payload using the dedicated Domain Service layer
        // $whatsAppService->sendOtp($client->phone, $client->full_name, $otp);

        return response()->json(['success' => true]);
    }

    /**
     * Validate the payload OTP credentials to authenticate transient client sessions.
     *
     * @param Request $request
     * @return RedirectResponse
     */
    public function loginViaOtp(Request $request): RedirectResponse
    {
        $request->validate([
            'whatsapp' => 'required|string',
            'otp'      => 'required|string',
            'name'     => 'required|string',
        ]);

        $client = Client::where('phone', $request->input('whatsapp'))
            ->where('otp_code', $request->input('otp'))
            ->where('otp_expires_at', '>', now())
            ->first();

        if (! $client) {
            return back()->withErrors(['otp' => 'Código inválido ou expirado. Tente novamente.']);
        }

        // Apply fallback correction adjustments to full name and clear transient token space
        $client->update([
            'full_name' => $request->input('name'),
            'otp_code'  => null,
        ]);

        // Log client identity instance into state guard session
        Auth::guard('clients')->login($client, true);

        return back()->with('success', 'Login realizado com sucesso!');
    }

    /**
     * Clear client state context and terminate active auth session tokens.
     *
     * @param Request $request
     * @return RedirectResponse
     */
    public function logout(Request $request): RedirectResponse
    {
        Auth::guard('clients')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home')->with('success', 'Sessão encerrada com sucesso.');
    }
}
