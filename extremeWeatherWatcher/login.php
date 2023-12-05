<!DOCTYPE html>
<?php
require_once("assets/initializer.php");
include("assets/header.php");

require_SSL();

$errormsg = "";

if (!empty($_POST['submit'])) {
    
    if (validateTextInput('userEmail') && validateTextInput('password')) {
        if (str_contains($_POST['userEmail'], "@") && str_contains($_POST['userEmail'], ".")) {
            $inputEmail = $_POST['userEmail'];
            $hash_pass = sha1($_POST['password']);
            $query_accounts = "SELECT hashedPassword FROM `users` WHERE email = ?";
        
            $stmt_accounts = mysqli_prepare($db, $query_accounts);
            mysqli_stmt_bind_param($stmt_accounts, "s", $inputEmail);
            mysqli_stmt_execute($stmt_accounts);
            $result = mysqli_stmt_get_result($stmt_accounts);
        
            if ($result) {
                $row = mysqli_fetch_assoc($result);
                if(!empty($row)){
                    if ($hash_pass == $row['hashedPassword']) {
                        //set session (log in) and redirect
                        $_SESSION['userEmail'] = $inputEmail;
                        header("Location: index.php");
                    }
                    
                }
                
            }
          
        }
        else {
            $errormsg = "Incorrect email or password.";
        } 
        
    }
    else{
        $errormsg = "Email and password fields can't be empty";
    }
}

?>

<html lang="en">
    <head>
        <title>Log in</title>
    </head>
    <body>
    <?php if (!empty($errormsg))
       echo "<div class=\"errormsg\"style=\"color: red;\"> $errormsg</div>"
    ?>
        <form action="login.php" method="POST">
            <label for="email">Email: </label>
            <input type="text" id="email" name="email" />

            <label for="password">Password: </label>
            <input type="password" id="password" name="password" />

            <input type="submit" name="submit"/>
        </form>
        <a href="register.php"><div>Register here</div></a>
    </body>
</html>