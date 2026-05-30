<?php ob_start(); ?>

<h1>Edit Task</h1>

<form action="/tasks/<?= (int) $task['id'] ?>/update" method="POST">

    <div class="form-group">
        <label for="title">Title *</label>
        <div class="input-row">
            <input type="text" name="title" id="title"
                   value="<?= htmlspecialchars($task['title']) ?>">
            <?php if (isset($errors['title'])): ?>
                <span class="field-error"><?= htmlspecialchars($errors['title']) ?></span>
            <?php endif; ?>
        </div>
    </div>

    <div class="form-group">
        <label for="project_id">Project *</label>
        <div class="input-row">
            <select name="project_id" id="project_id">
                <option value="">— Select a Project —</option>
                <?php foreach ($projects as $project): ?>
                    <?php $isSelected = (string) ($task['project_id'] ?? '') === (string) $project['id'] ? 'selected' : ''; ?>
                    <option value="<?= (int) $project['id'] ?>" <?= $isSelected ?>>
                        <?= htmlspecialchars($project['name']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
            <?php if (isset($errors['project_id'])): ?>
                <span class="field-error"><?= htmlspecialchars($errors['project_id']) ?></span>
            <?php endif; ?>
        </div>
        <?php if (empty($projects)): ?>
            <p class="hint">No projects exist yet. <a href="/projects/create">Create a project</a> first.</p>
        <?php endif; ?>
    </div>

    <div class="form-group">
        <label for="due_date">Due Date *</label>
        <div class="input-row">
            <input type="date" name="due_date" id="due_date"
                   value="<?= htmlspecialchars($task['due_date'] ?? '') ?>">
            <?php if (isset($errors['due_date'])): ?>
                <span class="field-error"><?= htmlspecialchars($errors['due_date']) ?></span>
            <?php endif; ?>
        </div>
    </div>

    <div class="form-group">
        <label for="status">Status *</label>
        <div class="input-row">
            <select name="status" id="status">
                <?php foreach (['pending' => 'Pending', 'in_progress' => 'In Progress', 'completed' => 'Completed'] as $val => $label): ?>
                    <?php $isSelected = $task['status'] === $val ? 'selected' : ''; ?>
                    <option value="<?= $val ?>" <?= $isSelected ?>>
                        <?= $label ?>
                    </option>
                <?php endforeach; ?>
            </select>
            <?php if (isset($errors['status'])): ?>
                <span class="field-error"><?= htmlspecialchars($errors['status']) ?></span>
            <?php endif; ?>
        </div>
    </div>

    <button type="submit">Update Task</button>
    &nbsp;<a href="/tasks">Cancel</a>

</form>

<?php
$content = ob_get_clean();
require __DIR__ . '/../layout.php';
?>
