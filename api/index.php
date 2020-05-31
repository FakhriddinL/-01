<?php
error_reporting(-1);

require_once('application/Application.php');

function router($params) {
    $method = $params['method'];
    if ($method) {
        $app = new Application();
        switch ($method) { 
            // about user
            case 'login': return $app->login($params);
            case 'logout': return $app->logout($params);
            case 'registration': return $app->registration($params);
            // about party
            case 'getFreeUsers': return $app->getFreeUsers($params);
            case 'newParty': return $app->newParty($params);
            case 'isParty' : return $app->isParty($params);
            case 'acceptParty' : return $app->acceptParty($params);
            case 'isAcceptParty' : return $app->isAcceptParty($params);
            // about game
            case 'getGame' : return $app->getGame($params);
            case 'turn': return $app->turn($params);
            default: return false;
        }
    }
    return false;
}

function answer($data) {
    if ($data) {
        return array(
            'result' => 'ok',
            'data' => $data
        );
    }
    return array(
        'result' => 'error',
        'error' => array(
            'code' => 9000,
            'text' => 'unknown error'
        )
    );
}

echo json_encode(answer(router($_GET)));