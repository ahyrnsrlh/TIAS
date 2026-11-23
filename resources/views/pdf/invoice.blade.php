<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Invoice {{ $invoice->invoice_number }}</title>
    <style>
        @page {
            size: A4 portrait;
            margin: 12mm 15mm 15mm 15mm;
        }
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            font-size: 11px;
            color: #1e293b;
            line-height: 1.4;
            background: #ffffff;
            margin: 0;
            padding: 0;
        }
        .header-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
            border-b: 2px solid #ee2e24;
            padding-bottom: 10px;
        }
        .logo-box {
            width: 44px;
            height: 44px;
            background-color: #ee2e24;
            color: #ffffff;
            font-size: 24px;
            font-weight: 900;
            text-align: center;
            line-height: 44px;
            border-radius: 8px;
            display: inline-block;
        }
        .company-title {
            font-size: 14px;
            font-weight: 800;
            color: #0f172a;
            margin: 0;
        }
        .witel-sub {
            font-size: 11px;
            font-weight: 700;
            color: #ee2e24;
            margin: 0;
        }
        .inv-title {
            font-size: 20px;
            font-weight: 900;
            color: #0f172a;
            text-align: right;
            margin: 0;
        }
        .inv-sub {
            font-size: 10px;
            color: #64748b;
            text-align: right;
        }
        .status-badge {
            display: inline-block;
            padding: 3px 10px;
            border-radius: 12px;
            font-size: 10px;
            font-weight: 700;
            text-transform: uppercase;
            background: #f1f5f9;
            color: #334155;
            border: 1px solid #cbd5e1;
        }
        .meta-table {
            width: 100%;
            margin-bottom: 20px;
            border-collapse: collapse;
        }
        .meta-box {
            background-color: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            padding: 12px;
            vertical-align: top;
        }
        .meta-label {
            font-size: 9px;
            font-weight: 800;
            color: #ee2e24;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 4px;
        }
        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        .items-table th {
            background-color: #0f172a;
            color: #ffffff;
            font-size: 10px;
            font-weight: 700;
            padding: 8px 10px;
            text-align: left;
        }
        .items-table td {
            padding: 9px 10px;
            border-bottom: 1px solid #e2e8f0;
            font-size: 10.5px;
        }
        .totals-table {
            width: 45%;
            float: right;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        .totals-table td {
            padding: 4px 8px;
            font-size: 10.5px;
        }
        .totals-table .grand-total {
            font-size: 13px;
            font-weight: 800;
            color: #ee2e24;
            border-top: 2px solid #0f172a;
            padding-top: 6px;
        }
        .payment-box {
            width: 50%;
            float: left;
            background-color: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            padding: 10px;
        }
        .clearfix::after {
            content: "";
            clear: both;
            display: table;
        }
        .footer-table {
            width: 100%;
            margin-top: 40px;
            border-top: 1px solid #e2e8f0;
            padding-top: 15px;
        }
        .qr-placeholder {
            width: 70px;
            height: 70px;
            border: 1px solid #cbd5e1;
            padding: 2px;
            background: #ffffff;
        }
    </style>
</head>
<body>

    <!-- Header -->
    <table class="header-table">
        <tr>
            <td style="width: 55px; vertical-align: top;">
                <div class="logo-box">T</div>
            </td>
            <td style="vertical-align: top;">
                <p class="company-title">PT. TELKOM INDONESIA (PERSERO) Tbk</p>
                <p class="witel-sub">WITEL LAMPUNG</p>
                <span style="font-size: 9.5px; color: #475569;">Jl. Majapahit No. 1, Enggal, Bandar Lampung 35118 | NPWP: 01.000.013.1-093.000</span>
            </td>
            <td style="vertical-align: top; text-align: right;">
                <p class="inv-title">INVOICE</p>
                <p class="inv-sub"><strong>{{ $invoice->invoice_number }}</strong></p>
                <span class="status-badge">{{ strtoupper($invoice->status) }}</span>
            </td>
        </tr>
    </table>

    <!-- Client & Meta Info -->
    <table class="meta-table">
        <tr>
            <td class="meta-box" style="width: 48%;">
                <div class="meta-label">Billed To (Pelanggan Enterprise)</div>
                <strong style="font-size: 12px; color: #0f172a;">{{ $invoice->customer->company_name }}</strong><br>
                <span>Attn: {{ $invoice->customer->name }}</span><br>
                <span>{{ $invoice->customer->address }}, {{ $invoice->customer->city }}</span><br>
                <div style="margin-top: 6px; font-family: monospace; font-size: 10px; color: #334155;">
                    NPWP: {{ $invoice->customer->npwp ?: 'N/A' }}<br>
                    Virtual Account: <strong style="color: #0284c7;">{{ $invoice->customer->virtual_account ?: '9881140000000001' }}</strong>
                </div>
            </td>
            <td style="width: 4%;"></td>
            <td class="meta-box" style="width: 48%;">
                <div class="meta-label">Ringkasan Tagihan</div>
                <table style="width: 100%; border-collapse: collapse; font-size: 10px;">
                    <tr>
                        <td style="color: #64748b;">Tanggal Terbit:</td>
                        <td style="text-align: right; font-weight: bold;">{{ $invoice->issue_date }}</td>
                    </tr>
                    <tr>
                        <td style="color: #64748b;">Jatuh Tempo:</td>
                        <td style="text-align: right; font-weight: bold; color: #ee2e24;">{{ $invoice->due_date }}</td>
                    </tr>
                    @if($invoice->period_start)
                    <tr>
                        <td style="color: #64748b;">Periode Layanan:</td>
                        <td style="text-align: right;">{{ $invoice->period_start }} s/d {{ $invoice->period_end }}</td>
                    </tr>
                    @endif
                    <tr>
                        <td style="color: #64748b;">Syarat Pembayaran:</td>
                        <td style="text-align: right;">{{ $invoice->payment_terms ?: 'Net 14 Days' }}</td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

    <!-- Line Items Table -->
    <table class="items-table">
        <thead>
            <tr>
                <th style="width: 30px; text-align: center;">#</th>
                <th>Rincian Layanan & Deskripsi</th>
                <th style="width: 50px; text-align: center;">Qty</th>
                <th style="width: 110px; text-align: right;">Harga Satuan (Rp)</th>
                <th style="width: 120px; text-align: right;">Jumlah (Rp)</th>
            </tr>
        </thead>
        <tbody>
            @foreach($invoice->items as $idx => $item)
            <tr>
                <td style="text-align: center; color: #64748b;">{{ $idx + 1 }}</td>
                <td>
                    <strong style="color: #0f172a;">{{ $item->service_name }}</strong>
                    @if($item->description)
                        <br><span style="font-size: 9.5px; color: #64748b;">{{ $item->description }}</span>
                    @endif
                    @if($item->service_code)
                        <br><span style="font-size: 9px; font-family: monospace; color: #94a3b8;">Kode: {{ $item->service_code }}</span>
                    @endif
                </td>
                <td style="text-align: center; font-family: monospace;">{{ $item->quantity }}</td>
                <td style="text-align: right; font-family: monospace;">{{ number_format($item->unit_price, 0, ',', '.') }}</td>
                <td style="text-align: right; font-family: monospace; font-weight: bold;">{{ number_format($item->amount, 0, ',', '.') }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <!-- Payment & Totals Section -->
    <div class="clearfix">
        <div class="payment-box">
            <div class="meta-label">Instruksi Pembayaran Transfer Bank</div>
            <p style="font-size: 9.5px; margin: 4px 0; color: #475569;">Transfer ke Bank Virtual Account PT. Telkom Indonesia Witel Lampung:</p>
            <div style="font-family: monospace; font-size: 10px; background: #ffffff; padding: 6px; border: 1px solid #cbd5e1; border-radius: 4px;">
                Bank: <strong>BANK MANDIRI</strong><br>
                A/N: <strong>PT TELKOM INDONESIA WITEL LAMPUNG</strong><br>
                Virtual Account: <strong style="color: #0284c7; font-size: 11px;">{{ $invoice->customer->virtual_account ?: '9881140000000001' }}</strong>
            </div>
            @if($invoice->notes)
                <p style="font-size: 9px; color: #64748b; font-style: italic; margin-top: 6px;">Catatan: {{ $invoice->notes }}</p>
            @endif
        </div>

        <table class="totals-table">
            <tr>
                <td style="color: #64748b;">Subtotal:</td>
                <td style="text-align: right; font-family: monospace;">Rp {{ number_format($invoice->subtotal, 0, ',', '.') }}</td>
            </tr>
            <tr>
                <td style="color: #64748b;">PPN ({{ $invoice->tax_percentage }}%):</td>
                <td style="text-align: right; font-family: monospace;">Rp {{ number_format($invoice->tax_amount, 0, ',', '.') }}</td>
            </tr>
            @if($invoice->discount_amount > 0)
            <tr>
                <td style="color: #64748b;">Potongan Harga:</td>
                <td style="text-align: right; font-family: monospace; color: #dc2626;">-Rp {{ number_format($invoice->discount_amount, 0, ',', '.') }}</td>
            </tr>
            @endif
            <tr class="grand-total">
                <td>Total Tagihan:</td>
                <td style="text-align: right; font-family: monospace;">Rp {{ number_format($invoice->total_amount, 0, ',', '.') }}</td>
            </tr>
        </table>
    </div>

    <!-- Footer Signatures & QR Code -->
    <table class="footer-table">
        <tr>
            <td style="width: 100px; vertical-align: bottom;">
                <!-- QR Code Verification Badge -->
                <div style="text-align: center;">
                    <svg class="qr-placeholder" viewBox="0 0 100 100" xmlns="http://www.w3.org/2000/svg">
                        <rect width="100" height="100" fill="#ffffff"/>
                        <rect x="10" y="10" width="30" height="30" fill="#0f172a"/>
                        <rect x="15" y="15" width="20" height="20" fill="#ffffff"/>
                        <rect x="20" y="20" width="10" height="10" fill="#0f172a"/>
                        <rect x="60" y="10" width="30" height="30" fill="#0f172a"/>
                        <rect x="65" y="15" width="20" height="20" fill="#ffffff"/>
                        <rect x="70" y="20" width="10" height="10" fill="#0f172a"/>
                        <rect x="10" y="60" width="30" height="30" fill="#0f172a"/>
                        <rect x="15" y="65" width="20" height="20" fill="#ffffff"/>
                        <rect x="20" y="70" width="10" height="10" fill="#0f172a"/>
                        <rect x="50" y="50" width="15" height="15" fill="#ee2e24"/>
                        <rect x="70" y="70" width="20" height="20" fill="#0f172a"/>
                    </svg>
                    <div style="font-size: 8px; color: #64748b; margin-top: 2px;">DJP Digital Verifier</div>
                </div>
            </td>
            <td style="vertical-align: bottom; padding-left: 15px;">
                <span style="font-size: 9px; color: #64748b;">
                    Dokumen ini diterbitkan secara otomatis oleh sistem TIAS Telkom Witel Lampung dan sah tanpa tanda tangan basah sesuai ketentuan UU ITE.
                </span>
            </td>
            <td style="width: 200px; text-align: right; vertical-align: bottom;">
                <span style="font-size: 9.5px; color: #64748b;">Bandar Lampung, {{ $invoice->issue_date }}</span><br>
                <strong style="font-size: 10px; color: #0f172a;">PT. TELKOM INDONESIA WITEL LAMPUNG</strong>
                <div style="height: 45px; text-align: right; padding-top: 5px;">
                    <!-- Digital Stamp graphic -->
                    <span style="display: inline-block; border: 2px dashed #ee2e24; color: #ee2e24; font-size: 9px; font-weight: 800; padding: 4px 8px; border-radius: 4px; transform: rotate(-5deg);">
                        VERIFIED DIGITAL STAMP<br>TELKOM WITEL LAMPUNG
                    </span>
                </div>
                <strong style="font-size: 10.5px; text-decoration: underline; color: #0f172a;">Manager Finance & Treasury</strong>
            </td>
        </tr>
    </table>

</body>
</html>
