<?php
	//include init
    require_once('../class/init.php');

    //check if already logged in
    if( $member->is_logged_in() ){ header('Location: admin/index.php'); } 

?>
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
                <li class="nav-item">
                    <a class="nav-link" href="/index.php">Home</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="/register.php">Register</a>
                </li>
                <li class="nav-item active">
                    <a class="nav-link" href="/admin/login.php">Login</a>
                </li>
            </ul>
        </div>
    </nav>

    <div id="login">

        <?php

        //process login form if submitted
        if(isset($_POST['submit'])){

            $email = trim($_POST['email']);
            $password = trim($_POST['password']);
            
            if($member->login($email,$password)){ 

                //logged in return to index page
                header('Location: index.php');
                exit;
            

            } else {
                $message = '<div class="alert alert-danger" role="alert">
                    Wrong email or password!
                </div>';
            }

        }//end if submit

        ?>
        <div class="col-md-6 offset-md-3 login-container">
            <?php if(isset($message)){ echo $message; } ?>
            <form action="" method="post">
                <div class="form-group">
                    <label for="email">Email</label>
                    <input name="email" type="text" class="form-control" id="email" placeholder="Enter email" required>
                </div>
                <div class="form-group">
                    <label for="password">Password</label>
                    <input name="password" type="password" class="form-control" id="password" placeholder="Enter password" required>
                </div>
                <button type="submit" name="submit" class="btn btn-block">Submit</button>
            </form>
        </div>

    </div>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/js/bootstrap.min.js" integrity="sha384-ChfqqxuZUCnJSK3+MXmPNIyE6ZbWh2IMqE241rYiqJxyMiZ6OW/JmZQ5stwEULTy" crossorigin="anonymous"></script>
</body>
</html>
