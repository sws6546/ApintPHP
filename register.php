<?php
    session_start();

    if(isset($_POST['submit'])){
        $username = htmlspecialchars($_POST['username'], ENT_QUOTES, 'UTF-8');
        $email = htmlspecialchars($_POST['email'], ENT_QUOTES, 'UTF-8');
        $password = htmlspecialchars($_POST['password'], ENT_QUOTES, 'UTF-8');

        $ok = true;
        require_once('vars.php');
//        checking username
        $con = mysqli_connect($dbHost, $dbUsername, $dbPassword, $db);

        $sqlQuery = sprintf("SELECT id_user FROM users WHERE username = '%s'", mysqli_real_escape_string($con, $username));
        $result = mysqli_query($con, $sqlQuery);
        if($username == ""){
            $ok = false;
            $_SESSION['e_username'] = "pole jest puste";
        }elseif(mysqli_num_rows($result) > 0){
            $ok = false;
            $_SESSION['e_username'] = "nazwa użytkownika już istnieje";
        }

//        checking e-mail
        if(!filter_var($email, FILTER_VALIDATE_EMAIL)){
            $ok = false;
            $_SESSION['e_email'] = "Nieprawidłowy adres email";
        }
        $sqlQuery = sprintf("SELECT id_user FROM users WHERE email = '%s'", mysqli_real_escape_string($con, $email));
        $result = mysqli_query($con, $sqlQuery);
        if(mysqli_num_rows($result) > 0){
            $ok = false;
            $_SESSION['e_email'] = "ten email jest już przydzielony do innego konta";
        }

//        checking password
        if(strlen($password) < 8 || strlen($password) > 50){
            $ok = false;
            $_SESSION['e_pwd'] = "Hasło powinno zawierać od 8 do 20 znaków";
        }

//        checking checkbox
        if(!isset($_POST['ryzyko'])){
            $ok = false;
            $_SESSION['e_checkbox'] = "Nie zaznaczono checkboxa";
        }

//        checking recaptcha
        $recaptchaResponse = file_get_contents("https://www.google.com/recaptcha/api/siteverify?secret=$recaptchaKey&response=".$_POST['g-recaptcha-response']);
        $recaptchaResponse = json_decode($recaptchaResponse, true);
        if(!$recaptchaResponse['success']){
            $ok = false;
            $_SESSION['e_captcha'] = "Nie można być robotem";
        }

        if($ok){
            $passwordHash = password_hash($password.$pepper, PASSWORD_DEFAULT);

            $username = mysqli_real_escape_string($con, $username);
            $email = mysqli_real_escape_string($con, $email);
            $passwordHash = mysqli_real_escape_string($con, $passwordHash);

            setcookie('username', $username, time() + (60*10));
            setcookie('email', $email, time() + (60*10));
            setcookie('password', $passwordHash, time() + (60*10));
            setcookie('verifyCode', rand(100000, 999999), time() + (60*10));

            $hmacUsername = hash_hmac("sha256", $username, $appSecret);
            $hmacEmail = hash_hmac("sha256", $email, $appSecret);
            $hmacPassword = hash_hmac("sha256", $passwordHash, $appSecret);

            setcookie('hmacUsername', $hmacUsername, time() + (60*10));
            setcookie('hmacEmail', $hmacEmail, time() + (60*10));
            setcookie('hmacPassword', $hmacPassword, time() + (60*10));

            header('Location: mailVerify.php');
        }

        mysqli_close($con);
    }
?>

<!doctype html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Rejestracja | Roksa 2</title>
    <link rel="stylesheet" href="register.css">
    <script src="https://www.google.com/recaptcha/api.js" async defer></script>
</head>
<body>
    <form action="register.php" method="post">
        <h1>Rejestracja</h1>
        <label for="username">username</label>
        <input type="text" name="username" id="username" class="tInput" placeholder="eg. exampleLogin123">
        <?php
            if(isset($_SESSION['e_username'])){
                echo "<p style='color: red;'>".$_SESSION['e_username']."</p>";
                unset($_SESSION['e_username']);
            }
        ?>
        <label for="email">email</label>
        <input type="email" name="email" id="email" class="tInput" placeholder="eg. mail@example.com">
        <?php
            if(isset($_SESSION['e_email'])){
                echo "<p style='color: red;'>".$_SESSION['e_email']."</p>";
                unset($_SESSION['e_email']);
            }
        ?>
        <label for="password">hasło</label>
        <input type="password" name="password" id="password" class="tInput" placeholder="eg. *******">
        <?php
            if(isset($_SESSION['e_pwd'])){
                echo "<p style='color: red;'>".$_SESSION['e_pwd']."</p>";
                unset($_SESSION['e_pwd']);
            }
        ?>
        <div style="color: orange;">
            <p>Strona roksa2 jest projektem szkolnym. </p>
            <input type="checkbox" name="ryzyko" id="ryzyko">
            <label for="ryzyko">Akceptuję ryzyko</label>
        </div>
        <?php
            if(isset($_SESSION['e_checkbox'])){
                echo "<p style='color: red;'>".$_SESSION['e_checkbox']."</p>";
                unset($_SESSION['e_checkbox']);
            }
        ?>
        <div class="g-recaptcha" data-sitekey="6LdzhrsjAAAAAHb_OpPtqF8sPlxtmdN-MfhjTtf7"></div>
        <?php
            if(isset($_SESSION['e_captcha'])){
                echo "<p style='color: red;'>".$_SESSION['e_captcha']."</p>";
                unset($_SESSION['e_captcha']);
            }
        ?>
        <br>
        <input type="submit" value="Zarejestruj" class="signupBtn" name="submit">
    </form>
</body>
</html>