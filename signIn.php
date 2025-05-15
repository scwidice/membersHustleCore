<?php
session_start();

include 'createDatabase.php';
include 'dbTablesSetup.php';
include 'dbConnect.php';

// ADMIN RIGHTS
$adminName = "admin";   // PLEASE CHECK: ADMIN for name diplay only header
$adminPassword = "12345";
$adminEmail = "admin@hustle.core";
$logErr = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email']; 
    $password = $_POST['password']; 

    // Check for ADMIN login
    if ($email == $adminEmail && $password === $adminPassword) {
        $_SESSION['adminLoggedIn'] = true;
        $_SESSION['adminEmail'] = $email; 
        $_SESSION['adminName'] = 'Admin'; // PLEASE CHECK: ADMIN CREATE TABLE???

        setcookie("email", $email, time() + (3600 * 1000), "/");

        header("Location: adminDashboard.php");
        exit();
    } else {    //check for members log in
        $sql = "SELECT memberID, memberPassword FROM Members WHERE email = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 1) {
            $row = $result->fetch_assoc();
            if (password_verify($password, $row['memberPassword'])) {
                $_SESSION['memberLoggedIn'] = true;
                $_SESSION['memberID'] = $row['memberID'];
                $_SESSION['memberEmail'] = $email; // Optionally store member email

                setcookie("email", $email, time() + (3600 * 1000), "/");

                header("Location: memberDashboard.php");
                exit();
            } else {
                $logErr = "Invalid email or password. Please try again.";
            }
        } else {
            $logErr = "Invalid email or password. Please try again.";
        }

        $stmt->close();
        $conn->close();
    }
    if (isset($_SESSION['adminLoggedIn']) && $_SESSION['adminLoggedIn'] === true) {
        header("Location: adminDashboard.php");
        exit();
    }
    
    if (isset($_SESSION['memberLoggedIn']) && $_SESSION['memberLoggedIn'] === true) {
        header("Location: home.php");
        exit();
    }
}

?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
        <title>Sign In</title>

        <!-- 

        FOR CSS LINK
        <link rel="stylesheet" href=""/> 
        
        -->

        <style>
            * {
                margin: 0;
                padding: 0;
                box-sizing: border-box;
            }

            body, html {
                height: 100%;
                font-family: Arial, sans-serif;
                background-color: #ffffff;
                overflow: hidden;
            }

            .container {
                display: flex;
                height: 100vh;
                width: 100vw;
                min-height: 100vh;
            }

            .rightCont {
                width: 35%;
                background-color:  #11162c;
                padding: 40px;
                color: #fff;
                display: flex;
                flex-direction: column;
                justify-content: center; 
                align-items: center;
                overflow-y: auto;
            }

            .leftCont {
                width: 65%;
                background-image: url(images/signInHustleCore.png);
                background-size: cover;
                background-position: left center;
                background-repeat: no-repeat;
                flex-grow: 1;
            }

            form{
                display: flex;
                flex-direction: column;
                flex: 1;
                width: 100%; 
                max-width: 350px;
            }

            h2 {
                margin-bottom: 30px;
                font-size: 24px;
                text-align: center;
                width: 100%;
                max-width: 350px;
            }

            form label {
                font-weight: bold;
                display: block;
                margin: 10px 0 5px;
            }

            input[type="email"],
            input[type="password"] {
                width: 100%;
                padding: 14px;
                margin-bottom: 10px;
                background-color: #6C757D;
                border: none;
                border-radius: 5px;
                color: #fff;
            }

            input::placeholder {
                color: #fff;
                opacity: 0.5; 
            }


            .forgotPass {
                text-align: right;
                margin-bottom: 20px;
                font-size: 0.9em;
                width: 100%;
            }

            .forgotPass a {
                color: #fff;
                text-decoration: none;
            }

            .forgotPass a:hover {
                text-decoration: underline;
                color: #DC6C06;
            }

            button {
                width: 100%;
                padding: 12px;
                margin-top: 20px;
                background-color: #007BFF;
                border: none;
                font-weight: bold;
                cursor: pointer;
                border-radius: 5px;
                color: #fff;
                font-size: 16px;
            }

            button:hover {
                background-color: #DC6C06;
            }

            .signUpText {
                color: #fff;
                margin-top: 20px;
                font-size: 0.9em;
                text-align: center;
                width: 100%;
            }

            .signUpLink{
                color: #fff;
                text-decoration: none;
            }

            .signUpLink:hover {
                text-decoration: underline;
                color: #DC6C06;
            }

            /* Bigger Screen Sizes */
            @media (min-width: 1440px) {
                .rightCont {
                    padding: 80px 60px;
                }
                form {
                    max-width: 400px;
                }
            }

            /* Smaller screen sizes */
            @media (max-width: 768px) {
                .container {
                    flex-direction: column;
                }
                
                .leftCont {
                    display: none;
                }
                
                .rightCont {
                    width: 100%;
                    height: 100%;
                }
            }
        </style>
    </head>
    <body>
        <div class="container">
            <div class="leftCont">
        <!-- Add pakog content here bg nga pina landing -->
            </div>
            <div class="rightCont">
                <h2>Sign In</h2>

                <form method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" placeholder="Enter your email" required />

                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" placeholder="Enter your password" required />
                    
                    <div class="forgotPass">
                        <!-- ialert ra guro ni sya pang check ra to see if mugana
                        ang link but not necessarily needed tung actual
                        mahitabo if malimtan ang pass (masend sa email)-->
                    <a href="#">Forgot password?</a> 

                    </div>

                    <button type="submit">Submit</button>
                    
                    <p class="signUpText">
                        Don't have an account? <a class="signUpLink" href="SignUp.php">Sign up here.</a>
                    </p>
                </form>
            </div>
        </div>
    </body>
</html>