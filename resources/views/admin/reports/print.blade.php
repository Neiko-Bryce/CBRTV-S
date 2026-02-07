<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Official Election Results - {{ $election->election_name }}</title>
    <style>
        /* A4 bond: 210mm x 297mm, standard margins */
        @page {
            size: A4 portrait;
            margin: 15mm;
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
            background: #fff;
        }

        .page {
            width: 210mm;
            min-height: 297mm;
            margin: 0 auto;
            padding: 0 15mm;
            background: white;
        }

        /* Letterhead */
        .letterhead {
            text-align: center;
            padding-bottom: 10pt;
            border-bottom: 2px solid #000;
            margin-bottom: 14pt;
        }

        .letterhead .republic {
            font-size: 9pt;
            letter-spacing: 0.5pt;
            color: #333;
            margin-bottom: 2pt;
        }

        .letterhead .school-name {
            font-size: 14pt;
            font-weight: bold;
            color: #000;
            text-transform: uppercase;
            letter-spacing: 1pt;
            margin: 2pt 0;
        }

        .letterhead .school-address {
            font-size: 9pt;
            color: #444;
            font-style: italic;
            margin-bottom: 2pt;
        }

        .letterhead .org-name {
            font-size: 10pt;
            font-weight: bold;
            margin-top: 4pt;
            color: #000;
        }

        /* Document title */
        .doc-title {
            text-align: center;
            margin: 16pt 0 12pt;
        }

        .doc-title h1 {
            font-size: 12pt;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 1pt;
            text-decoration: underline;
            margin-bottom: 4pt;
        }

        .doc-title .subtitle {
            font-size: 11pt;
            font-weight: normal;
        }

        /* Meta block */
        .doc-meta {
            margin-bottom: 12pt;
            font-size: 10pt;
        }

        .doc-meta table {
            width: 100%;
            border-collapse: collapse;
        }

        .doc-meta td {
            padding: 2pt 0;
            vertical-align: top;
        }

        .doc-meta .label {
            width: 100pt;
            font-weight: bold;
        }

        /* Summary: compact horizontal block */
        .summary {
            border: 1px solid #000;
            margin-bottom: 14pt;
            page-break-inside: avoid;
        }

        .summary-caption {
            background: #e8e8e8;
            padding: 4pt 8pt;
            font-size: 9pt;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 0.5pt;
            border-bottom: 1px solid #000;
        }

        .summary-grid {
            display: table;
            width: 100%;
            table-layout: fixed;
        }

        .summary-row {
            display: table-row;
        }

        .summary-cell {
            display: table-cell;
            text-align: center;
            padding: 6pt 4pt;
            border-right: 1px solid #ccc;
            border-bottom: 1px solid #ccc;
            font-size: 10pt;
        }

        .summary-cell:last-child {
            border-right: none;
        }

        .summary-cell .num {
            font-size: 12pt;
            font-weight: bold;
            display: block;
            margin-bottom: 1pt;
        }

        .summary-cell .lbl {
            font-size: 8pt;
            text-transform: uppercase;
            color: #444;
        }

        /* Position block */
        .position-block {
            margin-bottom: 14pt;
            page-break-inside: avoid;
        }

        .position-head {
            background: #333;
            color: #fff;
            padding: 4pt 8pt;
            font-size: 10pt;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 0.5pt;
        }

        .results-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 10pt;
        }

        .results-table th,
        .results-table td {
            border: 1px solid #000;
            padding: 4pt 6pt;
            text-align: left;
        }

        .results-table th {
            background: #f0f0f0;
            font-weight: bold;
            font-size: 9pt;
        }

        .results-table .col-rank {
            width: 32pt;
            text-align: center;
        }

        .results-table .col-votes {
            width: 48pt;
            text-align: right;
        }

        .results-table td.col-rank {
            text-align: center;
            font-weight: bold;
        }

        .results-table td.col-votes {
            text-align: right;
            font-weight: bold;
        }

        .results-table tr.winner td {
            background: #f5f5f5;
            font-weight: bold;
        }

        .winner-tag {
            font-size: 8pt;
            margin-left: 4pt;
            font-weight: normal;
        }

        /* No candidates */
        .no-candidates {
            border: 1px solid #000;
            padding: 16pt;
            text-align: center;
            margin: 14pt 0;
            font-size: 10pt;
        }

        /* Breakdown section */
        .breakdown-section {
            margin: 14pt 0;
            page-break-inside: avoid;
        }

        .breakdown-caption {
            font-size: 10pt;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 0.5pt;
            margin-bottom: 6pt;
            padding-bottom: 2pt;
            border-bottom: 1px solid #000;
        }

        .breakdown-tables {
            display: table;
            width: 100%;
            border-collapse: separate;
            border-spacing: 0 0;
        }

        .breakdown-col {
            display: table-cell;
            width: 50%;
            vertical-align: top;
            padding-right: 10pt;
        }

        .breakdown-col:last-child {
            padding-right: 0;
            padding-left: 10pt;
        }

        .breakdown-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 9pt;
        }

        .breakdown-table th,
        .breakdown-table td {
            border: 1px solid #000;
            padding: 3pt 6pt;
        }

        .breakdown-table th {
            background: #f0f0f0;
            font-weight: bold;
        }

        .breakdown-table td:last-child {
            text-align: right;
            width: 40pt;
        }

        .breakdown-subtitle {
            font-size: 9pt;
            font-weight: bold;
            margin-bottom: 4pt;
        }

        /* Certification */
        .certification {
            margin-top: 20pt;
            padding-top: 12pt;
            border-top: 1px solid #000;
            page-break-inside: avoid;
        }

        .certification p {
            text-align: justify;
            font-size: 10pt;
            line-height: 1.5;
            margin-bottom: 16pt;
        }

        .signatures {
            display: table;
            width: 100%;
            margin-top: 24pt;
        }

        .sig-cell {
            display: table-cell;
            width: 33.33%;
            text-align: center;
            padding: 0 8pt;
        }

        .sig-line {
            border-top: 1px solid #000;
            margin-top: 24pt;
            padding-top: 4pt;
            font-size: 10pt;
            font-weight: bold;
        }

        .sig-role {
            font-size: 8pt;
            color: #444;
            margin-top: 1pt;
        }

        .noted-by {
            margin-top: 28pt;
            text-align: center;
        }

        .noted-by .sig-line {
            margin-left: auto;
            margin-right: auto;
            max-width: 180pt;
        }

        /* Footer */
        .doc-footer {
            margin-top: 20pt;
            padding-top: 8pt;
            border-top: 1px solid #999;
            text-align: center;
            font-size: 8pt;
            color: #555;
        }

        /* Screen-only: print buttons */
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

        @media print {
            .print-controls {
                display: none !important;
            }

            body {
                background: #fff;
            }

            .page {
                width: 100%;
                min-height: auto;
                padding: 0;
                margin: 0;
            }

            .position-block,
            .certification,
            .summary {
                page-break-inside: avoid;
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
        <!-- Letterhead -->
        <div class="letterhead">
            <div class="republic">Republic of the Philippines</div>
            <div class="school-name">Central Philippines State University</div>
            <div class="school-address">Brgy Po-ok, Hinoba-an, Negros Occidental</div>
            @if ($election->organization)
                <div class="org-name">{{ $election->organization->name }}</div>
            @endif
        </div>

        <!-- Title -->
        <div class="doc-title">
            <h1>Official Election Results</h1>
            <div class="subtitle">{{ $election->election_name }}</div>
        </div>

        <!-- Document info -->
        <div class="doc-meta">
            <table>
                <tr>
                    <td class="label">Election Date:</td>
                    <td>{{ $election->election_date ? $election->election_date->format('F d, Y') : 'N/A' }}</td>
                </tr>
                <tr>
                    <td class="label">Academic Year:</td>
                    <td>{{ $reportData['electionYear'] }}-{{ (int) $reportData['electionYear'] + 1 }}</td>
                </tr>
                <tr>
                    <td class="label">Report Type:</td>
                    <td>
                        @if ($filterType === 'all')
                            Complete Results (All Students)
                        @else
                            Filtered — {{ ucfirst($filterType === 'yearlevel' ? 'Year Level' : $filterType) }}:
                            {{ $filterValue }}
                        @endif
                    </td>
                </tr>
            </table>
        </div>

        <!-- Summary -->
        <div class="summary">
            <div class="summary-caption">Election Summary</div>
            <div class="summary-grid">
                <div class="summary-row">
                    <div class="summary-cell">
                        <span class="num">{{ number_format($reportData['totalParticipants']) }}</span>
                        <span class="lbl">Students Voted</span>
                    </div>
                    <div class="summary-cell">
                        <span class="num">{{ number_format($reportData['maleVoters'] ?? 0) }}</span>
                        <span class="lbl">Male Voters</span>
                    </div>
                    <div class="summary-cell">
                        <span class="num">{{ number_format($reportData['femaleVoters'] ?? 0) }}</span>
                        <span class="lbl">Female Voters</span>
                    </div>
                    <div class="summary-cell">
                        <span class="num">{{ number_format($reportData['totalEligible']) }}</span>
                        <span class="lbl">Eligible Voters</span>
                    </div>
                    <div class="summary-cell">
                        <span class="num">{{ $reportData['participationRate'] }}%</span>
                        <span class="lbl">Turnout</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Results by position -->
        @foreach ($reportData['resultsByPosition'] as $group)
            <div class="position-block">
                <div class="position-head">{{ $group['position_name'] }}</div>
                <table class="results-table">
                    <thead>
                        <tr>
                            <th class="col-rank">Rank</th>
                            <th>Candidate Name</th>
                            <th>Partylist / Affiliation</th>
                            <th class="col-votes">Votes</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($group['candidates'] as $index => $candidate)
                            <tr class="{{ $index === 0 ? 'winner' : '' }}">
                                <td class="col-rank">{{ $index + 1 }}</td>
                                <td>
                                    {{ $candidate->candidate_name }}
                                    @if ($index === 0)
                                        <span class="winner-tag">(Elected)</span>
                                    @endif
                                </td>
                                <td>{{ $candidate->partylist->name ?? 'Independent' }}</td>
                                <td class="col-votes">{{ number_format($candidate->filtered_votes) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endforeach

        @if (count($reportData['resultsByPosition']) === 0)
            <div class="no-candidates">No candidates registered for this election.</div>
        @endif

        <!-- Participation breakdown -->
        @if (
            $filterType === 'all' &&
                (count($reportData['participationBreakdown']['byCourse']) > 0 ||
                    count($reportData['participationBreakdown']['byYearlevel']) > 0))
            <div class="breakdown-section">
                <div class="breakdown-caption">Voter Participation Breakdown</div>
                <div class="breakdown-tables">
                    <div class="breakdown-col">
                        @if (count($reportData['participationBreakdown']['byCourse']) > 0)
                            <div class="breakdown-subtitle">By Course / Program</div>
                            <table class="breakdown-table">
                                <thead>
                                    <tr>
                                        <th>Course</th>
                                        <th>Count</th>
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
                    </div>
                    <div class="breakdown-col">
                        @if (count($reportData['participationBreakdown']['byYearlevel']) > 0)
                            <div class="breakdown-subtitle">By Year Level</div>
                            <table class="breakdown-table">
                                <thead>
                                    <tr>
                                        <th>Year Level</th>
                                        <th>Count</th>
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
            </div>
        @endif

        <!-- Certification -->
        <div class="certification">
            <p>
                We, the undersigned members of the Election Committee, hereby certify that the above results are true
                and correct
                based on the official tally of votes conducted during the {{ $election->election_name }} held on
                {{ $election->election_date ? $election->election_date->format('F d, Y') : 'the scheduled date' }}. The
                election
                was conducted in accordance with the established rules and guidelines of the institution.
            </p>
            <div class="signatures">
                <div class="sig-cell">
                    <div class="sig-line">_________________________</div>
                    <div class="sig-role">Election Committee Chairperson</div>
                </div>
                <div class="sig-cell">
                    <div class="sig-line">_________________________</div>
                    <div class="sig-role">Election Committee Member</div>
                </div>
                <div class="sig-cell">
                    <div class="sig-line">_________________________</div>
                    <div class="sig-role">Election Committee Member</div>
                </div>
            </div>
            <div class="noted-by">
                <div class="sig-line">_________________________</div>
                <div class="sig-role">SSG / Organization Adviser</div>
            </div>
        </div>

        <!-- Footer -->
        <div class="doc-footer">
            <div>Generated on {{ $generatedAt->format('F d, Y') }} at {{ $generatedAt->format('h:i A') }}</div>
            <div>CpsuVotewisely.com — Cloud-Based Real-Time Voting System</div>
        </div>
    </div>
</body>

</html>
