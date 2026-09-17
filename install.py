#!/usr/bin/env python3
"""
Zhazhasu Web Security Range - Installer
Supports: Ubuntu/Debian/CentOS source install & Docker install
"""
import subprocess, sys, os, json, glob, socket

RED, GREEN, YELLOW, NC = "\033[0;31m", "\033[0;32m", "\033[1;33m", "\033[0m"
SCRIPT_DIR = os.path.dirname(os.path.abspath(__file__))

def log(msg): print(f"{GREEN}[+]{NC} {msg}")
def warn(msg): print(f"{YELLOW}[!]{NC} {msg}")
def err(msg): print(f"{RED}[-]{NC} {msg}"); sys.exit(1)

def run(cmd, check=True):
    print(f"    $ {cmd}")
    r = subprocess.run(cmd, shell=True, capture_output=True, text=True)
    if check and r.returncode != 0 and r.stderr.strip():
        warn(r.stderr.strip())
    return r.returncode == 0

def check_root():
    if os.geteuid() != 0:
        err("Please run as root: sudo python3 install.py")

def detect_os():
    try:
        with open("/etc/os-release") as f:
            info = dict(line.split("=", 1) for line in f if "=" in line)
        return info.get("ID", ""), info.get("VERSION_ID", "")
    except:
        return "", ""

def install_ubuntu():
    os.environ["DEBIAN_FRONTEND"] = "noninteractive"
    log("Installing Apache...")
    run("apt-get update -qq")
    run("apt-get install -y -qq apache2 > /dev/null")
    run("a2enmod rewrite > /dev/null 2>&1 || true")

    log("Installing PHP 7.3...")
    run("apt-get install -y -qq software-properties-common > /dev/null")
    run("add-apt-repository -y ppa:ondrej/php > /dev/null 2>&1 || true")
    run("apt-get update -qq")
    run("apt-get install -y -qq php7.3 php7.3-cli php7.3-mysql php7.3-gd "
        "php7.3-mbstring php7.3-xml php7.3-curl php7.3-zip php7.3-intl "
        "libapache2-mod-php7.3 > /dev/null")

    log("Installing MySQL...")
    run("apt-get install -y -qq mysql-server > /dev/null")

def install_centos():
    run("yum install -y -q epel-release > /dev/null 2>&1 || true")
    run("yum install -y -q https://rpms.remirepo.net/enterprise/remi-release-7.rpm > /dev/null 2>&1 || "
        "yum install -y -q https://rpms.remirepo.net/enterprise/remi-release-8.rpm > /dev/null 2>&1 || true")
    run("yum --enablerepo=remi -y install httpd php php-mysqlnd php-gd php-mbstring "
        "php-xml php-curl php-zip php-intl mysql-server > /dev/null 2>&1")
    run("systemctl enable httpd > /dev/null 2>&1")
    run("systemctl start httpd > /dev/null 2>&1")

def configure_mysql():
    log("Starting MySQL...")
    run("systemctl start mysql > /dev/null 2>&1 || systemctl start mysqld > /dev/null 2>&1 || true", check=False)
    run("systemctl enable mysql > /dev/null 2>&1 || systemctl enable mysqld > /dev/null 2>&1 || true", check=False)

    log("Setting root password...")
    # Try socket auth first (Ubuntu default)
    ok = run("mysql -u root -e \"ALTER USER 'root'@'localhost' IDENTIFIED WITH mysql_native_password BY 'root'; FLUSH PRIVILEGES;\" 2>/dev/null", check=False)
    if not ok:
        run("mysqladmin -u root password 'root' 2>/dev/null || true", check=False)

    # Verify
    if run("mysql -u root -proot -e 'SELECT 1;' > /dev/null 2>&1", check=False):
        log("MySQL OK: root / root")
    else:
        warn("Trying skip-grant method...")
        run("systemctl stop mysql > /dev/null 2>&1 || true", check=False)
        subprocess.Popen("mysqld_safe --skip-grant-tables", shell=True)
        import time; time.sleep(3)
        run("mysql -u root -e \"FLUSH PRIVILEGES; ALTER USER 'root'@'localhost' IDENTIFIED WITH mysql_native_password BY 'root'; FLUSH PRIVILEGES;\" 2>/dev/null", check=False)
        run("killall mysqld > /dev/null 2>&1 || true", check=False)
        time.sleep(2)
        run("systemctl start mysql > /dev/null 2>&1 || systemctl start mysqld > /dev/null 2>&1 || true", check=False)

    if not run("mysql -u root -proot -e 'SELECT 1;' > /dev/null 2>&1", check=False):
        err("MySQL password setup failed")

def configure_apache():
    log("Configuring Apache...")
    conf = """<VirtualHost *:80>
    DocumentRoot /var/www/html
    <Directory /var/www/html>
        Options Indexes FollowSymLinks
        AllowOverride All
        Require all granted
    </Directory>
</VirtualHost>"""

    for path in ["/etc/apache2/sites-available/000-default.conf",
                 "/etc/httpd/conf.d/zhazhasu.conf"]:
        try:
            if os.path.isdir(os.path.dirname(path)):
                with open(path, "w") as f:
                    f.write(conf)
        except:
            pass

    run("rm -rf /var/www/html/*", check=False)
    run(f"cp -r {SCRIPT_DIR}/* /var/www/html/")
    run("chown -R www-data:www-data /var/www/html 2>/dev/null || "
        "chown -R apache:apache /var/www/html 2>/dev/null || true", check=False)
    run("systemctl restart apache2 > /dev/null 2>&1 || "
        "systemctl restart httpd > /dev/null 2>&1", check=False)
    log("Apache restarted")

def init_database():
    log("Initializing databases...")
    for sql in glob.glob("/var/www/html/**/init_database.sql", recursive=True):
        parts = sql.split("/")
        # Find the range subfolder name
        try:
            idx = parts.index("range")
            db_name = "zhazhasu_" + parts[idx + 1]
        except:
            db_name = "zhazhasu_" + os.path.basename(os.path.dirname(os.path.dirname(sql)))

        run(f'mysql -u root -proot -e "CREATE DATABASE IF NOT EXISTS `{db_name}` '
            f'CHARACTER SET utf8 COLLATE utf8_unicode_ci;" 2>/dev/null', check=False)
        if run(f'mysql -u root -proot "{db_name}" < "{sql}" 2>/dev/null', check=False):
            log(f"  Imported: {db_name}")
        else:
            warn(f"  Skip: {db_name}")

    # Main CMS database
    cms_sql = "/var/www/html/database/init_database.sql"
    if os.path.exists(cms_sql):
        run('mysql -u root -proot -e "CREATE DATABASE IF NOT EXISTS `zhazhasu_cms` '
            'CHARACTER SET utf8 COLLATE utf8_unicode_ci;" 2>/dev/null', check=False)
        if run(f'mysql -u root -proot "zhazhasu_cms" < "{cms_sql}" 2>/dev/null', check=False):
            log("  Imported: zhazhasu_cms")

    log("All databases:")
    run("mysql -u root -proot -e \"SHOW DATABASES LIKE 'zhazhasu_%';\" 2>/dev/null", check=False)

def setup_docker_mirror():
    try:
        with open("/etc/docker/daemon.json") as f:
            cfg = json.load(f)
    except:
        cfg = {}
    if "registry-mirrors" not in cfg:
        cfg["registry-mirrors"] = ["https://docker.1ms.run", "https://docker.m.daocloud.io"]
        tmp = "/tmp/daemon.json"
        with open(tmp, "w") as f:
            json.dump(cfg, f, indent=2)
        run(f"cp {tmp} /etc/docker/daemon.json", check=False)
        run("rm " + tmp, check=False)
        run("systemctl daemon-reload", check=False)
        run("systemctl restart docker", check=False)
        log("Docker mirror configured")

def docker_install():
    setup_docker_mirror()
    log("Building Docker containers (3-5 min)...")
    os.chdir(SCRIPT_DIR)
    if run("docker compose up -d --build", check=False):
        log("Done! Visit http://localhost:8080/")
    else:
        err("Docker build failed")

def source_install():
    os_id, os_ver = detect_os()
    log(f"Detected: {os_id} {os_ver}")

    if os_id in ("ubuntu", "debian"):
        install_ubuntu()
    elif os_id in ("centos", "rhel"):
        install_centos()
    else:
        err(f"Unsupported OS: {os_id}")

    configure_mysql()
    configure_apache()
    init_database()

    ip = socket.gethostbyname(socket.gethostname())
    log("Installation complete!")
    print(f"\n  Visit: http://{ip}/")
    print(f"  MySQL: root / root\n")

def main():
    print("=" * 50)
    print("  Zhazhasu Web Security Range - Installer")
    print("=" * 50)

    if len(sys.argv) > 1 and sys.argv[1] == "--docker":
        check_root()
        docker_install()
    else:
        print("\n  Install method:\n")
        print("  [1] Source install (Apache + PHP + MySQL)")
        print("  [2] Docker install")
        print()
        choice = input("  Select (1/2): ").strip()
        check_root()
        if choice == "2":
            docker_install()
        else:
            source_install()

if __name__ == "__main__":
    main()
