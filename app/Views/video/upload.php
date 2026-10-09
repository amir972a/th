<div class="form-container" style="max-width: 600px;">
    <h2>آپلود ویدیو جدید</h2>
    <form action="<?= BASE_URL ?>/upload" method="POST" enctype="multipart/form-data">
        <input type="hidden" name="csrf_token" value="<?= Security::generateCSRFToken() ?>">

        <div class="form-group">
            <label for="title">عنوان ویدیو</label>
            <input type="text" id="title" name="title" required>
        </div>

        <div class="form-group">
            <label for="description">توضیحات</label>
            <textarea id="description" name="description" rows="4"></textarea>
        </div>

        <div class="form-group">
            <label for="category_id">دسته‌بندی</label>
            <select id="category_id" name="category_id">
                <option value="">انتخاب کنید...</option>
                <?php foreach ($categories as $cat): ?>
                    <option value="<?= $cat['id'] ?>"><?= Security::escape($cat['name']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="form-group">
            <label for="visibility">حریم خصوصی</label>
            <select id="visibility" name="visibility">
                <option value="public">عمومی</option>
                <option value="private">خصوصی (فقط دوستان)</option>
            </select>
        </div>

        <div class="form-group">
            <label for="video_file">فایل ویدیو (MP4, WebM)</label>
            <input type="file" id="video_file" name="video_file" accept="video/mp4,video/webm,video/ogg" required style="background: #2b2b2f; padding: 10px; border-radius: 4px;">
        </div>

        <button type="submit" class="btn" onclick="this.innerHTML='در حال آپلود...';">آپلود</button>
    </form>
</div>
