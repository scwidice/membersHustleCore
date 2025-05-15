<?php
include 'dbConnect.php';
include 'dbTablesSetup.php';
include 'adminSessionHandler.php';

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Members - Hustle Core</title>
    <link rel="stylesheet" href="adminStyles.css">
    <style>

    </style>
</head>


<body>
    <!-- Top bar -->
    <header>
        <a href="adminDashboard.php"> <image class="logo" src="images/logo.png" alt="gym logo" > </image> </a>
        
        <div class="user">
        <span><?php echo htmlspecialchars($adminName); ?></span>
        <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
            <button type="submit" name="logout">Log out</button>
        </form>
        </div>
    </header>

    <!-- Main section -->
    <main>
    
        <!-- Sidebar -->
        <div class="sideBar">
        <h4>Admin Panel</h4>
        <a href="adminDashboard.php">Dashboard</a>
        <a href="adminManageActivities.php">Manage Classes & Sessions</a>
        <a href="adminMembers.php">Members</a>
        <p class="currentPage"> <a href="adminManageBookings.php">Bookings</a></p>
        <a href="#">Reports</a>
        </div>

        <!-- Content Area -->
        <div class="content">
            <div class="breadcrumb">&gt; Bookings</div>
            <h2>Bookings</h2>

    </main>
</body>
</html>