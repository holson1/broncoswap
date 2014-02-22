<?php
session_start();

	//check for login
	if(empty($_SESSION['uid']))
	{
		header("Location: index.php");
	}

	//store session user id
	$uid = $_SESSION['uid'];

//This function checks the form submitted via POST, returns errors, and submits to the database
function formCheck() {
	//if the user submitted the form	
	if($_SERVER['REQUEST_METHOD'] == 'POST')
	{	
		//if the proper fields weren't filled out	
		if($_POST['post_title']=="" || $_POST['price']=="" || $_POST['descrip']=="" )
		{
			//send an error text and die
			echo "<p class=\"errtxt\">Form error: Please fill out the required fields.</p>";
			die();
		}
	
		//grab the variables from POST
		$title = htmlspecialchars($_POST['post_title']);
		$cat = $_POST['category'];
		$cond = $_POST['condition'];
		$price = $_POST['price'];
		$descrip = htmlspecialchars($_POST['descrip']);
		$contact = htmlspecialchars($_POST['contact']);

		//grab variable from SESSION
		$uid = $_SESSION['uid'];
	
		//set a regex for price
		$reg_price = '/^\d{1,6}([\.]\d{2})?$/';

		//test price against the regex
		if(preg_match($reg_price, $price) === 0)
		{
			//the price doesn't match the format, so send an error text and die
			echo "<p class=\"errtxt\">Form error: price '$price' doesn't match the expected format (ex: 14.56, 400.49, 90).</p>";
			die();
		}

		//preserve new lines in the post desc 
		$descrip = nl2br($descrip);

		//further sanitization
		$title = trim($title);
		$title = stripslashes($title);
		$descrip = trim($descrip);
		$descrip = stripslashes($descrip);
		$contact = trim($contact);
		$contact = stripslashes($contact);

		//****************
		//FILE UPLOAD CODE
		//****************
		//check the file extension and size
		$allowedExts = array("gif", "jpeg", "jpg", "png");
		$temp = explode(".", $_FILES["file"]["name"]);
		$extension = end($temp);
		if ((($_FILES["file"]["type"] == "image/gif")
		|| ($_FILES["file"]["type"] == "image/jpeg")
		|| ($_FILES["file"]["type"] == "image/jpg")
		|| ($_FILES["file"]["type"] == "image/png"))
		&& ($_FILES["file"]["size"] < 2000000)
		&& in_array($extension, $allowedExts))
		{
			//if there is an error
			if ($_FILES["file"]["error"] > 0)
			{
				echo "<p class=\"errtxt\">: " . $_FILES["file"]["error"] . "</p>";
				die();
			}

			//if the file already exists
			if(file_exists("upload/" . $_FILES["file"]["name"]))
			{
				echo "<p class=\"errtxt\">" . $_FILES["file"]["name"] . " already exists.</p> ";
				die();
			}
		}
		else
		{
			//let the user know the file isn't valid
			echo "<p class=\"errtxt\">Invalid file</p>";
			die();
		}
	
		//input is now ready to be stored
		//INITIALIZE THE CONNECTION!
		$con = mysqli_connect('localhost', 'omi', 'tharta107', 'test');

		//test that connection
		if(! $con)
		{
			//error if connection fails
			die("Error: could not connect to mysql database");
		}

		//prepare a query to store the post
		$q = $con->prepare("INSERT INTO post_information (profile_id, contact_info, item_condition, post_name, price, category) VALUES (?, ?, ?, ?, ?, ?)");
		//bind parameters
		$q->bind_param('isssds', $uid, $contact, $cond, $title, $price, $cat);
		//moment of truth
		$q->execute();	

		//run a query to grab the post that was just made
		$result = mysqli_query($con, "SELECT * FROM post_information WHERE profile_id='$uid' ORDER BY post_id DESC");
		$row = mysqli_fetch_array($result);
		$post_id = $row[0];	

		//store the description offsite in a file named after the post_id
		file_put_contents("postdesc/" . $post_id . ".txt", $descrip);		
		//and change the permissions so it can be read later on
		chmod("postdesc/" . $post_id . ".txt", 0755);

		//STORING PICTURE
		//assign the img a new name
		$newfname = $post_id . "." . $extension;
		//store it in the db
		$picstore = mysqli_query($con, "UPDATE post_information SET img_ref='$newfname' WHERE post_id='$post_id'");
		//and check to make sure it was successful
		if(! $picstore)
		{
			echo "<p class=\"errtxt\">Sorry, picture reference could not be stored in database</p>";
			die();
		}
		//upload the file and give it the name of the post id
		move_uploaded_file($_FILES["file"]["tmp_name"],"upload/" . $newfname);
		//change the permissions, too
		chmod("upload/" . $newfname, 0755);
				
		//store a session message
		$_SESSION['message'] =  "<h3>Your post has been successfully saved.</h3>";
		
		//close connection
		mysqli_close($con);
		
		//redirect to the same page to clear fields
		header("Location: newpost.php");
	}
}
?>

<!DOCTYPE html><html xmlns="http://broncoswap.csproject.org/newpost.php">
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
		<div id="return1">
			<a href="swap.php">Return to Swap</a>
		</div>

		<div id="createpost">
			<h1>New Post</h1>
		</div>

		<div id="newpost_info">
			<form name="new_post" action="newpost.php" enctype="multipart/form-data" method="post">
			Post Title: <input id="posting_title" type="text" name="post_title" maxlength="100" value='<?php echo $_POST['post_title']; ?>'/>*
			<br>
			Category: 
			<select name="category">
				<option value="books">Books</option>
				<option value="furniture">Furniture</option>
				<option value="clothing">Clothing</option>
				<option value="electronics">Electronics</option>
				<option selected="selected" value="other">Other</option>
			</select>*
			<br>
			Item Condition:
			<select name="condition">
				<option value="poor">Poor</option>
				<option value="acceptable">Acceptable</option>
				<option selected="selected" value="good">Good</option>
				<option value="likenew">Like New</option>
				<option value="brandnew">Brand New</option>
			</select>*
			<br>
			Price: $<input type="text" name="price" maxlength="9" value='<?php echo $_POST['price']; ?>'/>*
			<br>
			<br>
			<textarea rows=15 cols=50 name="descrip" placeholder="Enter your item description here!" wrap="physical"><?php echo  htmlspecialchars($_POST['descrip']);?></textarea>*
			<br/>
			Image upload: <input type="file" name="file"/>
			<br/>
			Contact info: <input type="text" name="contact" maxlength="50" value='<?php echo $_POST['contact']; ?>'/>
			<br/>	
			<input type="submit" value="Submit"/>
			<input type="reset" value="Clear"/>
			</form>

			<div id="form_return">
			<?php
				//display the 'successful' submit message if it's set
				if(isset($_SESSION['message']))
				{
					echo $_SESSION['message'];
					//and remove the variable so it isn't shown in the future
					unset($_SESSION['message']);
				}
				
				//call the form check here (this ensures the page loads fully even if the php "dies" above
				formCheck();
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

