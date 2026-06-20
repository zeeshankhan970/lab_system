<?php
include 'config.php';

$id = $_GET['id'];

mysqli_query($conn,"
DELETE FROM patients WHERE id='$id'
");

mysqli_query($conn,"
DELETE FROM patient_test_results WHERE patient_id='$id'
");
mysqli_query($conn,"
DELETE FROM patient_tests WHERE patient_id='$id'
");

header("Location: pages/patients.php");
?>