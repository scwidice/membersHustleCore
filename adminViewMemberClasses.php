<?php
    include 'dbConnect.php';
    include 'adminSessionHandler.php';

    if (!isset($_GET['memberID'])) {
        die("Member ID not provided.");
    }

    $memberID = intval($_GET['memberID']);

    // Handle status change for bookings
    if (isset($_POST['status']) && isset($_POST['bookingID'])) {
        $status = $_POST['status'];
        $bookingID = intval($_POST['bookingID']);

        $updateStatusQuery = $conn->prepare("UPDATE ActivityBookings SET bookingStatus = ? WHERE bookingID = ?");
        $updateStatusQuery->bind_param("si", $status, $bookingID);

        if ($updateStatusQuery->execute()) {
            echo "<script>alert('Status updated successfully!'); window.location.href='adminViewMemberClasses.php?memberID=$memberID';</script>";
        } else {
            echo "<script>alert('Error updating status: " . $updateStatusQuery->error . "');</script>";
        }

        $updateStatusQuery->close();
    }

    // get member name
    $memberQuery = $conn->prepare("SELECT firstName, lastName FROM Members WHERE memberID = ?");
    $memberQuery->bind_param("i", $memberID);
    $memberQuery->execute();
    $memberResult = $memberQuery->get_result();
    $memberName = $memberResult->fetch_assoc();

    // Fetch classes the member is enrolled in
    $classesQuery = $conn->prepare(
        "SELECT a.activityName, a.schedule, ab.bookingStatus, a.activityID, ab.bookingID
         FROM ActivityBookings ab
         JOIN Activities a ON ab.activityID = a.activityID
         WHERE ab.memberID = ?"
    );
    $classesQuery->bind_param("i", $memberID);
    $classesQuery->execute();
    $classesResult = $classesQuery->get_result();

    // Check if the member exists
    if (!$memberName) {
        die("Member not found.");
    }

    // Display member name
    $memberFullName = htmlspecialchars($memberName['firstName'] . ' ' . $memberName['lastName']);
?>

<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>View Enrolled Classes - Hustle Core</title>
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
    
    <main>
        <!-- Sidebar -->
        <div class="sideBar">
        <h4>Admin Panel</h4>
        <a href="adminDashboard.php">Dashboard</a>
        <a href="adminManageActivities.php">Manage Classes & Sessions</a>
        <p class="currentPage"><a href="adminMembers.php">Members</a></p>
        <a href="adminManageBookings.php">Bookings</a>
        <a href="#">Reports</a>
        </div>     

        <!-- Content Area -->
        <div class="content">
            <div class="breadcrumb">&gt; <a href="adminMembers.php">Members</a>&gt; <a href="adminViewMemberClasses.php"> View Classes</a></div>
        
            <h2 class="adminHeading2">Classes Enrolled by: <?= $memberFullName ?></h2>

            <table border="1" class="attendanceActTable">
                <thead>
                    <tr>
                        <th>Class Name</th>
                        <th>Schedule</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $classesResult->fetch_assoc()): ?>
                        <tr>
                            <td><?= htmlspecialchars($row['activityName']) ?></td>
                            <td><?= htmlspecialchars($row['schedule']) ?></td>
                            <td><?= htmlspecialchars($row['bookingStatus']) ?></td>
                            <td>
                                <form method="post" action="adminViewMemberClasses.php?memberID=<?= $memberID ?>" style="display:inline">
                                    <input type="hidden" name="bookingID" value="<?= $row['bookingID'] ?>">
                                    <input type="hidden" name="memberID" value="<?= $memberID ?>">
                                    <button name="status" value="cancelled" type="submit" class="cancelBtn" <?= $row['bookingStatus'] === 'cancelled' ? 'disabled' : '' ?>>Cancel Booking</button>
                                </form>
                                <form method="get" action="adminChangeSchedule.php" style="display:inline">
                                    <input type="hidden" name="activityID" value="<?= $row['activityID'] ?>">
                                    <input type="hidden" name="memberID" value="<?= $memberID ?>">
                                    <button type="submit" class="changeScheduleBtn">Change Schedule</button>
                                </form>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>

        </div>

    </main>
</body>
</html>