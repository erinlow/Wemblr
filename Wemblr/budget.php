<?php

include("includes/header.php");


?>

<!-- nav section -->

<div class="wrapper" style="width:100%">
	<div class="container-fluid" style="background-color:#e6eae8; min-height:200px; padding-top:25px">
		<div class="container" style="width:94%">
			<div class="row">
				<div class="col-md-12" style="color:#000">
					<h2>Budget</h2>
					<hr style="border: 1px solid #696969">
						<div class="sec-nav">
							<ul>
								<li><a href="budget.php">Budget</a></li>
								<li><a href="family.php">Family</a></li>
								<li><a href="wellness.php">Wellness</a></li>
								<li><a href="career.php">Career</a></li>
								<li><a href="faith.php">Faith</a></li>
							</ul>
						</div>
					<hr style="border: 1px solid #696969">
				</div>
			</div><!-- end .row -->
		</div><!-- end .container -->
	</div><!-- end .container-fluid -->
</div><!-- end .wrapper -->

<div class="wrapper">

	<div class="blog_column column">

		<div class="posts_area"></div>
		<img id="loading" src="assets/images/icons/loading.gif">

	</div><!-- End .main_column .column -->

	<script>
	var userLoggedIn = '<?php echo $userLoggedIn; ?>';

	//JQuery
	$(document).ready(function() {

		$('#loading').show();

		//Original ajax request for loading first blog posts
		$.ajax({
			url: "includes/handlers/ajax_load_blog_posts.php",
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
					url: "includes/handlers/ajax_load_blog_posts.php",
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