<?php
session_start();
require_once('DBconnect.php');

if(!isset($_SESSION['passenger_id'])){
    header("Location: index.html");
    exit();
}

// Fetch drivers
$drivers_sql = "SELECT driver_id, d_first_name, c_type FROM driver_info";
$drivers_result = mysqli_query($conn, $drivers_sql);

// Fetch available routes only
$routes_sql = "SELECT route_id, r_start, r_end FROM route WHERE no_go_flag = 0";
$routes_result = mysqli_query($conn, $routes_sql);
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
      <h2 class="text-2xl font-bold mb-6 text-center">Book a Ride</h2>

      <form action="booking_action.php" method="POST">

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

        <div class="mb-6">
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

        <button type="submit" class="w-full bg-black text-white py-2 rounded font-bold text-lg hover:bg-gray-800">
          Book Ride
        </button>

      </form>
    </div>
  </body>
</html>