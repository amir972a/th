<style>
.video-player-container {
    background: #000;
    width: 100%;
    max-width: 1000px;
    margin: 0 auto 20px auto;
    border-radius: 8px;
    overflow: hidden;
}
.video-player-container video {
    width: 100%;
    max-height: 600px;
    display: block;
}
.video-details {
    max-width: 1000px;
    margin: 0 auto;
    background: var(--secondary-bg);
    padding: 20px;
    border-radius: 8px;
}
.video-header {
    border-bottom: 1px solid var(--border-color);
    padding-bottom: 15px;
    margin-bottom: 15px;
}
.video-header h1 {
    font-size: 24px;
    margin-bottom: 10px;
}
.video-stats {
    display: flex;
    justify-content: space-between;
    color: #aaa;
    font-size: 14px;
}
.video-author {
    margin-bottom: 15px;
    font-weight: bold;
}
.video-description {
    white-space: pre-wrap;
    color: #ddd;
}
</style>

<div class="video-player-container">
    <video controls preload="metadata" poster="<?= BASE_URL ?>/thumbnail/<?= $video['thumbnail_path'] ?? 'default.jpg' ?>">
        <source src="<?= BASE_URL ?>/stream/<?= $video['id'] ?>" type="video/mp4">
        مرورگر شما از پخش ویدیو پشتیبانی نمی‌کند.
    </video>
</div>

<div class="video-details">
    <div class="video-header">
        <h1><?= Security::escape($video['title']) ?></h1>
        <div class="video-stats">
            <span>بازدید: <?= number_format($video['views']) ?></span>
            <span>تاریخ: <?= date('Y/m/d', strtotime($video['created_at'])) ?></span>
        </div>
    </div>

    <div class="video-author">
        آپلود شده توسط: <a href="<?= BASE_URL ?>/profile/<?= Security::escape($video['username']) ?>"><?= Security::escape($video['display_name']) ?></a>
        <?php if ($video['category_name']): ?>
            | دسته‌بندی: <a href="<?= BASE_URL ?>/category/<?= $video['category_id'] ?>"><?= Security::escape($video['category_name']) ?></a>
        <?php endif; ?>
    </div>

    <div class="video-description">
        <?= nl2br(Security::escape($video['description'])) ?>
    </div>

    <div class="video-actions" style="margin-top: 15px; border-top: 1px solid var(--border-color); padding-top: 15px; display: flex; gap: 10px;">
        <form action="<?= BASE_URL ?>/video/<?= $video['id'] ?>/rate" method="POST">
            <input type="hidden" name="csrf_token" value="<?= Security::generateCSRFToken() ?>">
            <button type="submit" name="rating" value="like" class="btn" style="background: <?= $userRating == 1 ? 'var(--primary-color)' : 'var(--hover-bg)' ?>">
                👍 <?= (int)($ratings['likes'] ?? 0) ?>
            </button>
            <button type="submit" name="rating" value="dislike" class="btn" style="background: <?= $userRating == -1 ? 'var(--primary-color)' : 'var(--hover-bg)' ?>">
                👎 <?= (int)($ratings['dislikes'] ?? 0) ?>
            </button>
        </form>
    </div>
</div>

<div class="video-comments" style="max-width: 1000px; margin: 20px auto; background: var(--secondary-bg); padding: 20px; border-radius: 8px;">
    <h3>نظرات (<?= count($comments) ?>)</h3>

    <?php if (Session::get('user_id')): ?>
        <form action="<?= BASE_URL ?>/video/<?= $video['id'] ?>/comment" method="POST" style="margin-bottom: 20px; margin-top: 15px;">
            <input type="hidden" name="csrf_token" value="<?= Security::generateCSRFToken() ?>">
            <div class="form-group">
                <textarea name="content" rows="3" placeholder="نظر خود را بنویسید..." required></textarea>
            </div>
            <button type="submit" class="btn">ثبت نظر</button>
        </form>
    <?php else: ?>
        <p style="margin: 15px 0;"><a href="<?= BASE_URL ?>/login">وارد شوید</a> تا بتوانید نظر بدهید.</p>
    <?php endif; ?>

    <div class="comments-list">
        <?php foreach ($comments as $comment): ?>
            <div class="comment" style="border-bottom: 1px solid var(--border-color); padding: 10px 0;">
                <div class="comment-author" style="font-weight: bold; margin-bottom: 5px;">
                    <a href="<?= BASE_URL ?>/profile/<?= Security::escape($comment['username']) ?>"><?= Security::escape($comment['display_name']) ?></a>
                    <span style="font-size: 12px; color: #aaa; font-weight: normal; margin-right: 10px;">
                        <?= date('Y/m/d H:i', strtotime($comment['created_at'])) ?>
                    </span>
                </div>
                <div class="comment-content">
                    <?= nl2br(Security::escape($comment['content'])) ?>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>
