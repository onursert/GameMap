<?php
    $url = 'https://steamspy.com/api.php?request=tag&tag='.$_POST["tag"];
    $data = file_get_contents($url);
    echo $data;
?>