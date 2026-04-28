# Secure Earning Repository

## Fullstack Audit Autofix Tool

### Run
```bash
python fullstack_audit_autofix.py
```

### Review Generated Files Safely
1. Open `audit_report.md` and check `Created Items` + `Missing Items`.
2. Review newly generated files before wiring into runtime:
   - `security.middleware.js` or `security.middleware.py`
   - `Dockerfile`, `docker-compose.yml`
   - `.github/workflows/ci.yml`
   - `scripts/backup_db.sh`
3. If a target file already existed with different content, use the generated `*.new` file for manual merge.
4. Test in a staging/local environment before production rollout.
