<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Financial Report</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 12px; }
        .header { text-align: center; margin-bottom: 30px; }
        .header h1 { margin: 0; font-size: 24px; }
        .header p { margin: 5px 0; color: #666; }
        .summary { margin-bottom: 30px; }
        .summary table { width: 100%; border-collapse: collapse; }
        .summary td { padding: 10px; border: 1px solid #ddd; }
        .summary .label { font-weight: bold; background: #f5f5f5; width: 40%; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th { background: #4CAF50; color: white; padding: 10px; text-align: left; }
        td { padding: 8px; border-bottom: 1px solid #ddd; }
        .income { color: green; }
        .expense { color: red; }
        .footer { margin-top: 30px; text-align: center; font-size: 10px; color: #999; }
    </style>
</head>
<body>
    <div class="header">
        <h1>{{ $store->name }}</h1>
        <p>Financial Report</p>
        <p>Period: {{ $startDate }} to {{ $endDate }}</p>
    </div>

    <div class="summary">
        <table>
            <tr>
                <td class="label">Total Income</td>
                <td>Rp {{ number_format($reportData['total_income'], 0, ',', '.') }}</td>
            </tr>
            <tr>
                <td class="label">Total Expense</td>
                <td>Rp {{ number_format($reportData['total_expense'], 0, ',', '.') }}</td>
            </tr>
            <tr>
                <td class="label">Profit</td>
                <td><strong>Rp {{ number_format($reportData['profit'], 0, ',', '.') }}</strong></td>
            </tr>
        </table>
    </div>

    <h3>Transaction Details</h3>
    <table>
        <thead>
            <tr>
                <th>Date</th>
                <th>Type</th>
                <th>Customer</th>
                <th>Category</th>
                <th>Note</th>
                <th>Amount</th>
            </tr>
        </thead>
        <tbody>
            @foreach($transactions as $transaction)
            <tr>
                <td>{{ $transaction->transaction_date }}</td>
                <td>{{ ucfirst($transaction->type) }}</td>
                <td>{{ $transaction->customer?->name ?? '-' }}</td>
                <td>{{ $transaction->category ?? '-' }}</td>
                <td>{{ $transaction->note ?? '-' }}</td>
                <td class="{{ $transaction->type }}">
                    Rp {{ number_format($transaction->amount, 0, ',', '.') }}
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        <p>Generated on {{ now()->format('d M Y H:i') }}</p>
    </div>
</body>
</html>
