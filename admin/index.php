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
                    echo '<h5><a href="/admin/show-post.php?id='.$row['id'].'">'.$row['title'].'</a></h5>';
                    echo '<p class="post-info">By: '.$row['username'].'<i class="float-right" style="font-style:normal">Date Posted: '.date('jS M Y h:i: A', strtotime($row['created_at'])).'</i></p>';
                    echo '<p>'.$row['content'].'</p>';
                   
                ?>
                    <form class="form-inline" action='' method='post'>
                        <div class="form-group col-md-10 col-md-2">
                            <input name="post_id" type="hidden" value="<?php echo $row['id']; ?>">
                            <input name="member_id" type="hidden" value="<?php echo $_SESSION['id']; ?>">
                            <input type="text" class="form-control col-md-12" name="content" id="comment" placeholder="Leave a comment.." required>
                        </div>
                        <button name="add_comment" type="submit" class="btn col-md-2">Submit</button>
                    </form>
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