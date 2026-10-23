<?php
// Roteador do servidor embutido do PHP (somente desenvolvimento local):
// imita o .htaccess — arquivos reais são servidos; pastas internas, bloqueadas.
$path = (string) parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
if (preg_match('#^/(app|storage|docs|tests|scripts|node_modules|\.git)(/|$)#', $path) || preg_match('#/\.#', $path)) {
    http_response_code(403);
    exit('Forbidden');
}
$file = __DIR__ . '/..' . $path;
if ($path !== '/' && is_file($file) && !str_ends_with($path, '.php')) {
    return false;
}
require __DIR__ . '/../index.php';
