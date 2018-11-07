<?php
include('password.php');
class Member extends Password {
	
    private $db;

	function __construct($db){
		parent::__construct();

		$this->_db = $db;
	}

	public function is_logged_in(){
		if(isset($_SESSION['loggedin']) && $_SESSION['loggedin'] == true){
			return true;
		}
	}

	private function get_user_hash($email){

		try {

			$stmt = $this->_db->prepare('SELECT * FROM members WHERE email = :email');
			$stmt->execute(array('email' => $email));

			return $stmt->fetch();

		} catch(PDOException $e) {
		    echo '<p class="error">'.$e->getMessage().'</p>';
		}
	}

	public function login($email,$password){

		$user = $this->get_user_hash($email);

		if($this->password_verify($password,$user['password']) == 1){

		    $_SESSION['loggedin'] = true;
		    $_SESSION['id'] = $user['id'];
		    $_SESSION['username'] = $user['username'];
		    return true;
		}
	}

	public function getPost($id){
		$stmt = $this->_db->prepare('SELECT posts.id, members.username, posts.title, posts.content, posts.created_at, posts.updated_at FROM posts LEFT JOIN members ON posts.member_id = members.id WHERE posts.id = :id');
		$stmt->execute(array(':id' => $id));
		return $stmt->fetch(); 
	}

	public function logout(){
		session_destroy();
	}
}