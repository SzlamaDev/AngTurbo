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
    <script>
        function confirmDeleteStudent(){
            return confirm("Czy napewno chcesz to usunąć");
        }
    </script>
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
            <form action="ParentPanel.php" method="post"  onsubmit="return confirmDeleteStudent()">
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
                            echo "<tr>" . "<td>" . $row[0] . "</td>" . "<td>" . $row[1] . "</td>" ."<td><button type='submit' name='delete_student' value='".$row[0]."'>usuń</button></td>". "</tr>";
                        }
                    }
                    ?>
                </table>
            </form>
            <?php
            @$delete_student = $_POST["delete_student"];
            @mysqli_query($polaczenie, "DELETE FROM student WHERE id = '$delete_student'");
            ?>
        </section>
        <section id="slowka">
            <h3>Dodane słówka:</h3>
            <form method="post" action="ParentPanel.php">
                <select id="category" name="category1" onchange="this.form.submit();">
                    <option>Wybierz kategorie</option>
                    <option value="all">Wszystkie</option>
                    <?php
                    $zapytanie = $polaczenie->query("SELECT id, name from category where parent_id = $id;");
                    while ($row = mysqli_fetch_array($zapytanie)){
                        echo "<option value='$row[0]'>" . $row[1] . "</option>";
                    }
                    ?>
                </select>
            </form>
            <form action="ParentPanel.php" method="post" onsubmit="return confirmDeleteStudent()">
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
                        $zapytanie = $polaczenie->query("SELECT category.name, word_en,word_pl, description, words.id from words join category on category.id = words.category_id;");
                        while ($row = mysqli_fetch_array($zapytanie)){
                            echo "<tr>" . "<td>" . $row[0] . "</td>" . "<td>" . $row[1] . "</td>" ."<td>". $row[2] . "</td>" ."<td>".$row[3]."</td><td><button type='submit' name='delete_word' value='".$row[4]."'>usuń</button></td>" . "</tr>";
                        }
                    }
                    else{
                        $zapytanie = $polaczenie->query("SELECT category.name, word_en,word_pl, description, words.id from words join category on category.id = words.category_id where category.id = '$category';");
                        while ($row = mysqli_fetch_array($zapytanie)){
                            echo "<tr>" . "<td>" . $row[0] . "</td>" . "<td>" . $row[1] . "</td>" ."<td>". $row[2] . "</td>" ."<td>".$row[3]."</td><td><button type='submit' name='delete_word' value='".$row[4]."'>usuń</button></td>" . "</tr>";
                        }
                    }

                    ?>
                </table>
            </form>
            <?php
            @$delete_word = $_POST["delete_word"];
            @mysqli_query($polaczenie, "DELETE FROM words WHERE id = '$delete_word'");
            ?>
            <h3>Dodane kategorie:</h3>
            <form action="ParentPanel.php" method="post" onsubmit="return confirmDeleteStudent()">
                <table>
                    <tr>
                        <th>Nazwa</th>
                    </tr>
                    <?php
                    $zapytanie = $polaczenie->query("SELECT name,id from category where parent_id = $id;");
                    while ($row = mysqli_fetch_array($zapytanie)){
                        echo "<tr>" . "<td>" . $row[0] . "</td><td><button type='submit' name='delete_category' value='".$row[1]."'>usuń</button></td>" . "</tr>";
                    }
                    ?>
                </table>
            </form>
            <?php
            @$delete_category = $_POST["delete_category"];
            @mysqli_query($polaczenie, "DELETE FROM category WHERE id = '$delete_category'");
            @mysqli_query($polaczenie, "DELETE FROM words WHERE category_id = '$delete_category'");
            ?>
        </section>
        <section id="dodawanie">
            <h3>Dodaj dziecko:</h3>
            <form action="ParentPanel.php" method="post">
                <label for="login">Login:</label>
                <input type="text" name="login" id="login"><br>
                <label for="password">Hasło:</label>
                <input type="password" name="password" id="password"><br>
                <input type="submit" value="Dodaj">
            </form>
            <?php
            @$login = $_POST['login'];
            @$password = $_POST['password'];
            if (!empty($login) && !empty($password)) {
                @mysqli_query($polaczenie, "INSERT INTO student (parent_id,username, passwd) VALUES ('$id','$login', '$password');");
            }
            ?>
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
            if (!empty($category_name)) {
                @mysqli_query($polaczenie, "INSERT INTO category(parent_id, student_id,name) values('$id','$student','$category_name');");
            }
            ?>
            <h3>Dodaj słówka:</h3>
            <form method="post" action="ParentPanel.php" >
                <label for="db">Uczeń:</label>
                <select id="db" name="db" onchange="this.form.submit()">
                    <option value="0">wybierz ucznia</option>
                    <?php
                    $zapytanie = $polaczenie->query("SELECT id, username from student where parent_id = $id;");
                    while ($row = mysqli_fetch_array($zapytanie)){
                        echo "<option value='$row[0]'>" . $row[1] . "</option>";
                    }
                    ?>
                </select><br />
            </form>
            <form method="post" action="ParentPanel.php">
                <?php
                @$student_id = $_POST["db"];
                echo '<select style="display: none" name="student_id">';
                echo "<option value='$student_id'></option>'>";
                echo '</select>';
                if (!empty($student_id)) {
                    echo '<label for="category">Wybierz kategorie:</label>';
                    echo '<select id="category" name="category">';

                        $zapytanie = $polaczenie->query("SELECT id, name from category where parent_id = $id and student_id = $student_id;");
                        while ($row = mysqli_fetch_array($zapytanie)) {
                            echo "<option value='$row[0]'>" . $row[1] . "</option>";
                        }
                    echo '</select><br />';
                    echo '<label for="word_en">Angieslkie słówko:</label>';
                    echo '<input type="text" id="word_en" name="word_en"><br />';
                    echo '<label for="word_pl">Polskie słówko</label>';
                    echo '<input type="text" id="word_pl" name="word_pl"><br />';
                    echo '<label for="definition">Definicja:</label>';
                    echo '<input type="text" id="definition" name="definition"><br />';
                    echo '<input type="submit" value="Dodaj">';
                }
                ?>
            </form>
            <?php
            @$student_id = $_POST["student_id"];
            @$category = $_POST['category'];
            @$word_en = $_POST['word_en'];
            @$word_pl = $_POST['word_pl'];
            @$definition = $_POST['definition'];
            $query = "Insert into words(category_id, parent_id, student_id, word_en, word_pl, description) values ('$category','$id','$student_id','$word_en','$word_pl' ,'$definition');";
            if (!empty($word_pl) && !empty($word_en)) {
                @mysqli_query($polaczenie, $query);
            }
            ?>
        </section>
    </main>
</body>
</html>
<?php
$polaczenie->close();
?>