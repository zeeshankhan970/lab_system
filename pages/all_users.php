<?php
include '../config.php';
include 'header.php';
include 'sidebar.php';
if (!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit();
}
// $users_result = mysqli_query($conn, "SELECT * FROM users ORDER BY id DESC");
$limit = 3; // users per page

$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;

if ($page < 1) {
    $page = 1;
}

$offset = ($page - 1) * $limit;

/* Total Users */
$total_result = mysqli_query($conn, "SELECT COUNT(*) AS total FROM users");
$total_row = mysqli_fetch_assoc($total_result);
$total_users = $total_row['total'];

$total_pages = ceil($total_users / $limit);

/* Paginated Users */
$users_result = mysqli_query($conn, "
    SELECT *
    FROM users
    ORDER BY id DESC
    LIMIT $limit OFFSET $offset
");
?>

<body class="bg-slate-100 min-h-screen p-6">

    <div class="w-full">

        <!-- Page Header -->
        <div class="mb-2 flex flex-col md:flex-row md:items-center md:justify-between gap-4 p-4">
            <div>
                <h1 class="text-xl font-bold text-slate-800">User Management</h1>
                <p class="text-slate-500 mt-1">Manage system users and permissions</p>
            </div>

            <a href="add_users.php"
                class="inline-flex items-center gap-2 px-3 py-2 bg-emerald-600 hover:bg-emerald-700 text-white font-medium rounded-xl shadow-lg shadow-emerald-200 transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                Add User
            </a>
        </div>

        <!-- Card -->
        <div class="bg-white rounded-3xl shadow-xl overflow-hidden border border-slate-200 m-4">

            <!-- Card Header -->
            <div class="px-4 py-2 border-b bg-gradient-to-r from-slate-50 to-white flex items-center justify-between">
                <div>
                    <h2 class="text-xl font-bold text-slate-800">
                        All Users
                    </h2>
                    <p class="text-sm text-slate-500 mt-1">
                        <?php echo $total_users; ?> Total Users
                    </p>
                </div>
            </div>

            <!-- Table -->
            <div class="overflow-x-auto">

                <table class="w-full">

                    <thead>
                        <tr class="bg-slate-50 border-b text-slate-600 uppercase text-xs tracking-wider">
                            <th class="px-2 py-2 text-left">Id</th>
                            <th class="px-2 py-2 text-left">Username</th>
                            <th class="px-2 py-2 text-left">Role</th>
                            <th class="px-2 py-2 text-center">Actions</th>
                        </tr>
                    </thead>

                    <tbody class="divide-y divide-slate-100">

                        <?php if (mysqli_num_rows($users_result) > 0): ?>
                            <?php $i = $offset + 1;
                            while ($user = mysqli_fetch_assoc($users_result)): ?>

                                <tr class="hover:bg-slate-50 transition duration-200">

                                    <td class="px-2 py-2 text-slate-500 font-medium">
                                        <?php echo $user['id']; ?>
                                    </td>

                                    <td class="px-2 py-2">
                                        <div class="flex items-center gap-3">
                                            <div
                                                class="w-7 h-7 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center font-bold">
                                                <?php echo strtoupper(substr($user['username'], 0, 1)); ?>
                                            </div>

                                            <div>
                                                <div class="font-semibold text-slate-800">
                                                    <?php echo htmlspecialchars($user['username']); ?>
                                                </div>
                                            </div>
                                        </div>
                                    </td>

                                    <td class="px-2 py-2">
                                        <?php if ($user['role'] == '1'): ?>
                                            <span
                                                class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-purple-100 text-purple-700">
                                                Admin
                                            </span>
                                        <?php else: ?>
                                            <span
                                                class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-blue-100 text-blue-700">
                                                User
                                            </span>
                                        <?php endif; ?>
                                    </td>

                                    <td class="px-2 py-2 text-center">
                                        <div class="relative inline-block text-left">

                                            <button onclick="toggleMenu(<?php echo $user['id']; ?>)"
                                                class="p-1 rounded hover:bg-gray-100">
                                                <svg class="w-5 h-5 text-gray-600" fill="currentColor" viewBox="0 0 20 20">
                                                    <path
                                                        d="M10 3a1.5 1.5 0 110 3 1.5 1.5 0 010-3zm0 5.5a1.5 1.5 0 110 3 1.5 1.5 0 010-3zm0 5.5a1.5 1.5 0 110 3 1.5 1.5 0 010-3z" />
                                                </svg>
                                            </button>

                                            <div id="menu-<?php echo $user['id']; ?>"
                                                class="hidden absolute right-0 mt-1 w-32 bg-white border rounded-lg shadow-lg z-50">

                                                <a href="edit_user.php?id=<?php echo $user['id']; ?>"
                                                    class="block px-3 py-2 text-sm hover:bg-gray-50">
                                                    Edit
                                                </a>

                                                <a href="delete_user.php?id=<?php echo $user['id']; ?>"
                                                    onclick="return confirm('Delete this user?')"
                                                    class="block px-3 py-2 text-sm text-red-600 hover:bg-red-50">
                                                    Delete
                                                </a>

                                            </div>

                                        </div>
                                    </td>

                                </tr>

                            <?php endwhile; ?>

                        <?php else: ?>

                            <tr>
                                <td colspan="4" class="py-16 text-center">

                                    <div class="flex flex-col items-center">

                                        <div
                                            class="w-20 h-20 rounded-full bg-slate-100 flex items-center justify-center mb-4">
                                            <svg class="w-10 h-10 text-slate-400" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                                    d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z" />
                                            </svg>
                                        </div>

                                        <h3 class="text-lg font-semibold text-slate-700">
                                            No Users Found
                                        </h3>

                                        <p class="text-slate-400 mt-1">
                                            Start by adding your first user.
                                        </p>

                                    </div>

                                </td>
                            </tr>

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

            document.getElementById('menu-' + id)
                .classList.toggle('hidden');
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