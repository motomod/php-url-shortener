<?php

    include("shortify.php");

    $shorten = new Shortify($_POST['url']);

    if (!empty($errors = $shorten->errors()))
    {
        echo implode(' - ', $errors);
        exit;
    }

    $url = "http://" . $_SERVER["HTTP_HOST"] . $_SERVER["REQUEST_URI"];
    $url = rtrim($url, 'geturl.php');

    echo $url . "go.php?q={$shorten->get_hash()}";
?>
