<?php
require_once(__DIR__ . "/../models/Message.php");
require_once(__DIR__ . "/../models/User.php");
require_once(__DIR__ . "/ServiceHelper.php");

class MessageService
{
    private mysqli $connection;

    public function __construct(mysqli $connection)
    {
        $this->connection = $connection;
    }

    public function getMessageById(int $id)
    {
        try {
            $message = Message::find($this->connection, $id, 'id');
            return $message
                ? ['status' => 200, 'data' => $message->toArray()]
                : ['status' => 404, 'data' => ['error' => 'message not found']];
        } catch (Exception $e) {
            return ['status' => 500, 'data' => ['error' => 'Database error occurred while getting message by id: ' . $e->getMessage()]];
        }
    }

    public function getMessageByChatIdAndStatus(int $chatId, string $status)
    {
        try {
            $messages = Message::findMessagesByChatIdAndStatus($this->connection, $chatId, $status);
            $data = array_map(fn($message) => $message->toArray(), $messages);
            return ['status' => 200, 'data' => $data];
        } catch (Exception $e) {
            return ['status' => 500, 'data' => ['error' => 'Database error occurred while getting message by chat ID and status: ' . $e->getMessage()]];
        }
    }

    public function getMessagesByChatId(int $chatId, int $receiverId, string $status)
    {
        try {
            $validationResult = ServiceHelper::validateIdExists($this->connection, $chatId);
            if (isset($validationResult['status']) && $validationResult['status'] !== 200) {
                return $validationResult;
            }

            $messages = Message::findAllByOtherId($this->connection, $chatId, 'chat_id');
            $eligibleMessages = $this->filterEligibleMessages($messages, $receiverId, $status);
            $this->updateMessageStatuses($eligibleMessages, $status);

            $data = array_map(fn($m) => $m->toArray(), $messages);
            return ['status' => 200, 'data' => $data];
        } catch (Exception $e) {
            return ['status' => 500, 'data' => ['error' => 'Database error occurred while getting messages by chat ID: ' . $e->getMessage()]];
        }
    }

    public function getAllMessages()
    {
        try {
            $messages = Message::findAll($this->connection);
            $data = array_map(fn($message) => $message->toArray(), $messages);
            return ['status' => 200, 'data' => $data];
        } catch (Exception $e) {
            return ['status' => 500, 'data' => ['error' => 'Database error occurred while getting all messages: ' . $e->getMessage()]];
        }
    }

    public function createMessage(array $data)
    {
        try {
            $validationResult = ServiceHelper::validateRequiredFields($data, $this->getRequiredFields());

            if ($validationResult !== true) {
                return $validationResult;
            }

            $messageId = Message::create($this->connection, $data);
            if ($messageId === "Duplicate") {
                return [
                    'status' => 500,
                    'data' => ['message' => 'duplicate message name']
                ];
            }
            if ($messageId) {
                return [
                    'status' => 201,
                    'data' => [
                        'message' => 'message created successfully',
                        'id' => $messageId
                    ]
                ];
            }

            return ['status' => 500, 'data' => ['error' => 'Failed to create message']];
        } catch (Exception $e) {
            return ['status' => 500, 'data' => ['error' => 'Database error occurred while creating message: ' . $e->getMessage()]];
        }
    }

    public function updateMessage(int $id, array $data)
    {
        try {
            $message = Message::find($this->connection, $id, "id");
            if (!$message) {
                return ['status' => 404, 'data' => ['error' => 'message not found']];
            }

            if (empty($data)) {
                return ['status' => 400, 'data' => ['error' => 'No data provided for update']];
            }

            $result = $message->update($this->connection, $data, "id");
            if ($result === "Duplicate") {
                return ['status' => 500, 'data' => ['message' => 'message name already exists']];
            }

            if ($result) {
                return ['status' => 200, 'data' => ['message' => 'message updated successfully']];
            }

            return ['status' => 500, 'data' => ['error' => 'Failed to update message']];
        } catch (Exception $e) {
            return ['status' => 500, 'data' => ['error' => 'Database error occurred while updating message: ' . $e->getMessage()]];
        }
    }

    public function deleteMessage(int $id)
    {
        try {
            $message = Message::find($this->connection, $id, "id");
            if (!$message) {
                return ['status' => 404, 'data' => ['error' => 'message not found']];
            }

            $result = Message::deleteById($id, $this->connection, "id");
            if ($result) {
                return ['status' => 200, 'data' => ['message' => 'message deleted successfully']];
            }

            return ['status' => 500, 'data' => ['error' => 'Failed to delete message']];
        } catch (Exception $e) {
            return ['status' => 500, 'data' => ['error' => 'Database error occurred while deleting message: ' . $e->getMessage()]];
        }
    }


    private function filterEligibleMessages(array $messages, int $receiverId, string $status): array
    {
        $expectedStatus = $status === 'delivered' ? 'sent' : 'delivered';
        return array_filter(
            $messages,
            fn(Message $m) => $m->getStatus() === $expectedStatus && $m->getreceiver_id() == $receiverId
        );
    }

    private function updateMessageStatuses(array $messages, string $status): void
    {
        if (empty($messages))
            return;

        $now = date('Y-m-d H:i:s');
        foreach ($messages as $message) {
            $message->setstatus($status);

            if ($status === 'delivered') {
                $message->setdelivered_at($now);
            } elseif ($status === 'read') {
                $message->setread_at($now);
            }

            $message->update($this->connection, $message->toArray(), 'id');
        }
    }

    private function getRequiredFields(): array
    {
        return ['chat_id', 'sender_id', 'receiver_id', 'message'];
    }
}