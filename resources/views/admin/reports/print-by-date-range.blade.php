<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Elections Summary - {{ $report_period_label }}</title>
    <style>
        @page {
            size: A4 portrait;
            margin: 18mm;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Times New Roman', Times, serif;
            font-size: 11pt;
            line-height: 1.4;
            color: #000;
            background: #e5e5e5;
        }

        .page {
            width: 210mm;
            min-height: 297mm;
            margin: 24px auto;
            padding: 18mm 18mm;
            background: #fff;
            position: relative;
            box-shadow: 0 2px 12px rgba(0,0,0,0.12);
        }

        .letterhead {
            text-align: center;
            padding-bottom: 12pt;
            border-bottom: 2px solid #000;
            margin-bottom: 16pt;
        }

        .letterhead .republic {
            font-size: 9pt;
            letter-spacing: 0.5pt;
            color: #1a1a1a;
            margin-bottom: 4pt;
        }

        .letterhead .school-name {
            font-size: 14pt;
            font-weight: bold;
            color: #000;
            text-transform: uppercase;
            letter-spacing: 1pt;
            margin: 4pt 0;
        }

        .letterhead .school-address {
            font-size: 9pt;
            color: #333;
            margin-bottom: 4pt;
        }

        .doc-title {
            text-align: center;
            margin: 18pt 0 10pt;
        }

        .doc-title h1 {
            font-size: 13pt;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 1pt;
            text-decoration: underline;
            margin-bottom: 6pt;
        }

        .doc-title .subtitle {
            font-size: 11pt;
            color: #333;
        }

        .summary-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 10pt;
            margin-top: 12pt;
        }

        .summary-table th,
        .summary-table td {
            border: 1px solid #000;
            padding: 6pt 8pt;
            text-align: left;
        }

        .summary-table th {
            background: #e8e8e8;
            font-weight: bold;
            font-size: 9pt;
            text-transform: uppercase;
        }

        .summary-table td.text-right {
            text-align: right;
        }

        .summary-table tbody tr:nth-child(even) {
            background: #fafafa;
        }

        .doc-footer {
            margin-top: 24pt;
            padding-top: 8pt;
            border-top: 1px solid #999;
            text-align: center;
            font-size: 8pt;
            color: #555;
        }

        .print-controls {
            position: fixed;
            top: 12px;
            right: 12px;
            display: flex;
            gap: 8px;
            z-index: 1000;
        }

        .print-btn {
            background: #166534;
            color: #fff;
            border: none;
            padding: 10px 18px;
            border-radius: 6px;
            cursor: pointer;
            font-size: 13px;
            font-weight: 600;
            font-family: 'Segoe UI', sans-serif;
        }

        .print-btn.back-btn {
            background: #555;
        }

        .print-btn:hover {
            opacity: 0.9;
        }

        .no-elections {
            border: 1px solid #000;
            padding: 20pt;
            text-align: center;
            margin-top: 12pt;
            font-size: 10pt;
        }

        @media print {
            .print-controls {
                display: none !important;
            }
            body {
                background: #fff;
            }
            .page {
                width: 100%;
                min-height: 0;
                padding: 0;
                margin: 0;
                box-shadow: none;
            }
        }
    </style>
</head>

<body>
    <div class="print-controls">
        <button class="print-btn" type="button" onclick="window.print()">Print Report</button>
        <button class="print-btn back-btn" type="button" onclick="window.close()">Close</button>
    </div>

    <div class="page">
        <div class="letterhead">
            <div class="republic">Republic of the Philippines</div>
            <div class="school-name">Central Philippines State University</div>
            <div class="school-address">Brgy Po-ok, Hinoba-an, Negros Occidental</div>
        </div>

        <div class="doc-title">
            <h1>Elections in period</h1>
            <div class="subtitle">{{ $report_period_label }}</div>
        </div>

        @if (count($elections) > 0)
            <table class="summary-table">
                <thead>
                    <tr>
                        <th>Election Name</th>
                        <th>Period</th>
                        <th class="text-right">Total Votes</th>
                        <th class="text-right">Students Voted</th>
                        <th class="text-right">Participation %</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($elections as $row)
                        <tr>
                            <td>{{ $row['election_name'] }}</td>
                            <td>{{ $row['period_label'] }}</td>
                            <td class="text-right">{{ number_format($row['total_votes']) }}</td>
                            <td class="text-right">{{ number_format($row['total_students_voted']) }}</td>
                            <td class="text-right">{{ $row['participation_rate'] !== null ? $row['participation_rate'] . '%' : '—' }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @else
            <div class="no-elections">No elections with votes in the selected period.</div>
        @endif

        <div class="doc-footer">
            <div>Generated on {{ $generatedAt->format('F d, Y') }} at {{ $generatedAt->format('h:i A') }}</div>
            <div>CpsuVotewisely.com — Cloud-Based Real-Time Voting System</div>
        </div>
    </div>
</body>

</html>
