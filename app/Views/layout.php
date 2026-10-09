<?php
$headerConf = ViewHelper::getHeaderConfig();
$footerConf = ViewHelper::getFooterConfig();
?>
<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= isset($pageTitle) ? htmlspecialchars($pageTitle) . ' - ' : '' ?><?= APP_NAME ?></title>
    <link rel="stylesheet" href="<?= BASE_URL ?>/css/style.css?v=<?= filemtime(PUBLIC_PATH . '/css/style.css') ?>">
</head>
<body>

<header class="site-header">
    <div class="top-bar">
        <div class="nav-container">
            <div class="welcome-msg">
                <?= $headerConf['welcome_msg'] ?>
            </div>

            <div class="top-right-actions">
                <?php if (isset($headerConf['search_enabled']) && $headerConf['search_enabled']): ?>
                <div class="search-bar">
                    <form action="<?= BASE_URL ?>/search" method="GET">
                        <input type="text" name="q" placeholder="جستجو..." required>
                        <button type="submit">
                            <svg viewBox="0 0 24 24" width="18" height="18" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
                        </button>
                    </form>
                </div>
                <?php endif; ?>

                <?php if (isset($headerConf['auth_enabled']) && $headerConf['auth_enabled']): ?>
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
                <?php endif; ?>
            </div>
        </div>
    </div>

    <div class="main-nav-bar">
        <div class="nav-container">
            <div class="logo">
                <a href="<?= BASE_URL ?>/">
                    <?php if (!empty($headerConf['logo_url'])): ?>
                        <img src="<?= htmlspecialchars($headerConf['logo_url']) ?>" alt="Logo" style="max-height: 40px;">
                    <?php else: ?>
                        <span class="text-primary"><?= htmlspecialchars($headerConf['logo_text_1']) ?></span>
                        <div class="logo-vid-wrapper" style="margin-right: 5px; color: #fff;">
                            <?= htmlspecialchars($headerConf['logo_text_2']) ?>
                            <div class="logo-arc"></div>
                        </div>
                    <?php endif; ?>
                </a>
            </div>

            <div class="main-links">
                <ul>
                    <?php if (isset($headerConf['menu']) && is_array($headerConf['menu'])): ?>
                        <?php foreach ($headerConf['menu'] as $item): ?>
                            <?php if (isset($item['enabled']) && $item['enabled']): ?>
                                <li><a href="<?= htmlspecialchars($item['url']) ?>"><?= htmlspecialchars($item['label']) ?></a></li>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    <?php endif; ?>

                    <?php if (Session::get('user_role') === 'admin' || Session::get('user_role') === 'moderator'): ?>
                        <li><a href="<?= BASE_URL ?>/admin">پنل مدیریت</a></li>
                    <?php endif; ?>

                    <?php if (isset($headerConf['upload_enabled']) && $headerConf['upload_enabled']): ?>
                        <li class="upload-btn"><a href="<?= BASE_URL ?>/upload">⬆ آپلود</a></li>
                    <?php endif; ?>
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

<footer style="background: var(--secondary-bg); padding: 40px 0; border-top: 1px solid var(--border-color); margin-top: 50px;">
    <div class="container" style="display: flex; flex-wrap: wrap; gap: 30px; justify-content: space-between;">

        <div style="flex: 1; min-width: 250px;">
            <?php if (!empty($footerConf['logo_url'])): ?>
                <img src="<?= htmlspecialchars($footerConf['logo_url']) ?>" alt="Footer Logo" style="max-height: 40px; margin-bottom: 15px;">
            <?php else: ?>
                <h3 style="color: var(--primary-color); margin-bottom: 15px;"><?= APP_NAME ?></h3>
            <?php endif; ?>

            <p style="color: #aaa; line-height: 1.6; margin-bottom: 20px;">
                <?= Security::escape($footerConf['description'] ?? '') ?>
            </p>

            <?php if (!empty($footerConf['social_links'])): ?>
            <div style="display: flex; gap: 15px;">
                <?php foreach ($footerConf['social_links'] as $social): ?>
                    <?php if (isset($social['enabled']) && $social['enabled']): ?>
                        <a href="<?= htmlspecialchars($social['url']) ?>" target="_blank" style="color: var(--text-color); background: var(--bg-color); width: 36px; height: 36px; display: flex; align-items: center; justify-content: center; border-radius: 50%; text-decoration: none;">
                            <?= htmlspecialchars($social['icon']) ?>
                        </a>
                    <?php endif; ?>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>
        </div>

        <?php if (!empty($footerConf['columns'])): ?>
            <?php foreach ($footerConf['columns'] as $col): ?>
                <?php if (isset($col['enabled']) && $col['enabled']): ?>
                <div style="flex: 1; min-width: 150px;">
                    <h4 style="margin-bottom: 15px; border-bottom: 1px solid var(--border-color); padding-bottom: 10px; display: inline-block;"><?= Security::escape($col['title']) ?></h4>
                    <ul style="list-style: none;">
                        <?php if (!empty($col['links'])): ?>
                            <?php foreach ($col['links'] as $link): ?>
                                <?php if (isset($link['enabled']) && $link['enabled']): ?>
                                <li style="margin-bottom: 10px;">
                                    <a href="<?= htmlspecialchars($link['url']) ?>" style="color: #aaa; text-decoration: none; transition: color 0.2s;">
                                        <?= htmlspecialchars($link['label']) ?>
                                    </a>
                                </li>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </ul>
                </div>
                <?php endif; ?>
            <?php endforeach; ?>
        <?php endif; ?>

    </div>

    <div style="text-align: center; margin-top: 40px; padding-top: 20px; border-top: 1px solid var(--border-color); color: #888; font-size: 14px;">
        <?php
            $copy = $footerConf['copyright_text'] ?? '&copy; {year} ' . APP_NAME . '. تمامی حقوق محفوظ است.';
            echo str_replace('{year}', date('Y'), $copy);
        ?>
    </div>
</footer>

<script src="<?= BASE_URL ?>/js/main.js"></script>
</body>
</html>
