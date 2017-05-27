<?php 

include("includes/header.php");


if(isset($_POST['submit_blog'])){

	//blog entry values

	//Title
	$title = $_POST['title']; 

	//Content
	$content = $_POST['content'];

	//Category
	$cat = $_POST['category'];

	//Current data and time
	$date_added = date("Y-m-d H:i:s");

	//Get username
	$added_by = $userLoggedIn;

	//Featured Image
	$main_img = $_FILES['ft_image']['name'];
	$temp_name = $_FILES['ft_image']['tmp_name'];

	switch ($_FILES['ft_image']['error']) {
        case UPLOAD_ERR_OK:
            break;
        case UPLOAD_ERR_NO_FILE:
            throw new RuntimeException('No file sent.');
        case UPLOAD_ERR_INI_SIZE:
        case UPLOAD_ERR_FORM_SIZE:
            throw new RuntimeException('Exceeded filesize limit.');
        default:
            throw new RuntimeException('Unknown errors.');
    }


	$uploaded = move_uploaded_file($temp_name, "assets/images/featured_images/$main_img");

	if($uploaded){
	//Insert blog post into the db
	$query = mysqli_query($con, "INSERT INTO blog VALUES ('', '$title', '$content', '$date_added', '$added_by', '$cat', '$main_img', '0', 'no')");
	echo "<script>alert('Success!')</script>";
	echo "<script>window.open('index.php')</script>";
	}
	else{
		echo "<script>alert('failed!')</script>";
	}
	//Increase num of posts by 1
	$user = new User($con, $userLoggedIn);
	$num_posts = $user->getNumPosts();
	$num_posts++;
	$update_query = mysqli_query($con, "UPDATE users SET num_posts='$num_posts' WHERE username='$added_by'");
}


?>

<div class="wrapper">
	<div class="user_details column">
		<h4 style="text-align:centered">choose a featured image</h4>
		<form class="blog_form" name="blog-entry" action="new-story.php" method="POST" enctype="multipart/form-data">
		<input type="file" name="ft_image" accept="image/*">
		<hr>
			 <select name="category">
			    <option value="" disable selcted>Select a category</option>
			    <option style="padding-left:0" value="" disable selcted>--LIVE--</option>
			    <option value="budget">Budget</option>
			    <option value="family">Family</option>
			    <option value="wellness">Wellness</option>
				<option value="career">Career</option>
				<option value="faith">Faith</option>
				<option style="padding-left:0" value="" disable selcted>--ENJOY--</option>
			    <option value="travel">Travel</option>
			    <option value="hobbies">Hobbies</option>
			    <option value="gadgets">Gadgets</option>
				<option value="retail therapy">Retail Therapy</option>
				<option value="weekended">Weekended</option>
				<option style="padding-left:0" value="" disable selcted>--PLAN--</option>
			    <option value="milestones">Milestones</option>
			    <option value="goals">Goals</option>
			    <option value="insurance">Insurance</option>
				<option value="credit">Credit</option>
				<option style="padding-left:0" value="" disable selcted>--INVEST--</option>
				<option value="entrepreneurship">Entrepreneurship</option>
			    <option value="startups">Startups</option>
			    <option value="stock market">Stock Market</option>
			    <option value="real estate">Real Estate</option>
				<option value="debt">Debt</option>
			 </select>
	</div>
			<div class="main_column column blog_form">
					<input type="text" name="title" placeholder="Title">
					<br><br>
					<textarea name="content" placeholder="Share your story..."></textarea>
					<br><br>
					<input type="submit" name="submit_blog" id="post_button" value="Publish">
					<br>
		</form>
	</div><!-- End .main_column .column -->
</div> <!-- end .wrapper -->
</body>
</html>