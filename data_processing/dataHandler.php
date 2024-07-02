<?php

include("EventImport.php");

$servername = 'localhost';
$username = 'root';
$password = '';
$dbname = 'event_management';

$importer = new EventImport($servername, $username, $password, $dbname);

$jsonData = file_get_contents('../data.json');
$data = json_decode($jsonData, true);

$importer->processData($data);

$importer->closeConnection();

echo "Data imported successfully!";
?>