<div class="admin-container">
    <h2>مدیریت دسته‌بندی‌ها</h2>
    <div style="margin-bottom: 20px;">
        <a href="<?= BASE_URL ?>/admin" class="btn" style="background: var(--secondary-bg);">بازگشت به داشبورد</a>
    </div>

    <form action="<?= BASE_URL ?>/admin/category/add" method="POST" style="background: var(--secondary-bg); padding: 15px; border-radius: 8px; margin-bottom: 20px;">
        <input type="hidden" name="csrf_token" value="<?= Security::generateCSRFToken() ?>">
        <h3>افزودن دسته‌بندی جدید</h3>
        <div style="display: flex; gap: 10px; align-items: center; margin-top: 10px;">
            <input type="text" name="name" class="form-control" placeholder="نام دسته‌بندی" required style="flex: 1;">
            <input type="text" name="slug" class="form-control" placeholder="اسلاگ (لاتین)" required style="flex: 1; direction: ltr;">
            <button type="submit" class="btn btn-primary">افزودن</button>
        </div>
    </form>

    <table style="width: 100%;">
        <thead>
            <tr>
                <th>ID</th>
                <th>نام</th>
                <th>اسلاگ</th>
                <th>تعداد ویدیو</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($categories as $cat): ?>
                <tr>
                    <td><?= $cat['id'] ?></td>
                    <td><?= Security::escape($cat['name']) ?></td>
                    <td><?= Security::escape($cat['slug']) ?></td>
                    <td><?= $cat['video_count'] ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
