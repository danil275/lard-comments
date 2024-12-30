<?php

namespace App\Services;

use App\Interfaces\ArticleRepository;
use App\Interfaces\CommentRepository;
use App\Interfaces\CommentService;
use Symfony\Component\HttpFoundation\Request;

class DefaultCommentService implements CommentService
{
    public function __construct(
        private CommentRepository $commentRepository,
        private ArticleRepository $articleRepository
    ) {
    }

    public function getCommentsByArticle(int $articleId): array
    {
        $article = $this->articleRepository->get($articleId);
        return $this->commentRepository->getByArticle($article->id);
    }

    public function createCommentForArticle(int $articleId, string $username, string $content): int
    {
        $article = $this->articleRepository->get($articleId);
        return $this->commentRepository->createForArticle($article->id, $username, $content);
    }

    public function createCommentForComment(int $id, int $articleId, string $username, string $content): int
    {
        $comment = $this->commentRepository->get($id);
        $count = $this->commentRepository->countNestingOfResponses($comment->id);
        if($count >= 10) {
            throw new \Exception('Вложенность комментариев не должна привышать 10');
        }
        $article = $this->articleRepository->get($articleId);
        return $this->commentRepository->createForComment($comment->id, $article->id, $username, $content);
    }

    public function updateComment(int $id, string $content)
    {
        $comment = $this->commentRepository->get($id);
        $this->commentRepository->update($comment->id, $content);
    }
}
