<?php 
session_start();
$db = new mysqli("localhost", "rrs680", "rut28vik", "rrs680");
    if($db->connect_error)
    {
        die("Connection failed: " . $db->connect_error);
    }   
$email = $_SESSION["email"];
$qFetch = "SELECT * FROM usersTT WHERE email = '$email'";
$rFetch = $db->query($qFetch);
$row = $rFetch->fetch_assoc();
$avatarURL = $row["avatarURL"];
if($row["loggedIn"])
{
    
}
else
{
    header("location: home.php");
    exit();
}

//form validation
$validate = true;
$msg = "checkpoint";
$textbox = $_POST["msg_textbox"];
$error="";
$uid = $row["uid"];
$regex_accesscode = "/^[a-zA-Z0-9]{6}$/";
$title = trim($_POST["msgtitle"]);
$accesscode = trim($_POST["accesscode"]);
$mysqlDate = date("Y-m-d H:i:s",strtotime($_POST["date_exp"]));
$currdate = date("Y-m-d H:i:s");
if($title == NULL || $title == "")
{
    $validate = false;
}
if (empty($_POST["msg_textbox"]))
{
    $validate = false;
}
$accesscodeMatch = preg_match($regex_accesscode, $accesscode);
if($accesscode == NULL || $accesscode =="" || $accesscodeMatch == false)
{
    $validate = false;
}
if($mysqlDate == NULL || $mysqlDate =="" || $mysqlDate < $currdate)
{
    $validate = false;
}

if($validate == true)
{
    $qmsg = "INSERT INTO msgsTT(uid,accesscode,msgtxt,msgExpDateTime, msgCreationDateTime, title) VALUES ('$uid','$accesscode','$textbox','$mysqlDate',NOW(), '$title') ";
    $rmsg = $db->query($qmsg);
    
    if($rmsg == true)
    {
        header("Location: msglist.php");
        $db->close();
        exit();
    }
    else
    {
        $error = "Generic error. Please retry.";
        $db->close();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8" />
         <link rel="shortcut icon" href="resources/img/favicon.ico" type="image/x-icon">
    <link rel="stylesheet" type="text/css" href="resources/css/style.css">
     <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Lato&amp;display=swap" />    
        <script type="text/javascript"src="resources/js/msgcreatevalidate.js"></script>
    <title> TeleTalk - msgcreate</title>
</head>
<body>
    <header>
        <div class="row">
            <a href="msglist.php"><img src="resources/img/TeleTalk.png" alt="TeleTalk logo" class="logo" /></a>
            <ul>
                <li> <img src="<?php echo $avatarURL?>" alt="Avatar" /><br /><a href="logout.php">logout</a></li>
            </ul>
        </div>
        
        <section class="msg_create_box">
            <form id="createmsg" method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>" enctype="multipart/form-data">
                <p> <img src="resources/img/avatar.png" alt="avatar"/></p> <br/>
                <label for="msgtitle">Title</label>
                <br/>
                <input type="text" name="msgtitle" id="msgtitle">
                <br/>
                <label for="msg_textbox">Enter message here</label>
                <br/>
                <textarea id="msg_textbox" name="msg_textbox"></textarea>
                <br />
                <span id="counter"></span>
                <br/>
                <label for="accesscode">Access Code</label>
                <br/>
                <input type="text" name="accesscode" maxlength="6" id="accesscode">
                <br/>
                <label for="date_exp">Enter message expiry date and time</label>
                <input type="datetime-local" name="date_exp" id="date_exp">
                <br/>
                <br/>
                <br/>
                <input type="submit" name="post_msg" value="POST">
            </form>
        </section>
    </header>
    <script type="text/javascript" src="resources/js/msgcreatevalidate-call.js"></script>
</body>
</html>