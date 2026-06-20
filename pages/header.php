<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['admin'])) {
    header("Location: ../login.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Only FA6, removed duplicate FA5 -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body class="bg-gradient-to-br from-gray-50 to-gray-100 font-sans antialiased">

    <!-- HEADER -->
    <div class="bg-gradient-to-r from-blue-700 via-blue-800 to-indigo-900 text-white shadow-xl sticky top-0 z-20">
        <div class="max-w-full mx-auto px-6 py-4 flex justify-between items-center">
            <div class="flex items-center gap-3">
                <div class="bg-white/20 p-2 rounded-xl backdrop-blur-sm">
                    <i class="fas fa-microscope text-2xl"></i>
                </div>
                <div>
                    <h1 class="text-2xl font-bold tracking-tight">
                        Laboratory <span class="text-blue-200">Dashboard</span>
                    </h1>
                    <p class="text-xs text-blue-200">Patient Registration & Billing System</p>
                </div>
            </div>
            <a href="dashboard.php"
                class="bg-red-500/80 hover:bg-red-600 text-white px-4 py-2 rounded-lg text-sm font-semibold flex items-center gap-2">
                <i class="fas fa-user"></i>
                <?php echo isset($_SESSION['admin']) ? htmlspecialchars($_SESSION['admin']) : 'Guest'; ?>
            </a>
        </div>
    </div>

    <!-- MAIN LAYOUT: opens flex wrapper (closed in footer.php) -->
    <div class="flex min-h-[calc(100vh-80px)]">