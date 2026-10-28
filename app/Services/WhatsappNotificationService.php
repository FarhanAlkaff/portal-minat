<?php

namespace App\Services;

use App\Models\InformationSetting;
use App\Models\RegistrationNote;
use App\Models\StudentRegistration;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WhatsappNotificationService
{
    /**
     * Notify parent that observation is scheduled.
     * Returns array: ['sent' => bool, 'wa_link' => string, 'driver' => string, 'phone' => string|null, 'error' => ?string]
     */
    public function notifyObservationScheduled(StudentRegistration $reg, int $adminId = null): array
    {
        $phone = $this->pickPhone($reg);
        $message = $this->composeObservationMessage($reg);
        $waLink = $phone ? $this->buildWaLink($phone, $message) : null;

        $driver = InformationSetting::get('wa_driver', 'link');
        $result = ['sent' => false, 'wa_link' => $waLink, 'driver' => $driver, 'phone' => $phone, 'error' => null];

        if (!$phone) {
            $result['error'] = 'Nomor HP orang tua kosong. Tidak dapat mengirim WA.';
            $this->logNote($reg, $adminId, "Notifikasi WA gagal: {$result['error']}");
            return $result;
        }

        if ($driver === 'fonnte') {
            $token = InformationSetting::get('wa_fonnte_token');
            if (!$token) {
                $result['error'] = 'Fonnte token belum diset. Fallback ke wa.me link.';
            } else {
                try {
                    $resp = Http::timeout(15)->withHeaders(['Authorization' => $token])
                        ->asForm()->post('https://api.fonnte.com/send', [
                            'target' => $phone,
                            'message' => $message,
                        ]);
                    if ($resp->successful()) {
                        $result['sent'] = true;
                    } else {
                        $result['error'] = 'Fonnte HTTP ' . $resp->status();
                    }
                } catch (\Throwable $e) {
                    $result['error'] = 'Fonnte exception: ' . $e->getMessage();
                    Log::warning('Fonnte send failed', ['e' => $e->getMessage()]);
                }
            }
        }

        $status = $result['sent'] ? 'terkirim otomatis (Fonnte)' : "siap dikirim manual via wa.me";
        $this->logNote(
            $reg, $adminId,
            "Notifikasi WA panggilan observasi ke {$phone}: {$status}." . ($waLink ? " Link: {$waLink}" : '')
        );

        return $result;
    }

    public function composeObservationMessage(StudentRegistration $reg): string
    {
        $reg->loadMissing(['grade', 'academicYear']);
        $date = $reg->observation_date ? \Carbon\Carbon::parse($reg->observation_date)->translatedFormat('l, d F Y') : '-';
        $time = $reg->observation_time ? substr($reg->observation_time, 0, 5) . ' WIB' : '-';
        $loc = $reg->observation_location ?? '-';
        $room = $reg->observation_room ? "\nRuang: {$reg->observation_room}" : '';
        return "*PORMIN — Al-Azhar Cairo Palembang*\n\n"
            . "Assalamu'alaikum Bapak/Ibu Orang Tua dari *{$reg->full_name}*,\n\n"
            . "Kami mengundang Bapak/Ibu untuk mengikuti observasi calon murid dengan detail berikut:\n\n"
            . "No. Pendaftaran: *{$reg->registration_number}*\n"
            . "Jenjang: {$reg->grade->code} · TA {$reg->academicYear->name}\n"
            . "Tanggal: *{$date}*\n"
            . "Waktu: *{$time}*\n"
            . "Lokasi: {$loc}{$room}\n\n"
            . "Mohon hadir tepat waktu. Terima kasih.\n"
            . "— Panitia PMB Al-Azhar Cairo Palembang";
    }

    public function buildWaLink(string $phone, string $message): string
    {
        return 'https://wa.me/' . $this->normalizePhone($phone) . '?text=' . rawurlencode($message);
    }

    public function normalizePhone(string $phone): string
    {
        $cc = InformationSetting::get('wa_country_code', '62');
        $p = preg_replace('/\D+/', '', $phone);
        if (str_starts_with($p, '0')) {
            $p = $cc . substr($p, 1);
        } elseif (!str_starts_with($p, $cc)) {
            $p = $cc . $p;
        }
        return $p;
    }

    private function pickPhone(StudentRegistration $reg): ?string
    {
        return $reg->mother_phone ?: ($reg->father_phone ?: $reg->phone);
    }

    private function logNote(StudentRegistration $reg, ?int $adminId, string $note): void
    {
        RegistrationNote::create([
            'registration_id' => $reg->id,
            'admin_id' => $adminId,
            'note' => $note,
            'note_type' => 'Notifikasi WA',
        ]);
    }
}
