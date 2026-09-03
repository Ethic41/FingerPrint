$ErrorActionPreference = "Continue"

function Report($Name, $Ok, $Details) {
    $status = if ($Ok) { "OK" } else { "FAIL" }
    $color = if ($Ok) { "Green" } else { "Red" }
    Write-Host ("[{0}] {1}: {2}" -f $status, $Name, $Details) -ForegroundColor $color
}

$windows = Get-CimInstance Win32_OperatingSystem
Report "Windows" ([int]$windows.BuildNumber -ge 19045) ("{0} build {1}" -f $windows.Caption, $windows.BuildNumber)

$virtualization = Get-CimInstance Win32_Processor | Select-Object -First 1 -ExpandProperty VirtualizationFirmwareEnabled
Report "Firmware virtualization" ([bool]$virtualization) (if ($virtualization) { "enabled" } else { "enable it in BIOS/UEFI" })

$wsl = & wsl.exe --version 2>&1
Report "WSL" ($LASTEXITCODE -eq 0) ($wsl -join " ")

$dockerVersion = & docker version --format '{{.Server.Version}}' 2>&1
Report "Docker engine" ($LASTEXITCODE -eq 0) ($dockerVersion -join " ")

$composeVersion = & docker compose version 2>&1
Report "Docker Compose" ($LASTEXITCODE -eq 0) ($composeVersion -join " ")

$reader = Get-PnpDevice -PresentOnly -ErrorAction SilentlyContinue |
    Where-Object { $_.FriendlyName -match 'U\.are\.U|DigitalPersona|Fingerprint' }
Report "Fingerprint reader" ($null -ne $reader) (($reader | Select-Object -ExpandProperty FriendlyName) -join ", ")

try {
    $service = Invoke-WebRequest -UseBasicParsing -TimeoutSec 3 http://127.0.0.1:5555/health.php
    Report "Fingerprint service" ($service.StatusCode -eq 200) $service.Content
} catch {
    Report "Fingerprint service" $false $_.Exception.Message
}

Write-Host "Open the application diagnostics endpoint next: <application-url>/src/core/diagnostics.php"
