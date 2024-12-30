<html>

<head>
    <meta charset="UTF-8">
    <title>
        Статьи
    </title>
    <link rel="stylesheet" href="/css/bootstrap.min.css">
    <link rel="stylesheet" href="/css/main.css">
    <script src="/js/jquery-3.6.0.min.js"></script>
</head>

<style>
    .comment {
        display: flex;
        flex-wrap: wrap;
    }

    .comment-response {
        cursor: pointer;
        margin-left: auto;
        margin-right: 10px;
    }

    .comment-response:hover {
        color: blue;
    }

    .comment-response_form {
        width: 100%;
    }

    .comment-edit {
        cursor: pointer;
    }

    .comment-edit:hover {
        color: blue;
    }
</style>

<body>
    <nav class="navbar navbar-expand-md navbar-light bg-light mb-3">
        <div class="container-fluid">
            <a class="navbar-brand abs" href="">Статьи</a>
        </div>
    </nav>

    <div class="container">
        <h1 class="mt-5"><?php echo $article->title ?></h1>

        <div class="mb-3">
            <?php echo $article->content ?>
        </div>
        <div class="card-body p-4">
            <h4 class="text-center mb-4 pb-2">Комментарии</h4>
            <div id="comments" class="row mb-3"></div>

            <form class="mb-3" method="post">
                <div class="mb-3">
                    <label for="username" class="form-label">Имя</label>
                    <input type="text" class="form-control" id="username" name="username" maxlength="50">
                    <div class="invalid-feedback display: block;">
                    </div>
                </div>
                <div class="mb-3">
                    <textarea class="form-control" id="content" name="content" rows="3" maxlength="300"></textarea>
                    <div class="invalid-feedback display: block;">

                    </div>
                </div>
                <button type="button" id="submit-comment" class="btn btn-primary">Отправить</button>
            </form>
        </div>

        <script>
            $(document).ready(function() {
                var apiUrl = 'http://localhost:8000/articles/1/comments';

                $.getJSON(apiUrl, function(data) {
                    $.each(data, function(key, comment) {
                        let commentHtml = renderComments(comment);
                        $('#comments').append(commentHtml);
                    });

                    // Ответы на комментарии
                    $('.response').click(function() {
                        let сommentEl = $(this).parent();
                        let form = сommentEl.append(renderResponseForm()).find('form');
                        form.find('button').click(function name(event) {
                            event.preventDefault();
                            var formData = {
                                username: form.find('#response-username').val(),
                                content: form.find('#response-content').val()
                            };

                            $.ajax({
                                url: 'http://localhost:8000/articles/1/comments/' + сommentEl.data('comment_id'),
                                type: 'POST',
                                contentType: 'application/json',
                                data: JSON.stringify(formData),
                                success: function(response) {
                                    var commentHtml = renderComment(formData.username, formData.content);
                                    сommentEl.append(commentHtml);
                                    form.remove();
                                },
                                error: function(xhr, status, error) {
                                    if (xhr.responseJSON && xhr.responseJSON.message) {
                                        errorMessage = xhr.responseJSON.message;
                                    }
                                    alert(errorMessage);
                                }
                            });
                        });
                    });

                    // Редактирование
                    $('.edit').click(function() {
                        let сommentEl = $(this).parent();
                        let form = сommentEl.append(renderEditForm()).find('form');
                        form.find('button').click(function name(event) {
                            event.preventDefault();
                            var formData = {
                                content: form.find('#response-content').val()
                            };

                            $.ajax({
                                url: 'http://localhost:8000/comments/' + сommentEl.data('comment_id'),
                                type: 'PUT',
                                contentType: 'application/json',
                                data: JSON.stringify(formData),
                                success: function(response) {
                                    let commentContent = сommentEl.find('.comment-content').first();
                                    commentContent.text(formData.content);
                                    form.remove();
                                },
                                error: function(xhr, status, error) {
                                    alert('Произошла ошибка: ' + error);
                                }
                            });
                        });
                    });

                    // 
                    $('#submit-comment').click(function() {
                        var formData = {
                            username: $('#username').val(),
                            content: $('#content').val()
                        };

                        $.ajax({
                            url: 'http://localhost:8000/articles/1/comments',
                            type: 'POST',
                            contentType: 'application/json',
                            data: JSON.stringify(formData),
                            success: function(response) {
                                var commentHtml = renderComment(formData.username, formData.content);

                                $('#comments').append(commentHtml);

                            },
                            error: function(xhr, status, error) {
                                alert('Произошла ошибка: ' + error);
                            }
                        });
                    });
                });

            });

            function renderComments(comment) {
                var commentHtml = `
                                    <div class="comment px-3 w-100" data-comment_id="${comment.id}">
                                        <div><strong>${comment.username}</strong>: <span class="comment-content">${comment.content}</span></div>
                                        <div class="comment-response response">ответить</div>
                                    <div class="comment-edit edit">редактировать</div>`
                $.each(comment.comments, function(key, comment) {
                    commentHtml += renderComments(comment);
                });
                commentHtml += '</div>';
                return commentHtml;
            }

            function renderResponseForm() {
                return `
                       <div class="comment-response_form">
                           <form class="mb-3">
                               <div class="mb-3">
                                   <label for="response-username" class="form-label">Имя</label>
                                   <input type="text" class="form-control" id="response-username" name="username" maxlength="50">
                               </div>
                               <div class="mb-3">
                                   <label for="response-content" class="form-label">Текст</label>
                                   <textarea class="form-control" id="response-content" name="content" rows="3" maxlength="300"></textarea>
                               </div>
                               <button type="submit" class="btn btn-primary">Отправить</button>
                           </form>
                       </div>`;
            }

            function renderEditForm() {
                return `
                       <div class="comment-response_form">
                           <form class="mb-3">
                               <div class="mb-3">
                                   <label for="response-content" class="form-label">Текст</label>
                                   <textarea class="form-control" id="response-content" name="content" rows="3" maxlength="300"></textarea>
                               </div>
                               <button type="submit" class="btn btn-primary">Сохранить</button>
                           </form>
                       </div>`;
            }

            function renderComment(username, content) {
                var commentHtml = `<div class="comment px-3 w-100">
                                        <div><strong>${username}</strong>: ${content}</div>
                                    </div>`;
                return commentHtml;
            }
        </script>

</body>

</html>