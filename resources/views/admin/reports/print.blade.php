<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Official Election Results - {{ $election->election_name }}</title>
    <style>
        @page {
            size: A4 portrait;
            margin: 10mm;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Times New Roman', Times, serif;
            font-size: 12pt;
            line-height: 1.6;
            color: #000;
            background: #fff;
        }

        .container {
            width: 210mm;
            min-height: 297mm;
            margin: 0 auto;
            padding: 15mm 20mm;
            background: white;
        }

        /* Letterhead Header */
        .letterhead {
            text-align: center;
            padding-bottom: 15px;
            border-bottom: 3px double #000;
            margin-bottom: 20px;
        }

        .letterhead-logo {
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 20px;
            margin-bottom: 10px;
        }

        .school-seal {
            width: 70px;
            height: 70px;
            border: 2px solid #166534;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 10px;
            color: #166534;
            font-weight: bold;
            text-align: center;
            line-height: 1.2;
        }

        .school-info {
            text-align: center;
        }

        .republic {
            font-size: 10pt;
            letter-spacing: 1px;
            color: #333;
        }

        .school-name {
            font-size: 16pt;
            font-weight: bold;
            color: #166534;
            text-transform: uppercase;
            letter-spacing: 2px;
            margin: 3px 0;
        }

        .school-address {
            font-size: 10pt;
            color: #555;
            font-style: italic;
        }

        .org-name {
            font-size: 11pt;
            font-weight: bold;
            margin-top: 8px;
            color: #14532d;
        }

        /* Document Title */
        .document-title {
            text-align: center;
            margin: 25px 0;
        }

        .document-title h1 {
            font-size: 14pt;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 3px;
            margin-bottom: 5px;
            text-decoration: underline;
        }

        .document-title .subtitle {
            font-size: 12pt;
            font-weight: normal;
        }

        /* Document Info */
        .document-info {
            margin-bottom: 20px;
        }

        .info-row {
            display: flex;
            margin-bottom: 5px;
        }

        .info-label {
            font-weight: bold;
            width: 150px;
        }

        .info-value {
            flex: 1;
        }

        .filter-badge {
            display: inline-block;
            background: #f0f0f0;
            padding: 3px 10px;
            border: 1px solid #ccc;
            font-size: 10pt;
            margin-top: 10px;
        }

        /* Summary Box */
        .summary-section {
            background: #f9f9f9;
            border: 1px solid #ddd;
            padding: 15px;
            margin: 20px 0;
        }

        .summary-title {
            font-weight: bold;
            font-size: 11pt;
            margin-bottom: 10px;
            text-transform: uppercase;
            border-bottom: 1px solid #ccc;
            padding-bottom: 5px;
        }

        .summary-grid {
            display: flex;
            justify-content: space-between;
            flex-wrap: wrap;
        }

        .summary-item {
            text-align: center;
            flex: 1;
            min-width: 120px;
            padding: 10px;
        }

        .summary-number {
            font-size: 20pt;
            font-weight: bold;
            color: #166534;
        }

        .summary-label {
            font-size: 9pt;
            color: #555;
            text-transform: uppercase;
        }

        /* Position Section */
        .position-section {
            margin: 25px 0;
            page-break-inside: avoid;
        }

        .position-header {
            background: #166534;
            color: white;
            padding: 8px 15px;
            font-weight: bold;
            font-size: 11pt;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        /* Results Table */
        .results-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 11pt;
            margin-top: 0;
        }

        .results-table th {
            background: #e8e8e8;
            padding: 10px;
            text-align: left;
            font-weight: bold;
            border: 1px solid #ccc;
            font-size: 10pt;
        }

        .results-table th.rank-col {
            width: 60px;
            text-align: center;
        }

        .results-table th.votes-col {
            width: 100px;
            text-align: center;
        }

        .results-table td {
            padding: 10px;
            border: 1px solid #ccc;
            vertical-align: middle;
        }

        .results-table td.rank-cell {
            text-align: center;
            font-weight: bold;
        }

        .results-table td.votes-cell {
            text-align: center;
            font-weight: bold;
            font-size: 12pt;
        }

        .results-table tr:nth-child(odd) {
            background: #fafafa;
        }

        .results-table tr.winner {
            background: #f0fff4;
        }

        .results-table tr.winner td {
            font-weight: bold;
        }

        .winner-badge {
            display: inline-block;
            background: #166534;
            color: white;
            padding: 2px 8px;
            font-size: 8pt;
            border-radius: 3px;
            margin-left: 10px;
            text-transform: uppercase;
        }

        /* Breakdown Tables */
        .breakdown-section {
            margin: 25px 0;
            page-break-inside: avoid;
        }

        .breakdown-title {
            font-weight: bold;
            font-size: 11pt;
            text-transform: uppercase;
            border-bottom: 2px solid #166534;
            padding-bottom: 5px;
            margin-bottom: 15px;
        }

        .breakdown-grid {
            display: flex;
            gap: 20px;
        }

        .breakdown-table {
            flex: 1;
            border-collapse: collapse;
            font-size: 10pt;
        }

        .breakdown-table th {
            background: #f0f0f0;
            padding: 6px 10px;
            text-align: left;
            border: 1px solid #ccc;
            font-weight: bold;
        }

        .breakdown-table td {
            padding: 5px 10px;
            border: 1px solid #ccc;
        }

        .breakdown-table td:last-child {
            text-align: center;
            font-weight: bold;
        }

        /* Certification & Signatures */
        .certification {
            margin-top: 40px;
            padding-top: 20px;
            border-top: 1px solid #ccc;
        }

        .certification-text {
            text-align: justify;
            margin-bottom: 30px;
            font-size: 11pt;
            line-height: 1.8;
        }

        .signature-section {
            display: flex;
            justify-content: space-between;
            margin-top: 50px;
        }

        .signature-box {
            text-align: center;
            width: 200px;
        }

        .signature-line {
            border-top: 1px solid #000;
            padding-top: 5px;
            font-weight: bold;
            font-size: 11pt;
        }

        .signature-title {
            font-size: 9pt;
            color: #555;
            margin-top: 2px;
        }

        .date-signed {
            font-size: 10pt;
            margin-top: 5px;
        }

        /* Noted By Section */
        .noted-section {
            margin-top: 60px;
            text-align: center;
        }

        .noted-title {
            font-size: 10pt;
            margin-bottom: 40px;
        }

        .noted-signature {
            margin: 0 auto;
            width: 250px;
        }

        /* Footer */
        .document-footer {
            margin-top: 40px;
            padding-top: 15px;
            border-top: 1px solid #ccc;
            text-align: center;
            font-size: 9pt;
            color: #777;
        }

        .document-footer .generated {
            margin-bottom: 5px;
        }

        .document-footer .system {
            font-style: italic;
        }

        /* Print Button */
        .print-controls {
            position: fixed;
            top: 20px;
            right: 20px;
            display: flex;
            gap: 10px;
            z-index: 1000;
        }

        .print-btn {
            background: #166534;
            color: white;
            border: none;
            padding: 12px 24px;
            border-radius: 8px;
            cursor: pointer;
            font-size: 14px;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 8px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
            font-family: 'Segoe UI', sans-serif;
        }

        .print-btn:hover {
            background: #14532d;
        }

        .back-btn {
            background: #555;
        }

        .back-btn:hover {
            background: #333;
        }

        /* Print Styles */
        @media print {
            .print-controls {
                display: none !important;
            }

            body {
                font-size: 11pt;
                background: white;
            }

            .container {
                width: 100%;
                min-height: auto;
                padding: 0;
                margin: 0;
            }

            .position-section {
                page-break-inside: avoid;
            }

            .certification {
                page-break-inside: avoid;
            }
        }
    </style>
</head>

<body>
    <!-- Print Controls -->
    <div class="print-controls">
        <button class="print-btn" onclick="window.print()">
            <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z">
                </path>
            </svg>
            Print Report
        </button>
        <button class="print-btn back-btn" onclick="window.close()">
            <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
            </svg>
            Close
        </button>
    </div>

    <div class="container">
        <!-- Letterhead -->
        <div class="letterhead">
            <div class="republic">Republic of the Philippines</div>
            <div class="school-name">Central Philippines State University</div>
            <div class="school-address">Kabankalan City, Negros Occidental</div>
            @if ($election->organization)
                <div class="org-name">{{ $election->organization->name }}</div>
            @endif
        </div>

        <!-- Document Title -->
        <div class="document-title">
            <h1>Official Election Results</h1>
            <div class="subtitle">{{ $election->election_name }}</div>
        </div>

        <!-- Document Information -->
        <div class="document-info">
            <div class="info-row">
                <span class="info-label">Election Date:</span>
                <span
                    class="info-value">{{ $election->election_date ? $election->election_date->format('F d, Y') : 'N/A' }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Academic Year:</span>
                <span
                    class="info-value">{{ $reportData['electionYear'] }}-{{ intval($reportData['electionYear']) + 1 }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Report Type:</span>
                <span class="info-value">
                    @if ($filterType === 'all')
                        Complete Results (All Students)
                    @else
                        Filtered Results - {{ ucfirst($filterType === 'yearlevel' ? 'Year Level' : $filterType) }}:
                        {{ $filterValue }}
                    @endif
                </span>
            </div>
        </div>

        <!-- Summary Statistics -->
        <div class="summary-section">
            <div class="summary-title">Election Summary Statistics</div>
            <div class="summary-grid">
                <div class="summary-item">
                    <div class="summary-number">{{ number_format($reportData['totalParticipants']) }}</div>
                    <div class="summary-label">Students Voted</div>
                </div>
                <div class="summary-item">
                    <div class="summary-number">{{ number_format($reportData['maleVoters'] ?? 0) }}</div>
                    <div class="summary-label">Male Voters</div>
                </div>
                <div class="summary-item">
                    <div class="summary-number">{{ number_format($reportData['femaleVoters'] ?? 0) }}</div>
                    <div class="summary-label">Female Voters</div>
                </div>
                <div class="summary-item">
                    <div class="summary-number">{{ number_format($reportData['totalEligible']) }}</div>
                    <div class="summary-label">Eligible Voters</div>
                </div>
                <div class="summary-item">
                    <div class="summary-number">{{ $reportData['participationRate'] }}%</div>
                    <div class="summary-label">Voter Turnout</div>
                </div>
            </div>
        </div>

        <!-- Results by Position -->
        @foreach ($reportData['resultsByPosition'] as $position => $candidates)
            <div class="position-section">
                <div class="position-header">{{ $position }}</div>
                <table class="results-table">
                    <thead>
                        <tr>
                            <th class="rank-col">Rank</th>
                            <th>Candidate Name</th>
                            <th>Partylist / Affiliation</th>
                            <th class="votes-col">Votes</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($candidates as $index => $candidate)
                            <tr class="{{ $index === 0 ? 'winner' : '' }}">
                                <td class="rank-cell">{{ $index + 1 }}</td>
                                <td>
                                    {{ $candidate->candidate_name }}
                                    @if ($index === 0)
                                        <span class="winner-badge">Elected</span>
                                    @endif
                                </td>
                                <td>{{ $candidate->partylist->name ?? 'Independent' }}</td>
                                <td class="votes-cell">{{ number_format($candidate->filtered_votes) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endforeach

        @if (count($reportData['resultsByPosition']) === 0)
            <div style="text-align: center; padding: 40px; border: 1px solid #ccc; margin: 20px 0;">
                <p>No candidates registered for this election.</p>
            </div>
        @endif

        <!-- Participation Breakdown -->
        @if (
            $filterType === 'all' &&
                (count($reportData['participationBreakdown']['byCourse']) > 0 ||
                    count($reportData['participationBreakdown']['byYearlevel']) > 0))
            <div class="breakdown-section">
                <div class="breakdown-title">Voter Participation Breakdown</div>
                <div class="breakdown-grid">
                    @if (count($reportData['participationBreakdown']['byCourse']) > 0)
                        <table class="breakdown-table">
                            <thead>
                                <tr>
                                    <th colspan="2" style="text-align: center;">By Course/Program</th>
                                </tr>
                                <tr>
                                    <th>Course</th>
                                    <th style="width: 60px;">Count</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($reportData['participationBreakdown']['byCourse']->take(8) as $item)
                                    <tr>
                                        <td>{{ $item->course }}</td>
                                        <td>{{ $item->count }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @endif

                    @if (count($reportData['participationBreakdown']['byYearlevel']) > 0)
                        <table class="breakdown-table">
                            <thead>
                                <tr>
                                    <th colspan="2" style="text-align: center;">By Year Level</th>
                                </tr>
                                <tr>
                                    <th>Year Level</th>
                                    <th style="width: 60px;">Count</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($reportData['participationBreakdown']['byYearlevel'] as $item)
                                    <tr>
                                        <td>{{ $item->yearlevel }}</td>
                                        <td>{{ $item->count }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @endif
                </div>
            </div>
        @endif

        <!-- Certification -->
        <div class="certification">
            <div class="certification-text">
                We, the undersigned members of the Election Committee, hereby certify that the above results are true
                and correct based on the official tally of votes conducted during the {{ $election->election_name }}
                held on
                {{ $election->election_date ? $election->election_date->format('F d, Y') : 'the scheduled date' }}. The
                election was conducted in accordance with the established rules and guidelines of the institution.
            </div>

            <div class="signature-section">
                <div class="signature-box">
                    <div class="signature-line">________________________</div>
                    <div class="signature-title">Election Committee Chairperson</div>
                </div>
                <div class="signature-box">
                    <div class="signature-line">________________________</div>
                    <div class="signature-title">Election Committee Member</div>
                </div>
                <div class="signature-box">
                    <div class="signature-line">________________________</div>
                    <div class="signature-title">Election Committee Member</div>
                </div>
            </div>

            <div class="noted-section">
                <div class="noted-title">Noted by:</div>
                <div class="noted-signature">
                    <div class="signature-line">________________________</div>
                    <div class="signature-title">SSG / Organization Adviser</div>
                </div>
            </div>
        </div>

        <!-- Footer -->
        <div class="document-footer">
            <div class="generated">
                This report was generated on {{ $generatedAt->format('F d, Y') }} at
                {{ $generatedAt->format('h:i A') }}
            </div>
            <div class="system">
                CpsuVotewisely.com — Cloud-Based Real-Time Voting System
            </div>
        </div>
    </div>
</body>

</html>
