<div class="home-container">
    <?php if (!empty($popularTags)): ?>
    <div class="popular-tags-section" style="margin-bottom: 30px; background: var(--secondary-bg); padding: 15px; border-radius: 8px;">
        <h3 style="margin-bottom: 10px; font-size: 16px;">تگ‌های محبوب 🔥</h3>
        <div style="display: flex; flex-wrap: wrap; gap: 8px;">
            <?php foreach ($popularTags as $ptag): ?>
                <a href="<?= BASE_URL ?>/tag/<?= htmlspecialchars($ptag['slug']) ?>" class="tag-chip" style="background: var(--bg-color); border: 1px solid var(--border-color); padding: 5px 12px; border-radius: 15px; font-size: 14px; text-decoration: none; color: var(--text-color);">
                    <?= htmlspecialchars($ptag['name']) ?>
                </a>
            <?php endforeach; ?>
        </div>
    </div>
    <?php endif; ?>

    <h2 style="margin-bottom: 20px;">ویدیوهای جدید</h2>
    <div class="video-grid">
        <?php if (empty($latestVideos)): ?>
            <p>در حال حاضر ویدیویی وجود ندارد.</p>
        <?php else: ?>
            <?php foreach ($latestVideos as $video): ?>
                <div class="video-card">
                    <a href="<?= BASE_URL ?>/watch/<?= $video['id'] ?>">
                        <img src="<?= BASE_URL ?>/thumbnail/<?= $video['thumbnail_path'] ?? 'default' ?>" class="video-thumb" alt="<?= Security::escape($video['title']) ?>">
                    </a>
                    <div class="video-info">
                        <a href="<?= BASE_URL ?>/watch/<?= $video['id'] ?>" class="video-title">
                            <?= Security::escape($video['title']) ?>
                        </a>
                        <div class="video-meta">
                            آپلود شده توسط <a href="<?= BASE_URL ?>/profile/<?= Security::escape($video['username']) ?>"><?= Security::escape($video['display_name']) ?></a><br>
                            <?= number_format($video['views']) ?> بازدید • <?= date('Y/m/d', strtotime($video['created_at'])) ?>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>
