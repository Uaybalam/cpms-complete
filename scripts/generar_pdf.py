import sys
from weasyprint import HTML

def generar_pdf_from_stdin(output_path):
    html_content = sys.stdin.read()
    HTML(string=html_content).write_pdf(output_path)

# Llamar a la función para generar el PDF
if __name__ == "__main__":
    if len(sys.argv) != 2:
        print("Usage: generar_pdf.py output_path")
        sys.exit(1)
    output_path = sys.argv[1]
    generar_pdf_from_stdin(output_path)
