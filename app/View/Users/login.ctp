<style>
    .wrapper {
        height: 100vh;
    }
</style>

<div class="container d-flex justify-content-center align-items-center h-100">
    <div class="col-md-6">
        <div class="row">
            <div class="col-6">
                <h1 id="title">Login</h1>
            </div>
            <div class="col-6 d-flex align-items-center justify-content-end">
                <small><a href="../users/add">New user? Click me!</a></small>
            </div>
        </div>
        <div class="row">
            <?php echo $this->Flash->render(); ?>
        </div>
        <form action="/messageboard/users/login" method="post" id="UserLoginForm">
            <div class="form-group">
                <input class="form-control" name="data[User][email]" type="text" placeholder="Email" required>
            </div>
            <div class="form-group">
                <input class="form-control" name="data[User][password]" type="password" placeholder="Password" required>
            </div>
            <button class="btn btn-primary w-100 mt-3" type="submit" id="login-button">Login</button>
        </form>
    </div>
</div>