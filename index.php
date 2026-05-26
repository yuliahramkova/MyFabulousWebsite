<?php
// index.php - главная страница с проверкой авторизации

require_once 'auth_config.php';

$current_user = getCurrentUser();  
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Травки будущего - Главная</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<!-- верхний блок -->
<div class="user-bar">
    <?php if ($current_user): ?>
        <div class="user-info">
            <?php if (isAdmin()): ?>
                <span class="admin-badge">Админ</span>
            <?php endif; ?>
            <a href="auth.php?action=logout" class="logout-btn">Выйти</a>
        </div>
    <?php else: ?>
        <div class="user-info">
            <a href="login.php" class="login-link">Войти</a>
        </div>
    <?php endif; ?>
</div>

<h2>Меню</h2>
<nav>
    <ul>
        <li><a href="index.php">Главная</a></li>
        <li><a href="catalog.php">Каталог</a></li>
        <?php if (isAdmin()): ?>
            <li><a href="admin_logs.php">Логи</a></li>
        <?php endif; ?>
    </ul>
</nav>

<hr>

<header>
    <h1>Магазин "Травки будущего"</h1>
    <p>Добро пожаловать! У нас вы найдёте <i>качественные семена</i> для вашего урожая.</p>
    <p>Мы предлагаем проверенные сорта <b>овощей, зелени и плодовых деревьев</b> по низким ценам.
        Кроме того мы предлагаем вам скидку 50% на семена лука "слезы бывшего" по пятницам с 18:00 до 21:00
    </p>
</header>

<br>
<h2>График работы:</h2>
<p>Ежедневно 10:00-21:00</p>

<hr>

<img src="семена.jpg" alt="Магазин Травки будущего">

<footer>
    <small>&copy; 2026 Травки будущего. Все права защищены</small>
</footer>

</body>
</html>