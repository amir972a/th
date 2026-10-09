<style>
.profile-header {
    background: var(--secondary-bg);
    padding: 30px;
    border-radius: 8px;
    margin-bottom: 20px;
    display: flex;
    justify-content: space-between;
    align-items: center;
}
.profile-info h1 {
    margin-bottom: 10px;
}
.profile-actions form {
    display: inline-block;
}
.section-title {
    margin: 20px 0;
    border-bottom: 1px solid var(--border-color);
    padding-bottom: 10px;
}
.friends-list {
    display: flex;
    flex-wrap: wrap;
    gap: 15px;
}
.friend-card {
    background: var(--secondary-bg);
    padding: 15px;
    border-radius: 8px;
    width: 200px;
    text-align: center;
}
</style>

<div class="profile-header">
    <div class="profile-info">
        <h1><?= Security::escape($profileUser['display_name']) ?> <small style="color:#aaa; font-size:16px;">(@<?= Security::escape($profileUser['username']) ?>)</small></h1>
        <p><?= Security::escape($profileUser['bio'] ?? 'بیوگرافی تنظیم نشده است.') ?></p>
        <p style="color:#aaa; font-size:14px; margin-top:5px;">تاریخ عضویت: <?= date('Y/m/d', strtotime($profileUser['created_at'])) ?></p>
    </div>

    <div class="profile-actions">
        <?php if (!Session::get('user_id')): ?>
            <a href="<?= BASE_URL ?>/login" class="btn">برای دوستی وارد شوید</a>
        <?php elseif (!$isOwner): ?>
            <form action="<?= BASE_URL ?>/friend/<?= $profileUser['id'] ?>" method="POST">
                <input type="hidden" name="csrf_token" value="<?= Security::generateCSRFToken() ?>">
                <?php if (!$friendStatus): ?>
                    <button type="submit" name="action" value="request" class="btn">درخواست دوستی</button>
                <?php elseif ($friendStatus['status'] === 'pending'): ?>
                    <?php if ($friendStatus['requester_id'] == Session::get('user_id')): ?>
                        <button type="submit" name="action" value="cancel" class="btn" style="background:#555;">لغو درخواست</button>
                    <?php else: ?>
                        <button type="submit" name="action" value="accept" class="btn" style="background:green;">قبول درخواست</button>
                        <button type="submit" name="action" value="reject" class="btn" style="background:red;">رد درخواست</button>
                    <?php endif; ?>
                <?php elseif ($friendStatus['status'] === 'accepted'): ?>
                    <button type="button" class="btn" style="background:green; cursor:default;">دوست هستید ✓</button>
                    <button type="submit" name="action" value="remove" class="btn" style="background:red; margin-right:5px;">حذف دوستی</button>
                <?php endif; ?>
            </form>
        <?php else: ?>
            <a href="<?= BASE_URL ?>/profile/edit" class="btn">ویرایش پروفایل</a>
        <?php endif; ?>
    </div>
</div>

<?php if (!empty($profileUser['gender']) || !empty($profileUser['interests'])): ?>
    <div style="background: var(--secondary-bg); padding: 15px; border-radius: 8px; margin-bottom: 20px;">
        <?php if (!empty($profileUser['gender'])): ?>
            <p><strong>جنسیت:</strong>
                <?php
                    $g = $profileUser['gender'];
                    echo ($g == 'male') ? 'مرد' : (($g == 'female') ? 'زن' : 'سایر');
                ?>
            </p>
        <?php endif; ?>
        <?php if (!empty($profileUser['interests'])): ?>
            <p><strong>علایق:</strong> <?= Security::escape($profileUser['interests']) ?></p>
        <?php endif; ?>
    </div>
<?php endif; ?>

<?php if ($isOwner && !empty($pendingRequests)): ?>
    <div style="background: rgba(229, 9, 20, 0.1); border: 1px solid var(--primary-color); padding: 15px; border-radius: 8px; margin-bottom: 20px;">
        <h3>درخواست‌های دوستی جدید</h3>
        <ul style="list-style:none; margin-top: 10px;">
            <?php foreach ($pendingRequests as $req): ?>
                <li style="margin-bottom: 10px; display:flex; justify-content:space-between; align-items:center;">
                    <span><a href="<?= BASE_URL ?>/profile/<?= Security::escape($req['username']) ?>"><?= Security::escape($req['display_name']) ?></a></span>
                    <form action="<?= BASE_URL ?>/friend/<?= $req['requester_id'] ?>" method="POST">
                        <input type="hidden" name="csrf_token" value="<?= Security::generateCSRFToken() ?>">
                        <button type="submit" name="action" value="accept" class="btn" style="background:green; padding:5px 10px;">قبول</button>
                        <button type="submit" name="action" value="reject" class="btn" style="background:red; padding:5px 10px;">رد</button>
                    </form>
                </li>
            <?php endforeach; ?>
        </ul>
    </div>
<?php endif; ?>

<h2 class="section-title">ویدیوها (<?= count($videos) ?>)</h2>
<div class="video-grid">
    <?php if (empty($videos)): ?>
        <p>ویدیویی یافت نشد.</p>
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
                        <?php if($isOwner) echo '<br>وضعیت: ' . $video['status']; ?>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>

<h2 class="section-title">دوستان (<?= count($friends) ?>)</h2>
<div class="friends-list">
    <?php if (empty($friends)): ?>
        <p>دوستی یافت نشد.</p>
    <?php else: ?>
        <?php foreach ($friends as $friend): ?>
            <div class="friend-card">
                <a href="<?= BASE_URL ?>/profile/<?= Security::escape($friend['username']) ?>" style="font-weight:bold; font-size:16px;">
                    <?= Security::escape($friend['display_name']) ?>
                </a>
                <p style="color:#aaa; font-size:12px;">@<?= Security::escape($friend['username']) ?></p>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>
