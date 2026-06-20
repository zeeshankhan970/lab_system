<?php
include '../config.php';

if (!isset($_SESSION['admin'])) {
    header("Location: /lab_system/login.php");
    exit();
}

$doctor_name    = "";
$doctor_edit_id = "";

// ADD / UPDATE
if (isset($_POST['save_doctor'])) {
    $doctor_name    = trim($_POST['doctor_name']);
    $doctor_edit_id = trim($_POST['doctor_edit_id']);

    if (empty($doctor_name)) {
        die("Doctor name cannot be empty.");
    }

    if ($doctor_edit_id !== "") {
        $stmt = $conn->prepare("UPDATE doctors SET doctor_name=? WHERE id=?");
        $stmt->bind_param("si", $doctor_name, $doctor_edit_id);
        $stmt->execute();
    } else {
        $stmt = $conn->prepare("INSERT INTO doctors(doctor_name) VALUES(?)");
        $stmt->bind_param("s", $doctor_name);
        $stmt->execute();
    }

    header("Location: manage_doctors.php");
    exit();
}

// DELETE
if (isset($_GET['delete_doctor'])) {
    $id = (int)$_GET['delete_doctor'];  // ✅ cast to int — safest for IDs
    $stmt = $conn->prepare("DELETE FROM doctors WHERE id=?");
    $stmt->bind_param("i", $id);
    $stmt->execute();

    header("Location: manage_doctors.php");
    exit();
}

// EDIT — load data
if (isset($_GET['edit_doctor'])) {
    $id   = (int)$_GET['edit_doctor'];
    $stmt = $conn->prepare("SELECT * FROM doctors WHERE id=?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $row  = $stmt->get_result()->fetch_assoc();

    if ($row) {
        $doctor_name    = $row['doctor_name'];
        $doctor_edit_id = $row['id'];
    }
}

// PAGINATION
$limit  = 5;
$page   = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
$offset = ($page - 1) * $limit;

$total_doctors = $conn->query("SELECT COUNT(*) AS total FROM doctors")
                      ->fetch_assoc()['total'];
$total_pages   = ceil($total_doctors / $limit);

$stmt = $conn->prepare("SELECT * FROM doctors ORDER BY id DESC LIMIT ?, ?");
$stmt->bind_param("ii", $offset, $limit);
$stmt->execute();
$doctors = $stmt->get_result();

include 'header.php';
include 'sidebar.php';
?>

<body class="bg-gradient-to-br from-gray-50 to-gray-100 font-sans antialiased">
    <!-- MAIN -->
    <div class="w-full max-w-4xl overflow-y-auto mx-auto px-4 md:px-6 py-8">

        <!-- FORM -->
        <div class="bg-white rounded-2xl shadow-lg border border-gray-100 overflow-hidden mb-10">

            <div class="bg-gradient-to-r from-emerald-50 to-teal-50 px-6 py-4 border-b border-emerald-100">

                <div class="flex items-center gap-3">

                    <div class="bg-emerald-600 p-2.5 rounded-xl text-white">
                        <i class="fas fa-user-md"></i>
                    </div>

                    <div>
                        <h2 class="text-md font-bold text-gray-800">
                            Medical Doctors
                        </h2>
                    </div>

                </div>

            </div>

            <div class="p-6">

                <form method="POST" class="space-y-5">

                    <input type="hidden" name="doctor_edit_id" value="<?php echo $doctor_edit_id; ?>">

                    <div>

                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            Doctor Full Name
                        </label>

                        <input type="text" name="doctor_name" value="<?php echo htmlspecialchars($doctor_name); ?>"
                            class="w-full border border-gray-300 rounded-xl px-4 py-3" required>

                    </div>

                    <button type="submit" name="save_doctor"
                        class="w-full bg-gradient-to-r from-emerald-600 to-teal-700 text-white font-semibold py-3 rounded-xl">

                        <?php echo ($doctor_edit_id == "") ? "Add Doctor" : "Update Doctor"; ?>

                    </button>
                    <?php if ($doctor_edit_id != ""): ?>

                        <div class="mt-4 text-center">

                            <a href="manage_doctors.php"
                                class="text-sm text-gray-400 hover:text-emerald-600 inline-flex items-center gap-1 transition-colors">

                                <i class="fas fa-times-circle"></i>
                                Cancel Edit

                            </a>

                        </div>

                    <?php endif; ?>

                </form>

            </div>

        </div>

        <!-- TABLE -->
        <div class="bg-white rounded-2xl shadow-lg border border-gray-200 overflow-hidden">

            <div class="bg-gray-50 px-6 py-4 border-b border-gray-200">

                <h3 class="text-base font-bold text-gray-800">
                    Doctors Panel
                </h3>

            </div>

            <div class="overflow-x-auto">

                <table class="w-full text-sm">

                    <thead>

                        <tr class="bg-gray-100 text-gray-700">

                            <th class="px-5 py-3 text-left">ID</th>
                            <th class="px-5 py-3 text-left">Doctor Name</th>
                            <th class="px-5 py-3 text-center">Actions</th>

                        </tr>

                    </thead>

                    <tbody>

                        <?php if (mysqli_num_rows($doctors) > 0): ?>

                            <?php while ($row = mysqli_fetch_assoc($doctors)): ?>

                                <tr class="border-b border-gray-100">

                                    <td class="px-5 py-3">
                                        <?php echo $row['id']; ?>
                                    </td>

                                    <td class="px-5 py-3 font-semibold">
                                        <?php echo htmlspecialchars($row['doctor_name']); ?>
                                    </td>

                                    <td class="px-5 py-3">

                                        <div class="relative inline-block text-left">

                                            <!-- Kebab Button -->
                                            <button type="button" onclick="toggleMenu(<?php echo $row['id']; ?>)"
                                                class="p-2 rounded-lg hover:bg-gray-100 transition">
                                                <i class="fas fa-ellipsis-v text-gray-600"></i>
                                            </button>

                                            <!-- Dropdown -->
                                            <div id="menu-<?php echo $row['id']; ?>"
                                                class="hidden absolute right-0 mt-2 w-36 bg-white rounded-xl shadow-lg border border-gray-200 overflow-hidden z-50">

                                                <a href="?edit_doctor=<?php echo $row['id']; ?>"
                                                    class="flex items-center gap-2 px-4 py-3 text-sm text-amber-700 hover:bg-amber-50">
                                                    <i class="fas fa-edit"></i>
                                                    Edit
                                                </a>

                                                <a href="?delete_doctor=<?php echo $row['id']; ?>"
                                                    onclick="return confirm('Delete this doctor?')"
                                                    class="flex items-center gap-2 px-4 py-3 text-sm text-rose-600 hover:bg-rose-50">
                                                    <i class="fas fa-trash-alt"></i>
                                                    Delete
                                                </a>

                                            </div>

                                        </div>

                                    </td>

                                </tr>

                            <?php endwhile; ?>

                        <?php endif; ?>

                    </tbody>

                </table>
                <?php if ($total_pages > 1): ?>
                    <div class="flex items-center justify-between px-6 py-4 bg-white border-t">

                        <div class="text-sm text-gray-500">
                            Page <?php echo $page; ?> of <?php echo $total_pages; ?>
                        </div>

                        <div class="flex items-center gap-2">

                            <!-- Previous -->
                            <?php if ($page > 1): ?>
                                <a href="?page=<?php echo ($page - 1); ?>"
                                    class="px-4 py-2 text-sm bg-gray-100 hover:bg-gray-200 rounded-lg transition">
                                    Previous
                                </a>
                            <?php endif; ?>

                            <?php
                            $start = max(1, $page - 2);
                            $end   = min($total_pages, $page + 2);

                            for ($i = $start; $i <= $end; $i++):
                            ?>
                                <a href="?page=<?php echo $i; ?>" class="px-4 py-2 text-sm rounded-lg transition
               <?php echo ($i == $page)
                                    ? 'bg-blue-600 text-white'
                                    : 'bg-gray-100 hover:bg-gray-200 text-gray-700'; ?>">
                                    <?php echo $i; ?>
                                </a>
                            <?php endfor; ?>

                            <!-- Next -->
                            <?php if ($page < $total_pages): ?>
                                <a href="?page=<?php echo ($page + 1); ?>"
                                    class="px-4 py-2 text-sm bg-gray-100 hover:bg-gray-200 rounded-lg transition">
                                    Next
                                </a>
                            <?php endif; ?>

                        </div>

                    </div>
                <?php endif; ?>

            </div>

        </div>


    </div>

<script>
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
