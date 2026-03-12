#!/usr/bin/env php
<?php

declare(strict_types=1);

const GUARD_PREFIX = '[git-access-guard] ';

$mode = $argv[1] ?? null;

if (!in_array($mode, ['pre-commit', 'pre-push'], true)) {
    fail('Usage: php scripts/git-access-guard.php <pre-commit|pre-push> [hook-args...]', 64);
}

$authorizedUsername = envValue('GIT_GUARD_AUTHORIZED_USERNAME')
    ?? gitConfig('guard.authorizedUsername')
    ?? 'andhyabernaz';
$authorizedEmail = envValue('GIT_GUARD_AUTHORIZED_EMAIL')
    ?? gitConfig('guard.authorizedEmail')
    ?? 'andhyabernaz@gmail.com';
$authorizedRemoteOwner = envValue('GIT_GUARD_AUTHORIZED_REMOTE_OWNER')
    ?? gitConfig('guard.authorizedRemoteOwner')
    ?? $authorizedUsername;
$authorizedRemoteHost = envValue('GIT_GUARD_AUTHORIZED_REMOTE_HOST')
    ?? gitConfig('guard.authorizedRemoteHost')
    ?? 'github.com';
$authorizedCredentialUsername = envValue('GIT_GUARD_AUTHORIZED_CREDENTIAL_USERNAME')
    ?? gitConfig('guard.authorizedCredentialUsername')
    ?? $authorizedUsername;

assertIdentity($authorizedUsername, $authorizedEmail);

if ($mode === 'pre-push') {
    $remoteName = $argv[2] ?? 'origin';
    $remoteUrl = $argv[3] ?? gitRemotePushUrl($remoteName);

    if ($remoteUrl === null || $remoteUrl === '') {
        fail("Tidak dapat membaca push URL untuk remote '{$remoteName}'.");
    }

    assertRemote($remoteUrl, $authorizedRemoteHost, $authorizedRemoteOwner);
    assertCredentialUsername($remoteUrl, $authorizedCredentialUsername);
}

exit(0);

function assertIdentity(string $authorizedUsername, string $authorizedEmail): void
{
    $currentUsername = gitConfig('user.name');
    $currentEmail = gitConfig('user.email');

    if ($currentUsername !== $authorizedUsername) {
        fail("Commit/push hanya diizinkan untuk git user.name '{$authorizedUsername}'. User aktif: '" . ($currentUsername ?? '(kosong)') . "'.");
    }

    if ($currentEmail !== $authorizedEmail) {
        fail("Commit/push hanya diizinkan untuk git user.email '{$authorizedEmail}'. Email aktif: '" . ($currentEmail ?? '(kosong)') . "'.");
    }
}

function assertRemote(string $remoteUrl, string $authorizedHost, string $authorizedOwner): void
{
    $remoteHost = extractRemoteHost($remoteUrl);
    $remoteOwner = extractRemoteOwner($remoteUrl);

    if ($remoteHost !== $authorizedHost) {
        fail("Push hanya diizinkan ke host '{$authorizedHost}'. Remote aktif: '{$remoteHost}' ({$remoteUrl}).");
    }

    if ($remoteOwner !== $authorizedOwner) {
        fail("Push hanya diizinkan ke repository milik '{$authorizedOwner}'. Remote aktif: '{$remoteUrl}'.");
    }
}

function assertCredentialUsername(string $remoteUrl, string $authorizedCredentialUsername): void
{
    $remoteHost = extractRemoteHost($remoteUrl);
    $signals = [];

    $configuredCredentialUsername = resolveConfiguredCredentialUsername($remoteHost);
    if ($configuredCredentialUsername !== null) {
        $signals['git-config credential username'] = $configuredCredentialUsername;
    }

    $ghAuthenticatedUser = resolveGhAuthenticatedUser($remoteHost);
    if ($ghAuthenticatedUser !== null) {
        $signals['gh auth status'] = $ghAuthenticatedUser;
    }

    if ($signals === []) {
        fail(
            "Tidak menemukan sinyal credential untuk host '{$remoteHost}'. " .
            "Set credential.https://{$remoteHost}.username ke '{$authorizedCredentialUsername}' atau login gh CLI dengan akun tersebut."
        );
    }

    foreach ($signals as $source => $username) {
        if ($username !== $authorizedCredentialUsername) {
            fail("Validasi credential dari {$source} mendeteksi akun '{$username}'. Hanya '{$authorizedCredentialUsername}' yang diizinkan push.");
        }
    }
}

function resolveConfiguredCredentialUsername(string $remoteHost): ?string
{
    $candidates = [
        envValue('GIT_GUARD_CREDENTIAL_USERNAME'),
        gitConfig("credential.https://{$remoteHost}.username"),
        gitConfig('credential.username'),
    ];

    foreach ($candidates as $candidate) {
        if ($candidate !== null && $candidate !== '') {
            return $candidate;
        }
    }

    return null;
}

function resolveGhAuthenticatedUser(string $remoteHost): ?string
{
    if (!commandExists('gh')) {
        return null;
    }

    $result = runCommand(['gh', 'auth', 'status', '--hostname', $remoteHost], false);
    if ($result['exit'] !== 0) {
        return null;
    }

    $output = $result['stdout'] . "\n" . $result['stderr'];

    if (preg_match('/account\s+([A-Za-z0-9-]+)/i', $output, $matches) === 1) {
        return $matches[1];
    }

    return null;
}

function gitRemotePushUrl(string $remoteName): ?string
{
    $result = runGit(['remote', 'get-url', '--push', $remoteName], false);
    if ($result['exit'] !== 0) {
        return null;
    }

    return trim($result['stdout']);
}

function extractRemoteHost(string $remoteUrl): string
{
    if (preg_match('#^[a-z]+://([^/@]+@)?([^/:]+)#i', $remoteUrl, $matches) === 1) {
        return strtolower($matches[2]);
    }

    if (preg_match('#^git@([^:]+):#i', $remoteUrl, $matches) === 1) {
        return strtolower($matches[1]);
    }

    return '';
}

function extractRemoteOwner(string $remoteUrl): string
{
    $path = '';

    if (preg_match('#^[a-z]+://[^/]+/(.+)$#i', $remoteUrl, $matches) === 1) {
        $path = $matches[1];
    } elseif (preg_match('#^git@[^:]+:(.+)$#i', $remoteUrl, $matches) === 1) {
        $path = $matches[1];
    }

    $path = ltrim($path, '/');
    if ($path === '') {
        return '';
    }

    $segments = explode('/', $path);
    return strtolower($segments[0] ?? '');
}

function gitConfig(string $key): ?string
{
    $result = runGit(['config', '--get', $key], false);
    if ($result['exit'] !== 0) {
        return null;
    }

    $value = trim($result['stdout']);
    return $value === '' ? null : $value;
}

function runGit(array $arguments, bool $mustSucceed = true): array
{
    return runCommand(array_merge(['git'], $arguments), $mustSucceed);
}

function runCommand(array $command, bool $mustSucceed = true): array
{
    $escaped = implode(' ', array_map('escapeShellArgument', $command));
    $descriptorSpec = [
        1 => ['pipe', 'w'],
        2 => ['pipe', 'w'],
    ];

    $process = proc_open($escaped, $descriptorSpec, $pipes, getcwd() ?: null);
    if (!is_resource($process)) {
        fail('Tidak dapat menjalankan command: ' . $escaped);
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
        fail("Command gagal: {$escaped}\n{$result['stderr']}");
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

function commandExists(string $command): bool
{
    $check = DIRECTORY_SEPARATOR === '\\'
        ? ['where', $command]
        : ['command', '-v', $command];

    $result = runCommand($check, false);
    return $result['exit'] === 0;
}

function envValue(string $key): ?string
{
    $value = getenv($key);
    if ($value === false || $value === '') {
        return null;
    }

    return $value;
}

function fail(string $message, int $exitCode = 1): void
{
    fwrite(STDERR, GUARD_PREFIX . $message . PHP_EOL);
    exit($exitCode);
}
