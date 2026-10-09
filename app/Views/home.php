<div class="home-container">
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
