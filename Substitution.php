<?php


    require_once 'helper.php';
    /* This file contains the code
        for encryption and decrytion
        using simple substitution cipher */


    function substitution_encrypt ( $str, $key) {
        $encrypted_string = '';
        for ( $i = 0; $i < strlen($str); $i++) {
            $value = ord(charAt($str, $i));
            if ( $value >= 65 && $value <= 90 ){
                // when it is an uppercase english character
                $temp = $value - 65;
                $encrypt = ($temp + $key)%26;
                $encrypt = $encrypt + 65;
                $encrypted_string .= chr($encrypt);
            } else if ( $value >= 97 && $value <= 122 ) {
                // when it is an lowercase english character
                $temp = $value - 97;
                $encrypt = ($temp + $key)%26;
                $encrypt = $encrypt + 97;
                $encrypted_string .= chr($encrypt);
            } else {
                $encrypted_string .= charAt($str, $i);
            }
        }
        return $encrypted_string;
    }

    function substitution_decrypt( $str, $key) {
        $newKey = $key *-1;
        return substitution_encrypt($str,$newKey);
    }
?>