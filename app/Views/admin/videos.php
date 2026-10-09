<div class="admin-container">
    <h2>مدیریت ویدیوها</h2>
    <a href="<?= BASE_URL ?>/admin" style="margin-bottom: 20px; display: inline-block;">&larr; بازگشت به پنل</a>

    <table style="width: 100%; border-collapse: collapse; margin-top: 20px; text-align: right;">
        <thead>
            <tr style="background: var(--secondary-bg); border-bottom: 2px solid var(--border-color);">
                <th style="padding: 10px;">آیدی</th>
                <th style="padding: 10px;">عنوان</th>
                <th style="padding: 10px;">کاربر</th>
                <th style="padding: 10px;">حریم خصوصی</th>
                <th style="padding: 10px;">وضعیت</th>
                <th style="padding: 10px;">عملیات</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($videos as $video): ?>
                <tr style="border-bottom: 1px solid var(--border-color);">
                    <td style="padding: 10px;"><?= $video['id'] ?></td>
                    <td style="padding: 10px;"><a href="<?= BASE_URL ?>/watch/<?= $video['id'] ?>"><?= Security::escape($video['title']) ?></a></td>
                    <td style="padding: 10px;"><?= Security::escape($video['username']) ?></td>
                    <td style="padding: 10px;"><?= $video['visibility'] == 'public' ? 'عمومی' : 'خصوصی' ?></td>
                    <td style="padding: 10px;"><?= $video['status'] ?></td>
                    <td style="padding: 10px;">
                        <form action="<?= BASE_URL ?>/admin/video/<?= $video['id'] ?>/moderate" method="POST" style="display:inline;">
                            <input type="hidden" name="csrf_token" value="<?= Security::generateCSRFToken() ?>">
                            <?php if ($video['status'] != 'published'): ?>
                                <button type="submit" name="action" value="approve" style="background: green; color: white; border: none; padding: 5px 10px; border-radius: 4px; cursor: pointer;">تایید</button>
                            <?php endif; ?>
                            <?php if ($video['status'] != 'removed'): ?>
                                <button type="submit" name="action" value="remove" style="background: red; color: white; border: none; padding: 5px 10px; border-radius: 4px; cursor: pointer;">حذف/رد</button>
                            <?php endif; ?>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
