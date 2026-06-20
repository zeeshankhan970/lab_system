<?php
include '../config.php';


if (!isset($_GET['id']) || empty($_GET['id'])) {
    header("Location: ../login.php");
    exit();
}

$id = mysqli_real_escape_string($conn, $_GET['id']);

// Fetch existing user data
$sql = "SELECT * FROM users WHERE id = '{$id}'";
$result = mysqli_query($conn, $sql);

if (mysqli_num_rows($result) === 0) {
    header("Location: users.php");
    exit();
}

$user = mysqli_fetch_assoc($result);

if (isset($_POST['submit'])) {
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $role     = mysqli_real_escape_string($conn, $_POST['role']);

    // Check if username already exists for a DIFFERENT user
    $check_sql = "SELECT * FROM users WHERE username = '{$username}' AND id != '{$id}'";
    $check_result = mysqli_query($conn, $check_sql);

    if (mysqli_num_rows($check_result) > 0) {
        $error = "Username already exists";
    } else {
        // Update password only if a new one was provided
        if (!empty($_POST['password'])) {
            $password = mysqli_real_escape_string($conn, md5($_POST['password']));
            $sql = "UPDATE users SET username = '{$username}', password = '{$password}', role = '{$role}' WHERE id = '{$id}'";
        } else {
            $sql = "UPDATE users SET username = '{$username}', role = '{$role}' WHERE id = '{$id}'";
        }

        if (mysqli_query($conn, $sql) === TRUE) {
            // Redirect to all_users.php after successful update
            header("Location: all_users.php");
            exit();
        } else {
            $error = "Unable to update user";
        }
    }
}
include 'header.php';
include 'sidebar.php';
?>
<body class="bg-gradient-to-br from-gray-50 to-gray-100 min-h-screen flex items-center justify-center">
    <div class="w-full max-w-2xl mx-auto">

    <!-- Main Card -->
    <div class="bg-white rounded-[30px] shadow-2xl overflow-hidden border border-gray-100">

        <!-- Top Banner -->
        <div class="relative bg-gradient-to-br from-indigo-600 via-blue-600 to-cyan-500 px-2 py-2">

            <div class="absolute inset-0 opacity-10">
                <div class="absolute top-0 right-0 w-40 h-40 bg-white rounded-full -mr-10 -mt-10"></div>
                <div class="absolute bottom-0 left-0 w-32 h-32 bg-white rounded-full -ml-6 -mb-6"></div>
            </div>

            <div class="relative flex items-center gap-4">
                <div class="w-10 h-10 rounded-2xl bg-white/20 backdrop-blur flex items-center justify-center">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M5.121 17.804A13.937 13.937 0 0112 16c2.5 0 4.847.655 6.879 1.804M15 10a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                </div>

                <div>
                    <h2 class="text-xl font-bold text-white">
                        Edit User
                    </h2>
                    <p class="text-blue-100 mt-1">
                        Update account information and permissions
                    </p>
                </div>
            </div>

        </div>

        <div class="p-2">

            <?php if (isset($error)): ?>
                <div class="p-4 rounded-2xl bg-red-50 border border-red-200 text-red-700">
                    <?php echo $error; ?>
                </div>
            <?php endif; ?>

            <?php if (isset($success)): ?>
                <div class="p-2 rounded-2xl bg-green-50 border border-green-200 text-green-700">
                    <?php echo $success; ?>
                </div>
            <?php endif; ?>

            <form action="<?php echo $_SERVER['PHP_SELF'] . '?id=' . $id; ?>" method="POST" autocomplete="off" class="space-y-2">

                <!-- Username -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700">
                        Username
                    </label>

                    <input
                        type="text"
                        name="username"
                        value="<?php echo htmlspecialchars($user['username']); ?>"
                        required
                        class="w-full px-1 py-1 bg-gray-50 border-2 border-gray-100 rounded-2xl focus:border-blue-500 focus:bg-white focus:outline-none transition"
                    >
                </div>

                <!-- Password -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700">
                        New Password
                    </label>

                    <input
                        type="password"
                        name="password"
                        placeholder="Leave blank to keep current password"
                        class="w-full px-1 py-1 bg-gray-50 border-2 border-gray-100 rounded-2xl focus:border-blue-500 focus:bg-white focus:outline-none transition"
                    >

                    <p class="mt-2 text-xs text-gray-500">
                        Password remains unchanged if left empty.
                    </p>
                </div>

                <!-- Role -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700">
                        User Role
                    </label>

                    <select
                        name="role"
                        required
                        class="w-full px-1 py-1 bg-gray-50 border-2 border-gray-100 rounded-2xl focus:border-blue-500 focus:bg-white focus:outline-none transition"
                    >
                        <option value="0" <?php echo ($user['role'] == '0') ? 'selected' : ''; ?>>
                            Standard User
                        </option>

                        <option value="1" <?php echo ($user['role'] == '1') ? 'selected' : ''; ?>>
                            Administrator
                        </option>
                    </select>
                </div>

                <!-- Profile Summary -->
                <div class="bg-gradient-to-r from-gray-50 to-blue-50 rounded-2xl p-2 border border-gray-100">

                    <div class="flex justify-between items-center">
                        <span class="text-gray-500 text-sm">User ID</span>
                        <span class="font-bold text-blue-600">
                            <?php echo $user['id']; ?>
                        </span>
                    </div>

                    <div class="flex justify-between items-center">
                        <span class="text-gray-500 text-sm">Current Role</span>
                        <span class="font-semibold text-gray-800">
                            <?php echo ($user['role'] == '1') ? 'Admin' : 'User'; ?>
                        </span>
                    </div>

                </div>

                <!-- Buttons -->
                <div class="flex gap-4">

                    <a
                        href="all_users.php"
                        class="flex-1 py-2 bg-gray-200 hover:bg-gray-200 rounded-2xl text-center font-semibold text-gray-700 transition"
                    >
                        Cancel
                    </a>

                    <button
                        type="submit"
                        name="submit"
                        class="flex-1 py-2 rounded-2xl bg-gradient-to-r from-indigo-600 to-blue-600 hover:from-indigo-700 hover:to-blue-700 text-white font-bold shadow-lg transition"
                    >
                        Update User
                    </button>

                </div>

            </form>

        </div>

    </div>

</div>
            </div>
            <?php include 'footer.php'; ?>