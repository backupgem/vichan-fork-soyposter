<?php
die();
echo "<PLAINTEXT>";


$acceptLanguage = $_SERVER['HTTP_ACCEPT_LANGUAGE'] ?? '';

if ($acceptLanguage) {
    // Get the first language from the header
    $language = explode(',', $acceptLanguage)[0];

    echo "Your preferred language is: " . htmlspecialchars($language);
} else {
    echo "Your browser did not provide a language preference.";
}

echo $_SERVER['HTTP_CF_IPCOUNTRY'];
echo "\n\n";
var_dump(getallheaders());
