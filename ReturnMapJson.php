<?php
/**
 * Created by PhpStorm.
 * User: thacrash
 * Date: 6/2/17
 * Time: 11:05 AM
 */

/**
 * Generates a JSON for the management.php to view available taxis. It reads from the shared memory.
 */

include ('shmop.php');


$Json = get_cache('Cabs');

header("Content-Type: application/json");

echo ($Json);


