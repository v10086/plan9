📃 开源协议
Apache License Version 2.0 see http://www.apache.org/licenses/LICENSE-2.0.html

# 一款简约 高效 可靠 的PHP web框架  
# 5分钟入门 10分钟精通


已用于视频cms、调研系统、金融支付、物联网等类型项目  

需要引入第三方类库的话直接composer 安装引入即可  

📃 开源协议
Apache License Version 2.0 — http://www.apache.org/licenses/LICENSE-2.0.html

## Plan9 — 简约、高效、可靠的 PHP Web 框架

本仓库是一款轻量级 PHP Web 框架，目标：极低学习成本、易扩展、在中小型服务/内部系统中能快速交付。

本 README 包含：安装、快速运行、目录说明、路由/控制器/视图示例、常用 helper 与库使用示例、开发建议与兼容性说明。

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
	library/
		RedisQueue.php
		WebSocketClient.php
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
- `app/library`：框架或业务层的工具类（比如 Redis 队列、WebSocket 客户端）
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
- logWrite：简单的日志写入函数（可替换为 Monolog）
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

## 扩展工具与第三方库（说明：`app/library` 已删除）

注意：当前代码库中已移除 `app/library` 目录（若你在早期分支或历史提交中看到了 `RedisQueue`、`WebSocketClient` 等实现，现已删除）。框架仍保留核心运行时与 helpers（见 `app/functions.php`、`app/controller`、`config` 等）。若需要队列、WebSocket 或其他工具，推荐以下替代方式：

- 使用 Composer 引入成熟库或客户端：
	- Redis/队列：`predis/predis`、`ext-redis` + 自己封装队列逻辑，或使用更完整的队列系统（如 RabbitMQ/Beanstalkd/队列驱动）。
	- WebSocket：若需要服务端 WebSocket，考虑 Swoole 或 Ratchet；若需要客户端，请使用现成的 WebSocket 客户端包。

示例：使用 Predis 做简单队列（示意）：

```php
// composer require predis/predis
$client = new \Predis\Client(['host'=>'127.0.0.1']);
$client->rpush('queue:job', json_encode($payload));
$item = $client->lpop('queue:job');
```

建议把工具封装成服务类（放在 `app/services` 或 `app/lib`），并通过简单工厂或容器注入到业务代码中，而不是放在全局 namespace 下。这样便于测试与替换。
---

## 日志与异常

- 当前使用 `logWrite()` 写入文件日志。建议引入 `monolog/monolog` 来实现分级日志、处理器（文件、stderr、远端）与格式化。
- 建议在 `public/index.php` 或 `app/bootstrap.php` 中统一捕获异常并以标准 JSON 返回（API）或渲染错误页（HTML），同时把错误记录到日志并（可选）上报到 Sentry/错误收集服务。

---

## 运行测试 / 静态检查（建议）

- 建议添加 `phpunit` 测试套件以及 `phpstan` 或 `psalm` 进行静态分析。测试与静态分析应当纳入 CI（例如 GitHub Actions）。

---

## 常见问题（FAQ）

- Q: 如何增加新的路由？
	A: 在 `config/route.php` 中添加一条映射，确保对应 `controller` 类存在并有对应方法。

- Q: 如何连接 Redis / MySQL？
	A: 在 `config/database.php` 中配置连接信息；在运行时通过 `dbnew()` 获取 PDO，或创建 Redis 客户端并赋给 `\library\RedisQueue::$redisHandler`。

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




