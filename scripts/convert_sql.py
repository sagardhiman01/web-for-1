import re

def convert_mysql_to_pg(mysql_file, pg_file):
    with open(mysql_file, 'r', encoding='utf-8') as f:
        content = f.read()

    # -------------------------------------------------------
    # 1. Remove MySQL conditional comments /*!...*/
    # -------------------------------------------------------
    content = re.sub(r'/\*!.*?\*/\s*;?', '', content, flags=re.DOTALL)

    # -------------------------------------------------------
    # 2. Remove MySQL session setters
    # -------------------------------------------------------
    content = re.sub(r'SET SQL_MODE\s*=\s*[^;]+;', '', content)
    content = re.sub(r'SET time_zone\s*=\s*[^;]+;', '', content)
    content = re.sub(r'SET NAMES\s+[^;]+;', '', content)

    # -------------------------------------------------------
    # 3. Remove ALL ALTER TABLE ... MODIFY blocks (AUTO_INCREMENT)
    # -------------------------------------------------------
    content = re.sub(
        r'ALTER TABLE\s+`?\w+`?\s*\n?\s*MODIFY\s+[^;]+;',
        '',
        content,
        flags=re.MULTILINE | re.DOTALL
    )

    # -------------------------------------------------------
    # 4. Remove ALTER TABLE ... ADD PRIMARY KEY blocks
    # -------------------------------------------------------
    content = re.sub(
        r'ALTER TABLE\s+`?\w+`?\s*\n?\s*ADD\s+PRIMARY KEY\s*\([^)]+\)\s*;',
        '',
        content,
        flags=re.MULTILINE | re.DOTALL
    )

    # -------------------------------------------------------
    # 5. Remove ALTER TABLE ... ADD KEY/INDEX blocks
    # -------------------------------------------------------
    content = re.sub(
        r'ALTER TABLE\s+`?\w+`?\s*\n?\s*ADD\s+(UNIQUE\s+)?KEY\s+[^;]+;',
        '',
        content,
        flags=re.MULTILINE | re.DOTALL
    )

    # -------------------------------------------------------
    # 6. Remove KEY/INDEX definitions inside CREATE TABLE
    # -------------------------------------------------------
    content = re.sub(r'^\s*(UNIQUE\s+)?KEY\s+`?\w+`?\s*\([^)]+\)\s*,?\s*$', '', content, flags=re.MULTILINE)
    content = re.sub(r'^\s*PRIMARY KEY\s*\([^)]+\)\s*,?\s*$', '', content, flags=re.MULTILINE)

    # -------------------------------------------------------
    # 7. Remove MySQL column COMMENT syntax
    #    e.g.  status tinyint DEFAULT 0 COMMENT '1=>active'
    # -------------------------------------------------------
    content = re.sub(r"\s+COMMENT\s+'(?:[^'\\]|\\.)*'", '', content)

    # -------------------------------------------------------
    # 8. Remove backtick quoting
    # -------------------------------------------------------
    content = content.replace('`', '')

    # -------------------------------------------------------
    # 9. Data type conversions
    # -------------------------------------------------------
    content = re.sub(r'bigint\(\d+\)\s+UNSIGNED', 'bigint', content, flags=re.IGNORECASE)
    content = re.sub(r'int\(\d+\)\s+UNSIGNED', 'integer', content, flags=re.IGNORECASE)
    content = re.sub(r'bigint\(\d+\)', 'bigint', content, flags=re.IGNORECASE)
    content = re.sub(r'int\(\d+\)', 'integer', content, flags=re.IGNORECASE)
    content = re.sub(r'tinyint\(\d+\)', 'smallint', content, flags=re.IGNORECASE)
    content = re.sub(r'\blongtext\b', 'text', content, flags=re.IGNORECASE)
    content = re.sub(r'\bmediumtext\b', 'text', content, flags=re.IGNORECASE)
    content = re.sub(r'\bdatetime\b', 'timestamp', content, flags=re.IGNORECASE)
    # Fix leftover from previous bad runs
    content = re.sub(r'\btinyinteger\b', 'smallint', content, flags=re.IGNORECASE)

    # -------------------------------------------------------
    # 10. Remove MySQL charset/collation options
    # -------------------------------------------------------
    content = re.sub(r'\s+COLLATE\s+\S+', '', content, flags=re.IGNORECASE)
    content = re.sub(r'\s+CHARACTER SET\s+\S+', '', content, flags=re.IGNORECASE)

    # -------------------------------------------------------
    # 11. Remove ENGINE=InnoDB and AUTO_INCREMENT from CREATE TABLE end
    # -------------------------------------------------------
    content = re.sub(r'\)\s*ENGINE=InnoDB[^;]*;', ');', content, flags=re.IGNORECASE | re.DOTALL)
    content = re.sub(r',\s*AUTO_INCREMENT=\d+', '', content, flags=re.IGNORECASE)
    content = re.sub(r'\bAUTO_INCREMENT\b', '', content, flags=re.IGNORECASE)

    # -------------------------------------------------------
    # 12. Fix common date defaults
    # -------------------------------------------------------
    content = content.replace("'0000-00-00 00:00:00'", "'1970-01-01 00:00:00'")
    content = content.replace("'0000-00-00'", "'1970-01-01'")

    # -------------------------------------------------------
    # 13. Fix transaction markers
    # -------------------------------------------------------
    content = re.sub(r'\bSTART TRANSACTION\b', 'BEGIN', content, flags=re.IGNORECASE)

    # -------------------------------------------------------
    # 14. Remove inline SQL -- comments
    # -------------------------------------------------------
    content = re.sub(r'--[^\n]*', '', content)

    # -------------------------------------------------------
    # 15. Clean up trailing commas before closing parenthesis
    # -------------------------------------------------------
    content = re.sub(r',\s*\)', ')', content)

    # -------------------------------------------------------
    # 16. Clean up multiple blank lines
    # -------------------------------------------------------
    content = re.sub(r'\n{3,}', '\n\n', content)

    # -------------------------------------------------------
    # 17. Add psql header to suppress metacommand errors
    #     \set ON_ERROR_STOP off  -> psql keeps going on errors
    #     SET standard_conforming_strings = ON -> handle backslashes in strings
    # -------------------------------------------------------
    header = (
        "\\set ON_ERROR_STOP off\n"
        "SET standard_conforming_strings = ON;\n\n"
    )
    content = header + content

    with open(pg_file, 'w', encoding='utf-8') as f:
        f.write(content)

if __name__ == "__main__":
    convert_mysql_to_pg('install/database.sql', 'install/database_pg.sql')
    print("Conversion complete: install/database_pg.sql")
