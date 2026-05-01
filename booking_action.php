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
$booking_done = false;
$rating_done = false;

// Step 1: Handle booking
if($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['driver_id'])){
    $selected_driver = $_POST['driver_id'];
    $selected_route = $_POST['route_id'];
    $your_share = $_POST['your_share'];

    $sql = "UPDATE passenger_info SET route_id = '$selected_route' WHERE passenger_id = '$passenger_id'";
    $result = mysqli_query($conn, $sql);

    if($result){
        $booking_done = true;
    } else {
        $error = "Something went wrong. Please try again.";
    }
}

// Step 2: Handle rating
if($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['rating'])){
    $rating = $_POST['rating'];
    $driver_to_rate = $_POST['driver_to_rate'];

    $sql = "UPDATE driver_rating SET skill_rating = '$rating' WHERE driver_id = '$driver_to_rate'";
    $result = mysqli_query($conn, $sql);

    if($result){
        $rating_done = true;
    } else {
        $error = "Rating could not be saved.";
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

    <?php if($rating_done): ?>
        <!-- Rating submitted -->
        <p class="text-green-600 text-xl font-bold">Thank you for your rating!</p>
        <a href="ride_booking.php" class="block mt-4 text-blue-500 underline">Book another ride</a>

    <?php elseif($booking_done): ?>
        <!-- Booking success, show rating form -->
        <p class="text-green-600 text-xl font-bold mb-2">Ride booked successfully!</p>
        <p class="text-gray-500 mb-1">Your fare: $<?php echo $your_share; ?></p>
        <p class="text-gray-700 font-semibold mt-4 mb-2">Rate your driver (1-5):</p>

        <form action="booking_action.php" method="POST">
            <input type="hidden" name="driver_to_rate" value="<?php echo $selected_driver; ?>">

            <select name="rating" required class="w-full border border-gray-300 rounded px-3 py-2 mb-4">
                <option value="">-- Select rating --</option>
                <option value="1">1 - Poor</option>
                <option value="2">2 - Fair</option>
                <option value="3">3 - Good</option>
                <option value="4">4 - Very Good</option>
                <option value="5">5 - Excellent</option>
            </select>

            <button type="submit" class="w-full bg-black text-white py-2 rounded font-bold text-lg hover:bg-gray-800">
                Submit Rating
            </button>
        </form>

        <a href="ride_booking.php" class="block mt-3 text-blue-500 underline text-sm">Skip rating</a>

    <?php elseif($error): ?>
        <!-- Error -->
        <p class="text-red-600 text-xl font-bold"><?php echo $error; ?></p>
        <a href="ride_booking.php" class="block mt-4 text-blue-500 underline">Try again</a>

    <?php endif; ?>

</div>
</body>
</html>