<?php ob_start(); ?>

<h1>Error 404 Not Found.</h1>

<p><?= htmlspecialchars($message ?? 'The requested resource could not be found.') ?></p>

<p><a href="/">Go back to home</a></p>

<?php
$content = ob_get_clean();
require __DIR__ . '/../layout.php';
?>
