<?php
    $url = 'https://steamspy.com/api.php?request=all';
    $data = file_get_contents($url);
    echo $data;
?>