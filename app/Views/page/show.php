<div class="container" style="background: var(--secondary-bg); padding: 30px; border-radius: 8px;">
    <h1 style="margin-bottom: 20px; border-bottom: 1px solid var(--border-color); padding-bottom: 15px;">
        <?= htmlspecialchars($page['title']) ?>
    </h1>
    <div class="page-content" style="line-height: 1.8;">
        <?= $page['content'] ?>
    </div>
</div>
