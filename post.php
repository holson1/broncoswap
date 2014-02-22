<?php
	session_start();

	//check for login
	if(empty($_SESSION['uid']))
	{
		header("Location: index.php");
	}

?>

<!DOCTYPE html><html xmlns="http://broncoswap.csproject.org/post.php">
<head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <link rel="stylesheet" type="text/css" href="style.css" />
        <title>BroncoSwap</title>
</head>

<div id="body">
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
	<div id="return1">
		<a href="swap.php">Return to Swap</a>   
	</div>

	<div id="post_info">
<?php
	//check to make sure there's a post number 
	if(empty($_GET['id']))
	{
		echo "<h3>Sorry, the post you requested doesn't exist</h3>";
		die();
	}

	
	//store the id for use in the mysql query
	$id = $_GET['id'];

	//initialize the connection
	$con = new mysqli("localhost", "omi", "tharta107", "test");

	//test the connection	
	if($con->connect_errno)
	{
		die("Error: could not connect to the MySQL database");
	}

	//run the query
	$result = mysqli_query($con, "SELECT * FROM post_information P, user_profile U WHERE U.profile_id=P.profile_id AND post_id='$id'");

	//grab the appropriate fields 
	while($row=mysqli_fetch_array($result))
	{
		$post_name = $row['post_name'];
		$cat = $row['category'];
		$date = $row['entry_date'];
		$cond = $row['item_condition'];
		$price = $row['price'];
		$contact = $row['contact_info'];
		$email = $row['email_address'];
		$imgref = $row['img_ref'];
	}

	//close the connection 
	mysqli_close($con);

	//grab the description
	$descrip = file_get_contents("postdesc/" . $id . ".txt");
		
	//echo the fields to the html
	//echo post name
	echo "<h1 id=\"post_title\">" . $post_name . "</h1>";

	//if there is an img, echo it
	if($imgref)
	{
		echo "<div id=\"postimg\">";
		echo "<a href=\"upload/" . $imgref . "\"><img height=200px width=300px src=\"upload/" . $imgref . "\"></a>";
		//echo "<img src=\"upload/" . $imgref . "\" alt=\"\" />";
		echo "</div>";
	}

	
	//create a new div for the post description
        echo "<h4 id=\"description_title\">Description:</h4>";
        echo "<div id=\"post_description\">";
        echo "<p id=\"post_desc_text\">" . $descrip . "</p>";
        echo "</div>";

	//echo price
        echo "<h4 id=\"post_price\">Price: $" . $price . "</h4>";
 	//echo the contact info
        echo "<h4 id=\"post_contact\">Contact info: " . $contact . "</h4>";

	//echo post category
	echo "<h4 id=\"post_category\">Category: " . $cat . "</h4>";
	//echo condition
        echo "<h4 id=\"post_condition\">Condition: " . $cond . "</h4>";	
	//echo post date and email
	echo "<h4 id=\"post_date\">Posted on: " . $date . " by " . $email . "</h4>";
	
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
