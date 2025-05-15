<?php
include 'dbConnect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $bookingID = intval($_POST['bookingID']);
    $status = $_POST['status'];

    // get memberID and activityID from booking
    $query = $conn->prepare("SELECT memberID, activityID FROM ActivityBookings WHERE bookingID = ?");
    $query->bind_param("i", $bookingID);
    $query->execute();
    $result = $query->get_result();
    $row = $result->fetch_assoc();

    if ($row) {
        $currentBookingStatus = $row['bookingStatus'];
        $memberID = $row['memberID'];
        $activityID = $row['activityID'];

        // !!NOT WORKING YET: alert user that the status is already set
        if ($currentBookingStatus === $status) {
            echo "<script>alert('The booking status is already set to $status.');</script>";
            header("Location: adminAttendance.php?activityID=" . $activityID);
            exit();

        } else{
            //insert into attendance
            $insertAttendance = $conn->prepare("INSERT INTO Attendance (activityID, memberID, status) VALUES (?, ?, ?)");
            $insertAttendance->bind_param("iis", $activityID, $memberID, $status);
            $insertAttendance->execute();

            // Update the bookingStatus to match the selected status
            if ($status === 'bookingStatus') {
                echo "<script>alert('bookingstatus is already $status.');</script>";
            }
            $updateStatus = $conn->prepare("UPDATE ActivityBookings SET bookingStatus = ? WHERE bookingID = ?");
            $updateStatus->bind_param("si", $status, $bookingID);
            $updateStatus->execute();

            // If cancelled: update booking status and decrement enrolled count
            if ($status === 'cancelled') {
                $conn->query("UPDATE ActivityBookings SET bookingStatus = 'cancelled' WHERE bookingID = $bookingID");
            }
        }
    }
}

header("Location: adminAttendance.php?activityID=" . $activityID);
exit();
?>
