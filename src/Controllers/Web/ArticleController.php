<?php

namespace App\Controllers\Web;

use App\Entities\Article;
use App\Controllers\Controller;
use App\Exceptions\ArticleNotFoundException;
use App\Interfaces\ArticleService;
use Exception;
use Monolog\Logger;
use Symfony\Component\HttpFoundation\Request;

class ArticleController extends Controller
{
    public function __construct(
        private Request $request,
        private Logger $logger,
        private ArticleService $articleService
    ) {
    }

    public function index(int $id)
    {
        try {
            $article = $this->articleService->getArticleById($id);
            $this->view('article.php', ['article' => $article]);
        } catch (ArticleNotFoundException $e) {
            $this->logger->error($e->getMessage());
            $this->view('notfound.php', [], 404);
        } catch (Exception $e) {
            $this->logger->error($e->getMessage());
            $this->view('error.php', [], 500);
        }
    }
}
