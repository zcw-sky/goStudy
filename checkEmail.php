<?php

function check_mx_record($domain) {
    return checkdnsrr($domain, 'MX');
}

function verify_email($email) {
    list(, $domain) = explode('@', $email);

    // Get MX records
    if (!checkdnsrr($domain, 'MX')) {
        return false;
    }

    $mxhosts = [];
    $mxweight = [];
    getmxrr($domain, $mxhosts, $mxweight);

    if (empty($mxhosts)) {
        return false;
    }

    // Select the first MX host
    $mxhost = $mxhosts[0];

    // Connect to the SMTP server
    $connect = @fsockopen($mxhost, 25, $errno, $errstr, 30);
    if (!$connect) {
        echo "Could not connect to SMTP host: $errstr ($errno)\n";
        return false;
    }

    $response = fgets($connect);
    if (substr($response, 0, 3) != '220') {
        echo "Invalid response after connecting: $response\n";
        fclose($connect);
        return false;
    }

    // HELO command
    fputs($connect, "HELO " . gethostname() . "\r\n");
    $response = fgets($connect);
    echo 'HELO--->'.$response.PHP_EOL;
    if (substr($response, 0, 3) != '250') {
        echo "Invalid response to HELO: $response\n";
        fclose($connect);
        return false;
    }

    // MAIL FROM command
    fputs($connect, "MAIL FROM: <zhw.zou@gmail.com>\r\n");
    $response = fgets($connect);
    echo 'MAIL FROM--->'.$response.PHP_EOL;
    if (substr($response, 0, 3) != '250') {
        echo "Invalid response to MAIL FROM: $response\n";
        fclose($connect);
        return false;
    }

    // RCPT TO command
    fputs($connect, "RCPT TO: <$email>\r\n");
    $response = fgets($connect);
    echo 'RCPT TO--->'.$response.PHP_EOL;
    $result = (substr($response, 0, 3) == '250');

    // QUIT command
    fputs($connect, "QUIT\r\n");
    fclose($connect);

    if (!$result) {
        echo "Invalid response to RCPT TO: $response\n";
    }

    return $result;
}

$email = "zcw909@gmail.com";
if (verify_email($email)) {
    echo "The email address $email exists.";
} else {
    echo "The email address $email does not exist.";
}
?>
