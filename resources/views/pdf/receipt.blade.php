<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Official Receipt {{ $invoice->invoice_number }}</title>
    <style>
        @page {
            size: A4 portrait;
            margin: 12mm 15mm 15mm 15mm;
        }
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            font-size: 11px;
            color: #1e293b;
            line-height: 1.5;
            background: #ffffff;
            margin: 0;
            padding: 0;
        }
        .header-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 25px;
            border-bottom: 3px double #16a34a;
            padding-bottom: 12px;
        }
        .company-title {
            font-size: 14px;
            font-weight: 800;
            color: #0f172a;
            margin: 0;
        }
        .doc-title {
            font-size: 20px;
            font-weight: 900;
            color: #16a34a;
            text-align: right;
            margin: 0;
        }
        .kwitansi-box {
            border: 2px solid #cbd5e1;
            border-radius: 12px;
            padding: 20px;
            background: #f8fafc;
            margin-bottom: 25px;
        }
        .kwitansi-row {
            margin-bottom: 12px;
        }
        .kwitansi-label {
            width: 150px;
            display: inline-block;
            font-weight: 700;
            color: #475569;
        }
        .terbilang-box {
            background-color: #dcfce7;
            border: 1px dashed #16a34a;
            color: #15803d;
            padding: 10px;
            border-radius: 6px;
            font-weight: 800;
            font-style: italic;
            margin-top: 5px;
        }
        .amount-badge {
            font-size: 18px;
            font-weight: 900;
            color: #15803d;
            background: #ffffff;
            border: 2px solid #16a34a;
            padding: 8px 16px;
            border-radius: 8px;
            display: inline-block;
            font-family: monospace;
        }
        .footer-table {
            width: 100%;
            margin-top: 30px;
        }
    </style>
</head>
<body>

    <!-- Header -->
    <table class="header-table">
        <tr>
            <td>
                <p class="company-title">PT. TELKOM INDONESIA (PERSERO) Tbk</p>
                <span style="font-size: 10px; color: #ee2e24; font-weight: bold;">WITEL LAMPUNG - FINANCE & TREASURY</span><br>
                <span style="font-size: 9px; color: #64748b;">Jl. Majapahit No. 1, Enggal, Bandar Lampung 35118</span>
            </td>
            <td style="text-align: right;">
                <p class="doc-title">KWITANSI PEMBAYARAN</p>
                <span style="font-size: 10px; font-family: monospace; color: #64748b;">No: KW/TLK-LPG/{{ date('Y/m') }}/{{ str_pad($invoice->id, 4, '0', STR_PAD_LEFT) }}</span>
            </td>
        </tr>
    </table>

    <div class="kwitansi-box">
        <div class="kwitansi-row">
            <span class="kwitansi-label">Telah Diterima Dari:</span>
            <strong style="font-size: 13px; color: #0f172a;">{{ $invoice->customer->company_name }}</strong> ({{ $invoice->customer->customer_code }})
        </div>

        <div class="kwitansi-row">
            <span class="kwitansi-label">Uang Sejumlah:</span>
            <div class="terbilang-box">
                "{{ \App\Helpers\TerbilangHelper::convert($invoice->total_amount) }}"
            </div>
        </div>

        <div class="kwitansi-row" style="margin-top: 15px;">
            <span class="kwitansi-label">Untuk Pembayaran:</span>
            <span>Pelunasan Tagihan Layanan Telekomunikasi / Enterprise Link Invoice No. <strong style="font-family: monospace;">{{ $invoice->invoice_number }}</strong></span>
        </div>

        <div class="kwitansi-row">
            <span class="kwitansi-label">Metode Pembayaran:</span>
            <span>Transfer Bank Mandiri Virtual Account (<strong style="font-family: monospace; color: #0284c7;">{{ $invoice->customer->virtual_account ?: '9881140000000001' }}</strong>)</span>
        </div>

        <div class="kwitansi-row" style="margin-top: 20px;">
            <span class="kwitansi-label">Jumlah Nominal:</span>
            <div class="amount-badge">
                Rp {{ number_format($invoice->total_amount, 0, ',', '.') }}
            </div>
        </div>
    </div>

    <!-- Signatures -->
    <table class="footer-table">
        <tr>
            <td style="width: 60%; vertical-align: top;">
                <div style="font-size: 9px; color: #64748b; padding-right: 20px;">
                    <strong>Catatan Resmi:</strong><br>
                    1. Kwitansi ini adalah bukti pembayaran yang sah dan diterbitkan secara digital oleh PT. Telkom Indonesia Witel Lampung.<br>
                    2. Pembayaran dinyatakan sah setelah dana efektif masuk ke rekening Virtual Account Telkom.<br>
                    3. Harap menyimpan bukti kwitansi ini untuk arsip keuangan perusahaan.
                </div>
            </td>
            <td style="width: 40%; text-align: center; vertical-align: top;">
                <span style="font-size: 9.5px; color: #64748b;">Bandar Lampung, {{ date('d F Y') }}</span><br>
                <strong style="font-size: 10px; color: #0f172a;">Kasir & Treasury Telkom Witel Lampung</strong>
                <div style="height: 50px; padding-top: 5px;">
                    <span style="display: inline-block; border: 2px solid #16a34a; color: #16a34a; font-size: 9px; font-weight: 800; padding: 4px 10px; border-radius: 4px; transform: rotate(-3deg);">
                        PAID & VERIFIED<br>TELKOM TREASURY
                    </span>
                </div>
                <strong style="font-size: 10.5px; text-decoration: underline; color: #0f172a;">Finance Officer</strong>
            </td>
        </tr>
    </table>

</body>
</html>
