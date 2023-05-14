<?php
    session_start();
    if(!isset($_SESSION['username'])){
        header("Location: index.php");
        exit();
    }
    if(!isset($_SESSION['limit'])){
        $_SESSION['limit'] = 0;
    }else{
        $_SESSION['limit'] += 10;
    }
?>

<!doctype html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Roksa 2 | Strona główna</title>
    <link rel="stylesheet" href="main.css">
</head>
<body>
    <header>
        <p>Witaj użytkowniku <?php echo $_SESSION['username']; ?></p>
        <a href="logout.php" style="color: black">Wyloguj się <<<</a>
    </header>
    <section id="first">
        <h1>Strona Główna</h1>
        <a href="addOffert.php" id="addPost"><p>Dodaj ofertę</p></a>
    </section>
    <section id="offerts">
        <?php
            require_once('vars.php');
            $con = mysqli_connect($dbHost, $dbUsername, $dbPassword, $db);
            $result = mysqli_query($con, "SELECT users.username, offers.aboutOffer, offers.phoneNumber, offers.cost, offers.pictureName FROM offers LEFT JOIN users on offers.id_user = users.id_user;");

            while($arrayRes = $result->fetch_assoc()){
                echo '<div class="offert">
                            <img src="pictures/'.$arrayRes["pictureName"].'" alt="Super zdjęcie">
                            <div class="postData">
                                <p>user: '.$arrayRes["username"].'</p>
                                <hr>
                                <h1>'.$arrayRes["aboutOffer"].'</h1>
                                <h4>Nrumer telefonu: '.$arrayRes["phoneNumber"].'</h4>
                                <h3>Koszt: '.$arrayRes["cost"].'</h3>
                            </div>
                        </div>';

            }
            $con->close();
        ?>
    </section>
</body>
</html>
