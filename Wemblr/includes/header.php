<?php 

require 'config/config.php';
include("includes/classes/User.php");
include("includes/classes/Post.php");

if(isset($_SESSION['username'])){
	$userLoggedIn = $_SESSION['username'];
	$user_details_query = mysqli_query($con, "SELECT * FROM users WHERE username = '$userLoggedIn'");
	$user = mysqli_fetch_array($user_details_query);
}
else{ //User is not logged in
	header("Location: register.php");
}

?>


<html>
<head>
	<title>Wemblr</title>

	<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.3/jquery.min.js"></script>

	<!-- Javascript -->
	<script src="assets/js/bootstrap.js"></script>
	<script src="assets/js/bootbox.min.js"></script>
	<script src="assets/js/wemblr.js"></script>
	<script src="assets/js/jquery.jcrop.js"></script>
	<script src="assets/js/jcrop_bits.js"></script>
	<script src="https://cloud.tinymce.com/stable/tinymce.min.js"></script>
 	<script>tinymce.init({ mode:'specific_textareas', editor_selector:'mceEditor' });</script>

	<!-- CSS -->
	<link rel="stylesheet" type="text/css" href="assets/css/bootstrap.css">
	<link rel="stylesheet" type="text/css" href="assets/css/style.css">
	<link rel="stylesheet" type="text/css" href="//maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
	<link rel="stylesheet" type="text/css" href="assets/css/jquery.Jcrop.css">


</head>
<body>

	<div class="top_bar">
		<div class="logo">
			<a href="landing.php">wemblr</a>
		</div>

		<nav>
			<ul>
				<li><a href="landing.php">Home</a></li>
				<li><a href="index.php">Network</a></li>
				<li><a href="live.php">LIVE</a></li>
				<li><a href="enjoy.php">ENJOY</a></li>
				<li><a href="plan.php">PLAN</a></li>
				<li><a href="invest.php">INVEST</a></li>

				<li><a href="<?php echo $userLoggedIn; ?>">
					<?php echo $user['first_name']; ?></a></li>
				<li><a href="settings.php">
					<i class="fa fa-cog"></i></a></li>
				<li><a href="includes/handlers/logout.php">
					<i class="fa fa-sign-out"></i></a></li>
			</ul>
		</nav>

	</div><!-- End .top_bar-->

