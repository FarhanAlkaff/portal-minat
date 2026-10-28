<?php

namespace App\Services;

use App\Models\StudentRegistration;
use Barryvdh\DomPDF\Facade\Pdf;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class PdfService
{
    public function proof(StudentRegistration $reg)
    {
        $qr = base64_encode(
            QrCode::format('svg')->size(220)->margin(1)
                ->generate(url("/verify/{$reg->verification_token}"))
        );
        return Pdf::loadView('pdf.proof', ['reg' => $reg->load(['academicYear', 'grade', 'campus', 'category']), 'qr' => $qr])
            ->setPaper('a4');
    }

    public function observationLetter(StudentRegistration $reg)
    {
        $qr = base64_encode(
            QrCode::format('svg')->size(180)->margin(1)
                ->generate(url("/verify/{$reg->verification_token}"))
        );
        return Pdf::loadView('pdf.observation', ['reg' => $reg->load(['academicYear', 'grade', 'campus']), 'qr' => $qr])
            ->setPaper('a4');
    }
}
