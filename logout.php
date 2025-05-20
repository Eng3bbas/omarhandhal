<?php

session_start();
session_unset();
session_destroy();
echo '
<link rel="stylesheet" href="style2.css"/>
                        <div class="style3">
                            <p>   شكرا" لك تم تسجيل الخروج</p>
                        </div>
                                                <meta http-equiv="refresh" content="3, url=login.php"/>';


//header('location:login.php');
exit();


?>