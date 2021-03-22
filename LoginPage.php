<?php

    require_once 'login.php';
    require_once 'helper.php';
    $connection = new mysqli($hn,$un,$pw,$db);
    if($connection->connect_error) die($mysql_fatal_error("error", $connection));
    session_start();

    echo<<<_END
    "    Login  Page"<br></br>
    <form action = "LoginPage.php" method = "post"><pre>
    Username <input type = "text" name = "uname"<br></br>
    Password <input type = "text" name = "pwd"<br></br>
    <input type = "submit" value = "Login!">
    </pre></form>
    _END;

    echo<<<_END
    <form action = "SignUp.php" method = "post"><pre>
    <input type = "submit" value = "Sign Up!">
    </pre></form>
    _END;

    if (!isset($_SESSION['initiated'])) 
	{
        session_regenerate_id();
        $_SESSION['initiated'] = 1;
	}

    if( isset($_POST['uname']) && isset($_POST['pwd']) ) {
        $bool1 = username_validater($_POST['uname'], $connection);
        $bool2 = password_validater($_POST['pwd'], $connection);

        if ( $bool1 && $bool2) {
            $un_temp = mysql_fix_string($connection, $_POST['uname']);
            $pw_temp = mysql_fix_string($connection, $_POST['pwd']);


            $query = "SELECT * FROM salt";
            $result = $connection->query($query);
            $rows = $result->num_rows;
            $s1 = '';
            $s2 = '';
            for ( $j = 0; $j< $rows; $j++) {
                $result->data_seek($j);
                $row = $result->fetch_array(MYSQLI_ASSOC);
                $s1 = $row['salt1'];
                $s2 = $row['salt2'];
            }
            $result->close();

            if ( !userExists($un_temp,$connection)) {
                $stmt = $connection->prepare('SELECT * FROM users WHERE username = ?');
                $stmt->bind_param('s',$un_temp);
                $stmt->execute();
                $stmt->bind_result($un, $pass,$email);
                $ret = $stmt->fetch();
                if ( $ret > 0 ) {
                    $token = hash('ripemd128', "$s1$pw_temp$s2");
                    if ( $token == $pass) {
                        echo " You are now logged In<br>";
                        $_SESSION['username'] = $un;
                        $_SESSION['check'] = hash('ripemd128',$_SERVER['REMOTE_ADDR'].$_SERVER['HTTP_USER_AGENT']);
                        $stmt->close();
                        success();
                    } else {
                        $stmt->close();
                        echo "Wrong Username/Password!";
                    }
                } else {
                   
                }
                echo "Wrong Username/Password Combination! <br>";
            }        
        }
    }

    function destroy_session_and_data() {
        $_SESSION = array();
        setcookie(session_name(), '', time() - 2592000, '/' );
        session_destroy();
	}

    function success() {
        header("Location:Encrypter.php");
    }

    $connection->close();



?>