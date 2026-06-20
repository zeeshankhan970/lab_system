<?php // No html, head, body, or auth check here ?>

<!-- SIDEBAR -->
<aside class="group bg-white border-r border-gray-200 text-gray-700 flex flex-col w-20 hover:w-64 transition-all duration-300 ease-in-out shadow-lg overflow-y-auto">
    <nav class="flex flex-col flex-1 py-6 space-y-2">

        <a href="dashboard.php"
            class="flex items-center gap-4 px-4 py-3 mx-2 rounded-xl hover:bg-blue-50 hover:text-blue-700">
            <i class="fas fa-tachometer-alt text-xl w-6 text-center text-blue-500"></i>
            <span class="hidden group-hover:inline-block text-sm font-medium whitespace-nowrap">Dashboard</span>
        </a>

        <a href="patients.php"
            class="flex items-center gap-4 px-4 py-3 mx-2 rounded-xl hover:bg-blue-50 hover:text-blue-700">
            <i class="fas fa-users text-xl w-6 text-center text-emerald-600"></i>
            <span class="hidden group-hover:inline-block text-sm font-medium whitespace-nowrap">All Patients</span>
        </a>

        <a href="add_patient.php"
            class="flex items-center gap-4 px-4 py-3 mx-2 rounded-xl hover:bg-blue-50 hover:text-blue-700">
            <i class="fas fa-user-plus text-xl w-6 text-center text-emerald-600"></i>
            <span class="hidden group-hover:inline-block text-sm font-medium whitespace-nowrap">Add Patient</span>
        </a>

        <a href="reports.php"
            class="flex items-center gap-4 px-4 py-3 mx-2 rounded-xl hover:bg-blue-50 hover:text-blue-700">
            <i class="fas fa-chart-bar text-xl w-6 text-center text-blue-600"></i>
            <span class="hidden group-hover:inline-block text-sm font-medium whitespace-nowrap">Reports</span>
        </a>

        <a href="manage_tests.php"
            class="flex items-center gap-4 px-4 py-3 mx-2 rounded-xl hover:bg-blue-50 hover:text-blue-700">
            <i class="fas fa-flask text-xl w-6 text-center text-purple-600"></i>
            <span class="hidden group-hover:inline-block text-sm font-medium whitespace-nowrap">Manage Tests</span>
        </a>

        <a href="manage_doctors.php"
            class="flex items-center gap-4 px-4 py-3 mx-2 rounded-xl hover:bg-blue-50 hover:text-blue-700">
            <i class="fas fa-user-md text-xl w-6 text-center text-amber-600"></i>
            <span class="hidden group-hover:inline-block text-sm font-medium whitespace-nowrap">Manage Doctors</span>
        </a>

        <a href="all_users.php"
            class="flex items-center gap-4 px-4 py-3 mx-2 rounded-xl hover:bg-blue-50 hover:text-blue-700">
            <i class="fas fa-user-cog text-xl w-6 text-center text-gray-600"></i>
            <span class="hidden group-hover:inline-block text-sm font-medium whitespace-nowrap">Manage Users</span>
        </a>

        <a href="../logout.php"
            class="flex items-center gap-4 px-4 py-3 mx-2 rounded-xl hover:bg-red-50 hover:text-red-600 mt-auto">
            <i class="fas fa-sign-out-alt text-xl w-6 text-center text-red-500"></i>
            <span class="hidden group-hover:inline-block text-sm font-medium whitespace-nowrap">Logout</span>
        </a>

    </nav>
</aside>