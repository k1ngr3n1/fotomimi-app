<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>New Contact Form Submission</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }
        .header {
            background-color: #dc2626;
            color: white;
            padding: 20px;
            text-align: center;
            border-radius: 8px 8px 0 0;
        }
        .content {
            background-color: #f9f9f9;
            padding: 20px;
            border-radius: 0 0 8px 8px;
        }
        .field {
            margin-bottom: 15px;
        }
        .label {
            font-weight: bold;
            color: #555;
        }
        .value {
            margin-top: 5px;
            padding: 10px;
            background-color: white;
            border-radius: 4px;
            border-left: 4px solid #dc2626;
        }
        .footer {
            margin-top: 20px;
            padding-top: 20px;
            border-top: 1px solid #ddd;
            text-align: center;
            color: #666;
            font-size: 14px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>New Contact Form Submission</h1>
        <p>Fotomimi Photography</p>
    </div>
    
    <div class="content">
        <p>A new contact form has been submitted on your website.</p>
        
        <div class="field">
            <div class="label">Name:</div>
            <div class="value">{{ $contactData['name'] }}</div>
        </div>
        
        <div class="field">
            <div class="label">Email:</div>
            <div class="value">{{ $contactData['email'] }}</div>
        </div>
        
        @if(!empty($contactData['phone']))
        <div class="field">
            <div class="label">Phone:</div>
            <div class="value">{{ $contactData['phone'] }}</div>
        </div>
        @endif
        
        <div class="field">
            <div class="label">Message:</div>
            <div class="value">{{ $contactData['message'] }}</div>
        </div>
        
        <div class="footer">
            <p>This message was sent from the contact form on your website.</p>
            <p>Submitted at: {{ now()->format('F j, Y \a\t g:i A') }}</p>
        </div>
    </div>
</body>
</html> 