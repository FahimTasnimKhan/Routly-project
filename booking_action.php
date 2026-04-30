<?php
session_start();
require_once('DBconnect.php');

if(!isset($_SESSION['passenger_id'])){
    header("Location: index.html");
    exit();
}

$passenger_id = $_SESSION['passenger_id'];
$error = "";
$success = "";

if($_SERVER['REQUEST_METHOD'] == 'POST'){
    $selected_driver = $_POST['driver_id'];
    $selected_route = $_POST['route_id'];

    $sql = "UPDATE passenger_info SET route_id = '$selected_route' WHERE passenger_id = '$passenger_id'";
    $result = mysqli_query($conn, $sql);

    if($result){
        $success = "Ride booked successfully!";
    } else {
        $error = "Something went wrong. Please try again.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <script src="https://cdn.tailwindcss.com"></script>
    <title>Booking Status</title>
</head>
<body class="bg-gray-100 min-h-screen flex items-center justify-center">
    <div class="bg-white p-8 rounded shadow-md w-full max-w-md text-center">
        <?php if($success): ?>
            <p class="text-green-600 text-xl font-bold"><?php echo $success; ?></p>
            <a href="ride_booking.php" class="block mt-4 text-blue-500 underline">Book another ride</a>
        <?php endif; ?>

        <?php if($error): ?>
            <p class="text-red-600 text-xl font-bold"><?php echo $error; ?></p>
            <a href="ride_booking.php" class="block mt-4 text-blue-500 underline">Try again</a>
        <?php endif; ?>
    </div>
</body>
</html>