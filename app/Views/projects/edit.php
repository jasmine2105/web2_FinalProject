<?php ob_start(); ?>

<h1>Edit Project</h1>

<form action="/projects/<?= (int) $project['id'] ?>/update" method="POST">

    <div class="form-group">
        <label for="name">Project Name *</label>
        <div class="input-row">
            <input type="text" name="name" id="name"
                   value="<?= htmlspecialchars($project['name']) ?>">
            <?php if (isset($errors['name'])): ?>
                <span class="field-error"><?= htmlspecialchars($errors['name']) ?></span>
            <?php endif; ?>
        </div>
    </div>

    <div class="form-group">
        <label for="description">Description *</label>
        <div class="input-row">
            <textarea name="description" id="description" rows="4"><?= htmlspecialchars($project['description'] ?? '') ?></textarea>
            <?php if (isset($errors['description'])): ?>
                <span class="field-error"><?= htmlspecialchars($errors['description']) ?></span>
            <?php endif; ?>
        </div>
    </div>

    <div class="form-group">
        <label for="start_date">Start Date *</label>
        <div class="input-row">
            <input type="date" name="start_date" id="start_date"
                   value="<?= htmlspecialchars($project['start_date'] ?? '') ?>">
            <?php if (isset($errors['start_date'])): ?>
                <span class="field-error"><?= htmlspecialchars($errors['start_date']) ?></span>
            <?php endif; ?>
        </div>
    </div>

    <div class="form-group">
        <label for="end_date">End Date *</label>
        <div class="input-row">
            <input type="date" name="end_date" id="end_date"
                   value="<?= htmlspecialchars($project['end_date'] ?? '') ?>">
            <?php if (isset($errors['end_date'])): ?>
                <span class="field-error"><?= htmlspecialchars($errors['end_date']) ?></span>
            <?php endif; ?>
        </div>
    </div>

    <button type="submit">Update Project</button>
    &nbsp;<a href="/projects">Cancel</a>

</form>

<?php
$content = ob_get_clean();
require __DIR__ . '/../layout.php';
?>
