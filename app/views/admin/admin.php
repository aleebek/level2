<?php $this->layout('layout', ['title' => 'Admin Panel']) ?>
<main class="py-4">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header"><h3>Админ панель</h3></div>

                    <div class="card-body">
                        <table class="table">
                            <thead>
                            <tr>
                                <th>Аватар</th>
                                <th>Имя</th>
                                <th>Дата</th>
                                <th>Комментарий</th>
                                <th>Действия</th>
                            </tr>
                            </thead>

                            <tbody>
                            <?php foreach ($posts as $post) { ?>
                                <tr>
                                    <td>
                                        <img src="<?php if (empty($post['image'])) echo 'img/no-user.jpg'; else echo $post['image'];?>" alt="" class="img-fluid" width="64" height="64">
                                    </td>
                                    <td><?php echo $post['username'] ?></td>
                                    <td><?php echo date('d/m/Y' , strtotime($post['date']))  ?></td>
                                    <td><?php echo $post['text'] ?></td>
                                    <td>
                                        <?php if (!empty($post['hidden'])) :?>
                                            <a href="admin/allow?id=<?php echo $post['id'] ?>" class="btn btn-success">Разрешить</a>
                                        <?php else: ?>
                                            <a href="admin/hide?id=<?php echo $post['id'] ?>" class="btn btn-warning">Запретить</a>
                                        <?php endif;?>
                                        <a href="admin/delete?id=<?php echo $post['id'] ?>" onclick="return confirm('are you sure?')" class="btn btn-danger">Удалить</a>
                                    </td>
                                </tr>
                            <?php }?>

                            </tbody>
                        </table>
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
        </div>
    </div>
</main>