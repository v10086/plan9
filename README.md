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

读取配置：

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

---

## 9. 日志与错误处理

项目不再包含内置的 `logWrite()` 实现。建议改进：

- 使用 Monolog 提供更丰富的日志后端与分级处理。
- 在 `app/bootstrap.php` 中注册全局异常处理器，统一返回 API 格式化错误，并记录到日志。

(这些为推荐改进，非必要)

---

## 10. 部署建议

- 在生产环境使用 PHP-FPM + Nginx，静态文件由 Nginx 直接服务。
- 在部署前确保：
	- `composer install --no-dev --optimize-autoloader`
	- 环境变量配置就绪（不要提交密码）
	- 日志目录可写

---

## 11. 开发与测试建议

- 建议添加 PHPUnit 测试与静态分析工具（PHPStan/Psalm）并加入 CI。 
- 建议启用 `composer validate` 与 `composer audit` 检查依赖性问题。

---

## 12. 变更记录与兼容性

参见 `CHANGELOG.md`：

- 最近变更（2025-12-08）：删除 `app/library` 目录并对少数内部命名做了可兼容的调整（例如为 `RedisQueue` 添加了 `getQueues()` 并保留 `queues()`）。

如果你的代码依赖历史库实现，请在仓库历史中查找或还原到相应提交。

---

## 需要我帮忙的后续工作（可选）

- 为项目添加 `.env` 支持与示例（安全配置）
- 集成 Monolog 并使用其 API（替换或代替旧的内置日志实现）
- 添加基础 PHPUnit 测试与 GitHub Actions CI
- 迁移到 PSR-4 并配置 Composer autoload

你希望我现在执行哪项？
---

## 要求
- PHP >= 7.1（项目当前声明为 >=7.1，建议至少使用 7.4 或更高以获得更好语言特性与性能）
- Composer（用于依赖管理）

---

## 快速开始（开发环境）

1. 克隆仓库或使用 composer create-project（如果发布到 packagist）：

```powershell
git clone https://github.com/v10086/plan9.git
cd plan9
composer install
```

2. 启动内置 PHP 开发服务器（仅用于本地开发）:

```powershell
# 在项目根目录下运行（Windows PowerShell）
php -S localhost:8080 -t public
```

访问 http://localhost:8080/ 即可看到首页。

---

## 项目目录概览

根目录示例：

```
composer.json
README.md
app/
	bootstrap.php
	functions.php    # 全局 helper
	controller/
		Base.php
		Index.php
	view/
		index.html
config/
	database.php
	route.php
public/
	index.php
```

主要概念：
- `public/index.php`：框架入口（请求分发与输出处理）
- `config/`：配置文件（数据库、路由等）
- `app/controller`：控制器（业务入口）
- `app/view`：视图文件（简单 include/html）
- `app/functions.php`：包含若干全局 helper 函数（http、config、dbnew 等）

---

## 配置

- `config/database.php`：包含 `mysql` 与 `redis` 的示例配置。建议不要把敏感凭据提交到仓库，建议使用环境变量（.env）并在 `app/bootstrap.php` 中读取。

示例（简化）：

```php
return [
	'mysql' => [
		'default' => [ 'dsn' => 'mysql:host=127.0.0.1;dbname=xxx', 'user'=>'root', 'password'=>'' ]
	],
	'redis' => [ /* ... */ ]
];
```

---

## 路由与请求分发

- 路由定义在 `config/route.php`，格式：路由路径 => "控制器/类@方法"，例如：

```php
return [
	'/' => 'controller/Index@index',
	'/test' => 'controller/Index@test',
];
```

- 在 `public/index.php` 中会根据 `API`（请求 URI）查找路由并实例化对应控制器类、调用指定方法。控制器方法可返回数组（将被 encode 为 JSON）或字符串（作为 HTML 输出）。

示例：

```php
namespace controller;

class Index extends Base {
	public function index() {
		$title = '首页';
		include(VIEW_PATH . '/index.html');
	}

	public function test() {
		return ['code'=>1, 'msg'=>'ok'];
	}
}
```

---

## 视图

- 视图文件位于 `app/view`，当前项目使用简单的 PHP include 渲染静态 HTML 文件。为了安全建议对输出做转义或采用模板引擎（如 Twig）来避免 XSS。

---

## 全局 helper（常用函数）

文件 `app/functions.php` 提供了若干常用的辅助函数：

- http / httpProxy / getCurl / httpMulti：基于 cURL 的 HTTP 请求工具
- config($key)：读取 `config/*.php` 中的配置信息，参数格式如 `database.mysql.default`
- fileSave：保存文件到指定目录（自动创建）
- 日志：请使用 `monolog/monolog` 等成熟库进行日志分级与管理
- encrypt / decrypt：简单的加解密封装
- dbnew / dbexec：快速创建 PDO 连接并执行 SQL

示例（读取配置）:

```php
$dbConf = config('database.mysql.default');
$pdo = dbnew($dbConf);
$rows = dbexec('SELECT * FROM users WHERE id=?', [1], $pdo);
```

注意：这些是全局函数，改动会影响全仓库。若要现代化，建议逐步将它们封装为服务并通过容器注入。

---

## 重要说明

当前代码库的核心运行时位于 `public/index.php`、`app/` 与 `config/`。早期示例中的 `app/library` 已被移除，仓库中仅保留基础运行和全局 helper（见下文）。

本 README 仅包含运行与开发所需的最小说明，避免包含历史或已删除模块的说明。
---

## 日志与异常

- 项目不再提供内置 `logWrite()`。建议引入 `monolog/monolog` 来实现分级日志、处理器（文件、stderr、远端）与格式化。
- 建议在 `public/index.php` 或 `app/bootstrap.php` 中统一捕获异常并以标准 JSON 返回（API）或渲染错误页（HTML），同时把错误记录到日志并（可选）上报到 Sentry/错误收集服务。

---

## 运行测试 / 静态检查（建议）

- 建议添加 `phpunit` 测试套件以及 `phpstan` 或 `psalm` 进行静态分析。测试与静态分析应当纳入 CI（例如 GitHub Actions）。

---

## 常见问题（FAQ）

- Q: 如何增加新的路由？
	A: 在 `config/route.php` 中添加一条映射，确保对应 `controller` 类存在并有对应方法。

-- Q: 如何连接 Redis / MySQL？
	A: 在 `config/database.php` 中配置连接信息；在运行时通过 `dbnew()` 获取 PDO，或创建 Redis 客户端（例如使用 `predis/predis` 或 PHP 的 `ext-redis` 扩展）并在业务代码中直接使用或封装为服务。

---

## 兼容性与注意事项

- 我对库代码做过若干命名优化以提高可读性（例如：`RedisQueue::getQueues()` 新增并保持旧的 `queues()` 兼容；`WebSocketClient` 内部私有成员/方法去掉了前导下划线，仅改名不改逻辑）。外部调用公有方法不会受影响。
- 建议今后逐步迁移至 PSR-4 命名与 Composer autoload（可通过修改 `composer.json` 的 `autoload.psr-4` 并调整命名空间实现），并把全局 helper 逐步封装成服务以便注入与测试。

---

## 贡献与开发规范

- 如要贡献，请基于 `main` 分支创建 feature 分支并提交 PR。PR 中请包含变更说明与必要的测试用例。
- 建议遵循 PSR-12 代码风格、为新增功能添加单元测试，并在 PR 中确保 `composer install` 与基础静态检查通过。

---

如果你需要，我可以：
- 1) 帮你把 README 转为更详细的开发者文档（分章节）并生成 CHANGELOG；
- 2) 帮你添加 `.env` 支持、Monolog 集成示例或 PSR-4 autoload 的补丁。

谢谢使用 Plan9！




