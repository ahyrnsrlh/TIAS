<?php

namespace App\Services;

use App\Models\InvoiceHeader;
use Dompdf\Dompdf;
use Dompdf\Options;
use Illuminate\Support\Facades\View;
use Spatie\Browsershot\Browsershot;

class PdfService
{
    /**
     * Render PDF content using Spatie Browsershot with Dompdf fallback.
     */
    protected function renderPdf(string $viewName, array $data, string $filename, bool $stream = false)
    {
        $html = View::make($viewName, $data)->render();

        try {
            // Attempt Spatie Browsershot PDF generation
            $pdfContent = Browsershot::html($html)
                ->paperSize(210, 297) // A4 mm
                ->margins(10, 10, 10, 10)
                ->showBackground()
                ->pdf();

            $disposition = $stream ? 'inline' : 'attachment';
            return response($pdfContent, 200, [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => "{$disposition}; filename=\"{$filename}\"",
            ]);
        } catch (\Throwable $e) {
            // Fallback to Dompdf engine for universal portable execution
            $options = new Options();
            $options->set('isHtml5ParserEnabled', true);
            $options->set('isRemoteEnabled', true);

            $dompdf = new Dompdf($options);
            $dompdf->loadHtml($html);
            $dompdf->setPaper('A4', 'portrait');
            $dompdf->render();

            $output = $dompdf->output();
            $disposition = $stream ? 'inline' : 'attachment';

            return response($output, 200, [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => "{$disposition}; filename=\"{$filename}\"",
            ]);
        }
    }

    public function generateInvoicePdf(InvoiceHeader $invoice, bool $stream = false)
    {
        $filename = "Telkom_Invoice_" . str_replace('/', '_', $invoice->invoice_number) . ".pdf";
        return $this->renderPdf('pdf.invoice', ['invoice' => $invoice], $filename, $stream);
    }

    public function generateBillingDetailPdf(InvoiceHeader $invoice, bool $stream = false)
    {
        $filename = "Telkom_Billing_Detail_" . str_replace('/', '_', $invoice->invoice_number) . ".pdf";
        return $this->renderPdf('pdf.billing_detail', ['invoice' => $invoice], $filename, $stream);
    }

    public function generateReceiptPdf(InvoiceHeader $invoice, bool $stream = false)
    {
        $filename = "Telkom_Official_Receipt_" . str_replace('/', '_', $invoice->invoice_number) . ".pdf";
        return $this->renderPdf('pdf.receipt', ['invoice' => $invoice], $filename, $stream);
    }

    public function generateTaxInvoicePdf(InvoiceHeader $invoice, bool $stream = false)
    {
        $filename = "Telkom_Faktur_Pajak_" . str_replace('/', '_', $invoice->invoice_number) . ".pdf";
        return $this->renderPdf('pdf.tax_invoice', ['invoice' => $invoice], $filename, $stream);
    }
}
