<html>
<head>
    <title>Event Registrations</title>
</head>
<body>

<form method="GET" action="">
    <label for="user">Employee Name:</label>
    <input type="text" id="user" name="user">
    <label for="event">Event Name:</label>
    <input type="text" id="event" name="event">
    <label for="date">Date:</label>
    <input type="date" id="date" name="date">
    <button type="submit">Filter</button>
</form>

<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "event_management";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$whereClauses = [];
if (!empty($_GET['user'])) {
    $user = $conn->real_escape_string($_GET['user']);
    $whereClauses[] = "users.name LIKE '%$user%'";
}
if (!empty($_GET['event'])) {
    $event = $conn->real_escape_string($_GET['event']);
    $whereClauses[] = "events.name LIKE '%$event%'";
}
if (!empty($_GET['date'])) {
    $date = $conn->real_escape_string($_GET['date']);
    $whereClauses[] = "events.date = '$date'";
}

$whereSql = '';
if (count($whereClauses) > 0) {
    $whereSql = 'WHERE ' . implode(' AND ', $whereClauses);
}

$sql = "SELECT users.name AS employee, users.email AS email, events.name AS event, events.date, participations.participation_fee AS fee
        FROM participations
        JOIN users ON participations.user_id = users.id
        JOIN events ON participations.event_id = events.id
        $whereSql";

$result = $conn->query($sql);

echo "<table border='1'>
<tr>
<th>Employee</th>
<th>Email</th>
<th>Event</th>
<th>Date</th>
<th>Fee</th>
</tr>";

$totalFee = 0;
while ($row = $result->fetch_assoc()) {
    echo "<tr>
    <td>" . $row['employee'] . "</td>
    <td>" . $row['email'] . "</td>
    <td>" . $row['event'] . "</td>
    <td>" . $row['date'] . "</td>
    <td>" . $row['fee'] . "</td>
    </tr>";
    $totalFee += $row['fee'];
}
echo "<tr>
<td colspan='4'>Total Fee</td>
<td>$totalFee</td>
</tr>";
echo "</table>";

$conn->close();
?>

</body>
</html>