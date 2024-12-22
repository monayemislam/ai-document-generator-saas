<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <style>
        body {
            font-family: sans-serif;
            padding: 2cm;
        }
        .header {
            text-align: center;
            margin-bottom: 2cm;
        }
        .content {
            margin-bottom: 1cm;
        }
        .row {
            margin-bottom: 0.5cm;
        }
        .label {
            font-weight: bold;
            display: inline-block;
            width: 150px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>{{ $document->project_title }}</h1>
        <p>Generated: {{ $date }}</p>
    </div>

    <div class="content">
        <h2>Project Details</h2>
        <div class="row">
            <span class="label">Type:</span>
            {{ $document->project_type }}
        </div>
        <div class="row">
            <span class="label">Start Date:</span>
            {{ $document->start_date }}
        </div>
        <div class="row">
            <span class="label">End Date:</span>
            {{ $document->end_date }}
        </div>
    </div>

    <div class="content">
        <h2>Client Information</h2>
        <div class="row">
            <span class="label">Name:</span>
            {{ $document->client->name ?? 'N/A' }}
        </div>
        <div class="row">
            <span class="label">Email:</span>
            {{ $document->email }}
        </div>
    </div>
</body>
</html>
