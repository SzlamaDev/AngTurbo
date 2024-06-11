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

        <title>AngTurbo - Admin</title>
</head>

<body>
    <h1>AdminPanel</h1>
    <?php
        echo "<p>Witaj ".$_SESSION['user']. '! [ <a href="logout.php">Wyloguj się!</a> ]</p>';
    ?>
    <h3>Lista użytkowników:</h3>
    <table>
        <tr>
        <th>
            ID
        </th>
        <th>
            Nazwa
        </th>
        </tr>
        <?php
            require_once "connect.php";

            $polaczenie = @new mysqli($host, $db_user, $db_password, $db_name);

            if ($polaczenie->connect_errno!=0)
            {
                echo "Error: ".$polaczenie->connect_errno;
            }
            else
            {
                $zapytanie = $polaczenie->query("SELECT id, username from users;");
                while ($row = mysqli_fetch_array($zapytanie)){
                    echo "<tr>" . "<td>" . $row[0] . "</td>" . "<td>" . $row[1] . "</td>" . "</tr>";
                }
            }
        ?>
    </table>
    <h3>Dodaj słówko:</h3>
    <form method="post" action="AdminPanel.php">
        <label for="db">Uczeń:</label>
        <select id="db" name="db">
<!--            <option value="1">test1</option>-->
<!--            <option value="2">test2</option>-->
<!--            <option value="3">test3</option>-->
            <?php
            $zapytanie1 = $polaczenie->query("show tables;");
            while ($row = mysqli_fetch_array($zapytanie1)){
                echo "<option value='1'>" . $row[0] . "</option>";
            };
            ?>
        </select><br />
        <label for="word">Słówko:</label>
        <input type="text" id="word" name="word"><br />
        <input type="submit" value="Dodaj">
    </form>
        <?php

        ?>
    </body>
</html>
