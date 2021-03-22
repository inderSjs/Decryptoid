<?php

    require_once 'login.php';
    require_once 'helper.php';
    $connection = new mysqli($hn,$un,$pw,$db);
    if($connection->connect_error) die($mysql_fatal_error("error", $connection));

    echo<<<_END
    "    Sign Up"<br></br>
    <form action = "SignUp.php" method = "post"><pre>
    Email <input type = "text" name = "email"<br></br>
    Username <input type = "text" name = "uname"<br></br>
    Password <input type = "text" name = "pwd"<br></br>
    <input type = "submit" value = "Sign Up!">
    </pre></form>
    _END;

    echo<<<_END
    <form action = "LoginPage.php" method = "post"><pre>
    <input type = "submit" value = "Log in">
    </pre></form>
    _END;


    if( isset($_POST['email']) && isset($_POST['uname']) && isset($_POST['pwd'])) {

    

        $bool1 = username_validater($_POST['uname'], $connection);
        $bool2 = email_validater($_POST['email'],$connection);
        $bool3 = password_validater($_POST['pwd'], $connection);

        if ( !$bool2 ) {
            echo <<<_END
                        "Username can only contain English letters (capitalized or not),
                        digits, and the characters '_' (underscore) and '-' (dash). Nothing else"
                 _END;
        }

        if ( $bool1 && $bool2 && $bool3) {
            $un_temp = mysql_fix_string($connection, $_POST['uname']);
            $em_temp = mysql_fix_string($connection, $_POST['email']);
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
            if ( !userExists($un_temp, $connection) ) {
                echo "Username already taken! Try another value<br>";
            } else {  
                $stmt = $connection->prepare('INSERT INTO users VALUES (?,?,?)');
                $pass = hash('ripemd128', "$s1$pw_temp$s2");
                $stmt->bind_param('sss',$un_temp,$pass,$em_temp);
                $stmt->execute();  
                printf("Account Created.\n");
                $stmt->close();
            }
        }
    }


    
    $connection->close();

?>