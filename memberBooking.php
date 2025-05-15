<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Enroll in a Class - Hustle Core</title>
    <style>
        body {
            font-family: sans-serif;
            margin: 20px;
            background-color: #f4f4f4;
            color: #333;
        }

        .container {
            max-width: 600px;
            margin: 0 auto;
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        h2 {
            color: #333;
            margin-bottom: 20px;
            text-align: center;
        }

        .form-group {
            margin-bottom: 15px;
        }

        label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }

        input[type="text"],
        input[type="email"],
        select {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
            box-sizing: border-box;
            margin-bottom: 10px;
        }

        button[type="submit"] {
            background-color: #007bff;
            color: white;
            border: none;
            padding: 10px 15px;
            border-radius: 4px;
            cursor: pointer;
            font-size: 1em;
        }

        button[type="submit"]:hover {
            opacity: 0.8;
        }

        .error-message {
            color: red;
            margin-top: 10px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Enroll in <span id="classNameDisplay"></span> Class</h2>
        <form action="process_enrollment.php" method="post">
            <input type="hidden" name="activity_id" id="activityIdInput">

            <div class="form-group">
                <label for="memberName">Your Name:</label>
                <input type="text" id="memberName" name="memberName" required>
            </div>

            <div class="form-group">
                <label for="memberEmail">Your Email:</label>
                <input type="email" id="memberEmail" name="memberEmail" required>
            </div>

            <div class="form-group">
                <label for="enrollmentDate">Enrollment Date:</label>
                <input type="text" id="enrollmentDate" name="enrollmentDate" value="<?php echo date('Y-m-d'); ?>" readonly>
            </div>

            <button type="submit" name="enroll_class">Confirm Enrollment</button>

            <?php if (isset($_GET['error'])): ?>
                <p class="error-message"><?php echo htmlspecialchars($_GET['error']); ?></p>
            <?php endif; ?>
        </form>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Get the activity ID and name from the URL query parameters (if present)
            const urlParams = new URLSearchParams(window.location.search);
            const activityId = urlParams.get('activity_id');
            const activityName = urlParams.get('activity_name');

            // Populate the hidden input and display the class name (if available)
            if (activityId) {
                document.getElementById('activityIdInput').value = activityId;
            }
            if (activityName) {
                document.getElementById('classNameDisplay').textContent = activityName;
            } else {
                document.getElementById('classNameDisplay').textContent = 'Selected'; // Default if name not passed
            }
        });
    </script>
</body>
</html>