<?php 
$msg = "Checkpoint";
$validate = true;
$error = "";


$regex_email = "/^\w+@[a-zA-Z_]+?\.[a-zA-Z]{2,3}$/";
$regex_pswd = "/^[(\S*)?\d+(\S*)]{8,}$/";


if ($_SERVER["REQUEST_METHOD"] == "POST")
{

    $email = trim($_POST["emailh"]);
    $password = trim($_POST["pswdh"]);
    
    
    $db = new mysqli("localhost", "rrs680", "rut28vik", "rrs680");
    if($db->connect_error)
    {
        die("Connection failed: " . $db->connect_error);
    }
    
    $q1 = "SELECT * FROM usersTT WHERE email = '$email' AND pswd = '$password'";
    $r1 = $db->query($q1);
    $row = $r1->fetch_assoc();
    
    if($email != $row["email"] && $password != $row["pswd"])
    {
        $validate = false;
    }
    else
    {
        
        $emailMatch = preg_match($regex_email, $email);
        if($email == NULL || $email =="" || $emailMatch == false)
        {
            $validate = false;
        }
        
        $pswdLen = strlen($password);
        $passwordMatch = preg_match($regex_pswd, $password);
        if($password == NULL || $password == "" || $passwordMatch == false || $pswdLen < 8)
        {
            $validate = false;
        }
    }
    
    if($validate == true)
    {
        session_start();
        $_SESSION["email"] = $row["email"];
        
        //Store logged in info in the database
        
        $qLoggedIn = "UPDATE usersTT SET loggedIn = true WHERE email = '$email'";
        $rLoggedIn = $db->query($qLoggedIn);
        if($rLoggedIn == true)
        {
            header("Location: msglist.php");
            $db->close();
            exit();
        }
    }
        else
        {
            $error = "Generic error. Please retry";
            $db->close();
        }
    }

$db = new mysqli("localhost", "rrs680", "rut28vik", "rrs680");

$query = "SELECT COUNT(mid) as ActiveMsgCount FROM msgsTT WHERE msgExpDateTime > NOW()";
$result = $db->query($query);

$query1 = "SELECT uid, handle, email, avatarURL FROM usersTT WHERE loggedIn = TRUE";
$result1 = $db->query($query1);
$rowAU = array();

$query2 = "SELECT COUNT(msid) as AllTimeMsgViews FROM msgstatsTT";
$result2 = $db->query($query2);
$rowTMV = $result2->fetch_assoc();

?>


<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8" />
        <link rel="shortcut icon" href="resources/img/favicon.ico" type="image/x-icon">
        <link rel="stylesheet" type="text/css" href="resources/css/style.css">
         <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Lato&amp;display=swap" /> 
        <script type="text/javascript" src="resources/js/homevalidation.js"></script>
        <title>TeleTalk- Home</title>
    </head>
    <body>
        <header>
                <div class="row">
                    <a href="home.php"><img src="resources/img/TeleTalk.png" alt="TeleTalk logo" class="logo" /></a>
                </div>
            <section class="listofusers"> 
            <h4>ACTIVE USERS</h4>
                <table class="userlist">
                    <tbody>
                        <?php while($rowAU = $result1->fetch_assoc()){?>
                        <tr>
                            <td>
                                <img src="<?=$rowAU["avatarURL"]?>" alt="avatar" class ="avatar_list" />
                            </td>
                            <td>
                                <?=$rowAU["handle"]?>
                            </td>
                        </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </section>
            <section class="msg">
                <div class="msginfo">
                    <h4>ACTIVE MESSAGES</h4>
                    <ul> 
                       <li><?php while($ActiveMsgCount = $result->fetch_assoc())
                            {
                                echo $ActiveMsgCount["ActiveMsgCount"];
                            }?></li>
                        <li> TOTAL MESSAGE VIEWS: <?php echo $rowTMV["AllTimeMsgViews"];?></li>
                    </ul>
                </div>
                </section>
            <section class="login"> 
                <h5>MEMBER LOGIN</h5>
                <p> <img src="resources/img/tele.png" alt="tele"/></p>
                <form class="loginform" id="loginh" method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>" enctype="multipart/form-data">
                    <table>
                        <tr>
                            <td><label for="emailh">E-MAIL</label></td>
                        </tr>
                        <tr>
                            <td><input type="email" name="emailh" id="emailh"></td>
                        </tr>
                        <tr> 
                            <td><label for="pswdh">PASSWORD</label></td>
                        </tr>
                        <tr>
                            <td><input type="password" name="pswdh" id="pswdh"></td>
                        </tr>
                    </table>
                    <input type="submit" name="login" value="LOGIN">
                </form>
                <h6>not a member? <a href="signup.php">signup!</a></h6>
                <?php if(isset($error)){echo $error;} else{echo '';}?>    
            </section>
           
        </header>
         <script type="text/javascript" src="resources/js/homevalidation-call.js"></script>
    </body>
    
</html>
<?php $db->close();?>