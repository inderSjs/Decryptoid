<?php


function userExists( $str, $connection ) {
    $bool = false;
    $stmt = $connection->prepare('SELECT username FROM users WHERE username = ?');
    $stmt->bind_param('s',$str);
    $stmt->execute();
    $t1 = $stmt->fetch();
    if ( $t1 == NULL) {
        $bool = true;
    }
    $stmt->close();
    return $bool;
}


function email_validater( $str, $connection) {
    $bool = true;
    $str = mysql_fix_string($connection,$str);
    if ( filter_var($str, FILTER_VALIDATE_EMAIL)) {
    } else {
        $bool = false;
        echo "Email is not in Proper Format<br>";
    }
    return $bool;
}



function username_validater( $str, $connection ) {
    $bool = true;
    $str = mysql_fix_string($connection,$str);

    for ( $i = 0; $i < strlen($str); $i++) {
        $temp = ord(charAt($str,$i));
        if ( $temp < 45 || $temp > 122 ) {
            $bool =false;
        } else if ( $temp > 45 && $temp < 48) {
            $bool = false;
        } else if ( $temp > 57 && $temp < 65) {
            $bool= false;
        } else if ( $temp > 90 && $temp < 95) {
            $bool = false;
        } else if ( $temp > 95 && $temp < 97) {
            $bool = false;
        }
    }
    if ( !$bool ) {
        echo "Wrong Username <br>";
    }
    return $bool;
}

function password_validater( $str, $connection ) {
    $bool = true;
    $str = mysql_fix_string($connection,$str);
    if ( strlen($str) < 8) {
        $bool = false;
        echo "Password should be atleast 8 characters! <br>";
    }
    return $bool;
}
function sanitizeString($var) {
    $var = stripslashes($var);
    $var = strip_tags($var);
    $var = htmlentities($var);
    return $var;
}

function mysql_fix_string($connection, $var) {
    $var = $connection->real_escape_string($var);
    $var = sanitizeString($var);
    return $var;
}

function mysql_fatal_error($msg, $connection) {
    $msg2 = mysqli_error($connection);
    echo<<< _END
    The task initiated by you cannot be completed at the moment.
    The error message we got was:
    <p>$msg:$msg2</p>
    Please contact the system administrator.
    Thank you.
    _END;
}

function charAt ( $str, $index ) {
    if ( $index >= strlen($str)) {
        echo "Invalid Index ";
        return null;
    } else {
        return $str[$index];
    }
}

?>