<?php

//Declaring variable to prevent errors
$fname = ""; //First name
$lname = ""; // Last name
$user = ""; //userName
$em = ""; //email
$password = ""; //password
$password2 = ""; //password2
$date = ""; //Sign-up date
$error_array = array(); //Holds error messages

if(isset($_POST['register_button'])){

	//Registration form values

	//First Name
	$fname = strip_tags($_POST['reg_fname']); //remove HTML tags
	$fname = str_replace(' ', '', $fname); //remove spaces
	$fname = ucfirst(strtolower($fname)); //Uppercase first letter
	$_SESSION['reg_fname'] = $fname; //Stores first name into session variable

	//Last Name
	$lname = strip_tags($_POST['reg_lname']); //remove HTML tags
	$lname = str_replace(' ', '', $lname); //remove spaces
	$lname = ucfirst(strtolower($lname)); //Uppercase first letter
	$_SESSION['reg_lname'] = $lname; //Stores last name into session variable


	//Username
	$user = strip_tags($_POST['reg_username']); //remove HTML tags
	$_SESSION['reg_username'] = $user; //Stores username into session variable


	//Email
	$em = strip_tags($_POST['reg_email']); //remove HTML tags
	$em = str_replace(' ', '', $em); //remove spaces
	$em = ucfirst(strtolower($em)); //Uppercase first letter
	$_SESSION['reg_email'] = $em; //Stores email into session variable


	//Password
	$password = strip_tags($_POST['reg_password']); //remove HTML tags
	//Password2
	$password2 = strip_tags($_POST['reg_password2']); //remove HTML tags

	//Date
	$date = date("Y-m-d"); //Current date

	//Check if email is is valid format
	if(filter_var($em, FILTER_VALIDATE_EMAIL)) {
		$em = filter_var($em, FILTER_VALIDATE_EMAIL);

		//Check if email already exists in db
		$e_check = mysqli_query($con, "SELECT email FROM users WHERE email= '$em'");

		//Count the number of rows returned
		$num_rows = mysqli_num_rows($e_check);

		if($num_rows > 0){
			array_push($error_array, "Email already in use<br>");
		}
	}
	else{
		array_push($error_array, "Invalid email format<br>");
	}

	if(strlen($fname) > 25 || strlen($fname) < 2) {
		array_push($error_array, "Your first name must be between 2 and 25 characters<br>");
	}

	if(strlen($lname) > 25 || strlen($lname) < 2) {
		array_push($error_array, "Your last name must be between 2 and 25 characters<br>");
	}

	if($password != $password2){
		array_push($error_array, "Your passwords do not match<br>");
	}
	else{
		if(preg_match('/[^A-Za-z0-9]/', $password)) {
			array_push($error_array, "Your password can only contain English characters or numbers<br>");
		}
	}

	if(strlen($password) > 30 || strlen($password) < 5){
		array_push($error_array, "Your password must be between 5 and 30 characters<br>");
	}

	//check if username is already in the db
	$username_check = mysqli_query($con, "SELECT username FROM users WHERE username = '$user'");


	//Count the number of rows returned
	$num_rows_user = 0;
	$num_rows_user = mysqli_num_rows($username_check);

	if($num_rows_user > 0){
		array_push($error_array, "Username already in use<br>");
	}

	if(empty($error_array)){
		$password = md5($password); //encrypyts password before sending to database

		//Default profile picture
		$rand = rand(1,2); //Random number between 1 and 2

		if($rand = 1){
			$profile_pic = "assets/images/profile_pics/defaults/head_turqoise.png";
		}
		else if($rand = 2){
			$profile_pic = "assets/images/profile_pics/defaults/head_green_sea.png";
		}
		//Insert values into the db
		$query = mysqli_query($con, "INSERT INTO users VALUES ('', '$fname', '$lname', '$user', '$em', '$password', '$date', '$profile_pic', '0', '0', 'no', ',', ',', ',')");
		
		array_push($error_array, "<span>You're all set! Go ahead and login!</span><br>");

		//Clear session variables
		$_SESSION['reg_fname'] = "";
		$_SESSION['reg_lname'] = ""; 
		$_SESSION['reg_username'] = "";
		$_SESSION['reg_email'] = "";
	}
}

?>