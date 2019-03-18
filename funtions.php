<?php
/**
* Archivo con funciones cortas
* @version 2.1
* @author Antonio Comino <acominogarcia@gmail.com>
*
*/

/* ------------------------------------------------------------------------- *
 *  Functions
/* ------------------------------------------------------------------------- */

/**
 * Connect:
 * Connection to the Database with PDO
 * @return object
 */
function Connect()
{
    try {
    	//llamada para la configuracion de la base de datos por archivo de configuracion
        $config = getConfig();
        $dsn = 'mysql:host='.$config["database"]["host"].';port='.$config["database"]["port"].';dbname='.$config["database"]["name"];
        return new PDO($dsn, $config["database"]["user"], $config["database"]["password"]);
    } catch (PDOException $e) {
        echo 'Error: ' . $e->getMessage();
    }
} //end method Connect
/**
 * getConfig:
 * Configuration for json web token including data for database connection
 * @return json token
 */
function getConfig()
{
    $serverName = $_SERVER['SERVER_NAME'];
    if ($serverName == "localhost") {
        $host = "127.0.0.1";
        $name = "name_databse";
        $user = "root";
        $password = "";
        $port = "3306";
    } else {
        $host = "host_remote";
        $name = "database_remote";
        $user = "user_remote";
        $password = "";
        $port ="port_remote";
    }
    $masterKey = base64_encode('master_key');
    $salt = '3ncr1pty';
    $jwt_app = array(
        'jwt_app' => array(
            'key' => $masterKey, // Clave maestra para el token
            'algorithm' => 'HS512', // Algorithm used to sign the token, consult https://tools.ietf.org/html/draft-ietf-jose-json-web-algorithms-40#section-3
        ),
        'database' => array(
            'host' => $host, // Database host
            'name' => $name, // Database schema name
            'user' => $user, // Database username
            'password' => $password, // Database password,
            'port' => $port //puerto de la base de datos
        ),
        'serverName' => $serverName,
        'salt' => $salt,
    );
    return $jwt;
} //end method getConfig
/**
 * Returns the field of a table
 *
 * @return string the data associated with the field of the bbdd indicated in $field
 * @param $field -> field
 * @param $table -> table
 * @param $where -> conditions where [opcional] default true
 *
 */
function getFieldWhere($field, $table, $where = true)
{
    $conn = Connect();
	try {
		$select = "SELECT $field FROM $table WHERE $where";
		$result = $conn->prepare($select);
		if (!$result->execute()) {
			//recogida de error, con funcion pdo de errorInfo, parametro 2 (solo mensajes)
			$aErrSql = $result->errorInfo();
			throw new Exception("Mensaje error: " . $aErrSql[2] . "\nSentencia: " . $select);
		}
		$fetch = $result->fetchAll(PDO::FETCH_ASSOC);
		foreach ($fetch as $key => $row) {
			$sField = $row[$field];
		}
		return $sField;
	} catch (PDOException $e) {
		echo 'Error: ' . $e->getMessage();
	}
} //end method getFieldWhere
/**
 * Return total of tuples affected by a selection of conditions of the bbdd, if $sWhere is true return total of records in a table according to $table
 *
 * @return int numero de tuplas afectadas
 * @param $table -> tabla
 * @param $where -> condiciones where [opcional] por defecto true
 *
 */
function getFieldCount($table, $field, $sWhere = true)
{
    $conn = Connect();
    $select = "SELECT COUNT(1) FROM $table WHERE $sWhere";
	$result = $conn->prepare($select);
	if (!$result->execute()) {
		//recogida de error, con funcion pdo de errorInfo, parametro 2 (solo mensajes)
		$aErrSql = $result->errorInfo();
		throw new Exception("Mensaje error: " . $aErrSql[2] . "\nSentencia: " . $select);
	}
	return $result->fetchColumn();
}//end method getFieldCount
/**
 * returns data for combobox with two fields
 *
 * @param $field1 -> id
 * @param $field2 -> name
 * @param $table -> table
 * @param [$sWhere] -> optional, sentence
 *
 * @return (json).
 */
function getComboTwoFields($table, $sWhere = true)
{
    $conn = Connect();
	$select = "SELECT id id, name_field name from $table WHERE $sWhere";
	$result = $conn->prepare($select);
	if (!$result->execute()) {
		//recogida de error, con funcion pdo de errorInfo, parametro 2 (solo mensajes)
		$aErrSql = $result->errorInfo();
		throw new Exception("Mensaje error: " . $aErrSql[2] . "\nSentencia: " . $select);
	}
	return $result->fetchAll(PDO::FETCH_OBJ);
} //end method getComboTwoFields
/**
 * Returns an array with the data
 *
 * @return array data array of the bbdd field indicated in $field
 * @param $field -> filed
 * @param $table -> table
 * @param $where -> conditions where [optional] default true
 *
 */
function getArrayFieldWhere($field, $table, $where = true)
{
    $conn = Connect();
	$select = "SELECT $field FROM $table WHERE $where";
	$result = $conn->prepare($select);
	if (!$result->execute()) {
		//recogida de error, con funcion pdo de errorInfo, parametro 2 (solo mensajes)
		$aErrSql = $result->errorInfo();
		throw new Exception("Mensaje error: " . $aErrSql[2] . "\nSentencia: " . $select);
	}
	$fetch = $result->fetchAll(PDO::FETCH_ASSOC);
	$arrayRetun = array();
	foreach ($fetch as $key => $row) {
		array_push($arrayRetun, $row[$field]);
	}
	return $arrayRetun;
} //end method getArrayFieldWhere
/**
 * Returns an alphanumeric randon string for passwords according to a length given by $length
 * The native function of PHP str_shuffle
 * # function str_shuffle (Sort randomly a string)
 *
 * @return string random string alphanumeric
 * @param $length -> the length of the chain
 *
 */
function generateRandomString($length = 11)
{
	return substr(str_shuffle("0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ"), 0, $length);
} //end method generateRandomString
/**
 * diffDate:
 * Difference between dates
 * @param $date1 : date one
 * @param $date2: date two
 * @return integer
 */
function diffDateInMoths($date1,$date2)
{
	$datei = new Date($date1);
	$datef = new Date($date2);
	return $datei->diff($datef)->m+1;
}//End method diffDate
/**
 * Verify that a date is within the range of established dates
 * @param $startDate:  fecha de inicio
 * @param $endDate: final date
 * @param $evaluame: date to compare
 * @return boolean true if it is in the range, false if it is not
 */
function checkInRangeDate($startDate, $endDate, $evaluame) {
    $startTs = strtotime($startDate);
    $endTs = strtotime($endDate);
    $checkTs = strtotime($evaluame);
    return (($checkTs >= $startTs) && ($checkTs <= $endTs));
}//end method checkInRangeDate
/**
 * Replace the keys of an array with other keys
  * @param array $array
  * @param array $replacements
  * @param boolean $override
  * @return array
  */
function array_replace_keys(array $array, array $replacements, $override = false) {
    foreach ($replacements as $old => $new) {
        if(is_int($new) || is_string($new)){
            if(array_key_exists($old, $array)){
                if(array_key_exists($new, $array) && $override === false){
                    continue;
                }
                $array[$new] = $array[$old];
                unset($array[$old]);
            }
        }
    }
    return $array;
}//end method array_replace_keys
/**
 * countVowels:
 * Returns number of vowels in provided string.
 * Use a regular expression to count the number of vowels (A, E, I, O, U) in a string.
 * @return integer
 */
function countVowels($string)
{
    preg_match_all('/[aeiou]/i', $string, $matches);

    return count($matches[0]);
}//End method countVowels


/**
 * pluck:
 * Retrieves all of the values for a given key
 * @return object
 */
function pluck($items, $key)
{
    return array_map( function($item) use ($key) {
        return is_object($item) ? $item->$key : $item[$key];
    }, $items);
}//End method pluck
//ejemplo
echo pluck([
    ['product_id' => 'prod-100', 'name' => 'Desk'],
    ['product_id' => 'prod-200', 'name' => 'Chair'],
], 'name');

/**
 * lcm:
 * Returns the least common multiple of two or more numbers.
 * @return integer
 */
function lcm(...$numbers)
{
    $ans = $numbers[0];
    for ($i = 1, $max = count($numbers); $i < $max; $i++) {
        $ans = (($numbers[$i] * $ans) / gcd($numbers[$i], $ans));
    }

    return $ans;
}//End method lcm


/**
 * reject:
 * Filters the collection using the given callback.
 */
function reject($items, $func)
{
    return array_values(array_diff($items, array_filter($items, $func)));
}//End method reject
//ejemplo
echo reject(['Apple', 'Pear', 'Kiwi', 'Banana'], function ($item) {
    return strlen($item) > 4;
});


?>


