<?php

namespace App\Http\Controllers;

use App\Models\InvoiceHeader;
use App\Services\EmailService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class EmailController extends Controller
{
    protected EmailService $emailService;

    public function __construct(EmailService $emailService)
    {
        $this->emailService = $emailService;
    }

    /**
     * Queue email dispatch for single invoice.
     */
    public function send(Request $request, InvoiceHeader $invoice): RedirectResponse
    {
        $this->emailService->sendInvoiceEmail($invoice, $request->user()?->id);

        return redirect()->back()->with('success', "Email dispatch for invoice {$invoice->invoice_number} has been queued successfully.");
    }

    /**
     * Queue email dispatch for bulk selected invoices.
     */
    public function sendBulk(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'invoice_ids' => ['required', 'array', 'min:1'],
            'invoice_ids.*' => ['exists:invoice_headers,id'],
        ]);

        $count = $this->emailService->sendBulkInvoiceEmails($validated['invoice_ids'], $request->user()?->id);

        return redirect()->back()->with('success', "Queued email delivery for {$count} selected invoices.");
    }

    /**
     * Retry failed email dispatch.
     */
    public function retry(Request $request, InvoiceHeader $invoice): RedirectResponse
    {
        $this->emailService->retryFailedEmail($invoice, $request->user()?->id);

        return redirect()->back()->with('success', "Re-queued email delivery for invoice {$invoice->invoice_number}.");
    }
}
