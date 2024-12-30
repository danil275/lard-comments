<?php

use App\Controllers\Api\CommentController;
use App\Controllers\Web\ArticleController;
use App\Entities\Article;
use App\Repositories\DefaultArticleRepository;
use App\Repositories\DefaultCommentRepository;
use App\Services\DefaultArticleService;
use App\Services\DefaultCommentService;
use Monolog\Logger;
use Symfony\Component\HttpFoundation\Request;

class Container
{
    static public function createArticleController(Pdo $pdo, Request $request, Logger $logger)
    {
        $articleRepository = new DefaultArticleRepository($pdo);
        $articleService = new DefaultArticleService($articleRepository);
        return new ArticleController($request, $logger, $articleService);
    }

    static public function createCommentController(Pdo $pdo, Request $request, Logger $logger)
    {
        $commentRepository = new DefaultCommentRepository($pdo);
        $articleRepository = new DefaultArticleRepository($pdo);
        $commentService = new DefaultCommentService($commentRepository, $articleRepository);
        return new CommentController($request, $logger, $commentService);
    }
}

return function (Request $request, Logger  $logger, PDO $pdo) {
    $uri = $request->getPathInfo();
    $method = $request->getMethod();

    $routes = [
        'GET' => [
            '/articles/(\d+)' => [ArticleController::class, 'index', function () use ($pdo, $request, $logger) {
                return Container::createArticleController($pdo, $request, $logger);
            }],
            '/articles/(\d+)/comments' => [CommentController::class, 'getByArticle', function () use ($pdo, $request, $logger) {
                return Container::createCommentController($pdo, $request, $logger);
            }],
        ],
        'POST' => [
            '/articles/(\d+)/comments' => [CommentController::class, 'createForArticle', function () use ($pdo, $request, $logger) {
                return Container::createCommentController($pdo, $request, $logger);
            }],
            '/articles/(\d+)/comments/(\d+)' => [CommentController::class, 'createForComment', function () use ($pdo, $request, $logger) {
                return Container::createCommentController($pdo, $request, $logger);
            }],
        ],
        'PUT' => [
            '/comments/(\d+)' => [CommentController::class, 'update', function () use ($pdo, $request, $logger) {
                return Container::createCommentController($pdo, $request, $logger);
            }],
        ]
    ];

    foreach ($routes[$method] as $route => $handler) {
        if (preg_match('#^' . $route . '$#', $uri, $matches)) {
            array_shift($matches);
            [$controllerClass, $method, $initialize] = $handler;
            $controller = $initialize();
            call_user_func_array([$controller, $method], $matches);
            return;
        }
    }

    http_response_code(404);
    include(__DIR__ . '/../views/notfound.php'); 
};
