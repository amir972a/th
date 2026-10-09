<div class="form-container">
    <h2>ورود به حساب کاربری</h2>
    <form action="<?= BASE_URL ?>/login" method="POST">
        <input type="hidden" name="csrf_token" value="<?= Security::generateCSRFToken() ?>">

        <div class="form-group">
            <label for="username">نام کاربری</label>
            <input type="text" id="username" name="username" required>
        </div>

        <div class="form-group">
            <label for="password">رمز عبور</label>
            <input type="password" id="password" name="password" required>
        </div>

        <button type="submit" class="btn">ورود</button>
    </form>
    <p style="margin-top: 15px;">حساب کاربری ندارید؟ <a href="<?= BASE_URL ?>/register">ثبت نام کنید</a>.</p>
</div>
