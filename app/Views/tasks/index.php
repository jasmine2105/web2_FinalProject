<?php ob_start(); ?>

<div style="display:flex; justify-content:space-between; align-items:center;">
    <h1>Tasks</h1>
    <a href="/tasks/create">+ New Task</a>
</div>

<?php if (empty($tasks)): ?>
    <p>No tasks yet. <a href="/tasks/create">Create one</a>.</p>
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
                    <?php if (!empty($task['project_id']) && isset($projectsById[$task['project_id']])): ?>
                        Project: <a href="/projects/<?= (int) $task['project_id'] ?>">
                            <?= htmlspecialchars($projectsById[$task['project_id']]['name']) ?>
                        </a>
                    <?php else: ?>
                        No project assigned
                    <?php endif; ?>
                    &nbsp;&middot;&nbsp;
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
