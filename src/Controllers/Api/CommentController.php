<?php

namespace App\Controllers\Api;

use App\Controllers\Controller;
use App\Interfaces\CommentService;
use Exception;
use Monolog\Logger;
use Symfony\Component\HttpFoundation\Request;

class CommentController extends Controller
{

    public function __construct(
        private Request $request,
        private Logger $logger,
        private CommentService $commentService
    ) {
    }

    public function getByArticle(int $articleId)
    {
        try {
            $this->json($this->commentService->getCommentsByArticle($articleId));
        } catch (Exception $e) {
            $this->logger->error($e->getMessage());
            $this->json(['message' => $e->getMessage()], 500);
        }
    }

    public function createForArticle(int $articleId)
    {
        try {
            $data = json_decode($this->request->getContent(), true);
            $this->commentService->createCommentForArticle($articleId, $data['username'], $data['content']);
        } catch (Exception $e) {
            $this->logger->error($e->getMessage());
            $this->json(['message' => $e->getMessage()], 500);
        }
    }

    public function createForComment(int $articleId, int $commentId)
    {
        try {
            $data = json_decode($this->request->getContent(), true);
            return $this->commentService->createCommentForComment($commentId, $articleId, $data['username'], $data['content']);
        } catch (Exception $e) {
            $this->logger->error($e->getMessage());
            $this->json(['message' => $e->getMessage()], 500);
        }
    }

    public function update(int $id)
    {
        try {
            $data = json_decode($this->request->getContent(), true);
            $this->commentService->updateComment($id, $data['content']);
        } catch (Exception $e) {
            $this->logger->error($e->getMessage());
            $this->json(['message' => $e->getMessage()], 500);
        }
    }
}
