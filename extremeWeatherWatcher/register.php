<!DOCTYPE html>
<?php
require_once("assets/initializer.php");
include("assets/header.php");

require_SSL();

//if already logged in, redirect
if (isset($_SESSION['userEmail'])) {
    header("Location: index.php");
}

$allInputValid = false;
$errormsg = "";

if (validateTextInput('username') && validateTextInput('userEmail') && validateTextInput('country') && validateTextInput('password') && validateTextInput('passwordConfirm')) {
    if (str_contains($_POST['userEmail'], "@") && str_contains($_POST['userEmail'], ".")) {
        if ($_POST['password'] == $_POST['passwordConfirm']) {
            $allInputValid = true;
        }
        else {
            $errormsg = "Passwords do not match.";
        }
    }
    else {
        $errormsg = "Email format requires: @ and a .domain";
    }
}
else {
    $errormsg =  "Please fill in all fields.";
}
if (!empty($errormsg)){
    echo "<div class=\"errormsg\"style=\"color: red;\"> $errormsg</div>";
}

if (!empty($_POST["submit"])) {
    if ($allInputValid) {

        //check if the entered email is already registered
        $query_emails = "SELECT count(*) as count FROM users WHERE userEmail=?";
        $stmt_emails = mysqli_prepare($db, $query_emails);
        mysqli_stmt_bind_param($stmt_emails, "s", $_POST['userEmail']);
        mysqli_stmt_execute($stmt_emails);
        $emails_result = mysqli_stmt_get_result($stmt_emails);

        if ($emails_result) {
            $row = mysqli_fetch_assoc($emails_result);
            if ($row['count'] > 0) {
                echo "An account already exists for this email.";
            }
            else {
                $postedEmail = $_POST['userEmail'];
                $username = $_POST['username'];
                $hashPassword = sha1($_POST['password']);
                $userCountry = ucfirst(strtolower($_POST['country']));
    
                //save user data into the db
                $insert_query = "INSERT INTO users (userEmail, username, hashedPassword, country) VALUES (?,?,?,?)";
                $insert_stmt = mysqli_prepare($db, $insert_query);
                mysqli_stmt_bind_param($insert_stmt, "ssss", $postedEmail, $username, $hashPassword, $userCountry);
                $res = mysqli_stmt_execute($insert_stmt);
    
                //if save successful, set session and redirect
                if ($res) {
                    $_SESSION['userEmail'] = $postedEmail;
                    header("Location: index.php");
                    exit;
                }
                else {
                    echo "unable to add user";
                }
            }
        }
          
    }
}

?>

<html lang="en">
<head>
    <title>Register account</title>
</head>
<body>
    <form action="register.php" method="POST">
        
        <?php 
            makeTextEntry('text', 'username', 'Username', 'username');
            makeTextEntry('text', 'email', 'Email', 'userEmail');
            makeCountryDropdown("Your home country","filterCountry","country");
            makeTextEntry('password', 'password', 'Password', 'password');
            makeTextEntry('password', 'passwordConfirm', 'Confirm password', 'passwordConfirm'); 
        ?>
        <input type="submit" name="submit"/>

    </form>
    
</body>
</html>