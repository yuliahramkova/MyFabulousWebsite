<?php
$passwords = [
    'admin123' => 'admin123',
    'user123' => 'user123',
];

foreach ($passwords as $name => $password) {
    $hash = password_hash($password, PASSWORD_DEFAULT);
    echo "<p><strong>$name</strong><br>";
    echo "Пароль: $password<br>";
    echo "Хэш: <code>$hash</code></p>";
    echo "<hr>";
}
?>
