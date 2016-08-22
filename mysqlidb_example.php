<?php
require("mysqlidb.php")

$before = microtime(true);
db_connect("localhost", "root", "", "hrd");
$q = db_query("select * from karyawan");
while ($d = db_fetch($q)) {
    print "<pre>";
    print_r($d);
    print "</pre>";
}
$after = microtime(true);
echo ($after-$before) . " ms";
?>