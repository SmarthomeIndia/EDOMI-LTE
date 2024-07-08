<?
/*
*/
?><? ?><? function loginVisu($visuId, $login, $pass)
{
    if ($userListId = checkVisuLoginPass($visuId, $login, $pass)) {
        $sid = createVisuSid();
        sql_call("UPDATE edomiLive.visuUserList SET sid='" . $sid . "',logindate=" . sql_getNow() . ",loginip='" . $_SERVER['REMOTE_ADDR'] . "',logout=0 WHERE (id=" . $userListId . ")");
        return $sid;
    }
    return false;
}

function checkVisuLoginPass($visuId, $login, $pass)
{
    $ss1 = sql_call("SELECT a.id FROM edomiLive.visuUserList AS a,edomiLive.visuUser AS b WHERE (a.visuid=" . sql_encodeValue($visuId) . " AND a.targetid=b.id AND b.login='" . sql_encodeValue($login) . "' AND b.pass='" . sql_encodeValue($pass) . "')");
    if ($n = sql_result($ss1)) {
        return $n['id'];
    }
    return false;
}

function checkVisuSid($visuId, $sid, $getCmd = false, $setOnline = false)
{
    if (isEmpty($sid)) {
        return false;
    }
    $ss1 = sql_call("SELECT * FROM edomiLive.visuUserList WHERE (visuid=" . $visuId . " AND (sid IS NOT NULL) AND sid='" . $sid . "')");
    if ($n = sql_result($ss1)) {
        sql_call("UPDATE edomiLive.visuUserList SET actiondate=" . sql_getNow() . (($setOnline) ? ',online=1' : '') . " WHERE (id=" . $n['id'] . ")");
        if ($getCmd) {
            return $n;
        } else {
            return true;
        }
    }
    return false;
}

function createVisuSid()
{
    do {
        $sid = substr(strtoupper(dechex(intval(rand(1000000, 1000000000))) . dechex(intval(strtotime('NOW') * rand(1, 1000000))) . dechex(intval(rand(1000000, 1000000000)))), 0, 30);
        $ss1 = sql_call("SELECT id FROM edomiLive.visuUserList WHERE (sid='" . $sid . "')");
    } while (sql_result($ss1));
    return $sid;
} ?>
