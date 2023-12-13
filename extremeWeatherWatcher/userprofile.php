<?php
require_once("assets/initializer.php");
include("assets/header.php");

SSLtoHTTP();

if (!isset($_SESSION['userEmail'])) {
    header("Location: login.php");
    exit();
}

$errormsg = "";
//Get user information from database
$userQuery = "SELECT username, country, hashedPassword FROM users WHERE userEmail = ?";
$userStmt = mysqli_prepare($db, $userQuery);
mysqli_stmt_bind_param($userStmt, "s", $_SESSION['userEmail']);
mysqli_stmt_execute($userStmt);
$userResult = mysqli_stmt_get_result($userStmt);

if ($row = mysqli_fetch_assoc($userResult)) {
    $username = $row['username'];
    $country = $row['country'];
    $hashedPassword = $row['hashedPassword'];
} else {
    header("Location: login.php");
    exit();
}
mysqli_free_result($userResult);
mysqli_stmt_close($userStmt);


//If pressed on update profile, check for missing field. If there is no missing field, update database with new information
if (isset($_POST['submit']) && ($_POST['submit'] == "Update Profile")) {
    if (validateTextInput('username') && validateTextInput('country')) {
        $username = $_POST['username'];
        $country = $_POST['country'];

        $updateQuery = "UPDATE users SET username = ?, country = ? WHERE userEmail = ?";
        $stmt = mysqli_prepare($db, $updateQuery);
        mysqli_stmt_bind_param($stmt, "sss", $username, $country, $_SESSION['userEmail']);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
    } else {
        $errormsg = "Fields can't be empty";
    }
}

//If pressed on update password, check for old password, then check whether new pass matches with confirm pass. Then update database with new password
if (isset($_POST['submit']) && ($_POST['submit'] == "Update Password")) {
    if (validateTextInput('password') && validateTextInput('passwordConfirm') && validateTextInput('newpassword')) {

        if (sha1($_POST['password']) == $hashedPassword) {
            if ($_POST['passwordConfirm'] == $_POST['newpassword']) {
                echo "pass";
                $newPassword = sha1($_POST['newpassword']);
                $updateQuery = "UPDATE users SET hashedPassword = ? WHERE userEmail = ?";
                $stmt = mysqli_prepare($db, $updateQuery);
                mysqli_stmt_bind_param($stmt, "ss", $newPassword, $_SESSION['userEmail']);
                mysqli_stmt_execute($stmt);
                $errormsg = "";
                //heehehehehehehe
                echo '<script type="text/javascript">alert("Update password success");</script>';
                unset($_POST['newpassword']);
                unset($_POST['password']);
                unset($_POST['passwordConfirm']);
                mysqli_stmt_close($stmt);
            } else {
                $errormsg = "New password confirmation error";
            }
        } else {
            $errormsg = "Wrong password";
        }
    } else {
        $errormsg = "Fields can't be empty";
    }
}



mysqli_stmt_close($userStmt);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <title>User Profile</title>
</head>

<body>
    <h1>Edit Profile</h1>
    <?php if (!empty($errormsg)) : ?>
        <div class="errormsg" style="color: red;"><?php echo $errormsg; ?></div>
    <?php endif; ?>
    <form class="standalone-form" action="userprofile.php" method="POST">

        <table>
            <td>
                <h3>Change user info</h3>
                <?php

                makeTextEntry('text', 'username', "Username", 'username', true);
                makeCountryDropdown("Your home country", "", "country", true);
                ?>
                <br><br><br><br><br>
                <input type="submit" class="button" name="submit" value="Update Profile" />
            </td>

            <td>
                <h3>Change password</h3>
                <?php
                makeTextEntry('password', 'password', 'Old Password', 'password');
                makeTextEntry('password', 'password', 'New Password', 'newpassword');
                makeTextEntry('password', 'passwordConfirm', 'Confirm new password', 'passwordConfirm');

                ?>
                <br><br><br>
                <input type="submit" class="button" name="submit" value="Update Password" />
            </td>
        </table>
    </form>
</body>

</html>