<div class="container">
    <div style="display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid var(--border-color); margin-bottom: 20px; padding-bottom: 10px;">
        <h2>ویدیوهای مرتبط با دسته‌بندی: <?= htmlspecialchars($category['name']) ?></h2>
    </div>
    <div class="video-grid">
        <?php if (empty($videos)): ?>
            <p>هیچ ویدیویی برای این دسته‌بندی یافت نشد یا شما اجازه مشاهده آن‌ها را ندارید.</p>
        <?php else: ?>
            <?php foreach ($videos as $video): ?>
                <div class="video-card">
                    <a href="<?= BASE_URL ?>/watch/<?= $video['id'] ?>">
                        <img src="<?= BASE_URL ?>/thumbnail/<?= $video['thumbnail_path'] ?? 'default' ?>" class="video-thumb" alt="<?= Security::escape($video['title']) ?>">
                    </a>
                    <div class="video-info">
                        <a href="<?= BASE_URL ?>/watch/<?= $video['id'] ?>" class="video-title">
                            <?= Security::escape($video['title']) ?>
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
</div>
