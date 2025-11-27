<?php
require_once __DIR__ . '/Model.php';
class Message extends Model
{
    private int $id;
    private int $chat_id;
    private int $sender_id;
    private int $receiver_id;
    private string $message;
    private string $delivered_at;
    private string $read_at;
    private string $created_at;
    private string $updated_at;
    private string $status;

    protected static string $table = "messages";

    public function __construct(array $data)
    {
        $this->id = $data["id"];
        $this->chat_id = $data["chat_id"];
        $this->sender_id = $data["sender_id"];
        $this->receiver_id = $data["receiver_id"];
        $this->message = $data["message"];
        $this->delivered_at = $data["delivered_at"];
        $this->read_at = $data["read_at"];
        $this->created_at = $data["created_at"];
        $this->updated_at = $data["updated_at"];
        $this->status = $data["status"];
    }

    // Getters
    public function getId(): int
    {
        return $this->id;
    }

    public function getchat_id(): string
    {
        return $this->chat_id;
    }

    public function getsender_id(): string
    {
        return $this->sender_id;
    }
    public function getreceiver_id(): string
    {
        return $this->receiver_id;
    }

    public function getmessage(): string
    {
        return $this->message;
    }

    public function getdelivered_at(): string
    {
        return $this->delivered_at;
    }
    public function getread_at(): string
    {
        return $this->read_at;
    }
    public function getcreated_at(): string
    {
        return $this->created_at;
    }
    public function getupdated_at(): string
    {
        return $this->updated_at;
    }
    public function getstatus(): string
    {
        return $this->status;
    }



    // Setters
    public function setId(int $id): self
    {
        $this->id = $id;
        return $this;
    }

    public function setchat_id(int $chat_id): self
    {
        $this->chat_id = $chat_id;
        return $this;
    }

    public function setsender_id(int $sender_id): self
    {
        $this->sender_id = $sender_id;
        return $this;
    }
    public function setreceiver_id(int $receiver_id): self
    {
        $this->receiver_id = $receiver_id;
        return $this;
    }

    public function setmessage(string $message): self
    {
        $this->message = $message;
        return $this;
    }

    public function setdelivered_at(string $delivered_at): self
    {
        $this->delivered_at = $delivered_at;
        return $this;
    }
    public function setupdated_at(string $updated_at): self
    {
        $this->updated_at = $updated_at;
        return $this;
    }
    public function setread_at(string $read_at): self
    {
        $this->read_at = $read_at;
        return $this;
    }
    public function setcreated_at(string $created_at): self
    {
        $this->created_at = $created_at;
        return $this;
    }
    public function setstatus(string $status): self
    {
        $this->status = $status;
        return $this;
    }


    public static function findMessagesByChatIdAndStatus(mysqli $connection, $chatId, $status)
    {
        $sql = "SELECT * FROM messages WHERE chat_id = ? AND status = ?";

        $query = $connection->prepare($sql);
        $query->bind_param("is", $chatId, $status);
        $query->execute();

        $result = $query->get_result();
        $objects = [];

        while ($row = $result->fetch_assoc()) {
            $objects[] = new static($row);
        }

        return $objects;
    }






    public function __toString()
    {
        return $this->id . " | " . $this->chat_id . " | " . $this->sender_id . " | " . $this->status . " | " . $this->delivered_at . " | " . $this->read_at . $this->updated_at . " | " . $this->created_at . " | " . $this->message;
    }

    public function toArray()
    {
        return [
            "id" => $this->id,
            "chat_id" => $this->chat_id,
            "status" => $this->status,
            "sender_id" => $this->sender_id,
            "receiver_id" => $this->receiver_id,
            "message" => $this->message,
            "delivered_at" => $this->delivered_at,
            "created_at" => $this->created_at,
            "read_at" => $this->read_at,
            "updated_at" => $this->updated_at
        ];
    }

}


?>