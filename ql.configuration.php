<?php
if(stringStartsWith(gethostname(), "emadmessiha-php")){
    //development settings
    $DB_SERVERNAME = "localhost";
    $DB_USERNAME = "emadmessiha";
    $DB_PASSWORD = "";
    $DB_DBNAME = "c9";
}else{
    //live settings
    $DB_SERVERNAME = "localhost";
    $DB_USERNAME = "emadmessiha";
    $DB_PASSWORD = "";
    $DB_DBNAME = "c9";
}

function stringStartsWith($haystack, $needle)
{
     $length = strlen($needle);
     return (substr($haystack, 0, $length) === $needle);
}

function stringEndsWith($haystack, $needle)
{
    $length = strlen($needle);
    if ($length == 0) {
        return true;
    }

    return (substr($haystack, -$length) === $needle);
}
?>
