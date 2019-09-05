<?php
require("mysqlidb.php");

$before = microtime(true);

db_connect("127.0.0.1", "root", "", "sispadu2020_");

$q = db_query("select * from siswa");
while ($d = db_fetch($q)) {
    print "<pre>";
    print_r($d);
    print "</pre>";
}

$after = microtime(true);
echo ($after-$before) . " ms";
?>