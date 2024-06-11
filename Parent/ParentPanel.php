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
<h1>RODZIC</h1>
<body>
    <?php
        echo "<p>Witaj ".$_SESSION['user']. '! [ <a href="../Login/logout.php">Wyloguj się!</a> ]</p>';
        if($_SESSION['user'] == 'admin'){
            header('Location: ../Admin/adminPanel.php');
        }
    ?>
    <main>
        <section id="dzieci">
           <h3>Twoje dzieci:</h3>
            <table>
                <tr>
                    <th>
                        Identyfikator
                    </th>
                    <th>
                        Nazwa
                    </th>
                </tr>
                <?php
                require_once "../connect.php";

                $polaczenie = @new mysqli($host, $db_user, $db_password, $db_name);

                if ($polaczenie->connect_errno!=0)
                {
                    echo "Error: ".$polaczenie->connect_errno;
                }
                else {
                    $zapytanie = $polaczenie->query("SELECT id, username from users;");
                    while ($row = mysqli_fetch_array($zapytanie)) {
                        if ($row['username'] == $_SESSION['user']) {
                            $id = $row['id'];
                        }
                    }
                    $zapytanie = $polaczenie->query("SELECT id, username from student where parent = $id;");
                    while ($row = mysqli_fetch_array($zapytanie)) {
                        echo "<tr>" . "<td>" . $row[0] . "</td>" . "<td>" . $row[1] . "</td>" . "</tr>";
                    }
                }
                ?>
            </table>
        </section>
        <section id="slowka">
            <h3>Dodaj słówka:</h3>
            <form method="post" action="ParentPanel.php">
                <label for="db">Uczeń:</label>
                <select id="db" name="db">
                    <?php
                    $zapytanie = $polaczenie->query("SELECT id, username from student where parent = $id;");
                    while ($row = mysqli_fetch_array($zapytanie)){
                        echo "<option value='$row[0]'>" . $row[1] . "</option>";
                    }
                    ?>
                </select><br />
                <label for="word">Słówko:</label>
                <input type="text" id="word" name="word"><br />
                <label for="definition">Definicja:</label>
                <input type="text" id="definition" name="definition"><br />
                <input type="submit" value="Dodaj">
            </form>
            <?php
            @$student = $_POST["db"];
            @$word = $_POST["word"];
            @$definition = $_POST["definition"];
            @mysqli_query($polaczenie, "Insert into words(word, definition, student) values ('$word', '$definition', '$student');");
            $polaczenie->close();
            ?>
            <h3>Dodane słówka:</h3>
<!--            <form method="post" action="ParentPanel.php">-->
<!--                <label for="db">Uczeń:</label>-->
<!--                <select id="db" name="db">-->
<!--                    --><?php
//                    $zapytanie = $polaczenie->query("SELECT id, username from student where parent = $id;");
//                    while ($row = mysqli_fetch_array($zapytanie)){
//                        echo "<option value='$row[0]'>" . $row[1] . "</option>";
//                    }
//                    ?>
<!--                </select><br />-->
<!--            </form>-->
            <table>
                <tr>
                    <th>
                        Identyfikator
                    </th>
                    <th>
                        Słówko
                    </th>
                    <th>
                        Definicja
                    </th>
                </tr>
                <?php
                $zapytanie = $polaczenie->query("SELECT id, word, definition from words");
                while ($row = mysqli_fetch_array($zapytanie)){
                    echo "<tr>" . "<td>" . $row[0] . "</td>" . "<td>" . $row[1] . "</td>" . "<td>" . $row[2] . "</td>" . "</tr>";
                }
                ?>
            </table>
        </section>
    </main>
</body>
</html>