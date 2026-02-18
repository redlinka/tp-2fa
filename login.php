<?php
session_start();
if (isset($_SESSION['user']['email']) && isset($_SESSION['user']['id'])) {
    if (isset($_SESSION['user']['tfa_secret'])) {
        header('Location: check-2fa.php');
    } else {
        header('Location: setup-2fa.php');
    }
    exit;
} else {
?>
<form method="post" action="check-login.php">
    <label>
        Email :
        <input type="text" name="email" placeholder="Email" value="user@test.fr">
    </label><br>
    <label>
        Password :
        <input type="password" name="password" placeholder="Password" value="Vivelephp!2026">
    </label><br>
    <input type="submit" value="Login">
</form>
<?php } ?>