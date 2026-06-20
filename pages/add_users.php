<?php
include '../config.php';
include 'header.php';
include 'sidebar.php';
if (!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit();
}
if (isset($_POST['submit'])) {
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $password = mysqli_real_escape_string($conn, md5($_POST['password']));
    $role = mysqli_real_escape_string($conn, $_POST['role']);
    $sql = "SELECT * FROM users WHERE username = '{$username}'";

    $result = mysqli_query($conn, $sql);
    if (mysqli_num_rows($result) > 0) {
        $error = "Username already exists";
    } else {
        $sql = "INSERT INTO users (username, password, role) VALUES ('{$username}', '{$password}', '{$role}')";
        if (mysqli_query($conn, $sql) === TRUE) {
            $success = "User added successfully";
        } else {
            $error = "Unable to add user";
        }
    }
}

// Fetch all users
$users_result = mysqli_query($conn, "SELECT * FROM users ORDER BY id DESC");
?>

<body class="min-h-screen">

    <div class="container mx-auto px-4 py-2">

        <div class="max-w-md mx-auto px-4">

            <!-- Card -->
            <div class="bg-white rounded-3xl shadow-2xl overflow-hidden">

                <!-- Header -->
                <div class="bg-gradient-to-r from-blue-600 to-indigo-700 px-5 py-4 text-center">
                    <h1 class="text-xl font-bold text-white">
                        Add New User
                    </h1>
                    <p class="text-blue-100 mt-2">
                        Create a new system user account
                    </p>
                </div>

                <!-- Form Area -->
                <div class="p-6 overflow-y-auto">

                    <?php if (isset($error)): ?>
                        <div id="alertMessage" class="alert-message mb-6 flex items-center gap-3 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-xl">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none"
                                viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <?= $error ?>
                        </div>
                    <?php endif; ?>

                    <?php if (isset($success)): ?>
                        <div id="alertMessage" class="alert-message mb-6 flex items-center gap-3 bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-xl">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none"
                                viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M5 13l4 4L19 7" />
                            </svg>
                            <?= $success ?>
                        </div>
                    <?php endif; ?>

                    <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="POST" autocomplete="off">

                        <!-- Username -->
                        <div class="mb-4">
                            <label class="block text-sm font-semibold text-gray-700 mb-2">
                                Username
                            </label>

                            <input
                                type="text"
                                name="username"
                                required
                                placeholder="Enter username"
                                class="w-full px-4 py-2 border border-gray-300 rounded-xl focus:ring-4 focus:ring-blue-100 focus:border-blue-500 outline-none transition">
                        </div>

                        <!-- Password -->
                        <div class="mb-4">
                            <label class="block text-sm font-semibold text-gray-700 mb-2">
                                Password
                            </label>

                            <input
                                type="password"
                                name="password"
                                required
                                placeholder="Enter password"
                                class="w-full px-4 py-2 border border-gray-300 rounded-xl focus:ring-4 focus:ring-blue-100 focus:border-blue-500 outline-none transition">
                        </div>

                        <!-- Role -->
                        <div class="mb-5">
                            <label class="block text-sm font-semibold text-gray-700 mb-2">
                                User Role
                            </label>

                            <select
                                name="role"
                                required
                                class="w-full px-4 py-2 border border-gray-300 rounded-xl bg-white focus:ring-4 focus:ring-blue-100 focus:border-blue-500 outline-none transition">
                                <option value="0">User</option>
                                <option value="1">Admin</option>
                            </select>
                        </div>

                        <!-- Buttons -->
                        <div class="flex flex-col sm:flex-row gap-3">

                            <button
                                type="submit"
                                name="submit"
                                class="flex-1 bg-gradient-to-r from-blue-600 to-indigo-700 hover:from-blue-700 hover:to-indigo-800 text-white font-semibold py-3 rounded-xl shadow-lg transition duration-300 hover:-translate-y-1">
                                Add User
                            </button>

                            <a href="all_users.php"
                                class="flex-1 text-center border border-gray-300 hover:bg-gray-100 py-3 rounded-xl font-medium text-gray-700 transition">
                                View Users
                            </a>

                        </div>

                    </form>

                </div>
            </div>
        </div>

    </div>

    </div>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            setTimeout(function() {
                document.querySelectorAll('.alert-message').forEach(function(alert) {

                    alert.style.transition = 'opacity 0.5s ease, transform 0.5s ease';
                    alert.style.opacity = '0';
                    alert.style.transform = 'translateY(-10px)';

                    setTimeout(function() {
                        alert.remove();
                    }, 500);

                });
            }, 3000); // 3 seconds
        });
    </script>
    <?php include 'footer.php'; ?>