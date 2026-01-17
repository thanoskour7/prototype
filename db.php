<?php

/*
 * Ρυθμίσεις της βασησ δεδομένων
*/

// Στοιχεία Συνδεσης
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "university_database";

// Δημιουργια συνδεσης
$conn = new mysqli($servername, $username, $password, $dbname);

// Σταματαει την εκτέλεση και εμφανίζει το error 
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

?>
