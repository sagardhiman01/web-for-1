#!/usr/bin/env python3
"""Fullstack audit + safe autofix tool."""

from __future__ import annotations

import json
import os
import re
from datetime import datetime, timezone
from pathlib import Path
from typing import Dict, List, Set, Tuple

ROOT = Path(__file__).resolve().parent
REPORT_PATH = ROOT / "audit_report.md"

IGNORE_DIR_NAMES = {
    ".git", ".hg", ".svn", "node_modules", "vendor", "__pycache__", ".venv", "venv",
    ".idea", ".vscode", "dist", "build", ".next", "target",
}
IGNORE_REL_PREFIXES = ("deploy/core_stage_sync/vendor", "siiiit/core/vendor")
TEXT_EXT = {
    ".php", ".js", ".jsx", ".ts", ".tsx", ".py", ".java", ".kt", ".groovy", ".xml",
    ".gradle", ".kts", ".json", ".yml", ".yaml", ".env", ".ini", ".cfg", ".conf",
    ".properties", ".md", ".txt", ".sql", ".sh", ".html", ".css", ".htaccess",
}
MAX_FILE_READ = 512_000


def rel(path: Path) -> str:
    return path.relative_to(ROOT).as_posix()


def normalize_rel(path: Path) -> str:
    return path.as_posix().strip("./")


def should_skip_dir(relative_dir: Path) -> bool:
    rel_dir = normalize_rel(relative_dir)
    if not rel_dir:
        return False
    if set(relative_dir.parts) & IGNORE_DIR_NAMES:
        return True
    return any(rel_dir.startswith(prefix) for prefix in IGNORE_REL_PREFIXES)


def iter_project_files() -> List[Path]:
    files: List[Path] = []
    for dirpath, dirnames, filenames in os.walk(ROOT):
        current = Path(dirpath)
        rel_dir = current.relative_to(ROOT)
        dirnames[:] = [d for d in dirnames if not should_skip_dir(rel_dir / d)]
        for name in filenames:
            path = current / name
            r = rel(path)
            if any(r.startswith(prefix + "/") for prefix in IGNORE_REL_PREFIXES):
                continue
            files.append(path)
    return files


def read_text(path: Path) -> str:
    try:
        if path.stat().st_size > MAX_FILE_READ:
            return ""
    except OSError:
        return ""
    try:
        return path.read_text(encoding="utf-8", errors="ignore")
    except OSError:
        return ""


def load_json(path: Path) -> Dict:
    txt = read_text(path).strip()
    if not txt:
        return {}
    try:
        obj = json.loads(txt)
        return obj if isinstance(obj, dict) else {}
    except json.JSONDecodeError:
        return {}


def is_text_candidate(path: Path) -> bool:
    if path.name == ".htaccess":
        return True
    return path.suffix.lower() in TEXT_EXT


def gather_package_deps(files: List[Path]) -> Set[str]:
    deps: Set[str] = set()
    for p in files:
        if p.name != "package.json":
            continue
        data = load_json(p)
        for key in ("dependencies", "devDependencies", "peerDependencies"):
            block = data.get(key, {})
            if isinstance(block, dict):
                deps.update(block.keys())
    return deps


def gather_composer_deps(files: List[Path]) -> Set[str]:
    deps: Set[str] = set()
    for p in files:
        if p.name != "composer.json":
            continue
        data = load_json(p)
        for key in ("require", "require-dev"):
            block = data.get(key, {})
            if isinstance(block, dict):
                deps.update(block.keys())
    return deps


def detect_frontend(files: List[Path], npm: Set[str]) -> Set[str]:
    out: Set[str] = set()
    if {"react", "react-dom"} & npm:
        out.add("react")
    if "next" in npm or any(p.name == "next.config.js" for p in files):
        out.add("next")
    if "vue" in npm or any(p.name.startswith("vue.config") for p in files):
        out.add("vue")
    if "@angular/core" in npm or any(p.name == "angular.json" for p in files):
        out.add("angular")
    if any(p.suffix.lower() == ".html" and "vendor" not in p.parts for p in files):
        out.add("static-html")
    return out


def detect_backend(files: List[Path], npm: Set[str], composer: Set[str]) -> Set[str]:
    out: Set[str] = set()
    rels = {rel(p) for p in files}
    if "laravel/framework" in composer or any(p.name == "artisan" and "core" in p.parts for p in files):
        out.add("laravel")
    if "express" in npm:
        out.add("express")
    if "@nestjs/core" in npm or "nest-cli.json" in rels:
        out.add("nest")
    if {"express", "@nestjs/core"} & npm:
        out.add("node")
    if any(p.name == "manage.py" for p in files):
        out.add("django")
    py = [p for p in files if p.suffix == ".py" and p.name != "fullstack_audit_autofix.py"]
    for p in py[:200]:
        t = read_text(p)
        if re.search(r"(?m)^\s*(from\s+flask\b|import\s+flask\b)", t):
            out.add("flask")
            break
    for p in [x for x in files if x.name in {"pom.xml", "build.gradle", "build.gradle.kts"}]:
        t = read_text(p)
        if "spring-boot" in t or "org.springframework" in t:
            out.add("spring")
            break
    return out


def detect_db(files: List[Path], npm: Set[str]) -> Set[str]:
    out: Set[str] = set()
    rels = {rel(p) for p in files}
    if "prisma/schema.prisma" in rels or "prisma" in npm or "@prisma/client" in npm:
        out.add("prisma")
    if "mongoose" in npm:
        out.add("mongoose")
    if "sequelize" in npm or "sequelize-cli" in npm:
        out.add("sequelize")
    if "typeorm" in npm:
        out.add("typeorm")
    if any(p.suffix.lower() == ".sql" for p in files):
        out.add("sql-files")
    return out


def detect_server(files: List[Path]) -> Set[str]:
    out: Set[str] = set()
    for p in files:
        n = p.name.lower()
        if n == ".htaccess" or "apache" in n or n in {"httpd.conf", "apache2.conf"}:
            out.add("apache")
        if "nginx" in n and ".conf" in n:
            out.add("nginx")
        if p.suffix.lower() == ".service":
            out.add("systemd")
    return out


def detect_ci(files: List[Path]) -> Set[str]:
    out: Set[str] = set()
    rels = {rel(p) for p in files}
    if any(r.startswith(".github/workflows/") and r.endswith((".yml", ".yaml")) for r in rels):
        out.add("github-actions")
    if ".gitlab-ci.yml" in rels:
        out.add("gitlab-ci")
    if "azure-pipelines.yml" in rels:
        out.add("azure-pipelines")
    if any(r.startswith(".circleci/") for r in rels):
        out.add("circleci")
    return out


def detect_containers(files: List[Path]) -> Set[str]:
    rels = {rel(p).lower() for p in files}
    out: Set[str] = set()
    if "dockerfile" in rels:
        out.add("dockerfile")
    if "docker-compose.yml" in rels or "docker-compose.yaml" in rels:
        out.add("docker-compose")
    return out


def detect_logging(files: List[Path], npm: Set[str]) -> Set[str]:
    out: Set[str] = set()
    if {"winston", "pino", "morgan"} & npm:
        out.add("node-logger")
    if any(p.name == "logging.php" and "config" in p.parts for p in files):
        out.add("laravel-logging")
    if any("logger" in p.name.lower() and p.suffix.lower() in {".js", ".ts", ".py", ".php"} for p in files):
        out.add("logger-file")
    return out


def detect_cdn_hints(files: List[Path]) -> Set[str]:
    out: Set[str] = set()
    for p in files:
        if p.name != ".htaccess" and "nginx" not in p.name.lower() and p.name.lower() not in {"httpd.conf", "apache2.conf"}:
            continue
        t = read_text(p).lower()
        if "cache-control" in t or "expires" in t or "max-age" in t:
            out.add("cache-headers")
            break
    return out

def detect_security(files: List[Path], npm: Set[str], composer: Set[str]) -> Dict[str, object]:
    text_files = [p for p in files if is_text_candidate(p)]
    has_helmet = "helmet" in npm
    has_cors = "cors" in npm or "fruitcake/laravel-cors" in composer

    has_rate = "express-rate-limit" in npm
    if not has_rate:
        for p in text_files[:400]:
            t = read_text(p)
            if "ThrottleRequests" in t or "RateLimiter" in t or "rateLimit" in t:
                has_rate = True
                break

    env_files = [rel(p) for p in files if p.name == ".env"]
    env_example_files = [rel(p) for p in files if p.name == ".env.example"]

    env_ignored = False
    for p in [x for x in files if x.name == ".gitignore"]:
        if re.search(r"(?m)^\s*\.env(\..*)?\s*$", read_text(p)):
            env_ignored = True
            break

    https_hint = False
    for p in text_files[:1000]:
        if p.name not in {".htaccess", ".env", ".env.example"} and "nginx" not in p.name.lower() and p.suffix.lower() not in {".conf", ".php"}:
            continue
        t = read_text(p).lower()
        if "rewritecond %{https}" in t or "strict-transport-security" in t or "app_url=https" in t:
            https_hint = True
            break

    leak_patterns = [
        re.compile(r"AKIA[0-9A-Z]{16}"),
        re.compile(r"-----BEGIN (?:RSA|EC|OPENSSH|DSA|PGP) PRIVATE KEY-----"),
        re.compile(r"(?i)(api[_-]?key|secret|token|password)\s*[:=]\s*['\"][^'\"]{16,}['\"]"),
    ]
    leaks: List[str] = []
    for p in text_files[:2500]:
        if p.name in {".env", ".env.local", ".env.production"}:
            leaks.append(rel(p))
            continue
        t = read_text(p)
        if t and any(pattern.search(t) for pattern in leak_patterns):
            leaks.append(rel(p))
        if len(leaks) >= 25:
            break

    middleware_files = [rel(p) for p in files if re.match(r"security\.middleware\.(js|ts|py)$", p.name)]

    return {
        "helmet": has_helmet,
        "cors": has_cors,
        "rate_limit": has_rate,
        "env_files": env_files,
        "env_example_files": env_example_files,
        "env_ignored": env_ignored,
        "https_hint": https_hint,
        "potential_secret_leaks": sorted(set(leaks)),
        "security_middleware_files": middleware_files,
    }


def detect_backup(files: List[Path]) -> Set[str]:
    rels = {rel(p) for p in files}
    out: Set[str] = set()
    if "scripts/backup_db.sh" in rels:
        out.add("backup_db.sh")
    if "scripts/backup_db.py" in rels:
        out.add("backup_db.py")
    return out


def primary_stack(backend: Set[str]) -> str:
    for s in ("laravel", "nest", "express", "node", "django", "flask", "spring"):
        if s in backend:
            return s
    return "unknown"


def safe_write(path: Path, content: str, created: List[str], conflicts: List[str]) -> Tuple[str, str]:
    if path.exists():
        existing = read_text(path)
        if existing == content:
            return "exists-same", rel(path)
        new_path = path.with_name(path.name + ".new")
        if new_path.exists():
            if read_text(new_path) == content:
                return "conflict-new-exists-same", rel(new_path)
            return "conflict-new-exists-different", rel(new_path)
        new_path.parent.mkdir(parents=True, exist_ok=True)
        new_path.write_text(content, encoding="utf-8")
        created.append(rel(new_path))
        conflicts.append(f"{rel(path)} -> {rel(new_path)}")
        return "conflict-created-new", rel(new_path)
    path.parent.mkdir(parents=True, exist_ok=True)
    path.write_text(content, encoding="utf-8")
    created.append(rel(path))
    return "created", rel(path)


def env_keys(files: List[Path]) -> List[str]:
    keys: Set[str] = set()
    patterns = [
        re.compile(r"env\(\s*['\"]([A-Z0-9_]+)['\"]"),
        re.compile(r"process\.env\.([A-Z0-9_]+)"),
        re.compile(r"os\.getenv\(\s*['\"]([A-Z0-9_]+)['\"]"),
        re.compile(r"\$_ENV\[['\"]([A-Z0-9_]+)['\"]\]"),
    ]
    for p in [x for x in files if is_text_candidate(x)][:3000]:
        t = read_text(p)
        for pat in patterns:
            for m in pat.findall(t):
                keys.add(m)
    for p in files:
        if p.name.startswith(".env"):
            for line in read_text(p).splitlines():
                line = line.strip()
                if not line or line.startswith("#") or "=" not in line:
                    continue
                k = line.split("=", 1)[0].strip()
                if re.fullmatch(r"[A-Z0-9_]+", k):
                    keys.add(k)
    preferred = [
        "APP_NAME", "APP_ENV", "APP_KEY", "APP_DEBUG", "APP_URL",
        "DB_CONNECTION", "DB_HOST", "DB_PORT", "DB_DATABASE", "DB_USERNAME", "DB_PASSWORD",
        "CACHE_DRIVER", "QUEUE_CONNECTION", "SESSION_DRIVER",
        "MAIL_MAILER", "MAIL_HOST", "MAIL_PORT", "MAIL_USERNAME", "MAIL_PASSWORD",
        "MAIL_ENCRYPTION", "MAIL_FROM_ADDRESS", "MAIL_FROM_NAME",
    ]
    ordered = [k for k in preferred if k in keys]
    rest = sorted(k for k in keys if k not in ordered)
    return ordered + rest


def security_template(stack: str) -> Tuple[str, str]:
    if stack in {"django", "flask"}:
        return "security.middleware.py", """\
\"\"\"Security middleware starter (non-wired by default).\"\"\"

def apply_security_headers(response):
    response.headers[\"X-Content-Type-Options\"] = \"nosniff\"
    response.headers[\"X-Frame-Options\"] = \"SAMEORIGIN\"
    response.headers[\"Referrer-Policy\"] = \"no-referrer-when-downgrade\"
    response.headers[\"Permissions-Policy\"] = \"geolocation=(), microphone=(), camera=()\"
    # Enable HSTS only when HTTPS is fully enforced end-to-end.
    # response.headers[\"Strict-Transport-Security\"] = \"max-age=31536000; includeSubDomains\"
    return response
"""
    return "security.middleware.js", """\
/** Security middleware starter (non-wired by default). */
let helmet;
let cors;
let rateLimit;
try { helmet = require('helmet'); } catch (_) { helmet = null; }
try { cors = require('cors'); } catch (_) { cors = null; }
try { rateLimit = require('express-rate-limit'); } catch (_) { rateLimit = null; }
const noOp = (_req, _res, next) => next();
module.exports = {
  helmetMiddleware: helmet ? helmet() : noOp,
  corsMiddleware: cors ? cors({ origin: true, credentials: true }) : noOp,
  apiRateLimit: rateLimit ? rateLimit({ windowMs: 15 * 60 * 1000, max: 100, standardHeaders: true, legacyHeaders: false }) : noOp,
};
"""


def logging_template(stack: str) -> Tuple[str, str]:
    if stack in {"django", "flask"}:
        return "logging_setup.py", """\
\"\"\"Central logging setup starter.\"\"\"
import logging
from logging.handlers import RotatingFileHandler
from pathlib import Path

def configure_logging(log_dir=\"logs\"):
    Path(log_dir).mkdir(parents=True, exist_ok=True)
    logger = logging.getLogger(\"app\")
    logger.setLevel(logging.INFO)
    if not logger.handlers:
        h = RotatingFileHandler(Path(log_dir) / \"app.log\", maxBytes=5_000_000, backupCount=5)
        h.setFormatter(logging.Formatter(\"%(asctime)s %(levelname)s %(name)s %(message)s\"))
        logger.addHandler(h)
    return logger
"""
    if stack == "laravel":
        return "logging.setup.php", """\
<?php
/** Non-breaking logging helper for Laravel custom usage. */
use Illuminate\\Support\\Facades\\Log;
if (!function_exists('app_logger')) {
    function app_logger(string $event, array $context = []): void {
        try {
            Log::channel(config('logging.default'))->info($event, $context);
        } catch (\\Throwable $e) {
            error_log('app_logger failed: ' . $e->getMessage());
        }
    }
}
"""
    return "logging.setup.js", """\
/** Logging setup starter. */
const fs = require('fs');
const path = require('path');
function ensureLogDir() {
  const dir = path.join(process.cwd(), 'logs');
  if (!fs.existsSync(dir)) fs.mkdirSync(dir, { recursive: true });
  return dir;
}
module.exports = { ensureLogDir };
"""

def dockerfile_template(stack: str) -> str:
    if stack in {"express", "nest", "node"}:
        return """\
FROM node:20-alpine
WORKDIR /app
COPY package*.json ./
RUN npm ci --omit=dev || npm install --omit=dev
COPY . .
EXPOSE 3000
CMD [\"npm\", \"start\"]
"""
    if stack in {"django", "flask"}:
        return """\
FROM python:3.11-slim
WORKDIR /app
COPY requirements*.txt ./
RUN pip install --no-cache-dir -r requirements.txt || true
COPY . .
EXPOSE 8000
CMD [\"python\", \"app.py\"]
"""
    if stack == "spring":
        return """\
FROM eclipse-temurin:21-jre
WORKDIR /app
COPY . /app
EXPOSE 8080
CMD [\"sh\", \"-c\", \"echo 'Build your jar and update CMD to run it.' && sleep infinity\"]
"""
    return """\
FROM php:8.2-apache
WORKDIR /var/www/html
RUN docker-php-ext-install pdo pdo_mysql
RUN a2enmod rewrite
COPY . /var/www/html
EXPOSE 80
CMD [\"apache2-foreground\"]
"""


def compose_template(stack: str) -> str:
    port_map, app_port = "8080:80", "80"
    if stack in {"express", "nest", "node"}:
        port_map, app_port = "3000:3000", "3000"
    elif stack in {"django", "flask"}:
        port_map, app_port = "8000:8000", "8000"
    elif stack == "spring":
        port_map, app_port = "8080:8080", "8080"
    return f"""\
version: \"3.9\"
services:
  app:
    build: .
    ports:
      - \"{port_map}\"
    environment:
      APP_PORT: \"{app_port}\"
    restart: unless-stopped
  db:
    image: mysql:8.0
    environment:
      MYSQL_ROOT_PASSWORD: root
      MYSQL_DATABASE: app
      MYSQL_USER: app
      MYSQL_PASSWORD: app
    ports:
      - \"3306:3306\"
    volumes:
      - db_data:/var/lib/mysql
volumes:
  db_data:
"""


def ci_template(stack: str) -> str:
    if stack in {"express", "nest", "node"}:
        return """\
name: CI
on: [push, pull_request]
jobs:
  build:
    runs-on: ubuntu-latest
    steps:
      - uses: actions/checkout@v4
      - uses: actions/setup-node@v4
        with:
          node-version: \"20\"
      - run: npm ci || npm install
      - run: npm run lint --if-present
      - run: npm test --if-present
"""
    if stack in {"django", "flask"}:
        return """\
name: CI
on: [push, pull_request]
jobs:
  test:
    runs-on: ubuntu-latest
    steps:
      - uses: actions/checkout@v4
      - uses: actions/setup-python@v5
        with:
          python-version: \"3.11\"
      - run: |
          python -m pip install --upgrade pip
          if [ -f requirements.txt ]; then pip install -r requirements.txt; fi
      - run: |
          if [ -f pytest.ini ] || [ -d tests ]; then pytest -q || true; fi
"""
    if stack == "spring":
        return """\
name: CI
on: [push, pull_request]
jobs:
  build:
    runs-on: ubuntu-latest
    steps:
      - uses: actions/checkout@v4
      - uses: actions/setup-java@v4
        with:
          distribution: temurin
          java-version: \"21\"
      - run: |
          if [ -f pom.xml ]; then mvn -B -DskipTests package; fi
          if [ -f gradlew ]; then chmod +x gradlew && ./gradlew build -x test; fi
"""
    return """\
name: CI
on: [push, pull_request]
jobs:
  php:
    runs-on: ubuntu-latest
    steps:
      - uses: actions/checkout@v4
      - uses: shivammathur/setup-php@v2
        with:
          php-version: \"8.2\"
          extensions: mbstring, pdo, pdo_mysql
      - run: |
          if [ -f core/composer.json ]; then
            cd core
            composer install --no-interaction --no-progress --prefer-dist
          elif [ -f composer.json ]; then
            composer install --no-interaction --no-progress --prefer-dist
          fi
      - run: |
          if [ -d core ]; then
            find core -name \"*.php\" -not -path \"*/vendor/*\" -print0 | xargs -0 -n1 php -l
          else
            find . -name \"*.php\" -not -path \"*/vendor/*\" -print0 | xargs -0 -n1 php -l
          fi
"""


def backup_template() -> str:
    return """\
#!/usr/bin/env bash
set -euo pipefail

# Non-destructive DB backup placeholder.
# Usage:
# DB_HOST=127.0.0.1 DB_PORT=3306 DB_NAME=app DB_USER=app DB_PASSWORD=secret ./scripts/backup_db.sh

ts=$(date +"%Y%m%d_%H%M%S")
mkdir -p backups

if command -v mysqldump >/dev/null 2>&1; then
  mysqldump -h "${DB_HOST:-127.0.0.1}" -P "${DB_PORT:-3306}" -u "${DB_USER:-root}" "-p${DB_PASSWORD:-}" "${DB_NAME:-app}" > "backups/db_${ts}.sql"
  echo "Saved backups/db_${ts}.sql"
elif command -v pg_dump >/dev/null 2>&1; then
  PGPASSWORD="${DB_PASSWORD:-}" pg_dump -h "${DB_HOST:-127.0.0.1}" -p "${DB_PORT:-5432}" -U "${DB_USER:-postgres}" "${DB_NAME:-app}" > "backups/db_${ts}.sql"
  echo "Saved backups/db_${ts}.sql"
else
  echo "Missing mysqldump/pg_dump"
  exit 1
fi
"""


def env_example_content(keys: List[str]) -> str:
    if not keys:
        keys = ["APP_NAME", "APP_ENV", "APP_DEBUG", "APP_URL", "DB_CONNECTION", "DB_HOST", "DB_PORT", "DB_DATABASE", "DB_USERNAME", "DB_PASSWORD"]
    lines = ["# Auto-generated environment template", "# Fill values before deployment", ""]
    lines.extend(f"{k}=" for k in keys)
    lines.append("")
    return "\n".join(lines)


def run() -> None:
    files = iter_project_files()
    npm = gather_package_deps(files)
    composer = gather_composer_deps(files)

    frontend = detect_frontend(files, npm)
    backend = detect_backend(files, npm, composer)
    db = detect_db(files, npm)
    server = detect_server(files)
    ci = detect_ci(files)
    containers = detect_containers(files)
    logging = detect_logging(files, npm)
    cdn = detect_cdn_hints(files)
    security = detect_security(files, npm, composer)
    backup = detect_backup(files)
    stack = primary_stack(backend)

    created: List[str] = []
    conflicts: List[str] = []

    if backend and not security["security_middleware_files"]:
        name, content = security_template(stack)
        safe_write(ROOT / name, content, created, conflicts)

    if not (ROOT / "Dockerfile").exists():
        safe_write(ROOT / "Dockerfile", dockerfile_template(stack), created, conflicts)
    if not ((ROOT / "docker-compose.yml").exists() or (ROOT / "docker-compose.yaml").exists()):
        safe_write(ROOT / "docker-compose.yml", compose_template(stack), created, conflicts)

    if not ci:
        safe_write(ROOT / ".github/workflows/ci.yml", ci_template(stack), created, conflicts)

    if not logging:
        name, content = logging_template(stack)
        safe_write(ROOT / name, content, created, conflicts)

    if not security["env_example_files"]:
        safe_write(ROOT / ".env.example", env_example_content(env_keys(files)), created, conflicts)

    if not backup:
        safe_write(ROOT / "scripts/backup_db.sh", backup_template(), created, conflicts)

    files2 = iter_project_files()
    ci2 = detect_ci(files2)
    cont2 = detect_containers(files2)
    log2 = detect_logging(files2, npm)
    sec2 = detect_security(files2, npm, composer)
    backup2 = detect_backup(files2)

    present: List[str] = []
    missing: List[str] = []
    manual: List[str] = []

    present.append(f"Frontend detected: {', '.join(sorted(frontend))}" if frontend else "")
    present.append(f"Backend detected: {', '.join(sorted(backend))}" if backend else "")
    present.append(f"Database usage hints: {', '.join(sorted(db))}" if db else "")
    present.append(f"Server config found: {', '.join(sorted(server))}" if server else "")
    present.append(f"CI/CD found: {', '.join(sorted(ci2))}" if ci2 else "")
    present.append(f"Containers found: {', '.join(sorted(cont2))}" if cont2 else "")
    present.append("CDN/static cache hint detected." if cdn else "")
    present.append(f"Monitoring/logging detected: {', '.join(sorted(log2))}" if log2 else "")
    present.append(f"Backup script detected: {', '.join(sorted(backup2))}" if backup2 else "")
    present.append(
        "Security basics present: " + ", ".join(
            [
                *( ["helmet"] if sec2["helmet"] else [] ),
                *( ["cors"] if sec2["cors"] else [] ),
                *( ["rate-limit"] if sec2["rate_limit"] else [] ),
                *( [".env present"] if sec2["env_files"] else [] ),
                *( [".env.example present"] if sec2["env_example_files"] else [] ),
                *( [".env ignored"] if sec2["env_ignored"] else [] ),
                *( ["HTTPS hint present"] if sec2["https_hint"] else [] ),
                *( [f"security middleware helper: {', '.join(sec2['security_middleware_files'])}"] if sec2["security_middleware_files"] else [] ),
            ]
        )
    )
    present = [p for p in present if p]

    if not frontend:
        missing.append("Frontend framework/static app not clearly detected.")
    if not backend:
        missing.append("Backend framework not detected.")
    if not db:
        missing.append("No database usage marker found (prisma/mongoose/sequelize/typeorm/sql).")
    if not server:
        missing.append("No nginx/apache/systemd config detected.")
    if not ci2:
        missing.append("CI/CD config missing.")
    if not cont2:
        missing.append("Dockerfile/docker-compose missing.")
    if not cdn:
        missing.append("No static caching header hint detected for CDN.")
    if not log2:
        missing.append("Monitoring/logging setup missing.")
    if not backup2:
        missing.append("Backup script missing.")

    sec_missing: List[str] = []
    if not sec2["helmet"]:
        sec_missing.append("helmet")
    if not sec2["cors"]:
        sec_missing.append("cors")
    if not sec2["rate_limit"]:
        sec_missing.append("rate-limit")
    if not sec2["env_files"]:
        sec_missing.append(".env")
    if not sec2["env_example_files"]:
        sec_missing.append(".env.example")
    if not sec2["env_ignored"]:
        sec_missing.append(".env not ignored")
    if not sec2["https_hint"]:
        sec_missing.append("HTTPS hint")
    if not sec2["security_middleware_files"]:
        sec_missing.append("security.middleware.(js|ts|py)")
    if sec_missing:
        missing.append("Security basics missing: " + ", ".join(sec_missing))

    leaks = sec2["potential_secret_leaks"]
    if leaks:
        missing.append("Potential secret leaks detected in: " + ", ".join(leaks[:8]) + (" ..." if len(leaks) > 8 else ""))
        manual.append("Review potential secret exposures. Rotate credentials and remove secrets from tracked files.")

    if created:
        manual.append("Review generated files before wiring them into runtime paths.")
    if conflicts:
        manual.append("Resolve *.new files manually and keep only intended final versions.")
    manual.extend([
        "Configure cloud WAF/CDN (Cloudflare or CloudFront) with TLS redirect at edge.",
        "Add centralized uptime + error + auth/deposit anomaly alerting.",
        "Enable dependency vulnerability scans in CI (Composer audit / npm audit / pip-audit).",
        "Test backups with periodic restore drills in staging.",
    ])

    created_list = created or ["No new files were created (idempotent run)."]

    lines: List[str] = [
        "# Fullstack Audit Report",
        "",
        f"- Generated: {datetime.now(timezone.utc).isoformat()}",
        f"- Root: `{ROOT}`",
        f"- Primary backend stack: `{stack}`",
        "",
        "## ✅ Present Items",
    ]
    if present:
        lines.extend([f"- {x}" for x in present])
    else:
        lines.append("- None detected.")

    lines.extend(["", "## ❌ Missing Items"])
    if missing:
        lines.extend([f"- {x}" for x in missing])
    else:
        lines.append("- None.")

    lines.extend(["", "## 🛠️ Created Items"])
    lines.extend([f"- {x}" for x in created_list])

    if conflicts:
        lines.extend(["", "- Conflict-safe outputs:", *[f"  - {c}" for c in conflicts]])

    lines.extend(["", "## 🔧 Next Manual Steps (Cloud/CDN/Security)", *[f"- {x}" for x in manual], ""])
    REPORT_PATH.write_text("\n".join(lines), encoding="utf-8")

    print(f"Audit complete. Report written to: {rel(REPORT_PATH)}")
    if created:
        print("Created files:")
        for p in created:
            print(f" - {p}")
    if conflicts:
        print("Conflicts detected (created .new files):")
        for c in conflicts:
            print(f" - {c}")


if __name__ == "__main__":
    run()
