#!/usr/bin/env python3
"""
Zhazhasu Web Security Range - Installer
Supports: Ubuntu/Debian/CentOS source install & Docker install
"""
import subprocess, sys, os, json, platform

RED, GREEN, YELLOW, NC = "\033[0;31m", "\033[0;32m", "\033[1;33m", "\033[0m"
SCRIPT_DIR = os.path.dirname(os.path.abspath(__file__))

def log(msg): print(f"{GREEN}[+]{NC} {msg}")
def warn(msg): print(f"{YELLOW}[!]{NC} {msg}")
def err(msg): print(f"{RED}[-]{NC} {msg}"); sys.exit(1)

def run(cmd, check=True, capture=False):
    print(f"    $ {cmd}")
    r = subprocess.run(cmd, shell=True, capture_output=capture, text=True)
    if check and r.returncode != 0:
        if capture:
            warn(r.stderr.strip())
        return False
    return True

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
    run("export DEBIAN_FRONTEND=noninteractive")
    run("apt-get update -qq")
    run("apt-get install -y -qq software-properties-common > /dev/null")
    run("add-apt-repository -y ppa:ondrej/php > /dev/null 2>&1 || true")
    run("apt-get update -qq")
    run("apt-get install -y -qq apache2 php7.3 php7.3-cli php7.3-mysql php7.3-gd "
        "php7.3-mbstring php7.3-xml php7.3-curl php7.3-zip php7.3-intl "
        "mysql-server libapache2-mod-php7.3 > /dev/null")
    run("a2enmod rewrite > /dev/null 2>&1 || true")

def install_centos():
    run("yum install -y -q epel-release > /dev/null 2>&1 || true")
    run("yum install -y -q https://rpms.remirepo.net/enterprise/remi-release-7.rpm > /dev/null 2>&1 || "
        "yum install -y -q https://rpms.remirepo.net/enterprise/remi-release-8.rpm > /dev/null 2>&1 || true")
    run("yum --enablerepo=remi -y install httpd php php-mysqlnd php-gd php-mbstring "
        "php-xml php-curl php-zip php-intl mysql-server > /dev/null 2>&1")
    run("systemctl enable httpd > /dev/null 2>&1")
    run("systemctl start httpd > /dev/null 2>&1")

def configure_mysql():
    log("Configuring MySQL...")
    run("systemctl start mysql > /dev/null 2>&1 || systemctl start mysqld > /dev/null 2>&1 || true", check=False)
    run("mysqladmin -u root password 'root' 2>/dev/null || true", check=False)

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

def init_database():
    log("Initializing databases...")
    import glob
    for sql in glob.glob("/var/www/html/**/init_database.sql", recursive=True):
        db_name = "zhazhasu_" + os.path.basename(os.path.dirname(os.path.dirname(sql)))
        run(f'mysql -u root -proot -e "CREATE DATABASE IF NOT EXISTS `{db_name}` '
            f'CHARACTER SET utf8 COLLATE utf8_unicode_ci;" 2>/dev/null', check=False)
        run(f'mysql -u root -proot "{db_name}" < "{sql}" 2>/dev/null', check=False)
        log(f"  {db_name}")

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

    import socket
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
