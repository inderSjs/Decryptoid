<?php

    require_once 'login.php';
    require_once 'helper.php';
    require_once 'Substitution.php';
    require_once 'DoubleTransposition.php';
    require_once 'Rc4.php';
    $connection = new mysqli($hn,$un,$pw,$db);
    if($connection->connect_error) die($mysql_fatal_error("error", $connection));
    ini_set('session.gc_maxlifetime', 60 * 60 * 24);
    session_start();
    echo<<<_END
    <form method='post' action='Encrypter.php' enctype='multipart/form-data'>
    <pre>
        Select txt File: <input type = 'file' name='filename' size='10'><br></br>
            Enter your Text: <input type = "text" name = "txt"<br></br>
            Select Cipher
                <select name="cipher" id="form">
                    <option value="-1">Select</option>
                    <option value="substitution">Substitution</option>
                    <option value="doubletransposition">Double Transposition</option>
                    <option value="rc4">Rc4</option>
                </select><br></br>
            Select your desired functionality: 
                <select name="function" id="form">
                    <option value="-1">Select</option>
                    <option value="encryption">Encryption</option>
                    <option value="decryption">Decryption</option>
                </select><br></br>
            <input type = "submit" value = "Submit">
    </pre></form>
    _END;

    if (!isset($_SESSION['initiated'])) 
	{
        session_regenerate_id();
        $_SESSION['initiated'] = 1;
	}


    if( isset($_POST['txt']) && isset($_SESSION['username'])) {
        if ( $_SESSION['check'] != hash('ripemd128',$_SERVER['REMOTE_ADDR'].$_SERVER['HTTP_USER_AGENT'])) {
            different_user();
        } 
        $temp = mysql_fix_string($connection, $_POST['txt']);
        encrypt_decrypt($temp, $connection, $_POST['cipher'], $_POST['function']);
    }

    if ( $_FILES && isset($_SESSION['username']) ) {

        if ( $_SESSION['check'] != hash('ripemd128',$_SERVER['REMOTE_ADDR'].$_SERVER['HTTP_USER_AGENT'])) {
            echo "syap1";
            different_user();
        } 


        if ( ($_POST['function'] != -1) && ($_POST['cipher'] != -1)) {
            $path = $_FILES['filename']['tmp_name'];
            $name = $_FILES['filename']['name'];
            $name = strtolower(preg_replace("[^A-Za-z0-9.]","",$name));
            $filetype = strtolower(pathinfo($name,PATHINFO_EXTENSION));
            if ( $filetype == "txt" ) {
                $fh = fopen($path,'r') or die("Failure");
                $str = file_get_contents($path);
                encrypt_decrypt($str, $connection, $_POST['cipher'], $_POST['function']);
                fclose($fh);
            } 

        } else {
            echo "Please select cipher and desired functionality! <br>";
        }

        
    }

    function updateDatabase ( $str, $connection, $cipher ) {
        $un_temp = mysql_fix_string($connection, $_SESSION['username']);
        $text_temp = mysql_fix_string($connection, $str);
        $cipher_temp = mysql_fix_string($connection, $cipher);
        $temp =gmdate('Y-m-d h:i:s', time());
        $stmt = $connection->prepare('INSERT INTO record VALUES (?,?,?,?)');
        $stmt->bind_param('ssss',$un_temp,$text_temp,$cipher_temp,$temp);
        $stmt->execute();  
        $stmt->close();
    }

    function encrypt_decrypt ($str, $connection, $cipher, $method) {
        updateDatabase($str, $connection, $cipher);
        $str = mysql_fix_string($connection, $str);
        $method = mysql_fix_string($connection, $method);
        $cipher = mysql_fix_string($connection, $cipher);
        if ( strcmp($cipher,"substitution") == 0 ) {
            if ( strcmp($method,"encryption") == 0 ) {
                $temp = substitution_encrypt($str,4);
                echo $temp;
            } else {
                $temp = substitution_decrypt($str,4);
                echo $temp;
            }
        } else if ( strcmp($cipher,"doubletransposition") == 0) {
            if ( strcmp($method,"encryption") == 0 ) {
                $temp = transposition_encrypt($str);
                echo $temp;
            } else {
                $temp = transposition_decrypt($str);
                echo $temp;
            }
        } else if  ( strcmp($cipher,"rc4") == 0) {
            if ( strcmp($method,"encryption") == 0 ) {
                $temp = rc4_encrypt($str, 'abc');
                echo $temp;
            } else {
                $temp = rc4_decrypt($str, 'abc');
                echo $temp;
            }
        }
    }

    function different_user() {
        destroy_session_and_data();
        header("Location:LoginPage.php");

    }

    function destroy_session_and_data() {
        $_SESSION = array();
        setcookie(session_name(), '', time() - 2592000, '/' );
        session_destroy();
	}

    $connection->close();


?>