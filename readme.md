# Simple MySQLi Functions #

Simple MySQLi functions is MySQLi wrapper to handle common database queries and operations.

**Include file:**
```
<?php
require("mysqlidb.php");
```


**Connect to database:**
```
db_connect("localhost","user","pass","student");
```


**Get records example:**
```
$q = db_query("select * from students");
while ($d = db_fetch($q)) {
    print "$d->id, $d->name <br />";
}
```

**Insert example:**
```
$a = array();
$a['name'] = db_filter($_POST["name"]);
$a['age']  = db_filter($_POST["age"]);

db_insert("student", $a);
```

**Update example:**
```
$a = array();
$a['name'] = "John Doe";
$a['age']  = 21;

db_update("student", $a, "id=10");
```

