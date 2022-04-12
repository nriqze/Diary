<?php

session_start();

$error = "";

if(array_key_exists('logout', $_GET)) {
    
    unset($_SESSION);
    setcookie("id", "", time() - 60*60);
    $_COOKIE["ID"] = "";
    
} else if ((array_key_exists("id", $_SESSION) AND $_SESSION['id']) OR (array_key_exists("id", $_COOKIE) AND $_COOKIE['id'])) {
    
    header("location: loggedIn.php");
    
    
}

if (array_key_exists("submit", $_POST)) {
    
    include("connection.php");
    
    if (!$_POST{'email'}) {
        
        $error .= "An email adress is required<br>"; 
        
    }
    
    if (!$_POST{'password'}) {
        
        $error .= "A password is required<br>"; 
        
    }
    
    if ($error != "") {
        
        $error = "<p>There were errors in your form</p>".$error;
        
    } else {
        
        if($_POST['signup'] == '1') {
        
            $query = "SELECT id FROM users WHERE email = '".mysqli_real_escape_string($link, $_POST['email'])."' LIMIT 1";
        
            $result = mysqli_query($link, $query);

            if (mysqli_num_rows($result) > 0) {

                $error = "That email adress is taken!";

            } else {

                $query = "INSERT INTO users (email, password, diary) VALUES ('".mysqli_real_escape_string($link, $_POST['email'])."', '".mysqli_real_escape_string($link, $_POST['password'])."', '')";

                if (!mysqli_query($link, $query)) {

                    $error = "<p>Could not sign you up - Please try again later.</p>";

                } else {

                    $query = "UPDATE users SET password = '".md5(md5(mysqli_insert_id($link)).$_POST['password'])."' WHERE id = ".mysqli_insert_id($link)." LIMIT 1";

                    mysqli_query($link, $query);

                    $_SESSION["id"] = mysqli_insert_id($link);

                    if ($_POST['stayloggedin'] == '1') {

                        setcookie("id", mysqli_insert_id($link), time() + 60*60*24*365);

                    }

                    header("location: loggedIn.php");

                }

            }
            
        } else { // verify if the email is correct
            
            $query = "SELECT * FROM users WHERE email = '".mysqli_real_escape_string($link, $_POST['email'])."'";
            
            $result = mysqli_query($link, $query);
            
            $row = mysqli_fetch_array($result);
            
            if (isset($row)) {
                
                $hashedPassword = md5(md5($row['id']).$_POST['password']);
                
                if($hashedPassword == $row['password']) {
                    
                    $_SESSION['id'] = $row['id'];
                    
                    if ($_POST['stayloggedin'] == '1') {

                        setcookie("id", $row['id'], time() + 60*60*24*365);

                    }
                    
                    header("location: loggedIn.php");
                    
                } else {
                    
                    $error = "That email/password combination could not be found.";
                    
                }
                
            } else {
                
                $error = "That email/password combination could not be found.";
                
            }
            
        }
        
    }

}

?>

<?php include("header.php"); ?>

<div class="container" id="homePageContainer" >

    <h1>Secret Diary</h1>

    <p><strog>Store your Thoughts permanentely and securely.</strog></p>

    <div id="error"><?php if ($error != "") {
    echo '<div class="alert alert-info" role="alert">'.$error.'</div>';}; ?></div>

    <form method="post" id="signUpForm">
        <p>Interested? Sign up now.</p>
        <div class="form-group">
            <input class="form-control" name="email" type="email" placeholder="Email adress">
        </div>
        <div class="form-group">
            <input class="form-control" name="password" type="password" placeholder="Password">
        </div>
        <div class="form-group form-check">
            <input class="form-check-input" name="stayLoggedIn" type="checkbox" value=1>
            <label class="form-check-label" >Stay logged in</label>
        </div>
        <div class="form-group">
            <input class="form-control" name="signup" type="hidden" value=1>           
            <input class="btn btn-success" name="submit" type="submit" value="Sign up!">
        </div>

        <p><a href=# class="toggleForms">Log in!</a></p>

    </form>

    <form method="post" id="logInForm">
        <p>Log in using your username and password.</p>
        <div class="form-group">
            <input class="form-control" name="email" type="email" placeholder="Email adress">
        </div>
        <div class="form-group">
            <input class="form-control" name="password" type="password" placeholder="Password">
        </div>
        <div class="form-group form-check">
            <input class="form-check-input" name="stayLoggedIn" type="checkbox" value=1>
            <label class="form-check-label" >Stay logged in</label>
        </div>                
        <div class="form-group">
            <input class="form-control" name="signup" type="hidden" value=0>
            <input class="btn btn-success" name="submit" type="submit" value="Log in!">
        </div>

        <p><a href=# class="toggleForms">Sign Up!</a></p>

    </form>

</div>

<?php include("footer.php"); ?>

    

