<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        body { font-family: sans-serif; font-size: 14px; color: #222; }
        h1 { font-size: 22px; margin-bottom: 0; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        td { padding: 6px 0; border-bottom: 1px solid #ddd; }
        .label { font-weight: bold; width: 200px; }
    </style>
</head>
<body>
    <h1>Damir CRM</h1>
    <p>Ponuda broj: <strong>{{ $offer->offer_number }}</strong></p>

    <table>
        <tr><td class="label">Tvrtka</td><td>{{ $offer->company->name }}</td></tr>
        <tr><td class="label">Deal</td><td>{{ $offer->deal->title }}</td></tr>
        <tr><td class="label">Iznos</td><td>{{ number_format($offer->amount, 2) }} €</td></tr>
        <tr><td class="label">Vrijedi do</td><td>{{ $offer->valid_until?->format('d.m.Y') ?? '-' }}</td></tr>
        <tr><td class="label">Napomene</td><td>{{ $offer->notes ?? '-' }}</td></tr>
        <tr><td class="label">Datum izrade</td><td>{{ $offer->created_at->format('d.m.Y') }}</td></tr>
    </table>
</body>
</html>
