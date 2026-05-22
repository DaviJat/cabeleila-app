<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class WhatsAppService
{
    /**
     * Dispatch the transient passwordless OTP login token via automated HTTP API.
     * This must remain automated since the guest user cannot send a message to themselves.
     *
     * @param string $phone
     * @param string $name
     * @param string $otp
     * @return void
     */
    public function sendOtp(string $phone, string $name, string $otp): void
    {
        $cleanPhone = '55' . preg_replace('/\D/', '', $phone);
        $adminNumber = env('WHATSAPP_ADMIN_NUMBER', '(75) 99999-0000');

        $message = "✂️ *Cabeleila* \n\nOlá, {$name}! Seu código de acesso é: *{$otp}*\n\nEste código expira em 10 minutos.\n\n_Se precisar de ajuda ou quiser alterar algo urgente, fale diretamente com a gente: {$adminNumber}_";

        $this->dispatchMessage($cleanPhone, $message);
    }

    /**
     * Generate a deep-linked WhatsApp Web URL to notify the client about status changes.
     *
     * @param mixed $client
     * @param mixed $appointment
     * @param string $status
     * @return string|null
     */
    public function getStatusNotificationUrl($client, $appointment, string $status): ?string
    {
        $cleanPhone = '55' . preg_replace('/\D/', '', $client->phone);
        $date = Carbon::parse($appointment->availability->date)->format('d/m/Y');
        $time = substr($appointment->availability->hour, 0, 5);

        $message = "";

        if ($status === 'confirmed') {
            $message = "✅ *Agendamento Confirmado!*\n\nOlá, {$client->full_name}! Seu horário para o dia *{$date}* às *{$time}* foi confirmado pela nossa equipe. Te esperamos ansiosamente!";
        } elseif ($status === 'canceled') {
            $message = "❌ *Agendamento Cancelado*\n\nOlá, {$client->full_name}. Informamos que seu agendamento do dia *{$date}* às *{$time}* foi cancelado. Se desejar, acesse nosso site para reagendar uma nova data.";
        }

        if ($message) {
            return $this->buildWhatsAppUrl($cleanPhone, $message);
        }

        return null;
    }

    /**
     * Generate a deep-linked WhatsApp Web URL to notify the client when an admin creates/modifies a slot.
     *
     * @param mixed $client
     * @param mixed $appointment
     * @param bool $isNew
     * @return string
     */
    public function getAdminActionNotificationUrl($client, $appointment, bool $isNew = false): string
    {
        $cleanPhone = '55' . preg_replace('/\D/', '', $client->phone);
        $date = Carbon::parse($appointment->availability->date)->format('d/m/Y');
        $time = substr($appointment->availability->hour, 0, 5);

        // Map collection entries to group selected relational service string tokens
        $services = $appointment->services->pluck('name')->join(', ');

        $action = $isNew ? "foi agendado" : "foi remarcado";
        $intro = $isNew ? "🎉 *Novo Agendamento!*" : "🔄 *Agendamento Alterado*";

        $message = "{$intro}\n\nOlá, {$client->full_name}! Um horário {$action} para você no salão:\n\n📅 *Data:* {$date}\n⏰ *Hora:* {$time}\n✂️ *Serviços:* {$services}\n\nPara gerenciar seus horários, acesse nosso site.";

        return $this->buildWhatsAppUrl($cleanPhone, $message);
    }

    /**
     * Construct the standard WhatsApp Web redirect string.
     *
     * @param string $phone
     * @param string $message
     * @return string
     */
    private function buildWhatsAppUrl(string $phone, string $message): string
    {
        return "https://web.whatsapp.com/send?phone={$phone}&text=" . urlencode($message);
    }

    /**
     * Execute a centralized outbound synchronous HTTP wrapper call targeting external API microservices (for OTP).
     *
     * @param string $phone
     * @param string $message
     * @return void
     */
    private function dispatchMessage(string $phone, string $message): void
    {
        try {
            $response = Http::withToken(env('WHATSAPP_API_TOKEN'))
                ->post(env('WHATSAPP_API_URL'), [
                    'phone'   => $phone,
                    'message' => $message,
                ]);

            if ($response->failed()) {
                Log::error("WhatsApp delivery failed for target phone [{$phone}]: " . $response->body());
            }
        } catch (\Exception $e) {
            Log::error("WhatsApp integration service exception occurred: " . $e->getMessage());
        }
    }
}
