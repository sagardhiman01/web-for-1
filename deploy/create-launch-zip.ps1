param(
    [string]$OutputZip = "core-asset-investing-launch.zip"
)

$projectRoot = Split-Path -Parent $PSScriptRoot
$zipPath = Join-Path $PSScriptRoot $OutputZip

if (Test-Path $zipPath) {
    Remove-Item $zipPath -Force
}

$items = @(
    (Join-Path $projectRoot '.htaccess')
    (Join-Path $projectRoot 'index.php')
    (Join-Path $projectRoot 'assets')
    (Join-Path $projectRoot 'core')
    (Join-Path $projectRoot 'install')
    (Join-Path $projectRoot 'deploy/HOSTINGER_LAUNCH_CHECKLIST.md')
    (Join-Path $projectRoot 'core/.env.hostinger.example')
)

Compress-Archive -Path $items -DestinationPath $zipPath -Force
Write-Output "Created: $zipPath"
