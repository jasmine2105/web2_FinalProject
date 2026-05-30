<?php ob_start(); ?>

<div style="display:flex; justify-content:space-between; align-items:center;">
    <h1>Projects</h1>
    <a href="/projects/create">+ New Project</a>
</div>

<?php if (empty($projects)): ?>
    <p>No projects yet. <a href="/projects/create">Create one</a>.</p>
<?php else: ?>
    <div class="card-list">
        <?php foreach ($projects as $project): ?>
            <div class="card">
                <div class="card-header">
                    <span class="card-title">
                        <a href="/projects/<?= (int) $project['id'] ?>">
                            <?= htmlspecialchars($project['name']) ?>
                        </a>
                    </span>
                    <div class="actions">
                        <a href="/projects/<?= (int) $project['id'] ?>">View</a>
                        <a href="/projects/<?= (int) $project['id'] ?>/edit">Edit</a>
                        <form action="/projects/<?= (int) $project['id'] ?>/delete" method="POST" style="display:inline;">
                            <button type="submit"
                                    onclick="return confirm('Delete this project? Its tasks will be deleted too.')">Delete</button>
                        </form>
                    </div>
                </div>
                <?php if (!empty($project['description'])): ?>
                    <p class="card-desc"><?= htmlspecialchars($project['description']) ?></p>
                <?php endif; ?>
                <?php if (!empty($project['start_date']) || !empty($project['end_date'])): ?>
                    <div class="card-meta">
                        <?= htmlspecialchars($project['start_date'] ?? '') ?>
                        <?php if (!empty($project['start_date']) && !empty($project['end_date'])): ?> &rarr; <?php endif; ?>
                        <?= htmlspecialchars($project['end_date'] ?? '') ?>
                    </div>
                <?php endif; ?>
            </div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<?php
$content = ob_get_clean();
require __DIR__ . '/../layout.php';
?>
