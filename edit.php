<!DOCTYPE html>
<html>
  <head>
    <meta charset="UTF-8">
    <title>Michael Zech</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" />
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

$stmt = $pdo->prepare('SELECT * FROM Profile WHERE profile_id = :id');
$stmt->execute(array(":id" => $_SESSION["id"]));
$profile = $stmt->fetch();

echo "<h1>Editing Profile for UMSI</h1>\n";

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

    $_SESSION['edited'] = true;
    unset($_SESSION["id"]);

    header("Location: index.php");
    return;
    }
}

?>