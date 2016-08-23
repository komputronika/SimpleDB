# Simple MySQLi Functions #

Simple MySQLi functions is MySQLi wrapper to handle common database queries and operations.

**Include file:**
```php
<?php
require("mysqlidb.php");
```


**Connect to database:**
```php
db_connect("localhost","user","pass","student");
```

**Get records example:**
```php
$q = db_query("select * from students");
while ($d = db_fetch($q)) {
    print "$d->id, $d->name <br />";
}
```

**Insert example:**
```php
$a = array();
$a['name'] = db_filter($_POST["name"]);
$a['age']  = db_filter($_POST["age"]);

db_insert("student", $a);
```

**Update example:**
```php
$a = array();
$a['name'] = "John Doe";
$a['age']  = 21;

db_update("student", $a, "id=10");
```

Delete example:
```php
db_delete("student", "id=5");
```

Get last insert id:
```php
//...
db_insert("student", $a);
$last_id = db_last_id();
```