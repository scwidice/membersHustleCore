<?php

include 'dbConnect.php';
include 'dbTablesSetup.php';
include 'adminSessionHandler.php';

// for debugging: $conn->query("DROP TABLE IF EXISTS Activities");

// activity for  modal form
if (isset($_POST['submitActivity'])) {
    $activityType = $_POST['activityType'];
    $activityName = $_POST['activityName'];
    $instructor = !empty($_POST['instructor']) ? $_POST['instructor'] : NULL; // if maguba, replace this line with-> 
    $schedule = $_POST['schedule'];
    $duration = $_POST['duration'];
    $capacity = $_POST['capacity'];

    $sqlInsertActivity = "INSERT INTO Activities (activityType, activityName, instructor, schedule, duration, capacity)
                       VALUES (?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sqlInsertActivity);
    $stmt->bind_param("ssssis", $activityType, $activityName, $instructor, $schedule, $duration, $capacity);

    if ($stmt->execute()) {
        echo "Activity added successfully!";
        // header("Location: classes.php");
    } else {
        echo "Error: " . $stmt->error;
    }
    $stmt->close();
}

// Handle editing/updating activity details
if (isset($_POST['updateActivitySubmit'])) {
    $activityIdToUpdate = $_POST['activityId'];
    $newDuration = $_POST['editDuration'];
    $newCapacity = $_POST['editCapacity'];

    $sqlUpdateActivity = "UPDATE Activities SET duration = ?, capacity = ? WHERE activityID = ?";
    $stmt = $conn->prepare($sqlUpdateActivity);
    $stmt->bind_param("iii", $newDuration, $newCapacity, $activityIdToUpdate);

    if ($stmt->execute()) {
        // echo "<script>alert('Activity updated successfully!'); window.location.href='adminManageActivities.php';</script>";
        echo "Activity added successfully!";
    } else {
       // echo "<script>alert('Error updating activity: " . $stmt->error . "');</script>";
    }
    $stmt->close();
}


// Delete activity
if (isset($_POST['deleteActivitySubmit'])) {
    $activityIdToDelete = $_POST['activityId'];   

    if (!empty($activityIdToDelete)) {
        // Check if there are any bookings for the activity before deleting
        $sqlCheckBookings = "SELECT COUNT(*) FROM ActivityBookings WHERE activityID = ?";
        $stmtCheckBookings = $conn->prepare($sqlCheckBookings);

        if($stmtCheckBookings) {
            $stmtCheckBookings->bind_param("i", $activityIdToDelete);
            $stmtCheckBookings->execute();
            $stmtCheckBookings->bind_result($bookingCount);
            $stmtCheckBookings->fetch();
            $stmtCheckBookings->close();

            if ($bookingCount > 0) {
                echo "<script>alert('Error: Cannot delete activity with existing bookings.');</script>";
                // echo "<script>window.location.href='adminManageActivities.php';</script>";
                // exit();
            }   else{
                $sqlDeleteActivity = "DELETE FROM Activities WHERE activityID = ?";
                $stmt = $conn->prepare($sqlDeleteActivity);

                if ($stmt) {
                    $stmt->bind_param("i", $activityIdToDelete);
                    if ($stmt->execute()) {
                        echo "Activity deleted successfully.";
                    } else {
                        echo "Error deleting activity.";
                    }
                    $stmt->close();
                } else {
                    echo "Error preparing delete statement." . $conn->error;
                }
            }
        }

    } else {
        //echo "No activity selected to delete.";
    }
}

// counts enrolled members in a class
$sqlSelectActivities = "
    SELECT
        a.*,
        COUNT(ab.bookingID) AS enrolledCount
    FROM Activities a
    LEFT JOIN ActivityBookings ab 
        ON a.activityID = ab.activityID 
        AND (ab.bookingStatus = 'booked' OR ab.bookingStatus = 'logged in' OR ab.bookingStatus = 'logged out')
    WHERE a.activityType = 'class' OR a.activityType = 'session'
    GROUP BY a.activityID
    ORDER BY a.activityID DESC
";

$result = $conn->query($sqlSelectActivities);
if (!$result) {
    echo "Error executing query: " . $conn->error;
    exit();
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Manage Classes & Sessions - Hustle Core</title>
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
        <a href="adminDashboard.php">Dashboard</a>
        <p class="currentPage"> <a href="adminManageActivities.php">Manage Classes & Sessions</a></p>
        <a href="adminMembers.php">Members</a>
        <a href="adminManageBookings.php">Bookings</a>
        <a href="adminReports.php">Reports</a>
        </div>

        <!-- Content Area -->
        <div class="content">
            <div class="breadcrumb">&gt; <a href="adminManageActivities.php">Manage Classes & Sessions</a></div>
            <h2>Classes and Sessions</h2>

            <!-- Add Activity Button -->
            <button id="addActivityBtn">Add Class / Session</button>

            <!-- Add Activity Modal -->
            <div id="addActivityModal" class="modal">
                <div class="modalContent">
                    <span id="addCloseBtn" class="closeBtn">&times;</span> <!-- Close button -->
                    <br>
                    <h3>Add Class / Session</h3>
                    <form action="adminManageActivities.php" method="POST">

                        <div class="modalLabelGrp">
                            <label for="activityType">Activity Type:</label>
                            <select name="activityType" required>
                                <option value="">-- Select Type --</option>
                                <option value="class">Class</option>
                                <option value="session">Session</option>
                            </select></div>

                        <div class="modalLabelGrp">
                            <label for="activityName">Activity Name:</label>
                            <select name="activityName" required>
                                <option value="">-- Select Activity --</option>
                                <option value="Yoga">Yoga</option>
                                <option value="HIIT">HIIT</option>
                                <option value="CrossFit">CrossFit</option>
                                <option value="Open Gym">Open Gym</option>
                            </select></div>

                        <div class="modalLabelGrp">
                            <label for="instructor">Instructor (optional for sessions):</label>
                            <input type="text" name="instructor"></div>

                        <div class="modalLabelGrp">
                            <label for="schedule">Schedule:</label>
                            <input type="datetime-local" name="schedule" required></div>

                        <div class="modalLabelGrp">
                            <label for="duration">Duration (minutes):</label>
                            <input type="number" name="duration" value="60" required></div>

                        <div class="modalLabelGrp">
                            <label for="capacity">Capacity:</label>
                            <input type="number" name="capacity" value="20"></div>

                        <button class="submitActBtn" type="submit" name="submitActivity">Add Activity</button>
                    </form>
                </div>
            </div>

            <h3>Scheduled Activities</h3>
            <?php
                // fetch activities and display
                $sqlSelectActivities = "
                SELECT
                    a.*,
                    COUNT(ab.bookingID) AS enrolledCount
                FROM Activities a
                LEFT JOIN ActivityBookings ab 
                    ON a.activityID = ab.activityID 
                    AND ab.bookingStatus = 'booked'
                GROUP BY a.activityID
                ORDER BY a.activityID DESC
            ";
            
            ?>

            <table border="1" class="scheduledActTable">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Type</th>
                        <th>Name</th>
                        <th>Instructor</th>
                        <th>Schedule</th>
                        <th>Duration</th>
                        <th>Capacity</th>
                        <th>Enrolled</th>
                        <th>Actions</th>
                    </tr>
                </thead>

                <tbody>
                    <?php
                    if ($result->num_rows > 0) {
                        while ($row = $result->fetch_assoc()) {
                            echo "<tr>";
                            echo "<td>" . $row['activityID'] . "</td>";
                            echo "<td>" . $row['activityType'] . "</td>";
                            echo "<td>" . $row['activityName'] . "</td>";
                            echo "<td>" . ($row['instructor'] ? $row['instructor'] : '-') . "</td>";
                            echo "<td>" . date("F j, Y, g:i a", strtotime($row['schedule'])) . "</td>";
                            echo "<td>" . $row['duration'] . " minutes</td>";
                            echo "<td>" . $row['capacity'] . "</td>";
                            echo "<td>" . $row['enrolledCount'] . "</td>";                       
                            echo "<td class='activityActions'>";
                            echo "<button class='editBtn'
                                data-activity-id='" . $row['activityID'] . "'
                                data-duration='" . $row['duration'] . "'
                                data-capacity='" . $row['capacity'] . "'>Edit</button>";
                            echo "<button class='deleteBtn'
                                data-activity-id='" . $row['activityID'] . "'>Delete</button>";

                            echo "<form method='get' action='adminAttendance.php' class='attendanceFrm'>";
                            echo "<input type='hidden' name='activityID' value='" . $row['activityID'] . "'>"; 
                            echo "<button type='submit' class='viewAttendanceBtn'>View Attendance</button>";
                            echo "</form>";

                            // echo "<a href='adminAttendance.php?activityID=" . $row['activityID'] . "' class='viewMembersBtn'>View Enrolled Members</a>";
                            echo "</td>";
                            echo "</tr>";
                        }
                    } else {
                        echo "<tr><td colspan='8' class='noRecordsResult'>No classes/sessions scheduled yet.</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
            
            <!-- edit activity modal -->
            <div id="editActivityModal" class="modal">
                <div class="modalEditContent">
                    <span id="editCloseBtn">&times;</span>
                    <h3>Edit Activity</h3>
                    <form method="post" action="adminManageActivities.php">
                        <input type="hidden" name="activityId" id="editActivityId">

                            <div class="modalLabelGrp">
                            <label for="editDuration">Duration (minutes):</label>
                            <input type="number" id="editDuration" name="editDuration" required></div>

                            <div class="modalLabelGrp">
                            <label for="editCapacity">Capacity:</label>
                            <input type="number" id="editCapacity" name="editCapacity"> </div>
                        <button type="submit" class="submitEditBtn" name="updateActivitySubmit">Update Activity</button>
                    </form>
                </div>
            </div>

            <!-- delete activity odal -->
            <div id="deleteActivityModal" class="modal">
                <div class="modalDeleteContent">
                <span class="closeBtn" id="deleteCloseBtn">&times;</span>
                    <h3>Confirm Delete</h3>
                    <p>Are you sure you want to delete this activity?</p>
                    <form method="post" action="adminManageActivities.php">
                        <input type="hidden" name="activityId" id="deleteActivityId">
                        <button type="submit" name="deleteActivitySubmit" class="submitDeleteBtn">Yes, Delete</button>
                    </form>   
                </div>
            </div>
        </div>
    </main>

    <script src="adminScript.js"></script>
</body>
</html>

<?php $conn->close(); ?>
