<?php
namespace GlamourSchedule\Core;

class Router
{
    private array $routes = [];
    private array $groupStack = [];
    private ?string $subdomain = null;
    private array $mainDomains = ['glamourschedule.nl', 'glamourschedule.com', 'new.glamourschedule.nl', 'localhost'];

    public function get(string $path, string $action): void
    {
        $this->addRoute('GET', $path, $action);
    }

    public function post(string $path, string $action): void
    {
        $this->addRoute('POST', $path, $action);
    }

    public function group(array $attributes, callable $callback): void
    {
        $this->groupStack[] = $attributes;
        $callback($this);
        array_pop($this->groupStack);
    }

    private function addRoute(string $method, string $path, string $action): void
    {
        $middleware = [];
        foreach ($this->groupStack as $group) {
            if (isset($group['middleware'])) {
                $middleware[] = $group['middleware'];
            }
        }

        $this->routes[] = [
            'method' => $method,
            'path' => $path,
            'action' => $action,
            'middleware' => $middleware
        ];
    }

    /**
     * Extract subdomain from the host
     * Returns null if it's a main domain, or the subdomain slug
     */
    private function extractSubdomain(): ?string
    {
        $host = $_SERVER['HTTP_HOST'] ?? '';

        // Remove port if present
        $host = preg_replace('/:\d+$/', '', $host);

        // Check if it's a main domain (no subdomain routing)
        foreach ($this->mainDomains as $mainDomain) {
            if ($host === $mainDomain || $host === 'www.' . $mainDomain) {
                return null;
            }
        }

        // Extract subdomain from host like "salon-elegance.glamourschedule.nl"
        foreach (['glamourschedule.nl', 'glamourschedule.com'] as $baseDomain) {
            if (str_ends_with($host, '.' . $baseDomain)) {
                $subdomain = str_replace('.' . $baseDomain, '', $host);
                // Skip www subdomain
                if ($subdomain !== 'www' && $subdomain !== 'new') {
                    return $subdomain;
                }
            }
        }

        return null;
    }

    /**
     * Get the current subdomain (business slug) if any
     */
    public function getSubdomain(): ?string
    {
        return $this->subdomain;
    }

    public function dispatch(string $method, string $uri): string
    {
        $uri = parse_url($uri, PHP_URL_PATH);
        $uri = rtrim($uri, '/') ?: '/';

        // Check for subdomain-based business routing
        $this->subdomain = $this->extractSubdomain();

        if ($this->subdomain !== null) {
            // Route to business page via subdomain
            return $this->handleSubdomainRequest($method, $uri, $this->subdomain);
        }

        // Treat HEAD requests as GET
        $routeMethod = ($method === 'HEAD') ? 'GET' : $method;

        foreach ($this->routes as $route) {
            if ($route['method'] !== $routeMethod) {
                continue;
            }

            $pattern = $this->convertToRegex($route['path']);

            if (preg_match($pattern, $uri, $matches)) {
                array_shift($matches);
                return $this->callAction($route['action'], $matches, $route['middleware']);
            }
        }

        http_response_code(404);
        return $this->render404();
    }

    private function convertToRegex(string $path): string
    {
        $pattern = preg_replace('/\{([a-zA-Z]+)\}/', '([^/]+)', $path);
        return '#^' . $pattern . '$#';
    }

    private function callAction(string $action, array $params, array $middleware): string
    {
        // Check middleware
        foreach ($middleware as $mw) {
            $result = $this->checkMiddleware($mw);
            if ($result !== true) {
                return $result;
            }
        }

        [$controller, $method] = explode('@', $action);
        $controllerClass = "GlamourSchedule\\Controllers\\{$controller}";

        if (!class_exists($controllerClass)) {
            return $this->renderWelcome();
        }

        $instance = new $controllerClass();
        return call_user_func_array([$instance, $method], $params);
    }

    /**
     * @return bool|string
     */
    private function checkMiddleware(string $middleware)
    {
        switch ($middleware) {
            case 'auth':
                if (!isset($_SESSION['user_id'])) {
                    header('Location: /login');
                    exit;
                }
                return true;

            case 'business':
                // Check if logged in as business directly
                if (isset($_SESSION['business_id'])) {
                    return true;
                }
                // Or check if user has a business linked
                if (isset($_SESSION['user_id'])) {
                    try {
                        $config = require BASE_PATH . '/config/config.php';
                        $db = new Database($config['database']);
                        $business = $db->fetch(
                            "SELECT id, is_verified FROM businesses WHERE user_id = ?",
                            [$_SESSION['user_id']]
                        );
                        if ($business) {
                            $_SESSION['business_id'] = $business['id'];
                            return true;
                        }
                    } catch (\Exception $e) {
                        // Fall through to redirect
                    }
                }
                header('Location: /login');
                exit;

            default:
                return true;
        }
    }

    /**
     * Handle requests coming through a business subdomain
     * Routes to the business page or booking page based on the URI
     */
    private function handleSubdomainRequest(string $method, string $uri, string $slug): string
    {
        $routeMethod = ($method === 'HEAD') ? 'GET' : $method;

        // Map subdomain routes to regular routes with the business slug
        // / -> BusinessController@show (show business page)
        // /book -> BookingController@create
        // /book (POST) -> BookingController@store
        // Other paths fall through to regular routing

        if ($uri === '/' && $routeMethod === 'GET') {
            // Show business page
            return $this->callAction('BusinessController@show', [$slug], []);
        }

        if ($uri === '/book') {
            if ($routeMethod === 'GET') {
                return $this->callAction('BookingController@create', [$slug], []);
            }
            if ($routeMethod === 'POST') {
                return $this->callAction('BookingController@store', [$slug], []);
            }
        }

        // For API routes and other paths, try regular routing
        foreach ($this->routes as $route) {
            if ($route['method'] !== $routeMethod) {
                continue;
            }

            $pattern = $this->convertToRegex($route['path']);

            if (preg_match($pattern, $uri, $matches)) {
                array_shift($matches);
                return $this->callAction($route['action'], $matches, $route['middleware']);
            }
        }

        // If no route matches, show 404
        http_response_code(404);
        return $this->render404();
    }

    private function render404(): string
    {
        return '<h1>404 - Pagina niet gevonden</h1>';
    }

    private function renderWelcome(): string
    {
        return $this->getWelcomePage();
    }

    private function getWelcomePage(): string
    {
        return '<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>GlamourSchedule - Beauty Booking Platform</title>
    <link rel="stylesheet" href="/css/theme.css">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .container {
            background: #ffffff;
            border-radius: 20px;
            padding: 60px;
            text-align: center;
            box-shadow: 0 25px 50px -12px rgba(0,0,0,0.25);
            max-width: 600px;
        }
        h1 { color: #000000; font-size: 2.5rem; margin-bottom: 20px; }
        .subtitle { color: #666; font-size: 1.2rem; margin-bottom: 40px; }
        .features { text-align: left; margin: 30px 0; }
        .feature { display: flex; align-items: center; margin: 15px 0; }
        .feature-icon {
            width: 40px; height: 40px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 10px;
            display: flex; align-items: center; justify-content: center;
            margin-right: 15px;
            color: white;
        }
        .domains {
            background: #f8f9fa;
            border-radius: 10px;
            padding: 20px;
            margin-top: 30px;
        }
        .domain {
            display: inline-block;
            background: #667eea;
            color: white;
            padding: 8px 16px;
            border-radius: 20px;
            margin: 5px;
            font-weight: 500;
        }
        .status {
            margin-top: 30px;
            padding: 15px;
            background: #d4edda;
            border-radius: 10px;
            color: #155724;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>GlamourSchedule</h1>
        <p class="subtitle">Beauty Booking Platform</p>

        <div class="features">
            <div class="feature">
                <div class="feature-icon">NL</div>
                <span>Meertalig platform (NL, EN, DE, FR)</span>
            </div>
            <div class="feature">
                <div class="feature-icon">$</div>
                <span>Bedrijven: EUR 99,99 (eerste 100: EUR 0,99)</span>
            </div>
            <div class="feature">
                <div class="feature-icon">%</div>
                <span>Administratiekosten: EUR 1,75 per boeking</span>
            </div>
        </div>

        <div class="domains">
            <strong>Actieve Domeinen:</strong><br>
            <span class="domain">glamourschedule.nl</span>
            <span class="domain">glamourschedule.com</span>
        </div>

        <div style="margin-top:30px;padding:25px;background:linear-gradient(135deg,#333333,#000000);border-radius:15px;color:white">
            <h3 style="margin:0 0 10px 0">Word Sales Partner</h3>
            <p style="margin:0 0 15px 0;opacity:0.9">Verdien commissie door bedrijven aan te melden</p>
            <a href="/sales/register" style="display:inline-block;background:#ffffff;color:#000000;padding:10px 25px;border-radius:8px;text-decoration:none;font-weight:600">Start nu</a>
        </div>

        <div class="status">
            Installatie succesvol! Platform is klaar voor configuratie.
        </div>
    </div>
</body>
</html>';
    }
}
