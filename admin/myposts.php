<?php
	//include init
    require_once('../class/init.php');
    
    //if not logged in redirect to login page
    if(!$member->is_logged_in()){ header('Location: login.php'); }

    $current_page = 'myposts';

    
    //remove post
    if(isset($_GET['delpost'])){ 

         //insert into database
         $stmt = $db->prepare('UPDATE posts SET deleted_at = :deleted_at WHERE id = :id') ;
         $stmt->execute(array(
             ':deleted_at' => date('Y-m-d H:i:s'),
             ':id' => $_GET['delpost']
         ));

         //redirect to index page
         header('Location: myposts.php?action=deleted');
         exit;
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
        <h3>My Posts</h3>
        
        <?php
            if (isset($_GET['pageno'])) {
                $pageno = $_GET['pageno'];
            } else {
                $pageno = 1;
            }
            $no_of_records_per_page = 10;
            $offset = ($pageno-1) * $no_of_records_per_page;
        ?>

        <table class="table table-striped">
        <thead>
            <tr>
                <th>Title</th>
                <th>Date Created</th>
                <th>Last Updated</th>
                <th>Content</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
        <?php
            //count total page
            $stmt = $db->prepare('SELECT COUNT(*) FROM posts WHERE member_id = :member_id AND deleted_at IS NULL');
            $stmt->execute(array(
                ':member_id' => $_SESSION['id']
            ));
            $result = $stmt->fetch();
            $total_rows = $result[0];
            $total_pages = ceil($total_rows / $no_of_records_per_page);
            
            $stmt = $db->prepare("SELECT * FROM posts WHERE member_id = :member_id AND deleted_at IS NULL ORDER BY id DESC LIMIT $offset, $no_of_records_per_page");
            $stmt->execute(array(
                ':member_id' => $_SESSION['id']
            ));
            $result = $stmt->fetchAll();

            if($result){
               foreach($result as $row){
                    echo '<tr>';
                    echo '<td>'.$row['title'].'</td>';
                    echo '<td>'.date('jS M Y h:i: A', strtotime($row['created_at'])).'</td>';
                    if($row['updated_at']!='') echo '<td>'.date('jS M Y h:i: A', strtotime($row['updated_at'])).'</td>';
                    else echo '<td></td>';
                    echo '<td>'.substr($row['content'],0,50).'..</td>';
                    ?>
        
                    <td>
                        <a href="edit-blog.php?id=<?php echo $row['id'];?>" class="btn btn-success btn-sm">edit</a>
                        <a href="javascript:removePost('<?php echo $row['id'];?>','<?php echo $row['title'];?>')" class="btn btn-danger btn-sm">remove</a>
                    </td>
                    
                    <?php 
                    echo '</tr>';
        
                }
            }
            
        ?>
            </tbody>
        </table>
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
    <script language="JavaScript" type="text/javascript">
        function removePost(id, title)
        {
            if (confirm("Are you sure you want to delete '" + title + "'"))
            {
                window.location.href = 'myposts.php?delpost=' + id;
            }
        }
    </script>
</body>
</html>