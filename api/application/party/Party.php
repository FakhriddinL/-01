<?php

class Party {

    function __construct($db) {
        $this->db = $db;
    }

    public function getFreeUsers($userId) {
        return $this->db->getFreeUsers($userId);
    }

    public function newParty($userId1, $userId2) {
        $this->db->deleteOpenParties($userId1); // удалить все открытые вызовы $userId1
        $this->db->newParty($userId1, $userId2); // добавить новый вызов $userId1
        return true;
    }

    public function isParty($userId) {
        return !!($this->db->isParty($userId));
    }

    public function acceptParty($userId, $answer) {
        $status = ($answer === 'yes') ? 'game' : 'close';
        return $this->db->acceptParty($userId, $status);
    }

    public function isAcceptParty($userId) {
        return !!($this->db->isAcceptParty($userId));
    }

    public function getPartyByUser2Id($userId) {
        return $this->db->getPartyByUser2Id($userId);
    }

    public function getPartyByUserId($userId, $status) {
        return $this->db->getPartyByUserId($userId, $status);
    }
}