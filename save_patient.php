<?php

include 'config.php';
$patient_name = $_POST['patient_name'];
$age = $_POST['age'];
$gender = $_POST['gender'];
$doctor_reference = $_POST['doctor_reference'];
$phone = $_POST['phone'];
$subtotal = $_POST['subtotal'];
$discount = $_POST['discount'];
$grand_total = $_POST['grand_total'];

$invoice_no = "INV-" . time();

$query = "INSERT INTO patients(
invoice_no,
patient_name,
age,
gender,
doctor_reference,
phone,
subtotal,
discount,
grand_total
)

VALUES(
'$invoice_no',
'$patient_name',
'$age',
'$gender',
'$doctor_reference',
'$phone',
'$subtotal',
'$discount',
'$grand_total'
)";

$result = mysqli_query($conn, $query);

if(!$result){

    die("Patient Insert Error: " . mysqli_error($conn));

}

$patient_id = mysqli_insert_id($conn);

$test_names = $_POST['test_name'];
$test_prices = $_POST['test_price'];

for($i=0; $i<count($test_names); $i++){

    $test_name = $test_names[$i];
    $test_price = $test_prices[$i];

    if($test_name != ''){

        mysqli_query($conn, "
        INSERT INTO patient_tests(
        patient_id,
        test_name,
        test_price
        )

        VALUES(
        '$patient_id',
        '$test_name',
        '$test_price'
        )
        ");

    }

}

header("Location: print_receipt.php?id=$patient_id");

?>