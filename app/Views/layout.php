<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Task Manager</title>
    <style>
        body { font-family: sans-serif; max-width: 960px; margin: 2rem auto; padding: 0 1rem; }
        nav { border-bottom: 1px solid #000; padding-bottom: 0.5rem; margin-bottom: 1.5rem; }
        nav a { margin-right: 1.5rem; }
        h1 { margin-top: 0; }
        .card-list { display: flex; flex-direction: column; gap: 0.6rem; margin-top: 0.75rem; }
        .card { border: 1px solid #000; padding: 0.75rem 1rem; }
        .card-header { display: flex; justify-content: space-between; align-items: center; gap: 1rem; }
        .card-title { font-weight: bold; font-size: 1rem; }
        .card-meta { font-size: 0.875rem; margin-top: 0.3rem; color: #444; }
        .card-desc { margin-top: 0.3rem; font-size: 0.9rem; }
        .badge { font-size: 0.8rem; padding: 0.15rem 0.5rem; border: 1px solid #000; white-space: nowrap; }
        .form-group { margin-bottom: 0.8rem; }
        label { display: block; margin-bottom: 0.2rem; font-weight: bold; }
        .input-row { display: flex; align-items: center; gap: 0.6rem; }
        .input-row input, .input-row select, .input-row textarea { flex: 1; min-width: 0; }
        input[type="text"], input[type="date"], select, textarea {
            padding: 0.3rem; border: 1px solid #000; font-size: 1rem; box-sizing: border-box; width: 100%;
        }
        textarea { width: 100%; }
        .field-error { color: #c00; font-size: 0.85rem; font-style: italic; white-space: nowrap; }
        button { padding: 0.35rem 0.75rem; cursor: pointer; font-size: 1rem; }
        .actions { display: flex; gap: 0.5rem; align-items: center; }
        .hint { font-size: 0.85rem; margin-top: 0.2rem; }

        #error-modal {
            display: none; position: fixed;
            top: 0; left: 0; width: 100%; height: 100%;
            background: rgba(0,0,0,0.5); z-index: 9999;
            justify-content: center; align-items: center;
        }
        #error-modal.visible { display: flex; }
        #error-modal-box {
            background: #fff; border: 2px solid #c00; border-radius: 6px;
            padding: 2rem; max-width: 520px; width: 90%;
        }
        #error-modal-box h2 { margin-top: 0; color: #c00; }
        #error-modal-box button { background: #c00; color: #fff; border: none; padding: 0.4rem 1rem; cursor: pointer; }
    </style>
</head>
<body>
    <nav>
        <strong>Task Manager</strong>&nbsp;&nbsp;
        <a href="/projects">Projects</a>
        <a href="/tasks">Tasks</a>
    </nav>

    <?= $content ?? '' ?>

    <div id="error-modal" role="dialog" aria-modal="true">
        <div id="error-modal-box">
            <h2>&#9888; Server Error</h2>
            <p id="error-modal-message"></p>
            <button onclick="document.getElementById('error-modal').classList.remove('visible')">Close</button>
        </div>
    </div>

    <?php if (!empty($serverError)): ?>
    <script>
    (function() {
        var msg = <?= json_encode($serverError) ?>;
        document.getElementById('error-modal-message').textContent = msg;
        document.getElementById('error-modal').classList.add('visible');
    })();
    </script>
    <?php endif; ?>

    <script>
    function showErrorModal(msg) {
        document.getElementById('error-modal-message').textContent = msg;
        document.getElementById('error-modal').classList.add('visible');
    }
    </script>
</body>
</html>
