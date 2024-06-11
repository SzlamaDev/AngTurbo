<?php
	session_start();
	
	if (!isset($_SESSION['zalogowany']))
	{
		header('Location: index.html');
		exit();
	}
?>
<!DOCTYPE HTML>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">

    <title>AngTurbo</title>
</head>

<body>
    <?php
        echo "<p>Witaj ".$_SESSION['user']. '! [ <a href="logout.php">Wyloguj się!</a> ]</p>';
        if($_SESSION['user'] == 'admin'){
            header('Location: adminPanel.php');
        }
    ?>
</body>
</html>