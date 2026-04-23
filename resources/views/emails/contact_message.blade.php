<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>New Contact Message</title>
</head>
<body style="font-family: Arial, sans-serif">

    <h2>New Contact Message</h2>

    <p><strong>Name:</strong> {{ $name }}</p>
    <p><strong>Email:</strong> {{ $email }}</p>
    <p><strong>Subject:</strong> {{ $subjectText }}</p>

    <hr>

    <p>{{ $messageText }}</p>

</body>
</html>
