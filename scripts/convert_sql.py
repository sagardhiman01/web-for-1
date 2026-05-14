import re

def convert_mysql_to_pg(mysql_file, pg_file):
    with open(mysql_file, 'r', encoding='utf-8') as f:
        content = f.read()

    # -------------------------------------------------------
    # 1. Remove MySQL-specific header/footer blocks (/*!...*/;)
    # -------------------------------------------------------
    # Remove /*!...*/ conditional comments (multi-line too)
    content = re.sub(r'/\*!.*?\*/\s*;?', '', content, flags=re.DOTALL)

    # -------------------------------------------------------
    # 2. Remove MySQL session-level setters
    # -------------------------------------------------------
    content = re.sub(r'SET SQL_MODE\s*=\s*[^;]+;', '', content)
    content = re.sub(r'SET time_zone\s*=\s*[^;]+;', '', content)
    content = re.sub(r'SET NAMES\s+[^;]+;', '', content)

    # -------------------------------------------------------
    # 3. Remove ALL MySQL-only ALTER TABLE ... MODIFY blocks
    #    (these are used for AUTO_INCREMENT, not needed in PG)
    # -------------------------------------------------------
    # Match: ALTER TABLE `xxx` \n  MODIFY `id` ... AUTO_INCREMENT...;
    content = re.sub(
        r'ALTER TABLE\s+`?\w+`?\s+\n?\s*MODIFY\s+[^;]+;',
        '',
        content,
        flags=re.MULTILINE | re.DOTALL
    )

    # -------------------------------------------------------
    # 4. Remove MySQL-only ALTER TABLE ... ADD PRIMARY KEY blocks
    #    (we'll handle PK inside CREATE TABLE)
    # -------------------------------------------------------
    content = re.sub(
        r'ALTER TABLE\s+`?\w+`?\s+\n?\s*ADD\s+PRIMARY KEY\s*\([^)]+\)\s*;',
        '',
        content,
        flags=re.MULTILINE | re.DOTALL
    )

    # -------------------------------------------------------
    # 5. Remove MySQL-only ALTER TABLE ... ADD KEY/INDEX blocks
    # -------------------------------------------------------
    content = re.sub(
        r'ALTER TABLE\s+`?\w+`?\s+\n?\s*ADD\s+(UNIQUE\s+)?KEY\s+[^;]+;',
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
    # 7. Remove backtick quoting
    # -------------------------------------------------------
    content = content.replace('`', '')

    # -------------------------------------------------------
    # 8. Data type conversions
    # -------------------------------------------------------
    content = re.sub(r'bigint\(\d+\)\s+UNSIGNED', 'bigint', content, flags=re.IGNORECASE)
    content = re.sub(r'int\(\d+\)\s+UNSIGNED', 'integer', content, flags=re.IGNORECASE)
    content = re.sub(r'bigint\(\d+\)', 'bigint', content, flags=re.IGNORECASE)
    content = re.sub(r'int\(\d+\)', 'integer', content, flags=re.IGNORECASE)
    content = re.sub(r'tinyint\(\d+\)', 'smallint', content, flags=re.IGNORECASE)
    content = re.sub(r'\blongtext\b', 'text', content, flags=re.IGNORECASE)
    content = re.sub(r'\bmediumtext\b', 'text', content, flags=re.IGNORECASE)
    content = re.sub(r'\bdatetime\b', 'timestamp', content, flags=re.IGNORECASE)
    content = re.sub(r'tinyinteger', 'smallint', content, flags=re.IGNORECASE)  # Fix leftover from previous runs

    # -------------------------------------------------------
    # 9. Remove MySQL charset/collation options
    # -------------------------------------------------------
    content = re.sub(r'\s+COLLATE\s+\S+', '', content, flags=re.IGNORECASE)
    content = re.sub(r'\s+CHARACTER SET\s+\S+', '', content, flags=re.IGNORECASE)

    # -------------------------------------------------------
    # 10. Remove ENGINE=InnoDB and trailing AUTO_INCREMENT
    #     These appear at end of CREATE TABLE
    # -------------------------------------------------------
    content = re.sub(r'\)\s*ENGINE=InnoDB[^;]*;', ');', content, flags=re.IGNORECASE | re.DOTALL)
    content = re.sub(r',\s*AUTO_INCREMENT=\d+', '', content, flags=re.IGNORECASE)
    content = re.sub(r'\bAUTO_INCREMENT\b', '', content, flags=re.IGNORECASE)

    # -------------------------------------------------------
    # 11. Fix common datetime default value
    # -------------------------------------------------------
    content = content.replace("'0000-00-00 00:00:00'", "'1970-01-01 00:00:00'")
    content = content.replace("'0000-00-00'", "'1970-01-01'")

    # -------------------------------------------------------
    # 12. Fix transaction markers
    # -------------------------------------------------------
    content = re.sub(r'\bSTART TRANSACTION\b', 'BEGIN', content, flags=re.IGNORECASE)

    # -------------------------------------------------------
    # 13. Clean up: remove inline -- comments
    # -------------------------------------------------------
    content = re.sub(r'--[^\n]*', '', content)

    # -------------------------------------------------------
    # 14. Clean up: remove leftover trailing commas before )
    # -------------------------------------------------------
    content = re.sub(r',\s*\)', ')', content)

    # -------------------------------------------------------
    # 15. Clean up: collapse multiple blank lines
    # -------------------------------------------------------
    content = re.sub(r'\n{3,}', '\n\n', content)

    with open(pg_file, 'w', encoding='utf-8') as f:
        f.write(content)

if __name__ == "__main__":
    convert_mysql_to_pg('install/database.sql', 'install/database_pg.sql')
    print("Conversion complete: install/database_pg.sql")
