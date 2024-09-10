<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Site</title>
    <link rel="stylesheet" href="./styles.css">
</head>
<body>
<?php
$fileJson = './dados.json';

$nameErr = $emailErr = $genderErr = "";
$name = $email = $gender = $comment = "";

// função principal que verifica o envio do formulário
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // valida os dados
    if (empty($_POST["name"])) {
        $nameErr = "Name is required";
    } else {
        $name = test_input($_POST["name"]);

        // preg_match -> perform a regular expression match
        // regex to check if name is valid
        if (!preg_match("/^[a-zA-Z ]*$/",$name)) {
            $nameErr = "Only letters and white space allowed";
        }
    }
    if (empty($_POST["email"])) {
        $emailErr = "Email is required";
    } else {
        $email = test_input($_POST["email"]);
        // filter to check if email is valid
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $emailErr = "Invalid email format";
        }
    }
    if (empty($_POST["gender"])) {
        $genderErr = "Gender is required";
    } else {
        $gender = test_input($_POST["gender"]);
    }
    if (empty($_POST["comment"])) {
        $comment = "";
    } else {
        $comment = test_input($_POST["comment"]);
    }

    if(empty($nameErr) && empty($emailErr) && empty($genderErr)) {
        // cria array com o formulario
        $formData = [
            'name' => $name,
            'email' => $email,
            'gender' => $gender,
            'comment' => $comment
        ];

        if (file_exists($fileJson)) {
            // verifica se o arquivo json existe, e adiciona os dados ao
            // array existente
            $jsonData = file_get_contents($fileJson);
            $dataArray = json_decode($jsonData, true);

            $dataArray[] = $formData;
        } else {
            // cria um novo array com os dados
            $dataArray = [$formData];
        }

        $jsonData = json_encode($dataArray, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        file_put_contents($fileJson, $jsonData);

        echo 'Success saving data to JSON file!';
    }
}

function test_input($data): string {
    $data = trim($data);
    $data = stripslashes($data);
    return htmlspecialchars($data);
}
?>
<h2>PHP Form Validation</h2>
<p><span class="error">* required fields</span></p>
<form method="POST" action="<?= htmlspecialchars($_SERVER["PHP_SELF"])?>">
    Name: <input type="text" name="name" value="<?= $name ?>">
    <span class="error">* <?= $nameErr ?></span>
    <br><br>
    Email: <input type="text" name="email" value="<?= $email ?>">
    <span class="error">* <?= $emailErr ?></span>
    <br><br>
    Comment: <textarea name="comment" rows="5" cols="40"><?= $comment ?></textarea>
    <br><br>

    Gender:
    <input type="radio" name="gender"
           <?php if (isset($gender) && $gender == "female") echo "checked";?>
           value="female">Female
    <input type="radio" name="gender"
           <?php if (isset($gender) && $gender == "male") echo "checked";?>
           value="male">Male
    <input type="radio" name="gender"
           <?php if (isset($gender) && $gender == "other") echo "checked";?>
           value="other">Other
    <span class="error">* <?= $genderErr ?></span>
    <br><br>
    <input type="submit" name="submit" value="Submit">

</form>
    <?php
    echo <<<TEXT
        <h2>Your input:</h2>
        <p>$name</p>
        <p>$email</p>
        <p>$comment</p>
        <p>$gender</p>
    TEXT;
    ?>
</body>
</html>