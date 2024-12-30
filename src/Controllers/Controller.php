<?php
namespace App\Controllers;
class Controller
{

    protected function view($view, $params, int $code = 200)
    {
        http_response_code($code);
        $file = __DIR__ . '/../../views/' . $view; 

        extract($params);

        include $file;
    }

    protected function json($data, int $code = 200) {
        http_response_code($code);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($data);
    }
}
