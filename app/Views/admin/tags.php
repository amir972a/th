<div class="admin-container">
    <h2>مدیریت تگ‌ها</h2>

    <div style="margin-bottom: 20px;">
        <a href="<?= BASE_URL ?>/admin" class="btn" style="background: var(--secondary-bg);">بازگشت به داشبورد</a>
    </div>

    <table style="width: 100%; border-collapse: collapse; margin-top: 20px;">
        <thead>
            <tr style="background: var(--secondary-bg); text-align: right;">
                <th style="padding: 10px;">ID</th>
                <th style="padding: 10px;">نام تگ</th>
                <th style="padding: 10px;">اسلاگ</th>
                <th style="padding: 10px;">تعداد ویدیوها</th>
                <th style="padding: 10px;">عملیات</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($tags as $tag): ?>
            <tr style="border-bottom: 1px solid var(--border-color);">
                <td style="padding: 10px;"><?= $tag['id'] ?></td>
                <td style="padding: 10px;"><?= Security::escape($tag['name']) ?></td>
                <td style="padding: 10px;"><?= Security::escape($tag['slug']) ?></td>
                <td style="padding: 10px;"><?= $tag['video_count'] ?></td>
                <td style="padding: 10px;">
                    <!-- Rename Form -->
                    <form action="<?= BASE_URL ?>/admin/tag/<?= $tag['id'] ?>/moderate" method="POST" style="display:inline-block; margin-left: 10px;">
                        <input type="hidden" name="csrf_token" value="<?= Security::generateCSRFToken() ?>">
                        <input type="hidden" name="action" value="rename">
                        <input type="text" name="new_name" placeholder="نام جدید" required style="padding:3px; font-size:12px; width:100px;">
                        <button type="submit" class="btn" style="padding: 3px 8px; font-size: 12px; background: var(--primary-color);">ویرایش</button>
                    </form>

                    <!-- Merge Form -->
                    <form action="<?= BASE_URL ?>/admin/tag/<?= $tag['id'] ?>/moderate" method="POST" style="display:inline-block; margin-left: 10px;">
                        <input type="hidden" name="csrf_token" value="<?= Security::generateCSRFToken() ?>">
                        <input type="hidden" name="action" value="merge">
                        <input type="number" name="target_id" placeholder="ID تگ مقصد" required style="padding:3px; font-size:12px; width:80px;">
                        <button type="submit" class="btn" style="padding: 3px 8px; font-size: 12px; background: #9c27b0;" onclick="return confirm('آیا مطمئن هستید؟ همه ویدیوهای این تگ به تگ مقصد منتقل شده و این تگ حذف می‌شود.');">ادغام</button>
                    </form>

                    <!-- Delete Form -->
                    <form action="<?= BASE_URL ?>/admin/tag/<?= $tag['id'] ?>/moderate" method="POST" style="display:inline-block;">
                        <input type="hidden" name="csrf_token" value="<?= Security::generateCSRFToken() ?>">
                        <button type="submit" name="action" value="delete" class="btn" style="padding: 3px 8px; font-size: 12px; background: red;" onclick="return confirm('آیا از حذف این تگ مطمئن هستید؟');">حذف</button>
                    </form>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>