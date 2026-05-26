<?php
// admin_logs.php - страница просмотра логов (только для админа)

require_once 'auth_config.php';

$current_user = getCurrentUser();  

if (!isAdmin()) {
    redirect('index.php', 'Доступ запрещён. Требуются права администратора');
}

$logs = [];
if (file_exists($log_file)) {
    // Читаем все строки файла
    $logs = file($log_file, FILE_IGNORE_NEW_LINES);
    // Переворачиваем массив, чтобы последние записи были сверху
    $logs = array_reverse($logs);
}

$total = count($logs);
$success_count = 0;
$fail_count = 0;
$logout_count = 0;

foreach ($logs as $log) {
    if (strpos($log, 'SUCCESS_LOGIN') !== false) $success_count++;
    elseif (strpos($log, 'FAIL_LOGIN') !== false) $fail_count++;
    elseif (strpos($log, 'LOGOUT') !== false) $logout_count++;
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Панель администратора - Журнал авторизации</title>
    <link rel="stylesheet" href="style.css">
    <style>
        /* Дополнительные стили для страницы логов */
        .stats-box {
            background: #e8f4f8;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
            display: flex;
            gap: 20px;
            flex-wrap: wrap;
        }
        .stat-card {
            background: white;
            padding: 10px 20px;
            border-radius: 5px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        }
        .stat-card h4 {
            margin: 0 0 5px 0;
            color: #555;
        }
        .stat-card .number {
            font-size: 24px;
            font-weight: bold;
        }
        .logs-table {
            background: white;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        }
        .log-row {
            font-family: monospace;
            padding: 10px 15px;
            border-bottom: 1px solid #eee;
            font-size: 13px;
        }
        .log-row:hover {
            background: #f5f5f5;
        }
        .log-row.success {
            border-left: 4px solid #27ae60;
        }
        .log-row.fail {
            border-left: 4px solid #e74c3c;
        }
        .log-row.logout {
            border-left: 4px solid #f39c12;
        }
        .filter-buttons {
            margin-bottom: 20px;
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
        }
        .filter-btn {
            padding: 8px 16px;
            background: #6d96ad;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
        .filter-btn:hover {
            background: #44687c;
        }
        .filter-btn.active {
            background: #2c3e50;
        }
    </style>
</head>
<body>

<!-- Верхняя панель пользователя -->
<div class="user-bar">
    <div class="user-info">
        <span class="admin-badge">Админ</span>
        <a href="auth.php?action=logout" class="logout-btn">Выйти</a>
    </div>
</div>

<nav>
    <ul>
        <li><a href="index.php">Главная</a></li>
        <li><a href="catalog.php">Каталог</a></li>
        <li><a href="admin_logs.php">Логи</a></li>
    </ul>
</nav>

<hr>

<h1>Журнал событий авторизации</h1>

<!-- Статистика -->
<div class="stats-box">
    <div class="stat-card">
        <h4>Всего записей</h4>
        <div class="number"><?php echo $total; ?></div>
    </div>
    <div class="stat-card">
        <h4>Успешных входов</h4>
        <div class="number" style="color: #27ae60;"><?php echo $success_count; ?></div>
    </div>
    <div class="stat-card">
        <h4>Неудачных попыток</h4>
        <div class="number" style="color: #e74c3c;"><?php echo $fail_count; ?></div>
    </div>
    <div class="stat-card">
        <h4>Выходов</h4>
        <div class="number" style="color: #f39c12;"><?php echo $logout_count; ?></div>
    </div>
</div>

<!-- Фильтры -->
<div class="filter-buttons">
    <button class="filter-btn active" onclick="filterLogs('all')">Все</button>
    <button class="filter-btn" onclick="filterLogs('SUCCESS_LOGIN')">Успешные входы</button>
    <button class="filter-btn" onclick="filterLogs('FAIL_LOGIN')">Неудачные попытки</button>
    <button class="filter-btn" onclick="filterLogs('LOGOUT')">Выходы</button>
</div>

<!-- Таблица логов -->
<div class="logs-table" id="logs-container">
    <?php if (empty($logs)): ?>
        <div class="log-row">Логов пока нет.</div>
    <?php else: ?>
        <?php foreach ($logs as $log): 
            $log_class = '';
            if (strpos($log, 'SUCCESS_LOGIN') !== false) $log_class = 'success';
            elseif (strpos($log, 'FAIL_LOGIN') !== false) $log_class = 'fail';
            elseif (strpos($log, 'LOGOUT') !== false) $log_class = 'logout';
        ?>
            <div class="log-row <?php echo $log_class; ?>">
                <?php echo htmlspecialchars($log); ?>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>

<hr>

<footer>
    <small>&copy; 2026 Травки будущего. Все права защищены</small>
</footer>

<script>
function filterLogs(action) {
    document.querySelectorAll('.filter-btn').forEach(btn => {
        btn.classList.remove('active');
    });
    event.target.classList.add('active');
    
    // Фильтруем логи
    const logs = document.querySelectorAll('.log-row');
    logs.forEach(log => {
        if (action === 'all' || log.textContent.includes(action)) {
            log.style.display = 'block';
        } else {
            log.style.display = 'none';
        }
    });
}
</script>
<!-- strpos() - ищет позицию первого вхождения одной строки внутри другой. -->
</body>
</html>