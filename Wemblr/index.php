<?php

include("includes/header.php");

if(isset($_POST['post'])){
	$post = new Post($con, $userLoggedIn);
	$post->submitPost($_POST['post_text']);


}

	$num_following = (substr_count($user['following_array'], ",")) -1;
	$num_followers = (substr_count($user['followers_array'], ",")) -1;

?>

<div class="wrapper">
	<div class="user_details column">
		<img src="<?php echo $user['profile_pic']; ?>"> 

		<div class="user_details_left_right">
			<a href="<?php echo $userLoggedIn; ?>">
			<?php 

			echo $user['first_name'] . " " . $user['last_name'];

			?>
			</a>
			<p><?php echo "Following: " . $num_following; ?></p>
			<p><?php echo "Followers: " . $num_followers; ?></p>
		</div>
	</div>

	<div class="main_column column">
		<form class="post_form" action="index.php" method="POST">
			<textarea name="post_text" id="post_text" placeholder="Got something to say?"></textarea>
			<input type="submit" name="post" id="post_button" value="Share">
		</form>
		<a href="new-story.php">Write a new story</a>
		<hr>
		<div class="posts_area"></div>
		<img id="loading" src="assets/images/icons/loading.gif">

	</div><!-- End .main_column .column -->

	<script>
	var userLoggedIn = '<?php echo $userLoggedIn; ?>';

	//JQuery
	$(document).ready(function() {

		$('#loading').show();

		//Original ajax request for loading first posts
		$.ajax({
			url: "includes/handlers/ajax_load_posts.php",
			type: "POST",
			data: "page=1&userLoggedIn=" + userLoggedIn,
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
					url: "includes/handlers/ajax_load_posts.php",
					type: "POST",
					data: "page=" + page + "&userLoggedIn=" + userLoggedIn,
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