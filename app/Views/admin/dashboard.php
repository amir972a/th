<div class="admin-container">
    <h2>پنل مدیریت</h2>

    <div style="margin: 20px 0; display: flex; gap: 15px;">
        <a href="<?= BASE_URL ?>/admin/users" class="btn">مدیریت کاربران</a>
        <a href="<?= BASE_URL ?>/admin/videos" class="btn">مدیریت ویدیوها</a>
        <a href="<?= BASE_URL ?>/admin/tags" class="btn">مدیریت تگ‌ها</a>
    </div>

    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 20px; margin-top: 30px;">
        <div style="background: var(--secondary-bg); padding: 20px; border-radius: 8px; text-align: center;">
            <h3>کل کاربران</h3>
            <p style="font-size: 24px; font-weight: bold; color: var(--primary-color); margin-top: 10px;"><?= $stats['users'] ?></p>
        </div>
        <div style="background: var(--secondary-bg); padding: 20px; border-radius: 8px; text-align: center;">
            <h3>کل ویدیوها</h3>
            <p style="font-size: 24px; font-weight: bold; color: var(--primary-color); margin-top: 10px;"><?= $stats['videos'] ?></p>
        </div>
        <div style="background: var(--secondary-bg); padding: 20px; border-radius: 8px; text-align: center;">
            <h3>ویدیوهای در حال بررسی</h3>
            <p style="font-size: 24px; font-weight: bold; color: var(--primary-color); margin-top: 10px;"><?= $stats['pending_videos'] ?></p>
        </div>
        <div style="background: var(--secondary-bg); padding: 20px; border-radius: 8px; text-align: center;">
            <h3>نظرات ثبت شده</h3>
            <p style="font-size: 24px; font-weight: bold; color: var(--primary-color); margin-top: 10px;"><?= $stats['comments'] ?></p>
        </div>
    </div>
</div>
