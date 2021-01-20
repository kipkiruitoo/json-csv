<?php

require 'vendor/autoload.php';

use OzdemirBurak\JsonCsv\File\Json;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $errors = array();
    $file_name = $_FILES['file']['name'];
    $file_size = $_FILES['file']['size'];
    $file_tmp = $_FILES['file']['tmp_name'];
    $file_type = $_FILES['file']['type'];
    $xploded =  explode('.', $_FILES['file']['name']);
    $end = end($xploded);
    $file_ext = strtolower($end);

    // echo $file_ext;

    $extensions = array("json");

    if (in_array($file_ext, $extensions) === false) {
        $errors[] = "extension not allowed, please choose a json file.";
    }

    // if ($file_size > 2097152) {
    //     $errors[] = 'File size must be excately 2 MB';
    // }
    // mt_srand(26);
    $name = mt_rand();



    if (empty($errors) == true) {
        move_uploaded_file($file_tmp, "data/" . $name . '.' . $file_ext);

        $json_data = file_get_contents("data/" . $name . '.' . $file_ext);

        $json_data =  json_decode($json_data, true);


        foreach ($json_data as $data) {
            $key = array_search($data, $json_data);

            // echo $key;

            $properties = json_decode(json_encode(json_decode($data["properties"], true)), true);

            $json_data[$key]["properties"] = $properties;
        }

        // $json_data =  json_encode($json_data);



        // $json_data =  json_decode($json_data, true);

        $json_data =  json_encode($json_data);

        file_put_contents("data/" . $name . '.' . $file_ext, $json_data);

        // // echo $json_data;



        $json = new Json(__DIR__ . '/data/' . $name . '.' . $file_ext);

        // var_dump($json);
        // To convert JSON to CSV string
        $csvString = $json->convert();
        // To set a conversion option then convert JSON to CSV and save
        $json->setConversionKey('utf8_encoding', true);
        // $json->convertAndSave(__DIR__ . '/above.csv');
        // To convert JSON to CSV and force download on browser
        $json->convertAndDownload();
    } else {
        print_r($errors);
    }
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Converter</title>
</head>

<body>

    <form action="index.php" method="POST" enctype="multipart/form-data">
        <input required type="file" name="file">
        <button type="submit">Convert</button>
    </form>

</body>

</html>