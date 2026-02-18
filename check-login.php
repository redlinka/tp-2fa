<?php
session_start();

#DECLARING THE VARIABLES
$client_ip = $_SERVER['REMOTE_ADDR'];
$is_local = ($client_ip === '127.0.0.1' || $client_ip === '::1');
$is_https = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off');
$email = isset($_POST['email']) ? trim((string) $_POST['email']) : '';
$password = isset($_POST['password']) ? trim((string) $_POST['password']) : '';
$db_file = __DIR__ . '/tp-2fa.db';

#PRELIMINARY TESTS
if (!$is_https && !$is_local) {
    $https_url = 'https://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
    header('Location: ' . $https_url);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    die('Invalid request method. Must be POST.');
}

if ($email === '' || $password === '') {
    die('Email and password cannot be empty.');
}

if (!file_exists($db_file)) {
    die('Database not found!');
}

try {
    $db = new PDO('sqlite:' . $db_file);
} catch (PDOException $e) {
    die('Connection failed: ' . $e->getMessage());
}

#CHECKING USER AUTHENTICITY
$stmt = $db->prepare('SELECT id, password FROM users WHERE email = :email');
$stmt->execute([':email' => $email]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    die('Invalid credentials.');
}

if (password_verify($password, $user['password'])) {

    session_regenerate_id(true);
    if(isset($_SESSION['user']['tfa_secret'])) {
        header('Location: setup-2fa.php');
        exit;
    } else {
        $_SESSION['user'] = [
            'id' => (int)$user['id'],
            'email' => (string)$user['email'],
            'tfa_secret' => (string)($user['tfa_secret'] ?? '')
        ];
    }
    unset($_SESSION['tfa_secret_temp']);
    header('Location: index.html');
    exit;

} else {
    die('Invalid credentials.');
}
