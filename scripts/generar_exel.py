import pandas as pd
import sys
import json

def export_to_excel(json_data, filename):
    data = json.loads(json_data)
    df = pd.DataFrame(data)
    with pd.ExcelWriter(filename, engine='openpyxl') as writer:
        df.to_excel(writer, index=False)

if __name__ == "__main__":
    json_input = sys.stdin.read()  # Leer los datos JSON desde stdin
    output_file = sys.argv[1]  # Ruta del archivo Excel
    export_to_excel(json_input, output_file)
