<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Invoice {{ $invoice->invoice_number }} - PT. Telkom Indonesia Witel Lampung</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f1f5f9;
            color: #1e293b;
            margin: 0;
            padding: 20px;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            background-color: #ffffff;
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 10px 25px rgba(0,0,0,0.05);
            border: 1px solid #e2e8f0;
        }
        .header {
            background-color: #ee2e24;
            color: #ffffff;
            padding: 30px;
            text-align: center;
        }
        .header h1 {
            margin: 0;
            font-size: 22px;
            font-weight: 800;
        }
        .header p {
            margin: 5px 0 0 0;
            font-size: 13px;
            opacity: 0.9;
        }
        .body {
            padding: 30px;
            font-size: 14px;
            line-height: 1.6;
        }
        .invoice-card {
            background-color: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            padding: 16px;
            margin: 20px 0;
        }
        .invoice-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 8px;
            font-size: 13px;
        }
        .invoice-row:last-child {
            margin-bottom: 0;
        }
        .amount-highlight {
            font-size: 18px;
            font-weight: 800;
            color: #ee2e24;
        }
        .footer {
            background-color: #0f172a;
            color: #94a3b8;
            padding: 20px;
            text-align: center;
            font-size: 11px;
        }
        .footer a {
            color: #38bdf8;
            text-decoration: none;
        }
        .btn {
            display: inline-block;
            background-color: #ee2e24;
            color: #ffffff;
            text-decoration: none;
            padding: 12px 24px;
            border-radius: 8px;
            font-weight: 700;
            font-size: 13px;
            margin-top: 15px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>PT. TELKOM INDONESIA WITEL LAMPUNG</h1>
            <p>Invoice Layanan Telekomunikasi & Enterprise B2B Link</p>
        </div>

        <div class="body">
            <p>Kepada Yth.<br><strong>{{ $invoice->customer->company_name }}</strong><br>Attn: {{ $invoice->customer->name }}</p>

            <p>Bersama email ini, kami sampaikan tagihan resmi penggunaan layanan PT. Telkom Indonesia Witel Lampung dengan rincian sebagai berikut:</p>

            <div class="invoice-card">
                <div class="invoice-row">
                    <span style="color: #64748b;">Nomor Invoice:</span>
                    <strong>{{ $invoice->invoice_number }}</strong>
                </div>
                <div class="invoice-row">
                    <span style="color: #64748b;">Tanggal Terbit:</span>
                    <span>{{ $invoice->issue_date }}</span>
                </div>
                <div class="invoice-row">
                    <span style="color: #64748b;">Tanggal Jatuh Tempo:</span>
                    <strong style="color: #ee2e24;">{{ $invoice->due_date }}</strong>
                </div>
                <div class="invoice-row">
                    <span style="color: #64748b;">Virtual Account (Mandiri):</span>
                    <strong style="color: #0284c7;">{{ $invoice->customer->virtual_account ?: '9881140000000001' }}</strong>
                </div>
                <hr style="border: none; border-top: 1px solid #cbd5e1; margin: 10px 0;">
                <div class="invoice-row">
                    <span>Total Tagihan:</span>
                    <span class="amount-highlight">Rp {{ number_format($invoice->total_amount, 0, ',', '.') }}</span>
                </div>
            </div>

            <p>Dokumen invoice resmi dalam format A4 PDF telah dilampirkan pada email ini untuk keperluan administrasi dan pencairan dana perusahaan Anda.</p>

            <p style="font-size: 12px; color: #64748b;">
                Pembayaran dapat dilakukan melalui transfer ke Bank Mandiri Virtual Account PT. Telkom Indonesia Witel Lampung. Mohon melakukan pelunasan sebelum tanggal jatuh tempo.
            </p>

            <p>Terima kasih atas kerja sama dan kepercayaan Anda menggunakan layanan PT. Telkom Indonesia Witel Lampung.</p>
        </div>

        <div class="footer">
            <p>PT. Telkom Indonesia (Persero) Tbk Witel Lampung<br>Jl. Majapahit No. 1, Enggal, Bandar Lampung 35118</p>
            <p>Email ini dikirim secara otomatis oleh Sistem TIAS (Telkom Invoice Automation System).</p>
        </div>
    </div>
</body>
</html>
