import re

def convert_mysql_to_pg(mysql_file, pg_file):
    with open(mysql_file, 'r', encoding='utf-8') as f:
        content = f.read()

    # Remove MySQL specific headers/setters
    content = re.sub(r'SET SQL_MODE = .*;', '', content)
    content = re.sub(r'START TRANSACTION;', 'BEGIN;', content)
    content = re.sub(r'COMMIT;', 'COMMIT;', content)
    content = re.sub(r'/*!.*;', '', content)

    # Replace backticks with nothing (Postgres handles unquoted lowercase)
    content = content.replace('`', '')

    # Convert data types
    content = re.sub(r'bigint\(\d+\) UNSIGNED', 'bigint', content)
    content = re.sub(r'int\(\d+\) UNSIGNED', 'integer', content)
    content = re.sub(r'int\(\d+\)', 'integer', content)
    content = re.sub(r'tinyint\(\d+\)', 'smallint', content)
    content = re.sub(r'longtext', 'text', content)
    content = re.sub(r'datetime', 'timestamp', content)
    content = re.sub(r' COLLATE [^ ]+', '', content)
    content = re.sub(r' CHARACTER SET [^ ]+', '', content)

    # Remove ENGINE and AUTO_INCREMENT from CREATE TABLE
    content = re.sub(r'ENGINE=InnoDB.*?;', ');', content)
    content = re.sub(r'AUTO_INCREMENT=\d+', '', content)
    content = re.sub(r'AUTO_INCREMENT', '', content)

    # Fix CREATE TABLE ending (if any doubled parentheses)
    content = re.sub(r'\)\s*\)\s*;', ');', content)

    # Handle comments
    content = re.sub(r'--.*', '', content)

    # Fix some common syntax issues
    content = content.replace('0000-00-00 00:00:00', '1970-01-01 00:00:00')

    with open(pg_file, 'w', encoding='utf-8') as f:
        f.write(content)

if __name__ == "__main__":
    convert_mysql_to_pg('install/database.sql', 'install/database_pg.sql')
    print("Conversion complete: install/database_pg.sql")
