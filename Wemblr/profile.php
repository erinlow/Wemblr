<?php
include("includes/header.php");

if(isset($_GET['profile_username'])){
	$username = $_GET['profile_username'];
	$user_details_query = mysqli_query($con, "SELECT * FROM users WHERE username='$username'");
	$user_array = mysqli_fetch_array($user_details_query);

	$num_following = (substr_count($user_array['following_array'], ",")) - 1;
	$num_followers = (substr_count($user_array['followers_array'], ",")) - 1;
}


if(isset($_POST['unfollow'])) {
	$user = new User($con, $userLoggedIn);
	$user->unFollow($username);
}

if(isset($_POST['follow'])) {
	$user = new User($con, $userLoggedIn);
	$user->follow($username);
}


?>
<div class="wrapper">
	<style type="text/css">
		.wrapper {
			margin-left: 0px;
    		padding-left: 0px;
		}

	</style>

	<div class="main_column column" id="profile-details">
		<div class="col-md-6">
			<div class="profile_info">
				<div class="row">
					<div class="col-sm-12">
						<h2><?php echo $user_array['first_name']. ' ' . $user_array['last_name']; ?></h2>
					</div>
				</div><!-- end .row -->
				<div class="row">
					<div class="col-sm-6">
						<p><?php echo "Following: " . $num_following; ?></p>
					</div>
					<div class="col-sm-6">
						<p><?php echo "Followers: " . $num_followers; ?></p>
					</div>
				</div><!-- end .row -->
			</div>
		</div>
		<div class="col-md-6">
			<div class="row" style="text-align:center">
				<img src="<?php echo $user_array['profile_pic']; ?>">
			</div><!-- end .row -->
			<div class="row" style="text-align:center;">
			
			<form action="<?php echo $username; ?>" method="POST">
				<?php 
				$profile_user_obj = new User($con, $username); 
				if($profile_user_obj->isClosed()) { 
					header("Location: user_closed.php");
				}
				$loggedin_user_obj = new User($con, $userLoggedIn); 
				if($userLoggedIn != $username){
					if($loggedin_user_obj->isFollowing($username)){
						echo '<input type="submit" name="unfollow" class="danger" value="Unfollow"><br>';
					}
					else{
						echo '<input type="submit" name="follow" class="success" value="Follow"><br>';
					}
				}
				?>

			</form>


			</div><!-- end .row -->
		</div>

	</div>

	<div class="main_column column">

		<?php 
			if($userLoggedIn == $username){
			 echo
				'<form class="post_form" action="" method="POST">
					<textarea name="post_text" id="post_text" placeholder="Got something to say?"></textarea>
					<input type="submit" name="post" id="post_button" value="Share">
				</form>
				<a href="new-story.php">Write a new story</a>
				<hr>';
			}
		?>

		<div class="posts_area"></div>
		<img id="loading" src="assets/images/icons/loading.gif">

	</div><!-- end .main_column column -->

	<script>
	var userLoggedIn = '<?php echo $userLoggedIn; ?>';
	var profile_username = '<?php echo $username; ?>';

	//JQuery
	$(document).ready(function() {

		$('#loading').show();

		//Original ajax request for loading first posts
		$.ajax({
			url: "includes/handlers/ajax_load_profile_posts.php",
			type: "POST",
			data: "page=1&userLoggedIn=" + userLoggedIn + "&profile_username=" + profile_username, 
			cache: false,

			success: function(data) {
				$('#loading').hide(); //Don't show the loading sign anymore
				$('.posts_area').html(data);
			}
		});

		$(window).scroll(function(){
			var height = $('.posts_area').height(); //Div containing posts
			var scroll_top = $(this).scrollTop();
			var page = $('.posts_area').find('.nextPage').val();
			var noMorePosts = $('.posts_area').find('.noMorePosts').val();

			if ((document.body.scrollHeight == document.body.scrollTop + window.innerHeight) & noMorePosts == 'false'){
				//Got to the bottom of the page
				$('#loading').show();

				var  ajaxReq = $.ajax({
					url: "includes/handlers/ajax_load_profile_posts.php",
					type: "POST",
					data: "page=" + page + "&userLoggedIn=" + userLoggedIn + "&profile_username=" + profile_username,
					cache: false,

					success: function(response) {
						$('.posts_area').find('.nextPage').remove(); //Remove current .nextPage
						$('.posts_area').find('.noMorePosts').remove(); //Remove current .nextPage

						$('#loading').hide(); //Don't show the loading sign anymore
						$('.posts_area').append(response);
					}
				});		
			}//End if statement

			return false;

		}); //(window).scroll(function()
	});

	</script>


</div> <!-- end .wrapper -->
</body>
</html>

