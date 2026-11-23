<?php

namespace App\Http\Controllers;

use App\Models\InvoiceHeader;
use App\Services\PdfService;

class PdfController extends Controller
{
    protected PdfService $pdfService;

    public function __construct(PdfService $pdfService)
    {
        $this->pdfService = $pdfService;
    }

    /**
     * Download Invoice A4 PDF.
     */
    public function downloadInvoice(InvoiceHeader $invoice)
    {
        $invoice->load(['customer', 'items', 'taxInvoice', 'receipts']);
        return $this->pdfService->generateInvoicePdf($invoice, false);
    }

    /**
     * Stream Invoice A4 PDF inline.
     */
    public function streamInvoice(InvoiceHeader $invoice)
    {
        $invoice->load(['customer', 'items', 'taxInvoice', 'receipts']);
        return $this->pdfService->generateInvoicePdf($invoice, true);
    }

    /**
     * Download Billing Detail A4 PDF.
     */
    public function downloadBillingDetail(InvoiceHeader $invoice)
    {
        $invoice->load(['customer', 'items']);
        return $this->pdfService->generateBillingDetailPdf($invoice, false);
    }

    /**
     * Download Official Receipt (Kwitansi) A4 PDF.
     */
    public function downloadReceipt(InvoiceHeader $invoice)
    {
        $invoice->load(['customer', 'items']);
        return $this->pdfService->generateReceiptPdf($invoice, false);
    }

    /**
     * Download Tax Invoice (Faktur Pajak) A4 PDF.
     */
    public function downloadTaxInvoice(InvoiceHeader $invoice)
    {
        $invoice->load(['customer', 'items', 'taxInvoice']);
        return $this->pdfService->generateTaxInvoicePdf($invoice, false);
    }
}
