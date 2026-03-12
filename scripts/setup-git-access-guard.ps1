$ErrorActionPreference = 'Stop'

$repoRoot = (Resolve-Path (Join-Path $PSScriptRoot '..')).Path
Set-Location $repoRoot

$authorizedUsername = 'andhyabernaz'
$authorizedEmail = 'andhyabernaz@gmail.com'
$authorizedRemoteHost = 'github.com'

git rev-parse --is-inside-work-tree *> $null
if ($LASTEXITCODE -ne 0) {
   throw 'Folder ini bukan Git repository.'
}

git config --local core.hooksPath .githooks
git config --local user.name $authorizedUsername
git config --local user.email $authorizedEmail
git config --local guard.authorizedUsername $authorizedUsername
git config --local guard.authorizedEmail $authorizedEmail
git config --local guard.authorizedRemoteOwner $authorizedUsername
git config --local guard.authorizedRemoteHost $authorizedRemoteHost
git config --local guard.authorizedCredentialUsername $authorizedUsername
git config --local credential.https://github.com.username $authorizedUsername

Write-Host 'Git access guard configured:' -ForegroundColor Green
Write-Host "  core.hooksPath = $(git config --local core.hooksPath)"
Write-Host "  user.name      = $(git config --local user.name)"
Write-Host "  user.email     = $(git config --local user.email)"
Write-Host "  credential     = $(git config --local credential.https://github.com.username)"
