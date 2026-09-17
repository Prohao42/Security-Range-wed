#!/bin/bash
# Zhazhasu Web Security Range - Source Installer
# Tested on Ubuntu 20.04/22.04, Debian 10/11, CentOS 7/8
set -e

RED='\033[0;31m'; GREEN='\033[0;32m'; YELLOW='\033[1;33m'; NC='\033[0m'
log()  { echo -e "${GREEN}[+]${NC} $1"; }
warn() { echo -e "${YELLOW}[!]${NC} $1"; }
err()  { echo -e "${RED}[-]${NC} $1"; exit 1; }

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
    apt-get update -qq

    # Add PHP 7.3 PPA
    apt-get install -y -qq software-properties-common > /dev/null
    add-apt-repository -y ppa:ondrej/php > /dev/null 2>&1
    apt-get update -qq

    apt-get install -y -qq \
        apache2 \
        php7.3 php7.3-cli php7.3-mysql php7.3-gd php7.3-mbstring \
        php7.3-xml php7.3-curl php7.3-zip php7.3-intl \
        mysql-server \
        libapache2-mod-php7.3 \
        > /dev/null

    a2enmod rewrite > /dev/null 2>&1
}

install_deps_centos() {
    yum install -y -q epel-release > /dev/null 2>&1

    # Remi repo for PHP 7.3
    yum install -y -q https://rpms.remirepo.net/enterprise/remi-release-7.rpm > /dev/null 2>&1 || \
    yum install -y -q https://rpms.remirepo.net/enterprise/remi-release-8.rpm > /dev/null 2>&1

    yum --enablerepo=remi -y install \
        httpd \
        php php-mysqlnd php-gd php-mbstring php-xml php-curl php-zip php-intl \
        mysql-server \
        > /dev/null 2>&1

    systemctl enable httpd > /dev/null 2>&1
    systemctl start httpd > /dev/null 2>&1
}

configure_mysql() {
    log "Configuring MySQL..."
    systemctl start mysql > /dev/null 2>&1 || systemctl start mysqld > /dev/null 2>&1 || true

    # Try to set root password (skip if already set)
    mysqladmin -u root password 'root' 2>/dev/null || true
    mysqladmin -u root -proot status > /dev/null 2>&1 || \
        mysqladmin -u root -p'' password 'root' 2>/dev/null || true
}

configure_apache() {
    log "Configuring Apache..."
    local WEBROOT="/var/www/html"

    # Remove default content
    rm -rf ${WEBROOT}/*

    # Copy project files
    cp -r "$(dirname "$0")"/* ${WEBROOT}/
    chown -R www-data:www-data ${WEBROOT} 2>/dev/null || \
        chown -R apache:apache ${WEBROOT} 2>/dev/null || true

    # Apache config
    cat > /etc/apache2/sites-available/000-default.conf 2>/dev/null <<'APACHECONF'
<VirtualHost *:80>
    DocumentRoot /var/www/html
    <Directory /var/www/html>
        Options Indexes FollowSymLinks
        AllowOverride All
        Require all granted
    </Directory>
    ErrorLog ${APACHE_LOG_DIR}/error.log
    CustomLog ${APACHE_LOG_DIR}/access.log combined
</VirtualHost>
APACHECONF

    # For CentOS/RHEL
    cat > /etc/httpd/conf.d/zhazhasu.conf 2>/dev/null <<'APACHECONF'
<VirtualHost *:80>
    DocumentRoot /var/www/html
    <Directory /var/www/html>
        Options Indexes FollowSymLinks
        AllowOverride All
        Require all granted
    </Directory>
</VirtualHost>
APACHECONF

    systemctl restart apache2 > /dev/null 2>&1 || \
        systemctl restart httpd > /dev/null 2>&1
}

init_database() {
    log "Initializing database..."
    local INIT_SQL=$(find /var/www/html -name "init_database.sql" -type f 2>/dev/null | head -5)

    if [ -n "$INIT_SQL" ]; then
        for sql in $INIT_SQL; do
            DB_NAME=$(basename $(dirname $(dirname "$sql")))
            mysql -u root -proot -e "CREATE DATABASE IF NOT EXISTS \`zhazhasu_${DB_NAME}\` CHARACTER SET utf8 COLLATE utf8_unicode_ci;" 2>/dev/null
            mysql -u root -proot "zhazhasu_${DB_NAME}" < "$sql" 2>/dev/null || true
            log "  Imported: zhazhasu_${DB_NAME}"
        done
    fi

    # Main CMS database
    CMS_SQL=$(find /var/www/html/database -name "*.sql" -type f 2>/dev/null | head -1)
    if [ -n "$CMS_SQL" ]; then
        mysql -u root -proot -e "CREATE DATABASE IF NOT EXISTS \`zhazhasu_cms\` CHARACTER SET utf8 COLLATE utf8_unicode_ci;" 2>/dev/null
        mysql -u root -proot "zhazhasu_cms" < "$CMS_SQL" 2>/dev/null || true
        log "  Imported: zhazhasu_cms"
    fi
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
    echo "  Default MySQL: root / root"
    echo "=============================================="
}

main "$@"
