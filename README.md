📃 开源协议
Apache License Version 2.0 see http://www.apache.org/licenses/LICENSE-2.0.html

# 一款简约 高效 可靠 的PHP web框架  
# 5分钟入门 10分钟精通


已用于视频cms、调研系统、金融支付、物联网等类型项目  

需要引入第三方类库的话直接composer 安装引入即可  

## Plan9 — 使用文档

这份文档基于当前仓库代码（2025-12-08），覆盖：项目概览、安装、目录结构、配置与运行、路由/控制器/视图、常用 helper、部署建议、开发规范与变更说明。

如果你需要把文档进一步拆分为开发者手册或 API 文档，我可以继续扩展。

---

## 1. 项目概览

Plan9 是一个极简的 PHP Web 框架。它的设计目标是低学习成本、易于嵌入到小型服务或内部工具中。核心特点：

- 单文件入口（`public/index.php`）负责路由匹配与响应输出。
- 配置以 PHP 文件形式放在 `config/`。
- 控制器采用类/方法映射，放在 `app/controller/`。
- 提供若干全局 helper（`app/functions.php`）以便快速开发。

注意：此前仓库中存在 `app/library`，用于一些工具类（例如 RedisQueue、WebSocketClient），但在当前分支这些已被删除。请查看 `CHANGELOG.md` 获取历史变更记录。

## 核心设计带来的性能优势

- 极简启动与低初始化开销：单文件入口（`public/index.php`）和少量引导逻辑减少每次请求需要 include/解析的文件数，冷启动更快。
- 简单高效的路由分发：当前使用静态路由映射（数组查找）进行分发，避免复杂正则或深层中间件链，查找成本非常低。
- 更少的运行时对象与依赖：默认不注入大量服务或使用反射/大量对象实例化，降低内存分配和垃圾回收压力，提升吞吐。
- 直接渲染视图：默认通过 `include` 渲染视图文件，减少模板解析/编译开销（如需要模板引擎，可按需引入并启用缓存）。
- 贴近底层的 I/O 与数据库处理：使用 PDO 并合理配置（例如关闭 emulate prepares、开启异常模式）可减少抽象层带来的额外开销。

这些设计在常见的 CRUD/API 场景下能带来更低的响应延迟和更高的 requests/sec，尤其在高并发、短请求生命周期的场景中优势明显。

---

## 2. 环境要求

- PHP >= 7.1（建议 >= 7.4）
- Composer

可选（按需）：MySQL、Redis 等服务

---

## 3. 安装与快速运行

克隆并安装依赖：

```powershell
git clone <repo-url> plan9
cd plan9
composer install
```

本地开发（使用 PHP 内置服务器）：

```powershell
php -S localhost:8080 -t public
```

打开浏览器并访问 `http://localhost:8080/`。

---

## 4. 目录结构（说明）

主要文件/目录：

- `public/`：Web 入口（`public/index.php`）
- `app/bootstrap.php`：框架初始化（常量定义、autoload 引导、bootstrap 注释位置）
- `app/functions.php`：全局 helper（HTTP 工具、config 读取、日志、DB 快速创建等）
- `app/controller/`：控制器目录（示例：`Index.php`）
- `app/view/`：视图文件（HTML/PHP）
- `config/`：配置目录（`database.php`、`route.php` 等）
- `README.md`, `CHANGELOG.md`：项目说明与变更记录


---

## 5. 配置

配置以 PHP 数组的形式放在 `config/` 下。示例：`config/database.php`：

```php
return [
	'mysql' => [
		'default' => [
			'dsn' => 'mysql:host=127.0.0.1;dbname=yourdb;charset=utf8mb4',
			'user' => 'root',
			'password' => ''
		],
	],
	'redis' => [ /* ... */ ]
];
```

```php
$val = config('database.mysql.default');
```

注意：不要在仓库中直接提交敏感凭据，建议采用环境变量（`.env`）并在 `app/bootstrap.php` 中读取。

---

## 6. 路由与控制器

路由定义在 `config/route.php`，格式为 `path => 'controller/Name@method'`。

示例：

```php
return [
	'/' => 'controller/Index@index',
	'/test' => 'controller/Index@test',
];
```

在 `public/index.php` 中，框架根据请求 URI 查找路由，实例化对应类并调用方法：

- 控制器类位于 `app/controller`，命名空间为 `controller`。
- 控制器方法可以返回数组（将被编码为 JSON）或字符串（作为 HTML 输出）。

示例控制器：`app/controller/Index.php`

```php
namespace controller;

class Index extends Base {
	public function index() {
		include(VIEW_PATH . '/index.html');
	}
	public function test() {
		return ['code'=>1, 'msg'=>'ok'];
	}
}
```

---

## 7. 视图

视图放在 `app/view`，当前框架使用直接 `include` 渲染静态 HTML。为防止 XSS，建议在渲染前对用户数据做转义，或迁移到模板引擎（例如 Twig）以自动转义。

---

## 8. 常用 helper（`app/functions.php`）

该文件包含：

- HTTP 客户端工具：`http`, `httpProxy`, `getCurl`, `httpMulti`
- 配置读取：`config($key)`
- 文件保存：`fileSave()`
- 日志写入：已移除内置实现，建议使用 `monolog/monolog` 等成熟库进行日志分级与管理
- 加解密：`encrypt`, `decrypt`
- 数据库辅助：`dbnew($config)`、`dbexec($sql, $params=[], $db=null)`（基于 PDO）

示例：创建 PDO 并执行查询：

```php
$db = dbnew(config('database.mysql.default'));
$rows = dbexec('SELECT * FROM users WHERE id=?', [1], $db);
```

注意：这些是全局函数。未来建议将其封装为服务并通过容器注入以便测试与替换。

### 使用 `dbnew()` 与 `dbexec()` 的示例（增删改查）

`dbnew($config)` 会返回一个已配置的 PDO 实例；`dbexec($sql, $params = [], $db = null)` 在不传 `$db` 的情况下会使用默认配置（`database.mysql.default`）。

- SELECT（查询）示例：

```php
try {
	// 使用默认配置
	$rows = dbexec('SELECT id, name FROM users WHERE status = ?', [1]);
	foreach ($rows as $row) {
		echo $row['id'] . ': ' . $row['name'] . "\n";
	}
} catch (\Throwable $e) {
	// dbnew 已设置 PDO::ERRMODE_EXCEPTION，错误会抛出异常
	error_log($e->getMessage());
}
```

- INSERT（新增）示例：

```php
try {
	$sql = 'INSERT INTO users (name, email, status) VALUES (?, ?, ?)';
	$affected = dbexec($sql, ['Alice', 'alice@example.com', 1]);
	// 对于非 SELECT，dbexec 返回受影响行数（rowCount）
	echo "Inserted rows: " . $affected;
} catch (\Throwable $e) {
	error_log($e->getMessage());
}
```

- UPDATE（更新）示例：

```php
try {
	$sql = 'UPDATE users SET status = ? WHERE id = ?';
	$affected = dbexec($sql, [0, 123]);
	echo "Updated rows: " . $affected;
} catch (\Throwable $e) {
	error_log($e->getMessage());
}
```

- DELETE（删除）示例：

```php
try {
	$sql = 'DELETE FROM users WHERE id = ?';
	$affected = dbexec($sql, [123]);
	echo "Deleted rows: " . $affected;
} catch (\Throwable $e) {
	error_log($e->getMessage());
}
```

说明：
- `dbexec` 对于 SELECT 返回结果数组（fetchAll），对于 INSERT/UPDATE/DELETE 返回受影响的行数。
- 当你需要多次复用连接或在事务中执行多个语句时，可先用 `dbnew(config('database.mysql.default'))` 获取 PDO 实例并把它传给 `dbexec(..., $db)`，或在 PDO 上手动管理事务。

### 查询单行或单字段的常见用法

要从 `dbexec` 的返回结果中取得单行或单字段，常见做法是对 SQL 加上 `LIMIT 1`，然后检查结果数组的第一项：

- 查询单行（返回一条记录）：

```php
$rows = dbexec('SELECT id, name, email FROM users WHERE id = ? LIMIT 1', [123]);
$row = $rows[0] ?? null;
if ($row) {
	// 使用 $row['field'] 访问字段
	echo $row['name'];
} else {
	// 未找到记录
}
```

- 查询单字段（只需要一个值，例如某条记录的某个列）：

```php
$rows = dbexec('SELECT email FROM users WHERE id = ? LIMIT 1', [123]);
$email = $rows[0]['email'] ?? null;
echo $email;
```


- Q: 如何增加新的路由？
	A: 在 `config/route.php` 中添加一条映射，确保对应 `controller` 类存在并有对应方法。

-- Q: 如何连接 Redis / MySQL？
	A: 在 `config/database.php` 中配置连接信息；在运行时通过 `dbnew()` 获取 PDO，或创建 Redis 客户端（例如使用 `predis/predis` 或 PHP 的 `ext-redis` 扩展）并在业务代码中直接使用或封装为服务。

---




