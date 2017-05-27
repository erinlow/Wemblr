<?php 
include("includes/header.php");

if(isset($_GET['id'])) {
	$id = $_GET['id'];
}
else{
	$id=0;
}

?>
<div class="wrapper">
	<div class="blog_column column">
		<div class="posts_area">
			<?php 
				$post = new Post($con, $userLoggedIn);
				$post->getSingleBlog($id);

			?>

		</div>

	</div>
</div>