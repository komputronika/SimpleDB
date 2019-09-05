<?php
/*============================================*\
Simple MySQLi Functions

HOW TO USE:

//--- connect to database
db_connect($host, $user, $pass, $name);

//--- get record from a table
$q = db_query("select * from students");
while ($d = db_fetch($q)) {
    print "$d->id, $d->name <br />";
}

//--- insert to a table
$a = array();
$a['name'] = "John";
$a['age']  = 20;
db_insert("student", $a);

//--- update a table
$a = array();
$a['name'] = "John Doe";
$a['age']  = 21;
db_update("student", $a, "id=10");

\*============================================*/

//--------------------------------------
// db_connect( host, user, pass, database name)
//--------------------------------------
function db_connect($host, $user, $pass, $name) {
    $GLOBALS["_conn"] = new mysqli( $host, $user, $pass, $name );
    if (!empty($GLOBALS["_conn"]->error)) {
        db_error("".$GLOBALS["_conn"]->error);
    }
    return $GLOBALS["_conn"];
}

//--------------------------------------
// db_close()
//--------------------------------------
function db_close() {
    $GLOBALS["_conn"]->close();
    return;
}

//--------------------------------------
// $str = db_filter( string )
//--------------------------------------
function db_filter( $data ) {
    $_conn = $GLOBALS["_conn"];
    if( !is_array( $data ) ) {
        $data = $_conn->real_escape_string( $data );
        $data = trim( htmlentities( $data, ENT_QUOTES, 'UTF-8', false ) );
    } else {
        // Self call function to sanitize array data
        $data = array_map( array( $this, 'db_filter' ), $data );
    }
    return $data;
}

//--------------------------------------
// $str = db_escape( string )
//--------------------------------------
function db_escape( $data ) {
    $_conn = $GLOBALS["_conn"];
    if( !is_array( $data ) ) {
        $data = $_conn->real_escape_string( $data );
    } else {
        // Self call function to sanitize array data
        $data = array_map( array( $this, 'db_filter' ), $data );
    }
    return $data;
}

//--------------------------------------
// echo db_clean( string )
//--------------------------------------
function db_clean( $data ) {
    $data = stripslashes( $data );
    $data = html_entity_decode( $data, ENT_QUOTES, 'UTF-8' );
    $data = nl2br( $data );
    $data = urldecode( $data );
    return $data;
}

//--------------------------------------
// $q = db_query( sql )
//--------------------------------------
function db_query($str) {
    $_conn = $GLOBALS["_conn"];
    $q = $_conn->query($str);
    if (!empty($GLOBALS["_conn"]->error)) {
        db_error("".$GLOBALS["_conn"]->error);
    }
    return $q;
}

//--------------------------------------
// $q = db_get_row( sql )
//--------------------------------------
function db_get_row($str) {
    $_conn = $GLOBALS["_conn"];
    $q = $_conn->query($str);
    $d = $q->fetch_object();
    if (!empty($GLOBALS["_conn"]->error)) {
        db_error("".$GLOBALS["_conn"]->error);
    }
    return $d;
}

//--------------------------------------
// $d = db_fetch( resource, array )
//--------------------------------------
function db_fetch($q, $array=false) {
    $d = ($array) ? $q->fetch_array(MYSQLI_ASSOC) : $q->fetch_object();
    if (!empty($GLOBALS["_conn"]->error)) {
        db_error("".$GLOBALS["_conn"]->error);
    }
    return $d;
}

//--------------------------------------
// $d = db_fetch_array( resource )
//--------------------------------------
function db_fetch_array($q) {
    return db_fetch($q, true);
}

//--------------------------------------
// $status = db_insert( table, array )
//--------------------------------------
function db_insert_array($table, $array) {
    db_insert($table, $array);
}

function db_insert($table, $array) {
    $field  = "(";
    $values = "(";
    foreach ($array as $key=>$val){
        if (!empty($val)){
            $field  .= "$key,";
            $values .= "'$val',";
        }
    }
    $field  = substr($field,0,-1).")";
    $values = substr($values,0,-1).");";

    $str = "INSERT INTO $table $field VALUES $values";
    return db_query($str);
}

//--------------------------------------
// $status = db_update( "siswa", $a, "siswa_id = '37'" )
//--------------------------------------
function db_update_array($table, $array, $cond="") {
    db_update($table, $array, $cond);
}

function db_update($table, $array, $cond="") {
    $sets = "";
    foreach ($array as $key=>$val){
        //if (!empty($val)){
            $sets .= "$key = '$val',";
        //}
    }
    $sets = substr($sets,0,-1);
    $str = "UPDATE $table SET $sets WHERE $cond";
    return db_query($str);
}

//--------------------------------------
// $status = db_delete( "siswa", "siswa_id = '37'" )
//--------------------------------------
function db_delete($table, $cond="") {
    return db_query("DELETE FROM $table WHERE $cond");
}

//--------------------------------------
// $n = db_num_rows( sql )
//--------------------------------------
function db_num_rows( $str ) {
    try {
        $q = db_query($str);
        $n = $q->num_rows;
    } catch(Exception $e) {
        db_error("".$e->getMessage()."<br>");
    }
    if (!empty($GLOBALS["_conn"]->error)) {
        db_error("".$GLOBALS["_conn"]->error);
    }
    return $n;
}

//--------------------------------------
// $last_id = db_last_id()
//--------------------------------------
function db_last_id() {
    $_conn = $GLOBALS["_conn"];
    $id = $_conn->insert_id;
    if (!empty($GLOBALS["_conn"]->error)) {
        db_error("".$GLOBALS["_conn"]->error);
    }
    return $id;
}

//--------------------------------------
// $n = db_affected_rows()
//--------------------------------------
function db_affected_rows() {
    $_conn = $GLOBALS["_conn"];
    $n = $_conn->affected_rows;
    if (!empty($GLOBALS["_conn"]->error)) {
        db_error("".$GLOBALS["_conn"]->error);
    }
    return $n;
}

//--------------------------------------
// db_shutdown()
//--------------------------------------
function db_shutdown() {
    db_close();
    die();
}

//--------------------------------------
// db_list_fields( table )
//--------------------------------------
function db_list_fields($table) {
    $_conn = $GLOBALS["_conn"];
    $q = db_query("SELECT * FROM $table");
    return $q->fetch_fields();
}

//--------------------------------------
// db_error( message )
//--------------------------------------
function db_error($str) {
    echo "<div class='db-error'><h1>DATABASE ERROR:</h1><p>$str</p></div>";
    die();
}

// New functions 05-September-2019

//--------------------------------------
// db_insert_update( table, condition, data )
//--------------------------------------
function db_insert_update($table, $where, $array) {
    $sql = "select * from $table where $where";
    $n = db_num_rows($sql);
    if ($n==0) {
        db_insert_array($table, $array);
    } else {
        db_update_array($table, $array, $where);
    }
    if (!empty($GLOBALS["_conn"]->error)) {
        db_error("".$GLOBALS["_conn"]->error);
    }
}

//--------------------------------------
// value_lookup( id, table, column id, column name)
//--------------------------------------
function value_lookup($id, $table, $col_id, $col_name) {
    $q = db_query("select $col_id, $col_name from $table where $col_id='$id'");
    if ($q->num_rows) {
        $d = db_fetch_array($q);
        return $d["$col_name"];
    } else {
        return null;
    }
}

//--------------------------------------
// select_lookup( select name, table, column id, column name, default, extra html tag)
//--------------------------------------
function select_lookup($name, $table, $col_id, $col_name, $default, $string="") {
    echo "<select name='$name' id='$name' $string>";
    $q = db_query("select $col_id,$col_name from $table");
    while ($d = db_fetch_array($q)) {
        $sel = stripslashes($default) == $d["$col_id"] ? " selected" : "";
        echo "<option value='".$d["$col_id"]."' $sel>".$d["$col_name"]."</option>";
    }
    echo "</select>";
}

//--------------------------------------
// query( sql )
//--------------------------------------
function query($sql) {
    $q = db_query($sql);
    $array = array();
    while ($d = db_fetch($q)) {
        $array[]=$d;
    }
    return $array;
}

//--------------------------------------
// db_one( sql )
//--------------------------------------
function db_one($str){
    $q = db_query($str);
    $d = db_fetch($q);
    if (!empty($GLOBALS["_conn"]->error)) {
        db_error("".$GLOBALS["_conn"]->error);
    }
    return $d;
}

?>
