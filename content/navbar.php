
<nav class="navbar navbar-expand-lg navbar-light bg-light">
    <a class="navbar-brand" href="#">Simple Blog Website</a>
    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarNav">
        <ul class="navbar-nav">
            <li class="nav-item <?php if($current_page=='home') echo 'active'?>">
                <a class="nav-link" href="/admin/index.php">Home</a>
            </li>
            <li class="nav-item <?php if($current_page=='myposts') echo 'active'?>">
                <a class="nav-link" href="/admin/myposts.php">My Posts</a>
            </li>
            <li class="nav-item <?php if($current_page=='new') echo 'active'?>">
                <a class="nav-link" href="/admin/create-blog.php">New Post</a>
            </li>
        </ul>
    </div>
    Welcome &nbsp; <b><?php echo $_SESSION['username'];?>!</b> &nbsp; &nbsp;
    <a href="/admin/logout.php">LOGOUT</a>
</nav>
