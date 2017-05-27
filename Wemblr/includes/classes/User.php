<?php 
class User {
	private $user;
	private $con;

	public function __construct($con, $user){
		$this->con = $con;
		$user_details_query = mysqli_query($con, "SELECT * FROM users WHERE username = '$user'");
		$this->user = mysqli_fetch_array($user_details_query);
	}

	public function getUsername() {
		return $this->user['username'];
	}

	public function getProfilePic() {
		$username = $this->user['username'];
		$query = mysqli_query($this->con, "SELECT profile_pic FROM users WHERE username = '$username'");
		$row = mysqli_fetch_array($query);
		return $row['profile_pic'];
	}

	public function getNumPosts() {
		$username = $this->user['username'];
		$query = mysqli_query($this->con, "SELECT num_posts FROM users WHERE username = '$username'");
		$row = mysqli_fetch_array($query);
		return $row['num_posts'];
	}

	public function getFirstAndLastName() {
		$username = $this->user['username'];
		$query = mysqli_query($this->con, "SELECT first_name, last_name FROM users WHERE username = '$username'");
		$row = mysqli_fetch_array($query);
		return $row['first_name'] . " " . $row['last_name'];
	}

	public function isClosed() {
		$username = $this->user['username'];
		$query = mysqli_query($this->con, "SELECT user_closed FROM users WHERE username ='$username'");
		$row = mysqli_fetch_array($query);

		if($row['user_closed'] == 'yes')
			return true;
		else
			return false;
	}

	public function isFollowing($username_to_check) {
		$usernameComma = "," . $username_to_check . ",";

		if(stristr($this->user['following_array'], $usernameComma) || $username_to_check == $this->user['username']){
			return true;
		}
		else{
			return false;
		}
	}

	public function unFollow($user_to_remove) {
		$loggedin_user = $this->user['username'];

		$query = mysqli_query($this->con, "SELECT followers_array FROM users WHERE username='$user_to_remove'");
		$row = mysqli_fetch_array($query);
		$username_followers_array = $row['followers_array'];

		//Remove user from userloggedin's following array
		$new_following_array = str_replace($user_to_remove . ",", "", $this->user['following_array']);
		$remove_user = mysqli_query($this->con, "UPDATE users SET following_array='$new_following_array' WHERE username='$loggedin_user'");
	
		//Remove userloggedin from user's followers array
		$new_followers_array = str_replace($this->user['username'] . ",", "", $username_followers_array);
		$remove_user = mysqli_query($this->con, "UPDATE users SET followers_array='$new_followers_array' WHERE username='$user_to_remove'");	
	}

	public function follow($user_to_follow) {
		$loggedin_user = $this->user['username'];

		//Add loggedin_user to user_to_follow's follower array
		$add_followers_query = mysqli_query($this->con, "UPDATE users SET followers_array=CONCAT(followers_array, '$loggedin_user,') WHERE username='$user_to_follow'");

		//Add user_to_follow to loggedin_user's following array
		$add_following_query = mysqli_query($this->con, "UPDATE users SET following_array=CONCAT(following_array, '$user_to_follow,') WHERE username='$loggedin_user'");	

	}
}


?>
