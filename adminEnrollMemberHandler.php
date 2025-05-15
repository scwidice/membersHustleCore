<?php
session_start();
include 'dbConnect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $activityID = intval($_POST['activityID']);
    $memberID = intval($_POST['memberID']);

    // Check if already enrolled
    $check = $conn->prepare("SELECT * FROM ActivityBookings WHERE memberID = ? AND activityID = ?");
    $check->bind_param("ii", $memberID, $activityID);
    $check->execute();
    $checkResult = $check->get_result();

    if ($checkResult->num_rows === 0) {
        // Cancel the original schedule
        $cancel = $conn->prepare("UPDATE ActivityBookings SET bookingStatus = 'cancelled' WHERE memberID = ? AND bookingStatus = 'booked'");
        $cancel->bind_param("i", $memberID);
        $cancel->execute();

        // Enroll in the new schedule
        $insert = $conn->prepare("INSERT INTO ActivityBookings (memberID, activityID, bookingStatus) VALUES (?, ?, 'booked')");
        $insert->bind_param("ii", $memberID, $activityID);
        $insert->execute();

        header("Location: adminAttendance.php?activityID=$activityID&enrollSuccess=1");
        exit();
    } else {
        header("Location: adminAttendance.php?activityID=$activityID&alreadyEnrolled=1");
        exit();
    }
} else {
    header("Location: adminAttendance.php");
    exit();
}
