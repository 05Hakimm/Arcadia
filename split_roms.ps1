$limit = 90 * 1024 * 1024 # 90MB
$files = Get-ChildItem "roms/nds/*.nds"

foreach ($file in $files) {
    if ($file.Length -gt $limit) {
        Write-Host "Splitting $($file.Name)..."
        $reader = [System.IO.File]::OpenRead($file.FullName)
        $buffer = New-Object byte[] $limit
        $partNumber = 1

        while ($reader.Position -lt $reader.Length) {
            $bytesRead = $reader.Read($buffer, 0, $limit)
            $partName = "$($file.FullName).$($partNumber.ToString('000'))"
            $writer = [System.IO.File]::Create($partName)
            $writer.Write($buffer, 0, $bytesRead)
            $writer.Close()
            $partNumber++
        }
        $reader.Close()
        
        # Remove original file to avoid git error, but maybe keep it for now? 
        # No, we must remove it or git add will fail.
        Remove-Item $file.FullName
        Write-Host "Split complete. Original removed."
    }
}
