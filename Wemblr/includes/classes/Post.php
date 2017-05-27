<?php 
class Post {
	private $user_obj;
	private $con;

	public function __construct($con, $user){
		$this->con = $con;
		$this->user_obj = new User($con, $user);
	}

	public function submitPost($body) {
		$body = strip_tags($body); // removes HTML tags
		$body = mysqli_real_escape_string($this->con, $body);
		$check_empty = preg_replace('/\s+/', '', $body); //Deletes all spaces

		if($check_empty != "") { //continue to post


			$body_array = preg_split("/\s+/", $body);

			foreach($body_array as $key => $value) {

				if(strpos($value, "www.youtube.com/watch?v=") !== false) {

					$link = preg_split("!&!", $value);
					$value = preg_replace("!watch\?v=!", "embed/", $link[0]);
					$value = "<br><iframe width=\'420\' height=\'315\' src=\'" . $value ."\'></iframe><br>";
					$body_array[$key] = $value;

				}

			}
			$body = implode(" ", $body_array);

			//Current data and time
			$date_added = date("Y-m-d H:i:s");

			//Get username
			$added_by = $this->user_obj->getUsername();

			//Insert post
			$query = mysqli_query($this->con, "INSERT INTO posts VALUES('', '$body', '$added_by', '$date_added', 'no', 'no', '0')");
			$return_id = mysqli_insert_id($this->con);

			//Update post count for user
			$num_posts = $this->user_obj->getNumPosts();
			$num_posts++;
			$update_query = mysqli_query($this->con, "UPDATE users SET num_posts='$num_posts' WHERE username='$added_by'");

		}
	}

	public function loadPostsFriends($data, $limit) { 

		$page = $data['page'];
		$userLoggedIn = $this->user_obj->getUsername();

		if($page == 1){
			$start = 0;
		}
		else{
			$start = ($page - 1) * $limit;
		}

		$str = ""; //String to return
		$data_query = mysqli_query($this->con, "SELECT * FROM posts WHERE deleted = 'no' ORDER BY id DESC");

		if(mysqli_num_rows($data_query) > 0){//Has results

			$num_iterations = 0; //Number of results checked (not necessarily posted)
			$count = 1;

			while($row = mysqli_fetch_array($data_query)){
				$id = $row['id'];
				$body = $row['body'];
				$added_by = $row['added_by'];
				$date_time = $row['date_added'];
			

			//Check if user who posted has their account closed
			$added_by_obj = new User($this->con, $added_by);
			if($added_by_obj->isClosed()){
				continue;
			}

			//Show posts by users who userLoggedIn is following
			$user_logged_obj = new User($this->con, $userLoggedIn);
			if($user_logged_obj->isFollowing($added_by)){

				if($num_iterations++ < $start)
					continue;
	
				//Once 10 posts have been loaded, break
				if($count > $limit) {
					break;
				}
				else{
					$count++;
				}

				if($userLoggedIn == $added_by){
					$delete_button = "<button class='delete_button btn-danger' id='post$id'>X</button>";
				}
				else{
					$delete_button = "";
				}


				$user_details_query = mysqli_query($this->con, "SELECT first_name, last_name, profile_pic FROM users WHERE username='$added_by'");
				$user_row = mysqli_fetch_array($user_details_query);
				$first_name = $user_row['first_name'];
				$last_name = $user_row['last_name'];
				$profile_pic = $user_row['profile_pic'];
				?>


				<script>
					function toggle<?php echo $id; ?>() {

						var target = $(event.target);
						if(!target.is("a")) {
							//if target is not an a link, show the comments
							var element = document.getElementById("toggleComment<?php echo $id; ?>");

							if(element.style.display == "block")
								element.style.display = "none";
							else
								element.style.display = "block";

						}

					}	

				</script>
				<?php

				$comments_check = mysqli_query($this->con, "SELECT * FROM comments WHERE post_id= '$id'");
				$comments_check_num = mysqli_num_rows($comments_check);


				//Timeframe
				$date_time_now = date("Y-m-d H:i:s");
				$start_date = new DateTime($date_time); //Time of post
				$end_date = new DateTime($date_time_now); //Current time
				$interval = $start_date->diff($end_date);//Different between start_date and end_date

				if($interval->y >= 1){
					if($interval == 1)
						$time_message = $interval->y . " year ago"; //1 year ago
					else
						$time_message = $interval->y . " years ago"; //x years ago
				}
				else if($interval->m >= 1){
					if($interval->d >= 0){
						$days = " ago";
					}
					else if($interval->d >= 1){
						$days = $interval->d . " day ago";
					}
					else{
						$days = $interval->d . " days ago";
					}

					if($interval->m == 1){
						$time_message = $interval->m . " month" . $days;
					}
					else{
						$time_message = $interval->m . "months" . $days;
					}

				}
				else if($interval->d >= 1){
					if($interval->d == 1){
						$time_message = "Yesterday";
					}
					else{
						$time_message = $interval->d . " days ago";
					}	
				}
				else if($interval->h >= 1){
					if($interval->h == 1){
						$time_message = $interval->h . " hour ago";
					}
					else{
						$time_message = $interval->h . " hours ago";
					}	
				}
				else if($interval->i >= 1){
					if($interval->i == 1){
						$time_message = $interval->i . " minute ago";
					}
					else{
						$time_message = $interval->i . " minutes ago";
					}	
				}
				else{
					if($interval->s < 30){
						$time_message = "Just now";
					}
					else{
						$time_message = $interval->s . " seconds ago";
					}	
				}

				$str .= "<div class = 'status_post' onClick='javascript:toggle$id()'>
							<div class='post_profile_pic'>
								<img src='$profile_pic' width='50'>
							</div>

							<div class='posted_by' style='color:#ACACAC;'>
								<a href='$added_by'> $first_name $last_name </a> &nbsp;&nbsp;&nbsp;&nbsp;$time_message
							</div>
							<div id='post_body'>
								$body
								<br>
								<br>
								<br>
							</div>

							<div class='newsfeedPostOptions'>
								Comments($comments_check_num)&nbsp;&nbsp;&nbsp;
								<iframe src='like.php?post_id=$id' scrolling='no'></iframe>


							</div>

						</div>
						<div class='post_comment' id='toggleComment$id' style='display:none;'>
							<iframe src='comment_frame.php?post_id=$id' id='comment_iframe' frameborder='0'></iframe>
						</div>
						<hr>";

			} //End if($user_logged_obj->isFollowing($added_by))

			?>

			<script>
				$(document).ready(function(){
					$('#post<?php echo $id; ?>').on('click', function() {
						bootbox.confirm("Are you sure you want to delete this post?", function(result){

							$.post("includes/form_handers/delete_post.php?post_id=<?php echo $id; ?>", {result:result});

							if(result){
								location.reload();

							}

						});
					});
				});
			</script>
			<?php
			} //End while loop

			if($count > $limit){
				$str .= "<input type='hidden' class='nextPage' value='" . ($page + 1) . "'>
					<input type='hidden' class='noMorePosts' value='false'>";
			}
			else{
				$str .= "<input type='hidden' class='noMorePosts' value='true'>
					<p style='text-align: center;'> No more posts to show </p>";				
			}

		} //End if(mysqli_num_rows($data_query))

		echo $str;

	}

	public function loadProfilePosts($data, $limit) { 

		$page = $data['page'];
		$profile_username = $data['profile_username'];
		$userLoggedIn = $this->user_obj->getUsername();

		if($page == 1){
			$start = 0;
		}
		else{
			$start = ($page - 1) * $limit;
		}

		$str = ""; //String to return
		//FIGURE OUT HOW TO PASS THE CATEGORY AS A PARAMETER IN BUDGET.PHP
		$data_query = mysqli_query($this->con, "SELECT * FROM posts WHERE deleted = 'no' AND added_by='$profile_username' ORDER BY id DESC");

		if(mysqli_num_rows($data_query) > 0){//Has results

			$num_iterations = 0; //Number of results checked (not necessarily posted)
			$count = 1;

			while($row = mysqli_fetch_array($data_query)){
				$id = $row['id'];
				$body = $row['body'];
				$added_by = $row['added_by'];
				$date_time = $row['date_added'];

				if($num_iterations++ < $start)
					continue;
	
				//Once 10 posts have been loaded, break
				if($count > $limit) {
					break;
				}
				else{
					$count++;
				}

				if($userLoggedIn == $added_by){
					$delete_button = "<button class='delete_button btn-danger' id='post$id'>X</button>";
				}
				else{
					$delete_button = "";
				}


				$user_details_query = mysqli_query($this->con, "SELECT first_name, last_name, profile_pic FROM users WHERE username='$added_by'");
				$user_row = mysqli_fetch_array($user_details_query);
				$first_name = $user_row['first_name'];
				$last_name = $user_row['last_name'];
				$profile_pic = $user_row['profile_pic'];
				?>


				<script>
					function toggle<?php echo $id; ?>() {

						var target = $(event.target);
						if(!target.is("a")) {
							//if target is not an a link, show the comments
							var element = document.getElementById("toggleComment<?php echo $id; ?>");

							if(element.style.display == "block")
								element.style.display = "none";
							else
								element.style.display = "block";

						}

					}	

				</script>
				<?php

				$comments_check = mysqli_query($this->con, "SELECT * FROM comments WHERE post_id= '$id'");
				$comments_check_num = mysqli_num_rows($comments_check);


				//Timeframe
				$date_time_now = date("Y-m-d H:i:s");
				$start_date = new DateTime($date_time); //Time of post
				$end_date = new DateTime($date_time_now); //Current time
				$interval = $start_date->diff($end_date);//Different between start_date and end_date

				if($interval->y >= 1){
					if($interval == 1)
						$time_message = $interval->y . " year ago"; //1 year ago
					else
						$time_message = $interval->y . " years ago"; //x years ago
				}
				else if($interval->m >= 1){
					if($interval->d >= 0){
						$days = " ago";
					}
					else if($interval->d >= 1){
						$days = $interval->d . " day ago";
					}
					else{
						$days = $interval->d . " days ago";
					}

					if($interval->m == 1){
						$time_message = $interval->m . " month" . $days;
					}
					else{
						$time_message = $interval->m . "months" . $days;
					}

				}
				else if($interval->d >= 1){
					if($interval->d == 1){
						$time_message = "Yesterday";
					}
					else{
						$time_message = $interval->d . " days ago";
					}	
				}
				else if($interval->h >= 1){
					if($interval->h == 1){
						$time_message = $interval->h . " hour ago";
					}
					else{
						$time_message = $interval->h . " hours ago";
					}	
				}
				else if($interval->i >= 1){
					if($interval->i == 1){
						$time_message = $interval->i . " minute ago";
					}
					else{
						$time_message = $interval->i . " minutes ago";
					}	
				}
				else{
					if($interval->s < 30){
						$time_message = "Just now";
					}
					else{
						$time_message = $interval->s . " seconds ago";
					}	
				}

				$str .= "<div class = 'status_post' onClick='javascript:toggle$id()'>
							<div class='post_profile_pic'>
								<img src='$profile_pic' width='50'>
							</div>

							<div class='posted_by' style='color:#ACACAC;'>
								<a href='$added_by'> $first_name $last_name </a> &nbsp;&nbsp;&nbsp;&nbsp;$time_message

							</div>
							<div id='post_body'>
								$body
								<br>
								<br>
								<br>
							</div>

							<div class='newsfeedPostOptions'>
								Comments($comments_check_num)&nbsp;&nbsp;&nbsp;
								<iframe src='like.php?post_id=$id' scrolling='no'></iframe>


							</div>

						</div>
						<div class='post_comment' id='toggleComment$id' style='display:none;'>
							<iframe src='comment_frame.php?post_id=$id' id='comment_iframe' frameborder='0'></iframe>
						</div>
						<hr>";

			?>

			<script>
				$(document).ready(function(){
					$('#post<?php echo $id; ?>').on('click', function() {
						bootbox.confirm("Are you sure you want to delete this post?", function(result){

							$.post("includes/form_handers/delete_post.php?post_id=<?php echo $id; ?>", {result:result});

							if(result){
								location.reload();

							}

						});
					});
				});
			</script>
			<?php
			} //End while loop

			if($count > $limit){
				$str .= "<input type='hidden' class='nextPage' value='" . ($page + 1) . "'>
					<input type='hidden' class='noMorePosts' value='false'>";
			}
			else{
				$str .= "<input type='hidden' class='noMorePosts' value='true'>
					<p style='text-align: center;'> No more posts to show </p>";				
			}

		} //End if(mysqli_num_rows($data_query))

		echo $str;

	}

	public function loadBlogPosts ($data, $limit) {

		$page = $data['page'];
		$userLoggedIn = $this->user_obj->getUsername();

		if($page == 1){
			$start = 0;
		}
		else{
			$start = ($page - 1) * $limit;
		}

		$str = ""; //String to return
		$data_query = mysqli_query($this->con, "SELECT * FROM blog WHERE deleted='no' ORDER BY id DESC");

		if(mysqli_num_rows($data_query) > 0){//Has results

			$num_iterations = 0; //Number of results checked (not necessarily posted)
			$count = 1;

		while($row = mysqli_fetch_array($data_query)) {
			$id = $row['id'];
			$title = $row['title'];
			$body = $row['content'];
			$added_by = $row['author'];
			$date_time = $row['date_added'];
			$category = $row['category'];
			$ft_image = $row['main_img'];
			$path = "assets/images/featured_images/";

			//Check if user who posted the blog has their account closed
			$added_by_obj = new User($this->con, $added_by);
			if($added_by_obj->isClosed()){
				continue;
			}


				if($num_iterations++ < $start)
					continue;
	
				//Once 10 posts have been loaded, break
				if($count > $limit) {
					break;
				}
				else{
					$count++;
				}


			$user_details_query = mysqli_query($this->con, "SELECT username, profile_pic FROM users 
				WHERE username='$added_by'");
			$user_row = mysqli_fetch_array($user_details_query);
			$username = $user_row['username'];
			$profile_pic = $user_row['profile_pic'];

				//Timeframe
				$date_time_now = date("Y-m-d H:i:s");
				$start_date = new DateTime($date_time); //Time of post
				$end_date = new DateTime($date_time_now); //Current time
				$interval = $start_date->diff($end_date);//Different between start_date and end_date

				if($interval->y >= 1){
					if($interval == 1)
						$time_message = $interval->y . " year ago"; //1 year ago
					else
						$time_message = $interval->y . " years ago"; //x years ago
				}
				else if($interval->m >= 1){
					if($interval->d >= 0){
						$days = " ago";
					}
					else if($interval->d >= 1){
						$days = $interval->d . " day ago";
					}
					else{
						$days = $interval->d . " days ago";
					}

					if($interval->m == 1){
						$time_message = $interval->m . " month" . $days;
					}
					else{
						$time_message = $interval->m . "months" . $days;
					}

				}
				else if($interval->d >= 1){
					if($interval->d == 1){
						$time_message = "Yesterday";
					}
					else{
						$time_message = $interval->d . " days ago";
					}	
				}
				else if($interval->h >= 1){
					if($interval->h == 1){
						$time_message = $interval->h . " hour ago";
					}
					else{
						$time_message = $interval->h . " hours ago";
					}	
				}
				else if($interval->i >= 1){
					if($interval->i == 1){
						$time_message = $interval->i . " minute ago";
					}
					else{
						$time_message = $interval->i . " minutes ago";
					}	
				}
				else{
					if($interval->s < 30){
						$time_message = "Just now";
					}
					else{
						$time_message = $interval->s . " seconds ago";
					}	
				}

				$str .= "<div class = 'blog_post'>
							<div class='post_profile_pic'>
								<img src='$profile_pic' width='50' style='border-radius:100px'>
							</div>

							<div class='posted_by' style='color:#ACACAC;'>
								<a href='$added_by'> $username </a> &nbsp;&nbsp;&nbsp;&nbsp;$time_message
							</div>
							<div id='post_body'>
								<h3><a href='blog-detail.php?id=$id'>$title</a></h3>

								<img style='width:475px; height:250px;' src= '$path$ft_image'>

								<br>
								<br>
								<br>
								<hr>
							</div>";

		} //end while loop
			if($count > $limit){
				$str .= "<input type='hidden' class='nextPage' value='" . ($page + 1) . "'>
					<input type='hidden' class='noMorePosts' value='false'>";
			}
			else{
				$str .= "<input type='hidden' class='noMorePosts' value='true'>
					<p style='text-align: center;'> No more blog posts to show </p>";				
			}
	

		echo $str;
	
		} //End if(mysqli_num_rows($data_query))

	}

	public function getSingleBlog($blog_id) {

		$userLoggedIn = $this->user_obj->getUsername();

		$str = ""; //String to return
		$data_query = mysqli_query($this->con, "SELECT * FROM blog WHERE deleted = 'no' AND id='$blog_id' ORDER BY id DESC");

		if(mysqli_num_rows($data_query) > 0){//Has results

			$row = mysqli_fetch_array($data_query);
			$id = $row['id'];
			$title = $row['title'];
			$body = $row['content'];
			$added_by = $row['author'];
			$date_time = $row['date_added'];
			$category = $row['category'];
			$ft_image = $row['main_img'];
			$path = "assets/images/featured_images/";
			

			//Check if user who posted has their account closed
			$added_by_obj = new User($this->con, $added_by);
			if($added_by_obj->isClosed()){
				return;
			}

				$user_details_query = mysqli_query($this->con, "SELECT username, profile_pic FROM users WHERE username='$added_by'");
				$user_row = mysqli_fetch_array($user_details_query);
				$username = $user_row['username'];
				$profile_pic = $user_row['profile_pic'];
				?>


				<script>
					function toggle<?php echo $id; ?>() {

						var target = $(event.target);
						if(!target.is("a")) {
							//if target is not an a link, show the comments
							var element = document.getElementById("toggleComment<?php echo $id; ?>");

							if(element.style.display == "block")
								element.style.display = "none";
							else
								element.style.display = "block";

						}

					}	

				</script>
				<?php

				$comments_check = mysqli_query($this->con, "SELECT * FROM comments WHERE post_id= '$id'");
				$comments_check_num = mysqli_num_rows($comments_check);


				//Timeframe
				$date_time_now = date("Y-m-d H:i:s");
				$start_date = new DateTime($date_time); //Time of post
				$end_date = new DateTime($date_time_now); //Current time
				$interval = $start_date->diff($end_date);//Different between start_date and end_date

				if($interval->y >= 1){
					if($interval == 1)
						$time_message = $interval->y . " year ago"; //1 year ago
					else
						$time_message = $interval->y . " years ago"; //x years ago
				}
				else if($interval->m >= 1){
					if($interval->d >= 0){
						$days = " ago";
					}
					else if($interval->d >= 1){
						$days = $interval->d . " day ago";
					}
					else{
						$days = $interval->d . " days ago";
					}

					if($interval->m == 1){
						$time_message = $interval->m . " month" . $days;
					}
					else{
						$time_message = $interval->m . "months" . $days;
					}

				}
				else if($interval->d >= 1){
					if($interval->d == 1){
						$time_message = "Yesterday";
					}
					else{
						$time_message = $interval->d . " days ago";
					}	
				}
				else if($interval->h >= 1){
					if($interval->h == 1){
						$time_message = $interval->h . " hour ago";
					}
					else{
						$time_message = $interval->h . " hours ago";
					}	
				}
				else if($interval->i >= 1){
					if($interval->i == 1){
						$time_message = $interval->i . " minute ago";
					}
					else{
						$time_message = $interval->i . " minutes ago";
					}	
				}
				else{
					if($interval->s < 30){
						$time_message = "Just now";
					}
					else{
						$time_message = $interval->s . " seconds ago";
					}	
				}

				$str .= "<div class = 'blog_post'>
							<div class='post_profile_pic'>
								<img src='$profile_pic' width='50' style='border-radius:100px'>
							</div>

							<div class='posted_by' style='color:#ACACAC;'>
								<a href='$added_by'> $username </a> &nbsp;&nbsp;&nbsp;&nbsp;$time_message
							</div>
							<div id='post_body'>
								<h3>$title</h3>
								
								<img style='padding-top:15px; padding-bottom:15px; width:540px; height:auto;' src= '$path$ft_image'>
								<br>
								$body

								<br>
								<br>
								<br>
								<hr>
							</div>";


		} //End if(mysqli_num_rows($data_query))

		echo $str;

	}



}
?>
