<?php
include '../config.php';

/* ======================
   UPDATE PROCESS
====================== */
if (isset($_POST['update'])) {

    $id = $_POST['id'];

    mysqli_query($conn, "UPDATE patients SET
        patient_name='{$_POST['patient_name']}',
        age='{$_POST['age']}',
        gender='{$_POST['gender']}',
        doctor_reference='{$_POST['doctor_reference']}',
        phone='{$_POST['phone']}',
        subtotal='{$_POST['subtotal']}',
        discount='{$_POST['discount']}',
        grand_total='{$_POST['grand_total']}'
        WHERE id='$id'
    ");

    /* DELETE OLD TESTS */
    mysqli_query($conn, "DELETE FROM patient_tests WHERE patient_id='$id'");

    /* INSERT NEW TESTS (PRICE FROM DB ONLY) */
    foreach ($_POST['test_name'] as $i => $test) {

        if ($test != '') {

            $q = mysqli_query($conn, "SELECT test_price FROM tests WHERE test_name='$test'");
            $r = mysqli_fetch_assoc($q);
            $price = $r['test_price'];

            mysqli_query($conn, "INSERT INTO patient_tests
            (patient_id, test_name, test_price)
            VALUES
            ('$id', '$test', '$price')");
        }
    }

    header("Location: patients.php");
    exit();
}

include 'header.php';
include 'sidebar.php';


/* ======================
   GET DATA
====================== */

if (!isset($_GET['id'])) {
    die("ID Missing");
}

$id = $_GET['id'];

$patientQuery = mysqli_query($conn, "SELECT * FROM patients WHERE id='$id'");
if (mysqli_num_rows($patientQuery) == 0) {
    die("No Record Found");
}

$patient = mysqli_fetch_assoc($patientQuery);

$doctors = mysqli_query($conn, "SELECT * FROM doctors");
$allTests = mysqli_query($conn, "SELECT * FROM tests");
$patientTests = mysqli_query($conn, "SELECT * FROM patient_tests WHERE patient_id='$id'");
?>
<body class="bg-gradient-to-br from-slate-100 to-blue-50 min-h-screen p-6 font-sans">
 
<div class="max-w-3xl mx-auto bg-white rounded-2xl shadow-md border border-slate-200 max-h-[90vh] overflow-y-auto">
 
    <!-- Header -->
    <div class="px-8 pt-7 pb-6 bg-gradient-to-r from-blue-600 to-blue-500 rounded-t-2xl">
        <div class="flex items-center gap-3">
            <div class="bg-white/20 p-2 rounded-lg">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
            </div>
            <div>
                <h2 class="text-lg font-semibold text-white tracking-tight">Update Patient</h2>
                <p class="text-xs text-blue-100 mt-0.5">Edit patient details and assigned tests</p>
            </div>
        </div>
    </div>
 
    <form method="POST" class="px-8 py-6 space-y-5">
 
        <input type="hidden" name="id" value="<?= $patient['id'] ?>">
 
        <!-- Patient Info Section -->
        <div class="bg-slate-50 rounded-xl p-5 border border-slate-100">
            <h3 class="text-xs font-semibold uppercase tracking-widest text-blue-500 mb-4 flex items-center gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                Patient Information
            </h3>
            <div class="grid grid-cols-2 gap-3">
 
                <div class="flex flex-col gap-1">
                    <label class="text-xs font-medium text-slate-500">Full Name</label>
                    <input type="text" name="patient_name" value="<?= $patient['patient_name'] ?>"
                        placeholder="Patient name"
                        class="border border-slate-200 bg-white rounded-lg px-3 py-2 text-sm text-slate-800 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition shadow-sm">
                </div>
 
                <div class="flex flex-col gap-1">
                    <label class="text-xs font-medium text-slate-500">Age</label>
                    <input type="text" name="age" value="<?= $patient['age'] ?>"
                        placeholder="Age"
                        class="border border-slate-200 bg-white rounded-lg px-3 py-2 text-sm text-slate-800 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition shadow-sm">
                </div>
 
                <div class="flex flex-col gap-1">
                    <label class="text-xs font-medium text-slate-500">Gender</label>
                    <select name="gender"
                        class="border border-slate-200 bg-white rounded-lg px-3 py-2 text-sm text-slate-800 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition shadow-sm appearance-none">
                        <option <?= $patient['gender'] == 'Male' ? 'selected' : '' ?>>Male</option>
                        <option <?= $patient['gender'] == 'Female' ? 'selected' : '' ?>>Female</option>
                    </select>
                </div>
 
                <div class="flex flex-col gap-1">
                    <label class="text-xs font-medium text-slate-500">Referring Doctor</label>
                    <select name="doctor_reference"
                        class="border border-slate-200 bg-white rounded-lg px-3 py-2 text-sm text-slate-800 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition shadow-sm appearance-none">
                        <?php while ($d = mysqli_fetch_assoc($doctors)) { ?>
                            <option <?= $patient['doctor_reference'] == $d['doctor_name'] ? 'selected' : '' ?>>
                                <?= $d['doctor_name'] ?>
                            </option>
                        <?php } ?>
                    </select>
                </div>
 
                <div class="flex flex-col gap-1 col-span-2">
                    <label class="text-xs font-medium text-slate-500">Phone Number</label>
                    <input type="text" name="phone" value="<?= $patient['phone'] ?>"
                        placeholder="Phone"
                        class="border border-slate-200 bg-white rounded-lg px-3 py-2 text-sm text-slate-800 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition shadow-sm">
                </div>
 
            </div>
        </div>
 
        <!-- Tests Section -->
        <div class="bg-slate-50 rounded-xl p-5 border border-slate-100">
            <h3 class="text-xs font-semibold uppercase tracking-widest text-blue-500 mb-4 flex items-center gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                Assigned Tests
            </h3>
 
            <div id="testRows" class="space-y-2">
 
                <?php while ($pt = mysqli_fetch_assoc($patientTests)) { ?>
 
                    <div class="flex gap-2 items-center test-row">
 
                        <select name="test_name[]"
                            class="border border-slate-200 bg-white rounded-lg px-3 py-2 text-sm text-slate-800 flex-1 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition shadow-sm testSelect">
                            <?php mysqli_data_seek($allTests, 0);
                            while ($t = mysqli_fetch_assoc($allTests)) { ?>
                                <option value="<?= $t['test_name'] ?>"
                                    data-price="<?= $t['test_price'] ?>"
                                    <?= $pt['test_name'] == $t['test_name'] ? 'selected' : '' ?>>
                                    <?= $t['test_name'] ?>
                                </option>
                            <?php } ?>
                        </select>
 
                        <!-- PRICE IS LOCKED -->
                        <input type="number" name="test_price[]" value="<?= $pt['test_price'] ?>"
                            class="border border-slate-200 bg-slate-100 rounded-lg px-3 py-2 text-sm text-slate-500 w-28 price cursor-not-allowed" readonly>
 
                        <button type="button" onclick="removeRow(this)"
                            class="bg-red-50 hover:bg-red-100 text-red-500 border border-red-200 px-3 py-2 rounded-lg text-sm font-medium transition flex items-center gap-1">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                            Remove
                        </button>
 
                    </div>
 
                <?php } ?>
 
            </div>
 
            <button type="button" onclick="addRow()"
                class="mt-3 inline-flex items-center gap-1.5 bg-emerald-50 hover:bg-emerald-100 text-emerald-700 border border-emerald-200 px-4 py-2 rounded-lg text-sm font-medium transition">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                Add Test
            </button>
        </div>
 
        <!-- Totals Section -->
        <div class="bg-slate-50 rounded-xl p-5 border border-slate-100">
            <h3 class="text-xs font-semibold uppercase tracking-widest text-blue-500 mb-4 flex items-center gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                Billing Summary
            </h3>
            <div class="grid grid-cols-3 gap-3">
 
                <div class="flex flex-col gap-1">
                    <label class="text-xs font-medium text-slate-500">Subtotal</label>
                    <input type="number" name="subtotal" id="subtotal"
                        value="<?= $patient['subtotal'] ?>"
                        class="border border-slate-200 bg-slate-100 rounded-lg px-3 py-2 text-sm text-slate-500 cursor-not-allowed w-full" readonly>
                </div>
 
                <div class="flex flex-col gap-1">
                    <label class="text-xs font-medium text-slate-500">Discount</label>
                    <input type="number" name="discount" id="discount"
                        value="<?= $patient['discount'] ?>"
                        placeholder="0"
                        class="border border-slate-200 bg-white rounded-lg px-3 py-2 text-sm text-slate-800 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition shadow-sm w-full">
                </div>
 
                <div class="flex flex-col gap-1">
                    <label class="text-xs font-medium text-slate-500">Grand Total</label>
                    <input type="number" name="grand_total" id="grand_total"
                        value="<?= $patient['grand_total'] ?>"
                        class="border border-blue-200 bg-blue-50 rounded-lg px-3 py-2 text-sm font-bold text-blue-700 cursor-not-allowed w-full" readonly>
                </div>
 
            </div>
        </div>
 
        <!-- Action Buttons -->
        <div class="flex gap-3 pt-1 pb-2">
            <a href="javascript:history.back()"
                class="flex-1 text-center bg-white hover:bg-slate-50 active:bg-slate-100 text-slate-600 font-medium py-2.5 rounded-xl text-sm border border-slate-300 transition">
                Cancel
            </a>
            <button type="submit" name="update"
                class="flex-1 bg-blue-600 hover:bg-blue-700 active:bg-blue-800 text-white font-medium py-2.5 rounded-xl text-sm tracking-wide transition shadow-sm">
                Update Patient
            </button>
        </div>
 
    </form>
 
</div>
 
<script>
 
function addRow() {
 
    let options = `<?php mysqli_data_seek($allTests, 0);
    while ($t = mysqli_fetch_assoc($allTests)) { ?>
<option value="<?= $t['test_name'] ?>" data-price="<?= $t['test_price'] ?>">
<?= $t['test_name'] ?>
</option>
<?php } ?>`;
 
    let row = `
<div class="flex gap-2 items-center test-row">
 
<select name="test_name[]" class="border border-slate-200 bg-white rounded-lg px-3 py-2 text-sm text-slate-800 flex-1 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition shadow-sm testSelect">
<option value="">Select Test</option>
${options}
</select>
 
<input type="number" name="test_price[]" class="border border-slate-200 bg-slate-100 rounded-lg px-3 py-2 text-sm text-slate-500 w-28 price cursor-not-allowed" readonly>
 
<button type="button" onclick="removeRow(this)" class="bg-red-50 hover:bg-red-100 text-red-500 border border-red-200 px-3 py-2 rounded-lg text-sm font-medium transition flex items-center gap-1"><svg xmlns='http://www.w3.org/2000/svg' class='w-3.5 h-3.5' fill='none' viewBox='0 0 24 24' stroke='currentColor'><path stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16'/></svg> Remove</button>
 
</div>`;
 
    document.getElementById('testRows').insertAdjacentHTML('beforeend', row);
}
 
function removeRow(btn) {
 
    let rows = document.querySelectorAll('.test-row');
 
    if (rows.length <= 1) {
        alert("At least one test required");
        return;
    }
 
    btn.closest('.test-row').remove();
    calculate();
}
 
document.addEventListener('change', function(e) {
    if (e.target.classList.contains('testSelect')) {
 
        let price = e.target.options[e.target.selectedIndex].dataset.price || 0;
        e.target.closest('.test-row').querySelector('.price').value = price;
 
        calculate();
    }
});
 
document.addEventListener('input', calculate);
 
function calculate() {
 
    let prices = document.querySelectorAll('.price');
    let subtotal = 0;
 
    prices.forEach(p => {
        subtotal += parseFloat(p.value || 0);
    });
 
    document.getElementById('subtotal').value = subtotal;
 
    let discount = parseFloat(document.getElementById('discount').value || 0);
 
    document.getElementById('grand_total').value = subtotal - discount;
}
 
</script>

</div>
<?php include 'footer.php'; ?>