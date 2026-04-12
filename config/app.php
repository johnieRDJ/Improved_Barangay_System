<?php
date_default_timezone_set('Asia/Manila');

$defaultConfig = [
    'app_url' => '',
    'database' => [
        'host' => 'localhost',
        'username' => 'root',
        'password' => '',
        'name' => 'barangay_db',
    ],
    'mail' => [
        'host' => 'smtp.gmail.com',
        'username' => '',
        'password' => '',
        'encryption' => 'tls',
        'port' => 587,
        'from_email' => '',
        'from_name' => 'Barangay Digital Complaint System',
    ],
];

$envConfigPath = __DIR__ . '/env.php';
$envConfig = file_exists($envConfigPath) ? include $envConfigPath : [];

if(!is_array($envConfig)){
    $envConfig = [];
}

$GLOBALS['APP_CONFIG'] = array_replace_recursive($defaultConfig, $envConfig);

if(!function_exists('app_config')){
    function app_config(string $key, $default = null){
        $config = $GLOBALS['APP_CONFIG'] ?? [];
        $parts = explode('.', $key);

        foreach($parts as $part){
            if(!is_array($config) || !array_key_exists($part, $config)){
                return $default;
            }

            $config = $config[$part];
        }

        return $config;
    }
}

if(!function_exists('detect_app_url')){
    function detect_app_url(): string{
        if(!empty($_SERVER['HTTP_HOST'])){
            $isHttps = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
                || (($_SERVER['HTTP_X_FORWARDED_PROTO'] ?? '') === 'https');
            $scheme = $isHttps ? 'https' : 'http';
            $scriptPath = str_replace('\\', '/', dirname(dirname($_SERVER['SCRIPT_NAME'] ?? '')));
            $basePath = ($scriptPath === '/' || $scriptPath === '.') ? '' : rtrim($scriptPath, '/');

            return $scheme . '://' . $_SERVER['HTTP_HOST'] . $basePath;
        }

        return 'http://localhost/barangay';
    }
}

if(!defined('APP_URL')){
    $configuredAppUrl = app_config('app_url', '');
    define('APP_URL', rtrim($configuredAppUrl !== '' ? $configuredAppUrl : detect_app_url(), '/'));
}
?>
