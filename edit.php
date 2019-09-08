<!DOCTYPE html>
<html>
  <head>
    <meta charset="UTF-8">
    <title>Michael Zech</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" />
    <link rel="stylesheet" href="https://code.jquery.com/ui/1.12.1/themes/ui-lightness/jquery-ui.css">
    <link rel="stylesheet" href="./css/style.css" type="text/css">
  </head>
  <body>
  <div class="container">
<?php
session_start();

if (!isset($_SESSION['name'])) {
    die('ACCESS DENIED');
}

if (isset($_SESSION["error"])) {
    echo "<p style='color: red;' class='error'>" . $_SESSION['error'] . "</p>";
    unset($_SESSION['error']);
  }

if (isset($_GET["profile_id"])) {
    $_SESSION["id"] = $_GET["profile_id"];
}

require_once "connect.php";

$stmt = $pdo->prepare(
    'SELECT * 
    FROM Profile 
    WHERE Profile.profile_id = :id');
$stmt->execute(array(":id" => $_SESSION["id"]));
$profile = $stmt->fetch();

$stmt = $pdo->prepare(
    'SELECT * 
    FROM Position 
    WHERE Position.profile_id = :id');
$stmt->execute(array(":id" => $_SESSION["id"]));
$position = $stmt->fetchAll();

$stmt = $pdo->prepare(
    'SELECT name, year, Education.profile_id, Education.institution_id 
    FROM Institution 
    INNER JOIN Education 
        ON Institution.institution_id = Education.institution_id 
    WHERE Education.profile_id = :id');
$stmt->execute(array(":id" => $_SESSION["id"]));
$education = $stmt->fetchAll();

echo "<h1>Editing Profile for " . $_SESSION["name"] . "</h1>\n";

?>
<form method="post">
<div class="form-group">
    <label for="first_name">First Name: </label>
    <input type="text" name="first_name"
            id="first_name" class="form-control"
            value="<?php echo $profile["first_name"] ?>" />
</div>
<div class="form-group">
    <label for="last_name">Last Name: </label>
    <input type="text" name="last_name" class="form-control"
            id="last_name" value="<?php echo $profile["last_name"] ?>" />
</div>
<div class="form-group">
    <label for="email">Email: </label>
    <input type="text" name="email" id="email" class="form-control"
            value="<?php echo $profile["email"] ?>" />
</div>
<div class="form-group">
    <label for="headline">Headline: </label>
    <input type="text" name="headline" id="headline" class="form-control"
            value="<?php echo $profile["headline"] ?>" />
</div>
<div class="form-group">
    <label for="summary">Summary: </label>
    <input type="text" name="summary" id="summary" class="form-control"
            value="<?php echo $profile["summary"] ?>" />
</div>
<div class="form-group">
    <label for="summary">Education: </label>
    <button name="add-education" id="add-education" class="btn btn-primary">+</button>
</div>
<div id="education"></div>
<?php
    $i = 1;   
    foreach($education as $value) {
        echo "<div id='educationId" . $i . "'>
                <p>Year: <input type='text' name='eduYear" . $i . "' value='" . $value['year'] . "'>
                <input type='button' class='minusButton' value='-' id='minusButton" . $i . "' onclick='$(\"#educationId$value[institution_id]\").remove(); return false;'></p>
                <p>School: <input type='text' name='school" . $i . "' value='" . $value['name'] . "'></p>
             </div>";
        $i++;
    }
?>
<div class="form-group">
    <label for="summary">Position: </label>
    <button name="add-position" id="add-position" class="btn btn-primary">+</button>
</div>
<div id="positions"></div>

<?php
    $i = 1;
    foreach($position as $value) {
        echo "<div id='" . $value['position_id'] . "'>
                <p>Year: <input type='text' name='year$i' value='" . $value['year'] . "'>
                <input type='button' class='minusButton' value='-' id='minusButton" . $i . "' onclick='$(\"#$value[position_id]\").remove(); return false;'></p>
                <textarea name='desc$i' rows='8' cols='80'>" . $value['description'] . "</textarea>
             </div>";
        $i++;
    }
?>

<input type="submit" name="save" value="Save" class="btn btn-primary">
<input type="submit" name="cancel" value="Cancel" class="btn btn-warning">
</form>

<?php
if (isset($_POST["cancel"])) {
    header("location: index.php");
}

if (isset($_POST["save"])) {
    $firstName = htmlentities($_POST["first_name"]);
    $lastName = htmlentities($_POST["last_name"]);
    $email = htmlentities($_POST["email"]);
    $headline = htmlentities($_POST["headline"]);
    $summary = htmlentities($_POST["summary"]);

    require_once "utils.php";

    for ($i = 1; i <= 9; $i++) {
        if (!is_numeric($_POST["eduYear" + $i])) {
            $_SESSION["error"] = "Year must be numeric";
            header("Location: edit.php");
            return false;
        }
    }

    $validatePosResult = validatePos();

    if (strlen($validatePosResult) > 0) {
        $_SESSION["error"] = $validatePosResult;
        header("Location: edit.php");
        return false;
    }

    if (strpos($email, "@") === false) {
        $_SESSION['error'] = "Email address must contain @";
        header("Location: edit.php");
        return;
    } else {
        $sql 
        = "UPDATE Profile SET 
            first_name=:fn, 
            last_name=:ld, 
            email=:em, 
            headline=:hl, 
            summary=:su 
          WHERE profile_id='" . $_SESSION["id"] . "'";
    
    $stmt 
        = $pdo->prepare($sql);
    $stmt->execute(array(
        ':fn' => $firstName,
        ':ld' => $lastName,
        ':em' => $email,
        ':hl' => $headline,
        ':su' => $summary)
    );
    // ----------------------------------------------------------
    $stmt = $pdo->prepare('DELETE FROM Position
        WHERE profile_id=:pid');
    $stmt->execute(array( ':pid' => $_SESSION["id"]));

    // Insert the position entries
    $rank = 1;
    for($i = 1; $i <= 9; $i++) {
        if ( ! isset($_POST['year' . $i]) ) continue;
        if ( ! isset($_POST['desc' . $i]) ) continue;

        $year = $_POST['year' . $i];
        $desc = $_POST['desc' . $i];

        $stmt = $pdo->prepare('INSERT INTO Position
            (profile_id, rank, year, description)
        VALUES ( :pid, :rank, :year, :desc)');
        $stmt->execute(array(
            ':pid' => $_SESSION['id'],
            ':rank' => $rank,
            ':year' => $year,
            ':desc' => $desc)
        );
        $rank++;
    }

    $stmt = $pdo->prepare('DELETE FROM Education
        WHERE profile_id=:pid');
    $stmt->execute(array( ':pid' => $_SESSION["id"]));

    $rank = 1;

    var_dump($_POST);

    for($i = 0; $i < 9; $i++) {
        if ( ! isset($_POST['eduYear' . $i]) ) continue;
        if ( ! isset($_POST['school' . $i]) ) continue;

        $year = htmlentities($_POST['eduYear' . $i]);
        $school = htmlentities($_POST["school" . $i]);

        $sql = "SELECT institution_id FROM Institution where name like '" . $school . "'";
        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(':na', "%{$school}%", PDO::PARAM_STR);
        $stmt->execute();

        $iid = "";

        foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $row) {
            $iid = $row["institution_id"];

            if (count($iid) > 0) {
            break;
            }
        }

        if (strlen($iid) == 0) {
            $sql = "INSERT INTO Institution(name) VALUES(:na)";
            $stmt = $pdo->prepare($sql);
            $stmt->execute(array(":na" => $school));

            $iid = $pdo->lastInsertId();
        } 
       
        $stmt = $pdo->prepare('INSERT INTO Education
            (institution_id, profile_id, rank, year)
        VALUES (:iid, :pid, :rank, :year)');
        $stmt->execute(array(
            ':iid' => $iid,
            ':pid' => $_SESSION['id'],
            ':rank' => $rank,
            ':year' => $year)
        );
        $rank++;
    }
    // ---------------------------------------------------------------
        $_SESSION['edited'] = true;

        $tmp_id = $_SESSION["id"];
        unset($_SESSION["id"]);

        // header("Location: index.php");
    }
}
?>
        </div>
        <script src="https://code.jquery.com/jquery-3.2.1.js" integrity="sha256-DZAnKJ/6XZ9si04Hgrsxu/8s717jcIzLy3oi35EouyE=" crossorigin="anonymous"></script>
        <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js" integrity="sha256-T0Vest3yCU7pafRw9r+settMBX6JkKN06dqBnpQ8d30=" crossorigin="anonymous"></script>
        <script src="js/edit.js"></script>
  </body>
</html>