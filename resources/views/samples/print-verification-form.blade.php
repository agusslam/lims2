<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Form Verifikasi Sampel - {{ $sample->sample_code ?? 'Tanpa Kode' }}</title>
    <style>
        body { font-family: 'Segoe UI', sans-serif; font-size: 13px; margin: 40px;}
        table { width: 100%; border-collapse: collapse; margin-bottom: 15px;}
        th, td { border: 1px solid #000; padding: 6px;}
        .header-title { text-align:center; font-weight:bold; font-size:16px; margin-bottom:15px;}
        .section-title { font-weight:bold; background:#f2f2f2; padding:5px; }
        .small { font-size:12px;}
        .footer { margin-top:40px; width:100%; }
        .signature { width:33%; text-align:center; float:left; }
    </style>
</head>
<body>

    <div class="header-title">
        <div>FORM VERIFIKASI SAMPEL</div>
        <div class="small">Laboratorium Pengujian</div>
    </div>

    <table>
        <tr><th colspan="2" class="section-title">Informasi Sampel</th></tr>
        <tr>
            <td>Kode Sampel</td>
            <td>{{ $sample->sample_code ?? '-' }}</td>
        </tr>
        <tr>
            <td>Tracking</td>
            <td>{{ $sample->tracking_code ?? $sample->sampleRequest->tracking_code ?? '-' }}</td>
        </tr>
        <tr>
            <td>Pelanggan</td>
            <td>
                {{ optional(optional($sample->sampleRequest)->customer)->contact_person 
                ?? $sample->customer_name ?? '-' }}
            </td>
        </tr>
        <tr>
            <td>Perusahaan</td>
            <td>
                {{ optional(optional($sample->sampleRequest)->customer)->company_name 
                ?? $sample->company_name ?? '-' }}
            </td>
        </tr>
        <tr>
            <td>Jenis Sampel</td>
            <td>{{ $sample->sampleType->name ?? $sample->custom_sample_type ?? '-' }}</td>
        </tr>
        <tr>
            <td>Jumlah Sampel</td>
            <td>{{ $sample->quantity }} buah</td>
        </tr>
    </table>

    <table>
        <tr><th colspan="2" class="section-title">Parameter Uji</th></tr>
        @foreach($sample->parameters ?? [] as $param)
        <tr>
            <td>{{ $param->name }}</td>
            <td>{{ $param->unit ?? '-' }}</td>
        </tr>
        @endforeach
        @if(empty($sample->parameters) || $sample->parameters->count() == 0)
        <tr><td colspan="2">Tidak ada parameter terdaftar.</td></tr>
        @endif
    </table>

    <table>
        <tr><th colspan="2" class="section-title">Catatan/Keterangan</th></tr>
        <tr>
            <td colspan="2" style="height:80px">
                {{ $sample->codification_notes ?? '......................................................' }}
            </td>
        </tr>
    </table>

    <br><br>

    <div class="footer">
        <div class="signature">
            <div>Penerima Sampel</div><br><br><br>
            <div>(.........................................)</div>
        </div>
        <div class="signature">
            <div>Analis</div><br><br><br>
            <div>(.........................................)</div>
        </div>
        <div class="signature">
            <div>Penanggung Jawab</div><br><br><br>
            <div>(.........................................)</div>
        </div>
    </div>

</body>
</html>
