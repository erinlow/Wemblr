<?php

include("includes/header.php");

?>


<div class="wrapper">
	<style type="text/css">
		.wrapper {
			padding-left: 0px;
			margin-left: 0px;
    		width:100%;
    		top:40;
    		padding: 0;
		}

	</style>


	<!-- hero section -->
	<div class="container-fluid" style="background-color:#7BB355; min-height:240px; padding-top:30px">
		<div class="container" style="width:72%">
			<div class="row">
				<div class="col-md-12" style=" text-align:center; color:#fff">
					<h2>The social ecosystem where women share financial advice</h2>

						<p style="font-size:16px">Living smart, Enjoying life, Planning moments and Investing wise 
	is our duty to ourselves. We will hack income inequality and socioeconomic disparities with real 
	stories, DIY tools and engaging blogs.</p>
				</div>
			</div><!-- end .row -->
		</div><!-- end .container -->
	</div><!-- end .container-fluid -->

	<!-- blog post section -->
	<div class="container-fluid" style="background-color:#fff; min-height:350px" >
			<div class="row" style="text-align:left; padding:50px;">

				<?php 

				$data_query = "SELECT * FROM blog ORDER BY id DESC LIMIT 3";
				$result = mysqli_query($con, $data_query);
				while($row = mysqli_fetch_array($result)){
				
					$path = "assets/images/featured_images/";
					$id = $row['id'];
					$title = $row['title'];
					$author = $row['author'];
					$ft_image = $row['main_img'];

					$user_details_query = mysqli_query($con, "SELECT * FROM users WHERE username='$author'");
					$user_array = mysqli_fetch_array($user_details_query);
					$profile_pic = $user_array['profile_pic'];

						echo '<div class="col-md-4">
								<img style="width:325px; height:200px; padding:5px" src="'. $path.$ft_image .'">
								<br>
								<img class="author_pic" style="width:50px;padding:5px; border-radius:100px" src="'. $profile_pic .'"><a href="'.$author.'"><small style="color:#7BB355; vertical-align:bottom;">'.$author.'</small> </a>
								<h4><a href="blog-detail.php?id='.$id.'">' . $title . '</a></h4>

							</div><!-- .col-md-4 -->';

				}

				?>
		</div><!-- end .container-fluid -->
	</div>

	<!-- download app section -->
	<div class="container-fluid" style="min-height:200px" >
			<div class="row" style="padding:50px">
				<div class="col-sm-12">
					<h1 style="color:#7BB355;">DOWNLOAD NOW.</h1>
					<div class="col-sm-6">
						<img id="apple-store" src="assets/images/icons/apple_store_badge.svg">
						<img id="google-play" style="width:150px" src="assets/images/icons/google-play-badge.png">
					</div>
				</div>
			</div><!-- End .row -->
	</div>
</div><!-- end .wrapper -->
</body>
</html>