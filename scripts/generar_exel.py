import pandas as pd
import sys
import json
from openpyxl import load_workbook

def export_to_excel(json_data, filename):
    # Convertir el JSON en un DataFrame de pandas
    data = json.loads(json_data)
    df = pd.DataFrame(data)

    # Verificar si la columna E (índice 4 en pandas) existe antes de ordenar
    if df.shape[1] >= 5:  # Asegurarse de que hay al menos 5 columnas
        df = df.sort_values(by=df.columns[4], ascending=True)  # Ordenar por la columna E

    # Guardar el DataFrame en un archivo Excel
    with pd.ExcelWriter(filename, engine='openpyxl') as writer:
        df.to_excel(writer, index=False)

    # Cargar el archivo Excel y aplicar el filtro
    workbook = load_workbook(filename)
    sheet = workbook.active

    # Agregar filtro automático en las filas
    sheet.auto_filter.ref = sheet.dimensions

    # Guardar cambios
    workbook.save(filename)

if __name__ == "__main__":
    json_input = sys.stdin.read()  # Leer los datos JSON desde stdin
    output_file = sys.argv[1]  # Ruta del archivo Excel
    export_to_excel(json_input, output_file)
