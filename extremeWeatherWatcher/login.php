<!DOCTYPE html>
<?php

require_once("assets/initializer.php");
include("assets/header.php");


require_SSL();

$errormsg = "";

// Check if login form is submitted
if (!empty($_POST['submit'])) {
    // Validate user input
    if (validateTextInput('userEmail') && validateTextInput('password')) {
        $inputEmail = $_POST['userEmail'];
        $hash_pass = sha1($_POST['password']);

        // SQL prep stmt
        $query_accounts = "SELECT hashedPassword FROM `users` WHERE userEmail = ?";
        $stmt_accounts = mysqli_prepare($db, $query_accounts);
        mysqli_stmt_bind_param($stmt_accounts, "s", $inputEmail);
        mysqli_stmt_execute($stmt_accounts);
        $result = mysqli_stmt_get_result($stmt_accounts);

        // Check email
        if (mysqli_num_rows($result) > 0) {
            $row = mysqli_fetch_assoc($result);
            if (!empty($row)) {
                // Check pass
                if ($hash_pass == $row['hashedPassword']) {
                    $_SESSION['userEmail'] = $inputEmail;
                    header("Location: index.php");
                }
                // Close statement and free result
                mysqli_stmt_close($stmt_accounts);
                mysqli_free_result($result);
            }
        } else {
            // Wrong pass or email
            $errormsg = "Incorrect email or password";
        }
    } else {
        // Missing field error
        $errormsg = "Email and password fields can't be empty";
    }
}
?>

<html lang="en">

<head>
    <title>Log in</title>
</head>

<body>
    <h1> Log in</h1>

    <form class="standalone-form-1-col" action="login.php" method="POST">
        <table>
            <td>
                <?php if (!empty($errormsg))
                    echo "<div class=\"errormsg\"style=\"color: red;\"> $errormsg</div>"
                ?>
                <!-- Didn't use makeText function here because I don't want the email and pass saved on failed entry -->
                <label for="email">Email: </label>
                <input type="text" id="userEmail" name="userEmail" />

                <label for="password">Password: </label>
                <input type="password" id="password" name="password" /><br><br>

                <br><input type="submit" class="button" name="submit" /><br><br>
                <a href="register.php">
                    <div>Register here</div>
                </a>
            </td>
        </table>
    </form>

</body>

</html>