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
        <a href="index.php">BACK TO HOME</a>
        <div class="row">
            <div class="col-md-7">
                <?php
                    //get pos
                    $post = $member->getPost($_GET['id']);

                    echo '<h3>'.$post['title'].'</h3>';
                    echo '<p class="post-info">By: '.$post['username'].'</p>';
                    echo '<p>'.$post['content'].'</p>';
                    echo '<p class="post-info">Date Posted: '.date('jS M Y h:i: A', strtotime($post['created_at'])).'</p>';
                    if($post['updated_at']!='') echo '<p class="post-info">Last Updated: '.date('jS M Y h:i: A', strtotime($post['created_at'])).'</p>';
                ?>
            </div>
            <div class="col-md-5">
                <h4>Comments</h4>
                <div class="list-group">
                <?php
                    if (isset($_GET['pageno'])) {
                        $pageno = $_GET['pageno'];
                    } else {
                        $pageno = 1;
                    }
                    $no_of_records_per_page = 5;
                    $offset = ($pageno-1) * $no_of_records_per_page;

                    //count total page
                    $stmt = $db->prepare('SELECT COUNT(*) FROM comments WHERE post_id = :post_id');
                    $stmt->execute(array(
                        ':post_id' => $post['id']
                    ));
                    $result = $stmt->fetch();
                    $total_rows = $result[0];
                    $total_pages = ceil($total_rows / $no_of_records_per_page);
                    
                    $stmt = $db->prepare("SELECT comments.content, comments.created_at, members.username FROM comments  LEFT JOIN members ON comments.member_id = members.id WHERE post_id = :post_id ORDER BY comments.id DESC LIMIT $offset, $no_of_records_per_page");

                    $stmt->execute(array(
                        ':post_id' =>  $post['id']
                    ));
                    $result = $stmt->fetchAll();

                    if($result){
                    foreach($result as $row){

                ?>
                        <div class="list-group-item list-group-item-action flex-column align-items-start">
                            <div class="d-flex w-100 justify-content-between">
                            <h5 class="mb-1"><?php echo $row['username'];?></h5>
                            <small><?php echo date('M d Y h:i: A', strtotime($row['created_at']))?></small>
                            </div>
                            <p class="mb-1"><?php echo $row['content'];?></p>
                        </div>
                <?php
                        }
                    }
                    
                ?> 
                </div>

                <?php if($total_pages>1){ ?>
                    <ul class="pagination float-right" style="margin-top: 10px">
                        <li class="page-item"><a class="page-link" href="<?php echo "?id=".$post['id']."&pageno=1";?>">First</a></li>
                        <li class="page-item <?php if($pageno <= 1){ echo 'disabled'; } ?>">
                            <a class="page-link" href="<?php if($pageno <= 1){ echo '#'; } else { echo"?id=".$post['id']."&pageno=".($pageno - 1); } ?>">Prev</a>
                        </li>
                        <li class="page-item <?php if($pageno >= $total_pages){ echo 'disabled'; } ?>">
                            <a class="page-link" href="<?php if($pageno >= $total_pages){ echo '#'; } else { echo "?id=".$post['id']."&pageno=".($pageno + 1); } ?>">Next</a>
                        </li>
                        <li class="page-item"><a class="page-link" href="<?php echo "?id=".$post['id']."&pageno=".$total_pages; ?>">Last</a></li>
                    </ul>
                <?php
                    }
                ?>
            </div>
        </div>
        
    </div>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/js/bootstrap.min.js" integrity="sha384-ChfqqxuZUCnJSK3+MXmPNIyE6ZbWh2IMqE241rYiqJxyMiZ6OW/JmZQ5stwEULTy" crossorigin="anonymous"></script>
</body>
</html>