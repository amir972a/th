<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= isset($pageTitle) ? htmlspecialchars($pageTitle) . ' - ' : '' ?><?= APP_NAME ?></title>
    <link rel="stylesheet" href="<?= BASE_URL ?>/css/style.css">
</head>
<body>

<header class="site-header">
    <div class="top-bar">
        <div class="nav-container">
            <div class="welcome-msg">
                به <span class="text-primary">پرشین</span>‌وید خوش آمدید - بهترین مکان برای ویدیوهای شما
            </div>

            <div class="top-right-actions">
                <div class="search-bar">
                    <form action="<?= BASE_URL ?>/search" method="GET">
                        <input type="text" name="q" placeholder="جستجو..." required>
                        <button type="submit">
                            <svg viewBox="0 0 24 24" width="18" height="18" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
                        </button>
                    </form>
                </div>

                <div class="auth-links">
                    <?php if (Session::get('user_id')): ?>
                        <a href="<?= BASE_URL ?>/profile">👤 <?= Security::escape(Session::get('username')) ?></a>
                        <span class="separator">|</span>
                        <a href="<?= BASE_URL ?>/logout">🚪 خروج</a>
                    <?php else: ?>
                        <a href="<?= BASE_URL ?>/register">🔒 ثبت نام</a>
                        <span class="separator">|</span>
                        <a href="<?= BASE_URL ?>/login">👤 ورود</a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <div class="main-nav-bar">
        <div class="nav-container">
            <div class="logo">
                <a href="<?= BASE_URL ?>/"><span class="text-primary">PERSIAN</span> VID</a>
            </div>

            <div class="main-links">
                <ul>
                    <li class="active"><a href="<?= BASE_URL ?>/">خانه</a></li>
                    <li><a href="#">ویدیوها</a></li>
                    <li><a href="#">پلی‌لیست‌ها</a></li>
                    <li><a href="#">دسته‌بندی‌ها</a></li>
                    <?php if (Session::get('user_role') === 'admin' || Session::get('user_role') === 'moderator'): ?>
                        <li><a href="<?= BASE_URL ?>/admin">پنل مدیریت</a></li>
                    <?php endif; ?>
                    <li class="upload-btn"><a href="<?= BASE_URL ?>/upload">⬆ آپلود</a></li>
                </ul>
            </div>
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
