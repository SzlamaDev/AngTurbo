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
        echo "<p>Witaj ".$_SESSION['user']. '! [ <a href="../Login/logout.php">Wyloguj się!</a> ]</p>';
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
        <th>
            Rodzic
        </th>
        </tr>
        <?php
            require_once "../connect.php";

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
                $zapytanie = $polaczenie->query("SELECT id, username, parent_id from student;");
                while ($row = mysqli_fetch_array($zapytanie)){
                    echo "<tr>" . "<td>" . $row[0] . "</td>" . "<td>" . $row[1] . "</td>" . "<td>" . $row[2] ."</td>" . "</tr>";
                }
            }
        ?>
    </table>
    <h3>Dodaj słówko:</h3>
    <form method="post" action="AdminPanel.php">
        <label for="student">Uczeń:</label>
        <select id="student" name="student">
            <?php
            $zapytanie = $polaczenie->query("SELECT id, username from student;");
            while ($row = mysqli_fetch_array($zapytanie)){
                echo "<option value='$row[0]'>" . $row[1]; "</option>";
            }
            ?>
        </select><br />
        <label for="category">Kategoria:</label>
        <select id="category" name="category">
            <?php
            $zapytanie = $polaczenie->query("SELECT id, name from category;");
            while ($row = mysqli_fetch_array($zapytanie)){
                echo "<option value='$row[0]'>" . $row[1] . "</option>";
            }
            ?>
        </select><br />
        <label for="word">Słówko po Polsku:</label>
        <input type="text" id="word_pl" name="word_pl"><br />
        <label for="word">Słówko po Angielsku:</label>
        <input type="text" id="word_en" name="word_en"><br />
        <label for="definition">Definicja:</label>
        <input type="text" id="definition" name="definition"><br />
        <input type="submit" value="Dodaj">
    </form>
        <?php
            @$student = $_POST["student"];
            $zapytanie = $polaczenie->query("SELECT parent_id from student where id = '$student';");
            while ($row = mysqli_fetch_array($zapytanie)){
                $parent_id = $row[0];
            }
            @$category = $_POST["category"];
            @$word_pl = $_POST["word_pl"];
            @$word_en = $_POST["word_en"];
            @$definition = $_POST["definition"];
        if (!empty($word_pl) && !empty($word_en)){
            @mysqli_query($polaczenie, "Insert into words(category_id, parent_id, student_id, word_en, word_pl, description) values ('$category', '$parent_id', '$student', '$word_en', '$word_pl', '$definition');");
        }
        else{
            echo "Pola nie moga byc puste!";
        }
        ?>
        <h3>Lista słówek / usuń słówko:</h3>
        <form method="post" action="AdminPanel.php">
            <select id="showcategory" name="showcategory">
                <?php
                $zapytanie = $polaczenie->query("SELECT id, name from category;");
                while ($row = mysqli_fetch_array($zapytanie)){
                    echo "<option value='$row[0]'>" . $row[1] . "</option>";
                }
                ?>
            </select><br />
            <input type="submit" value="Wyświetl">
        </form>
    <table>
        <tr>
            <th>
                ID
            </th>
            <th>
                ID Kategorii
            </th>
            <th>
                ID Rodzica
            </th>
            <th>
                ID Ucznia
            </th>
            <th>
                Słówko po Angielsku
            </th>
            <th>
                Słówko po Polsku
            </th>
            <th>
                Definicja
            </th>
        </tr>
    </table>
        <?php
        @$category = $_POST["showcategory"];
        $zapytanie = $polaczenie->query("SELECT id, category_id, parent_id, student_id, word_en, word_pl, description FROM words where category_id = '$category';");
        while ($row = mysqli_fetch_array($zapytanie)){
            echo "<tr>" . "<td>" . $row[0] . "</td>" . "<td>" . $row[1] . "</td>" . "<td>" .  $row[2] . "</td>" . "<td>" . $row[3] . "</td>" . "<td>" . $row[4] . "</td>" . "<td>" . $row[5] . "</td>" . "<td>" . $row[6] . "</td>" . "</tr>";
        }
        ?>
    <form action="AdminPanel.php" METHOD="post">
        <label for="wordnumber">Identyfikator słowka do usunięcia: </label>
        <input type="number" name="wordnumber" id="wordnumber">
        <input type="submit" value="usuń">
    </form>
    <?php
    @$wordnumber = $_POST["wordnumber"];
    mysqli_query($polaczenie, "Delete from words where id = '$wordnumber';");
    ?>
    <h3>Dodaj / Usuń kategorię: </h3>
    <form method="post" action="AdminPanel.php">
        <label for="categorystudent">Uczeń:</label>
        <select id="categorystudent" name="categorystudent">
            <?php
            $zapytanie = $polaczenie->query("SELECT id, username from student;");
            while ($row = mysqli_fetch_array($zapytanie)){
                echo "<option value='$row[0]'>" . $row[1]; "</option>";
            }
            ?>
        </select><br />
        <label for="categoryname">Nazwa kategorii:</label>
        <input type="text" name="categoryname" id="categoryname">
        <input type="submit" value="Dodaj">
    </form>
    <?php
    @$categorystudent = $_POST["categorystudent"];
    @$categoryname = $_POST["categoryname"];
    $zapytanie = $polaczenie->query("SELECT parent_id from student where id = '$categorystudent';");
    while ($row = mysqli_fetch_array($zapytanie)){
        @$parent_id = $row[0];
    }
    if (!empty($categoryname)) {
        mysqli_query($polaczenie, "Insert into category(parent_id, student_id, name) values ('$parent_id', '$categorystudent', '$categoryname');");
    }
    else{
        echo "Pola nie moga byc puste!";
    }
    ?>
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
        $zapytanie = $polaczenie->query("SELECT id, name from category;");
        while ($row = mysqli_fetch_array($zapytanie)){
            echo "<tr>" . "<td>" . $row[0] . "</td>" . "<td>" . $row[1] . "</td>" . "</tr>";
        }
        ?>
    </table>
    <form action="AdminPanel.php" METHOD="post">
        <label for="categorynumber">Identyfikator Kategorii do usunięcia: </label>
        <input type="number" name="categorynumber" id="categorynumber">
        <input type="submit" value="usuń">
    </form>
    <?php
    @$categorynumber = $_POST["categorynumber"];
    mysqli_query($polaczenie, "Delete from category where id = '$categorynumber';");
    ?>
<h3>Dodaj / Usuń Ucznia: </h3>
    <form method="post" action="AdminPanel.php">
        <label for="addingstudent">Rodzic:</label>
        <select id="addingstudent" name="addingstudent">
            <?php
            $zapytanie = $polaczenie->query("SELECT id, username from users");
            while ($row = mysqli_fetch_array($zapytanie)){
                echo "<option value='$row[0]'>" . $row[1]; "</option>";
            }
            ?>
        </select><br />
        <label for="studentlogin">Login:</label>
        <input type="text" id="studentlogin" name="studentlogin"><br />
        <label for="studentpasswd">Hasło:</label>
        <input type="password" id="studentpasswd" name="studentpasswd">
        <input type="submit" value="Dodaj">
    </form>
    <?php
    @$addingstudent = $_POST["addingstudent"];
    @$studentlogin = $_POST["studentlogin"];
    @$studentpasswd = $_POST["studentpasswd"];
    if (!empty($studentlogin) && !empty($studentpasswd)){
        mysqli_query($polaczenie, "Insert into student(parent_id, username, passwd) values ('$addingstudent', '$studentlogin', '$studentpasswd')");
    }
    else{
        echo "Pola nie moga byc puste!";
    }
    ?>
<table>
    <tr>
        <th>
            ID
        </th>
        <th>
            ID rodzica
        </th>
        <th>
            Nazwa
        </th>
    </tr>
    <?php
    $zapytanie = $polaczenie->query("SELECT id, parent_id, username from student;");
    while ($row = mysqli_fetch_array($zapytanie)){
        echo "<tr>" . "<td>" . $row[0] . "</td>" . "<td>" . $row[1] . "</td>" . "<td>" . $row[2] . "</td>" . "</tr>";
    }
    ?>
</table>
<form action="AdminPanel.php" METHOD="post">
    <label for="studentnumber">Identyfikator ucznia do usunięcia: </label>
    <input type="number" name="studentnumber" id="studentnumber">
    <input type="submit" value="usuń">
</form>
    <?php
    @$studentnumber = $_POST["studentnumber"];
    mysqli_query($polaczenie, "Delete from student where id = '$studentnumber';");
    ?>
    <h3>Dodaj / Usuń Rodzica: </h3>
    <form method="post" action="AdminPanel.php">
        <label for="parentlogin">Login:</label>
        <input type="text" id="parentlogin" name="parentlogin"><br />
        <label for="parentpasswd">Hasło:</label>
        <input type="password" id="parentpasswd" name="parentpasswd">
        <input type="submit" value="Dodaj">
    </form>
    <?php
    @$parentlogin = $_POST["parentlogin"];
    @$parentpasswd = $_POST["parentpasswd"];
    if (!empty($studentlogin) && !empty($studentpasswd)){
        mysqli_query($polaczenie, "insert into users(username, passwd) values ('$parentlogin', '$parentpasswd')");
    }
    else{
        echo "Pola nie moga byc puste!";
    }
    ?>
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
        $zapytanie = $polaczenie->query("SELECT id, username from users;");
        while ($row = mysqli_fetch_array($zapytanie)){
            echo "<tr>" . "<td>" . $row[0] . "</td>" . "<td>" . $row[1] . "</td>" . "</tr>";
        }
        ?>
    </table>
    <form action="AdminPanel.php" METHOD="post">
        <label for="parentnumber">Identyfikator rodzica do usunięcia: </label>
        <input type="number" name="parentnumber" id="parentnumber">
        <input type="submit" value="usuń">
    </form>
    <?php
    @$parentnumber = $_POST["parentnumber"];
    mysqli_query($polaczenie, "Delete from users where id = '$parentnumber';");
    ?>
    </body>
</html>
