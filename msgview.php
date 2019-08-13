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
$uid = $row["uid"];
$smid = $_SESSION["mid"];
$saccesscode = $_SESSION["accesscode"];

$Qmsg = "SELECT m.mid, u.uid, u.handle, u.avatarURL, m.msgtxt , m.title FROM msgsTT m INNER JOIN usersTT u ON m.uid = u.uid WHERE m.mid = '$smid' AND m.accesscode = '$saccesscode'";
$Rmsg = $db->query($Qmsg);
$Rowmsg = $Rmsg->fetch_assoc();

$Qvl = "SELECT u.handle, u.avatarURL, ms.viewDateTime FROM usersTT u INNER JOIN msgstatsTT ms ON ms.uid = u.uid AND ms.mid = '$smid' ORDER BY ms.viewDateTime DESC LIMIT 5";
$Rvl = $db->query($Qvl);
?>


<!DOCTYPE html>
<html lang="en">
    <head>
    <meta charset="UTF-8" />
    <link rel="shortcut icon" href="resources/img/favicon.ico" type="image/x-icon" />
    <link rel="stylesheet" type="text/css" href="resources/css/style.css" />
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Lato&amp;display=swap" />   
    <title> TeleTalk - msgdisplay</title>
</head>
<body>
    <header>
        <div class="row">
            <a href="msglist.php"><img src="resources/img/TeleTalk.png" alt="TeleTalk logo" class="logo" /></a>
            <ul>
                <li> <img src="<?php echo $avatarURL?>" alt="Avatar" /><br /><a href="logout.php">logout</a></li>
            </ul>
        </div>
        <section class="msg_display_box">
            <img src="<?=$Rowmsg["avatarURL"]?>" alt="Avatar" /> <br />
            <h4><?=$Rowmsg["handle"]?></h4>
            <br />
            <h4><?=$Rowmsg["title"]?></h4>
            <p><?=$Rowmsg["msgtxt"]?></p>
            <br />
            <div class="log_box">
                VIEW LOG
                <?php while($Rowvl = $Rvl->fetch_assoc()){?>
                <p><img src="<?=$Rowvl["avatarURL"]?>" alt="Avatar" /><?=$Rowvl["handle"]?><?=$Rowvl["viewDateTime"]?></p>
                <?php ;}?>
            </div>
        </section>
    </header>
</body>
</html>

<?php 

    
    $qV = "SELECT mid FROM msgsTT WHERE uid = '$uid'";
    $rV = $db->query($qV);
    $row1 = $rV->fetch_assoc();
    $mid = $row1["mid"];  
    $qIns = "INSERT INTO msgstatsTT(uid, mid, viewDateTime) VALUES ('$uid', '$smid', NOW())";
    $rIns = $db->query($qIns);
    $db->close();                      
?>