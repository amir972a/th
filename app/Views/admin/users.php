<div class="admin-container">
    <h2>مدیریت کاربران</h2>
    <a href="<?= BASE_URL ?>/admin" style="margin-bottom: 20px; display: inline-block;">&larr; بازگشت به پنل</a>

    <table style="width: 100%; border-collapse: collapse; margin-top: 20px; text-align: right;">
        <thead>
            <tr style="background: var(--secondary-bg); border-bottom: 2px solid var(--border-color);">
                <th style="padding: 10px;">آیدی</th>
                <th style="padding: 10px;">نام کاربری</th>
                <th style="padding: 10px;">ایمیل</th>
                <th style="padding: 10px;">نقش</th>
                <th style="padding: 10px;">وضعیت</th>
                <th style="padding: 10px;">عملیات</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($users as $user): ?>
                <tr style="border-bottom: 1px solid var(--border-color);">
                    <td style="padding: 10px;"><?= $user['id'] ?></td>
                    <td style="padding: 10px;"><?= Security::escape($user['username']) ?></td>
                    <td style="padding: 10px;"><?= Security::escape($user['email']) ?></td>
                    <td style="padding: 10px;"><?= $user['role'] ?></td>
                    <td style="padding: 10px;"><?= $user['status'] ?></td>
                    <td style="padding: 10px;">
                        <?php if ($user['id'] != Session::get('user_id')): ?>
                            <form action="<?= BASE_URL ?>/admin/user/<?= $user['id'] ?>/status" method="POST" style="display:inline;">
                                <input type="hidden" name="csrf_token" value="<?= Security::generateCSRFToken() ?>">
                                <select name="status" onchange="this.form.submit()" style="background: var(--bg-color); color: var(--text-color); border: 1px solid var(--border-color); padding: 5px; border-radius: 4px;">
                                    <option value="active" <?= $user['status'] == 'active' ? 'selected' : '' ?>>فعال</option>
                                    <option value="suspended" <?= $user['status'] == 'suspended' ? 'selected' : '' ?>>معلق</option>
                                    <option value="banned" <?= $user['status'] == 'banned' ? 'selected' : '' ?>>مسدود</option>
                                </select>
                            </form>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
