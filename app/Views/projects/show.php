<?php ob_start(); ?>

<p><a href="/projects">&larr; All Projects</a></p>

<div style="display:flex; justify-content:space-between; align-items:center;">
    <h1><?= htmlspecialchars($project['name']) ?></h1>
    <div class="actions">
        <a href="/projects/<?= (int) $project['id'] ?>/edit">Edit Project</a>
    </div>
</div>

<?php if (!empty($project['description'])): ?>
    <p><?= htmlspecialchars($project['description']) ?></p>
<?php endif; ?>

<?php if (!empty($project['start_date']) || !empty($project['end_date'])): ?>
    <p style="font-size:0.875rem; color:#444;">
        <?= htmlspecialchars($project['start_date'] ?? '') ?>
        <?php if (!empty($project['start_date']) && !empty($project['end_date'])): ?> &rarr; <?php endif; ?>
        <?= htmlspecialchars($project['end_date'] ?? '') ?>
    </p>
<?php endif; ?>

<h2>Tasks in this Project</h2>

<?php if (empty($tasks)): ?>
    <p>No tasks in this project yet. <a href="/tasks/create">Add a task</a>.</p>
<?php else: ?>
    <div class="card-list">
        <?php foreach ($tasks as $task): ?>
            <div class="card">
                <div class="card-header">
                    <span class="card-title"><?= htmlspecialchars($task['title']) ?></span>
                    <div style="display:flex; align-items:center; gap:0.75rem;">
                        <span class="badge"><?= htmlspecialchars(ucwords(str_replace('_', ' ', $task['status']))) ?></span>
                        <div class="actions">
                            <a href="/tasks/<?= (int) $task['id'] ?>/edit">Edit</a>
                            <form action="/tasks/<?= (int) $task['id'] ?>/delete" method="POST" style="display:inline;">
                                <button type="submit" onclick="return confirm('Delete this task?')">Delete</button>
                            </form>
                        </div>
                    </div>
                </div>
                <div class="card-meta">
                    Due: <?= $task['due_date'] ? htmlspecialchars($task['due_date']) : '&mdash;' ?>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<?php
$content = ob_get_clean();
require __DIR__ . '/../layout.php';
?>
