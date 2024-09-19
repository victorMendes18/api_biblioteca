<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Email Confirmation</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            color: #333;
            padding: 20px;
        }
        .email-container {
            background-color: #ffffff;
            padding: 20px;
            border-radius: 8px;
            max-width: 600px;
            margin: 0 auto;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        .email-header {
            text-align: center;
            margin-bottom: 20px;
        }
        .email-header h1 {
            color: #333;
        }
        .email-body {
            line-height: 1.6;
            font-size: 16px;
        }
        .email-body p {
            margin-bottom: 10px;
        }
        .email-footer {
            margin-top: 30px;
            text-align: center;
        }
        .button {
            background-color: #4CAF50;
            color: white;
            padding: 10px 20px;
            text-decoration: none;
            border-radius: 5px;
        }
        .button:hover {
            background-color: #45a049;
        }
    </style>
</head>
<body>

<div class="email-container">
    <div class="email-header">
        <h1>Email Confirmation</h1>
    </div>

    <div class="email-body">
        <p>Hello, {{ $nameUser }}</p>

        <p>Thank you for registering! Please click the button below to confirm your email address.</p>

        <p>
            <form action="{{ url('http://localhost:8000/api/users/confirmationEmail') }}" method="POST" style="display: inline;">
                <input type="hidden" name="token" value="{{ $token }}">
                <button type="submit" class="button">Confirm Your Email</button>
            </form>
        </p>

        <p>If the button doesn't work, you can manually use the following token to confirm your email:</p>
        <p><strong>Token:</strong> {{ $token }}</p>

        <p>Thank you, <br> The {{ config('app.name') }} Team</p>
    </div>

    <div class="email-footer">
        <p>&copy; {{ date('Y') }} {{ config('app.name') }}. All rights reserved.</p>
    </div>
</div>

</body>
</html>
