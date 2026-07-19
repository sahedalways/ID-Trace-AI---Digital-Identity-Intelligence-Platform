

# Load .env variables
ENV_FILE="$(dirname "$0")/.env"

if [ ! -f "$ENV_FILE" ]; then
    echo "[ERROR] .env file not found at $ENV_FILE"
    exit 1
fi

# Parse .env file
while IFS='=' read -r key value; do
    key=$(echo "$key" | xargs)
    value=$(echo "$value" | xargs)
    # Skip comments and empty lines
    [[ -z "$key" || "$key" == \#* ]] && continue
    # Remove surrounding quotes
    value="${value%\"}"
    value="${value#\"}"
    value="${value%\'}"
    value="${value#\'}"
    export "$key=$value"
done < "$ENV_FILE"

# Configuration
DB_HOST="${DB_HOST:-localhost}"
DB_NAME="${DB_NAME:-id_db}"
DB_USER="${DB_USER:-sahed}"
DB_PASS="${DB_PASS:-}"
BACKUP_DIR="$(dirname "$0")/backups"
DATE=$(date +"%Y-%m-%d_%H-%M-%S")
BACKUP_FILE="$BACKUP_DIR/${DB_NAME}_${DATE}.sql"
LOG_FILE="$BACKUP_DIR/backup.log"


mkdir -p "$BACKUP_DIR"


log() {
    echo "[$(date '+%Y-%m-%d %H:%M:%S')] $1" | tee -a "$LOG_FILE"
}

log "=========================================="
log "Starting database backup for: $DB_NAME"


if [ -z "$DB_PASS" ]; then
    mysqldump -h "$DB_HOST" -u "$DB_USER" "$DB_NAME" \
        --single-transaction \
        --routines \
        --triggers \
        --events \
        --add-drop-table \
        --complete-insert \
        --extended-insert \
        --quick \
        --lock-tables=false \
        > "$BACKUP_FILE" 2>> "$LOG_FILE"
else
    mysqldump -h "$DB_HOST" -u "$DB_USER" -p"$DB_PASS" "$DB_NAME" \
        --single-transaction \
        --routines \
        --triggers \
        --events \
        --add-drop-table \
        --complete-insert \
        --extended-insert \
        --quick \
        --lock-tables=false \
        > "$BACKUP_FILE" 2>> "$LOG_FILE"
fi

# Check if backup was successful
if [ $? -eq 0 ] && [ -f "$BACKUP_FILE" ] && [ -s "$BACKUP_FILE" ]; then
    FILE_SIZE=$(du -h "$BACKUP_FILE" | cut -f1)
    log "SUCCESS: Backup created -> $(basename "$BACKUP_FILE") ($FILE_SIZE)"
else
    log "ERROR: Backup failed for database '$DB_NAME'"
    # Remove empty/failed backup file
    [ -f "$BACKUP_FILE" ] && rm -f "$BACKUP_FILE"
    exit 1
fi

# Delete ALL old backups - keep only the most recent one
log "Cleaning up old backups (keeping only latest)..."
# Find all backup files except the current one, sorted by time (oldest first), delete them
DELETED=$(find "$BACKUP_DIR" -name "${DB_NAME}_*.sql" -type f ! -name "$(basename "$BACKUP_FILE")" -print -delete 2>> "$LOG_FILE")
if [ -n "$DELETED" ]; then
    log "Deleted old backups:"
    echo "$DELETED" | while read -r f; do
        log "  -> $(basename "$f")"
    done
else
    log "No old backups to delete."
fi

# Summary
TOTAL_BACKUPS=$(find "$BACKUP_DIR" -name "${DB_NAME}_*.sql" -type f | wc -l)
TOTAL_SIZE=$(du -sh "$BACKUP_DIR" 2>/dev/null | cut -f1)
log "Total backups stored: $TOTAL_BACKUPS ($TOTAL_SIZE)"
log "Backup process completed."
log "=========================================="
