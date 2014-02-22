<?php
	session_start();

	//this will be at the top of every login-restricted file
	if(empty($_SESSION['uid']))
	{
		header("Location: index.php");
	}
?>

<!DOCTYPE html>
<html xmlns="http://broncoswap.csproject.org/swap.html">
<head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <link rel="stylesheet" type="text/css" href="style.css" />
        <title>BroncoSwap</title>
</head>

<body>
                <div id="wrapperheader">
                        <div id="broncohead">
                <img src="images/header.jpg"/>            
		</div>
                </div>
                <div id="account">
<ul>               
<li><a href="account.php">My Account</a></li>
<li><a href="logout.php">(logout)</a></li>
</ul>
		</div>


<div id="content">

	<div id="head">
		<div id="slogan">
			<br/>
			<br/>
			<h1>Welcome, <?php echo $_SESSION['fname']; ?>!</h1>
		</div>
	</div>

	<div id="leftmenu">

		<h3>New Posting</h3>	
		<ul><li><a href="newpost.php">Create a new posting</a></li></ul>	
		<hr/>
		<h3>Categories</h3>

		<ul>
		<li><a href="swap.php">All</a></li>
		<li><a href="swap.php?category=books">Books</a></li>
		<li><a href="swap.php?category=furniture">Furniture</a></li>
		<li><a href="swap.php?category=clothing">Clothing</a></li>
		<li><a href="swap.php?category=electronics">Electronics</a></li>
		<li><a href="swap.php?category=other">Other</a></li>
		</ul>
	</div>


<div id="posts">
<aside>
<?php
//initiate connection
	$con=new mysqli("localhost", "omi", "tharta107", "test");

	//validate connection
	if($con->connect_errno)
	{
		die("Error: cannot connect to MySQL");
	}

	//if there is a URL query string
	if(isset($_GET['category']))
	{
		//get the category from the query string
		$cat=$_GET['category'];
		//and grab all posts w/ specified category
		$result = mysqli_query($con, "SELECT U.email_address, P.post_id, P.post_name, P.entry_date 
					FROM user_profile U, post_information P
					WHERE U.profile_id=P.profile_id AND category='$cat'
					ORDER BY entry_date DESC");
	}
	else
	{
		//get the default category (most recent)
		$result = mysqli_query($con, "SELECT U.email_address, P.post_id, P.post_name, P.entry_date 
					FROM user_profile U, post_information P 
					WHERE U.profile_id=P.profile_id 
					ORDER BY entry_date DESC");
	}

	//print them all out
	while($row = mysqli_fetch_array($result))
	{
		//print a link to the post and the post name
		echo "<a href='post.php?id=" . $row['post_id'] . "'>" . $row['post_name'] . "</a>";
		echo "<br/>";
		//print the date it was posted
		echo "&nbsp;&nbsp;Posted on: " . $row['entry_date'];
		//print the user who entered it
		echo "&nbsp;&nbsp;by " . $row['email_address'];	
		echo "<br/>";
		echo "<hr/>";
	}

	//close the connection
	mysqli_close($con);
?>
</aside>

</div>

<div id="ads">
<img src="images/ad1.jpg"/>
<br>
<img src="images/ad2.jpg"/>
<br>
<img src="images/ad3.jpg"/>
<br>
<img src="images/ad4.jpg"/>
<br>
<img src="images/ad5.jpg"/>
<br>
<img src="images/ad6.jpg"/>
</div>



</div>
</div>

</body>
</html>
