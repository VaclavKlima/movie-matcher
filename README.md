# movie-matcher

## Docker (Linux)

### Initial Setup with MySQL on SSD

1. **Configure MySQL data path** (optional, for better performance):

   If you want to store MySQL data on a specific disk (e.g., SSD), edit your `.env` file:
   ```bash
   MYSQL_DATA_PATH=/path/to/ssd/moviematcher/mysql-data
   ```

   For example, if your SSD is mounted at `/mnt/ssd`:
   ```bash
   MYSQL_DATA_PATH=/mnt/ssd/moviematcher/mysql-data
   ```

2. **Create the MySQL data directory with proper permissions**:
   ```bash
   sudo mkdir -p /path/to/ssd/moviematcher/mysql-data
   sudo chown -R 999:999 /path/to/ssd/moviematcher/mysql-data
   sudo chmod 755 /path/to/ssd/moviematcher/mysql-data
   ```

   Note: `999:999` is the default MySQL user/group ID in the official MySQL Docker image.

3. **Build and run the app**:
   ```bash
   docker compose up --build
   ```

4. **Run migrations** (first time only):
   ```bash
   docker compose exec app php artisan migrate
   ```

Then open `http://localhost:8000`.

### Development

If you want to run the Vite dev server instead of the production build:

```bash
docker compose exec app npm run dev -- --host 0.0.0.0
```

### Troubleshooting

If MySQL fails to start, check permissions:
```bash
ls -la /path/to/ssd/moviematcher/mysql-data
# Should show: drwxr-xr-x 999 999
```

View MySQL logs:
```bash
docker compose logs mysql
```
