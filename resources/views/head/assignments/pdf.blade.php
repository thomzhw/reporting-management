<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Assignment Details</title>
    <style>
        body {
            font-family: 'DejaVu Sans', sans-serif;
            margin: 30px;
            color: #333;
            line-height: 1.5;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 1px solid #ddd;
            padding-bottom: 10px;
        }
        h2 {
            color: #2c3e50;
            margin-bottom: 20px;
        }
        h3 {
            color: #3498db;
            margin-top: 25px;
            margin-bottom: 15px;
            padding-bottom: 5px;
            border-bottom: 1px solid #eee;
        }
        .section {
            margin-bottom: 25px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }
        table, th, td {
            border: 1px solid #ddd;
        }
        th, td {
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f8f9fa;
        }
        .info-row {
            margin-bottom: 5px;
        }
        .badge {
            display: inline-block;
            padding: 2px 8px;
            font-size: 12px;
            border-radius: 4px;
            color: white;
            margin-left: 5px;
        }
        .badge-warning {
            background-color: #ffc107;
        }
        .badge-info {
            background-color: #17a2b8;
        }
        .badge-success {
            background-color: #28a745;
        }
        .badge-danger {
            background-color: #dc3545;
        }
        .notes {
            background-color: #f8f9fa;
            border-radius: 5px;
            padding: 10px;
            margin-top: 10px;
        }
        .footer {
            position: fixed;
            bottom: 0;
            width: 100%;
            text-align: center;
            font-size: 12px;
            color: #6c757d;
            border-top: 1px solid #eee;
            padding-top: 5px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h2>Assignment Details</h2>
        <div>
            <span class="badge 
                @if($assignment->status == 'pending') badge-warning
                @elseif($assignment->status == 'in_progress') badge-info
                @elseif($assignment->status == 'completed') badge-success
                @elseif($assignment->status == 'overdue') badge-danger
                @endif">
                {{ ucfirst($assignment->status) }}
            </span>
        </div>
    </div>
    
    <div class="section">
        <h3>Assignment Information</h3>
        <table>
            <tr>
                <th>Report</th>
                <td>{{ $assignment->template->name }}</td>
                <th>Assigned Date</th>
                <td>{{ $assignment->created_at->format('M d, Y') }}</td>
            </tr>
            <tr>
                <th>Staff Member</th>
                <td>{{ $assignment->staff->name }}</td>
                <th>Due Date</th>
                <td>
                    @if($assignment->due_date)
                        {{ $assignment->due_date->format('M d, Y') }}
                        @if($assignment->due_date->isPast() && $assignment->status != 'completed')
                            (Overdue)
                        @endif
                    @else
                        No deadline
                    @endif
                </td>
            </tr>
            <tr>
                <th>Outlet</th>
                <td>{{ $assignment->outlet->name }}</td>
                <th>Reference</th>
                <td>{{ $assignment->assignment_reference ?: 'N/A' }}</td>
            </tr>
        </table>
        
        @if($assignment->notes)
            <div>
                <strong>Notes:</strong>
                <div class="notes">{{ $assignment->notes }}</div>
            </div>
        @endif
        
        @if($assignment->report)
            <div style="margin-top: 15px; color: green;">
                Report completed on {{ $assignment->report->completed_at->format('M d, Y') }}
            </div>
        @endif
    </div>
    
    <div class="section">
        <h3>Report Rules</h3>
        <table>
            <thead>
                <tr>
                    <th style="width: 40%">Title</th>
                    <th style="width: 50%">Description</th>
                    <th style="width: 10%">Requires Photo</th>
                </tr>
            </thead>
            <tbody>
                @foreach($assignment->template->rules as $rule)
                    <tr>
                        <td>{{ $rule->title }}</td>
                        <td>{{ $rule->description ?: '-' }}</td>
                        <td style="text-align: center">{{ $rule->requires_photo ? 'Yes' : 'No' }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    
    @if($assignment->report)
        <div class="section">
            <h3>Completed Report</h3>
            <table>
                <thead>
                    <tr>
                        <th style="width: 30%">Rule</th>
                        <th style="width: 55%">Response</th>
                        <th style="width: 15%">Evidence</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($assignment->report->responses as $response)
                        <tr>
                            <td>{{ $response->rule->title }}</td>
                            <td>{{ $response->response }}</td>
                            <td style="text-align: center">{{ $response->photo_path ? 'Yes' : 'No' }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
    
    <div class="footer">
        Generated on {{ now()->format('F d, Y H:i:s') }} | Page 1
    </div>
</body>
</html>