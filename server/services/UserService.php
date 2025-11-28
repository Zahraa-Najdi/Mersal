<?php
require_once(__DIR__ . "/../models/User.php");
require_once(__DIR__ . "/ServiceHelper.php");

class UserService
{
    private mysqli $connection;

    public function __construct(mysqli $connection)
    {
        $this->connection = $connection;
    }
//CRUD functions are here
    public function getUsers($id)
    {
        try {
            if ($id) {
                $user = User::find($this->connection, $id, "id");
                if ($user) {
                    return ['status' => 200, 'data' => $user->toArray()];
                }
                return ['status' => 404, 'data' => ['error' => 'user not found']];
            }

            $users = User::findAll($this->connection);
            $data = array_map(fn($user) => $user->toArray(), $users);
            return ['status' => 200, 'data' => $data];
        } catch (Exception $e) {
            return ['status' => 500, 'data' => ['error' => 'Database error occurred while getting user: ' . $e->getMessage()]];
        }
    }

    public function getUserByEmail($email, $password)
    {
        try {
            if ($email && $password) {
                $user = User::getUserByEmail($this->connection, $email, $password);
                if ($user) {
                    return ['status' => 200, 'data' => $user];
                }
                return ['status' => 404, 'data' => ['error' => 'user not found']];
            }
        } catch (Exception $e) {
            return ['status' => 500, 'data' => ['error' => 'Database error occurred while getting user by email: ' . $e->getMessage()]];
        }
    }

    public function createUser(array $data)
    {
        try {
            $validationResult = ServiceHelper::validateRequiredFields($data, $this->getRequiredFields());
            if ($validationResult !== true) {
                return $validationResult;
            }

            $data["password"] = password_hash($data["password"], PASSWORD_DEFAULT);
            

            $userId = User::create($this->connection, $data);
            if ($userId == "Duplicate") {
                return [
                    'status' => 500,
                    'data' => [
                        'message' => 'Duplicated Email'
                    ]
                ];
            }
            if ($userId) {
                return [
                    'status' => 201,
                    'data' => [
                        'message' => 'user created successfully',
                        'id' => $userId
                    ]
                ];
            }

            return ['status' => 500, 'data' => ['error' => 'Failed to create user']];
        } catch (Exception $e) {
            return ['status' => 500, 'data' => ['error' => 'Database error occurred while creating user: ' . $e->getMessage()]];
        }
    }

    public function updateUser(int $id, array $data)
    {
        try {
            $user = User::find($this->connection, $id, "id");
            if (!$user) {
                return ['status' => 404, 'data' => ['error' => 'user not found']];
            }

            if (empty($data)) {
                return ['status' => 400, 'data' => ['error' => 'No data provided for update']];
            }

            $data["password"] = password_hash($data["password"], PASSWORD_DEFAULT);

            $result = $user->update($this->connection, $data, "id");
            if ($result == "Duplicate") {
                return ['status' => 500, 'data' => ['message' => 'Email already in use']];
            }

            if ($result) {
                return ['status' => 200, 'data' => ['message' => 'user updated successfully']];
            }

            return ['status' => 500, 'data' => ['error' => 'Failed to update user']];
        } catch (Exception $e) {
            return ['status' => 500, 'data' => ['error' => 'Database error occurred while updating user: ' . $e->getMessage()]];
        }
    }

    public function deleteUser(int $id)
    {
        try {
            $user = User::find($this->connection, $id, "id");
            if (!$user) {
                return ['status' => 404, 'data' => ['error' => 'user not found']];
            }

            $result = User::deleteById($id, $this->connection, "id");
            if ($result) {
                return ['status' => 200, 'data' => ['message' => 'user deleted successfully']];
            }

            return ['status' => 500, 'data' => ['error' => 'Failed to delete user']];
        } catch (Exception $e) {
            return ['status' => 500, 'data' => ['error' => 'Database error occurred while deleting user: ' . $e->getMessage()]];
        }
    }



    private function getRequiredFields()
    {
        return ['name', 'email', 'password' ];
    }
}
