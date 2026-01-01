# Plan9 - 极简、高性能 PHP 框架

![Plan9 Logo](https://via.placeholder.com/150x50?text=Plan9)

> 🚀 5分钟入门，10分钟精通，QPS 是传统框架的 5-10 倍

## 📖 项目简介

Plan9 是一款设计极简的 PHP Web 框架，专注于极致性能与开发效率。它摒弃了繁重的 IOC 容器和 ORM，通过原生 SQL 与高效的连接管理，将 Web 响应耗时压低到毫秒级。

## 💡 设计理念

### 为什么不使用 ORM？
Plan9 选择直接使用 SQL 而不集成 ORM，主要基于以下考虑：
- **性能优势**：原生 SQL 避免了 ORM 带来的额外开销和性能损耗
- **灵活性**：直接编写 SQL 可以更精确地控制查询逻辑和性能优化
- **学习成本**：减少了学习特定 ORM 框架的成本，开发者可以专注于 SQL 和业务逻辑
- **可维护性**：SQL 语句更加直观，便于调试和维护

### 技术选型考量
在 Plan9 + GatewayWorker 与 Hyperf 的技术选型对比中，Plan9 展现出以下优势：
- **轻量级**：适合快速开发和部署小型服务
- **低资源占用**：在资源受限环境下表现更优
- **易于集成**：可以轻松与 GatewayWorker 等工具结合
- **高性能**：在高并发场景下具有显著的性能优势

详细讨论可参考豆包 AI 文档：
- [ORM 与直接写 SQL 核心讨论归档](https://www.doubao.com/doc/TcoYfB3yGd0IJyciw2jciSp7nBh?enter_from=public_link#)
- [Plan9 + GatewayWorker 与 Hyperf 技术选型讨论总结归档](https://www.doubao.com/doc/PbBLfm7xBdVc5JcLyvWcYPHynLc?enter_from=public_link#)

## ✨ 核心特性

### 性能极致
- **极速响应**：无容器初始化，启动开销极低
- **高效路由**：静态路由映射，避免复杂正则匹配
- **轻量级设计**：核心代码仅几百行，几乎零额外开销
- **资源友好**：低内存占用，高并发处理能力

### 开发友好
- **极简 API**：直观的函数式调用风格
- **原生 SQL**：直接操作数据库，性能可控
- **自动重连**：内置数据库断开自动重连机制
- **类型安全**：强制关闭模拟预编译，保持数据原始类型

### 稳定可靠
- **长连接支持**：静态连接复用与手动关闭
- **异常处理**：完整的错误捕获与日志记录
- **灵活扩展**：支持 Composer 引入第三方库

## 🚀 快速开始

### 环境要求
- PHP >= 7.1（建议 PHP >= 7.4）
- Composer

### 安装

```bash
git clone <仓库地址> plan9
cd plan9
composer install
```

### 启动开发服务器

```bash
php -S localhost:8080 -t public
```

访问 `http://localhost:8080/` 即可看到示例页面

## 📁 目录结构

```
plan9/
├── app/
│   ├── bootstrap.php      # 框架初始化
│   └── controller/        # 控制器目录
├── config/               # 配置文件
│   ├── database.php      # 数据库配置
│   └── route.php         # 路由配置
├── function/             # 全局工具函数
│   ├── core.php          # 核心功能
│   ├── db.php            # 数据库操作
│   └── helper.php        # 辅助函数
├── public/               # Web 入口
│   └── index.php         # 单文件入口
├── composer.json         # Composer 配置
├── CHANGELOG.md          # 变更记录
└── README.md             # 项目文档
```

## 🎯 核心功能

### 1. 路由与控制器

#### 定义路由
在 `config/route.php` 中配置路由：

```php
return [
    '/' => 'controller/Index@index',        // 首页
    '/api/users' => 'controller/User@list', // 用户列表接口
];
```

#### 创建控制器
在 `app/controller/` 目录下创建控制器：

```php
// app/controller/Index.php
namespace controller;

class Index {
    public function index() {
        return '欢迎使用 Plan9 框架！';
    }
}
```

#### 响应类型
- 返回字符串：直接输出 HTML
- 返回数组：自动转换为 JSON

```php
// 返回 HTML
public function html() {
    return '<h1>Hello Plan9</h1>';
}

// 返回 JSON
public function json() {
    return [
        'code' => 200,
        'message' => 'success',
        'data' => ['name' => 'Plan9']
    ];
}
```

### 2. 数据库操作

#### 配置数据库
在 `config/database.php` 中配置数据库连接：

```php
return [
    'mysql' => [
        'default' => [
            'dsn' => 'mysql:host=127.0.0.1;dbname=test;charset=utf8mb4',
            'user' => 'root',
            'password' => ''
        ]
    ]
];
```

#### 执行 SQL

```php
// 查询数据
$users = dbexec('SELECT * FROM users WHERE status = ?', [1]);

// 插入数据
dbexec('INSERT INTO users (name, email) VALUES (?, ?)', ['张三', 'zhangsan@example.com']);

// 更新数据
dbexec('UPDATE users SET status = ? WHERE id = ?', [0, 1]);

// 删除数据
dbexec('DELETE FROM users WHERE id = ?', [1]);

// 获取最后插入的 ID
$id = dbexec('lastInsertId');
```

#### 事务处理

```php
try {
    // 开始事务
    $db = dbnew(config('database.mysql.default'));
    $db->beginTransaction();
    
    // 执行多个操作
    dbexec('INSERT INTO orders (...) VALUES (...)', [...], $db);
    dbexec('UPDATE inventory SET quantity = quantity - 1 WHERE id = ?', [1], $db);
    
    // 提交事务
    $db->commit();
} catch (\Throwable $e) {
    // 回滚事务
    $db->rollBack();
    error_log($e->getMessage());
}
```

#### 异步并发查询

```php
$dbConfig = [
    'host' => '127.0.0.1',
    'user' => 'root',
    'password' => '',
    'database' => 'test'
];

$sqls = [
    'user' => 'SELECT * FROM users WHERE id = ?',
    'order' => 'SELECT * FROM orders WHERE user_id = ?',
    'product' => 'SELECT * FROM products LIMIT 10'
];

$results = asyncMysqliBatch($dbConfig, $sqls, 3);
```

### 3. 配置管理

#### 读取配置

```php
// 获取整个数据库配置
$dbConfig = config('database');

// 获取默认数据库配置
$defaultDb = config('database.mysql.default');

// 获取嵌套配置
$dbHost = config('database.mysql.default.dsn');
```

### 4. 常用工具函数

#### HTTP 请求

```php
// 发送 GET 请求
$response = http('https://api.example.com/users');

// 发送 POST 请求
$response = http('https://api.example.com/users', [
    'method' => 'POST',
    'body' => ['name' => 'Test', 'email' => 'test@example.com'],
    'headers' => ['Content-Type' => 'application/json']
]);
```

#### 文件操作

```php
// 保存文件
fileSave('/path/to/file.txt', 'Hello Plan9');

// 加密数据
$encrypted = encrypt('secret data', 'your-key');

// 解密数据
$decrypted = decrypt($encrypted, 'your-key');
```

## 📊 性能对比

| 框架 | QPS 预估区间 | 启动开销 | 路由性能 | 中间件 | ORM | 特点 |
|------|------------|---------|---------|--------|-----|------|
| Plan9 | 6,000-12,000 | 极低 | 极快 | 无 | 无 | ⚡ 极轻量、高性能 |
| Slim | 4,000-8,000 | 低 | 快 | 少量 | 无 | 轻量级 |
| Lumen | 2,500-5,000 | 中等 | 中等 | 简化版 | 有 | Laravel 微框架 |
| Laravel | 500-1,500 | 高 | 中/慢 | 丰富 | 有 | 全功能框架 |

### 🔍 为什么 Plan9 明显更快？

#### ✔️ 1. 无 IOC 容器（Laravel 最大开销来源）
Laravel/Lumen 的服务容器会构建、绑定、解析大量类 → 资源消耗大
Plan9：基本无容器 → 直接执行，启动极快

#### ✔️ 2. 无 ORM
Eloquent 是“方便但慢”的典型代表，存在额外的对象映射开销
Plan9/Slim 使用原生 PDO → 直接操作数据库，性能更优

#### ✔️ 3. 无中间件处理链
Laravel 一次请求可能经过 8~20 个中间件 → 处理链长
Plan9 只有路由 → 控制器 → 响应，路径最短

#### ✔️ 4. 极简设计带来的综合优势
- **更少的运行时对象与依赖**：默认不注入大量服务或使用反射/大量对象实例化
- **直接的视图渲染**：使用 PHP include（原生），减少模板解析/编译开销
- **贴近底层的 I/O 与数据库处理**：使用 PDO 并合理配置，减少抽象层带来的额外开销

这些设计在常见的 CRUD/API 场景下能带来更低的响应延迟和更高的 requests/sec，尤其在高并发、短请求生命周期的场景中优势明显。

## 🚀 部署建议

### 生产环境

#### 使用 Nginx

```nginx
server {
    listen 80;
    server_name example.com;
    root /path/to/plan9/public;
    index index.php;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        fastcgi_pass 127.0.0.1:9000;
        fastcgi_index index.php;
        fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
        include fastcgi_params;
    }
}
```

#### 使用 PHP-FPM

```bash
# 安装 PHP-FPM
sudo apt-get install php7.4-fpm

# 启动 PHP-FPM
sudo systemctl start php7.4-fpm
```

#### 启用 OPcache

在 `php.ini` 中启用 OPcache：

```ini
[opcache]
zend_extension=opcache.so
opcache.enable=1
opcache.enable_cli=1
opcache.memory_consumption=128
opcache.interned_strings_buffer=8
opcache.max_accelerated_files=10000
opcache.validate_timestamps=1
opcache.revalidate_freq=60
```

## 📝 开发规范

1. **命名规范**：
   - 文件名：首字母大写的驼峰命名
   - 类名：首字母大写的驼峰命名
   - 方法名：首字母小写的驼峰命名
   - 变量名：下划线分隔或驼峰命名

2. **代码风格**：
   - 使用 4 个空格缩进
   - 遵循 PSR-12 代码规范
   - 保持函数简洁，单一职责

3. **安全规范**：
   - 始终使用参数化查询
   - 验证用户输入
   - 过滤输出内容
   - 不要在代码中硬编码敏感信息

## 🔧 扩展建议

### 日志系统
建议使用 `monolog/monolog`：

```bash
composer require monolog/monolog
```

### 缓存系统
建议使用 `predis/predis`：

```bash
composer require predis/predis
```

### 验证系统
建议使用 `respect/validation`：

```bash
composer require respect/validation
```

## 📄 许可证

Apache License Version 2.0

## 🤝 贡献

欢迎提交 Issue 和 Pull Request！

## 📞 支持

如有问题，请查看 [CHANGELOG.md](CHANGELOG.md) 或提交 Issue。

---

**Plan9** - 让 PHP 开发更简单、更高效！🚀






