<div class="admin-container">
    <h2>مدیریت هدر (Header)</h2>

    <div style="margin-bottom: 20px;">
        <a href="<?= BASE_URL ?>/admin" class="btn" style="background: var(--secondary-bg);">بازگشت به داشبورد</a>
    </div>

    <form action="<?= BASE_URL ?>/admin/settings/header" method="POST" style="background: var(--secondary-bg); padding: 20px; border-radius: 8px;">
        <input type="hidden" name="csrf_token" value="<?= Security::generateCSRFToken() ?>">

        <h3>متن‌ها و لوگو</h3>
        <div class="form-group">
            <label>پیام خوش‌آمدگویی (پشتیبانی از HTML):</label>
            <input type="text" name="welcome_msg" value="<?= htmlspecialchars($headerConfig['welcome_msg'] ?? '') ?>" class="form-control" style="width: 100%; direction: ltr;">
        </div>

        <div class="form-group">
            <label>متن لوگو بخش اول:</label>
            <input type="text" name="logo_text_1" value="<?= htmlspecialchars($headerConfig['logo_text_1'] ?? '') ?>" class="form-control">
        </div>

        <div class="form-group">
            <label>متن لوگو بخش دوم:</label>
            <input type="text" name="logo_text_2" value="<?= htmlspecialchars($headerConfig['logo_text_2'] ?? '') ?>" class="form-control">
        </div>

        <div class="form-group">
            <label>آدرس تصویر لوگو (اگر خالی باشد، متن نمایش داده می‌شود):</label>
            <input type="text" name="logo_url" value="<?= htmlspecialchars($headerConfig['logo_url'] ?? '') ?>" class="form-control" style="direction: ltr;">
        </div>

        <hr style="margin: 20px 0; border-color: var(--border-color);">

        <h3>تنظیمات بخش‌ها</h3>
        <div class="form-group" style="display: flex; align-items: center; gap: 10px;">
            <input type="checkbox" name="search_enabled" id="search_enabled" <?= (isset($headerConfig['search_enabled']) && $headerConfig['search_enabled']) ? 'checked' : '' ?>>
            <label for="search_enabled" style="margin: 0;">نمایش نوار جستجو</label>
        </div>
        <div class="form-group" style="display: flex; align-items: center; gap: 10px;">
            <input type="checkbox" name="auth_enabled" id="auth_enabled" <?= (isset($headerConfig['auth_enabled']) && $headerConfig['auth_enabled']) ? 'checked' : '' ?>>
            <label for="auth_enabled" style="margin: 0;">نمایش دکمه‌های ورود/ثبت‌نام/پروفایل</label>
        </div>
        <div class="form-group" style="display: flex; align-items: center; gap: 10px;">
            <input type="checkbox" name="upload_enabled" id="upload_enabled" <?= (isset($headerConfig['upload_enabled']) && $headerConfig['upload_enabled']) ? 'checked' : '' ?>>
            <label for="upload_enabled" style="margin: 0;">نمایش دکمه آپلود</label>
        </div>

        <hr style="margin: 20px 0; border-color: var(--border-color);">

        <h3>منوی اصلی</h3>
        <table style="width: 100%;" id="menu-table">
            <thead>
                <tr>
                    <th>عنوان</th>
                    <th>لینک</th>
                    <th>فعال</th>
                    <th>ترتیب</th>
                    <th>عملیات</th>
                </tr>
            </thead>
            <tbody id="menu-tbody">
                <?php if (isset($headerConfig['menu']) && is_array($headerConfig['menu'])): ?>
                    <?php foreach ($headerConfig['menu'] as $i => $item): ?>
                    <tr>
                        <td><input type="text" name="menu[<?= $i ?>][label]" value="<?= htmlspecialchars($item['label'] ?? '') ?>" class="form-control"></td>
                        <td>
                            <div style="display: flex; gap: 5px;">
                                <input type="text" name="menu[<?= $i ?>][url]" value="<?= htmlspecialchars($item['url'] ?? '') ?>" class="form-control" style="direction: ltr; flex: 1;" placeholder="لینک مستقیم یا انتخاب...">
                                <select class="form-control" onchange="this.previousElementSibling.value = this.value; this.value='';" style="width: auto;">
                                    <option value="">انتخاب...</option>
                                    <optgroup label="لینک‌های سیستمی">
                                        <option value="/">خانه</option>
                                        <option value="/login">ورود</option>
                                        <option value="/register">ثبت نام</option>
                                        <option value="/upload">آپلود</option>
                                    </optgroup>
                                    <optgroup label="دسته‌بندی‌ها">
                                        <?php foreach ($categories as $cat): ?>
                                            <option value="category:<?= htmlspecialchars($cat['id']) ?>"><?= htmlspecialchars($cat['name']) ?></option>
                                        <?php endforeach; ?>
                                    </optgroup>
                                    <optgroup label="صفحات">
                                        <?php foreach ($pages as $p): ?>
                                            <option value="page:<?= htmlspecialchars($p['id']) ?>"><?= htmlspecialchars($p['title']) ?></option>
                                        <?php endforeach; ?>
                                    </optgroup>
                                </select>
                            </div>
                        </td>
                        <td><input type="checkbox" name="menu[<?= $i ?>][enabled]" <?= (isset($item['enabled']) && $item['enabled']) ? 'checked' : '' ?>></td>
                        <td>
                            <button type="button" class="btn" style="padding: 2px 5px;" onclick="moveRowUp(this)">↑</button>
                            <button type="button" class="btn" style="padding: 2px 5px;" onclick="moveRowDown(this)">↓</button>
                        </td>
                        <td><button type="button" class="btn" style="background: red; padding: 5px;" onclick="this.closest('tr').remove();">حذف</button></td>
                    </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
        <button type="button" class="btn mt-3" style="background: var(--bg-color); border: 1px solid var(--border-color); color: var(--text-color);" onclick="addMenuItem()">+ افزودن آیتم جدید</button>

        <div style="margin-top: 30px;">
            <button type="submit" class="btn btn-primary">ذخیره تغییرات</button>
        </div>
    </form>
</div>

<script>
let menuCounter = <?= isset($headerConfig['menu']) ? count($headerConfig['menu']) : 0 ?>;
function addMenuItem() {
    const tbody = document.getElementById('menu-tbody');
    const tr = document.createElement('tr');
    tr.innerHTML = `
        <td><input type="text" name="menu[${menuCounter}][label]" class="form-control"></td>
        <td>
            <div style="display: flex; gap: 5px;">
                <input type="text" name="menu[${menuCounter}][url]" class="form-control" style="direction: ltr; flex: 1;" value="#" placeholder="لینک مستقیم یا انتخاب...">
                <select class="form-control" onchange="this.previousElementSibling.value = this.value; this.value='';" style="width: auto;">
                    <option value="">انتخاب...</option>
                    <optgroup label="لینک‌های سیستمی">
                        <option value="/">خانه</option>
                        <option value="/login">ورود</option>
                        <option value="/register">ثبت نام</option>
                        <option value="/upload">آپلود</option>
                    </optgroup>
                    <optgroup label="دسته‌بندی‌ها">
                        <?php foreach ($categories as $cat): ?>
                            <option value="category:<?= htmlspecialchars($cat['id']) ?>"><?= htmlspecialchars($cat['name']) ?></option>
                        <?php endforeach; ?>
                    </optgroup>
                    <optgroup label="صفحات">
                        <?php foreach ($pages as $page): ?>
                            <option value="page:<?= htmlspecialchars($page['id']) ?>"><?= htmlspecialchars($page['title']) ?></option>
                        <?php endforeach; ?>
                    </optgroup>
                </select>
            </div>
        </td>
        <td><input type="checkbox" name="menu[${menuCounter}][enabled]" checked></td>
        <td>
            <button type="button" class="btn" style="padding: 2px 5px;" onclick="moveRowUp(this)">↑</button>
            <button type="button" class="btn" style="padding: 2px 5px;" onclick="moveRowDown(this)">↓</button>
        </td>
        <td><button type="button" class="btn" style="background: red; padding: 5px;" onclick="this.closest('tr').remove();">حذف</button></td>
    `;
    tbody.appendChild(tr);
    menuCounter++;
}

function moveRowUp(btn) {
    const row = btn.closest('tr');
    if (row.previousElementSibling) {
        row.parentNode.insertBefore(row, row.previousElementSibling);
    }
}

function moveRowDown(btn) {
    const row = btn.closest('tr');
    if (row.nextElementSibling) {
        row.parentNode.insertBefore(row.nextElementSibling, row);
    }
}
</script>
