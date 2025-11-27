<?php
abstract class Model
{
    public function __construct($data)
    {
    }

    protected static string $table;

    public static function create(mysqli $connection, array $data)
    {
        $columns = implode(", ", array_keys($data));
        $placeholders = str_repeat("?,", count($data) - 1) . "?";

        $sql = sprintf("INSERT INTO %s (%s) VALUES (%s)", static::$table, $columns, $placeholders);
        $query = $connection->prepare($sql); // prepares sql for execution
        $types = self::getAllTypes($data); //gets all variable(attributes) types from the static function
        $params = []; //empty array to hold references to the values of $data
        foreach ($data as $key => &$value) { //loops for each value in data
            $params[] =& $value; // takes the values and puts them in the array
        }


        //call_user_func_array: calls a function with an array of arguments
        //bind_param: replaces ?? with actual values
        //array_merge: combines $types + $params
        call_user_func_array(array($query, "bind_param"), array_merge(array($types), $params)); 
        $query->execute(); //runs sql query with all the values (saves to the db) 
        if ($connection->errno == 1062) //MySQL error means you tried inserting a duplicate value in a UNIQUE column
            return "Duplicate"; //so it returns "Duplicate" instead of crashing
        return $connection->insert_id; //if else->returns the ID of the new row

    }

    public function update(mysqli $connection, array $data, $primary_key)
    {
        $data['updated_at'] = date('Y-m-d H:i:s');

        $updates = "";
        $i = 0;
        $id = $this->getId();

        foreach ($data as $key => &$value) {
            $updates .= ($i === count($data) - 1)
                ? "`$key` = ?"
                : "`$key` = ?, ";
            $i++;
        }

        $sql = sprintf("UPDATE %s SET %s WHERE %s = ?", static::$table, $updates, $primary_key);
        $query = $connection->prepare($sql);

        if (!$query) {
            throw new RuntimeException(
                'Prepare failed: ' . $connection->error . '  SQL: ' . $sql
            );
        }


        $types = self::getAllTypes($data);
        $types .= "i";

        $params = [];
        foreach ($data as &$value) {
            $params[] = &$value;
        }
        $params[] = &$id;

        call_user_func_array([$query, "bind_param"], array_merge([$types], $params));
        $query->execute();

        if ($connection->errno == 1062) {
            return "Duplicate";
        }

        return $id;
    }

    public static function findAll(mysqli $connection)//selects all values from attribute x
    {
        $sql = sprintf("SELECT * FROM %s", static::$table); //%s is an attribute

        $query = $connection->prepare($sql);
        $query->execute();

        $result = $query->get_result();
        $objects = [];

        while ($row = $result->fetch_assoc()) {
            $objects[] = new static($row);
        }

        return $objects;
    }

    //read, find all by FK, ex: get all messages in a chat. chat_id is the FK:
    public static function findAllByOtherId(mysqli $connection, $id, $primary_key)
    {
        $sql = sprintf("SELECT * FROM %s WHERE %s = ? ", static::$table, $primary_key);

        $query = $connection->prepare($sql);
        $query->bind_param("i", $id);
        $query->execute();

        $result = $query->get_result();
        $objects = [];

        while ($row = $result->fetch_assoc()) {
            $objects[] = new static($row);
        }

        return $objects;
    }

    public static function find(mysqli $connection, int $id, $primary_key)//read, finds one row in a table
    {
        $sql = sprintf(
            "SELECT * from %s WHERE %s = ?", //find all from table where row(PK)=x
            static::$table,
            $primary_key
        );

        $query = $connection->prepare($sql);
        $query->bind_param("i", $id);
        $query->execute();

        $data = $query->get_result()->fetch_assoc();

        return $data ? new static($data) : null;
    }

    public static function deleteById($id, mysqli $connection, $primary_key)//delete
    {
        $sql = sprintf("DELETE FROM %s WHERE %s = ?", static::$table, $primary_key);
        $query = $connection->prepare($sql);
        $query->bind_param("i", $id);
        $query->execute();
        return true;
    }


    public static function getAllTypes($data) 
    {
        $types = "";
        foreach ($data as $key => $value) {
            if (gettype($value) == "string") {
                $types .= "s";
            } elseif (gettype($value) == "integer") {
                $types .= "i";
            } elseif (gettype($value) == "float" || gettype($value) == "double") {
                $types .= "d";
            }
        }

        return $types;
    }


}


?>