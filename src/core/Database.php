<?php
/*
 * Author: Dahir Muhammad Dahir
 * Date: 26-April-2020 12:05 AM
 * About: I will tell you later
 */


namespace fingerprint;

use mysqli;
use RuntimeException;

require_once(__DIR__ . '/../../configs.php');

date_default_timezone_set("Africa/Lagos");

class Database {
    private $connection;

    function __construct(){
        if (!extension_loaded('mysqli')) {
            throw new RuntimeException('The mysqli PHP extension is required.');
        }

        mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
        try {
            $this->connection = new mysqli(
                \fingerprint_config('DB_HOST', '127.0.0.1'),
                \fingerprint_config('DB_USER', 'root'),
                \fingerprint_config('DB_PASSWORD', ''),
                \fingerprint_config('DB_NAME', 'fingerprint'),
                (int) \fingerprint_config('DB_PORT', '3306')
            );
            $this->connection->set_charset('utf8mb4');
        } catch (\mysqli_sql_exception $exception) {
            throw new RuntimeException(
                'Unable to connect to the configured MySQL database.',
                0,
                $exception
            );
        }
    }

    function ping(): bool {
        return $this->connection->ping();
    }

    function getUserInfo($username): array {
        $sql_query = "SELECT * FROM users WHERE username=?";
        $param_type = "s";
        $param_array = [$username];
        $result = $this->select($sql_query, $param_type, $param_array);
        return $result;
    }

    function select($sql_query, $sql_param_type="", $param_array=array()){
        if ($statement = $this->connection->prepare($sql_query)){
            if (!empty($sql_param_type) && !empty($param_array)){
                $this->bindQueryParams($statement, $sql_param_type, $param_array);
            }

            $statement->execute();
            $sql_query_result = $statement->get_result();

            if ($sql_query_result->num_rows > 0){
                while ($row = $sql_query_result->fetch_assoc()){
                    $result_set[] = $row;
                }
            }

            if (!empty($result_set)){
                return $result_set;
            }
        }
    }

    function insert($sql_query, $sql_param_type="", $param_array=array()){
        if ($statement = $this->connection->prepare($sql_query)){
            $this->bindQueryParams($statement, $sql_param_type, $param_array);
            $statement->execute();
            $insert_id = $statement->insert_id;
            return $insert_id;
        }
    }

    function execute(){
        //pass
    }

    function update($sql_query, $sql_param_type="", $param_array=array()){
        if ($statement = $this->connection->prepare($sql_query)){
            if (!empty($sql_param_type) and !(empty($param_array))){
                $this->bindQueryParams($statement, $sql_param_type, $param_array);
                $statement->execute();
                $statement->store_result();
                return $statement->affected_rows;
            }
        }
        return 0;
    }

    function bindQueryParams($statement, $sql_param_type, $param_array=array()){
        $param_value_reference[] = & $sql_param_type;
        for ($i = 0; $i < count($param_array); $i++){
            $param_value_reference[] = & $param_array[$i];
        }

        call_user_func_array(array($statement, 'bind_param'), $param_value_reference);
    }
}
