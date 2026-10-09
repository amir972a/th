<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= isset($pageTitle) ? htmlspecialchars($pageTitle) . ' - ' : '' ?><?= APP_NAME ?></title>
    <link rel="stylesheet" href="<?= BASE_URL ?>/css/style.css">
</head>
<body>

<header>
    <div class="nav-container">
        <div class="logo">
            <a href="<?= BASE_URL ?>/"><?= APP_NAME ?></a>
        </div>

        <div class="search-bar">
            <form action="<?= BASE_URL ?>/search" method="GET">
                <input type="text" name="q" placeholder="جستجو..." required>
                <button type="submit">🔍</button>
            </form>
        </div>

        <div class="nav-links">
            <ul>
                <?php if (Session::get('user_id')): ?>
                    <li><a href="<?= BASE_URL ?>/upload">آپلود ویدیو</a></li>
                    <li><a href="<?= BASE_URL ?>/profile">پروفایل</a></li>
                    <?php if (Session::get('user_role') === 'admin' || Session::get('user_role') === 'moderator'): ?>
                        <li><a href="<?= BASE_URL ?>/admin">پنل مدیریت</a></li>
                    <?php endif; ?>
                    <li><a href="<?= BASE_URL ?>/logout">خروج</a></li>
                <?php else: ?>
                    <li><a href="<?= BASE_URL ?>/login">ورود</a></li>
                    <li><a href="<?= BASE_URL ?>/register">ثبت نام</a></li>
                <?php endif; ?>
            </ul>
        </div>
    </div>
</header>

<main>
    <?php
    $errors = Session::getFlash('error');
    if ($errors) {
        foreach ($errors as $error) {
            echo '<div class="alert alert-error">' . Security::escape($error) . '</div>';
        }
    }

    $successes = Session::getFlash('success');
    if ($successes) {
        foreach ($successes as $success) {
            echo '<div class="alert alert-success">' . Security::escape($success) . '</div>';
        }
    }
    ?>

    <?php require_once $contentView; ?>
</main>

<footer>
    <p>&copy; <?= date('Y') ?> <?= APP_NAME ?>. تمامی حقوق محفوظ است.</p>
</footer>

<script src="<?= BASE_URL ?>/js/main.js"></script>
</body>
</html>
