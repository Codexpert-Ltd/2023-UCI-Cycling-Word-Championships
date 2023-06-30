<?php
namespace Src;
class Post {
    private $db;
    private $requestMethod;
    private $postId;
    public function __construct($db, $requestMethod, $postId) {
        $this->db = $db;
        $this->requestMethod = $requestMethod;
        $this->postId = $postId;
    }
    public function processRequest() {
        switch ($this->requestMethod) {
            case 'GET':
                if ($this->postId) {
                    $response = $this->getPost($this->postId);
                } else {
                    $response = $this->getAllPosts();
                };
            break;
            case 'POST':
                $response = $this->createPost();
            break;
            case 'PUT':
                $response = $this->updatePost($this->postId);
            break;
            case 'DELETE':
                $response = $this->deletePost($this->postId);
            break;
            default:
                $response = $this->notFoundResponse();
            break;
        }
        header($response['status_code_header']);
        if ($response['body']) {
            echo $response['body'];
        }
    }
    private function getAllPosts() {
        $query = "
      SELECT
        id, Corridor, time, data, created_at
      FROM
      uci_data;
    ";
        try {
            $statement = $this->db->query($query);
            $result = $statement->fetchAll(\PDO::FETCH_ASSOC);
        }
        catch(\PDOException $e) {
            exit($e->getMessage());
        }
        $response['status_code_header'] = 'HTTP/1.1 200 OK';
        $response['body'] = json_encode($result);
        return $response;
    }
    private function getPost($id) {
        $result = $this->find($id);
        if (!$result) {
            return $this->notFoundResponse();
        }
        $response['status_code_header'] = 'HTTP/1.1 200 OK';
        $response['body'] = json_encode($result);
        return $response;
    }
    private function createPost() {
        $input = (array)json_decode(file_get_contents('php://input'), TRUE);
        if (!$this->validatePost($input)) {
            return $this->unprocessableEntityResponse();
        }
        $query = "
      INSERT INTO uci_data
        (Corridor, time, data)
      VALUES
        (:Corridor, :time, :data);
    ";
        try {
            $statement = $this->db->prepare($query);
            $statement->execute(array('Corridor' => $input['Corridor'], 'time' => $input['time'], 'data' => $input['data'],));
            $statement->rowCount();
        }
        catch(\PDOException $e) {
            exit($e->getMessage());
        }
        $response['status_code_header'] = 'HTTP/1.1 201 Created';
        $response['body'] = json_encode(array('message' => 'Corridor Post Created'));
        return $response;
    }
    private function updatePost($id) {
        $result = $this->find($id);
        if (!$result) {
            return $this->notFoundResponse();
        }
        $input = (array)json_decode(file_get_contents('php://input'), TRUE);
        if (!$this->validatePost($input)) {
            return $this->unprocessableEntityResponse();
        }
        $statement = "
      UPDATE uci_data
      SET
      Corridor = :Corridor,
      time  = :time,
      data = :data
      WHERE id = :id;
    ";
        try {
            $statement = $this->db->prepare($statement);
            $statement->execute(array('id' => (int)$id, 'Corridor' => $input['Corridor'], 'time' => $input['time'], 'data' => $input['data'],));
            $statement->rowCount();
        }
        catch(\PDOException $e) {
            exit($e->getMessage());
        }
        $response['status_code_header'] = 'HTTP/1.1 200 OK';
        $response['body'] = json_encode(array('message' => 'Corridor Post Updated!'));
        return $response;
    }
    private function deletePost($id) {
        $result = $this->find($id);
        if (!$result) {
            return $this->notFoundResponse();
        }
        $query = "
      DELETE FROM uci_data
      WHERE id = :id;
    ";
        try {
            $statement = $this->db->prepare($query);
            $statement->execute(array('id' => $id));
            $statement->rowCount();
        }
        catch(\PDOException $e) {
            exit($e->getMessage());
        }
        $response['status_code_header'] = 'HTTP/1.1 200 OK';
        $response['body'] = json_encode(array('message' => 'Post Deleted!'));
        return $response;
    }
    public function find($id) {
        $query = "
      SELECT
      id, Corridor, time, data, created_at
      FROM
      uci_data
      WHERE id = :id;
    ";
        try {
            $statement = $this->db->prepare($query);
            $statement->execute(array('id' => $id));
            $result = $statement->fetch(\PDO::FETCH_ASSOC);
            return $result;
        }
        catch(\PDOException $e) {
            exit($e->getMessage());
        }
    }
    private function validatePost($input) {
        if (!isset($input['Corridor'])) {
            return false;
        }
        if (!isset($input['time'])) {
            return false;
        }
        return true;
    }
    private function unprocessableEntityResponse() {
        $response['status_code_header'] = 'HTTP/1.1 422 Unprocessable Entity';
        $response['body'] = json_encode(['error' => 'Invalid input']);
        return $response;
    }
    private function notFoundResponse() {
        $response['status_code_header'] = 'HTTP/1.1 404 Not Found';
        $response['body'] = null;
        return $response;
    }
}
