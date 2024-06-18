## Instrucciones de Instalación y Solución de Problemas
Este repositorio contiene un proyecto que requiere la instalación de varias dependencias. Sigue las instrucciones a continuación para configurar el entorno de desarrollo correctamente y solucionar posibles errores comunes.

## Instalación de Dependencias
Python
Asegúrate de tener Python instalado en tu sistema. Puedes descargarlo desde el sitio web oficial de Python: Python.org.


pip es el sistema de gestión de paquetes de Python. Normalmente viene instalado con Python. Si no lo tienes, asegúrate de instalarlo siguiendo las instrucciones oficiales de Python.

## WeasyPrint 
es una biblioteca de Python que permite convertir HTML/CSS a PDF. Puedes instalarlo utilizando pip:

pip install weasyprint

## Qrcode
es una biblioteca de Python que permite generar códigos QR. Puedes instalarlo utilizando pip:
pip install qrcode
## Pandas y openpyxl
es una biblioteca de Python que permite generar excel. Puedes instalarlo utilizando pip:
pip install pandas openpyxl


## GObject Introspection (gobject-2.0-0)

GObject Introspection es una biblioteca que se requiere para WeasyPrint en algunos sistemas operativos. A continuación, se indican los pasos para instalarlo en diferentes sistemas:

## Debian/Ubuntu:

sudo apt update
sudo apt install libgirepository1.0-dev


## Fedora:

sudo dnf install gobject-introspection-devel


## CentOS/RHEL:

sudo yum install gobject-introspection-devel


## Arch Linux:

sudo pacman -S gobject-introspection


## macOS (con Homebrew):

brew install gobject-introspection

### Instalación de GObject Introspection en Windows

Para instalar GObject Introspection en Windows, sigue estos pasos:

1. Descarga el instalador del entorno de ejecución de GTK para Windows desde el siguiente enlace: [GTK for Windows Runtime Environment Installer](https://github.com/tschoonj/GTK-for-Windows-Runtime-Environment-Installer).

2. Ejecuta el instalador descargado y sigue las instrucciones en pantalla para completar la instalación. Asegúrate de seleccionar las opciones necesarias para instalar GObject Introspection.

3. Una vez completada la instalación, asegúrate de que la biblioteca GObject Introspection esté en tu PATH de Windows. Esto se puede verificar buscando la ubicación donde se instaló y agregándola manualmente al PATH si es necesario.

4. Después de instalar GObject Introspection, asegúrate de reiniciar tu sistema para aplicar los cambios.


Solución de Problemas Comunes

Error: "No module named 'weasyprint'"
Este error indica que WeasyPrint no está instalado correctamente. Asegúrate de haber ejecutado el comando pip install weasyprint y verifica que no hayan ocurrido errores durante la instalación.

Error: "No module named 'qrcode'"
Este error indica que Qrcode no está instalado correctamente. Asegúrate de haber ejecutado el comando pip install qrcode y verifica que no hayan ocurrido errores durante la instalación.

Error: "cannot load library 'gobject-2.0-0'"
Este error indica que falta la biblioteca GObject Introspection en tu sistema. Sigue las instrucciones de instalación proporcionadas anteriormente para instalarla correctamente.
