# Define la ruta al ejecutable de Acrobat y al archivo PDF que deseas imprimir
$acrobatPath = "C:\Program Files\Adobe\Acrobat DC\Acrobat\Acrobat.exe"
$pdfPath = "C:\xampp\htdocs\cpms-complete\public\salida.pdf"

# Construye el comando de impresión
$printCommand = "& `"$acrobatPath`" /t `"$pdfPath`""

# Ejecuta el comando de impresión
Invoke-Expression $printCommand

# Espera un momento para permitir que el documento se imprima
Start-Sleep -Seconds 5

# Cierra Acrobat
Get-Process Acrobat | ForEach-Object { $_.CloseMainWindow() } | Out-Null
