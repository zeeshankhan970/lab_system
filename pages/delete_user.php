<?php
include '../config.php';

if (!isset($_GET['id']) || empty($_GET['id'])) {
    header("Location: all_users.php");
    exit();
}

$id = mysqli_real_escape_string($conn, $_GET['id']);

// Fetch user to confirm deletion
$sql    = "SELECT * FROM users WHERE id = '{$id}'";
$result = mysqli_query($conn, $sql);

if (mysqli_num_rows($result) === 0) {
    header("Location: all_users.php");
    exit();
}

$user = mysqli_fetch_assoc($result);

if (isset($_POST['confirm_delete'])) {
    $sql = "DELETE FROM users WHERE id = '{$id}'";
    if (mysqli_query($conn, $sql) === TRUE) {
        header("Location: all_users.php");
        exit();
    } else {
        $error = "Unable to delete user";
    }
}
include 'header.php';
include 'sidebar.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Delete User</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gradient-to-br from-gray-50 to-gray-100 min-h-screen flex items-center justify-center p-4">
    <div class="max-w-7xl mx-auto w-6/12 py-8">
        <div class="bg-gray-200 rounded-lg shadow-lg p-8">
            <h1 class="text-3xl font-bold text-gray-800 mb-8 text-center">Delete User</h1>

            <?php if (isset($error)): ?>
                <div class="mb-6 p-4 bg-red-50 border border-red-200 text-red-700 rounded-lg text-sm">
                    <?php echo $error; ?>
                </div>
            <?php endif; ?>

            <!-- Confirmation Card -->
            <div class="mb-6 p-5 bg-red-50 border border-red-200 rounded-lg">
                <div class="flex items-center gap-3 mb-4">
                    <!-- Warning icon -->
                    <div class="flex-shrink-0 w-10 h-10 bg-red-100 rounded-full flex items-center justify-center">
                        <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 9v2m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/>
                        </svg>
                    </div>
                    <p class="text-red-700 font-semibold text-base">This action cannot be undone.</p>
                </div>
                <p class="text-gray-700 text-sm">You are about to permanently delete the following user:</p>

                <!-- User details -->
                <div class="mt-4 bg-white rounded-lg p-4 border border-red-100 space-y-2">
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-500 font-medium">Username</span>
                        <span class="text-gray-800 font-semibold"><?php echo htmlspecialchars($user['username']); ?></span>
                    </div>
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-500 font-medium">Role</span>
                        <span class="text-gray-800 font-semibold">
                            <?php echo ($user['role'] == '1') ? 'Admin' : 'User'; ?>
                        </span>
                    </div>
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-500 font-medium">User ID</span>
                        <span class="text-gray-800 font-semibold">#<?php echo htmlspecialchars($user['id']); ?></span>
                    </div>
                </div>
            </div>

            <!-- Action buttons -->
            <form action="<?php echo $_SERVER['PHP_SELF'] . '?id=' . $id; ?>" method="POST">
                <div class="flex gap-3">
                    <a
                        href="all_users.php"
                        class="w-1/2 bg-gray-500 hover:bg-gray-600 text-white font-semibold py-2 px-4 rounded-lg transition duration-200 ease-in-out transform hover:scale-105 text-center"
                    >
                        Cancel
                    </a>
                    <button
                        type="submit"
                        name="confirm_delete"
                        class="w-1/2 bg-red-600 hover:bg-red-700 text-white font-semibold py-2 px-4 rounded-lg transition duration-200 ease-in-out transform hover:scale-105"
                    >
                        Yes, Delete User
                    </button>
                </div>
            </form>
        </div>
    </div>
</body>
</html>