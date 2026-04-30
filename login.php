<?php
session_start();
require_once('DBconnect.php');

if(isset($_POST['username']) && isset($_POST['password'])){
    $username = $_POST['username'];
    $password = $_POST['password'];

    $sql = "SELECT * FROM passenger_info WHERE passenger_id = '$username' AND p_password = '$password'";
    $result = mysqli_query($conn, $sql);

    if(mysqli_num_rows($result) > 0){
        $row = mysqli_fetch_assoc($result);
        $_SESSION['passenger_id'] = $row['passenger_id'];
        $_SESSION['p_first_name'] = $row['p_first_name'];
        header("Location: home.html");
    }
    else{
        echo "Wrong username or password";
    }
}
?>