<div class="container">
    <div style="display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid var(--border-color); margin-bottom: 20px; padding-bottom: 10px;">
        <h2>ویدیوهای مرتبط با تگ: <?= htmlspecialchars($tag['name']) ?></h2>

        <div>
            <form action="<?= BASE_URL ?>/tag/<?= htmlspecialchars($tag['slug']) ?>" method="GET" style="display: inline-block;">
                <select name="sort" onchange="this.form.submit()" style="padding: 5px; background: var(--secondary-bg); color: var(--text-color); border: 1px solid var(--border-color); border-radius: 4px;">
                    <option value="newest" <?= $sort === 'newest' ? 'selected' : '' ?>>جدیدترین</option>
                    <option value="popular" <?= $sort === 'popular' ? 'selected' : '' ?>>پربازدیدترین</option>
                </select>
            </form>
        </div>
    </div>

    <div style="display: flex; gap: 20px; flex-wrap: wrap;">

        <!-- Main Videos Area -->
        <div style="flex: 3; min-width: 300px;">
            <div class="video-grid">
                <?php if (empty($videos)): ?>
                    <p>هیچ ویدیویی برای این تگ یافت نشد یا شما اجازه مشاهده آن‌ها را ندارید.</p>
                <?php else: ?>
                    <?php foreach ($videos as $video): ?>
                        <div class="video-card">
                            <a href="<?= BASE_URL ?>/watch/<?= $video['id'] ?>">
                                <img src="<?= BASE_URL ?>/thumbnail/<?= $video['thumbnail_path'] ?? 'default' ?>" class="video-thumb" alt="<?= Security::escape($video['title']) ?>">
                            </a>
                            <div class="video-info">
                                <a href="<?= BASE_URL ?>/watch/<?= $video['id'] ?>" class="video-title">
                                    <?= Security::escape($video['title']) ?>
                                    <?php if($video['visibility'] == 'private') echo ' 🔒'; ?>
                                </a>
                                <div class="video-meta">
                                    <?= number_format($video['views']) ?> بازدید • <?= date('Y/m/d', strtotime($video['created_at'])) ?>
                                </div>
                                <div class="video-meta" style="margin-top: 5px;">
                                    توسط: <a href="<?= BASE_URL ?>/profile/<?= Security::escape($video['username']) ?>"><?= Security::escape($video['display_name']) ?></a>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>

            <?php if ($totalPages > 1): ?>
                <div class="pagination" style="margin-top: 20px; display: flex; gap: 5px;">
                    <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                        <a href="?page=<?= $i ?>&sort=<?= Security::escape($sort) ?>" class="btn" style="<?= $i === $page ? 'background: var(--primary-color);' : 'background: var(--secondary-bg);' ?>"><?= $i ?></a>
                    <?php endfor; ?>
                </div>
            <?php endif; ?>
        </div>

        <!-- Sidebar -->
        <div style="flex: 1; min-width: 250px; max-width: 300px;">
            <div style="background: var(--secondary-bg); padding: 15px; border-radius: 8px;">
                <h3 style="margin-bottom: 15px; border-bottom: 1px solid var(--border-color); padding-bottom: 10px;">تگ‌های محبوب</h3>
                <div style="display: flex; flex-wrap: wrap; gap: 8px;">
                    <?php foreach ($popularTags as $ptag): ?>
                        <a href="<?= BASE_URL ?>/tag/<?= htmlspecialchars($ptag['slug']) ?>" style="background: var(--bg-color); border: 1px solid var(--border-color); padding: 5px 10px; border-radius: 12px; font-size: 14px; text-decoration: none; color: var(--text-color);">
                            <?= htmlspecialchars($ptag['name']) ?> (<?= $ptag['count'] ?>)
                        </a>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>

    </div>
</div>
