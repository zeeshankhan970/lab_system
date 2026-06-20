<?php
include '../config.php';
include 'header.php';
include 'sidebar.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

/* =========================
   NO ID — SHOW PATIENT LIST
========================= */
if ($id <= 0) {
    $patients = mysqli_query($conn, "SELECT * FROM patients ORDER BY created_at DESC");
?>
    <body class="bg-gray-100 min-h-screen py-8">

        <div class="max-w-6xl mx-auto bg-white rounded-2xl shadow border border-gray-200">

            <!-- HEADER -->
            <div class="bg-blue-700 text-white px-8 py-6 rounded-t-2xl flex items-center gap-3">
                <i class="fas fa-chart-bar text-2xl"></i>
                <div>
                    <h1 class="text-2xl font-bold">Patient Reports</h1>
                    <p class="text-blue-200 text-sm mt-0.5">Select a patient to view or enter their report</p>
                </div>
            </div>

            <div class="p-6">

                <!-- Search -->
                <div class="mb-5 relative">
                    <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm"></i>
                    <input type="text" id="searchInput" onkeyup="filterTable()"
                        placeholder="Search by name, invoice, doctor, phone..."
                        class="border border-gray-300 rounded-lg pl-9 pr-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400">
                </div>

                <!-- Scrollable Table Wrapper -->
                <div class="rounded-xl border border-gray-200 overflow-hidden">
                    <div class="overflow-x-auto overflow-y-auto" style="max-height: 500px;">
                        <table class="w-full text-sm" id="patientTable" style="border-collapse: separate; border-spacing: 0;">
                            <thead>
                                <tr class="bg-gray-50 text-gray-600 uppercase text-xs tracking-wide">
                                    <th class="px-4 py-3 text-left border-b bg-gray-50 sticky top-0 z-10">Patient Id</th>
                                    <th class="px-4 py-3 text-left border-b bg-gray-50 sticky top-0 z-10">Patient Name</th>
                                    <th class="px-4 py-3 text-left border-b bg-gray-50 sticky top-0 z-10">Age</th>
                                    <th class="px-4 py-3 text-left border-b bg-gray-50 sticky top-0 z-10">Gender</th>
                                    <th class="px-4 py-3 text-left border-b bg-gray-50 sticky top-0 z-10">Doctor</th>
                                    <th class="px-4 py-3 text-left border-b bg-gray-50 sticky top-0 z-10">Phone</th>
                                    <th class="px-4 py-3 text-left border-b bg-gray-50 sticky top-0 z-10">Date</th>
                                    <th class="px-4 py-3 text-center border-b bg-gray-50 sticky top-0 z-10">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $found = false;
                                while ($row = mysqli_fetch_assoc($patients)):
                                    $found = true;
                                ?>
                                    <tr class="border-b hover:bg-blue-50 transition">
                                        <td class="px-4 py-3 font-medium text-blue-700">LAB-<?= str_pad(htmlspecialchars($row['id']), 3, '0', STR_PAD_LEFT) ?></td>
                                        <td class="px-4 py-3 font-semibold text-gray-800"><?= htmlspecialchars($row['patient_name']) ?></td>
                                        <td class="px-4 py-3 text-gray-600"><?= htmlspecialchars($row['age']) ?></td>
                                        <td class="px-4 py-3 text-gray-600"><?= htmlspecialchars($row['gender']) ?></td>
                                        <td class="px-4 py-3 text-gray-600"><?= htmlspecialchars($row['doctor_reference']) ?></td>
                                        <td class="px-4 py-3 text-gray-600"><?= htmlspecialchars($row['phone']) ?></td>
                                        <td class="px-4 py-3 text-gray-500"><?= date('d M Y', strtotime($row['created_at'])) ?></td>
                                        <td class="px-4 py-3 text-center">
                                            <a href="add_test_result.php?id=<?= $row['id'] ?>"
                                                class="bg-blue-600 hover:bg-blue-700 text-white px-3 py-1.5 rounded-lg text-xs font-medium inline-flex items-center gap-1">
                                                <i class="fas fa-plus"></i> Add Result
                                            </a>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                                <?php if (!$found): ?>
                                    <tr>
                                        <td colspan="8" class="px-4 py-10 text-center text-gray-400">
                                            <i class="fas fa-inbox text-3xl mb-2 block"></i>
                                            No patients found.
                                        </td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>

            </div>
        </div>

        <script>
            function filterTable() {
                const input = document.getElementById('searchInput').value.toLowerCase();
                const rows = document.querySelectorAll('#patientTable tbody tr');
                rows.forEach(row => {
                    row.style.display = row.innerText.toLowerCase().includes(input) ? '' : 'none';
                });
            }
        </script>
                                </div>
    <?php include 'footer.php'; ?>
<?php
    exit;
}
?>