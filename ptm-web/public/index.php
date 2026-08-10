<?php

// === FORCE PROJECT TEMP DIR (very early) ===
$tempDir = __DIR__ . "/../storage/temp";
if (!is_dir($tempDir)) {
    @mkdir($tempDir, 0775, true);
}
putenv("TMPDIR=$tempDir");
ini_set("sys_temp_dir", $tempDir);

// Force compiled views into our temp dir
$viewCompiled = $tempDir . "/views";
if (!is_dir($viewCompiled)) {
    @mkdir($viewCompiled, 0775, true);
}

define("LARAVEL_START", microtime(true));

if (file_exists($maintenance = __DIR__."/../storage/framework/maintenance.php")) {
    require $maintenance;
}

require __DIR__."/../vendor/autoload.php";

/** @var \Illuminate\Foundation\Application $app */
$app = require_once __DIR__."/../bootstrap/app.php";

// Force the compiled path very early
$app->afterBootstrapping(\Illuminate\Foundation\Bootstrap\LoadConfiguration::class, function ($app) use ($viewCompiled) {
    $app["config"]->set("view.compiled", $viewCompiled);
});

try {
    $app->handleRequest(\Illuminate\Http\Request::capture());
} catch (\Throwable $e) {
    // Print the REAL error instead of letting the renderer fail
    header("Content-Type: text/plain; charset=utf-8");
    echo "=== REAL EXCEPTION (bypassing renderer) ===\n";
    echo $e->getMessage() . "\n\n";
    echo $e->getTraceAsString();
    exit(1);
}
