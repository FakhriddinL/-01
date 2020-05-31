<?php

require_once('db/DB.php');
require_once('user/User.php');
require_once('party/Party.php');
require_once('piXO/PiXO.php');

class Application {

    function __construct() {
        $db = new DB();
        $this->user = new User($db);
        $this->party = new Party($db);
        $this->piXO = new PiXO($db);
    }

    /**************/
    /* ABOUT USER */
    /**************/
    public function login($params) {
        if ($params['login'] && $params['password']) {
            return $this->user->login($params['login'], $params['password']);
        }
        return false;
    }

    public function logout($params) {
        if ($params['token']) {
            return $this->user->logout($params['token']);
        }
        return false;
    }

    public function registration($params) {
        if ($params['login'] && $params['password']) {
            return $this->user->registration($params['login'], $params['password']);
        }
        return false;
    }

    /***************/
    /* ABOUT PARTY */
    /***************/
    public function getFreeUsers($params) {
        if ($params['token']) {
            $user = $this->user->getUserByToken($params['token']);
            if ($user) {
                return $this->party->getFreeUsers($user->id);
            }
        }
        return false;
    }

    public function newParty($params) {
        print_r($params['id']);
        if ($params['token'] && $params['id']) {
            $user = $this->user->getUserByToken($params['token']);
            if ($user) {
                return $this->party->newParty($user->id, $params['id']);
            }
        }
        return false;
    }

    public function isParty($params) {
        if($params['token']) {
            $user = $this->user->getUserByToken($params['token']);
            if($user) {
                return $this->party->isParty($user->id);
            }
        }
        return false;
    }

    public function acceptParty($params) {
        if($params['token'] && $params['answer']) {
            $user = $this->user->getUserByToken($params['token']);
            if($user) {
                $this->party->acceptParty($user->id, $params['answer']);
                if ($params['answer'] === 'yes') {
                    // взять партию по id юзера
                    $party = $this->party->getPartyByUser2Id($user->id);
                    if ($party) {
                        $this->piXO->createGame($party->id);
                    }
                }
                return ($params['answer'] === 'yes');
            }
        }
        return false;
    }

    public function isAcceptParty($params) {
        if($params['token']) {
            $user = $this->user->getUserByToken($params['token']);
            if($user) {
                return $this->party->isAcceptParty($user->id);
            }
        }
        return false;
    }

    /**************/
    /* ABOUT GAME */
    /**************/
    public function turn($params) {
        if ($params['token'] && 
            intval($params['x']) >= 0 && 
            intval($params['y']) >= 0
        ) {
            $user = $this->user->getUserByToken($params['token']);
            if ($user) {
                $party = $this->party->getPartyByUserId($user->id, 'game');
                if ($party) {
                    return $this->piXO->turn(
                        $party, 
                        $user, 
                        intval($params['x']), 
                        intval($params['y'])
                    );
                }
            }
        }
        return false;
    }

    public function getGame($params) {
        if ($params['token'] && $params['hash']) {
            $user = $this->user->getUserByToken($params['token']);
            if($user) {
                // если игра играется
                $party = $this->party->getPartyByUserId($user->id, 'game');
                if ($party) {
                    return $this->piXO->getGame($party, $params['hash']);
                }
                // проверить, что игра м.б. завершена
                $party = $this->party->getPartyByUserId($user->id, 'ended');
                if ($party) {
                    return $this->piXO->getEndGame($party);
                }
            }
        }
        return false;
    }
}