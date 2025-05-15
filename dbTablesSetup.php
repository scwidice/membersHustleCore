<?php

include 'dbConnect.php';


if ($conn) {

    // Create Memberships table 
    $sqlCreateMembershipsTable = "CREATE TABLE IF NOT EXISTS Memberships (
        membershipID INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        membershipType ENUM('None', 'Monthly', 'Quarterly', 'Annual') NOT NULL UNIQUE
    )";
    if ($conn->query($sqlCreateMembershipsTable) === FALSE) {
        echo "<script>Error creating Memberships table: " . $conn->error . "\n</script>";
    }   else{
        echo "<script>Memberships table created successfully.</script>";
    }

    // Insert default types if table is empty
    $result = $conn->query("SELECT COUNT(*) AS total FROM Memberships");
    $row = $result->fetch_assoc();
    if ($row['total'] == 0) {
        $conn->query("INSERT INTO Memberships (membershipType) VALUES 
            ('None'), ('Monthly'), ('Quarterly'), ('Annual')");
    }
    // Create Members table with membershipID column and foreign key
    $sqlCreateMembersTable = "CREATE TABLE IF NOT EXISTS Members (
        memberID INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        firstName VARCHAR(50) NOT NULL,
        lastName VARCHAR(50) NOT NULL,
        email VARCHAR(100) NOT NULL UNIQUE,
        memberPassword VARCHAR(255) NOT NULL,
        registrationDate TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        membershipID INT UNSIGNED DEFAULT NULL,
        FOREIGN KEY (membershipID) REFERENCES Memberships(membershipID)
    )";
    if ($conn->query($sqlCreateMembersTable) === FALSE) {
        echo "<script>Error creating Members table: " . $conn->error . "\n</script>";
    } else{
        echo "<script>Members table created successfully.</script>";
    }

    // Create Activity table if it doesn't exist
    $sqlCreateActivitiesTable= "CREATE TABLE IF NOT EXISTS Activities (
        activityID INT AUTO_INCREMENT PRIMARY KEY,
        activityType ENUM('class', 'session') NOT NULL,
        activityName VARCHAR(100) NOT NULL,
        instructor VARCHAR(100),
        schedule DATETIME NOT NULL,
        duration INT UNSIGNED NOT NULL DEFAULT 60,
        capacity INT DEFAULT 20)";

    if ($conn->query($sqlCreateActivitiesTable) === FALSE) {
        echo "<script>Error creating Activities table: " . $conn->error . "\n</script>";
    } else{
        echo "<script>Activities table created successfully.</script>";
    }


    // Create activity bookings
    $sqlCreateActivityBookingTable = "CREATE TABLE IF NOT EXISTS ActivityBookings (
        bookingID INT AUTO_INCREMENT PRIMARY KEY,
        memberID INT UNSIGNED NOT NULL,
        activityID INT NOT NULL,
        bookingDate TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        bookingStatus ENUM('booked', 'cancelled', 'attended', 'logged in', 'logged out') DEFAULT 'booked',
        notes TEXT,
        createdAt TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updatedAt TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        FOREIGN KEY (memberID) REFERENCES Members(memberID),
        FOREIGN KEY (activityID) REFERENCES Activities(activityID),
        UNIQUE KEY unique_member_activity (memberID, activityID) )";

    if ($conn->query($sqlCreateActivityBookingTable) === FALSE) {
        echo "<script>Error creating ActivityBookings table: " . $conn->error . "\n</script>";
    } else{
        echo "<script>ActivityBookings table created successfully.</script>";
    }

    // attendace table
    $sqlCreateAttendaceTable = "CREATE TABLE IF NOT EXISTS Attendance (
        attendanceID INT AUTO_INCREMENT PRIMARY KEY,
        activityID INT NOT NULL,
        memberID INT UNSIGNED NOT NULL,
        status ENUM('logged in', 'logged out', 'cancelled') NOT NULL,
        timestamp DATETIME DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (activityID) REFERENCES Activities(activityID),
        FOREIGN KEY (memberID) REFERENCES Members(memberID))";

    if ($conn->query($sqlCreateAttendaceTable) === FALSE) {
        echo "<script>Error creating Attendance table: " . $conn->error . "\n</script>";
    } else{
        echo "<script>Attendance table created successfully.</script>";
    }
    
}
?>

