<div class="admin-container">
    <h2>مدیریت صفحات</h2>
    <div style="margin-bottom: 20px;">
        <a href="<?= BASE_URL ?>/admin" class="btn" style="background: var(--secondary-bg);">بازگشت به داشبورد</a>
    </div>

    <form action="<?= BASE_URL ?>/admin/page/add" method="POST" style="background: var(--secondary-bg); padding: 15px; border-radius: 8px; margin-bottom: 20px;">
        <input type="hidden" name="csrf_token" value="<?= Security::generateCSRFToken() ?>">
        <h3>افزودن صفحه جدید</h3>

        <div class="form-group" style="margin-top: 10px;">
            <label>عنوان صفحه:</label>
            <input type="text" name="title" class="form-control" required>
        </div>

        <div class="form-group">
            <label>اسلاگ (لاتین):</label>
            <input type="text" name="slug" class="form-control" required style="direction: ltr;">
        </div>

        <div class="form-group">
            <label>محتوای صفحه (پشتیبانی از HTML):</label>
            <textarea name="content" class="form-control" rows="6" required></textarea>
        </div>

        <button type="submit" class="btn btn-primary">افزودن</button>
    </form>

    <table style="width: 100%;">
        <thead>
            <tr>
                <th>ID</th>
                <th>عنوان</th>
                <th>اسلاگ</th>
                <th>وضعیت</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($pages as $p): ?>
                <tr>
                    <td><?= $p['id'] ?></td>
                    <td><?= Security::escape($p['title']) ?></td>
                    <td><?= Security::escape($p['slug']) ?></td>
                    <td><?= $p['status'] ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
