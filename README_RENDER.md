# Carpooling App - Render Deployment Guide

## 1. Environment Variables (on Render)

In your Render Web Service -> **Environment** section, add:

- `DB_HOST` = (from Railway / other MySQL provider)
- `DB_USER` = your database username
- `DB_PASS` = your database password
- `DB_NAME` = your database name
- `DB_PORT` = 3306  (or your provider's port)

## 2. Docker Deployment

This project includes:

- `Dockerfile`
- `render.yaml`
- updated `db.php` (uses `getenv()` for DB connection)

Steps:

1. Push this folder to a **GitHub repo**.
2. On Render:
   - Create **New + Web Service**
   - Connect the GitHub repo
   - It will auto-detect `render.yaml` and create the service.
3. Make sure your MySQL database is online and credentials are correct.
4. Open the Render URL to use the app.
