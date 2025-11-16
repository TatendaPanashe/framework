<?php

namespace Kodomo\Core;

class App
{
    public Router $router;
    public Request $request;
    public Response $response;
    public static ?Database $database = null;

    public function __construct()
    {
        $this->router = new Router();
        $this->request = new Request();
        $this->response = new Response();
        
        // Initialize database if config exists
        $this->initializeDatabase();
    }

    public function run()
    {
        try {
            $output = $this->router->resolve($this->request, $this->response);
            echo $output;
        } catch (\Exception $e) {
            $this->handleException($e);
        }
    }

    private function initializeDatabase()
    {
        $configPath = __DIR__ . '/../../config/database.php';
        
        if (file_exists($configPath)) {
            $config = require $configPath;
            $defaultConnection = $config['connections'][$config['default']];
            
            // Convert to Kodomo format
            $dbConfig = [
                'dsn' => $this->buildDsn($defaultConnection),
                'user' => $defaultConnection['username'] ?? '',
                'password' => $defaultConnection['password'] ?? ''
            ];
            
            self::$database = new Database($dbConfig);
        }
    }

    private function buildDsn($connection)
    {
        switch ($connection['driver']) {
            case 'mysql':
                return "mysql:host={$connection['host']};dbname={$connection['database']};charset={$connection['charset']}";
            case 'sqlite':
                return "sqlite:{$connection['database']}";
            case 'pgsql':
                return "pgsql:host={$connection['host']};dbname={$connection['database']}";
            default:
                throw new \Exception("Unsupported database driver: {$connection['driver']}");
        }
    }

    private function handleException(\Exception $e)
    {
        if (php_sapi_name() === 'cli') {
            echo "Error: " . $e->getMessage() . "\n";
        } else {
            $this->response->setStatusCode(500);
            
            if (file_exists(__DIR__ . '/../../src/Views/errors/500.php')) {
                echo view('errors.500', ['error' => $e->getMessage()]);
            } else {
                echo "<h1>500 Internal Server Error</h1>";
                echo "<p>" . htmlspecialchars($e->getMessage()) . "</p>";
            }
        }
    }

    public static function db()
    {
        return self::$database;
    }
}