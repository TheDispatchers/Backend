<?php

/**
 * @param $data - Data to be stored
 * @param $name - Name of the block it's being saved out
 * @param $timeout - How long the information should be in the memory
 * @return bool|int - Int for data size, or false if it failed
 */
function save_cache($data, $name, $timeout) {
    // delete cache
    $id=shmop_open(get_cache_id($name), "a", 0, 0);
    shmop_delete($id);
    shmop_close($id);

    // get id for name of cache
    $id=shmop_open(get_cache_id($name), "c", 0644, strlen(serialize($data)));

    // return int for data size or boolean false for fail
    if ($id) {
        set_timeout($name, $timeout);
        return shmop_write($id, serialize($data), 0);
    }
    else return false;
}

/**
 * @param $name - Name of the shared memory you wish to access
 * @return bool|mixed - Returns data if succesfull, false if it failed
 */
function get_cache($name) {
    if (!check_timeout($name)) {
        $id=shmop_open(get_cache_id($name), "a", 0, 0);

        if ($id) $data=unserialize(shmop_read($id, 0, shmop_size($id)));
        else return false;          // failed to load data

        if ($data) {                // array retrieved
            shmop_close();
            //echo "Data received from shmop :".(string)$data."\n";
            return $data;
        }
        else return false;          // failed to load data
    }
    else return false;              // data was expired
}

/**
 * @param $name Hardcoded names for sharedmemory blocks, so far only Cabs is used, but Users is for a future iteration (to view on the livemap)
 * @return mixed - Returns matching ID of the name
 */
function get_cache_id($name) {
    // maintain list of caches here
    $id=array(  'Cabs' => 1,
                'Users' => 2
                );

    return $id[$name];
}

/**
 * @param $name - Name of the block you are setting the timeout for
 * @param $int - How long the timeout should in seconds.
 */
function set_timeout($name, $int) {
    $timeout=new DateTime(date('Y-m-d H:i:s'));
    date_add($timeout, date_interval_create_from_date_string("$int seconds"));
    $timeout=date_format($timeout, 'YmdHis');

    $id=shmop_open(100, "a", 0, 0);
    if ($id) $tl=unserialize(shmop_read($id, 0, shmop_size($id)));
    else $tl=array();
    shmop_delete($id);
    shmop_close($id);

    $tl[$name]=$timeout;
    $id=shmop_open(100, "c", 0644, strlen(serialize($tl)));
    shmop_write($id, serialize($tl), 0);
}

/**
 * @param $name - Check if the timeout is reached
 * @return bool
 */
function check_timeout($name) {
    $now=new DateTime(date('Y-m-d H:i:s'));
    $now=date_format($now, 'YmdHis');
    $id=shmop_open(100, "a", 0, 0);
    if ($id) $tl=unserialize(shmop_read($id, 0, shmop_size($id)));
    else return true;
    shmop_close($id);

    $timeout=$tl[$name];
    return (intval($now)>intval($timeout));
}

?>