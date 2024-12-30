<?php

namespace App\Interfaces;

interface CommentService
{
    public function getCommentsByArticle(int $articleId): array;
    public function createCommentForArticle(int $articleId, string $username, string $content): int;
    public function createCommentForComment(int $id, int $articleId, string $username, string $content): int;
    public function updateComment(int $id, string $content);
}
