<?php
session_start();
require_once('DBconnect.php');

if(!isset($_SESSION['passenger_id'])){
    header("Location: index.html");
    exit();
}

$passenger_id = $_SESSION['passenger_id'];
$step = isset($_POST['step']) ? $_POST['step'] : '1';
$success = "";
$error = "";

// Fetch drivers
$drivers_sql = "SELECT driver_id, d_first_name, c_type FROM driver_info";
$drivers_result = mysqli_query($conn, $drivers_sql);

// Fetch available routes with price
$routes_sql = "SELECT route_id, r_start, r_end, route_price FROM route WHERE no_go_flag = 0";
$routes_result = mysqli_query($conn, $routes_sql);

// Step 2: Calculate fare and show confirmation
if($step == '2' && $_SERVER['REQUEST_METHOD'] == 'POST'){
    $selected_driver_id = $_POST['driver_id'];
    $selected_route_id = $_POST['route_id'];
    $split = $_POST['split'];

    // Fetch selected driver name
    $driver_sql = "SELECT d_first_name, c_type FROM driver_info WHERE driver_id = '$selected_driver_id'";
    $driver_result = mysqli_query($conn, $driver_sql);
    $driver = mysqli_fetch_assoc($driver_result);

    // Fetch selected route and price
    $route_sql = "SELECT r_start, r_end, route_price FROM route WHERE route_id = '$selected_route_id'";
    $route_result = mysqli_query($conn, $route_sql);
    $route = mysqli_fetch_assoc($route_result);

    $total_price = $route['route_price'];
    $your_share = number_format($total_price / $split, 2);
}

// Step 3: Confirm and save booking
if($step == '3' && $_SERVER['REQUEST_METHOD'] == 'POST'){
    $selected_driver_id = $_POST['driver_id'];
    $selected_route_id = $_POST['route_id'];
    $your_share = $_POST['your_share'];

    $sql = "UPDATE passenger_info SET route_id = '$selected_route_id' WHERE passenger_id = '$passenger_id'";
    $result = mysqli_query($conn, $sql);

    if($result){
        $success = "Ride booked! Your fare is $" . $your_share;
    } else {
        $error = "Something went wrong. Please try again.";
    }
}
?>

<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <script src="https://cdn.tailwindcss.com"></script>
    <title>Book Your Ride</title>
</head>
<body class="bg-gray-100 min-h-screen flex items-center justify-center">
<div class="bg-white p-8 rounded shadow-md w-full max-w-md">

    <!-- SUCCESS -->
    <?php if($success): ?>
        <h2 class="text-2xl font-bold mb-4 text-center">Booking Confirmed!</h2>
        <p class="text-green-600 text-xl font-bold text-center"><?php echo $success; ?></p>
        <a href="ride_booking.php" class="block mt-6 text-center text-blue-500 underline">Book another ride</a>

    <!-- ERROR -->
    <?php elseif($error): ?>
        <h2 class="text-2xl font-bold mb-4 text-center">Something went wrong</h2>
        <p class="text-red-600 text-xl font-bold text-center"><?php echo $error; ?></p>
        <a href="ride_booking.php" class="block mt-6 text-center text-blue-500 underline">Try again</a>

    <!-- STEP 2: Show fare and confirm -->
    <?php elseif($step == '2'): ?>
        <h2 class="text-2xl font-bold mb-6 text-center">Confirm Your Ride</h2>

        <div class="mb-3 p-4 bg-gray-50 border border-gray-200 rounded text-sm">
            <p class="mb-1"><span class="font-semibold">Driver:</span> <?php echo $driver['d_first_name'] . " (" . $driver['c_type'] . ")"; ?></p>
            <p class="mb-1"><span class="font-semibold">Route:</span> <?php echo $route['r_start'] . " → " . $route['r_end']; ?></p>
            <p class="mb-1"><span class="font-semibold">Total Price:</span> $<?php echo number_format($total_price, 2); ?></p>
            <p class="mb-1"><span class="font-semibold">Split Between:</span> <?php echo $split; ?> person(s)</p>
            <p class="mt-2 text-lg"><span class="font-semibold">Your Share:</span> <span class="text-green-600 font-bold">$<?php echo $your_share; ?></span></p>
        </div>

        <form action="ride_booking.php" method="POST">
            <input type="hidden" name="step" value="3">
            <input type="hidden" name="driver_id" value="<?php echo $selected_driver_id; ?>">
            <input type="hidden" name="route_id" value="<?php echo $selected_route_id; ?>">
            <input type="hidden" name="your_share" value="<?php echo $your_share; ?>">

            <button type="submit" class="w-full bg-black text-white py-2 rounded font-bold text-lg hover:bg-gray-800 mt-4">
                Confirm Booking
            </button>
        </form>

        <a href="ride_booking.php" class="block mt-3 text-center text-blue-500 underline text-sm">Go back</a>

    <!-- STEP 1: Selection form -->
    <?php else: ?>
        <h2 class="text-2xl font-bold mb-6 text-center">Book a Ride</h2>

        <form action="ride_booking.php" method="POST">
            <input type="hidden" name="step" value="2">

            <!-- Driver -->
            <div class="mb-4">
                <label class="block font-semibold mb-1">Select Driver</label>
                <select name="driver_id" required class="w-full border border-gray-300 rounded px-3 py-2">
                    <option value="">-- Choose a driver --</option>
                    <?php while($driver = mysqli_fetch_assoc($drivers_result)): ?>
                        <option value="<?php echo $driver['driver_id']; ?>">
                            <?php echo $driver['d_first_name'] . " (" . $driver['c_type'] . ")"; ?>
                        </option>
                    <?php endwhile; ?>
                </select>
            </div>

            <!-- Route -->
            <div class="mb-4">
                <label class="block font-semibold mb-1">Select Route</label>
                <select name="route_id" required class="w-full border border-gray-300 rounded px-3 py-2">
                    <option value="">-- Choose a route --</option>
                    <?php while($route = mysqli_fetch_assoc($routes_result)): ?>
                        <option value="<?php echo $route['route_id']; ?>">
                            <?php echo $route['r_start'] . " → " . $route['r_end']; ?>
                        </option>
                    <?php endwhile; ?>
                </select>
            </div>

            <!-- Fare Split -->
            <div class="mb-6">
                <label class="block font-semibold mb-1">Split Fare With</label>
                <select name="split" class="w-full border border-gray-300 rounded px-3 py-2">
                    <option value="1">No splitting (just me)</option>
                    <option value="2">1 other person (split between 2)</option>
                    <option value="3">2 other people (split between 3)</option>
                    <option value="4">3 other people (split between 4)</option>
                </select>
            </div>

            <button type="submit" class="w-full bg-black text-white py-2 rounded font-bold text-lg hover:bg-gray-800">
                See Fare
            </button>
        </form>
    <?php endif; ?>

</div>
</body>
</html>