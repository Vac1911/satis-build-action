<?php

require_once __DIR__ . '/src/autoload.php';


$config = (new ConfigFactory())->create(getenv());
print_r($config);

removeDir(__DIR__ . '/builder');

$repos = array_map(fn($pkg) => $config->getPackageRepo($pkg), $config->getPackageList());

execNormal('composer create-project composer/satis builder --stability=dev --remove-vcs -n -q');

$satisConfig = [
    "name"         => "qis/repository",
    "homepage"     => "https://packages.quasarwebdev.com/",
    "repositories" => $repos,
    "require-all"  => true
];

file_put_contents('satis.json', json_encode($satisConfig));

execVerbose('php builder/bin/satis build satis.json dist');

execNormal('tar -czf dist.tar.gz dist');

execVerbose('gh auth status');

removeDir(__DIR__ . '/builder');

function execVerbose(string $commandLine): void
{
    note('Running: ' . $commandLine);
    exec($commandLine, $output);
    echo implode(PHP_EOL, $output);
}
function execNormal(string $commandLine): void
{
    note('Running: ' . $commandLine);
    exec($commandLine, $output, $code);
}


function note(string $message): void
{
    echo PHP_EOL . PHP_EOL . "\033[0;33m[NOTE] " . $message . "\033[0m" . PHP_EOL . PHP_EOL;
}

function error(string $message): void
{
    echo PHP_EOL . PHP_EOL . "\033[0;31m[ERROR] " . $message . "\033[0m" . PHP_EOL . PHP_EOL;
}

function removeDir($dir): void
{
    if (empty($dir) || !is_dir($dir)) return;

    $files = array_diff(scandir($dir), array('.', '..'));
    foreach ($files as $file) {
        $path = $dir . '/' . $file;
        if (!is_dir($path)) {
            unlink($path);
        } else {
            removeDir($path);
        }
    }
    rmdir($dir);
}