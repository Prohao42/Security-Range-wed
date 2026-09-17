#!/bin/bash
# Fix MySQL connection for Zhazhasu
set -e

RED='\033[0;31m'; GREEN='\033[0;32m'; YELLOW='\033[1;33m'; NC='\033[0m'
log()  { echo -e "${GREEN}[+]{NC} $1"; }
warn() { echo -e "${YELLOW}[!]{NC} $1"; }
err()  { echo -e "${RED}[-]{NC} $1"; exit 1; }

[ "$(id -u)" -eq 0 ] || err "Run as root: sudo bash fix_mysql.sh"

log "Checking MySQL service..."
systemctl start mysql 2>/dev/null || systemctl start mysqld 2>/dev/null || true
systemctl enable mysql 2>/dev/null || systemctl enable mysqld 2>/dev/null || true

if ! systemctl is-active --quiet mysql 2>/dev/null && ! systemctl is-active --quiet mysqld 2>/dev/null; then
    err "MySQL failed to start"
fi
log "MySQL is running"

log "Configuring root user..."
# Set password with mysql_native_password
mysql -u root -e "
    ALTER USER 'root'@'localhost' IDENTIFIED WITH mysql_native_password BY 'root';
    FLUSH PRIVILEGES;
" 2>/dev/null || \
mysql -u root -e "
    UPDATE mysql.user SET plugin='mysql_native_password' WHERE user='root' AND host='localhost';
    FLUSH PRIVILEGES;
    SET PASSWORD FOR 'root'@'localhost' = PASSWORD('root');
    FLUSH PRIVILEGES;
" 2>/dev/null || warn "May need manual password reset"

# Create remote access user
mysql -u root -proot -e "
    CREATE USER IF NOT EXISTS 'root'@'%' IDENTIFIED WITH mysql_native_password BY 'root';
    GRANT ALL PRIVILEGES ON *.* TO 'root'@'%' WITH GRANT OPTION;
    FLUSH PRIVILEGES;
" 2>/dev/null || true

# Test connection
if mysql -u root -proot -e "SELECT 1;" > /dev/null 2>&1; then
    log "MySQL connection OK (password: root)"
else
    err "MySQL connection failed"
fi

# Check PHP extensions
log "Checking PHP extensions..."
for ext in mysql mysqli pdo pdo_mysql; do
    if php -m | grep -qi "$ext"; then
        log "  PHP extension: $ext OK"
    else
        warn "  PHP extension: $ext MISSING"
    fi
done

# Test PHP connection
log "Testing PHP MySQL connection..."
php -r "
try {
    \$pdo = new PDO('mysql:host=localhost;port=3306', 'root', 'root');
    echo 'PHP PDO connection OK\n';
    \$pdo = null;
} catch (PDOException \$e) {
    echo 'PHP PDO failed: ' . \$e->getMessage() . '\n';
    exit(1);
}
" 2>&1

# Restart Apache
log "Restarting Apache..."
systemctl restart apache2 2>/dev/null || systemctl restart httpd 2>/dev/null || true

# Show databases
log "Databases:"
mysql -u root -proot -e "SHOW DATABASES LIKE 'zhazhasu_%';" 2>/dev/null

echo ""
log "Fix complete! Visit http://$(hostname -I | awk '{print $1}')/"
