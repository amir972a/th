<?php
$pageTitle = "ویرایش پروفایل - " . APP_NAME;
?>

<div class="container">
    <h2>ویرایش پروفایل</h2>

    <?php if (isset($error)): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>
    <?php if (isset($_SESSION['success'])): ?>
        <div class="alert alert-success"><?= htmlspecialchars($_SESSION['success']) ?></div>
        <?php unset($_SESSION['success']); ?>
    <?php endif; ?>

    <form method="POST" action="/profile/edit" class="profile-edit-form">
        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token'] ?? '') ?>">

        <div class="form-group">
            <label for="display_name">نام نمایشی</label>
            <input type="text" name="display_name" id="display_name" class="form-control" value="<?= htmlspecialchars($user['display_name'] ?? '') ?>">
        </div>

        <div class="form-group">
            <label for="bio">بیوگرافی</label>
            <textarea name="bio" id="bio" class="form-control" rows="4"><?= htmlspecialchars($user['bio'] ?? '') ?></textarea>
        </div>

        <div class="form-group">
            <label for="gender">جنسیت</label>
            <select name="gender" id="gender" class="form-control">
                <option value="">انتخاب کنید</option>
                <option value="male" <?= (isset($user['gender']) && $user['gender'] == 'male') ? 'selected' : '' ?>>مرد</option>
                <option value="female" <?= (isset($user['gender']) && $user['gender'] == 'female') ? 'selected' : '' ?>>زن</option>
                <option value="other" <?= (isset($user['gender']) && $user['gender'] == 'other') ? 'selected' : '' ?>>سایر</option>
            </select>
        </div>

        <div class="form-group">
            <label for="interests">علایق</label>
            <input type="text" name="interests" id="interests" class="form-control" placeholder="مثال: برنامه نویسی، ورزش" value="<?= htmlspecialchars($user['interests'] ?? '') ?>">
        </div>

        <button type="submit" class="btn btn-primary mt-3">ذخیره تغییرات</button>
    </form>
</div>
