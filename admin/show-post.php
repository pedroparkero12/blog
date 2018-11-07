<?php
	//include init
    require_once('../class/init.php');
    
    //if not logged in redirect to login page
    if(!$member->is_logged_in()){ header('Location: login.php'); }

    $current_page = 'home';

    if(isset($_POST['add_comment'])){
        $_POST = array_map( 'stripslashes', $_POST );
        extract($_POST);

        if($content!=''){
            try {

                //insert into database
                $stmt = $db->prepare('INSERT INTO comments (content,post_id,member_id,created_at) VALUES (:content, :post_id, :member_id, :created_at)') ;
                $stmt->execute(array(
                    ':content' => $content,
                    ':post_id' => $post_id,
                    ':member_id' => $_SESSION['id'],
                    ':created_at' => date('Y-m-d H:i:s')  
                ));

                //redirect to index page
                header('Location: show-post.php?id='.$post_id);
                exit;

            } catch(PDOException $e) {
                echo $e->getMessage();
            }
        }
    }


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
    <?php
        include('../content/navbar.php');
    ?>
    <div class="wrapper">
        <a href="/admin/index.php">BACK TO HOME</a>
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
                <form class="form-inline" action='' method='post' style="margin-top: 50px">
                    <div class="form-group col-md-10 col-md-2">
                        <input name="post_id" type="hidden" value="<?php echo $post['id']; ?>">
                        <input name="member_id" type="hidden" value="<?php echo $_SESSION['id']; ?>">
                        <input type="text" class="form-control col-md-12" name="content" id="comment" placeholder="Leave a comment.." required>
                    </div>
                    <button name="add_comment" type="submit" class="btn col-md-2">Submit</button>
                </form>
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