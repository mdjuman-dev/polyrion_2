<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $subject }}</title>
</head>
<body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333; max-width: 600px; margin: 0 auto; padding: 20px;">
    @if($logoUrl)
    <div style="text-align: center; margin-bottom: 30px;">
        <img src="{{ $logoUrl }}" alt="{{ $appName }}" style="max-width: 200px; height: auto;">
    </div>
    @endif
    
    <div style="background: #ffffff; border: 1px solid #e0e0e0; border-radius: 8px; padding: 30px;">
        <h2 style="color: #333; margin-top: 0;">{{ $subject }}</h2>
        
        <p style="margin: 20px 0;">{{ $greeting }}</p>
        
        <p style="margin: 20px 0;">{{ $line1 }}</p>
        
        <div style="background: #f5f5f5; border: 2px dashed #333; border-radius: 8px; padding: 20px; text-align: center; margin: 30px 0;">
            <p style="font-size: 32px; font-weight: bold; letter-spacing: 5px; color: #333; margin: 0;">{{ $otp }}</p>
        </div>
        
        <p style="margin: 20px 0; color: #666;">{{ $line2 }}</p>
        
        <p style="margin: 20px 0; color: #666;">{{ $line3 }}</p>
        
        <hr style="border: none; border-top: 1px solid #e0e0e0; margin: 30px 0;">
        
        <p style="margin: 20px 0; color: #999; font-size: 12px;">{{ $salutation }}</p>
    </div>
    
    <div style="text-align: center; margin-top: 20px; color: #999; font-size: 12px;">
        <p>This is an automated email. Please do not reply to this message.</p>
    </div>
</body>
</html>

