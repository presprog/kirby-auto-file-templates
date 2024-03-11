<?php declare(strict_types=1);

$toolsDir = __DIR__;
$command  = $argv[1] ?? null;

if (!in_array($command, ['install', 'update'])) {
    echo "Please choose either \e[0;32minstall\e[0m or \e[0;32mupdate\e[0m" . PHP_EOL;
    return 1;
}

/** @var DirectoryIterator $tool */
foreach (new DirectoryIterator($toolsDir) as $tool) {
    if (!$tool->isDir() || $tool->isDot()) {
        continue;
    }

    chdir($tool->getPathname());
    exec(sprintf('symfony composer %s --ansi', $command));
    chdir($toolsDir);
}
