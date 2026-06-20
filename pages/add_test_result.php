<?php
include '../config.php';
include 'header.php';
include 'sidebar.php';
if (!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit();
}
// Get patient ID from URL
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($id <= 0) {
    die("
    <div style='font-family:sans-serif;padding:40px;text-align:center'>
        <h2 style='color:red'>Invalid Patient ID</h2>
        <a href='reports.php'>Back to Reports</a>
    </div>
    ");
}

/* =========================
   GET PATIENT + TESTS
========================= */

$patientQuery = mysqli_query($conn, "SELECT * FROM patients WHERE id='$id'");

if (!$patientQuery) {
    die("Database Error: " . mysqli_error($conn));
}

$patient = mysqli_fetch_assoc($patientQuery);

if (!$patient) {
    die("
    <div style='font-family:sans-serif;padding:40px;text-align:center'>
        <h2 style='color:red'>Patient Not Found</h2>
        <a href='reports.php'>Back to Reports</a>
    </div>
    ");
}

/* =========================
   SAVE RESULTS
========================= */

if (isset($_POST['save_results'])) {

    if (isset($_POST['result']) && is_array($_POST['result'])) {

        foreach ($_POST['result'] as $pt_id => $params) {

            $pt_id = (int)$pt_id;

            if (!is_array($params)) continue;

            foreach ($params as $parameter_id => $value) {

                $parameter_id = (int)$parameter_id;
                $value = mysqli_real_escape_string($conn, trim($value));

                $check = mysqli_query(
                    $conn,
                    "SELECT id FROM patient_test_results
                     WHERE patient_id='$id'
                     AND patient_test_id='$pt_id'
                     AND parameter_id='$parameter_id'"
                );

                if (mysqli_num_rows($check) > 0) {

                    mysqli_query(
                        $conn,
                        "UPDATE patient_test_results
                         SET result_value='$value'
                         WHERE patient_id='$id'
                         AND patient_test_id='$pt_id'
                         AND parameter_id='$parameter_id'"
                    );

                } else {
                    foreach ($_POST as $key => $value) {
                     $$key = $value;
                    }

                    mysqli_query(
                        $conn,
                        "INSERT INTO patient_test_results
                         (patient_id, patient_test_id, parameter_id, result_value)
                         VALUES ('$id', '$pt_id', '$parameter_id', '$value')"
                    );
                }
            }
        }
    }

    // Hide patient from report list if checkbox is checked
    $report_completed = isset($_POST['report_completed']) ? 1 : 0;

    mysqli_query($conn, "
        UPDATE patients
        SET report_completed = '$report_completed'
        WHERE id = '$id'
    ");
}

/* =========================
   FETCH TESTS (after POST so fresh)
========================= */

$tests = mysqli_query($conn, "SELECT * FROM patient_tests WHERE patient_id='$id'");

if (!$tests) {
    die("Database Error: " . mysqli_error($conn));
}

// Pre-load all tests into array so $row is never overwritten
$allTests = [];
while ($t = mysqli_fetch_assoc($tests)) {
    $allTests[] = $t;
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Patient Report - <?= htmlspecialchars($patient['patient_name']) ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" />
    <style>
        @media print {
            .no-print {
                display: none !important;
            }

            body {
                background: white;
            }

            .print-container {
                box-shadow: none;
                border: none;
            }
        }

        .view-mode {
            display: block;
        }

        .edit-mode {
            display: none;
        }

        .editing .view-mode {
            display: none;
        }

        .editing .edit-mode {
            display: block;
        }
    </style>
</head>

<body class="bg-gray-100 min-h-screen py-8">

    <!-- Toast -->
    <div id="toast"
        class="fixed bottom-6 right-6 bg-green-600 text-white px-5 py-3 rounded-xl text-sm font-medium hidden z-[999] shadow-lg">
        <i class="fas fa-check-circle mr-2"></i><span id="toastMsg"></span>
    </div>

    <div class="w-full max-w-6xl mx-auto overflow-y-auto print-container bg-white rounded-2xl shadow border border-gray-200">

        <!-- HEADER -->
        <div class="bg-blue-700 text-white px-8 py-6 rounded-t-2xl flex justify-between items-center flex-wrap gap-3">
            <div>
                <h1 class="text-2xl font-bold">Laboratory Patient Report</h1>
                <p class="text-blue-200 text-sm mt-0.5">Diagnostic & Medical Laboratory</p>
            </div>
            <a href="reports.php"
                class="no-print bg-white/20 hover:bg-white/30 text-white text-sm px-4 py-2 rounded-lg flex items-center gap-2 transition">
                <i class="fas fa-arrow-left"></i> All Reports
            </a>
        </div>

        <div class="p-6">
            <form method="POST" action="add_test_result.php?id=<?= $id ?>">

                <!-- PATIENT INFO -->
                <div class="flex items-center gap-6 mb-8 bg-gray-50 border rounded-xl p-4">
                    <div>
                        <p class="text-xs text-gray-500 mb-1">Patient ID</p>
                        <p class="font-bold text-gray-800"><?= 'LAB-' . str_pad($patient['id'], 3, '0', STR_PAD_LEFT) ?></p>
                    </div>
                    <div class="w-px h-10 bg-gray-300"></div>
                    <div>
                        <p class="text-xs text-gray-500 mb-1">Patient Name</p>
                        <p class="font-bold text-gray-800"><?= htmlspecialchars($patient['patient_name']) ?></p>
                    </div>
                </div>

                <!-- TESTS -->
                <div class="flex items-center gap-2 mb-5">
                    <div class="w-1.5 h-6 bg-blue-600 rounded-full"></div>
                    <h2 class="text-lg font-bold text-gray-800">Test Results</h2>
                </div>

                <?php if (count($allTests) === 0): ?>
                    <div class="text-center py-10 text-gray-400">
                        <i class="fas fa-flask text-4xl mb-3 block"></i>
                        <p>No tests assigned to this patient.</p>
                    </div>
                <?php endif; ?>

                <?php foreach ($allTests as $testRow):

                    // Store patient_test id IMMEDIATELY before any other query runs
                    $pt_id      = (int)$testRow['id'];
                    $pt_name    = $testRow['test_name'];
                    $pt_price   = $testRow['test_price'];

                    $safe_name  = mysqli_real_escape_string($conn, $pt_name);
                    $testData   = mysqli_fetch_assoc(
                        mysqli_query($conn, "SELECT * FROM tests WHERE test_name='$safe_name'")
                    );
                ?>

                    <div class="border rounded-xl mb-6 overflow-hidden">

                        <!-- Test Header -->
                        <div class="bg-blue-600 text-white px-5 py-3 flex justify-between items-center">
                            <span class="font-semibold"><?= htmlspecialchars($pt_name) ?></span>
                            <span class="text-blue-100 text-sm">Rs. <?= htmlspecialchars($pt_price) ?></span>
                        </div>

                        <div class="p-4">
                            <?php if ($testData): ?>
                                <?php
                                $test_id    = (int)$testData['id'];
                                $parameters = mysqli_query($conn, "SELECT * FROM parameters WHERE test_id='$test_id'");

                                // Pre-load parameters into array to avoid $param being overwritten
                                $allParams = [];
                                while ($p = mysqli_fetch_assoc($parameters)) {
                                    $allParams[] = $p;
                                }
                                ?>

                                <?php if (count($allParams) > 0): ?>
                                    <div class="max-h-[400px] overflow-y-auto border border-gray-200 rounded-lg">
                                        <table class="w-full text-sm border-collapse">
                                            <thead>
                                                <tr class="bg-gray-50 text-gray-500 text-xs uppercase tracking-wide">
                                                    <th class="border border-gray-200 px-3 py-2 text-left">Parameter</th>
                                                    <th class="border border-gray-200 px-3 py-2 text-left">Result</th>
                                                    <th class="border border-gray-200 px-3 py-2 text-left">Normal Range</th>
                                                    <th class="border border-gray-200 px-3 py-2 text-left">Unit</th>
                                                    <th class="border border-gray-200 px-3 py-2 text-center no-print">Edit</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php foreach ($allParams as $param):

                                                    $param_id  = (int)$param['id'];
                                                    $row_uid   = 'row-' . $pt_id . '-' . $param_id;

                                                    $resQuery  = mysqli_query($conn, "
                                            SELECT result_value FROM patient_test_results
                                            WHERE patient_id='$id'
                                            AND patient_test_id='$pt_id'
                                            AND parameter_id='$param_id'
                                            LIMIT 1
                                        ");
                                                    $resRow    = $resQuery ? mysqli_fetch_assoc($resQuery) : null;
                                                    $val       = $resRow['result_value'] ?? '';
                                                ?>
                                                    <tr class="editable-row hover:bg-gray-50" id="<?= $row_uid ?>">
                                                        <td class="border border-gray-200 px-3 py-2 font-medium text-gray-700">
                                                            <?= htmlspecialchars($param['parameter_name']) ?>
                                                        </td>
                                                        <td class="border border-gray-200 px-3 py-2">
                                                            <span class="view-mode <?= $val ? 'text-gray-800 font-medium' : 'text-gray-400 italic' ?>">
                                                                <?= $val ? htmlspecialchars($val) : 'Not entered' ?>
                                                            </span>
                                                            <input type="text"
                                                                name="result[<?= $pt_id ?>][<?= $param_id ?>]"
                                                                value="<?= htmlspecialchars($val) ?>"
                                                                class="edit-mode w-full border border-blue-400 px-2 py-1 rounded text-sm focus:outline-none focus:ring-1 focus:ring-blue-500 transition-colors duration-200"
                                                                placeholder="Enter result">
                                                        </td>
                                                        <td class="border border-gray-200 px-3 py-2 text-gray-500">
                                                            <?= htmlspecialchars($param['normal_range']) ?>
                                                        </td>
                                                        <td class="border border-gray-200 px-3 py-2 text-gray-500">
                                                            <?= htmlspecialchars($param['unit']) ?>
                                                        </td>
                                                        <td class="border border-gray-200 px-3 py-2 text-center no-print">
                                                            <button type="button" onclick="toggleEdit('<?= $row_uid ?>')"
                                                                class="edit-btn text-blue-600 hover:text-blue-800 text-xs font-medium">
                                                                <i class="fas fa-pen"></i> Edit
                                                            </button>
                                                        </td>
                                                    </tr>
                                                <?php endforeach; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                <?php else: ?>
                                    <p class="text-gray-400 text-sm py-2">No parameters found for this test.</p>
                                <?php endif; ?>

                            <?php else: ?>
                                <p class="text-gray-400 text-sm py-2">Test definition not found in database.</p>
                            <?php endif; ?>
                        </div>

                    </div>

                <?php endforeach; ?>

                <!-- ACTION BUTTONS -->
                <div class="flex flex-wrap gap-3 mt-8 no-print">
                    <button type="submit" name="save_results"
                        class="bg-green-600 hover:bg-green-700 text-white px-5 py-2.5 rounded-lg text-sm flex items-center gap-2 transition">
                        <i class="fas fa-save"></i> Save Results
                    </button>
                    <button type="button" onclick="window.print()"
                        class="bg-blue-600 hover:bg-blue-700 text-white px-5 py-2.5 rounded-lg text-sm flex items-center gap-2 transition">
                        <i class="fas fa-print"></i> Print Report
                    </button>
                    <a href="reports.php"
                        class="bg-gray-600 hover:bg-gray-700 text-white px-5 py-2.5 rounded-lg text-sm flex items-center gap-2 transition">
                        <i class="fas fa-arrow-left"></i> Back
                    </a>
                </div>
                <div class="mt-4">
                    <label class="flex items-center gap-2">
                        <input type="checkbox"
                            name="report_completed"
                            value="1"
                            class="w-4 h-4">
                        <span>Report Completed</span>
                    </label>
                </div>

            </form>
        </div>
    </div>

    <script>
        function toggleEdit(rowId) {
            const row = document.getElementById(rowId);
            const isEditing = row.classList.contains('editing');
            if (isEditing) {
                row.classList.remove('editing');
                const btn = row.querySelector('.edit-btn');
                btn.innerHTML = '<i class="fas fa-pen"></i> Edit';
                btn.classList.replace('text-green-600', 'text-blue-600');
                btn.classList.replace('hover:text-green-800', 'hover:text-blue-800');
                const input = row.querySelector('input[type=text]');
                const span = row.querySelector('.view-mode');
                span.textContent = input.value || 'Not entered';
                span.className = 'view-mode ' + (input.value ? 'text-gray-800 font-medium' : 'text-gray-400 italic');
            } else {
                row.classList.add('editing');
                const btn = row.querySelector('.edit-btn');
                btn.innerHTML = '<i class="fas fa-check"></i> Done';
                btn.classList.replace('text-blue-600', 'text-green-600');
                btn.classList.replace('hover:text-blue-800', 'hover:text-green-800');
                row.querySelector('input[type=text]').focus();
            }
        }

        function showToast(msg) {
            const toast = document.getElementById('toast');
            document.getElementById('toastMsg').textContent = msg;
            toast.classList.remove('hidden');
            setTimeout(() => toast.classList.add('hidden'), 3000);
        }
    </script>

    </div>
    <?php include 'footer.php'; ?>