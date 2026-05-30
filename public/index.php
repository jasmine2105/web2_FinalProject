<?php

ini_set('display_errors', '1');
error_reporting(E_ALL);

set_exception_handler(function (\Throwable $e) {
    http_response_code(500);
    echo '<pre style="background:#1e1e1e;color:#f8f8f2;padding:2rem;font-size:14px;">';
    echo '<b>Error:</b> ' . htmlspecialchars($e->getMessage()) . "\n\n";
    echo '<b>File:</b> '  . $e->getFile() . ':' . $e->getLine() . "\n\n";
    echo '<b>Trace:</b>'  . "\n" . htmlspecialchars($e->getTraceAsString());
    echo '</pre>';
});

require_once __DIR__ . '/../vendor/autoload.php';

$app = new \Core\Application();

$app->use(\App\Middleware\TrimStrings::class);

$app->container->singleton(\PDO::class, function () {
    $config    = require __DIR__ . '/../config/database.php';
    $default   = $config['default'];
    $driverCfg = $config['connections'][$default];

    $pdo = \Core\Database\Connection::create($driverCfg['driver'], $driverCfg);

    $pdo->exec("CREATE TABLE IF NOT EXISTS projects (
        id          INTEGER PRIMARY KEY AUTOINCREMENT,
        name        TEXT    NOT NULL,
        description TEXT,
        start_date  TEXT,
        end_date    TEXT,
        created_at  TEXT    DEFAULT (datetime('now'))
    )");

    $pdo->exec("CREATE TABLE IF NOT EXISTS tasks (
        id         INTEGER PRIMARY KEY AUTOINCREMENT,
        project_id INTEGER,
        title      TEXT    NOT NULL,
        status     TEXT    NOT NULL DEFAULT 'pending',
        due_date   TEXT
    )");

    return $pdo;
});

$router = $app->router;

require_once __DIR__ . '/../routes/web.php';

$request  = \Core\Http\Request::createFromGlobals();
$response = $app->handle($request);
$response->send();
