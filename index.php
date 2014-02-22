<?php
session_start();

require('password.php');

//put code here to redirect user away from this page if they're logged in
if(isset($_SESSION['uid']))
{
	header("Location: swap.php");
}

//function to test and submit login information
function checkLogin()
{
	//if a POST was sent
	if ($_SERVER['REQUEST_METHOD'] == 'POST')
	{
		//if the fields have not been filled out
		if (! $_POST["email"] || ! $_POST["pswrd"])
		{
			//send an error message and die
			echo "<p class=\"errtxt\">You need to provide a username and password</p>";
			die();
		}

		//store the information
		$email = $_POST['email'];
		$password = $_POST['pswrd'];

		//connect to Datebase
		$con = mysqli_connect("localhost", "omi", "tharta107", "test");

		//check connection
		if (! $con)
		{
			die("Failed to connect to MySQL");
		}

		//Now create query that check if the profile exists
		$q = $con->prepare("SELECT UP.profile_id, UP.first_name, U.email_address, U.password
				FROM User_Login U, user_profile UP
				WHERE U.email_address=UP.email_address AND U.email_address=?
				LIMIT 1");

		//and bind the vars to the fields
		$q->bind_param('s', $email);
		//aaaand execute
		$q->execute();

		//bind the result to some variables
		$q->bind_result($uid, $firstname, $col_email, $col_pw);
		
		//if the values cannot be fetched
		if (! $q->fetch())
		{
			echo "<p class=\"errtxt\">Email not found in our records, please try again.</p>";
			die();
		}

		//test the hashed password
		if(! password_verify($password, $col_pw))
		{
			echo "<p class=\"errtxt\">Email and password do not match.</p>";
			die();
		}

		//create a session variable for email
		$_SESSION['email'] = $email;
		//and for user id
		$_SESSION['uid'] = $uid;
		//and lastly, for the first name (it's nice to see some personalization)
		$_SESSION['fname'] = $firstname;

		//close the connection
		mysqli_close($con);

		//head to swap.php
		Header("Location: swap.php");
	}
}
?>


<!DOCTYPE html>
<html xmlns="http://broncoswap.csproject.org">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<link rel="stylesheet" type="text/css" href="style.css" />
	<title>BroncoSwap</title>
</head>

<body>
	<div id="wrapperheader">
		<div id="broncohead">
		<img src="images/header.jpg" alt="broncoswap"/>
		</div>
	</div>

	<div id="menu">
	</div>
    
	<div id="content">

		<div id="login">
			<p>Login</p>
		</div>
		<div id="login_info">
			<form name="login" action="index.php" method="post">
			Email address:<input type="text" name="email"/>
			<br>
			Password: <input type="password" name="pswrd"/>
			<br>
			</div>
			<div id="submit">
			<input type="submit" value="Login"/>
			<input type="reset" value="Clear"/>
			</form>
<br>
<br>
</div>
<div id="signup">
			<a href="newuser.php">Sign up now</a>
			<br>
		</div>
		<div id="error_return">
			<?php
				session_start();
				//if a message has been sent form the registration page
				if(isset($_SESSION['message']))
				{
					//print it out once
					echo $_SESSION['message'];
					unset($_SESSION['message']);
				}
			
				//checks for login info sent via post	
				checkLogin();
			?>
		</div>	
	</div>
	<footer>
  		<div id="footermenu">
            		<ul>
               		<li class="menuitem"><a href="about1.html">About Us</a></li>
                	<li class="menuitem"><a href="contact1.html">Contact</a></li>
            		</ul>
  		</div>
		<p>copyright 2013 BroncoSwap</p>
	</footer>
</body>
</html>
