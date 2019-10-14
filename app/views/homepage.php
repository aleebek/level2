<?php $this->layout('layout', ['title' => 'Homepage']) ?>
<main class="py-4">
    <div class="container">
        <?php if ($_SESSION['auth_logged_in']) :?>
            <div class="row justify-content-center">
                <div class="col-md-12">
                    <?php echo flash()->display();?>
                    <div class="card">
                        <div class="card-header"><h3>Комментарии</h3></div>

                        <div class="card-body">
                            <div class="alert alert-success <?php if (!isset($success_comment)) echo 'd-none';?>" role="alert">
                                Комментарий успешно добавлен
                            </div>
                            <?php foreach ($posts as $post) { ?>
                                <div class="media">
                                    <img src="<?php if (empty($post['image'])) echo 'img/no-user.jpg'; else echo $post['image'];?>" class="mr-3" alt="..." width="64" height="64">
                                    <div class="media-body">
                                        <h5 class="mt-0"><?php echo $post['username'] ?></h5>
                                        <span><small><?php echo date('d/m/Y' , strtotime($post['date']))  ?></small></span>
                                        <p><?php echo $post['text'] ?></p>
                                    </div>
                                </div>
                            <?php }?>
                        </div>
                    </div>

                </div>

                <div class="col-md-12 mt-3 ">
                    <ul class="pagination justify-content-center">
                        <?php if ($paginator->getPrevUrl()): ?>
                            <li class="page-item"><a class="page-link" href="<?php echo $paginator->getPrevUrl(); ?>">&laquo; Previous</a></li>
                        <?php endif; ?>

                        <?php foreach ($paginator->getPages() as $page): ?>
                            <?php if ($page['url']): ?>
                                <li class ="page-item <?php echo $page['isCurrent'] ? 'active' : ''; ?>">
                                    <a class="page-link" href="<?php echo $page['url']; ?>"><?php echo $page['num']; ?></a>
                                </li>
                            <?php else: ?>
                                <li class="page-item disabled"><span><?php echo $page['num']; ?></span></li>
                            <?php endif; ?>
                        <?php endforeach; ?>

                        <?php if ($paginator->getNextUrl()): ?>
                            <li class="page-item"><a class="page-link" href="<?php echo $paginator->getNextUrl(); ?>">Next &raquo;</a></li>
                        <?php endif; ?>
                    </ul>
                </div>


                <div class="col-md-12" style="margin-top: 20px;">
                    <div class="card">
                        <div class="card-header"><h3>Оставить комментарий</h3></div>

                        <div class="card-body">
                            <form action="/post" method="post">

                                <div class="form-group">
                                    <label for="exampleFormControlTextarea1">Сообщение</label>
                                    <textarea name="text" class="form-control <?php if (isset($error_text)) echo 'is-invalid'; else echo 'valid';?>" id="exampleFormControlTextarea1" rows="3"></textarea>
                                    <div class="invalid-feedback">
                                        <?php if (isset($error_text)) echo $error_text;?>
                                    </div>
                                </div>
                                <button type="submit" class="btn btn-success">Отправить</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        <?php endif;?>
    </div>
</main>