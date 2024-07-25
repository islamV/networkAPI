<?php


if (!function_exists("createDB")) {
  function createDB($table, array $data = [])
  {
    $query = "INSERT INTO " . $table;
    $columns =  '';
    $values  =  '';
    foreach ($data  as $key => $value) {
      $columns .= $key . ",";
      $values .= " '" . $value . "' ,";
    }

    $values = rtrim($values, ',');
    $columns = rtrim($columns, ',');


    $query .= " (" . $columns . ") VALUES  (" . $values . ")";
    $GLOBALS['query'] = $query;
    mysqli_query($GLOBALS['connect'], $query);

    return mysqli_insert_id($GLOBALS['connect']);
  }
}

if (!function_exists("updateDB")) {
  function updateDB($table, array $data = [], $condition)
  {
    $query = "UPDATE " . $table . " set ";
    $datac =  '';

    foreach ($data  as $key => $value) {
      $datac .= $key . "='" . $value . "' ,";
    }

    $datac = rtrim($datac, ',');
     # code...

    $query .= $datac . $condition;
    $GLOBALS['query'] = $query;
    mysqli_query($GLOBALS['connect'], $query);
   
  }
}


if (!function_exists("deleteDB")) {
  function deleteDB($table, $id)
  {
    if (findDb($table, $id)) {
      $q = mysqli_query($GLOBALS['connect'], "DELETE FROM " . $table . " WHERE id = " . $id);
      $GLOBALS['query'] = $q;

    } else {
      return "NOT FOUND";
    }
  }
}

if (!function_exists("findDB")) {
  function findDB($table, $id): mixed
  {
    $q = mysqli_query($GLOBALS['connect'], "SELECT * FROM " . $table . " WHERE id = " . $id);
    $GLOBALS['query'] = $q;
    $result = mysqli_fetch_assoc($q);
    return $result;
  }
}


if (!function_exists("firstDB")) {
  function firstDB($table, string $query_statment): mixed
  {
    $q = mysqli_query($GLOBALS['connect'], "SELECT * FROM " . $table ." ". $query_statment);
    $GLOBALS['query'] = $q;
    $result = mysqli_fetch_assoc($q);
    return $result;
  }
}


if (!function_exists("getDB")) {
  function getDB($table, string $query_statement, string $select = "*"): array
  {
    // Execute the query
    $q = mysqli_query($GLOBALS['connect'], "SELECT $select FROM $table $query_statement");

    // Check for query errors
    if (!$q) {
      die("Query failed: " . mysqli_error($GLOBALS['connect']));
    }

    // Fetch all results
    $data = mysqli_fetch_all($q, MYSQLI_ASSOC);

    // Add count of rows to the data
    $num_rows = mysqli_num_rows($q);
    $result = ['data' => $data, 'count' => $num_rows];

    // Free result set and close the connection
    mysqli_free_result($q);

    return $result;
  }
}

// $users = getPaginateDB("users" , '',2) ;

// while($row = mysqli_fetch_assoc($users['query'])){
//           echo $row['email'] . "<br>" ;
// }
// echo $users['render'];