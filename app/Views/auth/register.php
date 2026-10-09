<div class="form-container">
    <h2>ثبت نام</h2>
    <form action="<?= BASE_URL ?>/register" method="POST">
        <input type="hidden" name="csrf_token" value="<?= Security::generateCSRFToken() ?>">

        <div class="form-group">
            <label for="username">نام کاربری (انگلیسی)</label>
            <input type="text" id="username" name="username" required>
        </div>

        <div class="form-group">
            <label for="email">ایمیل</label>
            <input type="email" id="email" name="email" required>
        </div>

        <div class="form-group">
            <label for="password">رمز عبور</label>
            <input type="password" id="password" name="password" required>
        </div>

        <div class="form-group">
            <label for="password_confirm">تکرار رمز عبور</label>
            <input type="password" id="password_confirm" name="password_confirm" required>
        </div>

        <button type="submit" class="btn">ثبت نام</button>
    </form>
    <p style="margin-top: 15px;">قبلا ثبت نام کرده اید؟ <a href="<?= BASE_URL ?>/login">وارد شوید</a>.</p>
</div>
