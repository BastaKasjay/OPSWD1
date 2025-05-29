<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>

    <h1>Patient Information:</h1>
    <p>First Name: {{ $name }}</p>
    <p>Middle Name: {{ $middle_name }}</p>
    <p>Last Name: {{ $last_name }}</p>
    <p>Sex: {{ $sex }}</p>
    <p>Age: {{ $age }}</p>
    <p>pwd: {{ $pwd }}</p>
    <p>_4ps: {{ $_4ps }}</p>
    <p>Address: {{ $address }}</p>
    <p>Contact Number: {{ $contact_number }}</p>
    <p>Valid ID: {{ $valid_id }}</p>
    <p>Municipality: {{ $municipality }}</p>
    <p>Assistance_type: {{ $assistance_type }}</p>
    <p>Assistance_category: {{ $assistance_category }}</p>

    <h2>Client Information</h2>
    <p>First Name: {{ $client_name }}</p>
    <p>Middle name: {{ $client_middle_name }}</p>
    <p>Last Name: {{ $client_last_name }}</p>
    <p>Relationship: {{ $client_relationship }}</p>


</body>
</html>