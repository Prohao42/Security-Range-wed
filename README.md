<div align="center">

# 炸炸酥网安靶场

**专注网安实战，掌控安全**

[![License: GPL v3](https://img.shields.io/badge/License-GPL%20v3-blue.svg)](LICENSE)
[![PHP 7.3.4](https://img.shields.io/badge/PHP-7.3.4-777BB4?logo=php&logoColor=white)](https://php.net)
[![MySQL 5.7+](https://img.shields.io/badge/MySQL-5.7+-4479A1?logo=mysql&logoColor=white)](https://mysql.com)

</div>

---

## 简介

炸炸酥网安靶场是一款开源 WEB 安全学习平台，包含 80+ 独立靶场，覆盖从 HTTP 协议基础到业务逻辑漏洞的完整学习路径。

## 靶场覆盖

| 分类 | 内容 | 数量 |
|------|------|:----:|
| WEB安全基础知识 | HTTP协议、前端代码、服务端语言 | 16 |
| 输入验证类漏洞 | XSS、SQLi、RCE、文件上传、XXE、反序列化 | 42 |
| 业务逻辑类漏洞 | 绕过验证、越权访问、交易篡改 | 26 |
| 综合实战 | 商城系统 | 1 |

## 安装教程

### 环境要求

| 组件 | 版本 | 备注 |
|:---:|:---:|------|
| PHP | 7.3.4 | 版本过低无法运行，过高可能影响漏洞利用 |
| MySQL | 5.7+ | 推荐 5.7 |
| Apache | 2.4+ | 必须使用 Apache，不支持 Nginx |
| 操作系统 | Windows / Linux | Windows 推荐 |

---

### 方式一：PHPStudy 部署（推荐新手）

#### 第一步：下载安装 PHPStudy

1. 访问 [https://m.xp.cn/phpstudy](https://m.xp.cn/phpstudy)，下载 **phpStudy v8.1** 版本
2. 双击安装包，按照提示完成安装
3. **注意**：安装路径**不要包含中文字符**，否则可能导致异常

#### 第二步：启动服务

1. 打开 PHPStudy 控制面板
2. 在「一键启动」区域，鼠标悬停在 **WNMP** 上
3. 点击出现的**切换按钮**，将 WEB 服务切换为 **Apache**
4. 点击 **WNMP** 启动服务

#### 第三步：部署源码

1. 点击左侧「网站」选项卡
2. 找到**物理路径**位置，删除目录下的所有文件
3. 将本靶场的所有源码文件复制到该目录下
4. 通常是将源码 zip 包解压后放入物理路径目录

#### 第四步：访问靶场

1. 打开浏览器，输入 `http://localhost/`
2. 首次访问会自动弹出**数据库初始化提示**
3. 点击「确认初始化」按钮，等待完成
4. 初始化完成后即可正常使用

---

### 方式二：Docker 部署

#### 前置条件

- 已安装 Docker 和 Docker Compose
- Windows 用户安装 [Docker Desktop](https://www.docker.com/products/docker-desktop/)
- Linux 用户安装 Docker Engine

#### 第一步：配置镜像加速器（国内用户）

**Windows（Docker Desktop）：**

1. 打开 Docker Desktop → Settings → Docker Engine
2. 在 JSON 配置中添加：
   ```json
   {
     "registry-mirrors": [
       "https://docker.1ms.run",
       "https://docker.m.daocloud.io"
     ]
   }
   ```
3. 保存并重启 Docker

**Linux：**

```bash
# 编辑或创建配置文件
sudo mkdir -p /etc/docker
sudo tee /etc/docker/daemon.json <<-'EOF'
{
  "registry-mirrors": [
    "https://docker.1ms.run",
    "https://docker.m.daocloud.io"
  ]
}
EOF

# 重启 Docker 服务
sudo systemctl daemon-reload
sudo systemctl restart docker
```

#### 第二步：构建并启动

```bash
# 进入项目根目录（docker-compose.yml 所在目录）
cd HeaSecWebLab-master

# 首次构建并启动（需要 3-5 分钟编译 PHP 扩展）
docker compose up -d --build
```

#### 第三步：访问靶场

1. 打开浏览器，访问 `http://localhost:8080/`
2. 首次访问会自动提示初始化数据库
3. 点击确认即可

#### Docker 常用命令

```bash
# 启动（已构建过则不需要 --build）
docker compose up -d

# 停止
docker compose down

# 重置靶场（删除数据卷，下次启动时恢复初始状态）
docker compose down -v

# 查看日志
docker compose logs -f

# 重新构建（修改代码后）
docker compose up -d --build
```

---

### 数据库配置

- 配置文件位于 `config/config.json`
- 默认适配本地 PHPStudy 环境，无需额外配置
- 数据库前缀：`zhazhasu_`
- `zhazhasu_cms` 为前台数据库，其他为靶场数据库
- 前台页面右上角支持一键重置数据库

---

### 常见问题

**Q：访问页面显示空白或报错？**

- 检查 PHP 版本是否为 7.3.4
- 检查 Apache 是否已启动
- 检查 MySQL 服务是否正常运行

**Q：数据库初始化失败？**

- 确认 MySQL 用户有创建数据库的权限
- 检查 `config/config.json` 中的数据库配置是否正确

**Q：Docker 构建失败？**

- 检查 Docker 镜像加速器是否配置正确
- 确认网络连接正常，能访问 Docker Hub

**Q：靶场页面 404？**

- 确认 Apache 已启用 mod_rewrite 模块
- 检查源码是否完整复制到网站根目录

## 安全警告

> ⚠️ 本平台**故意包含大量已知安全漏洞**，仅适合在本地隔离环境部署。**切勿直接部署于互联网**，否则极易导致服务器被非法入侵。因不当部署引发的安全事件及法律责任，由部署者自行承担。

## 许可证

[GNU General Public License v3.0](LICENSE) — 衍生作品必须以 GPL-3.0 协议发布。

项目来源于二次开发 https://github.com/HeaSec/HeaSecWebLab

## 联系方式

推特：[https://x.com/Fakerrf5](https://x.com/Fakerrf5)

飞机：[https://t.me/Prohao42](https://t.me/Prohao42)

抖音：[https://www.douyin.com/user/self?from_tab_name=main&showSubTab=video&showTab=post](https://www.douyin.com/user/self?from_tab_name=main&showSubTab=video&showTab=post)

知识星球：[https://t.zsxq.com/FGUeq](https://t.zsxq.com/FGUeq)

知识星球有更强的版本，更多的渗透技能

