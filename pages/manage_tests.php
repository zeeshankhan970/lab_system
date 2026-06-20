<?php
include '../config.php';

if (!isset($_SESSION['admin'])) {
    header("Location: ../login.php");
    exit();
}
/* =========================
   VARIABLES
========================= */
$test_name = "";
$test_price = "";
$edit_id = "";

/* =========================
   ADD / UPDATE TEST
========================= */
if (isset($_POST['save_test'])) {

    $test_name  = mysqli_real_escape_string($conn, $_POST['test_name']);
    $test_price = mysqli_real_escape_string($conn, $_POST['test_price']);
    $edit_id    = $_POST['edit_id'];

    // UPDATE TEST
    if ($edit_id != "") {

        mysqli_query($conn, "
            UPDATE tests SET
            test_name='$test_name',
            test_price='$test_price'
            WHERE id='$edit_id'
        ");

        $test_id = $edit_id;

        // Remove old parameters
        mysqli_query($conn, "
            DELETE FROM parameters
            WHERE test_id='$test_id'
        ");
    }

    // INSERT TEST
    else {

        mysqli_query($conn, "
            INSERT INTO tests(test_name,test_price)
            VALUES('$test_name','$test_price')
        ");

        $test_id = mysqli_insert_id($conn);
    }

    /* =========================
       SAVE PARAMETERS
    ========================= */

    if (isset($_POST['parameter_name'])) {

        foreach ($_POST['parameter_name'] as $key => $parameter_name) {

            $parameter_name = mysqli_real_escape_string($conn, $parameter_name);

            $normal_range = mysqli_real_escape_string(
                $conn,
                $_POST['normal_range'][$key]
            );

            $unit = mysqli_real_escape_string(
                $conn,
                $_POST['unit'][$key]
            );

            if (!empty($parameter_name)) {

                mysqli_query($conn, "
                    INSERT INTO parameters
                    (
                        test_id,
                        parameter_name,
                        normal_range,
                        unit
                    )
                    VALUES
                    (
                        '$test_id',
                        '$parameter_name',
                        '$normal_range',
                        '$unit'
                    )
                ");
            }
        }
    }

    header("Location: manage_tests.php");
    exit();
}

/* =========================
   DELETE TEST
========================= */
if (isset($_GET['delete'])) {

    $id = $_GET['delete'];

    mysqli_query($conn, "DELETE FROM parameters WHERE test_id='$id'");
    mysqli_query($conn, "DELETE FROM tests WHERE id='$id'");

    header("Location: manage_tests.php");
    exit();
}

$parameter_data = [];

if (isset($_GET['edit'])) {

    $id = $_GET['edit'];

    $result = mysqli_query($conn, "SELECT * FROM tests WHERE id='$id'");
    $row = mysqli_fetch_assoc($result);

    if ($row) {
        $test_name  = $row['test_name'];
        $test_price = $row['test_price'];
        $edit_id    = $row['id'];

        $param_query = mysqli_query($conn, "SELECT * FROM parameters WHERE test_id='$id'");
        while ($param = mysqli_fetch_assoc($param_query)) {
            $parameter_data[] = $param;
        }
    }
}

/* =========================
   PAGINATION
========================= */

$limit = 5;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
if ($page < 1) $page = 1;
$start = ($page - 1) * $limit;

$total_result = mysqli_query($conn, "SELECT COUNT(*) as total FROM tests");
$total_row = mysqli_fetch_assoc($total_result);
$total_records = $total_row['total'];
$total_pages = ceil($total_records / $limit);

/* =========================
   GET TESTS WITH PARAMETERS
========================= */

$tests = mysqli_query($conn, "
    SELECT * FROM tests
    ORDER BY id DESC
    LIMIT $start, $limit
");

// Fetch all parameters for displayed tests in one query
$test_ids = [];
$tests_data = [];
while ($row = mysqli_fetch_assoc($tests)) {
    $tests_data[] = $row;
    $test_ids[] = $row['id'];
}

$all_parameters = [];
if (!empty($test_ids)) {
    $ids_str = implode(',', $test_ids);
    $param_result = mysqli_query($conn, "
        SELECT * FROM parameters
        WHERE test_id IN ($ids_str)
        ORDER BY id ASC
    ");
    while ($p = mysqli_fetch_assoc($param_result)) {
        $all_parameters[$p['test_id']][] = $p;
    }
}

include 'header.php';
include 'sidebar.php';
?>
<body class="bg-gradient-to-br from-slate-50 to-gray-100 font-sans antialiased">
    <div class="w-full overflow-y-auto mx-auto px-4 md:px-6 py-8">

        <!-- Form Section -->
        <div class="flex justify-center mb-10">
            <div class="w-full max-w-2xl">
                <div class="bg-white rounded-2xl shadow-xl border border-gray-100 overflow-hidden transition-smooth">

                    <div class="bg-gradient-to-r from-blue-50 to-indigo-50 px-6 py-4 border-b border-blue-100">
                        <div class="flex items-center gap-3">
                            <div class="bg-gradient-to-br from-blue-600 to-indigo-700 p-2.5 rounded-xl text-white shadow-md">
                                <i class="fas fa-plus-circle text-lg"></i>
                            </div>
                            <div>
                                <h2 class="text-lg font-bold text-gray-800">
                                    <?php echo ($edit_id == "") ? "Add New Diagnostic Test" : "Edit Test Information"; ?>
                                </h2>
                                <p class="text-xs text-gray-500 mt-0.5">Fill in the test details below</p>
                            </div>
                        </div>
                    </div>

                    <div class="p-6">
                        <form method="POST" class="space-y-6">
                            <input type="hidden" name="edit_id" value="<?php echo htmlspecialchars($edit_id); ?>">

                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2 flex items-center gap-2">
                                    <i class="fas fa-vial text-blue-500 text-xs"></i> Test Name
                                </label>
                                <input type="text" name="test_name"
                                    value="<?php echo htmlspecialchars($test_name); ?>"
                                    class="w-full border border-gray-300 rounded-xl px-4 py-2.5 focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none transition-all"
                                    placeholder="e.g. Complete Blood Count" required>
                            </div>

                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2 flex items-center gap-2">
                                    <i class="fas fa-tag text-green-500 text-xs"></i> Test Price
                                </label>
                                <input type="number" name="test_price"
                                    value="<?php echo htmlspecialchars($test_price); ?>"
                                    class="w-full border border-gray-300 rounded-xl px-4 py-2.5 focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none transition-all"
                                    placeholder="0.00" required>
                            </div>

                            <!-- Parameters Section -->
                            <div class="pt-2">
                                <div class="flex justify-between items-center mb-3">
                                    <div class="flex items-center gap-2">
                                        <i class="fas fa-list-ul text-purple-500 text-sm"></i>
                                        <label class="block text-sm font-semibold text-gray-700">Test Parameters</label>
                                    </div>
                                    <button type="button" onclick="addParameter()"
                                        class="bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white px-4 py-2 rounded-xl text-sm shadow-md transition-all flex items-center gap-2">
                                        <i class="fas fa-plus text-xs"></i> Add Parameter
                                    </button>
                                </div>

                                <!-- Column headers -->
                                <div class="grid grid-cols-12 gap-3 px-3 pb-1 mb-1">
                                    <div class="col-span-5 text-xs font-semibold text-gray-500 uppercase tracking-wide">Parameter Name</div>
                                    <div class="col-span-3 text-xs font-semibold text-gray-500 uppercase tracking-wide">Normal Range</div>
                                    <div class="col-span-2 text-xs font-semibold text-gray-500 uppercase tracking-wide">Unit</div>
                                    <div class="col-span-2"></div>
                                </div>

                                <div id="parameter-section" class="space-y-3">
                                    <?php if (!empty($parameter_data)): ?>
                                        <?php foreach ($parameter_data as $param): ?>
                                            <div class="grid grid-cols-12 gap-3 items-center parameter-row bg-gray-50 p-3 rounded-xl border border-gray-200">
                                                <div class="col-span-5">
                                                    <input type="text" name="parameter_name[]"
                                                        value="<?php echo htmlspecialchars($param['parameter_name']); ?>"
                                                        placeholder="e.g. Hemoglobin"
                                                        class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-400 outline-none transition-all">
                                                </div>
                                                <div class="col-span-3">
                                                    <input type="text" name="normal_range[]"
                                                        value="<?php echo htmlspecialchars($param['normal_range']); ?>"
                                                        placeholder="e.g. 12–17"
                                                        class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-400 outline-none transition-all">
                                                </div>
                                                <div class="col-span-2">
                                                    <input type="text" name="unit[]"
                                                        value="<?php echo htmlspecialchars($param['unit']); ?>"
                                                        placeholder="e.g. g/dL"
                                                        class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-400 outline-none transition-all">
                                                </div>
                                                <div class="col-span-2">
                                                    <button type="button" onclick="removeParameter(this)"
                                                        class="bg-red-50 hover:bg-red-100 text-red-500 px-3 py-2 rounded-lg w-full text-sm flex items-center justify-center gap-1 transition-all">
                                                        <i class="fas fa-trash-alt text-xs"></i> Remove
                                                    </button>
                                                </div>
                                            </div>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </div>
                            </div>

                            <div class="pt-4 flex justify-end">
                                <button type="submit" name="save_test"
                                    class="bg-gradient-to-r from-blue-600 to-indigo-700 hover:from-blue-700 hover:to-indigo-800 text-white font-semibold py-3 px-6 rounded-xl shadow-lg transition-all transform hover:scale-[1.02] flex items-center justify-center gap-2">
                                    <i class="fas <?php echo ($edit_id == "") ? 'fa-save' : 'fa-pen'; ?>"></i>
                                    <?php echo ($edit_id == "") ? "Save Test" : "Update Test"; ?>
                                </button>
                            </div>

                            <?php if ($edit_id != ""): ?>
                                <div class="text-center pt-2">
                                    <a href="manage_tests.php"
                                        class="text-sm text-gray-400 hover:text-red-500 inline-flex items-center gap-1 transition-colors">
                                        <i class="fas fa-times-circle"></i> Cancel Edit
                                    </a>
                                </div>
                            <?php endif; ?>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tests Directory Table -->
        <div class="bg-white rounded-2xl shadow-xl border border-gray-200 overflow-hidden">
            <div class="bg-gray-50 px-6 py-5 border-b border-gray-200 flex justify-between items-center flex-wrap gap-3">
                <div class="flex items-center gap-3">
                    <div class="bg-gradient-to-br from-slate-700 to-slate-800 p-2 rounded-xl text-white shadow">
                        <i class="fas fa-table-list text-sm"></i>
                    </div>
                    <div>
                        <h3 class="text-base font-bold text-gray-800">Tests Directory</h3>
                        <p class="text-xs text-gray-500">Total tests: <?php echo $total_records; ?></p>
                    </div>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="bg-gray-100 text-gray-700 border-b border-gray-200">
                            <th class="px-5 py-4 text-left font-semibold">ID</th>
                            <th class="px-5 py-4 text-left font-semibold">Test Name</th>
                            <th class="px-5 py-4 text-left font-semibold">Price</th>
                            <th class="px-5 py-4 text-left font-semibold">Parameters</th>
                            <th class="px-5 py-4 text-center font-semibold">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($tests_data)): ?>
                            <?php foreach ($tests_data as $row): ?>
                                <tr class="border-b border-gray-100 hover:bg-blue-50/30 transition-colors align-top">
                                    <td class="px-5 py-3.5 text-gray-500 font-mono text-xs">
                                        <?php echo $row['id']; ?>
                                    </td>
                                    <td class="px-5 py-3.5 font-semibold text-gray-800">
                                        <?php echo htmlspecialchars($row['test_name']); ?>
                                    </td>
                                    <td class="px-5 py-3.5">
                                        <span class="bg-emerald-50 text-emerald-700 px-2.5 py-1 rounded-full text-xs font-bold">
                                            <?php echo number_format($row['test_price'], 2); ?>
                                        </span>
                                    </td>

                                    <!-- ✅ Parameters column -->
                                    <td class="px-5 py-3.5">
                                        <?php if (!empty($all_parameters[$row['id']])): ?>
                                            <ul class="space-y-1">
                                                <?php foreach ($all_parameters[$row['id']] as $p): ?>
                                                    <li class="flex items-center gap-2 text-xs text-gray-700">
                                                        <span class="w-1.5 h-1.5 rounded-full bg-blue-400 flex-shrink-0"></span>
                                                        <span class="font-medium"><?php echo htmlspecialchars($p['parameter_name']); ?></span>
                                                        <?php if (!empty($p['normal_range'])): ?>
                                                            <span class="text-gray-400">(<?php echo htmlspecialchars($p['normal_range']); ?>
                                                                <?php echo htmlspecialchars($p['unit']); ?>)</span>
                                                        <?php endif; ?>
                                                    </li>
                                                <?php endforeach; ?>
                                            </ul>
                                        <?php else: ?>
                                            <span class="text-xs text-gray-400 italic">No parameters</span>
                                        <?php endif; ?>
                                    </td>

                                    <td class="px-5 py-3.5">
                                        <div class="relative inline-block text-left">

                                            <!-- Kebab Button -->
                                            <button type="button"
                                                onclick="toggleMenu(<?php echo $row['id']; ?>)"
                                                class="p-2 rounded-lg hover:bg-gray-100 transition">
                                                <i class="fas fa-ellipsis-v text-gray-600"></i>
                                            </button>

                                            <!-- Dropdown Menu -->
                                            <div id="menu-<?php echo $row['id']; ?>"
                                                class="hidden absolute right-0 mt-2 w-36 bg-white rounded-xl shadow-lg border border-gray-200 overflow-hidden z-50">

                                                <a href="?edit=<?php echo $row['id']; ?>"
                                                    class="flex items-center gap-2 px-4 py-3 text-sm text-amber-700 hover:bg-amber-50 transition">
                                                    <i class="fas fa-edit"></i>
                                                    Edit
                                                </a>

                                                <a href="?delete=<?php echo $row['id']; ?>"
                                                    onclick="return confirm('⚠️ Delete this test permanently? This action cannot be undone.')"
                                                    class="flex items-center gap-2 px-4 py-3 text-sm text-rose-600 hover:bg-rose-50 transition">
                                                    <i class="fas fa-trash-alt"></i>
                                                    Delete
                                                </a>

                                            </div>

                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="5" class="text-center py-12 text-gray-400">
                                    <i class="fas fa-flask text-4xl mb-3 opacity-30 block"></i>
                                    <p>No diagnostic tests found</p>
                                    <p class="text-xs mt-1">Add your first test using the form above</p>
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <?php if ($total_pages > 1): ?>
                <div class="flex justify-center items-center gap-2 py-6 border-t border-gray-100 bg-gray-50/50 flex-wrap">
                    <?php if ($page > 1): ?>
                        <a href="?page=<?php echo $page - 1; ?>"
                            class="px-4 py-2 bg-white hover:bg-gray-100 rounded-xl shadow-sm transition-all flex items-center gap-1 text-sm font-medium text-gray-600">
                            <i class="fas fa-chevron-left text-xs"></i> Previous
                        </a>
                    <?php endif; ?>

                    <div class="flex gap-1.5">
                        <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                            <a href="?page=<?php echo $i; ?>"
                                class="w-9 h-9 flex items-center justify-center rounded-xl transition-all text-sm font-medium shadow-sm
                            <?php echo ($page == $i) ? 'bg-gradient-to-r from-blue-600 to-indigo-700 text-white shadow-md' : 'bg-white hover:bg-gray-100 text-gray-700'; ?>">
                                <?php echo $i; ?>
                            </a>
                        <?php endfor; ?>
                    </div>

                    <?php if ($page < $total_pages): ?>
                        <a href="?page=<?php echo $page + 1; ?>"
                            class="px-4 py-2 bg-white hover:bg-gray-100 rounded-xl shadow-sm transition-all flex items-center gap-1 text-sm font-medium text-gray-600">
                            Next <i class="fas fa-chevron-right text-xs"></i>
                        </a>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <script>
        function addParameter() {
            const section = document.getElementById("parameter-section");
            const row = document.createElement("div");
            row.className = "grid grid-cols-12 gap-3 items-center parameter-row bg-gray-50 p-3 rounded-xl border border-gray-200";
            row.innerHTML = `
            <div class="col-span-5">
                <input type="text" name="parameter_name[]" placeholder="e.g. Hemoglobin"
                    class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-400 outline-none transition-all">
            </div>
            <div class="col-span-3">
                <input type="text" name="normal_range[]" placeholder="e.g. 12–17"
                    class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-400 outline-none transition-all">
            </div>
            <div class="col-span-2">
                <input type="text" name="unit[]" placeholder="e.g. g/dL"
                    class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-400 outline-none transition-all">
            </div>
            <div class="col-span-2">
                <button type="button" onclick="removeParameter(this)"
                    class="bg-red-50 hover:bg-red-100 text-red-500 rounded-lg px-3 py-2 transition-all w-full flex items-center justify-center gap-1 text-sm">
                    <i class="fas fa-trash-alt text-xs"></i> Remove
                </button>
            </div>
        `;
            section.appendChild(row);
        }

        function removeParameter(button) {
            let rows = document.querySelectorAll('.parameter-row');

            if (rows.length <= 1) {
                alert("At least one parameter row must remain.");
                return;
            }

            button.closest('.parameter-row').remove();
        }

        document.addEventListener('DOMContentLoaded', function() {
            const section = document.getElementById("parameter-section");
            if (section.children.length === 0) {
                addParameter();
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
    </script>
</div>
<?php include 'footer.php'; ?>