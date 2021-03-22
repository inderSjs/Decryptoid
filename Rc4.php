<?php
    require_once 'helper.php';

    function convert_key( $str ) {
        $arr = array_fill(0, strlen($str), 1);
        for ( $i =0 ; $i < strlen($str); $i++ ) {
            $arr[$i] = ord(charAt($str,$i));
        }
        return $arr;
    }

    function rc4_encrypt($str, $key) {
        $temp = convert_key($key);
        $converted_text= convert_key($str);
        $S = array_fill(0, strlen($str),1);
        $K = array_fill(0, strlen($str),1);
        for ( $i = 0 ; $i < 256; $i++ ) {
            $S[$i] = $i;
            $K[$i] = $temp[$i%(strlen($key))];
        }
        $j = 0;
        for ( $i = 0 ; $i < 256 ; $i++ ) {
            $j = ( $j + $S[$i] + $K[$i])%256;
            $t = $S[$i];
            $S[$i] = $S[$j];
            $S[$j] = $t;
        }
        $i = 0;
        $j = 0;
        $i = ($i+1)%256;
        $j = ($j + $S[$i])%256;
        $t1 = $S[$i];
        $S[$i] = $S[$j];
        $S[$j] = $t1;
        $t = ($S[$i] + $S[$j])%256;
        $keyStreamByte = $S[$t];
        $result = array_fill(0, strlen($str), 1);
        for ( $i = 0 ; $i < count($converted_text); $i++ ) {
            $result[$i] = $converted_text[$i]^$keyStreamByte;
        }
        return toHex($result);
    }

    function rc4_decrypt($str, $key) {
        $temp = convert_key($key);
        $array = fromHex($str);
        $S = array_fill(0, count($array),1);
        $K = array_fill(0, count($array),1); 
        for ( $i = 0 ; $i < 256; $i++ ) {
            $S[$i] = $i;
            $K[$i] = $temp[$i%(strlen($key))];
        }
        $j = 0;
        for ( $i = 0 ; $i < 256 ; $i++ ) {
            $j = ( $j + $S[$i] + $K[$i])%256;
            $t = $S[$i];
            $S[$i] = $S[$j];
            $S[$j] = $t;
        }
        $i = 0;
        $j = 0;
        $i = ($i+1)%256;
        $j = ($j + $S[$i])%256;
        $t1 = $S[$i];
        $S[$i] = $S[$j];
        $S[$j] = $t1;
        $t = ($S[$i] + $S[$j])%256;
        $keyStreamByte = $S[$t];
        $result = array_fill(0, count($array), 1);
        for ( $i = 0 ; $i < count($array); $i++ ) {
            $result[$i] = $array[$i]^$keyStreamByte;
        }
        return toString($result);
    }


    function fromHex( $str ) {
        $result = array(0);
        $counter = 0;
        for ( $i = 0 ; $i < strlen($str); $i++) {
            if ( charAt($str,$i) == "-") {
                $result[$counter] = hexdec(substr($str,$i-2,2));
                $counter++; 
            } else {
                // Do Nothing
            }
        }
        $result[$counter] = hexdec(substr($str,strlen($str)-2,2 ));
        return $result;
    }

    function toHex( $arr ) {
        $ret = "Encryption in Hex: ";
        for ( $i = 0 ; $i < count($arr); $i++) {
           $ret .= dechex($arr[$i]);
           if ( $i < (count($arr)-1)) {
               $ret .= '-';
           }
        }
        return $ret;
    }

    function toString ($array) {
        $ret = '';
        for ( $i = 0 ; $i < count($array); $i++) {
            $ret .= chr($array[$i]);
        }
        return $ret;
    }
?>