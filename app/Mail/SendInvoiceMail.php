<?php

namespace App\Mail;

use App\Models\InvoiceHeader;
use Dompdf\Dompdf;
use Dompdf\Options;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\View;

class SendInvoiceMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public InvoiceHeader $invoice;

    public function __construct(InvoiceHeader $invoice)
    {
        $this->invoice = $invoice;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "Invoice Tagihan Layanan Telkom - {$this->invoice->invoice_number}",
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.invoice',
            with: [
                'invoice' => $this->invoice,
            ],
        );
    }

    public function attachments(): array
    {
        // Render PDF directly for mail attachment
        $html = View::make('pdf.invoice', ['invoice' => $this->invoice])->render();

        $options = new Options();
        $options->set('isHtml5ParserEnabled', true);
        $options->set('isRemoteEnabled', true);

        $dompdf = new Dompdf($options);
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        $pdfBinary = $dompdf->output();
        $filename = "Telkom_Invoice_" . str_replace('/', '_', $this->invoice->invoice_number) . ".pdf";

        return [
            Attachment::fromData(fn () => $pdfBinary, $filename)
                ->withMime('application/pdf'),
        ];
    }
}
