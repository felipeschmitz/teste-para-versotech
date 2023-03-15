<?php

class Connection {

    private $databaseFile;
    private $connection;

    public function __construct()
    {
        $this->databaseFile = realpath(__DIR__ . "/database/db.sqlite");
        $this->connect();
    }

    private function connect()
    {
        return $this->connection = new PDO("sqlite:{$this->databaseFile}");
    }

    public function getConnection()
    {
        return $this->connection ?: $this->connection = $this->connect();
    }

    public function query($query)
    {
        $result      = $this->getConnection()->query($query);

        $result->setFetchMode(PDO::FETCH_INTO, new stdClass);

        return $result;
    }

    // faz o get do usuário com o "join" das cores
    public function getUserById($id)
    {
        $prepare = $this->getConnection()->prepare("SELECT * FROM users WHERE users.id = :id");
        $prepare->execute([
            ':id' => $id,
        ]);

        $prepare->setFetchMode(PDO::FETCH_INTO, new stdClass);

        $user = $prepare->fetch();

        $prepare = $this->getConnection()->prepare("SELECT * FROM user_colors WHERE user_id = :userId");
        $prepare->execute([
            ':userId' => $user->id,
        ]);

        $loopColors = $prepare->fetchAll();

        $colors = [];
        foreach ($loopColors as $item) {
            $colors[] = $item['color_id'];
        }

        return (object) array_merge((array) $user, ['colors' => (array) $colors]);
    }

    // cria o novo usuário com o relacionamento das cores (se houver)
    public function storeUser($data)
    {
        $prepare = $this->getConnection()->prepare("INSERT INTO users (name, email) VALUES (:name, :email)");
        $prepare->execute([
            ':name' => $data['name'],
            ':email' => $data['email'],
        ]);

        $id = $this->getConnection()->lastInsertId();

        if (isset($data['color'])) {
            foreach ($data['color'] as $colorId) {
                $prepare = $this->getConnection()->prepare("INSERT INTO user_colors (user_id, color_id) VALUES (:userId, :colorId)");
    
                $prepare->execute([
                    ':userId' => $id,
                    ':colorId' => $colorId,
                ]);
            }
        }

        return $id;
    }

    // atualiza o usuário com o seu relacionamento de corew (se houver)
    public function updateUser($data)
    {
        $prepare = $this->getConnection()->prepare("UPDATE users SET name = :name, email = :email WHERE id = :id");
        $prepare->execute([
            ':id' => $data['id'],
            ':name' => $data['name'],
            ':email' => $data['email'],
        ]);

        if (isset($data['color'])) {
            $prepare = $this->getConnection()->prepare("DELETE FROM user_colors WHERE user_id = :userId");
            $prepare->execute([
                ':userId' => $data['id'],
            ]);

            foreach ($data['color'] as $colorId) {
                $prepare = $this->getConnection()->prepare("INSERT INTO user_colors (user_id, color_id) VALUES (:userId, :colorId)");
    
                $prepare->execute([
                    ':userId' => $data['id'],
                    ':colorId' => $colorId,
                ]);
            }
        }
    }

    // remove o usuário e seu relacionamento de cores (se houver)
    public function destroyUser($data)
    {
        $prepare = $this->getConnection()->prepare("DELETE FROM users WHERE id = :id");
        $prepare->execute([
            ':id' => $data['id'],
        ]);

        $prepare = $this->getConnection()->prepare("DELETE FROM user_colors WHERE user_id = :userId");
        $prepare->execute([
            ':userId' => $data['id'],
        ]);
    }

    // faz o get da cor
    public function getColorById($id)
    {
        $prepare = $this->getConnection()->prepare("SELECT * FROM colors WHERE id = :id");
        $prepare->execute(
            [':id' => $id],
        );

        $prepare->setFetchMode(PDO::FETCH_INTO, new stdClass);

        return $prepare->fetch();
    }

    // cria a nova cor
    public function storeColor($data)
    {
        $prepare = $this->getConnection()->prepare("INSERT INTO colors (name) VALUES (:name)");
        $prepare->execute([
            ':name' => $data['name'],
        ]);

        return $this->getConnection()->lastInsertId();
    }

    // atualiza a cor
    public function updateColor($data)
    {
        $prepare = $this->getConnection()->prepare("UPDATE colors SET name = :name WHERE id = :id");
        $prepare->execute([
            ':id' => $data['id'],
            ':name' => $data['name'],
        ]);
    }

    // remove a cor
    public function destroyColor($data)
    {
        $prepare = $this->getConnection()->prepare("DELETE FROM colors WHERE id = :id");
        $prepare->execute([
            ':id' => $data['id'],
        ]);
    }
}