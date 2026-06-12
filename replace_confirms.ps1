$files = Get-ChildItem -Path resources/views -Recurse -Filter *.blade.php
foreach ($file in $files) {
    $content = Get-Content $file.FullName -Raw
    $newContent = [regex]::Replace($content, 'onsubmit="return confirm\(''(.+?)''\);?"', 'onsubmit="return confirmAction(event, ''$1'');"')
    if ($content -ne $newContent) {
        Set-Content -Path $file.FullName -Value $newContent -NoNewline
        Write-Host "Updated $($file.FullName)"
    }
}
