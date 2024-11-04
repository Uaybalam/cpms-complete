$sumatraPath = "C:\Program Files\SumatraPDF\SumatraPDF.exe"
$pdfPath = "C:\xampp\htdocs\cpms-complete\public\entrada.pdf"
$printerName = "EPSON TM-T(203dpi) Receipt6"

# Agrega opciones para ajustar la escala y forzar que el PDF ocupe todo el tamaño del ticket
Start-Process -FilePath $sumatraPath -ArgumentList '-print-to "', $printerName, '" "', $pdfPath, '" -print-settings "shrink" ' -NoNewWindow -Wait
