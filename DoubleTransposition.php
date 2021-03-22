<?php

    require_once 'helper.php';

    function matrix_size ( $str ) {
        $len = strlen($str);
        $row = floor(sqrt($len)); 
        $column = ceil(sqrt($len));
        if ( $row*$column < $len) {
            $arr[0] = $column;
        } else {
            $arr[0] = $row;
        }
        $arr[1] = $column;
        return $arr;
    }


    function construct_matrix( $str ) {
        $len = strlen($str);
        $counter = 0;
        $size_array = matrix_size($str);
        $arr = array_fill(0, $size_array[0], array_fill(0, $size_array[1], 1));
        for ($i = 0 ; $i < $size_array[0]; $i++ ) {
            for ( $j = 0 ; $j < $size_array[1]; $j++ ) {  
                if ( $counter < strlen($str)) {
                    $arr[$i][$j] = charAt($str, $counter);
                    $counter++; 
                } else {
                    $arr[$i][$j] = " ";
                }                                 
            }
        }
       return $arr;
    }

    function transposition_encrypt ( $str ) {
        $str1 = sanitizeString($str);
        $array = construct_matrix($str1);
        $temp = 0;
        for ( $i = 0 ; $i < (count($array)-1); $i++ ) {
            $temp = $array[$i];
            $array[$i] = $array[count($array)-1];
            $array[count($array)-1] = $temp;
        }
        echo "encryption:";
        return print_string($array);
    }

    function transposition_decrypt ( $str ) {
        $str1 = sanitizeString($str);
        $array = construct_matrix($str1);
        $temp = 0;
        for ( $i = (count($array)-1) ; $i > 0; $i-- ) {
            $temp = $array[$i];
            $array[$i] = $array[0];
            $array[0] = $temp;
        }
        echo "Decrption:";
        return print_string($array);
    }

    function print_string ( $array ) {
        $temp ='';
        for ( $i = 0 ; $i < count($array); $i++ ) {
            for ( $j = 0 ; $j < count($array[$i]); $j++) {
                $temp .= $array[$i][$j];
            }
        }
        return $temp;
    }

?>