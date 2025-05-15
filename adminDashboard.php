<?php
include 'dbConnect.php';
include 'adminSessionHandler.php';

// Fetch total members count
$totalMembersQuery = $conn->prepare("SELECT COUNT(*) as total FROM Members");
$totalMembersQuery->execute();
$totalMembersResult = $totalMembersQuery->get_result();
$totalMembers = $totalMembersResult->fetch_assoc()['total'] ?? 0;

// Fetch today's activities
$today = date('Y-m-d');
$todaysActivitiesQuery = $conn->prepare("SELECT activityID, activityName, instructor, schedule FROM Activities WHERE DATE(schedule) = ?");
$todaysActivitiesQuery->bind_param("s", $today);
$todaysActivitiesQuery->execute();
$todaysActivitiesResult = $todaysActivitiesQuery->get_result();

// fetch cancellations
$cancellationsQuery = $conn->prepare("SELECT COUNT(*) as total FROM ActivityBookings WHERE bookingStatus = 'cancelled' AND DATE(updatedAt) = ?");
$cancellationsQuery->bind_param("s", $today);
$cancellationsQuery->execute();
$cancellationsResult = $cancellationsQuery->get_result();
$cancellations = $cancellationsResult->fetch_assoc()['total'] ?? 0;


// Today's Bookings
$todayBookingsQuery = $conn->prepare("SELECT COUNT(*) as total FROM ActivityBookings WHERE DATE(createdAt) = ?");
$todayBookingsQuery->bind_param("s", $today);
$todayBookingsQuery->execute();
$todayBookingsResult = $todayBookingsQuery->get_result();
$todayBookings = $todayBookingsResult->fetch_assoc()['total'] ?? 0;

// Today's Sessions
$dailySessionsQuery = $conn->prepare("SELECT COUNT(*) as total FROM Activities WHERE DATE(schedule) = ?");
$dailySessionsQuery->bind_param("s", $today);
$dailySessionsQuery->execute();
$dailySessionsResult = $dailySessionsQuery->get_result();
$dailySessions = $dailySessionsResult->fetch_assoc()['total'] ?? 0;
?>



<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Admin Dashboard - HusleCore</title>
    <link rel="stylesheet" href="adminStyles.css">
</head>


<body>
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
            <p class="currentPage"><a href="adminDashboard.php">Dashboard</a></p>
            <a href="adminManageActivities.php">Manage Classes & Sessions</a>
            <a href="adminMembers.php">Members</a>
            <a href="adminManageBookings.php">Bookings</a>
            <a href="adminReports.php">Reports</a>
        </div>

        <!-- Dashboard Content Area -->
        <div class="dashboardContent">
            <div class="breadcrumb">&gt; Dashboard</div>
            <div class="stats">
                <div class="card">
                <h5>Total Members</h5>
                <p><?= $totalMembers ?></p>
                </div>
                <div class="card">
                <h5>Today's Bookings</h5>
                <p><?= $todayBookings ?></p>
                </div>
                <div class="card">
                <h5>Today's Sessions</h5>
                <p><?=$dailySessions ?></p> 
                </div>
                <div class="card">
                <h5>Cancellations</h5>
                <p><?= $cancellations?> </p> 
                </div>
            </div>

        <div class="adminTodaySchedule">
            <h3>Today’s Schedule / Activities</h3>
            <?php while ($activity = $todaysActivitiesResult->fetch_assoc()): ?>
                <?php
                $activityID = $activity['activityID'];
                $enrolledResult = $conn->query("SELECT COUNT(*) as enrolled FROM ActivityBookings WHERE activityID = $activityID");
                $enrolledRow = $enrolledResult->fetch_assoc();
                $enrolledCount = $enrolledRow ? $enrolledRow['enrolled'] : 0;
                ?>
                <div class="todayActivityBox">
                    <p><strong><?php echo htmlspecialchars($activity['activityName']); ?></strong></p>
                    <p>
                        <?php echo date('h:i A', strtotime($activity['schedule'])); ?>
                        <?php if (!empty($activity['instructor'])): ?>
                            &nbsp; &bull; <?php echo htmlspecialchars($activity['instructor']); ?> 
                        <?php endif; ?>
                        &nbsp; &bull; <?php echo $enrolledCount; ?> enrolled
                    </p>
                </div>
            <?php endwhile; ?>
        </div>  
    </main>

</body>
</html>
