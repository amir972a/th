<div class="form-container">
    <h2>راه‌اندازی اولیه: ساخت مدیر کل</h2>
    <p style="margin-bottom: 15px; color: #ffb4b8; background: rgba(229, 9, 20, 0.1); padding: 10px; border-radius: 4px;">شما اولین مدیر این پلتفرم را می‌سازید. این صفحه پس از ساخت مدیر دیگر در دسترس نخواهد بود.</p>

    <form action="<?= BASE_URL ?>/setup" method="POST">
        <input type="hidden" name="csrf_token" value="<?= Security::generateCSRFToken() ?>">

        <div class="form-group">
            <label for="username">نام کاربری مدیر (انگلیسی)</label>
            <input type="text" id="username" name="username" required>
        </div>

        <div class="form-group">
            <label for="email">ایمیل مدیر</label>
            <input type="email" id="email" name="email" required>
        </div>

        <div class="form-group">
            <label for="password">رمز عبور (حداقل ۸ کاراکتر)</label>
            <input type="password" id="password" name="password" required>
        </div>

        <div class="form-group">
            <label for="password_confirm">تکرار رمز عبور</label>
            <input type="password" id="password_confirm" name="password_confirm" required>
        </div>

        <button type="submit" class="btn" style="background: green;">ساخت حساب مدیر</button>
    </form>
</div>
