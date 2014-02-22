<?php
session_start();

//compatibility library for PHP 5.5's "hash_password"
require("password.php");

//function to check registration
function checkRegister() 
{	
	//check that request method is post
	if($_SERVER['REQUEST_METHOD'] == 'POST')
	{
		//check to make sure all fields are entered
		if (!$_POST['firstname'] | !$_POST['lastname'] | !$_POST['email'] | !$_POST['passwd'] | !$_POST['passwd2'])
		{
			//if not, alert the user
			echo "<p class=\"errtxt\">Please fill out all of the fields</p>";
			die();
		}

		//Define the data from form
		$email=htmlspecialchars($_POST['email']);
		$passwd=htmlspecialchars($_POST['passwd']);
		$fname=htmlspecialchars($_POST['firstname']);
		$lname=htmlspecialchars($_POST['lastname']);
		$passwd2=htmlspecialchars($_POST['passwd2']);

		//define a regex
		$reg_email = '/^[a-z]+[@](scu.edu)$/';
		//check email against regex
		if(preg_match($reg_email, $email) === 0)
		{
			//if they don't match, we should alert
			echo "<p class=\"errtxt\">Please enter a valid SCU email address</p>";
			die();
		}
		//check to make sure passwords match
		if($passwd != $passwd2)
		{
			//if not, DIE
			echo "<p class=\"errtxt\">Passwords don't match, please try again</p>";
			die();
		}	

		//Connect to database
		$con=mysqli_connect("localhost", "omi", "tharta107", "test");
		//check connection
		if (! $con)
		{
			die("Failed to connect to MySQL");
		}

		//prepare a query to check if the email is in use
		$check=$con->prepare("SELECT email_address FROM User_Login
				WHERE email_address=?");
		//bind the variables to the query
		$check->bind_param('s', $email);
		//execute
		$check->execute();

		//bind the result to a var
		$check->bind_result($db_email);
		//check to see if we got a result
		if($check->fetch())
		{
			//the email already exists, so give the user an error message
			echo "<p class=\"errtxt\">Sorry, the email address " . $_POST['email'] . " is already in use</p>";
			//DIE DIE DIE
			die();
		}

		//hash the password
		$hash = password_hash($passwd, PASSWORD_BCRYPT);

		if(! $hash)
		{
			//there has been an error in the hash function
			echo "<p class=\"errtxt\">Error: password could not be encrypted. Please try again.</p>";
			die();
		}

		//first query to store email and salted hashed password
		$insert2 = $con->prepare("INSERT INTO User_Login (email_address, password) VALUES (?,?)");
		$insert2->bind_param('ss', $email, $hash);
		if(! $insert2->execute())
		{
			//if the query didn't go through, report the error
			echo "<p class=\"errtxt\">Error: could not save email/password information</p>";
			die();
		}

		//now we insert the plain text into the database
		$insert = $con->prepare("INSERT INTO user_profile (email_address, first_name, last_name) VALUES (?,?,?)");
		//bind the variables to the query
		$insert->bind_param('sss', $email, $fname, $lname);
		//execute
		if(! $insert->execute())
		{
			//if it didn't work, report the error
			echo "<p class=\"errtxt\">Error: could not save account information</p>";
			die();
		}	
	
		//store a message of successful registration	
		$_SESSION['message'] = "<h2>You have successfully registered. Login to get swappin'!</h2>";

		//close the connection
		mysql_close($con);

		//redirect to the login page
		header("Location: index.php");
	}
}
?>

<!DOCTYPE html>
<html xmlns="http://broncoswap.csproject.org/newuser.php">
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
        	<div id="newuser">
                	<p>
                	Create New User:
                	</p>
        	</div>

		<div id="newuser_info">
			<form name="Newuser" action="newuser.php" method="post">
                        First Name:<input type="text" name="firstname" maxlength="20" value="<?php echo $_POST['firstname'] ?>"/>
                        <br/>
                        Last Name: <input type="text" name="lastname" maxlength="20" value="<?php echo $_POST['lastname'] ?>"/>
                        <br/>
                        SCU Email:<input type="text" name="email" maxlength="25" value="<?php echo $_POST['email'] ?>"/>
                        <br/>
                        Password: <input type="password" maxlength="20" name="passwd"/>
                        <br/>
			Retype Password: <input type="password" maxlength="20" name="passwd2"/>
                        <br/>
			<br/>
		</div>
		<div id="submit1">                
        <input type="submit" value="Submit"/>
                </form>
		</div>
		<br/>
		<div id="signup1">
		<a href="index.php">Already have an account?</a>
		</div>

		<div>
			<?php checkRegister(); ?>
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
