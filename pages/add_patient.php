<?php
include '../config.php';
include 'header.php';
include 'sidebar.php';
if (!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit();
}
$result = mysqli_query($conn, "SHOW TABLE STATUS LIKE 'patients'");
$row = mysqli_fetch_assoc($result);

$nextId = $row['Auto_increment'];
$patientCode = 'LAB-' . str_pad($nextId, 3, '0', STR_PAD_LEFT);

$doctors = mysqli_query($conn, "SELECT * FROM doctors");
$allTests = mysqli_query($conn, "SELECT * FROM tests");
?>
<body>
    <div class="w-full max-w-4xl overflow-y-auto mx-auto bg-white px-4 md:px-6 py-8">

        <form action="../save_patient.php" method="POST">

            <!-- Header -->
            <div class="bg-gradient-to-r from-blue-600 to-indigo-700 px-5 py-4 rounded-t-xl shadow-2xl overflow-hidden">
                <p class="text-xl font-bold text-white">New Patient Registration</p>
                <p class="text-blue-100 mt-2">Fill in patient details and select diagnostic tests</p>
            </div>

            <!-- Personal Information -->
            <div class="border border-gray-200 rounded-b-xl overflow-hidden mb-3.5">
                <div class="bg-gray-50 border-b border-gray-200 px-4 py-2.5 flex items-center gap-2">
                    <i class="fas fa-user text-blue-500 text-xs"></i>
                    <span class="text-xs font-bold text-gray-700 uppercase tracking-wide">Personal Information</span>
                </div>
                <div class="p-4">
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3">

                        <div class="flex flex-col gap-1">
                            <span class="text-xs font-medium text-gray-600 uppercase tracking-wider">Patient ID</span>
                            <div
                                class="flex items-center h-8 px-3 bg-blue-50 border border-blue-400 rounded-lg text-sm font-semibold text-blue-600">
                                <?php echo $patientCode; ?>
                            </div>
                            <input type="hidden" name="patient_id" value="<?php echo $patientCode; ?>">
                        </div>

                        <div class="flex flex-col gap-1">
                            <span class="text-xs font-medium text-gray-600 uppercase tracking-wider">Full Name</span>
                            <input type="text" name="patient_name" placeholder="Enter name" required
                                class="h-8 px-3 text-sm border border-gray-200 rounded-lg outline-none focus:border-blue-400 transition-colors w-full bg-white text-gray-800 placeholder-gray-300">
                        </div>

                        <div class="flex flex-col gap-1">
                            <span class="text-xs font-medium text-gray-600 uppercase tracking-wider">Age</span>
                            <input type="text" name="age" placeholder="Enter age" required
                                class="h-8 px-3 text-sm border border-gray-200 rounded-lg outline-none focus:border-blue-400 transition-colors w-full bg-white text-gray-800 placeholder-gray-300">
                        </div>

                        <div class="flex flex-col gap-1">
                            <span class="text-xs font-medium text-gray-600 uppercase tracking-wider">Gender</span>
                            <select name="gender"
                                class="h-8 px-3 text-sm border border-gray-200 rounded-lg outline-none focus:border-blue-400 transition-colors w-full bg-white text-gray-800 cursor-pointer">
                                <option value="Male">Male</option>
                                <option value="Female">Female</option>
                            </select>
                        </div>

                        <div class="flex flex-col gap-1">
                            <span class="text-xs font-medium text-gray-600 uppercase tracking-wider">Referring
                                Doctor</span>
                            <select name="doctor_reference"
                                class="h-8 px-3 text-sm border border-gray-200 rounded-lg outline-none focus:border-blue-400 transition-colors w-full bg-white text-gray-800 cursor-pointer">
                                <option value="">Select doctor</option>
                                <?php
                                mysqli_data_seek($doctors, 0);
                                while ($doctor = mysqli_fetch_assoc($doctors)) { ?>
                                    <option value="<?php echo $doctor['doctor_name']; ?>">
                                        <?php echo $doctor['doctor_name']; ?></option>
                                <?php } ?>
                            </select>
                        </div>

                        <div class="flex flex-col gap-1">
                            <span class="text-xs font-medium text-gray-600 uppercase tracking-wider">Contact
                                Number</span>
                            <input type="text" name="phone" placeholder="phone"
                                class="h-8 px-3 text-sm border border-gray-200 rounded-lg outline-none focus:border-blue-400 transition-colors w-full bg-white text-gray-800 placeholder-gray-300">
                        </div>

                    </div>
                </div>
            </div>

            <!-- Diagnostic Tests -->
            <div class="border border-gray-200 rounded-xl overflow-hidden mb-3.5">
                <div class="bg-gray-50 border-b border-gray-200 px-4 py-2.5 flex items-center gap-2">
                    <i class="fas fa-flask text-green-500 text-xs"></i>
                    <span class="text-xs font-bold text-gray-700 uppercase tracking-wide">Diagnostic Tests</span>
                </div>
                <div class="p-4">

                    <!-- Column headers -->
                    <div class="grid gap-2 mb-2 pb-2 border-b border-gray-100"
                        style="grid-template-columns: 16px 1fr 90px 28px;">
                        <span></span>
                        <span class="text-sm font-medium text-gray-600 uppercase tracking-wide">Test name</span>
                        <span class="text-sm font-medium text-gray-600 uppercase tracking-wide text-right">Price</span>
                        <span></span>
                    </div>

                    <div id="testRows">
                        <div class="grid gap-2 mb-1.5 items-center" style="grid-template-columns: 16px 1fr 90px 28px;">
                            <span class="w-1.5 h-1.5 bg-blue-200 rounded-full mx-auto block"></span>
                            <select name="test_name[]"
                                class="testSelect h-7 px-2 text-xs border border-gray-200 rounded-md outline-none focus:border-blue-400 transition-colors bg-white text-gray-900 cursor-pointer w-1/2">
                                <option value="">Select test</option>
                                <?php
                                mysqli_data_seek($allTests, 0);
                                while ($test = mysqli_fetch_assoc($allTests)) { ?>
                                    <option value="<?php echo $test['test_name']; ?>"
                                        data-price="<?php echo $test['test_price']; ?>">
                                        <?php echo $test['test_name']; ?>
                                    </option>
                                <?php } ?>
                            </select>
                            <input type="number" name="test_price[]"
                                class="price h-7 px-2 text-xs text-right border border-gray-200 rounded-md bg-blue-100 text-gray-900 font-medium w-full outline-none"
                                placeholder="Price" oninput="updateTotals()">
                            <button type="button" onclick="removeRow(this)"
                                class="w-7 h-7 border border-gray-600 rounded-md bg-red-50 text-gray-900 cursor-pointer flex items-center justify-center flex-shrink-0 hover:border-red-400 hover:bg-red-200 hover:text-red-900 transition-colors">
                                <i class="fas fa-times text-xs"></i>
                            </button>
                        </div>
                    </div>

                    <button type="button" onclick="addRow()"
                        class="mt-1 text-xs text-blue-500 bg-blue-50 border border-dashed border-blue-200 rounded-md px-3 py-1 cursor-pointer inline-flex items-center gap-1.5 hover:bg-blue-100 transition-colors">
                        <i class="fas fa-plus text-xs"></i> Add another test
                    </button>

                </div>
            </div>

            <!-- Billing -->
            <div class="border border-gray-200 rounded-xl overflow-hidden">
                <div class="bg-gray-50 border-b border-gray-200 px-4 py-2.5 flex items-center gap-2">
                    <i class="fas fa-file-invoice-dollar text-purple-500 text-xs"></i>
                    <span class="text-xs font-bold text-gray-700 uppercase tracking-wide">Billing Summary</span>
                </div>
                <div class="p-4">
                    <div class="max-w-xs ml-auto">

                        <div class="flex items-center justify-between py-2 border-b border-gray-100">
                            <span class="text-sm font-semibold text-gray-800">Subtotal</span>
                            <span class="text-lg font-semibold text-gray-600" id="subtotal-display">Rs. 0</span>
                            <input type="hidden" name="subtotal" id="subtotal">
                        </div>

                        <div class="flex items-center justify-between py-2 border-b border-gray-100">
                            <span class="text-sm font-semibold text-gray-800">Discount</span>
                            <input type="number" name="discount" id="discount" placeholder="0"
                                class="w-24 text-right text-sm px-2.5 h-7 border border-gray-300 rounded-md outline-none focus:border-blue-400 transition-colors"
                                oninput="updateTotals()">
                        </div>

                        <div class="flex items-center justify-between py-2">
                            <span class="text-sm font-semibold text-gray-800">Total</span>
                            <span class="text-lg font-semibold text-green-600" id="grand-display">Rs. 0</span>
                            <input type="hidden" name="grand_total" id="grand_total">
                        </div>

                        <div class="flex justify-end mt-2">
                            <button type="submit"
                                class="px-4 py-1.5 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg inline-flex items-center gap-1.5 transition-colors cursor-pointer">
                                <i class="fas fa-save text-xs"></i> Save &amp; Print
                            </button>
                        </div>

                    </div>
                </div>
            </div>

        </form>
    </div>
</div>
<?php include 'footer.php'; ?>
<script>
    function getTestOptions() {
        const original = document.querySelector('.testSelect');
        return original ? original.innerHTML : '';
    }

    function addRow() {
        const row = document.createElement('div');
        row.className = 'grid gap-2 mb-1.5 items-center';
        row.style.gridTemplateColumns = '16px 1fr 90px 28px';
        row.innerHTML = `
        <span class="w-1.5 h-1.5 bg-blue-200 rounded-full mx-auto block"></span>
        <select name="test_name[]" class="testSelect h-7 px-2 text-xs border border-gray-200 rounded-md outline-none focus:border-blue-400 transition-colors bg-white text-gray-900 cursor-pointer w-1/2">
            ${getTestOptions()}
        </select>
       <input type="number"
       name="test_price[]"
       class="price h-7 px-2 text-xs text-right border border-gray-200 rounded-md bg-blue-100 text-gray-900 font-medium w-full outline-none"
       placeholder="Price"
       oninput="updateTotals()">
        <button type="button" onclick="removeRow(this)"
            class="w-7 h-7 border border-gray-600 rounded-md bg-red-50 text-gray-900 cursor-pointer flex items-center justify-center flex-shrink-0 hover:border-red-400 hover:bg-red-200 hover:text-red-900 transition-colors">
            <i class="fas fa-times text-xs"></i>
        </button>`;
        document.getElementById('testRows').appendChild(row);
        wireSelect(row.querySelector('.testSelect'));
    }

    function removeRow(btn) {
        const rows = document.querySelectorAll('#testRows > div');
        if (rows.length <= 1) return;
        btn.closest('div').remove();
        updateTotals();
    }

  function wireSelect(sel) {
    sel.addEventListener('change', function() {
        const opt = this.options[this.selectedIndex];
        this.closest('div').querySelector('.price').value = opt.dataset.price || '';
        updateTotals();
    });
}

    function updateTotals() {
        let sub = 0;
        document.querySelectorAll('.price').forEach(p => sub += parseFloat(p.value) || 0);
        const disc = parseFloat(document.getElementById('discount').value) || 0;
        const grand = Math.max(0, sub - disc);
        document.getElementById('subtotal').value = sub;
        document.getElementById('grand_total').value = grand;
        document.getElementById('subtotal-display').textContent = 'Rs. ' + sub.toLocaleString();
        document.getElementById('grand-display').textContent = 'Rs. ' + grand.toLocaleString();
    }

    document.querySelectorAll('.testSelect').forEach(wireSelect);
    document.addEventListener('input', function(e) {
        if (e.target.classList.contains('price')) {
            updateTotals();
        }
    });
</script>