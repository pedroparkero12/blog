<?php
	//include init
    require_once('class/init.php');

    //check if already logged in
    if( $member->is_logged_in() ){ header('Location: admin/index.php'); } 

?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>My Posts</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-MCw98/SFnGE8fJT3GXwEOngsV7Zt27NXFoaoApmYm81iuXoPkFOJwJ8ERdknLPMO" crossorigin="anonymous">
    <link rel="stylesheet" type="text/css" media="screen" href="/css/style.css"/>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-light bg-light">
        <a class="navbar-brand" href="#">Simple Blog Website</a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav">
                <li class="nav-item active">
                    <a class="nav-link" href="index.php">Home</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="register.php">Register</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="/admin/login.php">Login</a>
                </li>
            </ul>
        </div>
    </nav>
     <div class="wrapper">
        <h3>Posts</h3>
        
        <?php
            if (isset($_GET['pageno'])) {
                $pageno = $_GET['pageno'];
            } else {
                $pageno = 1;
            }
            $no_of_records_per_page = 10;
            $offset = ($pageno-1) * $no_of_records_per_page;
        ?>

        <?php
            //count total page
            $stmt = $db->query('SELECT COUNT(*) FROM posts WHERE deleted_at IS NULL');
            $result = $stmt->fetch();
            $total_rows = $result[0];
            $total_pages = ceil($total_rows / $no_of_records_per_page);
            
            $stmt = $db->query("SELECT posts.id, members.username, posts.title, posts.content, posts.created_at, posts.updated_at FROM posts LEFT JOIN members ON posts.member_id = members.id WHERE deleted_at IS NULL ORDER BY id DESC LIMIT $offset, $no_of_records_per_page");
            $result = $stmt->fetchAll();

            if($result){
               foreach($result as $row){
                    echo '<hr>';
                    echo '<h5><a href="show-post.php?id='.$row['id'].'">'.$row['title'].'</a></h5>';
                    echo '<p class="post-info">By: '.$row['username'].'<i class="float-right" style="font-style:normal">Date Posted: '.date('jS M Y h:i: A', strtotime($row['created_at'])).'</i></p>';
                    echo '<p>'.$row['content'].'</p>';
                   
                ?>
                <?php
                }
            }
            
        ?>
        
        <?php if($total_pages>1){ ?>
            <ul class="pagination float-right">
                <li class="page-item"><a class="page-link" href="?pageno=1">First</a></li>
                <li class="page-item <?php if($pageno <= 1){ echo 'disabled'; } ?>">
                    <a class="page-link" href="<?php if($pageno <= 1){ echo '#'; } else { echo "?pageno=".($pageno - 1); } ?>">Prev</a>
                </li>
                <li class="page-item <?php if($pageno >= $total_pages){ echo 'disabled'; } ?>">
                    <a class="page-link" href="<?php if($pageno >= $total_pages){ echo '#'; } else { echo "?pageno=".($pageno + 1); } ?>">Next</a>
                </li>
                <li class="page-item"><a class="page-link" href="?pageno=<?php echo $total_pages; ?>">Last</a></li>
            </ul>
        <?php
            }
        ?>
    </div>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/js/bootstrap.min.js" integrity="sha384-ChfqqxuZUCnJSK3+MXmPNIyE6ZbWh2IMqE241rYiqJxyMiZ6OW/JmZQ5stwEULTy" crossorigin="anonymous"></script>
</body>
</html>