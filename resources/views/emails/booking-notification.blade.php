<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>New Booking Request</title>
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
        .booking-details {
            background-color: white;
            padding: 15px;
            border-radius: 5px;
            margin: 15px 0;
        }
        .field {
            margin-bottom: 10px;
        }
        .field-label {
            font-weight: bold;
            color: #dc2626;
        }
        .footer {
            text-align: center;
            margin-top: 20px;
            padding-top: 20px;
            border-top: 1px solid #ddd;
            color: #666;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>New Photography Booking Request</h1>
        <p>You have received a new booking request from your website</p>
    </div>
    
    <div class="content">
        <div class="booking-details">
            <div class="field">
                <span class="field-label">Name:</span>
                <span>{{ $bookingData['name'] }}</span>
            </div>
            
            <div class="field">
                <span class="field-label">Email:</span>
                <span>{{ $bookingData['email'] }}</span>
            </div>
            
            <div class="field">
                <span class="field-label">Phone:</span>
                <span>{{ $bookingData['phone'] }}</span>
            </div>
            
            <div class="field">
                <span class="field-label">Event Type:</span>
                <span>{{ ucfirst($bookingData['event_type']) }}</span>
            </div>
            
            <div class="field">
                <span class="field-label">Event Date:</span>
                <span>{{ $bookingData['event_date'] }}</span>
            </div>
            
            <div class="field">
                <span class="field-label">Location:</span>
                <span>{{ $bookingData['location'] }}</span>
            </div>
            
            @if(isset($bookingData['guests']) && $bookingData['guests'])
            <div class="field">
                <span class="field-label">Number of Guests:</span>
                <span>{{ $bookingData['guests'] }}</span>
            </div>
            @endif
            
            @if(isset($bookingData['budget']) && $bookingData['budget'])
            <div class="field">
                <span class="field-label">Budget Range:</span>
                <span>{{ $bookingData['budget'] }}</span>
            </div>
            @endif
            
            @if(isset($bookingData['message']) && $bookingData['message'])
            <div class="field">
                <span class="field-label">Message:</span>
                <p style="margin-top: 5px; white-space: pre-wrap;">{{ $bookingData['message'] }}</p>
            </div>
            @endif
        </div>
        
        <div class="footer">
            <p>This booking request was submitted from your Fotomimi website.</p>
            <p>Please respond to the client at: <a href="mailto:{{ $bookingData['email'] }}">{{ $bookingData['email'] }}</a></p>
        </div>
    </div>
</body>
</html> 