<?php
// catalog.php - страница каталога (доступна только авторизованным)

require_once 'auth_config.php';

if (!isAuthenticated()) {
    redirect('login.php', 'Пожалуйста, авторизуйтесь для просмотра каталога');
}

$current_user = getCurrentUser();
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Травки будущего - Каталог</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<div class="user-bar">
    <div class="user-info">
        <?php if (isAdmin()): ?>
            <span class="admin-badge">Админ</span>
        <?php endif; ?>
        <a href="auth.php?action=logout" class="logout-btn">Выйти</a>
    </div>
</div>

<nav>
    <ul>
        <li><a href="index.php">Главная</a></li>
        <li><a href="catalog.php">Каталог</a></li>
        <?php if (isAdmin()): ?>
            <li><a href="admin_logs.php">Просмотр логов</a></li>
        <?php endif; ?>
    </ul>
</nav>

<hr>

<h2>Каталог товаров</h2>

<div class="catalog-controls">
    <label for="category-filter">Фильтр по категории:</label>
    <select id="category-filter">
        <option value="all">Все товары</option>
        <option value="greens">Зелень</option>
        <option value="vegetables">Овощи</option>
        <option value="trees">Плодовые деревья</option>
    </select>
</div>

<!-- Товар 1: Укроп -->
<div class="product-card" data-category="greens">
    <a href="dill.html">
        <img src="укроп.png" alt="Укроп Гладиатор" width="200" height="150">
        <p>Укроп "Гладиатор"</p>
    </a>
    <p class="price" data-price="45">45 ₽</p>
    <button class="add-to-cart" 
            data-id="dill" 
            data-name="Укроп Гладиатор" 
            data-price="45"
            data-category="greens">
        В корзину
    </button>
</div>

<!-- Товар 2: Томат -->
<div class="product-card" data-category="vegetables">
    <a href="tomat.html">
        <img src="томат.png" alt="Томат Бычье сердце" width="200" height="150">
        <p>Томат "Бычье сердце"</p>
    </a>
    <p class="price" data-price="80">80 ₽</p>
    <button class="add-to-cart" 
            data-id="tomat" 
            data-name="Томат Бычье сердце" 
            data-price="80"
            data-category="vegetables">
        В корзину
    </button>
</div>

<!-- Товар 3: Яблоня -->
<div class="product-card" data-category="trees">
    <a href="apple.html">
        <img src="яблоня.png" alt="Яблоня Слава победителям" width="200" height="150">
        <p>Яблоня "Слава победителям"</p>
    </a>
    <p class="price" data-price="350">350 ₽</p>
    <button class="add-to-cart" 
            data-id="apple" 
            data-name="Яблоня Слава победителям" 
            data-price="350"
            data-category="trees">
        В корзину
    </button>
</div>

<!-- Блок корзины -->
<aside class="cart-section">
    <h3>Ваша корзина</h3>
    <ul id="cart-items" class="cart-list"></ul>
    <p><strong>Итого: <span id="cart-total">0</span> ₽</strong></p>
    <div class="cart-buttons">
        <button id="clear-cart" class="btn-secondary">Очистить</button>
        <button id="pay-btn" class="btn-primary">Оплатить</button>
    </div>
</aside>

<hr>

<footer>
    <small>&copy; 2026 Травки будущего. Все права защищены</small>
</footer>

<script src="script.js"></script>
</body>
</html>