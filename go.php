<?php

    include("shortify.php");

    $redirect = new Shortify();
    $redirect = $redirect->get_original_url($_REQUEST['q']);

    header("location: {$redirect}");
    exit;
?>
