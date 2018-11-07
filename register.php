<?php
	//include init
    require_once('class/init.php');

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
                    <a class="nav-link" href="index.php">Home</a>
                </li>
                <li class="nav-item active">
                    <a class="nav-link" href="register.php">Register</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="/admin/login.php">Login</a>
                </li>
            </ul>
        </div>
    </nav>

    <div class="wrapper">

        <?php

        	//if form has been submitted process it
            if(isset($_POST['submit'])){

                //collect form data
                extract($_POST);

                //very basic validation
                if($username ==''){
                    $error[] = 'Please enter the username.';
                }

                if($password ==''){
                    $error[] = 'Please enter the password.';
                }

                if($passwordConfirm ==''){
                    $error[] = 'Please confirm the password.';
                }

                if($password != $passwordConfirm){
                    $error[] = 'Passwords do not match.';
                }

                if($email ==''){
                    $error[] = 'Please enter the email address.';
                }


                if(!isset($error)){

                    $stmt = $db->prepare('SELECT * FROM members WHERE email = :email');
                    $stmt->execute(array('email' => $email));

                    $result =  $stmt->fetch();

                    if($result){
                        $error[] = 'Email already exist!';
                    }
                    else{
                        $hashedpassword = $member->password_hash($password, PASSWORD_BCRYPT);

                        try {

                            //insert into database
                            $stmt = $db->prepare('INSERT INTO members (username,password,email) VALUES (:username, :password, :email)') ;
                            $stmt->execute(array(
                                ':username' => $username,
                                ':password' => $hashedpassword,
                                ':email' => $email
                            ));

                            $id = $db->lastInsertId();

                            $_SESSION['loggedin'] = true;
                            $_SESSION['id'] = $id;
                            $_SESSION['username'] = $username;

                            //redirect to index page
                            header('Location: index.php');
                            exit;

                        } catch(PDOException $e) {
                            echo $e->getMessage();
                        }
                    }

                }

            }

        ?>
        <div class="col-md-6 offset-md-3">
            <?php
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
            <h3 style="text-align:center"> Register </h3>
            <?php if(isset($message)){ echo $message; } ?>
            <form action="" method="post">
                <div class="form-group">
                    <label for="email">Email</label>
                    <input name="email" type="email" class="form-control" id="email" placeholder="Enter email" value='<?php if(isset($error)){ echo $_POST['email'];}?>'>
                </div>
                <div class="form-group">
                    <label for="username">Username</label>
                    <input name="username" type="text" class="form-control" id="username" placeholder="Enter username" value='<?php if(isset($error)){ echo $_POST['username'];}?>'>
                </div>
                <div class="form-group">
                    <label for="password">Password</label>
                    <input name="password" type="password" class="form-control" id="password" placeholder="Enter password" value='<?php if(isset($error)){ echo $_POST['password'];}?>'>
                </div>
                <div class="form-group">
                    <label for="passwordConfirm">Password</label>
                    <input name="passwordConfirm" type="passwordConfirm" class="form-control" id="passwordConfirm" placeholder="Confirm password" value='<?php if(isset($error)){ echo $_POST['passwordConfirm'];}?>'>
                </div>
                <button type="submit" name="submit" class="btn btn-block">Submit</button>
            </form>
        </div>

    </div>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/js/bootstrap.min.js" integrity="sha384-ChfqqxuZUCnJSK3+MXmPNIyE6ZbWh2IMqE241rYiqJxyMiZ6OW/JmZQ5stwEULTy" crossorigin="anonymous"></script>
</body>
</html>
