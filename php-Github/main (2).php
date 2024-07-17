<?php
function INSERT_user($connection,$username,$password,$phone){
     try
    {
    $put=$connection->prepare("INSERT INTO user (username,password,phone) VALUES (:user,:pass,:phone)");
    $put->bindParam(':user',$username);
    $put->bindParam(':pass',$password);
    $put->bindParam(':phone',$phone);
    $result=$put->execute();
    return $result;
    }
    catch(PDOException $e)
    {
        echo "Error: " . $e->getMessage();
    }
}
function SELECT_user($connection,$username,$field){
     try
    {
    $get= $connection->prepare("SELECT $field from user WHERE username =:user ");
    $get->bindParam(':user',$username);
    $get->execute();
    $get->setFetchMode(PDO::FETCH_BOTH );
    $result=$get->fetchAll();
    return $result;
    }
    catch(PDOException $e)
    {
        echo "Error: " . $e->getMessage();
    }
}
?>
<?php
if ($_SERVER["REQUEST_METHOD"] == "POST"){
   
    $host="localhost";
    $user="digiai_first";
    $pass="Nima@228";
    $dbname="digiai_first";
    try
   {
    $conn = new PDO("mysql:host=$host;dbname=$dbname", $user, $pass);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    }
    catch(PDOException $e)
    {
  echo "Error: " . $e->getMessage();
    }
   if(isset($_POST["login"]))
   {
     $username=$_POST["login-user"];
     $password=$_POST["login-pass"];
     $error_login="";
     $result=SELECT_user($conn,$username,'password');// len result != 0 =>ok
     if(count($result) == 0)
     {
        $error_login="نام کاربری مورد نظر یافت نشد"; 
     }//error not valid username
     elseif($result[0][0] != $password)
     {
         $error_login="رمز عبور را اشتباه وارد کردید";
     }//error incorrect pass
     else{
         echo "welcome:";
         echo $username;
         
     }//for correct user and pass
   }
   if(isset($_POST["signup"])){
       $username=$_POST["signup-user"];
       $password=$_POST["signup-pass"];
       $phone=$_POST["signup-phone"];
       $error_login="";
       $valid=SELECT_user($conn,$username,'username');
       if(count($valid) != 0)
       {
           $error_login="نام کاربری تکراری است";
       }
       else
       {
        $result=INSERT_user($conn,$username,$password,$phone);//return 1 == ok
       }
   }
}
?>
<!DOCTYPE html>
<html>
<head>
  <link rel="stylesheet" href="project.css">
  <script src="project.js"></script>
</head>
<body>
  <div class="form-modal">
    
    <div class="form-toggle">
        <button id="login-toggle" onclick="toggleLogin()">ورود</button>
        <button id="signup-toggle" onclick="toggleSignup()">ثبت نام</button>
    </div>

    <div id="login-form">
        <form  name="login" onsubmit="return validatelogin()" method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
            <input type="text" name="login-user" placeholder="نام کاربری "/>
            <input type="password" name="login-pass"  placeholder="رمز عبور"/>
            <p id="error-login" style="color:red;"></p>
            <button type="submit" class="btn login" name="login" value="login" >ورود</button>
            <p style="color:red;"><?php echo $error_login ?></p>
            <hr/>

        </form>
    </div>

    <div id="signup-form">
        <form name="signup" onsubmit="return validatesignup()" method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
            <input type="text" name="signup-phone" placeholder="شماره موبایل"/>
            <input type="text" name="signup-user" placeholder="نام کاربری"/>
            <input type="password" name="signup-pass" placeholder="رمز عبور"/>
            <input type="password" name="signup-pass_2" placeholder="تکرار رمز عبور">
            <p id="error-signup" style="color:red;"></p>
            <button type="submit" class="btn signup"  name="signup" value="signup">ایجاد حساب</button>
         <!--  <p style="color:red;"><?php echo $error_signup ?></p>-->
            <hr/>
           
        </form>
    </div>

</div>

</body>
</html>
