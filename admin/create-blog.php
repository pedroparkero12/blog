<?php
	//include database
    require_once('../class/init.php');
    
    //if not logged in redirect to login page
    if(!$member->is_logged_in()){ header('Location: login.php'); }

    $current_page = 'new';
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Create Blog</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-MCw98/SFnGE8fJT3GXwEOngsV7Zt27NXFoaoApmYm81iuXoPkFOJwJ8ERdknLPMO" crossorigin="anonymous">
    <link rel="stylesheet" type="text/css" media="screen" href="/css/style.css"/>
    
</head>
<body>
    <?php
        include('../content/navbar.php');
    ?>

    <div class="wrapper">
        <h3>Create New Post</h3>
        <?php

            if(isset($_POST['save_blog'])){

                $_POST = array_map( 'stripslashes', $_POST );
                extract($_POST);

                //validation
                if($title ==''){
                    $error[] = 'Title field is required.';
                }

                if($content ==''){
                    $error[] = 'Content field is required.';
                }

                if(!isset($error)){

                    try {

                        //insert into database
                        $stmt = $db->prepare('INSERT INTO posts (title,content,created_at,member_id) VALUES (:title, :content, :created_at, :id)') ;
                        $stmt->execute(array(
                            ':title' => $title,
                            ':content' => $content,
                            ':created_at' => date('Y-m-d H:i:s'),
                            ':id' => $_SESSION['id']
                        ));

                        //redirect to index page
                        header('Location: myposts.php?action=added');
                        exit;

                    } catch(PDOException $e) {
                        echo $e->getMessage();
                    }

                }

            }

            //check for any errors
            if(isset($error)){
                echo '<div class="alert alert-danger alert-dismissible fade show" role="alert">';
                foreach($error as $error){
                    echo '
                            '.$error.'</br>
                    '
                  ;
                }
                echo '
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                      <span aria-hidden="true">&times;</span>
                    </button>
                </div>';
            }
            ?>
        <form action='' method='post'>

            <div class="form-group">
                <label for="title">Title</label>
                <input name="title" type="text" class="form-control" id="title" placeholder="Enter title" value="<?php if(isset($error)){ echo $_POST['title'];}?>">
            </div>

            <div class="form-group">
                <label for="content">Content</label>
                <textarea name="content" class="form-control" id="content" cols='60' rows='10'><?php if(isset($error)){ echo $_POST['content'];}?></textarea>
            </div>

            <button type="submit" name="save_blog" class="btn float-right">Submit</button>

        </form>
    </div>

    
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/js/bootstrap.min.js" integrity="sha384-ChfqqxuZUCnJSK3+MXmPNIyE6ZbWh2IMqE241rYiqJxyMiZ6OW/JmZQ5stwEULTy" crossorigin="anonymous"></script>
    <script src="//tinymce.cachefly.net/4.0/tinymce.min.js"></script>
    <script>
            tinymce.init({
                selector: "textarea",
                plugins: [
                    "advlist autolink lists link image charmap print preview anchor",
                    "searchreplace visualblocks code fullscreen",
                    "insertdatetime media table contextmenu paste"
                ],
                toolbar: "insertfile undo redo | styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image"
            });
    </script>
</body>
</html>