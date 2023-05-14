<?php
    session_start();
    if(!isset($_SESSION['username'])){
        header("Location: index.php");
        exit();
    }

    if(isset($_POST['submit'])){
        $ok = true;

//        check about
        if($_POST['about'] == ""){
            $ok = false;
            $_SESSION['e_about'] = 'pole nie może być puste';
        }

//        check phone Number
        $pattern = "/^\d{3}[ -]?\d{3}[ -]?\d{3}$/";
        if($_POST['phone'] == ""){
            $ok = false;
            $_SESSION['e_phone'] = 'pole nie może być puste';
        }elseif(!preg_match($pattern, $_POST['phone'])){
            $ok = false;
            $_SESSION['e_phone'] = 'Niepoprawnie wpisany numer telefonu';
        }

//        check picture
         if($_FILES['picture']['name'] == ""){
             $ok = false;
             $_SESSION['e_picture'] = 'Proszę wybrać zdjęcie';
         }elseif($_FILES['picture']["type"] != "image/jpeg" && $_FILES['picture']["type"] != "image/png"){
             $ok = false;
             $_SESSION['e_picture'] = 'Plik musi być w formacie jpg lub png';
         }elseif($_FILES['picture']['size'] > 104857600){
             $ok = false;
             $_SESSION['e_picture'] = 'Plik nie może przekraczać 100MB';
         }

//         check cost
        if(!is_numeric($_POST['cost'])){
            $ok = false;
            $_SESSION['e_cost'] = 'Proszę wpisać samą liczbę';
        }

//        check time of previous
        {
            require_once('vars.php');
            $con = mysqli_connect($dbHost, $dbUsername, $dbPassword, $db);
            $res = $con->query("SELECT * FROM offers ORDER BY date DESC LIMIT 4");
            $arrayRes = $res->fetch_assoc();
            if(isset($arrayRes['date'])){
                if(time() - $arrayRes['date'] < 600){
                    $ok = false;
                    $_SESSION['e_time'] = "Poczekaj 10 minut przed napisaniem kolejnej oferty";
                }
            }
            $con->close();
        }

         if($ok){
             $about = htmlspecialchars($_POST['about'], ENT_QUOTES, 'UTF-8');
             $phone = htmlspecialchars($_POST['phone'], ENT_QUOTES, 'UTF-8');
             $cost = htmlspecialchars($_POST['cost'], ENT_QUOTES, 'UTF-8');
             $userId = $_SESSION['userId'];

             $picture = $_FILES['picture'];
             $pictureName = md5(mt_rand(1, 999999999999));
             if(move_uploaded_file($picture['tmp_name'], "./pictures/".$pictureName.".jpg")){
                 $pictureName = $pictureName.".jpg";

                 require_once('vars.php');
                 $con = mysqli_connect($dbHost, $dbUsername, $dbPassword, $db);
                 mysqli_query($con, "INSERT INTO offers VALUES (NULL, '$userId', '$about', '$phone', '$cost', '$pictureName', '".time()."')");
                 mysqli_close($con);

                 header("Location: main.php");
                 exit();
             }else{
                 echo "Coś poszło nie tak";
                 exit();
             }
         }
    }
?>

<!doctype html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Dodaj ofertę | Roksa 2</title>
    <link rel="stylesheet" href="register.css">
</head>
<body>
    <form action="addOffert.php" method="post" enctype="multipart/form-data">
        <h1>Dodawanie oferty na spotkanie</h1>
        <label for="about">Napisz coś od siebie</label>
        <textarea class="tInput" name="about" id="about" cols="30" rows="5"></textarea>
        <?php
            if(isset($_SESSION['e_about'])){
                echo "<p style='color: red;'>".$_SESSION['e_about']."</p>";
                unset($_SESSION['e_about']);
            }
        ?>
        <label for="phone">Numer telefonu</label>
        <div style="
            display: flex;
            flex-direction: row;
            align-items: center;
            gap: 10px;
        ">
            <h2>+48</h2><input type="tel" class="tInput" name="phone" id="phone" placeholder="eg. 000 000 000" style="width: 100%;">
        </div>
        <?php
            if(isset($_SESSION['e_phone'])){
                echo "<p style='color: red;'>".$_SESSION['e_phone']."</p>";
                unset($_SESSION['e_phone']);
            }
        ?>
        <label for="cost">Cena [zł]</label>
        <input type="number" class="tInput" name="cost" id="cost">
        <?php
            if(isset($_SESSION['e_cost'])){
                echo "<p style='color: red;'>".$_SESSION['e_cost']."</p>";
                unset($_SESSION['e_cost']);
            }
        ?>
        <label for="picture">Zdjęcie</label>
        <input type="file" name="picture" id="picture">
        <?php
            if(isset($_SESSION['e_picture'])){
                echo "<p style='color: red;'>".$_SESSION['e_picture']."</p>";
                unset($_SESSION['e_picture']);
            }
        ?>
        <input type="submit" value="Dodaj post" name="submit">
        <?php
            if(isset($_SESSION['e_time'])){
                echo "<p style='color: red;'>".$_SESSION['e_time']."</p>";
                unset($_SESSION['e_time']);
            }
        ?>
    </form>
    <script src="addOffert.js"></script>
</body>
</html>
