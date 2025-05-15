<?php
session_start();

include 'dbConnect.php';

// insert member
if (isset($_POST['submitMember'])) {
    $email = $_POST['email'];
    $firstName = $_POST['firstName'];
    $lastName = $_POST['lastName'];
    $plainPassword = $_POST['password'];
    $confirmPassword = $_POST['confirmPassword'];

    // password matching check
    if ($plainPassword !== $confirmPassword){
        echo "Passwords do not match.<br>";
        exit();
    }

    // Hash the password securely
    $hashedPassword = password_hash($plainPassword, PASSWORD_DEFAULT);

    // insert member
    $sqlInsertMember = "INSERT INTO Members (email, firstName, lastName, memberPassword)
                        VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($sqlInsertMember);
    $stmt->bind_param("ssss", $email,$firstName, $lastName, $hashedPassword);

    if ($stmt->execute()) {
        $_SESSION['memberID'] = $conn->insert_id;
        echo "Sign up successful.<br>";
         // header("Location: subscribe.php"); // will add the page later
    } else {
        echo "Error signing up: " . $stmt->error . "<br>";
    }
    $stmt->close();
}

$conn->close();
?>



<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
        <title>Sign Up - Husle Core</title>

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
                overflow-x: hidden;
            }

            .container {
                display: flex;
                height: 100%;
                width: 100%;
                min-height: 100vh;
            }

            .rightCont {
                width: 35%;
                background-color: #11162c;
                padding: 40px;
                color: #fff;
                display: flex;
                flex-direction: column;
                align-items: center;
                overflow-y: auto;
                min-height: 100vh;
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
                margin-bottom: 20px; 
                width: 100%; 
                max-width: 350px;
            }

            h2 {
                margin-top: 40px;
                margin-bottom: 30px;
                font-size: 24px;
                text-align: center;
                max-width: 350px;
            }

            form label {
                font-weight: bold;
                display: block;
                margin: 10px 0 5px;
            }

            input[type="email"],
            input[type="password"],
            input[type="text"] {
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

            button {
                width: 100%;
                margin-top: 40px;
                margin-bottom: 20px;
                padding: 10px;
                background-color: #007BFF;
                border: none;
                font-weight: bold;
                cursor: pointer;
                border-radius: 5px;
                color: #fff;
            }

            button:hover {
                background-color: #DC6C06;
            }

            .signUpText {
                color: #fff;
                margin-top: 20px;
                margin-bottom: 20px;
                font-size: 0.9em;
                text-align: center;
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
                    padding: 30px 20px;
                }
                
                form {
                    margin-bottom: 40px;
                }
                
                button {
                    margin-bottom: 20px;
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
                <h2>Sign Up</h2>

                <form action="signUp.php" method ="POST">
                    <label for="email">Email</label>
                    <input type="email" name="email" id="email" placeholder="Enter your email" required />

                    <label for="firstName">First Name</label>
                    <input type="text" name="firstName" id="firstName" placeholder="Enter your first name" required />

                    <label for="lastName">Last Name</label>
                    <input type="text" name="lastName" id="lastName" placeholder="Enter your last name" required />

                    <label for="password">Password</label>
                    <input type="password" name="password" id="password" placeholder="Enter your password" required />
                    
                    <label for="confirmPassword">Confirm Password</label>
                    <input type="password" name="confirmPassword" id="confirmPassword" placeholder="Re-enter your password" required />

                    <button type="submit" name="submitMember">Submit</button>
                    
                    <p class="signUpText">
                    Have an account? <a class="signUpLink" href="SignIn.php">Sign in here.</a>
                    </p>
                </form>
            </div>
        </div>
    </body>
</html>
