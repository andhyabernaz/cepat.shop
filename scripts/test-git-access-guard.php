#!/usr/bin/env php
<?php

declare(strict_types=1);

$repoRoot = realpath(__DIR__ . '/..');
if ($repoRoot === false) {
    fwrite(STDERR, "Cannot resolve repository root.\n");
    exit(1);
}

$guardScript = $repoRoot . '/scripts/git-access-guard.php';
$tempDir = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'cepat-git-guard-' . bin2hex(random_bytes(6));

mkdir($tempDir, 0777, true);

try {
    run(['git', 'init'], $tempDir);
    run(['git', 'remote', 'add', 'origin', 'https://github.com/andhyabernaz/cepat.shop.git'], $tempDir);
    run(['git', 'config', 'user.name', 'andhyabernaz'], $tempDir);
    run(['git', 'config', 'user.email', 'andhyabernaz@gmail.com'], $tempDir);
    run(['git', 'config', 'guard.authorizedUsername', 'andhyabernaz'], $tempDir);
    run(['git', 'config', 'guard.authorizedEmail', 'andhyabernaz@gmail.com'], $tempDir);
    run(['git', 'config', 'guard.authorizedRemoteOwner', 'andhyabernaz'], $tempDir);
    run(['git', 'config', 'guard.authorizedRemoteHost', 'github.com'], $tempDir);
    run(['git', 'config', 'guard.authorizedCredentialUsername', 'andhyabernaz'], $tempDir);
    run(['git', 'config', 'credential.https://github.com.username', 'andhyabernaz'], $tempDir);

    run(['php', $guardScript, 'pre-commit'], $tempDir);

    run(['git', 'config', 'user.name', 'other-user'], $tempDir);
    $blockedCommit = run(['php', $guardScript, 'pre-commit'], $tempDir, false);
    assertFailed($blockedCommit, 'Unauthorized user should not be able to commit.');

    run(['git', 'config', 'user.name', 'andhyabernaz'], $tempDir);
    run(['git', 'config', 'user.email', 'andhyabernaz@gmail.com'], $tempDir);
    run(['php', $guardScript, 'pre-push', 'origin', 'https://github.com/andhyabernaz/cepat.shop.git'], $tempDir);

    run(['git', 'config', 'credential.https://github.com.username', 'other-user'], $tempDir);
    $blockedPush = run(['php', $guardScript, 'pre-push', 'origin', 'https://github.com/andhyabernaz/cepat.shop.git'], $tempDir, false);
    assertFailed($blockedPush, 'Unauthorized credential should not be able to push.');

    echo "Git access guard test passed.\n";
    exit(0);
} finally {
    deleteDirectory($tempDir);
}

function assertFailed(array $result, string $message): void
{
    if ($result['exit'] === 0) {
        fwrite(STDERR, $message . PHP_EOL);
        exit(1);
    }
}

function run(array $command, string $cwd, bool $mustSucceed = true): array
{
    $escaped = implode(' ', array_map('escapeShellArgument', $command));
    $process = proc_open(
        $escaped,
        [
            1 => ['pipe', 'w'],
            2 => ['pipe', 'w'],
        ],
        $pipes,
        $cwd
    );

    if (!is_resource($process)) {
        throw new RuntimeException('Cannot start command: ' . $escaped);
    }

    $stdout = stream_get_contents($pipes[1]);
    fclose($pipes[1]);
    $stderr = stream_get_contents($pipes[2]);
    fclose($pipes[2]);
    $exitCode = proc_close($process);

    $result = [
        'exit' => $exitCode,
        'stdout' => trim((string) $stdout),
        'stderr' => trim((string) $stderr),
    ];

    if ($mustSucceed && $exitCode !== 0) {
        throw new RuntimeException($escaped . PHP_EOL . ($result['stderr'] ?: $result['stdout']));
    }

    return $result;
}

function escapeShellArgument(string $value): string
{
    if (DIRECTORY_SEPARATOR === '\\') {
        return '"' . str_replace('"', '\"', $value) . '"';
    }

    return escapeshellarg($value);
}

function deleteDirectory(string $path): void
{
    if (!is_dir($path)) {
        return;
    }

    $items = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($path, FilesystemIterator::SKIP_DOTS),
        RecursiveIteratorIterator::CHILD_FIRST
    );

    foreach ($items as $item) {
        if ($item->isDir()) {
            rmdir($item->getPathname());
            continue;
        }

        unlink($item->getPathname());
    }

    rmdir($path);
}
