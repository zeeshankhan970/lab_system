<?php
include '../config.php';
include 'header.php';
include 'sidebar.php';

if (!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit();
}

$patients = mysqli_query($conn, "
    SELECT * FROM patients
    ORDER BY id DESC
");
?>

<body class="bg-gradient-to-br from-blue-50 to-indigo-100 font-sans">

    <div class="w-full overflow-y-auto mx-auto px-4 py-8">
        <!-- Header Card -->
        <div class="bg-white rounded-2xl shadow-xl overflow-hidden mb-8 border border-gray-100">

            <div class="p-6 bg-gray-50 border-b border-gray-200">
                <div class="relative max-w-md">
                    <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm"></i>
                    <input type="text" id="searchInput" onkeyup="filterTable()"
                        placeholder="Search by patient name, phone, ID..."
                        class="border border-gray-300 rounded-lg pl-9 pr-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400">
                </div>
            </div>
            <!-- Table Container -->
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="bg-gradient-to-r from-gray-100 to-gray-200 border-b-2 border-gray-300">
                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">
                            </i> ID
                            </th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">
                                <i class="fas fa-user mr-1"></i> Patient Name
                            </th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">
                                <i class="fas fa-phone mr-1"></i> Phone
                            </th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">
                                <i class="fas fa-rupee-sign mr-1"></i> Total Amount
                            </th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">
                                <i class="fas fa-cog mr-1"></i> Actions
                            </th>
                        </tr>
                    </thead>
                    <tbody id="patientTableBody" class="divide-y divide-gray-200">
                        <?php if (mysqli_num_rows($patients) > 0): ?>
                        <?php while ($row = mysqli_fetch_assoc($patients)) { ?>
                        <tr class="hover:bg-blue-50 transition-custom group">
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                <span class="bg-gray-100 text-gray-700 px-2 py-1 rounded-md text-xs font-mono">
                                    <?php echo 'LAB-0' . str_pad($row['id'], 3, '0', STR_PAD_LEFT); ?>
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center gap-2">
                                    <div
                                        class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center text-blue-600 font-bold">
                                        <?php echo strtoupper(substr(htmlspecialchars($row['patient_name']), 0, 1)); ?>
                                    </div>
                                    <span
                                        class="text-sm font-semibold text-gray-800"><?php echo htmlspecialchars($row['patient_name']); ?></span>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                <i class="fas fa-phone-alt text-gray-400 mr-1"></i>
                                <?php echo htmlspecialchars($row['phone']); ?>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="text-sm font-bold text-green-600 bg-green-100 px-3 py-1 rounded-full">
                                    <i class="fas fa-rupee-sign text-xs mr-0.5"></i>
                                    <?php echo number_format($row['grand_total'], 2); ?>
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm">
                                <div class="relative inline-block text-left">

                                    <!-- Kebab Button -->
                                    <button type="button" onclick="toggleMenu(<?php echo $row['id']; ?>)"
                                        class="p-2 rounded-lg hover:bg-gray-100 transition">
                                        <i class="fas fa-ellipsis-v"></i>
                                    </button>

                                    <!-- Dropdown Menu -->
                                    <div id="menu-<?php echo $row['id']; ?>"
                                        class="hidden absolute right-0 mt-2 w-40 bg-white rounded-lg shadow-lg border border-gray-200 z-50">

                                        <a href="../print_receipt.php?id=<?php echo $row['id']; ?>"
                                            class="flex items-center gap-2 px-4 py-2 text-sm text-gray-700 hover:bg-blue-50">
                                            <i class="fas fa-eye text-blue-500"></i>
                                            View
                                        </a>

                                        <a href="update.php?id=<?php echo $row['id']; ?>"
                                            class="flex items-center gap-2 px-4 py-2 text-sm text-gray-700 hover:bg-green-50">
                                            <i class="fas fa-edit text-green-500"></i>
                                            Edit
                                        </a>

                                        <a href="../delete_patient.php?id=<?php echo $row['id']; ?>"
                                            onclick="return confirm('⚠️ Are you sure you want to delete this patient?');"
                                            class="flex items-center gap-2 px-4 py-2 text-sm text-red-600 hover:bg-red-50">
                                            <i class="fas fa-trash-alt"></i>
                                            Delete
                                        </a>

                                    </div>
                                </div>
                            </td>
                        </tr>
                        <?php } ?>
                        <?php else: ?>
                        <tr>
                            <td colspan="5" class="px-6 py-16 text-center">
                                <div class="flex flex-col items-center gap-3">
                                    <div class="w-20 h-20 bg-gray-100 rounded-full flex items-center justify-center">
                                        <i class="fas fa-search text-3xl text-gray-400"></i>
                                    </div>
                                    <p class="text-gray-500 text-lg font-medium">No patients found</p>
                                    <p class="text-gray-400 text-sm">Try adjusting your search criteria</p>
                                </div>
                            </td>
                        </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <!-- Footer Stats -->
            <div class="bg-gray-50 px-6 py-4 border-t border-gray-200">
                <div class="flex justify-between items-center text-sm text-gray-600">
                    <div class="flex items-center gap-2">
                        <i class="fas fa-database text-gray-400"></i>
                        <span>Total Records: <strong
                                class="text-gray-800"><?php echo mysqli_num_rows($patients); ?></strong></span>
                    </div>
                    <div class="flex items-center gap-2">
                        <i class="fas fa-clock text-gray-400"></i>
                        <span>Last updated: <?php echo date('d M Y, h:i A'); ?></span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Custom confirmation dialog style (keeps functionality intact) -->
    <script>
    // This maintains the original confirm behavior but adds a visual touch
    // No logic changed, just ensures confirm dialog works as expected
    const deleteLinks = document.querySelectorAll('a[href*="delete_patient.php"]');
    deleteLinks.forEach(link => {
        const originalConfirm = link.onclick;
        if (originalConfirm) {
            link.onclick = (e) => {
                return confirm('Are you sure you want to delete this patient?');
            };
        }
    });

    function toggleMenu(id) {
        document.querySelectorAll('[id^="menu-"]').forEach(menu => {
            if (menu.id !== 'menu-' + id) {
                menu.classList.add('hidden');
            }
        });

        document.getElementById('menu-' + id).classList.toggle('hidden');
    }

    document.addEventListener('click', function(e) {
        if (!e.target.closest('.relative')) {
            document.querySelectorAll('[id^="menu-"]').forEach(menu => {
                menu.classList.add('hidden');
            });
        }
    });

    function filterTable() {
        const input = document.getElementById('searchInput').value.toLowerCase();
        const rows = document.querySelectorAll('#patientTableBody tr');

        rows.forEach(row => {
            row.style.display =
                row.innerText.toLowerCase().includes(input) ?
                '' :
                'none';
        });
    }
    </script>
    </div>
<?php include 'footer.php'; ?>
