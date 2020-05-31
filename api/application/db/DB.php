<?php

class DB {
    function __construct() {
        $host = 'localhost';
        $user = 'root';
        $password = '';
        $db = 'pixo';
        $this->conn = new mysqli($host, $user, $password, $db);
        if ($this->conn->connect_errno) {
            printf("Не удалось подключиться: %s\n", $conn->connect_error);
            exit();
        }
    }

    function __destruct() {
        $this->conn->close();
    }

    public function getUserByLogin($login) {
        $query = 'SELECT * FROM users WHERE login="' . $login . '"';
        $result = $this->conn->query($query);
        return $result->fetch_object();
    }

    public function getUserByLoginPassword($login, $password) {
        $query = 'SELECT * 
                  FROM users 
                  WHERE login="' . $login . '" AND password="'.$password.'"';
        $result = $this->conn->query($query);
        return $result->fetch_object();
    }

    public function setUserByLoginPassword($login, $password) {
        $query = "INSERT INTO users (login, password) 
                  VALUES ('" . $login . "', '" . $password . "')";
        $this->conn->query($query);
        return true;
    }

    public function getUserByToken($token) {
        $query = 'SELECT * FROM users WHERE token="'.$token.'"';
        $result = $this->conn->query($query);
        return $result->fetch_object();
    }

    public function updateToken($id, $token) {
        $query = 'UPDATE users SET token="'.$token.'" WHERE id='.$id;
        $this->conn->query($query);
        return true;
    }

    public function getFreeUsers($userId) {
        $query = "SELECT id, login 
                  FROM users 
                  WHERE 
                    token<>'' AND
                    id NOT IN (SELECT u.id 
                            FROM 
                                parties AS p, 
                                users AS u
                            WHERE 
                                p.status = 'open' AND
                                (p.user1_id = u.id OR
                                 p.user2_id = u.id)) AND
                    id<>" . $userId;
        $result = $this->conn->query($query);
        $res = array();
        while ($obj = $result->fetch_object()) {
            $res[] = $obj;
        }
        return $res;
    }

    public function deleteOpenParties($userId) {
        $query = 'DELETE FROM parties 
                  WHERE user1_id='.$userId.' AND (status="open" OR status="game")';
        $this->conn->query($query);
        return true;
    }

    public function newParty($userId1, $userId2) {
        $query = 'INSERT INTO parties (user1_id, user2_id, status, turn) 
                  VALUES ('.$userId1.', '.$userId2.', "open", '.$userId1.')';
        $this->conn->query($query);
        return true;
    }

    public function isParty($userId) {
        $query = 'SELECT * FROM parties WHERE user2_id = '.$userId. ' AND status = "open"';
        $result = $this->conn->query($query);
        return $result->fetch_object();
    }

    public function acceptParty($userId, $status) {
        $query = 'UPDATE parties 
                  SET status = "'.$status.'" 
                  WHERE user2_id = ' .$userId. ' AND status = "open"';
        $this->conn->query($query);
        return true;
    }

    public function isAcceptParty($userId) {
        $query = 'SELECT * 
                  FROM parties 
                  WHERE user1_id='.$userId.' AND status="close" OR status="game"';
        $result = $this->conn->query($query);
        return $result->fetch_object();
    }

    public function updateGame($partyId, $field, $hash) {
        $query = "UPDATE parties 
                  SET game='".$field."', hash='".$hash."' 
                  WHERE id=".$partyId;
        $this->conn->query($query);
        return true;
    }

    public function getPartyByUser2Id($userId) {
        $query = 'SELECT * FROM parties WHERE user2_id='.$userId.' AND status="game"';
        $result = $this->conn->query($query);
        return $result->fetch_object();
    }

    public function getPartyByUserId($userId, $status) {
        $query = 'SELECT * 
                  FROM parties 
                  WHERE 
                    (user1_id='.$userId.' OR user2_id='.$userId.') AND 
                    status="'.$status.'" 
                    ORDER BY id DESC 
                    LIMIT 1';
        $result = $this->conn->query($query);
        return $result->fetch_object();
    }

    public function updateTurn($partyId, $turn) {
        $query = "UPDATE parties
                  SET turn= " .$turn. " 
                  WHERE id=" .$partyId;
        $this->conn->query($query);
        return true;
    }

    public function getGamerById($id) {
        $query = "SELECT id, login FROM users WHERE id=" .$id;
        $result = $this->conn->query($query);
        return $result->fetch_object();
    }
}