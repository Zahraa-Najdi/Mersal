<?php
require_once __DIR__ . '/Model.php';

class User extends Model
{
    private int $id;
    private string $name;
    private string $email;
    private string $password;
    private string $created_at;
    private string $updated_at;

    protected static string $table = "users";

    public function __construct(array $data)
    {
        $this->id = $data["id"];
        $this->name = $data["name"];
        $this->email = $data["email"];
        $this->password = $data["password"];
        $this->created_at = $data["created_at"];
        $this->updated_at = $data["updated_at"];
    }

    // Getters
    public function getId(): int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function getPassword(): string
    {
        return $this->password;
    }
    public function getCreated_at(): string
    {
        return $this->created_at;
    }

    public function getUpdated_at(): string
    {
        return $this->updated_at;
    }

   

    // Setters
    public function setId(int $id): self
    {
        $this->id = $id;
        return $this;
    }

    public function setName(string $name): self
    {
        $this->name = $name;
        return $this;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;
        return $this;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;
        return $this;
    }
    public function setCreated_at(string $created_at): self
    {
        $this->created_at = $created_at;
        return $this;
    }
    public function setUpdated_at(string $updated_at): self
    {
        $this->updated_at = $updated_at;
        return $this;
    }




    public static function getUserByEmail(mysqli $connection, $email, $password)
    {
        $sql = "SELECT * from users WHERE email = ?";
        $query = $connection->prepare($sql);
        $query->bind_param("s", $email);
        $query->execute();

        $user = $query->get_result()->fetch_assoc();
        if (!$user)
            return false;
        if (!password_verify($password, $user["password"]))
            return false;
        unset($user["password"]);
        return $user;

    }




    public function __toString()
    {
        return $this->id . " | " . $this->name . " | " . $this->email . " | " . $this->created_at . " | " . $this->updated_at;
    }

    public function toArray()
    {
        return ["id" => $this->id, "name" => $this->name, "email" => $this->email, "password" => $this->password, "updated_at" => $this->updated_at, "created_at" => $this->created_at];
    }

}


?>