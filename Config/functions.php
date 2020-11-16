<?php

    $config = file_get_contents("config.php");

    function db($string) {
        return $config[$string];
    }