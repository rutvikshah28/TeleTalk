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
$msg = "checkpoint!";
$qMC = "SELECT m.mid, u.uid, u.handle, u.avatarURL FROM msgsTT m INNER JOIN usersTT u ON m.uid = u.uid AND m.msgExpDateTime > NOW() ORDER BY m.mid desc";
$rMC = $db->query($qMC);

if ($_SERVER["REQUEST_METHOD"] == "POST")
{
    $accesscode1 = $_POST["accesscode1"];
    $mid1 = $_POST["msgid"];
    
    $qAC = "SELECT * from msgsTT where mid = '$mid1'";
    $rAC = $db->query($qAC);
    $rowAC = $rAC->fetch_assoc();
    //validating
    $validateF = true;
    if($accesscode1 == NULL || $accesscode1 =="" || $accesscode1 != $rowAC["accesscode"])
    {
        $validateF = false;
    }
    
    if($validateF == true)
    {
        $_SESSION["mid"] = $mid1;
        $_SESSION["accesscode"] = $accesscode1;
        header("Location: msgview.php");
        exit();
    }
    else
    {
        $errorF = "Wrong accesscode. Please try again";
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
        <script type="text/javascript" src="resources/js/msglistvalidation.js"></script>
    <title> TeleTalk - msglist</title>
</head>
<body>
    <header>
        <div class="row">
            <a href="msglist.php"><img src="resources/img/TeleTalk.png" alt="TeleTalk logo" class="logo"/></a>
            <ul>
                <li> <img src="<?php echo $avatarURL?>" alt="Avatar" /><br/><a href="logout.php">logout</a></li>
            </ul>
        </div>
        <section class="messagelisttitle">
            Currently Active Messages are shown below:
        </section>
        <?php while($rowMC = $rMC->fetch_assoc()){?>
        <article class="messagelistboxes">
            <p class="img_align_msg"><img src="<?=$rowMC["avatarURL"]?>" alt="avatar" align="middle">
                <?=$rowMC["handle"]?></p>
            <br/><br/>
        <form id="list1" method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>" enctype="multipart/form-data">   
             <input type="hidden" id="msgid" name="msgid" value ="<?php echo $rowMC["mid"];?>">
            <label for="accesscode1">Enter the Access code to view the message: </label>
            <input type="text" name="accesscode1" id="accesscode1">  
            <br/>
            <input type="submit" name="submit" value="SUBMIT">
            <?php if(isset($errorF)){echo $errorF;} else{echo '';}?>
        </form>
        </article>
        <?php }?>
        <footer>
            <a href="msgcreate.php"><img src="resources/img/newmsg.png" alt="compose" /></a>
        </footer>
    </header>
    <script type="text/javascript" src="resources/js/msglistvalidation-call.js"></script>
</body>
</html>