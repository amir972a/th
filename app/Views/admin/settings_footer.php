<div class="admin-container">
    <h2>مدیریت فوتر (Footer)</h2>

    <div style="margin-bottom: 20px;">
        <a href="<?= BASE_URL ?>/admin" class="btn" style="background: var(--secondary-bg);">بازگشت به داشبورد</a>
    </div>

    <form action="<?= BASE_URL ?>/admin/settings/footer" method="POST" style="background: var(--secondary-bg); padding: 20px; border-radius: 8px;">
        <input type="hidden" name="csrf_token" value="<?= Security::generateCSRFToken() ?>">

        <h3>متن‌ها و لوگو</h3>
        <div class="form-group">
            <label>متن کپی‌رایت (از {year} برای سال جاری استفاده کنید):</label>
            <input type="text" name="copyright_text" value="<?= htmlspecialchars($footerConfig['copyright_text'] ?? '') ?>" class="form-control" style="width: 100%; direction: ltr;">
        </div>

        <div class="form-group">
            <label>توضیحات کوتاه:</label>
            <textarea name="description" class="form-control" rows="3"><?= htmlspecialchars($footerConfig['description'] ?? '') ?></textarea>
        </div>

        <div class="form-group">
            <label>آدرس تصویر لوگوی فوتر (اگر خالی باشد، نام سایت نمایش داده می‌شود):</label>
            <input type="text" name="logo_url" value="<?= htmlspecialchars($footerConfig['logo_url'] ?? '') ?>" class="form-control" style="direction: ltr;">
        </div>

        <hr style="margin: 20px 0; border-color: var(--border-color);">

        <h3>ستون‌های لینک</h3>
        <div id="columns-container">
            <?php if (isset($footerConfig['columns']) && is_array($footerConfig['columns'])): ?>
                <?php foreach ($footerConfig['columns'] as $cIndex => $col): ?>
                    <div class="footer-column-card" style="border: 1px solid var(--border-color); padding: 15px; border-radius: 8px; margin-bottom: 15px; background: var(--bg-color);">
                        <div style="display: flex; gap: 10px; margin-bottom: 10px; align-items: center;">
                            <button type="button" class="btn" style="padding: 2px 5px;" onclick="moveColUp(this)">↑</button>
                            <button type="button" class="btn" style="padding: 2px 5px;" onclick="moveColDown(this)">↓</button>
                            <label>عنوان ستون:</label>
                            <input type="text" name="col[<?= $cIndex ?>][title]" value="<?= htmlspecialchars($col['title'] ?? '') ?>" class="form-control" style="width: 200px;">
                            <label><input type="checkbox" name="col[<?= $cIndex ?>][enabled]" <?= (isset($col['enabled']) && $col['enabled']) ? 'checked' : '' ?>> فعال</label>
                            <button type="button" class="btn" style="background: red; padding: 5px; margin-right: auto;" onclick="this.closest('.footer-column-card').remove();">حذف ستون</button>
                        </div>

                        <table style="width: 100%;">
                            <thead>
                                <tr>
                                    <th>عنوان لینک</th>
                                    <th>URL</th>
                                    <th>فعال</th>
                                    <th>ترتیب</th>
                                    <th>عملیات</th>
                                </tr>
                            </thead>
                            <tbody id="links-tbody-<?= $cIndex ?>">
                                <?php if (isset($col['links']) && is_array($col['links'])): ?>
                                    <?php foreach ($col['links'] as $lIndex => $link): ?>
                                    <tr>
                                        <td><input type="text" name="col[<?= $cIndex ?>][links][<?= $lIndex ?>][label]" value="<?= htmlspecialchars($link['label'] ?? '') ?>" class="form-control"></td>
                                        <td><input type="text" name="col[<?= $cIndex ?>][links][<?= $lIndex ?>][url]" value="<?= htmlspecialchars($link['url'] ?? '') ?>" class="form-control" style="direction: ltr;"></td>
                                        <td><input type="checkbox" name="col[<?= $cIndex ?>][links][<?= $lIndex ?>][enabled]" <?= (isset($link['enabled']) && $link['enabled']) ? 'checked' : '' ?>></td>
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
                        <button type="button" class="btn mt-3" style="background: transparent; border: 1px solid var(--border-color); color: var(--text-color);" onclick="addLink(this, <?= $cIndex ?>)">+ افزودن لینک</button>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
        <button type="button" class="btn" style="background: var(--primary-color);" onclick="addColumn()">+ افزودن ستون جدید</button>

        <hr style="margin: 20px 0; border-color: var(--border-color);">

        <h3>شبکه‌های اجتماعی</h3>
        <table style="width: 100%;">
            <thead>
                <tr>
                    <th>کلاس آیکون / ایموجی</th>
                    <th>لینک</th>
                    <th>فعال</th>
                    <th>ترتیب</th>
                    <th>عملیات</th>
                </tr>
            </thead>
            <tbody id="social-tbody">
                <?php if (isset($footerConfig['social_links']) && is_array($footerConfig['social_links'])): ?>
                    <?php foreach ($footerConfig['social_links'] as $i => $social): ?>
                    <tr>
                        <td><input type="text" name="social[<?= $i ?>][icon]" value="<?= htmlspecialchars($social['icon'] ?? '') ?>" class="form-control"></td>
                        <td><input type="text" name="social[<?= $i ?>][url]" value="<?= htmlspecialchars($social['url'] ?? '') ?>" class="form-control" style="direction: ltr;"></td>
                        <td><input type="checkbox" name="social[<?= $i ?>][enabled]" <?= (isset($social['enabled']) && $social['enabled']) ? 'checked' : '' ?>></td>
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
        <button type="button" class="btn mt-3" style="background: var(--bg-color); border: 1px solid var(--border-color); color: var(--text-color);" onclick="addSocial()">+ افزودن شبکه اجتماعی</button>

        <div style="margin-top: 30px;">
            <button type="submit" class="btn btn-primary">ذخیره تغییرات</button>
        </div>
    </form>
</div>

<script>
let colCounter = <?= isset($footerConfig['columns']) ? count($footerConfig['columns']) : 0 ?>;
let linkCounters = {};
<?php if (isset($footerConfig['columns']) && is_array($footerConfig['columns'])): ?>
    <?php foreach ($footerConfig['columns'] as $cIndex => $col): ?>
        linkCounters[<?= $cIndex ?>] = <?= isset($col['links']) ? count($col['links']) : 0 ?>;
    <?php endforeach; ?>
<?php endif; ?>

let socialCounter = <?= isset($footerConfig['social_links']) ? count($footerConfig['social_links']) : 0 ?>;

function addColumn() {
    const container = document.getElementById('columns-container');
    const div = document.createElement('div');
    div.className = 'footer-column-card';
    div.style = 'border: 1px solid var(--border-color); padding: 15px; border-radius: 8px; margin-bottom: 15px; background: var(--bg-color);';

    linkCounters[colCounter] = 0;

    div.innerHTML = `
        <div style="display: flex; gap: 10px; margin-bottom: 10px; align-items: center;">
            <button type="button" class="btn" style="padding: 2px 5px;" onclick="moveColUp(this)">↑</button>
            <button type="button" class="btn" style="padding: 2px 5px;" onclick="moveColDown(this)">↓</button>
            <label>عنوان ستون:</label>
            <input type="text" name="col[${colCounter}][title]" class="form-control" style="width: 200px;">
            <label><input type="checkbox" name="col[${colCounter}][enabled]" checked> فعال</label>
            <button type="button" class="btn" style="background: red; padding: 5px; margin-right: auto;" onclick="this.closest('.footer-column-card').remove();">حذف ستون</button>
        </div>
        <table style="width: 100%;">
            <thead><tr><th>عنوان لینک</th><th>URL</th><th>فعال</th><th>ترتیب</th><th>عملیات</th></tr></thead>
            <tbody id="links-tbody-${colCounter}"></tbody>
        </table>
        <button type="button" class="btn mt-3" style="background: transparent; border: 1px solid var(--border-color); color: var(--text-color);" onclick="addLink(this, ${colCounter})">+ افزودن لینک</button>
    `;
    container.appendChild(div);
    colCounter++;
}

function addLink(btn, cIndex) {
    const tbody = btn.closest('.footer-column-card').querySelector('tbody');
    if (!tbody) return;

    const lIndex = linkCounters[cIndex] || 0;
    const tr = document.createElement('tr');
    tr.innerHTML = `
        <td><input type="text" name="col[${cIndex}][links][${lIndex}][label]" class="form-control"></td>
        <td>
            <div style="display: flex; gap: 5px;">
                <input type="text" name="col[${cIndex}][links][${lIndex}][url]" class="form-control" style="direction: ltr; flex: 1;" value="#" placeholder="لینک مستقیم یا انتخاب...">
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
        <td><input type="checkbox" name="col[${cIndex}][links][${lIndex}][enabled]" checked></td>
        <td>
            <button type="button" class="btn" style="padding: 2px 5px;" onclick="moveRowUp(this)">↑</button>
            <button type="button" class="btn" style="padding: 2px 5px;" onclick="moveRowDown(this)">↓</button>
        </td>
        <td><button type="button" class="btn" style="background: red; padding: 5px;" onclick="this.closest('tr').remove();">حذف</button></td>
    `;
    tbody.appendChild(tr);
    linkCounters[cIndex] = lIndex + 1;
}

function addSocial() {
    const tbody = document.getElementById('social-tbody');
    const tr = document.createElement('tr');
    tr.innerHTML = `
        <td><input type="text" name="social[${socialCounter}][icon]" class="form-control" placeholder="ایموجی یا متن"></td>
        <td><input type="text" name="social[${socialCounter}][url]" class="form-control" style="direction: ltr;" value="#"></td>
        <td><input type="checkbox" name="social[${socialCounter}][enabled]" checked></td>
        <td>
            <button type="button" class="btn" style="padding: 2px 5px;" onclick="moveRowUp(this)">↑</button>
            <button type="button" class="btn" style="padding: 2px 5px;" onclick="moveRowDown(this)">↓</button>
        </td>
        <td><button type="button" class="btn" style="background: red; padding: 5px;" onclick="this.closest('tr').remove();">حذف</button></td>
    `;
    tbody.appendChild(tr);
    socialCounter++;
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

function moveColUp(btn) {
    const col = btn.closest('.footer-column-card');
    if (col.previousElementSibling) {
        col.parentNode.insertBefore(col, col.previousElementSibling);
    }
}

function moveColDown(btn) {
    const col = btn.closest('.footer-column-card');
    if (col.nextElementSibling) {
        col.parentNode.insertBefore(col.nextElementSibling, col);
    }
}
</script>
