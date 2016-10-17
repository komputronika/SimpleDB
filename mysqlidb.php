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
    $GLOBALS["_conn"] = new mysql_connect( $host, $user, $pass, $name );
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
    $d = ($array) ? $q->fetch_row() : $q->fetch_object();
    if (!empty($GLOBALS["_conn"]->error)) {
        db_error("".$GLOBALS["_conn"]->error);
    }
    return $d;
}

//--------------------------------------
// $status = db_insert( table, array )
//--------------------------------------
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
// $n = db_num_rows( resource )
//--------------------------------------
function db_num_rows($q) {
    //try {
    //$q = db_query($str);
    $n = $q->num_rows;
    //} catch(Exception $e) {
    //    db_error("".$e->getMessage()."<br>");
    //}
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

?>