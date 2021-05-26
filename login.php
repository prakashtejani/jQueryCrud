<?php 
require_once('dbconnection.php');
$userfound=0;
$loginpost=0;
$_SESSION['email']='';

if(isset($_POST['loginbtn']) && !empty($_POST['email']) && !empty($_POST['password']))
{ 
  $loginpost=1;
  //$dbconn=mysqli_connect('localhost','root','','test1');
  // Check connection
  if ($dbconn -> connect_errno) {
    echo "Failed to connect to MySQL: " . $mysqli -> connect_error;
    exit();
  }
  $email=$_POST['email'];
  $password=$_POST['password'];
  $loginqry="SELECT * from users where email='$email' and password='$password'";
  //echo $loginqry;
  
  if ($qryresult = mysqli_query($dbconn,$loginqry))
  {
    $userdetail=mysqli_fetch_assoc($qryresult);
    
    $userfound=$qryresult->num_rows;
    mysqli_free_result($qryresult);
    if($userfound){
      $_SESSION['email']=$userdetail['email'];
      $_SESSION['role_name']=$userdetail['role_name'];
      header('Location: listpage.php');
    }
  }
  
  
//print_r($userfound);exit;
  
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Login</title>

  <!-- Google Font: Source Sans Pro -->
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" />
</head>
<body>
  <?php 
  if(empty($_SESSION['role'])){?>

<div class="container my-4">
  <div id="loginbox" style="margin-top:50px;" class="mainbox col-md-6 col-md-offset-3 col-sm-8 col-sm-offset-2">                    
      <div class="panel panel-info" >
      <?php 
          if($loginpost && $userfound<=0)
          {
            echo "<p><strong>Please enter correct email & password</strong></p>";
            //continue;
          }
          ?>
        <div class="panel-heading">
            <div class="panel-title">Sign In</div>
            <div style="float:right; font-size: 80%; position: relative; top:-10px">&nbsp;</div>
        </div>     
        <div style="padding-top:30px" class="panel-body" >
            <div style="display:none" id="login-alert" class="alert alert-danger col-sm-12"></div>
            <form action="" method="post">
              <div style="margin-bottom: 25px" class="input-group">
                <span class="input-group-addon"><i class="glyphicon glyphicon-user"></i></span>
                <input type="email" name="email" class="form-control" id="email" placeholder="Enter Email">
              </div>
              <div style="margin-bottom: 25px" class="input-group">
                  <span class="input-group-addon"><i class="glyphicon glyphicon-lock"></i></span>
                  <input type="password" name="password" class="form-control" id="password" placeholder="Password">
              </div>
              <div style="margin-top:10px" class="form-group">
                <div class="col-sm-12 controls">
                  <input type="submit" name="loginbtn" class="form-control" id="submit" value="Login" />
                </div>
              </div>  
            </form>
          </div>
      </div>
    </div>
  </div>
                            

<?php } ?>
</body>
</html>