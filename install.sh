#!/bin/bash
# Zhazhasu Web Security Range - Source Installer
# Tested on Ubuntu 20.04/22.04/24.04, Debian 10/11, CentOS 7/8
set -e

RED='\033[0;31m'; GREEN='\033[0;32m'; YELLOW='\033[1;33m'; NC='\033[0m'
log()  { echo -e "${GREEN}[+]{NC} $1"; }
warn() { echo -e "${YELLOW}[!]{NC} $1"; }
err()  { echo -e "${RED}[-]{NC} $1"; exit 1; }

check_root() {
    [ "$(id -u)" -eq 0 ] || err "Please run as root: sudo bash install.sh"
}

detect_os() {
    if [ -f /etc/os-release ]; then
        . /etc/os-release
        OS=$ID; VER=$VERSION_ID
    elif [ -f /etc/centos-release ]; then
        OS="centos"; VER=$(grep -oE '[0-9]+' /etc/centos-release | head -1)
    else
        err "Unsupported OS"
    fi
    log "Detected: $OS $VER"
}

install_deps_ubuntu() {
    export DEBIAN_FRONTEND=noninteractive
    log "Installing Apache..."
    apt-get update -qq
    apt-get install -y -qq apache2 > /dev/null
    a2enmod rewrite > /dev/null 2>&1

    log "Installing PHP 7.3..."
    apt-get install -y -qq software-properties-common > /dev/null
    add-apt-repository -y ppa:ondrej/php > /dev/null 2>&1
    apt-get update -qq
    apt-get install -y -qq \
        php7.3 php7.3-cli php7.3-mysql php7.3-gd php7.3-mbstring \
        php7.3-xml php7.3-curl php7.3-zip php7.3-intl \
        libapache2-mod-php7.3 > /dev/null

    log "Installing MySQL..."
    apt-get install -y -qq mysql-server > /dev/null
}

install_deps_centos() {
    yum install -y -q epel-release > /dev/null 2>&1
    yum install -y -q https://rpms.remirepo.net/enterprise/remi-release-7.rpm > /dev/null 2>&1 || \
    yum install -y -q https://rpms.remirepo.net/enterprise/remi-release-8.rpm > /dev/null 2>&1

    yum --enablerepo=remi -y install \
        httpd \
        php php-mysqlnd php-gd php-mbstring php-xml php-curl php-zip php-intl \
        mysql-server > /dev/null 2>&1

    systemctl enable httpd > /dev/null 2>&1
    systemctl start httpd > /dev/null 2>&1
}

configure_mysql() {
    log "Starting MySQL..."
    systemctl start mysql > /dev/null 2>&1 || systemctl start mysqld > /dev/null 2>&1 || true
    systemctl enable mysql > /dev/null 2>&1 || systemctl enable mysqld > /dev/null 2>&1 || true

    log "Configuring MySQL root password..."
    # Try socket auth first (Ubuntu default), then set password
    mysql -u root -e "ALTER USER 'root'@'localhost' IDENTIFIED WITH mysql_native_password BY 'root'; FLUSH PRIVILEGES;" 2>/dev/null || \
    mysqladmin -u root password 'root' 2>/dev/null || true

    # Verify connection
    if mysql -u root -proot -e "SELECT 1;" > /dev/null 2>&1; then
        log "MySQL password set: root"
    else
        warn "Trying alternate method..."
        # For some MySQL versions, need to skip grant tables
        systemctl stop mysql > /dev/null 2>&1 || true
        mysqld_safe --skip-grant-tables &
        sleep 3
        mysql -u root -e "FLUSH PRIVILEGES; ALTER USER 'root'@'localhost' IDENTIFIED WITH mysql_native_password BY 'root'; FLUSH PRIVILEGES;" 2>/dev/null || true
        killall mysqld > /dev/null 2>&1 || true
        sleep 2
        systemctl start mysql > /dev/null 2>&1 || systemctl start mysqld > /dev/null 2>&1 || true
    fi

    # Final verify
    if mysql -u root -proot -e "SELECT 1;" > /dev/null 2>&1; then
        log "MySQL OK: root / root"
    else
        err "MySQL password setup failed"
    fi
}

configure_apache() {
    log "Configuring Apache..."
    local WEBROOT="/var/www/html"
    rm -rf ${WEBROOT}/*
    cp -r "$(dirname "$0")"/* ${WEBROOT}/
    chown -R www-data:www-data ${WEBROOT} 2>/dev/null || \
        chown -R apache:apache ${WEBROOT} 2>/dev/null || true

    APACHE_CONF='<VirtualHost *:80>
    DocumentRoot /var/www/html
    <Directory /var/www/html>
        Options Indexes FollowSymLinks
        AllowOverride All
        Require all granted
    </Directory>
    ErrorLog ${APACHE_LOG_DIR}/error.log
    CustomLog ${APACHE_LOG_DIR}/access.log combined
</VirtualHost>'

    [ -d /etc/apache2 ] && echo "$APACHE_CONF" > /etc/apache2/sites-available/000-default.conf
    [ -d /etc/httpd ] && echo "$APACHE_CONF" > /etc/httpd/conf.d/zhazhasu.conf

    systemctl restart apache2 > /dev/null 2>&1 || \
        systemctl restart httpd > /dev/null 2>&1
    log "Apache restarted"
}

init_database() {
    log "Initializing databases..."

    # Find all init_database.sql files
    find /var/www/html -name "init_database.sql" -type f | while read sql; do
        # Extract DB name from path: .../range/category/subcategory/database/init_database.sql
        DB_NAME=$(basename $(dirname $(dirname "$sql")))
        DB_FULL="zhazhasu_${DB_NAME}"

        mysql -u root -proot -e "CREATE DATABASE IF NOT EXISTS \`${DB_FULL}\` CHARACTER SET utf8 COLLATE utf8_unicode_ci;" 2>/dev/null
        mysql -u root -proot "${DB_FULL}" < "$sql" 2>/dev/null && log "  Imported: ${DB_FULL}" || warn "  Skip: ${DB_FULL}"
    done

    # Main CMS database
    if [ -f /var/www/html/database/init_database.sql ]; then
        mysql -u root -proot -e "CREATE DATABASE IF NOT EXISTS \`zhazhasu_cms\` CHARACTER SET utf8 COLLATE utf8_unicode_ci;" 2>/dev/null
        mysql -u root -proot "zhazhasu_cms" < /var/www/html/database/init_database.sql 2>/dev/null && log "  Imported: zhazhasu_cms"
    fi

    # List all databases
    log "All databases:"
    mysql -u root -proot -e "SHOW DATABASES LIKE 'zhazhasu_%';" 2>/dev/null
}

main() {
    echo "=============================================="
    echo "  Zhazhasu Web Security Range - Installer"
    echo "=============================================="

    check_root
    detect_os

    case $OS in
        ubuntu|debian) install_deps_ubuntu ;;
        centos|rhel)   install_deps_centos ;;
        *) err "Unsupported: $OS" ;;
    esac

    configure_mysql
    configure_apache
    init_database

    log "Installation complete!"
    echo ""
    echo "  Visit: http://$(hostname -I | awk '{print $1}')/"
    echo "  MySQL: root / root"
    echo "=============================================="
}

main "$@"
