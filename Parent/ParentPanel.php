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
                    $zapytanie = $polaczenie->query("SELECT id, username from student where parent_id = $id;");
                    while ($row = mysqli_fetch_array($zapytanie)) {
                        echo "<tr>" . "<td>" . $row[0] . "</td>" . "<td>" . $row[1] . "</td>" ."<td><button type='submit' value='".$row[0]."'>usuń</button>". "</tr>";
                    }
                }
                ?>
            </table>
        </section>
        <section id="slowka">
            <h3>Dodaj Kategorie:</h3>
                <form method="post" action="ParentPanel.php">
                    <label for="db1">Uczeń:</label>
                    <select id="db1" name="db1">
                        <?php
                        $zapytanie = $polaczenie->query("SELECT id, username from student where parent_id = $id;");
                        while ($row = mysqli_fetch_array($zapytanie)){
                            echo "<option value='$row[0]'>" . $row[1] . "</option>";
                        }
                        ?>
                    </select><br />
                    <label for="category_name">Nazwa:</label>
                    <input type="text" id="category_name" name="category_name"> <br>
                    <input type="submit" value="Dodaj">
                </form>
            <?php
                @$student = $_POST['db1'];
                @$category_name = $_POST['category_name'];
                if (!empty($word_pl) && !empty($word_en)) {
                    @mysqli_query($polaczenie, "INSERT INTO category(parent_id, student_id,name) values('$id','$student','$category_name');");
                }
            ?>
            <h3>Dodaj słówka:</h3>
            <form method="post" action="ParentPanel.php">
                <label for="db">Uczeń:</label>
                <select id="db" name="db">
                    <?php
                    $zapytanie = $polaczenie->query("SELECT id, username from student where parent_id = $id;");
                    while ($row = mysqli_fetch_array($zapytanie)){
                        echo "<option value='$row[0]'>" . $row[1] . "</option>";
                    }
                    ?>
                </select><br />
                <label for="category">Wybierz kategorie:</label>
                <select id="category" name="category">
                    <?php
                    $zapytanie = $polaczenie->query("SELECT id, name from category where parent_id = $id;");
                    while ($row = mysqli_fetch_array($zapytanie)){
                        echo "<option value='$row[0]'>" . $row[1] . "</option>";
                    }
                    ?>
                </select><br />
                <label for="word_en">Angieslkie słówko:</label>
                <input type="text" id="word_en" name="word_en"><br />
                <label for="word_pl">Polskie słówko</label>
                <input type="text" id="word_pl" name="word_pl"><br />
                <label for="definition">Definicja:</label>
                <input type="text" id="definition" name="definition"><br />
                <input type="submit" value="Dodaj">
            </form>
            <?php
            @$student = $_POST["db"];
            @$category = $_POST["category"];
            @$word_en = $_POST["word_en"];
            @$word_pl = $_POST["word_pl"];
            @$definition = $_POST["definition"];
            if (!empty($word_pl) && !empty($word_en)) {
                @mysqli_query($polaczenie, "Insert into words(category_id, parent_id, student_id, word_en, word_pl, description) values ('$category','$id','$student','$word_en','$word_pl' ,'$definition');");
            }
            ?>
            <h3>Dodane słówka:</h3>
            <form method="post" action="ParentPanel.php">
                <select id="category" name="category1">
                    <option value="all">Wszystkie</option>
                    <?php
                    $zapytanie = $polaczenie->query("SELECT id, name from category where parent_id = $id;");
                    while ($row = mysqli_fetch_array($zapytanie)){
                        echo "<option value='$row[0]'>" . $row[1] . "</option>";
                    }
                    ?>
                </select>
                <input type="submit" value="pokaż">
            </form>
            <table>
                <tr>
                    <th>
                        Kategoria
                    </th>
                    <th>
                        Angielski
                    </th>
                    <th>
                        Polski
                    </th>
                    <th>
                        Definicja
                    </th>

                </tr>
                <?php
                @$category = $_POST["category1"];
                if ($category == 'all') {
                    $zapytanie = $polaczenie->query("SELECT category.name, word_en,word_pl, description from words join category on category.id = words.category_id;");
                    while ($row = mysqli_fetch_array($zapytanie)){
                        echo "<tr>" . "<td>" . $row[0] . "</td>" . "<td>" . $row[1] . "</td>" ."<td>". $row[2] . "</td>" ."<td>".$row[3]."</td>" . "</tr>";
                    }
                }
                else{
                    $zapytanie = $polaczenie->query("SELECT category.name, word_en,word_pl, description from words join category on category.id = words.category_id where category.id = '$category';");
                    while ($row = mysqli_fetch_array($zapytanie)){
                        echo "<tr>" . "<td>" . $row[0] . "</td>" . "<td>" . $row[1] . "</td>" ."<td>". $row[2] . "</td>" ."<td>".$row[3]."</td>" . "</tr>";
                    }
                }

                ?>
            </table>
            <h3>Dodane kategorie:</h3>
            <table>
                <tr>
                    <th>Nazwa</th>
                </tr>
                <?php
                $zapytanie = $polaczenie->query("SELECT name from category where parent_id = $id;");
                while ($row = mysqli_fetch_array($zapytanie)){
                    echo "<tr>" . "<td>" . $row[0] . "</td>" . "</tr>";
                }
                ?>
            </table>
        </section>
    </main>
</body>
</html>
<?php
$polaczenie->close();
?>