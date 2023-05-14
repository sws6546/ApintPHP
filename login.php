<?php
    session_start();

    if(isset($_POST['login'])){
        $username = htmlspecialchars($_POST['login'], ENT_QUOTES, 'UTF-8');
        $password = htmlspecialchars($_POST['password'], ENT_QUOTES, 'UTF-8');

        if($username == "" || $password == ''){
            $_SESSION['loginErr'] = "Nie wpisano loginu lub hasła";
            header('Location: index.php');
            exit();
        }else{
            require_once('vars.php');
            $con = mysqli_connect($dbHost, $dbUsername, $dbPassword, $db);
            $sqlQuery = sprintf("SELECT * FROM users WHERE username = '%s'", mysqli_real_escape_string($con, $username));
            $result = $con->query($sqlQuery);
            if($result->num_rows < 1){
                $_SESSION['loginErr'] = "Nie ma takiego użytkownika";
                header('Location: index.php');
                mysqli_close($con);
                exit();
            }else{
                $userData = $result->fetch_assoc();
                if(password_verify($password.$pepper, $userData['password'])){
                    $_SESSION['username'] = $userData['username'];
                    $_SESSION['userId'] = $userData['id_user'];
                    header('Location: main.php');
                    mysqli_close($con);
                    exit();
                }else{
                    $_SESSION['loginErr'] = "Złe hasło";
                    header('Location: index.php');
                    mysqli_close($con);
                    exit();
                }
            }
        }
    }else{
        header('Location: index.php');
        exit();
    }