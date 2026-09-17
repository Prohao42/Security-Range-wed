#!/usr/bin/env python3
import subprocess, sys, os, json

def run(cmd, sudo=False):
    prefix = ["sudo"] if sudo else []
    print(f"[*] {'sudo ' if sudo else ''}{cmd}")
    r = subprocess.run(prefix + cmd.split(), capture_output=True, text=True)
    if r.returncode != 0 and r.stderr:
        print(f"    WARN: {r.stderr.strip()}")
    return r.returncode == 0

def setup_docker_mirror():
    daemon_json = "/etc/docker/daemon.json"
    mirrors = {"registry-mirrors": ["https://docker.1ms.run", "https://docker.m.daocloud.io"]}

    try:
        with open(daemon_json, "r") as f:
            cfg = json.load(f)
    except:
        cfg = {}

    if "registry-mirrors" not in cfg:
        cfg.update(mirrors)
        tmp = "/tmp/daemon.json"
        with open(tmp, "w") as f:
            json.dump(cfg, f, indent=2)
        run(f"cp {tmp} {daemon_json}", sudo=True)
        run("rm " + tmp)
        print("[+] Docker mirror configured")
        run("systemctl daemon-reload", sudo=True)
        run("systemctl restart docker", sudo=True)

def main():
    if os.geteuid() != 0:
        print("[!] Please run as root: sudo python3 install.py")
        sys.exit(1)

    print("=" * 50)
    print("  Zhazhasu Web Security Range - Installer")
    print("=" * 50)

    setup_docker_mirror()

    print("\n[*] Building and starting containers (may take 3-5 min)...")
    if run("docker compose up -d --build"):
        print("\n[+] Done! Visit http://localhost:8080/")
        print("[+] First visit will prompt to init database - click confirm")
    else:
        print("\n[-] Build failed. Check docker logs")
        sys.exit(1)

if __name__ == "__main__":
    main()
