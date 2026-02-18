<?php
$password = 'Vivelephp!2026';

#PASSWORD_DEFAULT uses the most secure hashing algo, right now it is bcrypt, it will also update the algo with time as better ones are found
$hashed = password_hash($password, PASSWORD_DEFAULT);

echo "password: $password\n";
echo "hashed password: $hashed\n";
