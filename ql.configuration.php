<?php
if(startsWith(gethostname(), "emadmessiha-php")){
    //development settings
    $DB_SERVERNAME = "localhost";
    $DB_USERNAME = "emadmessiha";
    $DB_PASSWORD = "";
    $DB_DBNAME = "c9";
}else{
    //live settings
    $DB_SERVERNAME = "localhost";
    $DB_USERNAME = "rd-emad";
    $DB_PASSWORD = "rd-emad";
    $DB_DBNAME = "rd-expenses";
}

function startsWith($haystack, $needle)
{
     $length = strlen($needle);
     return (substr($haystack, 0, $length) === $needle);
}

function endsWith($haystack, $needle)
{
    $length = strlen($needle);
    if ($length == 0) {
        return true;
    }

    return (substr($haystack, -$length) === $needle);
}
?>