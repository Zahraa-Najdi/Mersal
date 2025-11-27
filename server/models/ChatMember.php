<?php
require_once __DIR__ . '/Model.php';
class ChatMember extends Model
{
    private int $id;
    private int $chat_id;
    private int $user_id;
    private string $created_at;
    private string $updated_at;

    protected static string $table = "chat_members";

    public function __construct(array $data)
    {
        $this->id = $data["id"];
        $this->chat_id = $data["chat_id"];
        $this->user_id = $data["user_id"];
        $this->created_at = $data["created_at"];
        $this->updated_at = $data["updated_at"];
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

    public function getUser_id(): string
    {
        return $this->user_id;
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

    public function setchat_id(string $chat_id): self
    {
        $this->chat_id = $chat_id;
        return $this;
    }

    public function setUser_id(int $user_id): self
    {
        $this->user_id = $user_id;
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
    public static function getAllChats(mysqli $connection,int $userId): array
    {
        $sql = "
        SELECT  c.id AS chat_id,
                u.id AS other_user_id,
                u.name AS other_user_name,
                MAX(m.created_at) AS last_message_at
        FROM    chat_members cm
        JOIN    chats        c  ON c.id   = cm.chat_id
        JOIN    chat_members op  ON op.chat_id = c.id AND op.user_id <> cm.user_id
        JOIN    users        u  ON u.id   = op.user_id
        LEFT JOIN messages   m  ON m.chat_id = c.id
        WHERE   cm.user_id = ?
          AND   c.is_group = 0
        GROUP BY c.id
        ORDER BY last_message_at DESC
    ";

        $query = $connection->prepare($sql);
        $query->bind_param('i', $userId);
        $query->execute();
        $data = $query->get_result();

        $list = [];
        while ($row = $data->fetch_assoc()) {
            $list[] = [
                'chatId' => (int) $row['chat_id'],
                'otherUserId' => (int) $row['other_user_id'],
                'name' => $row['other_user_name'],
                'lastMessageAt' => $row['last_message_at']
            ];
        }
        return $list;
    }







    public function __toString()
    {
        return $this->id . " | " . $this->chat_id . " | " . $this->user_id . " | " . $this->updated_at . " | " . $this->created_at;
    }

    public function toArray()
    {
        return ["id" => $this->id, "chat_id" => $this->chat_id, "user_id" => $this->user_id, "created_at" => $this->created_at, "updated_at" => $this->updated_at];
    }

}


?>