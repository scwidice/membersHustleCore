<?php
session_start();
include 'dbConnect.php';

// Check if the admin is logged in
if (!isset($_SESSION['adminLoggedIn']) || $_SESSION['adminLoggedIn'] !== true) {
    header("Location: signIn.php");
    exit();
}

// Get the admin's name from the session
$adminName = isset($_SESSION['adminName']) ? htmlspecialchars($_SESSION['adminName']) : 'Admin';

// Fetch report data
// Total Members
$totalMembersQuery = $conn->prepare("SELECT COUNT(*) as total FROM Members");
$totalMembersQuery->execute();
$totalMembersResult = $totalMembersQuery->get_result();
$totalMembers = $totalMembersResult->fetch_assoc()['total'] ?? 0;

// Today's Enrollments
$today = date('Y-m-d');
$dailyEnrollmentsQuery = $conn->prepare("SELECT COUNT(*) as total FROM ActivityBookings WHERE DATE(createdAt) = ?");
$dailyEnrollmentsQuery->bind_param("s", $today);
$dailyEnrollmentsQuery->execute();
$dailyEnrollmentsResult = $dailyEnrollmentsQuery->get_result();
$dailyEnrollments = $dailyEnrollmentsResult->fetch_assoc()['total'] ?? 0;

// Today's Sessions
$dailySessionsQuery = $conn->prepare("SELECT COUNT(*) as total FROM Activities WHERE DATE(schedule) = ?");
$dailySessionsQuery->bind_param("s", $today);
$dailySessionsQuery->execute();
$dailySessionsResult = $dailySessionsQuery->get_result();
$dailySessions = $dailySessionsResult->fetch_assoc()['total'] ?? 0;

// Cancellations
$cancellationsQuery = $conn->prepare("SELECT COUNT(*) as total FROM ActivityBookings WHERE bookingStatus = 'cancelled' AND DATE(updatedAt) = ?");
$cancellationsQuery->bind_param("s", $today);
$cancellationsQuery->execute();
$cancellationsResult = $cancellationsQuery->get_result();
$cancellations = $cancellationsResult->fetch_assoc()['total'] ?? 0;

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Admin Reports - HustleCore</title>
    <link rel="stylesheet" href="adminStyles.css">
</head>
<body>
    <header>
        <a href="adminDashboard.php"> <image class="logo" src="images/logo.png" alt="gym logo"> </image> </a>
        <div class="user">
            <span><?php echo htmlspecialchars($adminName); ?></span>
            <form method="post" action="signIn.php">
                <button type="submit" name="logout">Log out</button>
            </form>
        </div>
    </header>

    <main>
        <div class="sideBar">
            <h4>Admin Panel</h4>
            <a href="adminDashboard.php">Dashboard</a>
            <a href="adminManageActivities.php">Manage Classes & Sessions</a>
            <a href="adminMembers.php">Members</a>
            <a href="adminManageBookings.php">Bookings</a>
            <p class="currentPage"><a href="adminReports.php">Reports</a></p>
        </div>

        <div class="dashboardContent">
            <div class="breadcrumb">&gt; Reports</div>
            <h2 class="adminHeading2">Reports</h2>

            <h3>Daily Reports</h3>
            <table class="dailyReportsTable" border="1">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Daily Enrollment</th>
                        <th>Daily Sessions</th>
                        <th>Bookings per Session</th>
                    </tr>
                </thead>
                <tbody>
                <?php
                // Get the last 7 days (or all days with data)
                $reportQuery = $conn->query("
                    SELECT d.report_date,
                        IFNULL(e.enrollments, 0) AS enrollments,
                        IFNULL(s.sessions, 0) AS sessions
                    FROM (
                        SELECT CURDATE() as report_date
                        UNION SELECT CURDATE() - INTERVAL 1 DAY
                        UNION SELECT CURDATE() - INTERVAL 2 DAY
                        UNION SELECT CURDATE() - INTERVAL 3 DAY
                        UNION SELECT CURDATE() - INTERVAL 4 DAY
                        UNION SELECT CURDATE() - INTERVAL 5 DAY
                        UNION SELECT CURDATE() - INTERVAL 6 DAY
                    ) d
                    LEFT JOIN (
                        SELECT DATE(createdAt) as enroll_date, COUNT(*) as enrollments
                        FROM Members
                        WHERE createdAt IS NOT NULL
                        GROUP BY enroll_date
                    ) e ON d.report_date = e.enroll_date
                    LEFT JOIN (
                        SELECT DATE(schedule) as session_date, COUNT(*) as sessions
                        FROM Activities
                        GROUP BY session_date
                    ) s ON d.report_date = s.session_date
                    ORDER BY d.report_date DESC
                ");
                while ($row = $reportQuery->fetch_assoc()):
                    $date = $row['report_date'];
                    // Get bookings per session for this date
                    $bookingsPerSession = [];
                    $sessionQuery = $conn->prepare("SELECT activityID, activityName FROM Activities WHERE DATE(schedule) = ?");
                    $sessionQuery->bind_param("s", $date);
                    $sessionQuery->execute();
                    $sessionsResult = $sessionQuery->get_result();
                    while ($session = $sessionsResult->fetch_assoc()) {
                        $activityID = $session['activityID'];
                        $activityName = $session['activityName'];
                        $bookingCountQuery = $conn->prepare("SELECT COUNT(*) as cnt FROM ActivityBookings WHERE activityID = ? AND DATE(createdAt) = ?");
                        $bookingCountQuery->bind_param("is", $activityID, $date);
                        $bookingCountQuery->execute();
                        $bookingCountResult = $bookingCountQuery->get_result();
                        $cnt = $bookingCountResult->fetch_assoc()['cnt'] ?? 0;
                        $bookingsPerSession[] = htmlspecialchars($activityName) . ": " . $cnt;
                        $bookingCountQuery->close();
                    }
                    $sessionQuery->close();
                ?>
                    <tr>
                        <td><?= htmlspecialchars($date) ?></td>
                        <td><?= htmlspecialchars($row['enrollments']) ?></td>
                        <td><?= htmlspecialchars($row['sessions']) ?></td>
                        <td><?= $bookingsPerSession ? implode('<br>', $bookingsPerSession) : 'No sessions' ?></td>
                    </tr>
                <?php endwhile; ?>
                </tbody>
            </table>

        </div>
    </main>
</body>
</html>
