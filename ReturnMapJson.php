<?php
/**
 * Created by PhpStorm.
 * User: thacrash
 * Date: 6/2/17
 * Time: 11:05 AM
 */

include ('shmop.php');


$Json = get_cache('Cabs');

header("Content-Type: application/json");

echo ($Json);


