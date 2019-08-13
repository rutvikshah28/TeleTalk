<?php
$validate = true;
$validateL = true;
$error = "";
$regex_email = "/^\w+@[a-zA-Z_]+?\.[a-zA-Z]{2,3}$/"; 
$regex_pswd = "/^[(\S*)?\d+(\S*)]{8,}$/";
$regex_uname= "/^[a-zA-Z0-9_-]+$/";
$email = "";


if ($_SERVER["REQUEST_METHOD"] == "POST")
{
    $email = trim($_POST["emails"]);
    $password = trim($_POST["pswds"]);
    $passwordc = trim($_POST["pswdc"]);
    $handle = trim($_POST["uname"]);
    $fname = trim($_POST["fname"]);
    $lname = trim($_POST["lname"]);
    
    
    $emailh = trim($_POST["emailh"]);
    $pswdh = trim($_POST["pswdh"]);
    
    
    $db = new mysqli("localhost", "rrs680", "rut28vik", "rrs680");
    if($db->connect_error)
    {
        die("Connection failed: " . $db->connect_error);
    }   
    
    //Checking login form validation
    $qL = "SELECT * FROM usersTT WHERE email = '$emailh' AND pswd = '$pswdh'";
    $rL = $db->query($qL);
    $row = $rL->fetch_assoc();
   
    
    if($emailh != $row["email"] && $pswdh != $row["pswd"])
    {
        $validateL = false;
    }
    else
    {
        
        $emailMatchh = preg_match($regex_email, $emailh);
        if($emailh == NULL || $emailh =="" || $emailMatchh == false)
        {
            $validateL = false;
        }
        
        $pswdLenL = strlen($pswdh);
        $passwordMatchh = preg_match($regex_pswd, $pswdh);
        if($pswdh == NULL || $pswdh == "" || $passwordMatchh == false || $pswdLenL < 8)
        {
            $validateL = false;
        }
    }
    
    if($validateL == true)
    {
        session_start();
        $_SESSION["email"] = $row["email"];
        
        //Store logged in info in the database
        
        $qLoggedIn = "UPDATE usersTT SET loggedIn = true WHERE email = '$emailh'";
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
            $errorL = "Generic error. Please retry Logging In.";
        }
    
    
    
    
    
    
    
    
    //if email is taken, validation is false
    
    $q1 = "SELECT * FROM usersTT WHERE email = '$email'";
    $r1 = $db->query($q1);
    if($r1->num_rows > 0)
    {
        $validate = false;
        $error1 = "Email address already exists";
    }
    else
    {

        $emailMatch = preg_match($regex_email, $email);
        if($email == NULL || $email == "" || $emailMatch == false)
        {
            $validate = false;
        }
        
        
        $pswdslen = strlen($password);
        $pswdMatch = preg_match($regex_pswd, $password);
        if($password == NULL || $password == "" || $pswdMatch == false || $password != $passwordc || pswdslen < 8)
        {
            $validate = false;
        }
        if($fname == NULL || $fname == "")
        {
            $validate = false;
        }
        if($lname == NULL || $lname == "")
        {
            $validate = false;
        }
        
        //File handling
        
        $target_dir = "uploads/";
        $target_file = $target_dir . basename($_FILES["fileToUpload"]["name"]);
        $uploadOk = 1;
        $imageFileType = strtolower(pathinfo($target_file,PATHINFO_EXTENSION));
        // Check if image file is a actual image or fake image
        if(isset($_POST["submit"])) {
            $check = getimagesize($_FILES["fileToUpload"]["tmp_name"]);
            if($check !== false) {
                $uploadOk = 1;
            } else {
                $uploadOk = 0;
            }
        }
        // Allow certain file formats
        if($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg"
                && $imageFileType != "gif" ) {
            $uploadOk = 0;
        }
        // Check if $uploadOk is set to 0 by an error
        if ($uploadOk == 0) {
            $validate = false;
            // if everything is ok, try to upload file
        } else {
            if (move_uploaded_file($_FILES["fileToUpload"]["tmp_name"], $target_file)) { $validate = true;}
	 else {
                $validate = false;
            }
        }
    }
    
    if($validate == true)
    {
        //inserting data to usersTT
        $INSq = "INSERT INTO usersTT(Fname,Lname,email,pswd,handle,avatarURL,userCreationDateTime) VALUES ('$fname', '$lname', '$email', '$password', '$handle', '$target_file',NOW())";
        
        $r2 = $db->query($INSq);
        if($r2 == true)
        {

            header("Location: home.php");
            $db->close();
            exit();
        }
    }
    else
    {
        $error = "Unexpected error, please check for empty fields and try again!";
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
        <script type="text/javascript" src="resources/js/signupvalidation.js"></script>
        <script type="text/javascript" src="resources/js/homevalidation.js"></script>
    <title> TeleTalk - signup</title>
</head>
<body>
    <header>
        <div class="row">
            <a href="home.php"><img src="resources/img/TeleTalk.png" alt="TeleTalk logo" class="logo"></a>
        </div>
        <section class="form_signup">
             <div class="signup">
                <h4> SIGNUP FOR MEMBERSHIP</h4>
                <form id = "signupform" method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>" enctype="multipart/form-data">
                    <p> <img src="resources/img/avatar.png" alt="avatar" /></p> <br />
                    <div class="img_upload">
                    <label for="fileToUpload">Avatar:</label>
                    <input type="file" name="fileToUpload" accept="image/*" id="fileToUpload"/>
                    <br/><br/>
                    <label for="fname">First Name</label>
                    <input type="text" name="fname" id="fname">
                    <br/>
                    <label for="lname">Last Name</label>
                    <input type="text" name="lname" id="lname">
                    <br/>
                    <br/>
                    <label for="uname">username</label>
                    <input type="text" name="uname" id="uname">
                    <br/>    
                    <label for="emails">E-MAIL</label>
                    <input type="email" name="emails" id="emails">
                    <br/>
                    <label for="pswds">PASSWORD</label>
                    <input type="password" name="pswds" id="pswds">
                    <br/>
                    <label for="pswdc">Confirm Password</label>
                    <input type="password" name="pswdc" id="pswdc">
                    <input type="submit" name="logins" value="Sign-Up">
                    <br />
                    <br />
                    <?php if(isset($error1)){echo $error1;} else if(isset($error)){echo $error;} else{echo '';}?>
                    </div>
                </form>
            </div>
            <script type="text/javascript" src="resources/js/signupvalidation-call.js"></script>
            <div class="loginform1">
                <h5>MEMBER LOGIN</h5>
                <p> <img src="resources/img/tele.png" alt="tele" /></p>
                <form id="loginh" method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>" enctype="multipart/form-data" >
                    <label for="emailh">E-MAIL</label>
                    <br/>
                    <input type="email" name="emailh" id="emailh">
                    <br/>
                    <label for="pswdh">PASSWORD</label>
                    <br/>
                    <input type="password" name="pswdh" id="pswdh">
                    <br/>
                    <input type="submit" name="loginh" value="LOGIN">
                </form>
                <?php if(isset($errorL)){echo $errorL;} else{echo '';}?>
            </div>
           <script type="text/javascript" src="resources/js/homevalidation-call.js"></script>
        </section>
    </header>
</body>
</html>