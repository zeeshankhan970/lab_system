<?php
include 'config.php';

$id = $_GET['id'];

$patient = mysqli_fetch_assoc(mysqli_query($conn, "
SELECT * FROM patients WHERE id='$id'
"));

$tests = mysqli_query($conn, "
SELECT * FROM patient_tests WHERE patient_id='$id'
");
?>

<!DOCTYPE html>
<html>

<head>

    <title>Receipt</title>

    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-100">

    <div class="max-w-2xl mx-auto bg-white p-8 mt-10 shadow">

        <h1 class="text-3xl font-bold text-center mb-6">
            Laboratory Receipt
        </h1>

        <p><strong>Patient ID:</strong>
            <?php echo 'LAB-0' . str_pad($patient['id'], 3, '0', STR_PAD_LEFT); ?>
        </p>

        </p>
        <p><strong>Patient:</strong>
            <?php echo $patient['patient_name']; ?>
        </p>

        <p><strong>Phone:</strong>
            <?php echo $patient['phone']; ?>
        </p>

        <p><strong>Doctor:</strong>
            <?php echo $patient['doctor_reference']; ?>
        </p>

        <hr class="my-4">

        <table class="w-full border">

            <tr class="bg-gray-200">

                <th class="border p-2">Test</th>
                <th class="border p-2">Price</th>

            </tr>

            <?php while ($row = mysqli_fetch_assoc($tests)) { ?>

                <tr>

                    <td class="border p-2">
                        <?php echo $row['test_name']; ?>
                    </td>

                    <td class="border p-2">
                        <?php echo $row['test_price']; ?>
                    </td>

                </tr>

            <?php } ?>

        </table>

        <div class="mt-6">

            <p>
                <strong>Subtotal:</strong>
                <?php echo $patient['subtotal']; ?>
            </p>

            <p>
                <strong>Discount:</strong>
                <?php echo $patient['discount']; ?>
            </p>

            <p class="text-2xl font-bold">
                Grand Total:
                <?php echo $patient['grand_total']; ?>
            </p>

        </div>
        <div class="flex justify-between gap-4 mt-6">
            <button
                onclick="window.print()"
                class="bg-blue-600 text-white px-6 py-2 rounded mt-6">

                Print Receipt
            </button>
            <a href = "pages/dashboard.php"
                class="bg-gray-200 text-gray-700 px-6 py-2 rounded mt-6">
                <i class="fas fa-tachometer-alt"></i> Dashboard
            </a>
            <a href="pages/patients.php"
                class="bg-blue-600 text-white px-6 py-2 rounded mt-6">
                <i class="fas fa-tachometer-alt"></i> Back
            </a>
        </div>

    </div>

</body>

</html>