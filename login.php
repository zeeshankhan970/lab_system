<?php
include 'config.php';

if(isset($_POST['login'])){

    $username = $_POST['username'];
    $password = md5($_POST['password']);

    $query = "SELECT * FROM users 
              WHERE username='$username' 
              AND password='$password'";

    $result = mysqli_query($conn,$query);

    if(mysqli_num_rows($result) > 0){

        $_SESSION['admin'] = $username;

header("Location: pages/dashboard.php");

    }else{
        $error = "Invalid Login";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
<title>Login</title>

<script src="https://cdn.tailwindcss.com"></script>

</head>
<body class="bg-gray-100 flex items-center justify-center h-screen">

<form method="POST" class="bg-white p-8 rounded shadow w-96">

<h2 class="text-2xl font-bold mb-6 text-center">
Lab System Login
</h2>

<?php if(isset($error)){ ?>
<p class="text-red-500 mb-4">
<?php echo $error; ?>
</p>
<?php } ?>

<input type="text"
name="username"
placeholder="Username"
class="w-full border p-3 mb-4 rounded"
required>

<input type="password"
name="password"
placeholder="Password"
class="w-full border p-3 mb-4 rounded"
required>

<button
name="login"
class="bg-blue-500 text-white w-full p-3 rounded">
Login
</button>

</form>

</body>
</html>