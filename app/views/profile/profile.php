<?php $this->layout('layout', ['title' => 'Profile']) ?>
<main class="py-4">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <?php echo flash()->display();?>
                <div class="card">
                    <div class="card-header"><h3>Профиль пользователя</h3></div>

                    <div class="card-body">



                        <form action="/profile" method="post" enctype="multipart/form-data">
                            <div class="row">
                                <div class="col-md-8">
                                    <div class="form-group">
                                        <label for="exampleFormControlInput1">Name</label>
                                        <input type="text" class="form-control" name="name" value="<?php echo $this->e($username);?>">


                                    </div>

                                    <div class="form-group">
                                        <label for="exampleFormControlInput1">Email</label>
                                        <input type="email" class="form-control " name="email" value="<?php echo $this->e($email);?>">

                                    </div>

                                    <div class="form-group">
                                        <label for="exampleFormControlInput1">Аватар</label>
                                        <input type="file" class="form-control" name="image" id="exampleFormControlInput1" enctype="multipart/form-data">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <img src="<?php if (empty($this->e($image))) echo 'img/no-user.jpg'; else echo $this->e($image);?> " alt="" class="img-fluid">
                                </div>

                                <div class="col-md-12">
                                    <button class="btn btn-warning">Edit profile</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <div class="col-md-12" style="margin-top: 20px;">
                <div class="card">
                    <div class="card-header"><h3>Безопасность</h3></div>

                    <div class="card-body">


                        <form action="/password" method="post">
                            <div class="row">
                                <div class="col-md-8">
                                    <div class="form-group">
                                        <label for="exampleFormControlInput1">Current password</label>
                                        <input type="password" class="form-control " name="oldPassword">

                                    </div>

                                    <div class="form-group">
                                        <label for="exampleFormControlInput1">New password</label>
                                        <input type="password" class="form-control " name="newPassword">

                                    </div>

                                    <div class="form-group">
                                        <label for="exampleFormControlInput1">Password confirmation</label>

                                        <input type="password" class="form-control " name="passwordConfirmation">

                                    </div>

                                    <button class="btn btn-success">Submit</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>