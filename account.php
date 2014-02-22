<?php
session_start();
//check for login
	if(empty($_SESSION['uid']))
	{
		header("Location: index.php");
	}

	$uid = $_SESSION['uid'];
?>
<!DOCTYPE html><html xmlns="http://broncoswap.csproject.org/account.php">
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

	<div id="myinfo">
	</div>

	<div id="myposts">
	</div>

                <div id="account">
<ul>
<li><a href="account.php">My Account</a></li>
<li><a href="logout.php">(logout)</a></li>
</ul>
                </div>



<div id="content">
        <div id="return1">
<a href="swap.php">Return to Swap</a>
        </div>

<div id="acountpage">
<div id="myaccount">
<h1>Account Information</h1>
<?php
	//create connection
	$con=mysqli_connect("localhost", "omi", "tharta107", "test");

	//Check connection
	if (mysqli_connect_errno($con))
	{
		echo "Failed to connect to MySQL: " . mysqli_connect_error();
	}

	$result = mysqli_query($con, "SELECT * FROM user_profile WHERE user_profile.profile_id='$uid'");
	
	while($row = mysqli_fetch_array($result))
	{
	echo "<h3>First name:           ";
	echo $row["first_name"] . "</h3>";
	echo "<h3>Last Name:            ";
	echo $row["last_name"] . "</h3>";
	echo "<h3>E-mail address:       "; 
	echo $row["email_address"] . "</h3>";
		
	echo "<br/>";
	echo "<br/>";
	}


	mysqli_close($con);
?>


</div>


<div id="my_postings">
<h1>My Postings</h1>
<?php
	//create connection
	$con=mysqli_connect("localhost", "omi", "tharta107", "test");

	//Check connection
	if (mysqli_connect_errno($con))
	{
		echo "Failed to connect to MySQL: " . mysqli_connect_error();
	}


	$result = mysqli_query($con, "SELECT * FROM post_information
					WHERE post_information.profile_id='$uid'
					ORDER BY post_information.entry_date DESC");

	while($row = mysqli_fetch_array($result))
	{
	echo "<a href='post.php?id=" . $row["post_id"] . "'>" . $row["post_name"] . "</a>";
	echo "&nbsp;&nbsp;Posted on: " . $row["entry_date"];


	echo "<br/>";

	}

	mysqli_close($con);
?>

</div>	
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


