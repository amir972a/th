<div class="form-container" style="max-width: 600px;">
    <h2>ویرایش ویدیو</h2>
    <form action="<?= BASE_URL ?>/video/<?= $video['id'] ?>/edit" method="POST">
        <input type="hidden" name="csrf_token" value="<?= Security::generateCSRFToken() ?>">

        <div class="form-group">
            <label for="title">عنوان ویدیو</label>
            <input type="text" id="title" name="title" value="<?= Security::escape($video['title']) ?>" required>
        </div>

        <div class="form-group">
            <label for="description">توضیحات</label>
            <textarea id="description" name="description" rows="4"><?= Security::escape($video['description']) ?></textarea>
        </div>

        <div class="form-group">
            <label for="category_id">دسته‌بندی</label>
            <select id="category_id" name="category_id">
                <option value="">انتخاب کنید...</option>
                <?php foreach ($categories as $cat): ?>
                    <option value="<?= $cat['id'] ?>" <?= $cat['id'] == $video['category_id'] ? 'selected' : '' ?>><?= Security::escape($cat['name']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="form-group">
            <label for="visibility">حریم خصوصی</label>
            <select id="visibility" name="visibility">
                <option value="public" <?= $video['visibility'] === 'public' ? 'selected' : '' ?>>عمومی</option>
                <option value="private" <?= $video['visibility'] === 'private' ? 'selected' : '' ?>>خصوصی (فقط دوستان)</option>
            </select>
        </div>

        <div class="form-group">
            <label for="tags_input">تگ‌ها (با اینتر جدا کنید)</label>
            <div class="tag-input-container">
                <div id="tags_display" class="tags-display"></div>
                <input type="text" id="tags_input" placeholder="افزودن تگ..." autocomplete="off">
                <input type="hidden" name="tags" id="tags_hidden" value="">
                <div id="tag_suggestions" class="tag-suggestions" style="display:none;"></div>
            </div>
        </div>

        <button type="submit" class="btn">ذخیره تغییرات</button>
        <a href="<?= BASE_URL ?>/watch/<?= $video['id'] ?>" class="btn" style="background: var(--secondary-bg); margin-right: 10px;">لغو</a>
    </form>
</div>

<style>
.tag-input-container {
    border: 1px solid var(--border-color);
    background: var(--input-bg);
    border-radius: 4px;
    padding: 5px;
    position: relative;
    display: flex;
    flex-wrap: wrap;
    align-items: center;
    gap: 5px;
}
.tags-display {
    display: flex;
    flex-wrap: wrap;
    gap: 5px;
}
.tag-chip {
    background: var(--primary-color);
    color: white;
    padding: 3px 8px;
    border-radius: 12px;
    font-size: 14px;
    display: flex;
    align-items: center;
    gap: 5px;
}
.tag-chip .remove-tag {
    cursor: pointer;
    font-weight: bold;
}
.tag-input-container input[type="text"] {
    border: none;
    background: transparent;
    outline: none;
    flex: 1;
    min-width: 100px;
    padding: 5px;
}
.tag-suggestions {
    position: absolute;
    top: 100%;
    left: 0;
    right: 0;
    background: var(--secondary-bg);
    border: 1px solid var(--border-color);
    z-index: 100;
    max-height: 150px;
    overflow-y: auto;
    border-radius: 4px;
    box-shadow: 0 4px 6px rgba(0,0,0,0.3);
}
.tag-suggestion-item {
    padding: 8px 10px;
    cursor: pointer;
}
.tag-suggestion-item:hover {
    background: rgba(255,255,255,0.1);
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const tagsInput = document.getElementById('tags_input');
    const tagsHidden = document.getElementById('tags_hidden');
    const tagsDisplay = document.getElementById('tags_display');
    const suggestionsBox = document.getElementById('tag_suggestions');

    // Initialize tags from PHP
    let tags = [];
    <?php if (isset($tags) && !empty($tags)): ?>
        <?php foreach ($tags as $tag): ?>
            tags.push(<?= json_encode($tag['name']) ?>);
        <?php endforeach; ?>
        updateTagsUI();
    <?php endif; ?>

    function updateTagsUI() {
        tagsDisplay.innerHTML = '';
        tags.forEach((tag, index) => {
            const chip = document.createElement('div');
            chip.className = 'tag-chip';

            const span = document.createElement('span');
            span.textContent = tag;

            const removeSpan = document.createElement('span');
            removeSpan.className = 'remove-tag';
            removeSpan.setAttribute('data-index', index);
            removeSpan.textContent = '×';

            chip.appendChild(span);
            chip.appendChild(removeSpan);
            tagsDisplay.appendChild(chip);
        });
        tagsHidden.value = tags.join(',');
    }

    tagsDisplay.addEventListener('click', function(e) {
        if (e.target.classList.contains('remove-tag')) {
            const index = e.target.getAttribute('data-index');
            tags.splice(index, 1);
            updateTagsUI();
        }
    });

    tagsInput.addEventListener('keydown', function(e) {
        if (e.key === 'Enter' || e.key === ',') {
            e.preventDefault();
            const val = this.value.trim().replace(/,/g, '');
            if (val && !tags.includes(val)) {
                tags.push(val);
                updateTagsUI();
            }
            this.value = '';
            suggestionsBox.style.display = 'none';
        } else if (e.key === 'Backspace' && this.value === '' && tags.length > 0) {
            tags.pop();
            updateTagsUI();
        }
    });

    let searchTimeout;
    tagsInput.addEventListener('input', function() {
        clearTimeout(searchTimeout);
        const query = this.value.trim();

        if (query.length < 2) {
            suggestionsBox.style.display = 'none';
            return;
        }

        searchTimeout = setTimeout(() => {
            fetch(`<?= BASE_URL ?>/api/tags/search?q=${encodeURIComponent(query)}`)
                .then(res => res.json())
                .then(data => {
                    suggestionsBox.innerHTML = '';
                    if (data.length > 0) {
                        data.forEach(tag => {
                            const item = document.createElement('div');
                            item.className = 'tag-suggestion-item';
                            item.textContent = tag;
                            item.addEventListener('click', function() {
                                if (!tags.includes(tag)) {
                                    tags.push(tag);
                                    updateTagsUI();
                                }
                                tagsInput.value = '';
                                suggestionsBox.style.display = 'none';
                                tagsInput.focus();
                            });
                            suggestionsBox.appendChild(item);
                        });
                        suggestionsBox.style.display = 'block';
                    } else {
                        suggestionsBox.style.display = 'none';
                    }
                });
        }, 300);
    });

    document.addEventListener('click', function(e) {
        if (!e.target.closest('.tag-input-container')) {
            suggestionsBox.style.display = 'none';
        }
    });
});
</script>
