import sys
import pandas as pd
from sqlalchemy import create_engine

def main():
    try:
        # Leer argumentos de la línea de comandos
        file_path = sys.argv[1]
        db_user = sys.argv[2]
        db_password = sys.argv[3]
        db_host = sys.argv[4]
        db_port = sys.argv[5]
        db_name = sys.argv[6]

        # Leer el archivo CSV
        df = pd.read_csv(f'C:/Users/angel/OneDrive/Escritorio/cpms-complete/storage/app/{file_path}')
        print(f"Archivo CSV leído correctamente: {file_path}")

        # Crear la conexión a la base de datos MySQL
        engine = create_engine(f'mysql+mysqlconnector://{db_user}:{db_password}@{db_host}:{db_port}/{db_name}')
        print(f"Conexión a la base de datos MySQL establecida: {db_host}:{db_port}/{db_name}")

        # Obtener el nombre del archivo sin extensión para usarlo como nombre de la tabla
        table_name = file_path.split('/')[-1].split('.')[0]
        print(f"Nombre de la tabla a crear: {table_name}")

        # Volcar los datos del DataFrame en la tabla de la base de datos
        df.to_sql(table_name, engine, index=False, if_exists='replace')
        print(f"Datos importados a la tabla '{table_name}' en la base de datos MySQL.")

    except Exception as e:
        print(f"Error: {e}")

if __name__ == "__main__":
    main()
