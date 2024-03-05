import qrcode
import sys

def generar_qr(texto, nombre_archivo):
    qr = qrcode.QRCode(
        version=1,
        error_correction=qrcode.constants.ERROR_CORRECT_L,
        box_size=10,
        border=4,
    )
    qr.add_data(texto)
    qr.make(fit=True)

    img = qr.make_image(fill_color="black", back_color="white")
    img.save(nombre_archivo)


texto = sys.argv[1]
nombre_archivo = "../codigo_qr.png"
generar_qr(texto, nombre_archivo)
print(f"Código QR generado para: {texto} en {nombre_archivo}")
