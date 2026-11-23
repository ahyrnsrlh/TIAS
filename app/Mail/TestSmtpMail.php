<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class TestSmtpMail extends Mailable
{
    use Queueable, SerializesModels;

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Tes Koneksi SMTP - Telkom TIAS Witel Lampung',
        );
    }

    public function content(): Content
    {
        return new Content(
            htmlString: '<h3>Tes Koneksi SMTP Berhasil</h3><p>Email ini dikirim oleh sistem TIAS Telkom Witel Lampung untuk memverifikasi konfigurasi mail server.</p>',
        );
    }
}
