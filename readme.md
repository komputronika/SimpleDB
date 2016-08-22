# Simple MySQLi Function #

Simple MySQLi function is MySQLi wrapper to handle common database queries and operations.

**Include file:**
```
<?php
require("mysqlidb.php");
```


**Connect to database:**
```
db_connect("localhost","user","pass","student");
```


**Get data from table:**
```
$q = db_query("select * from students");
while ($d = db_fetch($q)) {
    print "$d->id, $d->name <br />";
}
```




