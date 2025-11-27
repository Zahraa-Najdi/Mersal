<?php
require_once __DIR__ . '/Model.php';
class Chat extends Model
{
    private int $id;
    private int $is_group;
    private string $group_name;
    private string $created_at;
    private string $updated_at;
    

    protected static string $table = "chats";

    public function __construct(array $data)
    {
        $this->id = $data["id"];
        $this->is_group = $data["is_group"];
        $this->group_name = $data["group_name"];
        $this->created_at = $data["created_at"];
        $this->updated_at = $data["updated_at"];
    }
    public function getId(): int
    {
        return $this->id;
    }

    public function getis_group(): string
    {
        return $this->is_group;
    }

    public function getgroup_name(): string
    {
        return $this->group_name;
    }

    public function getcreated_at(): string
    {
        return $this->created_at;
    }
    public function getupdated_at(): string
    {
        return $this->updated_at;
    }




    // Setters
    public function setId(int $id): self
    {
        $this->id = $id;
        return $this;
    }

    public function setis_group(string $is_group): self
    {
        $this->is_group = $is_group;
        return $this;
    }

    public function setgroup_name(int $group_name): self
    {
        $this->group_name = $group_name;
        return $this;
    }

    public function setcreated_at(string $created_at): self
    {
        $this->created_at = $created_at;
        return $this;
    }
    public function setupdated_at(string $updated_at): self
    {
        $this->updated_at = $updated_at;
        return $this;
    }








    public function __toString()
    {
        return $this->id . " | " . $this->is_group . " | " . $this->group_name . " | "  . $this->created_at ." | "  . $this->updated_at;
    }

    public function toArray()
    {
        return ["id" => $this->id, "is_group" => $this->is_group, "group_name" => $this->group_name, "created_at" => $this->created_at, "updated_at" => $this->updated_at];
    }

}


?>