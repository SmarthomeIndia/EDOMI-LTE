<?
/*
*/
?><? ?><? function db_itemSave($db, $data, $var1 = null, $var2 = null, $var3 = null)
{
    global $global_dpt, $global_charttyp;
    if ($db == 'editRoot') {
        if ($data[1] > 0) {
            $thisFolder = sql_getValues('edomiProject.editRoot', '*', 'id=' . $data[1]);
            if ($thisFolder === false) {
                return 0;
            }
            if (isEmpty($data[2])) {
                $data[2] = $thisFolder['name'];
            }
            if (isEmpty($data[3])) {
                $data[3] = $thisFolder['parentid'];
            }
            $parentFolder = sql_getValues('edomiProject.editRoot', '*', 'id=' . $data[3]);
            if ($parentFolder === false) {
                return 0;
            }
            $rootFolder = sql_getValues('edomiProject.editRoot', '*', 'id=' . $parentFolder['rootid']);
            if ($rootFolder === false) {
                return 0;
            }
            if (!($rootFolder['allow'] & 8)) {
                return 0;
            }
            if (!($thisFolder['rootid'] == $parentFolder['rootid'] && (($thisFolder['link'] == $parentFolder['link'] && $thisFolder['linkid'] == $parentFolder['linkid']) || ($parentFolder['link'] == 0 && isEmpty($parentFolder['linkid']) && $parentFolder['id'] < 1000)))) {
                return 0;
            }
            if ($thisFolder['id'] == $parentFolder['id']) {
                return 0;
            }
            $dbId = sql_save('edomiProject.editRoot', $data[1], array('path' => "'" . $parentFolder['path'] . $parentFolder['id'] . "/'", 'parentid' => $parentFolder['id'], 'name' => "'" . sql_encodeValue($data[2]) . "'", 'tmp' => (($data[5] > 0) ? $data[5] : 'null')));
            return $dbId;
        } else if (!($data[1] > 0) && $data[3] > 0 && !isEmpty($data[2])) {
            $parentFolder = sql_getValues('edomiProject.editRoot', '*', 'id=' . $data[3]);
            if ($parentFolder === false) {
                return 0;
            }
            $rootFolder = sql_getValues('edomiProject.editRoot', '*', 'id=' . $parentFolder['rootid']);
            if ($rootFolder === false) {
                return 0;
            }
            if (!($rootFolder['allow'] & 4)) {
                return 0;
            }
            if ($parentFolder['id'] < 1000 && $parentFolder['allow'] & 1) {
                $link = $parentFolder['id'];
                $linkid = $data[4];
                if (!($linkid > 0 && ($linkid == $parentFolder['linkid'] || isEmpty($parentFolder['linkid'])))) {
                    return 0;
                }
            } else {
                $link = $parentFolder['link'];
                $linkid = $parentFolder['linkid'];
            }
            $dbId = sql_save('edomiProject.editRoot', (($data[1] > 0) ? $data[1] : null), array('path' => "'" . $parentFolder['path'] . $parentFolder['id'] . "/'", 'rootid' => $parentFolder['rootid'], 'parentid' => $parentFolder['id'], 'name' => "'" . sql_encodeValue($data[2]) . "'", 'namedb' => sql_encodeValue($parentFolder['namedb'], true), 'link' => (($link > 0) ? $link : 0), 'linkid' => sql_encodeValue($linkid, true), 'tmp' => (($data[5] > 0) ? $data[5] : 'null')));
            return $dbId;
        }
    }
    if ($db == 'editLogicElementDef') {
        if ($data[2] > 0 && !isEmpty($data[4]) && is_numeric($data[4]) && $data[4] >= 0) {
            $rootFolderId = dbRoot_getRootId($data[2]);
            $lbsId = strval(sprintf("%02d", $rootFolderId)) . strval(sprintf("%06d", $data[4]));
            if (!($data[1] > 0) && $lbsId >= 19000000 && $lbsId <= 19999999) {
                $lbsFn = MAIN_PATH . '/www/admin/lbs/' . $lbsId . '_lbs.php';
                if (!file_exists($lbsFn)) {
                    if (file_exists(MAIN_PATH . '/www/admin/lbs/LBSVorlage' . $data[5] . '.php')) {
                        $vorlage = file_get_contents(MAIN_PATH . '/www/admin/lbs/LBSVorlage' . $data[5] . '.php');
                        $f = fopen($lbsFn, 'w');
                        fwrite($f, $vorlage);
                        fclose($f);
                        lbs_import($lbsId, false, $data[2]);
                        return $lbsId;
                    }
                }
            }
        }
    }
    if ($db == 'editVisuElementDef') {
        if ($data[2] > 0 && !isEmpty($data[4])) {
            $vseId = intVal($data[4]);
            if (!($data[1] > 0) && $vseId >= 1000 && $vseId <= 99999999) {
                $vseFn = MAIN_PATH . '/www/admin/vse/' . $vseId . '_vse.php';
                if (!file_exists($vseFn)) {
                    if (file_exists(MAIN_PATH . '/www/admin/vse/VSEVorlage' . $data[5] . '.php')) {
                        $vorlage = file_get_contents(MAIN_PATH . '/www/admin/vse/VSEVorlage' . $data[5] . '.php');
                        $f = fopen($vseFn, 'w');
                        fwrite($f, $vorlage);
                        fclose($f);
                        vse_import($vseId);
                        return $vseId;
                    }
                }
            }
        }
    }
    if ($db == 'editLogicElement') {
        if ($lbsData = sql_getValues('edomiProject.editLogicElementDef', '*', "id='" . $data[3] . "' AND errcount=0")) {
            $changedLBS = false;
            if ($data[1] > 0) {
                $tmp = sql_getValue('edomiProject.editLogicElement', 'functionid', "id='" . $data[1] . "'");
                if ($tmp != $data[3]) {
                    $changedLBS = true;
                }
                $lbsDataOld = sql_getValues('edomiProject.editLogicElementDef', '*', "id='" . $tmp . "' AND errcount=0");
                if ($lbsDataOld === false) {
                    return 0;
                }
            }
            $dbId = sql_save('edomiProject.editLogicElement', (($data[1] > 0) ? $data[1] : null), array('pageid' => $data[2], 'functionid' => $data[3], 'xpos' => "'" . $data[4] . "'", 'ypos' => "'" . $data[5] . "'", 'name' => sql_encodeValue($data[6], true)));
            if ($dbId > 0) {
                if (!($data[1] > 0) || $changedLBS) {
                    if ($changedLBS) {
                        if (!($data[3] >= 12000010 && $data[3] <= 12000019)) {
                            sql_call("DELETE FROM edomiProject.editLogicCmdList WHERE targetid=" . $dbId);
                        }
                        if ($data[3] >= 12000001 && $data[3] <= 12000005) {
                            sql_call("UPDATE edomiProject.editLogicLink SET linktyp=2,linkid=null,ausgang=null WHERE elementid=" . $dbId . " AND linktyp<>0");
                            sql_call("UPDATE edomiProject.editLogicLink SET value=null WHERE elementid=" . $dbId);
                        }
                        $tmp_srcMaxA = sql_getValue('edomiProject.editLogicElementDefOut', 'MAX(id)', "targetid='" . $lbsDataOld['id'] . "'");
                        $tmp_dstMaxA = sql_getValue('edomiProject.editLogicElementDefOut', 'MAX(id)', "targetid='" . $lbsData['id'] . "'");
                        if ($tmp_srcMaxA > $tmp_dstMaxA) {
                            $tmp_maxA = $tmp_srcMaxA;
                        } else {
                            $tmp_maxA = $tmp_dstMaxA;
                        }
                        if ($tmp_maxA > 0) {
                            for ($t = 1; $t <= $tmp_maxA; $t++) {
                                $tmp_src = sql_getValue('edomiProject.editLogicElementDefOut', 'id', "targetid='" . $lbsDataOld['id'] . "' AND id=" . $t);
                                $tmp_dst = sql_getValue('edomiProject.editLogicElementDefOut', 'id', "targetid='" . $lbsData['id'] . "' AND id=" . $t);
                                if ($tmp_src > 0 && $tmp_dst > 0) {
                                } else if (!$tmp_src > 0 && $tmp_dst > 0) {
                                } else if ($tmp_src > 0 && !$tmp_dst > 0) {
                                    sql_call("UPDATE edomiProject.editLogicLink SET linktyp=2,linkid=null,ausgang=null WHERE linktyp=1 AND linkid=" . $dbId . " AND ausgang=" . $t);
                                } else if (!$tmp_src > 0 && !$tmp_dst > 0) {
                                }
                            }
                        }
                        $tmp_srcMaxE = sql_getValue('edomiProject.editLogicElementDefIn', 'MAX(id)', "targetid='" . $lbsDataOld['id'] . "'");
                        $tmp_dstMaxE = sql_getValue('edomiProject.editLogicElementDefIn', 'MAX(id)', "targetid='" . $lbsData['id'] . "'");
                        if ($tmp_srcMaxE > $tmp_dstMaxE) {
                            $tmp_maxE = $tmp_srcMaxE;
                        } else {
                            $tmp_maxE = $tmp_dstMaxE;
                        }
                        if ($tmp_maxE > 0) {
                            for ($t = 1; $t <= $tmp_maxE; $t++) {
                                $tmp_src = sql_getValue('edomiProject.editLogicElementDefIn', 'id', "targetid='" . $lbsDataOld['id'] . "' AND id=" . $t);
                                $tmp_dst = sql_getValue('edomiProject.editLogicElementDefIn', 'id', "targetid='" . $lbsData['id'] . "' AND id=" . $t);
                                if ($tmp_src > 0 && $tmp_dst > 0) {
                                    $n = sql_getValues('edomiProject.editLogicElementDefIn', '*', "targetid='" . $lbsData['id'] . "' AND id=" . $t);
                                    if ($n !== false) {
                                        sql_call("UPDATE edomiProject.editLogicLink SET value=" . sql_encodeValue($n['value'], true) . " WHERE elementid=" . $dbId . " AND eingang=" . $n['id'] . " AND (value IS NULL)");
                                    }
                                } else if (!$tmp_src > 0 && $tmp_dst > 0) {
                                    $n = sql_getValues('edomiProject.editLogicElementDefIn', '*', "targetid='" . $lbsData['id'] . "' AND id=" . $t);
                                    if ($n !== false) {
                                        sql_save('edomiProject.editLogicLink', null, array('elementid' => $dbId, 'linktyp' => 2, 'eingang' => $n['id'], 'value' => sql_encodeValue($n['value'], true)));
                                    }
                                } else if ($tmp_src > 0 && !$tmp_dst > 0) {
                                    sql_call("DELETE FROM edomiProject.editLogicLink WHERE elementid=" . $dbId . " AND eingang=" . $t);
                                } else if (!$tmp_src > 0 && !$tmp_dst > 0) {
                                }
                            }
                        }
                        sql_call("DELETE FROM edomiProject.editLogicElementVar WHERE elementid=" . $dbId);
                        $ss1 = sql_call("SELECT * FROM edomiProject.editLogicElementDefVar WHERE targetid=" . $lbsData['id'] . " ORDER BY id ASC");
                        while ($n = sql_result($ss1)) {
                            sql_save('edomiProject.editLogicElementVar', null, array('elementid' => $dbId, 'varid' => $n['id'], 'value' => sql_encodeValue($n['value'], true), 'remanent' => $n['remanent']));
                        }
                        sql_close($ss1);
                    } else {
                        $ss1 = sql_call("SELECT * FROM edomiProject.editLogicElementDefIn WHERE targetid=" . $lbsData['id'] . " ORDER BY id ASC");
                        while ($n = sql_result($ss1)) {
                            sql_save('edomiProject.editLogicLink', null, array('elementid' => $dbId, 'linktyp' => 2, 'eingang' => $n['id'], 'value' => sql_encodeValue($n['value'], true)));
                        }
                        sql_close($ss1);
                        $ss1 = sql_call("SELECT * FROM edomiProject.editLogicElementDefVar WHERE targetid=" . $lbsData['id'] . " ORDER BY id ASC");
                        while ($n = sql_result($ss1)) {
                            sql_save('edomiProject.editLogicElementVar', null, array('elementid' => $dbId, 'varid' => $n['id'], 'value' => sql_encodeValue($n['value'], true), 'remanent' => $n['remanent']));
                        }
                        sql_close($ss1);
                    }
                }
            }
            return $dbId;
        }
    }
    if ($db == 'editLogicCmdList' || $db == 'editVisuCmdList' || $db == 'editSequenceCmdList' || $db == 'editMacroCmdList') {
        $ok = false;
        if ($data[3] == 1 && $data[4] > 0) {
            $ok = true;
        }
        if ($data[3] == 2 && $data[4] > 0) {
            $ok = true;
        }
        if ($data[3] == 3 && $data[4] > 0 && $data[5] > 0) {
            $ok = true;
        }
        if ($data[3] == 4 && $data[4] > 0 && !isEmpty($data[8])) {
            $ok = true;
        }
        if ($data[3] == 5 && $data[4] > 0) {
            $data[6] = getNearestNumericValue($data[6], '-1,1');
            $ok = true;
        }
        if ($data[3] == 6 && $data[4] > 0 && $data[5] > 0 && !isEmpty($data[8])) {
            $ok = true;
        }
        if ($data[3] == 19 && $data[4] > 0 && $data[5] > 0 && !isEmpty($data[8])) {
            $ok = true;
        }
        if ($data[3] == 7 && $data[4] > 0 && !isEmpty($data[8])) {
            $ok = true;
        }
        if ($data[3] == 8 && $data[4] > 0) {
            $ok = true;
        }
        if ($data[3] == 9 && $data[4] > 0 && is_numeric($data[8]) && abs($data[8]) >= 1) {
            $data[8] = intval($data[8]);
            $ok = true;
        }
        if ($data[3] == 10 && $data[4] > 0) {
            $data[6] = getNearestNumericValue($data[6], '0,1');
            $ok = true;
        }
        if ($data[3] == 11 && $data[4] > 0) {
            $data[6] = getNearestNumericValue($data[6], '0,1');
            $ok = true;
        }
        if ($data[3] == 12 && $data[4] > 0) {
            $ok = true;
        }
        if ($data[3] == 13 && $data[4] > 0) {
            $data[6] = getNearestNumericValue($data[6], '0,1,2,3,11,12,13,21,22,23');
            $ok = true;
        }
        if ($data[3] == 14 && $data[4] > 0) {
            $data[6] = getNearestNumericValue($data[6], '0,1,2,3,11,12,13,21,22,23');
            $ok = true;
        }
        if ($data[3] == 15 && $data[4] > 0) {
            $ok = true;
        }
        if ($data[3] == 16 && $data[4] > 0) {
            $data[6] = getNearestNumericValue($data[6], '1,2,3');
            $ok = true;
        }
        if ($data[3] == 17 && $data[4] > 0) {
            $ok = true;
        }
        if ($data[3] == 18 && $data[4] > 0) {
            if (!($data[5] > 0)) {
                $data[5] = 0;
            }
            $ok = true;
        }
        if ($data[3] == 20 && $data[4] > 0) {
            $ok = true;
        }
        if ($data[3] == 21 && $data[4] > 0) {
            if (!($data[5] > 0)) {
                $data[5] = 0;
            }
            $ok = true;
        }
        if ($data[3] == 22 && $data[4] >= 0) {
            if (!($data[4] > 0)) {
                $data[4] = 0;
            }
            $data[6] = getNearestNumericValue($data[6], '0,3,5,10,15,20,30,60');
            $ok = true;
        }
        if ($data[3] == 23) {
            if (!($data[4] > 0)) {
                $data[4] = 0;
            }
            if (!($data[5] > 0)) {
                $data[5] = 0;
            }
            $ok = true;
        }
        if ($data[3] == 24 && $data[4] > 0) {
            if (!($data[5] > 0)) {
                $data[5] = 0;
            }
            $ok = true;
        }
        if ($data[3] == 25 && $data[4] > 0) {
            if (!($data[5] > 0)) {
                $data[5] = 0;
            }
            $ok = true;
        }
        if ($data[3] == 26 && $data[4] > 0 && !isEmpty($data[8])) {
            $ok = true;
        }
        if ($data[3] == 27 && $data[4] > 0 && !isEmpty($data[8])) {
            $ok = true;
        }
        if ($data[3] == 28 && $data[4] > 0) {
            if (!($data[5] > 0)) {
                $data[5] = 0;
            }
            $ok = true;
        }
        if ($data[3] == 29 && $data[4] > 0) {
            if (!($data[5] > 0)) {
                $data[5] = 0;
            }
            $ok = true;
        }
        if ($data[3] == 30) {
            $data[6] = getNearestNumericValue($data[6], '1,4,2,3,9');
            $ok = true;
        }
        if ($data[3] == 40 && $data[4] > 0) {
            $data[6] = getNearestNumericValue($data[6], '0,1,2,3,11,12,13,21,22,23');
            $ok = true;
        }
        if ($data[3] == 41 && $data[4] > 0) {
            $data[6] = getNearestNumericValue($data[6], '0,1,2,3,11,12,13,21,22,23');
            $ok = true;
        }
        if ($data[3] == 42 && $data[4] > 0 && $data[5] > 0) {
            $data[6] = getNearestNumericValue($data[6], '0,1,2,3,11,12,13,21,22,23');
            $ok = true;
        }
        if ($data[3] == 50 && $data[4] > 0) {
            $data[6] = getNearestNumericValue($data[6], '0,1,2');
            $ok = true;
        }
        if ($data[3] == 51 && $data[4] > 0) {
            $data[6] = getNearestNumericValue($data[6], '0,1,2');
            $ok = true;
        }
        if ($data[3] == 52 && $data[4] > 0) {
            $data[6] = getNearestNumericValue($data[6], '0,1,2');
            $ok = true;
        }
        if ($data[3] == 53 && $data[4] > 0) {
            $data[6] = getNearestNumericValue($data[6], '0,1,2');
            $ok = true;
        }
        if ($ok && $data[2] > 0) {
            if ($db == 'editSequenceCmdList') {
                if (!($data[1] > 0) && !($data[11] >= 1)) {
                    $max = sql_getValue('edomiProject.editSequenceCmdList', 'MAX(sort)', 'targetid=' . $data[2]);
                    if ($max > 0) {
                        $data[11] = $max + 1;
                    } else {
                        $data[11] = 1;
                    }
                }
                $tmp = array('delay' => (($data[10] > 0) ? intval($data[10]) : 0), 'sort' => (($data[11] > 1) ? intval($data[11]) : 1));
            } else {
                $tmp = array();
            }
            $dbId = sql_save('edomiProject.' . $db, (($data[1] > 0) ? $data[1] : null), array('targetid' => "'" . $data[2] . "'", 'cmd' => "'" . $data[3] . "'", 'cmdid1' => "'" . $data[4] . "'", 'cmdid2' => "'" . $data[5] . "'", 'cmdoption1' => "'" . $data[6] . "'", 'cmdoption2' => "'" . $data[7] . "'", 'cmdvalue1' => sql_encodeValue($data[8], true), 'cmdvalue2' => sql_encodeValue($data[9], true)) + $tmp);
            return $dbId;
        }
    }
    if ($db == 'editVisu') {
        if (!($data[2] > 0)) {
            $data[2] = 21;
        }
        if ($data[2] > 0 && !isEmpty($data[10]) && $data[4] > 0 && $data[5] > 0) {
            $dbId = sql_save('edomiProject.editVisu', (($data[1] > 0) ? $data[1] : null), array('folderid' => "'" . $data[2] . "'", 'name' => "'" . sql_encodeValue($data[10]) . "'", 'xsize' => "'" . sql_encodeValue($data[4]) . "'", 'ysize' => "'" . sql_encodeValue($data[5]) . "'", 'defaultpageid' => sql_encodeValue($data[6], true), 'sspageid' => sql_encodeValue($data[7], true), 'sstimeout' => getNearestNumericValue($data[8], '0,1,2,3,5,10,15,20,30,45,60,120,180,300'), 'indicolor' => sql_encodeValue($data[13], true), 'indicolor2' => sql_encodeValue($data[14], true)));
            return $dbId;
        }
    }
    if ($db == 'editVisuPage') {
        if (!($data[2] > 0)) {
            $data[2] = 22;
        }
        if ($data[2] > 0 && !isEmpty($data[4]) && $data[3] > 0 && ($data[6] == 0 || $data[6] == 2 || ($data[6] == 1 && $data[7] > 0 && $data[8] > 0))) {
            if ($data[6] == 0 || $data[6] == 2) {
                $data[15] = null;
                $data[16] = null;
                $data[7] = null;
                $data[8] = null;
                $data[5] = 0;
                $data[18] = 1;
                $data[19] = 1;
                $data[20] = 1;
            }
            if ($data[6] == 1) {
                $data[9] = 0;
            }
            if ($data[6] == 2) {
                $data[9] = 0;
            }
            if (isEmpty($data[15]) || isEmpty($data[16])) {
                $data[15] = null;
                $data[16] = null;
            } else {
                $data[15] = intval($data[15]);
                $data[16] = intval($data[16]);
            }
            $dbId = sql_save('edomiProject.editVisuPage', (($data[1] > 0) ? $data[1] : null), array('folderid' => "'" . $data[2] . "'", 'name' => "'" . sql_encodeValue($data[4]) . "'", 'visuid' => "'" . $data[3] . "'", 'includeid' => "'" . $data[9] . "'", 'globalinclude' => (($data[14] > 0) ? 1 : 0), 'pagetyp' => getNearestNumericValue($data[6], '0,1,2'), 'xpos' => sql_encodeValue($data[15], true), 'ypos' => sql_encodeValue($data[16], true), 'xsize' => sql_encodeValue($data[7], true), 'ysize' => sql_encodeValue($data[8], true), 'autoclose' => getNearestNumericValue($data[5], '0,5,10,20,30,60,120,180,300,600,900,1800,3600,7200,10800'), 'bgmodal' => (($data[18] > 0) ? 1 : 0), 'bganim' => (($data[19] > 0) ? 1 : 0), 'bgdark' => (($data[20] > 0) ? 1 : 0), 'bgshadow' => (($data[21] > 0) ? 1 : 0), 'bgcolorid' => sql_encodeValue($data[10], true), 'bgimg' => sql_encodeValue($data[11], true), 'xgrid' => (($data[12] > 1) ? "'" . sql_encodeValue($data[12]) . "'" : 1), 'ygrid' => (($data[13] > 1) ? "'" . sql_encodeValue($data[13]) . "'" : 1), 'outlinecolorid' => sql_encodeValue($data[17], true),));
            return $dbId;
        }
    }
    if ($db == 'editVisuElement') {
        if ($data[6] == 0) {
            if ($data[1] > 0) {
                if (!isEmpty($data[20])) {
                    $dbId = sql_save('edomiProject.editVisuElement', $data[1], array('name' => "'" . sql_encodeValue($data[20]) . "'", 'gaid' => sql_encodeValue($data[7], true), 'text' => sql_encodeValue(str_replace(';', '', $data[18]), true)));
                    return $dbId;
                }
            } else {
                if ($data[97] > 0 && $data[98] > 0 && !isEmpty($data[20])) {
                    $dbId = sql_save('edomiProject.editVisuElement', null, array('visuid' => "'" . $data[97] . "'", 'pageid' => "'" . $data[98] . "'", 'gaid' => sql_encodeValue($data[7], true), 'text' => sql_encodeValue($data[18], true), 'controltyp' => 0, 'xpos' => 0, 'ypos' => 0, 'xsize' => 1, 'ysize' => 1, 'name' => "'" . sql_encodeValue($data[20]) . "'", 'groupid' => 0, 'linkid' => 0, 'tmp' => sql_encodeValue($data[99], true), 'tmp2' => sql_encodeValue($data[100], true)));
                    return $dbId;
                }
            }
        } else {
            $vseDef = sql_getValues('edomiProject.editVisuElementDef', '*', 'id=' . $data[6] . ' AND errcount=0');
            if ($vseDef !== false && $data[97] > 0 && $data[98] > 0 && is_numeric($data[2]) && is_numeric($data[3]) && $data[8] >= 0 && $data[15] > 0 && $data[16] > 0) {
                if ($vseDef['flagtext'] == 0) {
                    $data[18] = 'null';
                } else {
                    $data[18] = "'" . sql_encodeValue($data[18]) . "'";
                }
                for ($t = 1; $t <= 20; $t++) {
                    if (is_null($vseDef['var' . $t])) {
                        $data[31 + $t - 1] = 'null';
                    } else {
                        $data[31 + $t - 1] = "'" . sql_encodeValue($data[31 + $t - 1]) . "'";
                    }
                }
                $dbId = sql_save('edomiProject.editVisuElement', (($data[1] > 0) ? $data[1] : null), array('visuid' => "'" . $data[97] . "'", 'pageid' => "'" . $data[98] . "'", 'controltyp' => $data[6], 'dynstylemode' => getNearestNumericValue($data[95], '0,1,2'), 'xpos' => "'" . sql_encodeValue($data[2]) . "'", 'ypos' => "'" . sql_encodeValue($data[3]) . "'", 'xsize' => "'" . sql_encodeValue($data[15]) . "'", 'ysize' => "'" . sql_encodeValue($data[16]) . "'", 'gaid' => sql_encodeValue($data[7], true), 'galive' => getNearestNumericValue($data[96], '-1,0,100,250,500,1000,2000,3000,5000,10000,15000,20000,25000,30000,60000'), 'gaid2' => sql_encodeValue($data[17], true), 'gaid3' => sql_encodeValue($data[23], true), 'zindex' => "'" . sql_encodeValue($data[8]) . "'", 'var1' => $data[31], 'var2' => $data[32], 'var3' => $data[33], 'var4' => $data[34], 'var5' => $data[35], 'var6' => $data[36], 'var7' => $data[37], 'var8' => $data[38], 'var9' => $data[39], 'var10' => $data[40], 'var11' => $data[41], 'var12' => $data[42], 'var13' => $data[43], 'var14' => $data[44], 'var15' => $data[45], 'var16' => $data[46], 'var17' => $data[47], 'var18' => $data[48], 'var19' => $data[49], 'var20' => $data[50], 'gotopageid' => sql_encodeValue($data[13], true), 'closepopupid' => sql_encodeValue($data[94], true), 'closepopup' => (($data[14] > 0) ? 1 : 0), 'text' => $data[18], 'initonly' => getNearestNumericValue($data[19], '0,1'), 'name' => "'" . sql_encodeValue($data[20]) . "'", 'groupid' => (($data[21] > 0) ? $data[21] : 0), 'linkid' => (($data[22] > 0) ? $data[22] : 0), 'tmp' => sql_encodeValue($data[99], true), 'tmp2' => sql_encodeValue($data[100], true)));
                return $dbId;
            }
        }
    }
    if ($db == 'editVisuUser') {
        if (!($data[2] > 0)) {
            $data[2] = 23;
        }
        if ($data[2] > 0 && !isEmpty($data[3]) && !isEmpty($data[4])) {
            $dbId = sql_save('edomiProject.editVisuUser', (($data[1] > 0) ? $data[1] : null), array('folderid' => "'" . $data[2] . "'", 'login' => "'" . sql_encodeValue($data[3]) . "'", 'pass' => "'" . sql_encodeValue($data[4]) . "'", 'gaid' => sql_encodeValue($data[11], true), 'gaid2' => sql_encodeValue($data[13], true), 'gaid3' => sql_encodeValue($data[14], true), 'touch' => getNearestNumericValue($data[9], '0,1,2,3,4'), 'longclick' => getNearestNumericValue($data[19], '-50,-40,-30,-20,-10,0,25,50,75,100,150'), 'preload' => getNearestNumericValue($data[18], '0,1'), 'noerrors' => getNearestNumericValue($data[17], '0,1'), 'noacksounds' => getNearestNumericValue($data[16], '0,1'), 'click' => getNearestNumericValue($data[15], '0,1'), 'touchscroll' => getNearestNumericValue($data[10], '0,1,2,3'), 'autologout' => (($data[12] > 0) ? 1 : 0)));
            return $dbId;
        }
    }
    if ($db == 'editVisuUserList') {
        if ($data[1] > 0) {
            $tmp = sql_getValue('edomiProject.editVisuUserList', 'id', "visuid='" . $data[2] . "' AND targetid='" . $data[3] . "' AND id<>'" . $data[1] . "'");
        } else {
            $tmp = sql_getValue('edomiProject.editVisuUserList', 'id', "visuid='" . $data[2] . "' AND targetid='" . $data[3] . "'");
        }
        if ($data[2] > 0 && $data[3] > 0 && !($tmp > 0)) {
            $dbId = sql_save('edomiProject.editVisuUserList', (($data[1] > 0) ? $data[1] : null), array('visuid' => "'" . $data[2] . "'", 'targetid' => "'" . $data[3] . "'", 'defaultpageid' => "'" . $data[4] . "'", 'sspageid' => "'" . $data[5] . "'"));
            return $dbId;
        }
    }
    if ($db == 'editVisuSnd') {
        if (!($data[2] > 0)) {
            $data[2] = 29;
        }
        if ($data[2] > 0 && !isEmpty($data[3])) {
            $dbId = sql_save('edomiProject.editVisuSnd', (($data[1] > 0) ? $data[1] : null), array('folderid' => "'" . $data[2] . "'", 'name' => "'" . sql_encodeValue($data[3]) . "'", 'ts' => "'" . date('YmdHis') . "'"));
            return $dbId;
        }
    }
    if ($db == 'editVisuFont') {
        if (!($data[2] > 0)) {
            $data[2] = 150;
        }
        if ($data[2] > 0 && !isEmpty($data[3])) {
            if ($data[4] == 1) {
                $dbId = sql_save('edomiProject.editVisuFont', (($data[1] > 0) ? $data[1] : null), array('folderid' => "'" . $data[2] . "'", 'name' => "'" . sql_encodeValue($data[3]) . "'", 'ts' => "'" . date('YmdHis') . "'", 'fonttyp' => 1, 'fontname' => "''", 'fontstyle' => (($data[6] > 0) ? 1 : 0), 'fontweight' => (($data[7] > 0) ? 1 : 0)));
                return $dbId;
            } else {
                $tmp = explode(',', $data[5], 2);
                $data[5] = $tmp[0];
                if (!isEmpty($data[5]) || !($data[1] > 0)) {
                    $dbId = sql_save('edomiProject.editVisuFont', (($data[1] > 0) ? $data[1] : null), array('folderid' => "'" . $data[2] . "'", 'name' => "'" . sql_encodeValue($data[3]) . "'", 'ts' => "'" . date('YmdHis') . "'", 'fonttyp' => 0, 'fontname' => "'" . sql_encodeValue($data[5]) . "'", 'fontstyle' => 0, 'fontweight' => 0));
                    if ($data[1] > 0) {
                        deleteFiles(MAIN_PATH . '/www/data/project/visu/etc/font-' . $data[1] . '.ttf');
                    }
                    return $dbId;
                }
            }
        }
    }
    if ($db == 'editVisuFormat') {
        if (!($data[2] > 0)) {
            $data[2] = 155;
        }
        if ($data[2] > 0 && !isEmpty($data[3])) {
            $dbId = sql_save('edomiProject.editVisuFormat', (($data[1] > 0) ? $data[1] : null), array('folderid' => "'" . $data[2] . "'", 'name' => "'" . sql_encodeValue($data[3]) . "'", 'fgid' => sql_encodeValue($data[4], true), 'bgid' => sql_encodeValue($data[5], true), 'imgid' => sql_encodeValue($data[6], true)));
            return $dbId;
        }
    }
    if ($db == 'editVisuImg') {
        if (!($data[2] > 0)) {
            $data[2] = 28;
        }
        if ($data[2] > 0 && !isEmpty($data[3]) && !isEmpty($data[6])) {
            $dbId = sql_save('edomiProject.editVisuImg', (($data[1] > 0) ? $data[1] : null), array('folderid' => "'" . $data[2] . "'", 'name' => "'" . sql_encodeValue($data[3]) . "'", 'ts' => "'" . date('YmdHis') . "'", 'xsize' => "'" . sql_encodeValue($data[4]) . "'", 'ysize' => "'" . sql_encodeValue($data[5]) . "'", 'suffix' => sql_encodeValue($data[6], true)));
            return $dbId;
        }
    }
    if ($db == 'editVisuBGcol') {
        if (!($data[2] > 0)) {
            $data[2] = 25;
        }
        if ($data[2] > 0 && !isEmpty($data[3]) && !isEmpty($data[4])) {
            $dbId = sql_save('edomiProject.editVisuBGcol', (($data[1] > 0) ? $data[1] : null), array('folderid' => "'" . $data[2] . "'", 'name' => "'" . sql_encodeValue($data[3]) . "'", 'color' => "'" . sql_encodeValue($data[4]) . "'"));
            return $dbId;
        }
    }
    if ($db == 'editVisuFGcol') {
        if (!($data[2] > 0)) {
            $data[2] = 26;
        }
        if ($data[2] > 0 && !isEmpty($data[3]) && !isEmpty($data[4])) {
            $dbId = sql_save('edomiProject.editVisuFGcol', (($data[1] > 0) ? $data[1] : null), array('folderid' => "'" . $data[2] . "'", 'name' => "'" . sql_encodeValue($data[3]) . "'", 'color' => "'" . sql_encodeValue($data[4]) . "'"));
            return $dbId;
        }
    }
    if ($db == 'editVisuAnim') {
        if (!($data[2] > 0)) {
            $data[2] = 27;
        }
        if ($data[2] > 0 && !isEmpty($data[3]) && !isEmpty($data[4])) {
            $dbId = sql_save('edomiProject.editVisuAnim', (($data[1] > 0) ? $data[1] : null), array('folderid' => "'" . $data[2] . "'", 'name' => "'" . sql_encodeValue($data[3]) . "'", 'keyframes' => "'" . sql_encodeValue($data[4]) . "'", 'timing' => getNearestNumericValue($data[5], '0,1,2,3,4'), 'delay' => "'" . sql_encodeValue($data[6]) . "'", 'direction' => getNearestNumericValue($data[7], '0,1,2,3'), 'fillmode' => getNearestNumericValue($data[8], '0,1,2,3')));
            return $dbId;
        }
    }
    if ($db == 'editVisuElementDesignDef') {
        if ($var1 == 1 || $var1 == 2) {
            db_itemSave_parseVisuElementDesignData($data);
            if (isEmpty($data[11]) || isEmpty($data[12])) {
                $data[11] = '';
                $data[12] = '';
                $data[21] = '';
                $data[4] = 0;
            } else {
                $data[4] = 1;
            }
            $tmp = array();
            for ($t = 1; $t <= 48; $t++) {
                $tmp['s' . $t] = sql_encodeValue($data[$t + 10], true);
            }
            if ($var1 == 1) {
                $dbId = sql_save('edomiProject.editVisuElementDesignDef', $data[1], array('styletyp' => (($data[4] > 0) ? 1 : 0)) + $tmp);
            } else {
                $dbId = sql_save('edomiProject.editVisuElementDesignDef', (($data[1] > 0) ? $data[1] : null), array('folderid' => "'" . $data[2] . "'", 'name' => "'" . sql_encodeValue($data[3]) . "'", 'styletyp' => (($data[4] > 0) ? 1 : 0)) + $tmp);
            }
            return $dbId;
        }
        if ($var1 == 3) {
            if ($data[2] > 0 && !isEmpty($data[3])) {
                $dbId = sql_save('edomiProject.editVisuElementDesignDef', (($data[1] > 0) ? $data[1] : null), array('folderid' => "'" . $data[2] . "'", 'name' => "'" . sql_encodeValue($data[3]) . "'"));
                return $dbId;
            }
        }
    }
    if ($db == 'editVisuElementDesign') {
        db_itemSave_parseVisuElementDesignData($data);
        if ($data[3] > 0) {
            if (sql_getValue('edomiProject.editVisuElementDesignDef', 'id', 'id=' . $data[3]) === false) {
                return 0;
            }
        }
        if ($var1) {
            if (!($data[3] > 0) && (isEmpty($data[11]) || isEmpty($data[12]))) {
                return 0;
            }
            $data[4] = 1;
            if ($data[3] > 0) {
                $tmp = sql_getValue('edomiProject.editVisuElementDesignDef', 'styletyp', 'id=' . $data[3]);
                if ($tmp !== false) {
                    if ($tmp == 0 && (isEmpty($data[11]) || isEmpty($data[12]))) {
                        return 0;
                    }
                } else {
                    return 0;
                }
            }
        } else {
            $data[4] = 0;
            $data[11] = null;
            $data[12] = null;
        }
        $tmp = array();
        for ($t = 1; $t <= 48; $t++) {
            $tmp['s' . $t] = sql_encodeValue($data[$t + 10], true);
        }
        $dbId = sql_save('edomiProject.editVisuElementDesign', (($data[1] > 0) ? $data[1] : null), array('targetid' => "'" . $data[2] . "'", 'defid' => "'" . $data[3] . "'", 'styletyp' => (($data[4] > 0) ? 1 : 0)) + $tmp);
        return $dbId;
    }
    if ($db == 'editChart') {
        if (!($data[2] > 0)) {
            $data[2] = 130;
        }
        if ($data[9] >= 1) {
            $data[9] = intVal($data[9]);
        } else {
            $data[9] = '';
        }
        if ($data[2] > 0 && !isEmpty($data[3]) && ($data[8] < 0 || $data[9] >= 1)) {
            if ($data[10] > $data[11] && !isEmpty($data[11])) {
                $tmp = $data[10];
                $data[10] = $data[11];
                $data[11] = $tmp;
            }
            if (!($data[13] >= 0) || !is_numeric($data[13])) {
                $data[13] = 0;
            }
            $dbId = sql_save('edomiProject.editChart', (($data[1] > 0) ? $data[1] : null), array('folderid' => "'" . $data[2] . "'", 'name' => "'" . sql_encodeValue($data[3]) . "'", 'titel' => "'" . sql_encodeValue($data[4]) . "'", 'datefrom' => "'" . sql_encodeValue($data[5]) . "'", 'dateto' => "'" . sql_encodeValue($data[6]) . "'", 'mode' => getNearestNumericValue($data[7], '0,1,2,3,4,5,6,7,8,9'), 'xunit' => getNearestNumericValue($data[8], '-1,0,1,2,3,4,5'), 'xinterval' => sql_encodeValue($data[9], true), 'ymin' => sql_encodeValue($data[10], true), 'ymax' => sql_encodeValue($data[11], true), 'ynice' => getNearestNumericValue($data[12], '0,1'), 'yticks' => sql_encodeValue($data[13], true)));
            return $dbId;
        }
    }
    if ($db == 'editChartList') {
        if ($data[2] > 0 && $data[3] > 0 && $data[5] > 0 && $data[9] > 0 && ($data[19] == 0 || $data[20] > 0) && $data[24] >= 0) {
            if ($data[6] > $data[7] && !isEmpty($data[7])) {
                $tmp = $data[6];
                $data[6] = $data[7];
                $data[7] = $tmp;
            }
            if (!($data[11] >= 1)) {
                $data[11] = 1;
            }
            if (!($data[22] >= 1)) {
                $data[22] = 1;
            }
            if (!($data[24] >= 0 || isEmpty($data[24]))) {
                $data[24] = 0;
            }
            $chartTypes1 = '';
            $chartTypes2 = '';
            foreach ($global_charttyp as $k => $v) {
                if ($i > 0) {
                    $chartTypes1 .= $k . ',';
                }
                $chartTypes2 .= $k . ',';
            }
            if (!($data[1] > 0) && !($data[30] >= 1)) {
                $max = sql_getValue('edomiProject.editChartList', 'MAX(sort)', 'targetid=' . $data[2]);
                if ($max > 0) {
                    $data[30] = $max + 1;
                } else {
                    $data[30] = 1;
                }
            }
            $dbId = sql_save('edomiProject.editChartList', (($data[1] > 0) ? $data[1] : null), array('targetid' => "'" . $data[2] . "'", 'archivkoid' => "'" . $data[3] . "'", 'titel' => "'" . sql_encodeValue($data[4]) . "'", 'charttyp' => getNearestNumericValue($data[5], rtrim($chartTypes1, ',')), 'ymin' => sql_encodeValue($data[6], true), 'ymax' => sql_encodeValue($data[7], true), 'ystyle' => getNearestNumericValue($data[8], '0,1,2'), 's1' => "'" . $data[9] . "'", 's2' => getNearestNumericValue($data[10], '100,90,80,70,60,50,40,30,20,10'), 's3' => sql_encodeValue($data[11], true), 's4' => getNearestNumericValue($data[12], '0,1,2,3,4,5,6,7,8,9,10'), 'ygrid1' => getNearestNumericValue($data[13], '0,1,2'), 'ygrid2' => getNearestNumericValue($data[14], '100,90,80,70,60,50,40,30,20,10'), 'ygrid3' => (($data[15] > 0) ? 1 : 0), 'yshow' => (($data[16] > 0) ? 1 : 0), 'ynice' => getNearestNumericValue($data[17], '0,1'), 'yticks' => (($data[18] > 0) ? "'" . $data[18] . "'" : 0), 'charttyp2' => getNearestNumericValue($data[19], rtrim($chartTypes2, ',')), 'ss1' => "'" . $data[20] . "'", 'ss2' => getNearestNumericValue($data[21], '100,90,80,70,60,50,40,30,20,10'), 'ss3' => sql_encodeValue($data[22], true), 'ss4' => getNearestNumericValue($data[23], '0,1,2,3,4,5,6,7,8,9,10'), 'xinterval' => sql_encodeValue($data[24], true), 'yminmax' => getNearestNumericValue($data[25], '0,1,2,3'), 'extend1' => getNearestNumericValue($data[26], '0,1,2'), 'extend2' => getNearestNumericValue($data[27], '0,1,2'), 'yshowvalue' => getNearestNumericValue($data[28], '0,1'), 'yscale' => (($data[29] > 0) ? 1 : 0), 'sort' => (($data[30] > 1) ? intval($data[30]) : 1)));
            return $dbId;
        }
    }
    if ($db == 'editEmail') {
        if (!($data[2] > 0)) {
            $data[2] = 120;
        }
        if ($data[2] > 0 && !isEmpty($data[3]) && !isEmpty($data[5]) && !isEmpty($data[6])) {
            $dbId = sql_save('edomiProject.editEmail', (($data[1] > 0) ? $data[1] : null), array('folderid' => "'" . $data[2] . "'", 'name' => "'" . sql_encodeValue($data[3]) . "'", 'mailaddr' => "'" . sql_encodeValue($data[4]) . "'", 'subject' => "'" . sql_encodeValue($data[5]) . "'", 'body' => "'" . sql_encodeValue($data[6]) . "'"));
            return $dbId;
        }
    }
    if ($db == 'editHttpKo') {
        if (!($data[2] > 0)) {
            $data[2] = 140;
        }
        if ($data[2] > 0 && !isEmpty($data[3]) && $data[4] > 0) {
            $dbId = sql_save('edomiProject.editHttpKo', (($data[1] > 0) ? $data[1] : null), array('folderid' => "'" . $data[2] . "'", 'name' => "'" . sql_encodeValue($data[3]) . "'", 'gaid' => sql_encodeValue($data[4], true)));
            return $dbId;
        }
    }
    if ($db == 'editPhoneBook') {
        if (!($data[2] > 0)) {
            $data[2] = 125;
        }
        if ($data[2] > 0 && !isEmpty($data[3]) && (!isEmpty($data[4]) || !isEmpty($data[5]))) {
            $dbId = sql_save('edomiProject.editPhoneBook', (($data[1] > 0) ? $data[1] : null), array('folderid' => "'" . $data[2] . "'", 'name' => "'" . sql_encodeValue($data[3]) . "'", 'phone1' => "'" . sql_encodeValue($data[4]) . "'", 'phone2' => "'" . sql_encodeValue($data[5]) . "'"));
            return $dbId;
        }
    }
    if ($db == 'editPhoneCall') {
        if (!($data[2] > 0)) {
            $data[2] = 126;
        }
        if ($data[2] > 0 && !isEmpty($data[3]) && ($data[6] > 0 || $data[7] > 0 || $data[8] > 0)) {
            $dbId = sql_save('edomiProject.editPhoneCall', (($data[1] > 0) ? $data[1] : null), array('folderid' => "'" . $data[2] . "'", 'name' => "'" . sql_encodeValue($data[3]) . "'", 'phoneid1' => sql_encodeValue($data[4], true), 'phoneid2' => sql_encodeValue($data[5], true), 'gaid1' => sql_encodeValue($data[6], true), 'gaid2' => sql_encodeValue($data[7], true), 'gaid3' => sql_encodeValue($data[8], true), 'typ' => (($data[9] > 0) ? 1 : 0)));
            return $dbId;
        }
    }
    if ($db == 'editArchivPhone') {
        if (!($data[2] > 0)) {
            $data[2] = 127;
        }
        if ($data[2] > 0 && !isEmpty($data[3]) && $data[4] >= 0 && $data[5] >= 0) {
            $dbId = sql_save('edomiProject.editArchivPhone', (($data[1] > 0) ? $data[1] : null), array('folderid' => "'" . $data[2] . "'", 'name' => "'" . sql_encodeValue($data[3]) . "'", 'keep' => getNearestNumericValue($data[4], '0,1,2,3,7,14,31,90,180,365,730,1095'), 'outgaid' => sql_encodeValue($data[5], true)));
            return $dbId;
        }
    }
    if ($db == 'editIr') {
        if (!($data[2] > 0)) {
            $data[2] = 75;
        }
        $data[4] = preg_replace("/[^A-F0-9]+/i", '', $data[4]);
        if ($data[2] > 0 && !isEmpty($data[3])) {
            $dbId = sql_save('edomiProject.editIr', (($data[1] > 0) ? $data[1] : null), array('folderid' => "'" . $data[2] . "'", 'name' => "'" . sql_encodeValue($data[3]) . "'", 'data' => sql_encodeValue($data[4], true)));
            return $dbId;
        }
    }
    if ($db == 'editAws') {
        if (!($data[2] > 0)) {
            $data[2] = 110;
        }
        if ($data[2] > 0 && !isEmpty($data[3]) && $data[4] > 0) {
            $dbId = sql_save('edomiProject.editAws', (($data[1] > 0) ? $data[1] : null), array('folderid' => "'" . $data[2] . "'", 'name' => "'" . sql_encodeValue($data[3]) . "'", 'gaid' => sql_encodeValue($data[4], true)));
            return $dbId;
        }
    }
    if ($db == 'editAwsList') {
        if ($data[2] > 0 && $data[3] > 0) {
            $dbId = sql_save('edomiProject.editAwsList', (($data[1] > 0) ? $data[1] : null), array('targetid' => "'" . $data[2] . "'", 'gaid' => "'" . $data[3] . "'", 'gaid2' => "'" . $data[6] . "'", 'gavalue1' => sql_encodeValue($data[4], true), 'gavalue2' => sql_encodeValue($data[5], true)));
            return $dbId;
        }
    }
    if ($db == 'editTimer') {
        if (!($data[2] > 0)) {
            $data[2] = 100;
        }
        if ($data[2] > 0 && !isEmpty($data[3]) && $data[4] > 0) {
            $dbId = sql_save('edomiProject.editTimer', (($data[1] > 0) ? $data[1] : null), array('folderid' => "'" . $data[2] . "'", 'name' => "'" . sql_encodeValue($data[3]) . "'", 'gaid' => sql_encodeValue($data[4], true), 'gaid2' => sql_encodeValue($data[5], true)));
            return $dbId;
        }
    }
    if ($db == 'editTimerData') {
        if (isEmpty($data[12]) || $data[12] < 1 || $data[12] > 31) {
            $data[12] = null;
        }
        if (isEmpty($data[13]) || $data[13] < 1 || $data[13] > 12) {
            $data[13] = null;
        }
        if (isEmpty($data[14]) || $data[14] < 0 || $data[14] > 9999) {
            $data[14] = null;
        }
        if (isEmpty($data[15]) || $data[15] < 1 || $data[15] > 31) {
            $data[15] = null;
        }
        if (isEmpty($data[16]) || $data[16] < 1 || $data[16] > 12) {
            $data[16] = null;
        }
        if (isEmpty($data[17]) || $data[17] < 0 || $data[17] > 9999) {
            $data[17] = null;
        }
        $dayOk = false;
        for ($t = 3; $t <= 9; $t++) {
            if ($data[$t] == 1) {
                $dayOk = true;
                break;
            }
        }
        if ($data[2] > 0 && $dayOk && is_numeric($data[10]) && $data[10] >= 0 && $data[10] <= 23 && is_numeric($data[11]) && $data[11] >= 0 && $data[11] <= 59) {
            $dbId = sql_save('edomiProject.editTimerData', (($data[1] > 0) ? $data[1] : null), array('targetid' => "'" . $data[2] . "'", 'fixed' => 1, 'cmdid' => sql_encodeValue($data[18], true), 'd0' => (($data[3] > 0) ? 1 : 0), 'd1' => (($data[4] > 0) ? 1 : 0), 'd2' => (($data[5] > 0) ? 1 : 0), 'd3' => (($data[6] > 0) ? 1 : 0), 'd4' => (($data[7] > 0) ? 1 : 0), 'd5' => (($data[8] > 0) ? 1 : 0), 'd6' => (($data[9] > 0) ? 1 : 0), 'hour' => intval($data[10]), 'minute' => intval($data[11]), 'day1' => sql_encodeValue($data[12], true), 'month1' => sql_encodeValue($data[13], true), 'year1' => sql_encodeValue($data[14], true), 'day2' => sql_encodeValue($data[15], true), 'month2' => sql_encodeValue($data[16], true), 'year2' => sql_encodeValue($data[17], true), 'mode' => (($data[19] > 0) ? 1 : 0), 'd7' => getNearestNumericValue($data[20], '0,1,2')));
            return $dbId;
        }
    }
    if ($db == 'editTimerMacroList') {
        if (!($data[1] > 0) && !($data[4] >= 1)) {
            $max = sql_getValue('edomiProject.editTimerMacroList', 'MAX(sort)', 'timerid=' . $data[2]);
            if ($max > 0) {
                $data[4] = $max + 1;
            } else {
                $data[4] = 1;
            }
        }
        if ($data[1] > 0) {
            $tmp = sql_getValue('edomiProject.editTimerMacroList', 'id', "timerid='" . $data[2] . "' AND targetid='" . $data[3] . "' AND id<>'" . $data[1] . "'");
        } else {
            $tmp = sql_getValue('edomiProject.editTimerMacroList', 'id', "timerid='" . $data[2] . "' AND targetid='" . $data[3] . "'");
        }
        if ($data[2] > 0 && $data[3] > 0 && $data[4] > 0 && !($tmp > 0)) {
            $dbId = sql_save('edomiProject.editTimerMacroList', (($data[1] > 0) ? $data[1] : null), array('timerid' => "'" . $data[2] . "'", 'targetid' => "'" . $data[3] . "'", 'sort' => (($data[4] > 1) ? intval($data[4]) : 1)));
            return $dbId;
        }
    }
    if ($db == 'editAgenda') {
        if (!($data[2] > 0)) {
            $data[2] = 101;
        }
        if ($data[2] > 0 && !isEmpty($data[3]) && $data[4] > 0) {
            $dbId = sql_save('edomiProject.editAgenda', (($data[1] > 0) ? $data[1] : null), array('folderid' => "'" . $data[2] . "'", 'name' => "'" . sql_encodeValue($data[3]) . "'", 'gaid' => sql_encodeValue($data[4], true), 'gaid2' => sql_encodeValue($data[5], true)));
            return $dbId;
        }
    }
    if ($db == 'editAgendaData') {
        if (isEmpty($data[4])) {
            $data[4] = 0;
        }
        if (isEmpty($data[5])) {
            $data[5] = 0;
        }
        if (strtotime($data[6]) === false) {
            return 0;
        }
        $data[6] = date('Y-m-d', strtotime($data[6]));
        if (!isEmpty($data[7])) {
            if (strtotime($data[7]) === false) {
                return 0;
            }
            $data[7] = date('Y-m-d', strtotime($data[7]));
        }
        if ($data[2] > 0 && is_numeric($data[4]) && $data[4] >= 0 && $data[4] <= 23 && is_numeric($data[5]) && $data[5] >= 0 && $data[5] <= 59) {
            $dbId = sql_save('edomiProject.editAgendaData', (($data[1] > 0) ? $data[1] : null), array('targetid' => "'" . $data[2] . "'", 'fixed' => 1, 'name' => "'" . sql_encodeValue($data[10]) . "'", 'cmdid' => sql_encodeValue($data[3], true), 'hour' => intval($data[4]), 'minute' => intval($data[5]), 'date1' => sql_encodeValue($data[6], true), 'date2' => sql_encodeValue($data[7], true), 'step' => ((intval($data[8]) > 0) ? intval($data[8]) : 0), 'unit' => getNearestNumericValue($data[9], '0,1,2,3'), 'd7' => getNearestNumericValue($data[11], '0,1,2')));
            return $dbId;
        }
    }
    if ($db == 'editAgendaMacroList') {
        if (!($data[1] > 0) && !($data[4] >= 1)) {
            $max = sql_getValue('edomiProject.editAgendaMacroList', 'MAX(sort)', 'agendaid=' . $data[2]);
            if ($max > 0) {
                $data[4] = $max + 1;
            } else {
                $data[4] = 1;
            }
        }
        if ($data[1] > 0) {
            $tmp = sql_getValue('edomiProject.editAgendaMacroList', 'id', "agendaid='" . $data[2] . "' AND targetid='" . $data[3] . "' AND id<>'" . $data[1] . "'");
        } else {
            $tmp = sql_getValue('edomiProject.editAgendaMacroList', 'id', "agendaid='" . $data[2] . "' AND targetid='" . $data[3] . "'");
        }
        if ($data[2] > 0 && $data[3] > 0 && $data[4] > 0 && !($tmp > 0)) {
            $dbId = sql_save('edomiProject.editAgendaMacroList', (($data[1] > 0) ? $data[1] : null), array('agendaid' => "'" . $data[2] . "'", 'targetid' => "'" . $data[3] . "'", 'sort' => (($data[4] > 1) ? intval($data[4]) : 1)));
            return $dbId;
        }
    }
    if ($db == 'editSequence') {
        if (!($data[2] > 0)) {
            $data[2] = 90;
        }
        if ($data[2] > 0 && !isEmpty($data[3])) {
            $dbId = sql_save('edomiProject.editSequence', (($data[1] > 0) ? $data[1] : null), array('folderid' => "'" . $data[2] . "'", 'name' => "'" . sql_encodeValue($data[3]) . "'", 'playpointer' => 0));
            return $dbId;
        }
    }
    if ($db == 'editMacro') {
        if (!($data[2] > 0)) {
            $data[2] = 95;
        }
        if ($data[2] > 0 && !isEmpty($data[3])) {
            $dbId = sql_save('edomiProject.editMacro', (($data[1] > 0) ? $data[1] : null), array('folderid' => "'" . $data[2] . "'", 'name' => "'" . sql_encodeValue($data[3]) . "'"));
            return $dbId;
        }
    }
    if ($db == 'editCam') {
        if (!($data[2] > 0)) {
            $data[2] = 81;
        }
        if ($data[2] > 0 && !isEmpty($data[3]) && !isEmpty($data[4])) {
            $dbId = sql_save('edomiProject.editCam', (($data[1] > 0) ? $data[1] : null), array('folderid' => "'" . $data[2] . "'", 'name' => "'" . sql_encodeValue($data[3]) . "'", 'url' => "'" . sql_encodeValue($data[4]) . "'", 'mjpeg' => (($data[5] > 0) ? 1 : 0), 'dvr' => (($data[7] > 0) ? 1 : 0), 'dvrrate' => ((intVal($data[8]) > 0) ? intVal($data[8]) : 1), 'dvrkeep' => intVal($data[9]), 'dvrgaid' => sql_encodeValue($data[10], true), 'dvrgaid2' => sql_encodeValue($data[11], true), 'cachets' => "null"));
            return $dbId;
        }
    }
    if ($db == 'editCamView') {
        if (!($data[2] > 0)) {
            $data[2] = 83;
        }
        if ($data[2] > 0 && !isEmpty($data[3]) && $data[4] >= 1) {
            $dbId = sql_save('edomiProject.editCamView', (($data[1] > 0) ? $data[1] : null), array('folderid' => "'" . $data[2] . "'", 'name' => "'" . sql_encodeValue($data[3]) . "'", 'camid' => sql_encodeValue($data[4], true), 'srctyp' => getNearestNumericValue($data[5], '0,1,2,3'), 'zoom' => intVal($data[10]), 'a1' => intVal($data[11]), 'a2' => intVal($data[12]), 'x' => intVal($data[13]), 'y' => intVal($data[14]), 'dstw' => intVal($data[15]), 'dsth' => intVal($data[16]), 'dsts' => intVal($data[17]), 'srcr' => intVal($data[18]), 'srcd' => intVal($data[19]), 'srcs' => intVal($data[20])));
            return $dbId;
        }
    }
    if ($db == 'editIp') {
        if (!($data[2] > 0)) {
            $data[2] = 70;
        }
        if ($data[2] > 0 && !isEmpty($data[3]) && !isEmpty($data[4]) && $data[5] > 0 && $data[11] >= 1) {
            $dbId = sql_save('edomiProject.editIp', (($data[1] > 0) ? $data[1] : null), array('folderid' => "'" . $data[2] . "'", 'name' => "'" . sql_encodeValue($data[3]) . "'", 'url' => "'" . sql_encodeValue($data[4]) . "'", 'iptyp' => getNearestNumericValue($data[5], '1,2,3,4'), 'httperrlog' => (($data[7] > 0) ? 1 : 0), 'httptimeout' => (($data[11] >= 1) ? "'" . sql_encodeValue($data[11]) . "'" : 1), 'udpraw' => getNearestNumericValue($data[8], '0,1'), 'outgaid' => sql_encodeValue($data[9], true), 'outgaid2' => sql_encodeValue($data[10], true), 'data' => "'" . sql_encodeValue($data[6]) . "'"));
            return $dbId;
        }
    }
    if ($db == 'editArchivMsg') {
        if (!($data[2] > 0)) {
            $data[2] = 60;
        }
        if ($data[2] > 0 && !isEmpty($data[3]) && $data[4] >= 0 && $data[5] >= 0 && $data[6] >= 0) {
            $dbId = sql_save('edomiProject.editArchivMsg', (($data[1] > 0) ? $data[1] : null), array('folderid' => "'" . $data[2] . "'", 'name' => "'" . sql_encodeValue($data[3]) . "'", 'keep' => getNearestNumericValue($data[4], '0,1,2,3,7,14,31,90,180,365,730,1095'), 'delay' => (($data[6] > 0) ? "'" . sql_encodeValue($data[6]) . "'" : 0), 'outgaid' => sql_encodeValue($data[5], true)));
            return $dbId;
        }
    }
    if ($db == 'editArchivKo') {
        if (!($data[2] > 0)) {
            $data[2] = 50;
        }
        if ($data[2] > 0 && !isEmpty($data[3]) && $data[4] >= 0 && $data[5] >= 0 && $data[6] >= 0) {
            $dbId = sql_save('edomiProject.editArchivKo', (($data[1] > 0) ? $data[1] : null), array('folderid' => "'" . $data[2] . "'", 'name' => "'" . sql_encodeValue($data[3]) . "'", 'keep' => getNearestNumericValue($data[4], '0,1,2,3,7,14,31,90,180,365,730,1095'), 'delay' => (($data[6] > 0) ? "'" . sql_encodeValue($data[6]) . "'" : 0), 'outgaid' => sql_encodeValue($data[5], true), 'cmode' => getNearestNumericValue($data[7], '0,1,2,3'), 'cinterval' => getNearestNumericValue($data[8], '5,10,21,22,23'), 'cts' => getNearestNumericValue($data[9], '0,1,2,10,11,12'), 'clist' => ((isEmpty($data[10])) ? 'null' : getNearestNumericValue($data[10], '0,1,2,3,4,5')), 'coffset' => (($data[11] > 1) ? "'" . sql_encodeValue($data[11]) . "'" : 1), 'cunit' => getNearestNumericValue($data[12], '9,10,11,12,13,19,20,21,22,23')));
            return $dbId;
        }
    }
    if ($db == 'editArchivCam') {
        if (!($data[2] > 0)) {
            $data[2] = 82;
        }
        if ($data[2] > 0 && $data[3] > 0 && $data[4] >= 0 && !isEmpty($data[5]) && $data[6] >= 0 && $data[7] >= 0 && $data[8] >= 0) {
            $dbId = sql_save('edomiProject.editArchivCam', (($data[1] > 0) ? $data[1] : null), array('folderid' => "'" . $data[2] . "'", 'name' => "'" . sql_encodeValue($data[5]) . "'", 'camid' => sql_encodeValue($data[3], true), 'keep' => getNearestNumericValue($data[4], '0,1,2,3,7,14,31,90,180,365,730,1095'), 'delay' => (($data[8] > 0) ? "'" . sql_encodeValue($data[8]) . "'" : 0), 'outgaid' => sql_encodeValue($data[6], true), 'outgaid2' => sql_encodeValue($data[7], true)));
            return $dbId;
        }
    }
    if ($db == 'editScene') {
        if (!($data[2] > 0)) {
            $data[2] = 40;
        }
        if ($data[2] > 0 && !isEmpty($data[3])) {
            $dbId = sql_save('edomiProject.editScene', (($data[1] > 0) ? $data[1] : null), array('folderid' => "'" . $data[2] . "'", 'name' => "'" . sql_encodeValue($data[3]) . "'"));
            return $dbId;
        }
    }
    if ($db == 'editSceneList') {
        if ($data[2] > 0 && $data[3] > 0 && $data[5] >= 0) {
            $dbId = sql_save('edomiProject.editSceneList', (($data[1] > 0) ? $data[1] : null), array('targetid' => "'" . $data[2] . "'", 'gaid' => "'" . $data[3] . "'", 'gavalue' => sql_encodeValue($data[4], true), 'learngaid' => (($data[5] > 0) ? "'" . $data[5] . "'" : 0), 'valuegaid' => "'" . $data[6] . "'"));
            return $dbId;
        }
    }
    if ($db == 'editLogicPage') {
        if (!($data[2] > 0)) {
            $data[2] = 11;
        }
        if ($data[2] > 0 && !isEmpty($data[3])) {
            $dbId = sql_save('edomiProject.editLogicPage', (($data[1] > 0) ? $data[1] : null), array('folderid' => "'" . $data[2] . "'", 'name' => "'" . sql_encodeValue($data[3]) . "'", 'pagestatus' => (($data[4] > 0) ? 1 : 0), 'text' => sql_encodeValue($data[5], true)));
            return $dbId;
        }
    }
    if ($db == 'editKo') {
        if (!($data[2] > 0) && $data[4] == 2) {
            $data[2] = 31;
        }
        if (!($data[2] > 0) && $data[4] == 1) {
            $data[2] = 32;
        }
        if ($data[2] > 0 && $data[16] != '' && ($data[4] == 2 || substr_count($data[3], '/') == 2 || ($data[1] == 1 && $data[3] == '')) && ($data[5] > 0 || $data[4] == 2)) {
            $data[3] = trim($data[3]);
            if (is_numeric($data[7]) && is_numeric($data[8])) {
                if ($data[7] > $data[8]) {
                    $tmp = $data[7];
                    $data[7] = $data[8];
                    $data[8] = $tmp;
                }
            }
            if (is_numeric($data[9])) {
                if (!($data[9] > 0)) {
                    $data[9] = null;
                }
            }
            if ($data[4] == 2) {
                $dpts = '';
                foreach ($global_dpt as $k => $v) {
                    $dpts .= $k . ',';
                }
                $data[11] = 0;
                $data[12] = 0;
                $data[13] = 0;
                $data[3] = $data[1];
            } else if ($data[4] == 1) {
                $dpts = '';
                foreach ($global_dpt as $k => $v) {
                    if ($k > 0) {
                        $dpts .= $k . ',';
                    }
                }
                $data[14] = 0;
                $tmp = explode('/', $data[3]);
                if (count($tmp) == 3) {
                    if (!is_numeric($tmp[0]) || $tmp[0] < 0 || $tmp[0] > 31) {
                        return -1;
                    }
                    if (!is_numeric($tmp[1]) || $tmp[1] < 0 || $tmp[1] > 7) {
                        return -1;
                    }
                    if (!is_numeric($tmp[2]) || $tmp[2] < 0 || $tmp[2] > 255) {
                        return -1;
                    }
                } else {
                    return -1;
                }
                $tmp = sql_getValue('edomiProject.editKo', 'id', "id<>" . $data[1] . " AND ga='" . sql_encodeValue($data[3]) . "'");
                if ($tmp > 0) {
                    return -2;
                }
            }
            if ($data[2] == 33 && $data[1] > 0) {
                if ($data[1] == 6) {
                    $dbId = sql_save('edomiProject.editKo', $data[1], array('defaultvalue' => sql_encodeValue($data[6], true), 'text' => sql_encodeValue($data[17], true)));
                } else {
                    $dbId = sql_save('edomiProject.editKo', $data[1], array('text' => sql_encodeValue($data[17], true)));
                }
            } else if ($data[2] != 33) {
                $dbId = sql_save('edomiProject.editKo', (($data[1] > 0) ? $data[1] : null), array('folderid' => "'" . $data[2] . "'", 'name' => "'" . sql_encodeValue($data[16]) . "'", 'ga' => sql_encodeValue($data[3], true), 'gatyp' => "'" . $data[4] . "'", 'valuetyp' => getNearestNumericValue($data[5], rtrim($dpts, ',')), 'defaultvalue' => sql_encodeValue($data[6], true), 'endvalue' => sql_encodeValue($data[20], true), 'initscan' => (($data[11] > 0) ? 1 : 0), 'initsend' => (($data[12] > 0) ? 1 : 0), 'endsend' => (($data[21] > 0) ? 1 : 0), 'remanent' => (($data[14] > 0) ? 1 : 0), 'prio' => (($data[19] > 0) ? 1 : 0), 'requestable' => getNearestNumericValue($data[13], '0,1,2,3,4,5,6'), 'vmin' => sql_encodeValue($data[7], true), 'vmax' => sql_encodeValue($data[8], true), 'vstep' => sql_encodeValue($data[9], true), 'vlist' => ((isEmpty($data[10])) ? 'null' : getNearestNumericValue($data[10], '0,1,2,3,4,5')), 'vcsv' => sql_encodeValue($data[18], true), 'text' => sql_encodeValue($data[17], true)));
                if ($dbId !== false) {
                    sql_call("UPDATE edomiProject.editKo SET ga=id WHERE id=" . $dbId . " AND gatyp=2");
                }
            }
            return $dbId;
        }
    }
    return 0;
}

function db_itemSave_parseVisuElementDesignData(&$data)
{
    if (!($data[19] > 0)) {
        $data[19] = '';
    }
    if (!($data[20] > 0)) {
        $data[20] = '';
    }
    if (!($data[23] > 0)) {
        $data[23] = '';
    }
    if (!($data[25] > 0)) {
        $data[25] = '';
    }
    if (!($data[32] > 0)) {
        $data[32] = '';
    }
    if (!($data[37] > 0)) {
        $data[37] = '';
    }
    if (!($data[38] > 0)) {
        $data[38] = '';
    }
    if (!($data[39] > 0)) {
        $data[39] = '';
    }
    if (!($data[40] > 0)) {
        $data[40] = '';
    }
    if (!($data[47] > 0)) {
        $data[47] = '';
    }
    if (!($data[48] > 0)) {
        $data[48] = '';
    }
    if (!($data[49] > 0)) {
        $data[49] = '';
    }
    if (!($data[52] > 0)) {
        $data[52] = '';
    }
    if (!($data[53] > 0)) {
        $data[53] = '';
    }
    if (!($data[54] > 0)) {
        $data[54] = '';
    }
    if (!($data[55] > 0)) {
        $data[55] = '';
    }
    if (!($data[56] > 0)) {
        $data[56] = '';
    }
    if (!($data[57] > 0)) {
        $data[57] = '';
    }
}

function db_itemVisuelementHelper($db, $id, $delete = false)
{
    $r = '';
    $root = sql_getValues('edomiProject.editRoot', 'id,name', "namedb='" . $db . "' AND id=rootid");
    if ($root !== false && $root['id'] > 0) {
        $tmp1 = '';
        $tmp2 = '';
        for ($t = 1; $t <= 20; $t++) {
            $tmp1 .= '(var' . $t . 'root=' . $root['id'] . ') AS tmp' . $t . ',';
        }
        for ($t = 1; $t <= 20; $t++) {
            $tmp2 .= '(var' . $t . 'root=' . $root['id'] . ') OR ';
        }
        $ss1 = sql_call("SELECT " . $tmp1 . "id FROM edomiProject.editVisuElementDef WHERE " . $tmp2 . " 1=2");
        while ($n = sql_result($ss1)) {
            for ($t = 1; $t <= 20; $t++) {
                if ($n['tmp' . $t] == 1) {
                    if ($delete) {
                        sql_call("UPDATE edomiProject.editVisuElement SET var" . $t . "=0 WHERE (var" . $t . "=" . $id . " AND controltyp=" . $n['id'] . ")");
                    } else {
                        $ss2 = sql_call("SELECT id,visuid,pageid FROM edomiProject.editVisuElement WHERE (var" . $t . "=" . $id . " AND controltyp=" . $n['id'] . ")");
                        while ($nn = sql_result($ss2)) {
                            $r .= '&middot; Visu ' . $nn['visuid'] . ', Seite ' . $nn['pageid'] . ', Element ' . $nn['id'] . ': spezifische Eigenschaft (' . $root['name'] . ')<br>';
                        }
                        sql_close($ss2);
                    }
                }
            }
        }
        sql_close($ss1);
    }
    return $r;
}

function db_itemLinks($db, $id)
{
    $info = db_itemVisuelementHelper($db, $id);
    if ($db == 'editLogicElementDef') {
        $ss1 = sql_call("SELECT * FROM edomiProject.editLogicElement WHERE (functionid=" . $id . ")");
        while ($n = sql_result($ss1)) {
            $info .= '&middot; Logik, Seite ' . $n['pageid'] . ', LBS-Instanz ' . $n['id'] . ': LBS ' . $n['functionid'] . '<br>';
        }
    } else if ($db == 'editVisuElementDef') {
        $ss1 = sql_call("SELECT * FROM edomiProject.editVisuElement WHERE (controltyp=" . $id . ")");
        while ($n = sql_result($ss1)) {
            $info .= '&middot; Visu ' . $n['visuid'] . ', Seite ' . $n['pageid'] . ', VSE-Instanz ' . $n['id'] . ': VSE ' . $n['controltyp'] . '<br>';
        }
    } else if ($db == 'editVisu') {
        $ss2 = sql_call("SELECT id FROM edomiProject.editVisuPage WHERE (visuid=" . $id . ")");
        while ($page = sql_result($ss2)) {
            $ss1 = sql_call("SELECT * FROM edomiProject.editVisuCmdList WHERE ((cmd=21 OR cmd=29) AND cmdid1=" . $page['id'] . ")");
            while ($n = sql_result($ss1)) {
                $tmp = sql_getValue('edomiProject.editVisuElement', 'visuid', 'id=' . $n['targetid']);
                if ($tmp != $id) {
                    $info .= '&middot; Visu ' . $tmp . ', Seite ' . sql_getValue('edomiProject.editVisuElement', 'pageid', 'id=' . $n['targetid']) . ', Element ' . $n['targetid'] . ': Befehl (Visuseite/Popup aufrufen oder schließen)<br>';
                }
            }
            $ss1 = sql_call("SELECT * FROM edomiProject.editLogicCmdList WHERE ((cmd=21 OR cmd=29) AND cmdid1=" . $page['id'] . ")");
            while ($n = sql_result($ss1)) {
                $info .= '&middot; Logik, Seite ' . sql_getValue('edomiProject.editLogicElement', 'pageid', 'id=' . $n['targetid']) . ', LBS ' . $n['targetid'] . ': Befehl (Visuseite/Popup aufrufen oder schließen)<br>';
            }
            $ss1 = sql_call("SELECT * FROM edomiProject.editSequenceCmdList WHERE ((cmd=21 OR cmd=29) AND cmdid1=" . $page['id'] . ")");
            while ($n = sql_result($ss1)) {
                $info .= '&middot; Sequenz ' . $n['targetid'] . ': Befehl (Visuseite/Popup aufrufen oder schließen)<br>';
            }
            $ss1 = sql_call("SELECT * FROM edomiProject.editMacroCmdList WHERE ((cmd=21 OR cmd=29) AND cmdid1=" . $page['id'] . ")");
            while ($n = sql_result($ss1)) {
                $info .= '&middot; Makro ' . $n['targetid'] . ': Befehl (Visuseite/Popup aufrufen oder schließen)<br>';
            }
        }
        $ss1 = sql_call("SELECT * FROM edomiProject.editVisuCmdList WHERE ((cmd=18 OR cmd=23 OR cmd=24 OR cmd=26 OR cmd=28) AND cmdid1=" . $id . ")");
        while ($n = sql_result($ss1)) {
            $info .= '&middot; Visu ' . sql_getValue('edomiProject.editVisuElement', 'visuid', 'id=' . $n['targetid']) . ', Seite ' . sql_getValue('edomiProject.editVisuElement', 'pageid', 'id=' . $n['targetid']) . ', Element ' . $n['targetid'] . ': Befehl<br>';
        }
        $ss1 = sql_call("SELECT * FROM edomiProject.editLogicCmdList WHERE ((cmd=18 OR cmd=23 OR cmd=24 OR cmd=26 OR cmd=28) AND cmdid1=" . $id . ")");
        while ($n = sql_result($ss1)) {
            $info .= '&middot; Logik, Seite ' . sql_getValue('edomiProject.editLogicElement', 'pageid', 'id=' . $n['targetid']) . ', LBS ' . $n['targetid'] . ': Befehl<br>';
        }
        $ss1 = sql_call("SELECT * FROM edomiProject.editSequenceCmdList WHERE ((cmd=18 OR cmd=23 OR cmd=24 OR cmd=26 OR cmd=28) AND cmdid1=" . $id . ")");
        while ($n = sql_result($ss1)) {
            $info .= '&middot; Sequenz ' . $n['targetid'] . ': Befehl<br>';
        }
        $ss1 = sql_call("SELECT * FROM edomiProject.editMacroCmdList WHERE ((cmd=18 OR cmd=23 OR cmd=24 OR cmd=26 OR cmd=28) AND cmdid1=" . $id . ")");
        while ($n = sql_result($ss1)) {
            $info .= '&middot; Makro ' . $n['targetid'] . ': Befehl<br>';
        }
    } else if ($db == 'editVisuPage') {
        $ss1 = sql_call("SELECT * FROM edomiProject.editVisu WHERE (defaultpageid=" . $id . " OR sspageid=" . $id . ")");
        while ($n = sql_result($ss1)) {
            $info .= '&middot; Visu ' . $n['id'] . ': Startseite/Bildschirmschoner<br>';
        }
        $ss1 = sql_call("SELECT * FROM edomiProject.editVisuUserList WHERE (defaultpageid=" . $id . " OR sspageid=" . $id . ")");
        while ($n = sql_result($ss1)) {
            $info .= '&middot; Visu ' . $n['visuid'] . ', Visuaccount ' . $n['targetid'] . ': individuelle Startseite/Bildschirmschoner<br>';
        }
        $ss1 = sql_call("SELECT * FROM edomiProject.editVisuPage WHERE (includeid=" . $id . ")");
        while ($n = sql_result($ss1)) {
            $info .= '&middot; Visu ' . $n['visuid'] . ', Seite ' . $n['id'] . ': Inkludeseite<br>';
        }
        $ss1 = sql_call("SELECT * FROM edomiProject.editVisuElement WHERE (gotopageid=" . $id . " OR closepopupid=" . $id . ")");
        while ($n = sql_result($ss1)) {
            $info .= '&middot; Visu ' . $n['visuid'] . ', Seite ' . $n['pageid'] . ', Element ' . $n['id'] . ': Seitenverweis bzw. Popup schließen<br>';
        }
        $ss1 = sql_call("SELECT * FROM edomiProject.editVisuCmdList WHERE ((cmd=21 OR cmd=29) AND cmdid1=" . $id . ")");
        while ($n = sql_result($ss1)) {
            $info .= '&middot; Visu ' . sql_getValue('edomiProject.editVisuElement', 'visuid', 'id=' . $n['targetid']) . ', Seite ' . sql_getValue('edomiProject.editVisuElement', 'pageid', 'id=' . $n['targetid']) . ', Element ' . $n['targetid'] . ': Befehl (Visuseite/Popup aufrufen oder schließen)<br>';
        }
        $ss1 = sql_call("SELECT * FROM edomiProject.editLogicCmdList WHERE ((cmd=21 OR cmd=29) AND cmdid1=" . $id . ")");
        while ($n = sql_result($ss1)) {
            $info .= '&middot; Logik, Seite ' . sql_getValue('edomiProject.editLogicElement', 'pageid', 'id=' . $n['targetid']) . ', LBS ' . $n['targetid'] . ': Befehl (Visuseite/Popup aufrufen oder schließen)<br>';
        }
        $ss1 = sql_call("SELECT * FROM edomiProject.editSequenceCmdList WHERE ((cmd=21 OR cmd=29) AND cmdid1=" . $id . ")");
        while ($n = sql_result($ss1)) {
            $info .= '&middot; Sequenz ' . $n['targetid'] . ': Befehl (Visuseite/Popup aufrufen oder schließen)<br>';
        }
        $ss1 = sql_call("SELECT * FROM edomiProject.editMacroCmdList WHERE ((cmd=21 OR cmd=29) AND cmdid1=" . $id . ")");
        while ($n = sql_result($ss1)) {
            $info .= '&middot; Makro ' . $n['targetid'] . ': Befehl (Visuseite/Popup aufrufen oder schließen)<br>';
        }
    } else if ($db == 'editVisuUser') {
        $ss1 = sql_call("SELECT * FROM edomiProject.editVisuUserList WHERE (targetid=" . $id . ")");
        while ($n = sql_result($ss1)) {
            $info .= '&middot; Visu ' . $n['visuid'] . ': Visuaccount<br>';
        }
        $ss1 = sql_call("SELECT * FROM edomiProject.editVisuCmdList WHERE ((cmd=18 OR cmd=21 OR cmd=23 OR cmd=28 OR cmd=29) AND cmdid2=" . $id . ") OR ((cmd=25 OR cmd=27) AND cmdid1=" . $id . ")");
        while ($n = sql_result($ss1)) {
            $info .= '&middot; Visu ' . sql_getValue('edomiProject.editVisuElement', 'visuid', 'id=' . $n['targetid']) . ', Seite ' . sql_getValue('edomiProject.editVisuElement', 'pageid', 'id=' . $n['targetid']) . ', Element ' . $n['targetid'] . ': Befehl<br>';
        }
        $ss1 = sql_call("SELECT * FROM edomiProject.editLogicCmdList WHERE ((cmd=18 OR cmd=21 OR cmd=23 OR cmd=28 OR cmd=29) AND cmdid2=" . $id . ") OR ((cmd=25 OR cmd=27) AND cmdid1=" . $id . ")");
        while ($n = sql_result($ss1)) {
            $info .= '&middot; Logik, Seite ' . sql_getValue('edomiProject.editLogicElement', 'pageid', 'id=' . $n['targetid']) . ', LBS ' . $n['targetid'] . ': Befehl<br>';
        }
        $ss1 = sql_call("SELECT * FROM edomiProject.editSequenceCmdList WHERE ((cmd=18 OR cmd=21 OR cmd=23 OR cmd=28 OR cmd=29) AND cmdid2=" . $id . ") OR ((cmd=25 OR cmd=27) AND cmdid1=" . $id . ")");
        while ($n = sql_result($ss1)) {
            $info .= '&middot; Sequenz ' . $n['targetid'] . ': Befehl<br>';
        }
        $ss1 = sql_call("SELECT * FROM edomiProject.editMacroCmdList WHERE ((cmd=18 OR cmd=21 OR cmd=23 OR cmd=28 OR cmd=29) AND cmdid2=" . $id . ") OR ((cmd=25 OR cmd=27) AND cmdid1=" . $id . ")");
        while ($n = sql_result($ss1)) {
            $info .= '&middot; Makro ' . $n['targetid'] . ': Befehl<br>';
        }
    } else if ($db == 'editVisuElementDesignDef') {
        $ss1 = sql_call("SELECT * FROM edomiProject.editVisuElementDesign WHERE (defid=" . $id . ")");
        while ($n = sql_result($ss1)) {
            $info .= '&middot; Visu ' . sql_getValue('edomiProject.editVisuElement', 'visuid', 'id=' . $n['targetid']) . ', Seite ' . sql_getValue('edomiProject.editVisuElement', 'pageid', 'id=' . $n['targetid']) . ', Element ' . $n['targetid'] . ': Designvorlage<br>';
        }
    } else if ($db == 'editVisuBGcol') {
        $ss1 = sql_call("SELECT * FROM edomiProject.editVisuPage WHERE (bgcolorid=" . $id . ")");
        while ($n = sql_result($ss1)) {
            $info .= '&middot; Visu ' . $n['visuid'] . ', Seite ' . $n['id'] . ', Hintergrundfarbe<br>';
        }
        $ss1 = sql_call("SELECT * FROM edomiProject.editVisuElementDesign WHERE (s9=" . $id . " OR s44=" . $id . " OR s45=" . $id . ") GROUP BY targetid");
        while ($n = sql_result($ss1)) {
            $info .= '&middot; Visu ' . sql_getValue('edomiProject.editVisuElement', 'visuid', 'id=' . $n['targetid']) . ', Seite ' . sql_getValue('edomiProject.editVisuElement', 'pageid', 'id=' . $n['targetid']) . ', Element ' . $n['targetid'] . '<br>';
        }
        $ss1 = sql_call("SELECT * FROM edomiProject.editVisuElementDesignDef WHERE (s9=" . $id . " OR s44=" . $id . " OR s45=" . $id . ")");
        while ($n = sql_result($ss1)) {
            $info .= '&middot; Designvorlage ' . $n['id'] . '<br>';
        }
        $ss1 = sql_call("SELECT * FROM edomiProject.editVisuFormat WHERE (bgid=" . $id . ")");
        while ($n = sql_result($ss1)) {
            $info .= '&middot; Formatierung (Meldungsarchive) ' . $n['id'] . '<br>';
        }
    } else if ($db == 'editVisuFGcol') {
        $ss1 = sql_call("SELECT * FROM edomiProject.editVisuElementDesign WHERE (s15=" . $id . " OR s22=" . $id . " OR s27=" . $id . " OR s28=" . $id . " OR s29=" . $id . " OR s30=" . $id . " OR s37=" . $id . " OR s42=" . $id . " OR s43=" . $id . ") GROUP BY targetid");
        while ($n = sql_result($ss1)) {
            $info .= '&middot; Visu ' . sql_getValue('edomiProject.editVisuElement', 'visuid', 'id=' . $n['targetid']) . ', Seite ' . sql_getValue('edomiProject.editVisuElement', 'pageid', 'id=' . $n['targetid']) . ', Element ' . $n['targetid'] . '<br>';
        }
        $ss1 = sql_call("SELECT * FROM edomiProject.editVisuElementDesignDef WHERE (s15=" . $id . " OR s22=" . $id . " OR s27=" . $id . " OR s28=" . $id . " OR s29=" . $id . " OR s30=" . $id . " OR s37=" . $id . " OR s42=" . $id . " OR s43=" . $id . ")");
        while ($n = sql_result($ss1)) {
            $info .= '&middot; Designvorlage ' . $n['id'] . '<br>';
        }
        $ss1 = sql_call("SELECT * FROM edomiProject.editChartList WHERE (s1=" . $id . " OR ss1=" . $id . ")");
        while ($n = sql_result($ss1)) {
            $info .= '&middot; Diagramm ' . $n['targetid'] . ', Datenquelle ' . $n['id'] . '<br>';
        }
        $ss1 = sql_call("SELECT * FROM edomiProject.editVisu WHERE (indicolor=" . $id . " OR indicolor2=" . $id . ")");
        while ($n = sql_result($ss1)) {
            $info .= '&middot; Visu ' . $n['id'] . ': Indikatorfarbe/Eingabefarbe<br>';
        }
        $ss1 = sql_call("SELECT * FROM edomiProject.editVisuFormat WHERE (fgid=" . $id . ")");
        while ($n = sql_result($ss1)) {
            $info .= '&middot; Formatierung (Meldungsarchive) ' . $n['id'] . '<br>';
        }
    } else if ($db == 'editVisuAnim') {
        $ss1 = sql_call("SELECT * FROM edomiProject.editVisuElementDesign WHERE (s39=" . $id . ") GROUP BY targetid");
        while ($n = sql_result($ss1)) {
            $info .= '&middot; Visu ' . sql_getValue('edomiProject.editVisuElement', 'visuid', 'id=' . $n['targetid']) . ', Seite ' . sql_getValue('edomiProject.editVisuElement', 'pageid', 'id=' . $n['targetid']) . ', Element ' . $n['targetid'] . '<br>';
        }
        $ss1 = sql_call("SELECT * FROM edomiProject.editVisuElementDesignDef WHERE (s39=" . $id . ")");
        while ($n = sql_result($ss1)) {
            $info .= '&middot; Designvorlage ' . $n['id'] . '<br>';
        }
    } else if ($db == 'editVisuImg') {
        $ss1 = sql_call("SELECT * FROM edomiProject.editVisuPage WHERE (bgimg=" . $id . ")");
        while ($n = sql_result($ss1)) {
            $info .= '&middot; Visu ' . $n['visuid'] . ', Seite ' . $n['id'] . ', Hintergrundbild<br>';
        }
        $ss1 = sql_call("SELECT * FROM edomiProject.editVisuElementDesign WHERE (s10=" . $id . " OR s46=" . $id . " OR s47=" . $id . ") GROUP BY targetid");
        while ($n = sql_result($ss1)) {
            $info .= '&middot; Visu ' . sql_getValue('edomiProject.editVisuElement', 'visuid', 'id=' . $n['targetid']) . ', Seite ' . sql_getValue('edomiProject.editVisuElement', 'pageid', 'id=' . $n['targetid']) . ', Element ' . $n['targetid'] . '<br>';
        }
        $ss1 = sql_call("SELECT * FROM edomiProject.editVisuElementDesignDef WHERE (s10=" . $id . " OR s46=" . $id . " OR s47=" . $id . ")");
        while ($n = sql_result($ss1)) {
            $info .= '&middot; Designvorlage ' . $n['id'] . '<br>';
        }
        $ss1 = sql_call("SELECT * FROM edomiProject.editVisuFormat WHERE (imgid=" . $id . ")");
        while ($n = sql_result($ss1)) {
            $info .= '&middot; Formatierung (Meldungsarchive) ' . $n['id'] . '<br>';
        }
    } else if ($db == 'editVisuSnd') {
        $ss1 = sql_call("SELECT * FROM edomiProject.editVisuCmdList WHERE ((cmd=24 OR cmd=25) AND cmdid2=" . $id . ")");
        while ($n = sql_result($ss1)) {
            $info .= '&middot; Visu ' . sql_getValue('edomiProject.editVisuElement', 'visuid', 'id=' . $n['targetid']) . ', Seite ' . sql_getValue('edomiProject.editVisuElement', 'pageid', 'id=' . $n['targetid']) . ', Element ' . $n['targetid'] . ': Befehl<br>';
        }
        $ss1 = sql_call("SELECT * FROM edomiProject.editLogicCmdList WHERE ((cmd=24 OR cmd=25) AND cmdid2=" . $id . ")");
        while ($n = sql_result($ss1)) {
            $info .= '&middot; Logik, Seite ' . sql_getValue('edomiProject.editLogicElement', 'pageid', 'id=' . $n['targetid']) . ', LBS ' . $n['targetid'] . ': Befehl<br>';
        }
        $ss1 = sql_call("SELECT * FROM edomiProject.editSequenceCmdList WHERE ((cmd=24 OR cmd=25) AND cmdid2=" . $id . ")");
        while ($n = sql_result($ss1)) {
            $info .= '&middot; Sequenz ' . $n['targetid'] . ': Befehl<br>';
        }
        $ss1 = sql_call("SELECT * FROM edomiProject.editMacroCmdList WHERE ((cmd=24 OR cmd=25) AND cmdid2=" . $id . ")");
        while ($n = sql_result($ss1)) {
            $info .= '&middot; Makro ' . $n['targetid'] . ': Befehl<br>';
        }
    } else if ($db == 'editVisuFont') {
        $ss1 = sql_call("SELECT * FROM edomiProject.editVisuElementDesign WHERE (s13=" . $id . ") GROUP BY targetid");
        while ($n = sql_result($ss1)) {
            $info .= '&middot; Visu ' . sql_getValue('edomiProject.editVisuElement', 'visuid', 'id=' . $n['targetid']) . ', Seite ' . sql_getValue('edomiProject.editVisuElement', 'pageid', 'id=' . $n['targetid']) . ', Element ' . $n['targetid'] . '<br>';
        }
        $ss1 = sql_call("SELECT * FROM edomiProject.editVisuElementDesignDef WHERE (s13=" . $id . ")");
        while ($n = sql_result($ss1)) {
            $info .= '&middot; Designvorlage ' . $n['id'] . '<br>';
        }
    } else if ($db == 'editKo') {
        $ss1 = sql_call("SELECT * FROM edomiProject.editLogicLink WHERE (linktyp=0 AND linkid=" . $id . ")");
        while ($n = sql_result($ss1)) {
            $info .= '&middot; Logik, Seite ' . sql_getValue('edomiProject.editLogicElement', 'pageid', 'id=' . $n['elementid']) . ', LBS ' . $n['elementid'] . ': Eingang ' . $n['eingang'] . '<br>';
        }
        $ss1 = sql_call("SELECT * FROM edomiProject.editLogicCmdList WHERE (cmd>0 AND cmd<10 AND cmdid1=" . $id . ") OR ((cmd=3 OR cmd=6 OR cmd=42) AND cmdid2=" . $id . ")");
        while ($n = sql_result($ss1)) {
            $info .= '&middot; Logik, Seite ' . sql_getValue('edomiProject.editLogicElement', 'pageid', 'id=' . $n['targetid']) . ', LBS ' . $n['targetid'] . ': Befehl<br>';
        }
        $ss1 = sql_call("SELECT * FROM edomiProject.editVisuCmdList WHERE (cmd>0 AND cmd<10 AND cmdid1=" . $id . ") OR ((cmd=3 OR cmd=6 OR cmd=42) AND cmdid2=" . $id . ")");
        while ($n = sql_result($ss1)) {
            $info .= '&middot; Visu ' . sql_getValue('edomiProject.editVisuElement', 'visuid', 'id=' . $n['targetid']) . ', Seite ' . sql_getValue('edomiProject.editVisuElement', 'pageid', 'id=' . $n['targetid']) . ', Element ' . $n['targetid'] . ': Befehl<br>';
        }
        $ss1 = sql_call("SELECT * FROM edomiProject.editVisuElement WHERE (gaid=" . $id . " OR gaid2=" . $id . " OR gaid3=" . $id . ")");
        while ($n = sql_result($ss1)) {
            $info .= '&middot; Visu ' . $n['visuid'] . ', Seite ' . $n['pageid'] . ', Element ' . $n['id'] . ': KO<br>';
        }
        $ss1 = sql_call("SELECT id FROM edomiProject.editVisuUser WHERE (gaid=" . $id . " OR gaid2=" . $id . " OR gaid3=" . $id . ")");
        while ($n = sql_result($ss1)) {
            $info .= '&middot; Visuaccount ' . $n['id'] . ': Status-KO<br>';
        }
        $ss1 = sql_call("SELECT id FROM edomiProject.editAws WHERE (gaid=" . $id . ")");
        while ($n = sql_result($ss1)) {
            $info .= '&middot; Anwesenheitssimulation ' . $n['id'] . ': Steuerungs-KO<br>';
        }
        $ss1 = sql_call("SELECT targetid FROM edomiProject.editAwsList WHERE (gaid=" . $id . " OR gaid2=" . $id . ")");
        while ($n = sql_result($ss1)) {
            $info .= '&middot; Anwesenheitssimulation ' . $n['targetid'] . ': KO (Aufzeichnung/Status-KO)<br>';
        }
        $ss1 = sql_call("SELECT id FROM edomiProject.editHttpKo WHERE (gaid=" . $id . ")");
        while ($n = sql_result($ss1)) {
            $info .= '&middot; Fernzugriff ' . $n['id'] . ': KO<br>';
        }
        $ss1 = sql_call("SELECT id FROM edomiProject.editPhoneCall WHERE (gaid1=" . $id . " OR gaid2=" . $id . " OR gaid3=" . $id . ")");
        while ($n = sql_result($ss1)) {
            $info .= '&middot; Anruftrigger ' . $n['id'] . ': Status-KO<br>';
        }
        $ss1 = sql_call("SELECT targetid FROM edomiProject.editSequenceCmdList WHERE (cmd>0 AND cmd<10 AND cmdid1=" . $id . ") OR ((cmd=3 OR cmd=6 OR cmd=42) AND cmdid2=" . $id . ")");
        while ($n = sql_result($ss1)) {
            $info .= '&middot; Sequenz ' . $n['targetid'] . ': Befehl<br>';
        }
        $ss1 = sql_call("SELECT targetid FROM edomiProject.editMacroCmdList WHERE (cmd>0 AND cmd<10 AND cmdid1=" . $id . ") OR ((cmd=3 OR cmd=6 OR cmd=42) AND cmdid2=" . $id . ")");
        while ($n = sql_result($ss1)) {
            $info .= '&middot; Makro ' . $n['targetid'] . ': Befehl<br>';
        }
        $ss1 = sql_call("SELECT targetid FROM edomiProject.editSceneList WHERE (gaid=" . $id . " OR learngaid=" . $id . " OR valuegaid=" . $id . ")");
        while ($n = sql_result($ss1)) {
            $info .= '&middot; Szene ' . $n['targetid'] . ': KO/Lern-KO/Wert-KO<br>';
        }
        $ss1 = sql_call("SELECT id FROM edomiProject.editTimer WHERE (gaid=" . $id . " OR gaid2=" . $id . ")");
        while ($n = sql_result($ss1)) {
            $info .= '&middot; Zeitschaltuhr ' . $n['id'] . ': Steuerungs-/Zusatzbedingungs-KO<br>';
        }
        $ss1 = sql_call("SELECT id FROM edomiProject.editAgenda WHERE (gaid=" . $id . " OR gaid2=" . $id . ")");
        while ($n = sql_result($ss1)) {
            $info .= '&middot; Terminschaltuhr ' . $n['id'] . ': Steuerungs-/Zusatzbedingungs-KO<br>';
        }
        $ss1 = sql_call("SELECT * FROM edomiProject.editArchivCam WHERE (outgaid=" . $id . " OR outgaid2=" . $id . ")");
        while ($n = sql_result($ss1)) {
            $info .= '&middot; Kameraarchiv ' . $n['id'] . ': Status-/Metadaten-KO<br>';
        }
        $ss1 = sql_call("SELECT * FROM edomiProject.editCam WHERE (dvrgaid=" . $id . " OR dvrgaid2=" . $id . ")");
        while ($n = sql_result($ss1)) {
            $info .= '&middot; Kameraeinstellungen ' . $n['id'] . ': DVR-KO<br>';
        }
        $ss1 = sql_call("SELECT * FROM edomiProject.editArchivKo WHERE (outgaid=" . $id . ")");
        while ($n = sql_result($ss1)) {
            $info .= '&middot; Datenarchiv ' . $n['id'] . ': KO<br>';
        }
        $ss1 = sql_call("SELECT * FROM edomiProject.editArchivMsg WHERE (outgaid=" . $id . ")");
        while ($n = sql_result($ss1)) {
            $info .= '&middot; Meldungsarchiv ' . $n['id'] . ': KO<br>';
        }
        $ss1 = sql_call("SELECT * FROM edomiProject.editArchivPhone WHERE (outgaid=" . $id . ")");
        while ($n = sql_result($ss1)) {
            $info .= '&middot; Anrufarchiv ' . $n['id'] . ': KO<br>';
        }
        $ss1 = sql_call("SELECT * FROM edomiProject.editIp WHERE (outgaid=" . $id . " OR outgaid2=" . $id . ")");
        while ($n = sql_result($ss1)) {
            $info .= '&middot; HTTP/UDP/SHELL ' . $n['id'] . ': Antwort-/Fehler-KO<br>';
        }
    } else if ($db == 'editScene') {
        $ss1 = sql_call("SELECT * FROM edomiProject.editVisuCmdList WHERE (cmd=10 AND cmdid1=" . $id . ")");
        while ($n = sql_result($ss1)) {
            $info .= '&middot; Visu ' . sql_getValue('edomiProject.editVisuElement', 'visuid', 'id=' . $n['targetid']) . ', Seite ' . sql_getValue('edomiProject.editVisuElement', 'pageid', 'id=' . $n['targetid']) . ', Element ' . $n['targetid'] . ': Befehl<br>';
        }
        $ss1 = sql_call("SELECT * FROM edomiProject.editLogicCmdList WHERE (cmd=10 AND cmdid1=" . $id . ")");
        while ($n = sql_result($ss1)) {
            $info .= '&middot; Logik, Seite ' . sql_getValue('edomiProject.editLogicElement', 'pageid', 'id=' . $n['targetid']) . ', LBS ' . $n['targetid'] . ': Befehl<br>';
        }
        $ss1 = sql_call("SELECT * FROM edomiProject.editSequenceCmdList WHERE (cmd=10 AND cmdid1=" . $id . ")");
        while ($n = sql_result($ss1)) {
            $info .= '&middot; Sequenz ' . $n['targetid'] . ': Befehl<br>';
        }
        $ss1 = sql_call("SELECT * FROM edomiProject.editMacroCmdList WHERE (cmd=10 AND cmdid1=" . $id . ")");
        while ($n = sql_result($ss1)) {
            $info .= '&middot; Makro ' . $n['targetid'] . ': Befehl<br>';
        }
    } else if ($db == 'editArchivKo') {
        $ss1 = sql_call("SELECT * FROM edomiProject.editVisuCmdList WHERE ((cmd=13 OR cmd=40 OR cmd=42 OR cmd=50) AND cmdid1=" . $id . ")");
        while ($n = sql_result($ss1)) {
            $info .= '&middot; Visu ' . sql_getValue('edomiProject.editVisuElement', 'visuid', 'id=' . $n['targetid']) . ', Seite ' . sql_getValue('edomiProject.editVisuElement', 'pageid', 'id=' . $n['targetid']) . ', Element ' . $n['targetid'] . ': Befehl<br>';
        }
        $ss1 = sql_call("SELECT * FROM edomiProject.editLogicCmdList WHERE ((cmd=13 OR cmd=40 OR cmd=42 OR cmd=50) AND cmdid1=" . $id . ")");
        while ($n = sql_result($ss1)) {
            $info .= '&middot; Logik, Seite ' . sql_getValue('edomiProject.editLogicElement', 'pageid', 'id=' . $n['targetid']) . ', LBS ' . $n['targetid'] . ': Befehl<br>';
        }
        $ss1 = sql_call("SELECT * FROM edomiProject.editSequenceCmdList WHERE ((cmd=13 OR cmd=40 OR cmd=42 OR cmd=50) AND cmdid1=" . $id . ")");
        while ($n = sql_result($ss1)) {
            $info .= '&middot; Sequenz ' . $n['targetid'] . ': Befehl<br>';
        }
        $ss1 = sql_call("SELECT * FROM edomiProject.editMacroCmdList WHERE ((cmd=13 OR cmd=40 OR cmd=42 OR cmd=50) AND cmdid1=" . $id . ")");
        while ($n = sql_result($ss1)) {
            $info .= '&middot; Makro ' . $n['targetid'] . ': Befehl<br>';
        }
        $ss1 = sql_call("SELECT * FROM edomiProject.editChartList WHERE (archivkoid=" . $id . ")");
        while ($n = sql_result($ss1)) {
            $info .= '&middot; Diagramm ' . $n['targetid'] . ', Datenquelle ' . $n['id'] . '<br>';
        }
    } else if ($db == 'editArchivMsg') {
        $ss1 = sql_call("SELECT * FROM edomiProject.editVisuCmdList WHERE ((cmd=14 OR cmd=41 OR cmd=51) AND cmdid1=" . $id . ")");
        while ($n = sql_result($ss1)) {
            $info .= '&middot; Visu ' . sql_getValue('edomiProject.editVisuElement', 'visuid', 'id=' . $n['targetid']) . ', Seite ' . sql_getValue('edomiProject.editVisuElement', 'pageid', 'id=' . $n['targetid']) . ', Element ' . $n['targetid'] . ': Befehl<br>';
        }
        $ss1 = sql_call("SELECT * FROM edomiProject.editLogicCmdList WHERE ((cmd=14 OR cmd=41 OR cmd=51) AND cmdid1=" . $id . ")");
        while ($n = sql_result($ss1)) {
            $info .= '&middot; Logik, Seite ' . sql_getValue('edomiProject.editLogicElement', 'pageid', 'id=' . $n['targetid']) . ', LBS ' . $n['targetid'] . ': Befehl<br>';
        }
        $ss1 = sql_call("SELECT * FROM edomiProject.editSequenceCmdList WHERE ((cmd=14 OR cmd=41 OR cmd=51) AND cmdid1=" . $id . ")");
        while ($n = sql_result($ss1)) {
            $info .= '&middot; Sequenz ' . $n['targetid'] . ': Befehl<br>';
        }
        $ss1 = sql_call("SELECT * FROM edomiProject.editMacroCmdList WHERE ((cmd=14 OR cmd=41 OR cmd=51) AND cmdid1=" . $id . ")");
        while ($n = sql_result($ss1)) {
            $info .= '&middot; Makro ' . $n['targetid'] . ': Befehl<br>';
        }
    } else if ($db == 'editVisuFormat') {
        $ss1 = sql_call("SELECT * FROM edomiProject.editVisuCmdList WHERE ((cmd=14 OR cmd=41) AND cmdid2=" . $id . ")");
        while ($n = sql_result($ss1)) {
            $info .= '&middot; Visu ' . sql_getValue('edomiProject.editVisuElement', 'visuid', 'id=' . $n['targetid']) . ', Seite ' . sql_getValue('edomiProject.editVisuElement', 'pageid', 'id=' . $n['targetid']) . ', Element ' . $n['targetid'] . ': Befehl<br>';
        }
        $ss1 = sql_call("SELECT * FROM edomiProject.editLogicCmdList WHERE ((cmd=14 OR cmd=41) AND cmdid2=" . $id . ")");
        while ($n = sql_result($ss1)) {
            $info .= '&middot; Logik, Seite ' . sql_getValue('edomiProject.editLogicElement', 'pageid', 'id=' . $n['targetid']) . ', LBS ' . $n['targetid'] . ': Befehl<br>';
        }
        $ss1 = sql_call("SELECT * FROM edomiProject.editSequenceCmdList WHERE ((cmd=14 OR cmd=41) AND cmdid2=" . $id . ")");
        while ($n = sql_result($ss1)) {
            $info .= '&middot; Sequenz ' . $n['targetid'] . ': Befehl<br>';
        }
        $ss1 = sql_call("SELECT * FROM edomiProject.editMacroCmdList WHERE ((cmd=14 OR cmd=41) AND cmdid2=" . $id . ")");
        while ($n = sql_result($ss1)) {
            $info .= '&middot; Makro ' . $n['targetid'] . ': Befehl<br>';
        }
    } else if ($db == 'editIp') {
        $ss1 = sql_call("SELECT * FROM edomiProject.editVisuCmdList WHERE (cmd=15 AND cmdid1=" . $id . ")");
        while ($n = sql_result($ss1)) {
            $info .= '&middot; Visu ' . sql_getValue('edomiProject.editVisuElement', 'visuid', 'id=' . $n['targetid']) . ', Seite ' . sql_getValue('edomiProject.editVisuElement', 'pageid', 'id=' . $n['targetid']) . ', Element ' . $n['targetid'] . ': Befehl<br>';
        }
        $ss1 = sql_call("SELECT * FROM edomiProject.editLogicCmdList WHERE (cmd=15 AND cmdid1=" . $id . ")");
        while ($n = sql_result($ss1)) {
            $info .= '&middot; Logik, Seite ' . sql_getValue('edomiProject.editLogicElement', 'pageid', 'id=' . $n['targetid']) . ', LBS ' . $n['targetid'] . ': Befehl<br>';
        }
        $ss1 = sql_call("SELECT * FROM edomiProject.editSequenceCmdList WHERE (cmd=15 AND cmdid1=" . $id . ")");
        while ($n = sql_result($ss1)) {
            $info .= '&middot; Sequenz ' . $n['targetid'] . ': Befehl<br>';
        }
        $ss1 = sql_call("SELECT * FROM edomiProject.editMacroCmdList WHERE (cmd=15 AND cmdid1=" . $id . ")");
        while ($n = sql_result($ss1)) {
            $info .= '&middot; Makro ' . $n['targetid'] . ': Befehl<br>';
        }
    } else if ($db == 'editIr') {
        $ss1 = sql_call("SELECT * FROM edomiProject.editVisuCmdList WHERE (cmd=16 AND cmdid1=" . $id . ")");
        while ($n = sql_result($ss1)) {
            $info .= '&middot; Visu ' . sql_getValue('edomiProject.editVisuElement', 'visuid', 'id=' . $n['targetid']) . ', Seite ' . sql_getValue('edomiProject.editVisuElement', 'pageid', 'id=' . $n['targetid']) . ', Element ' . $n['targetid'] . ': Befehl<br>';
        }
        $ss1 = sql_call("SELECT * FROM edomiProject.editLogicCmdList WHERE (cmd=16 AND cmdid1=" . $id . ")");
        while ($n = sql_result($ss1)) {
            $info .= '&middot; Logik, Seite ' . sql_getValue('edomiProject.editLogicElement', 'pageid', 'id=' . $n['targetid']) . ', LBS ' . $n['targetid'] . ': Befehl<br>';
        }
        $ss1 = sql_call("SELECT * FROM edomiProject.editSequenceCmdList WHERE (cmd=16 AND cmdid1=" . $id . ")");
        while ($n = sql_result($ss1)) {
            $info .= '&middot; Sequenz ' . $n['targetid'] . ': Befehl<br>';
        }
        $ss1 = sql_call("SELECT * FROM edomiProject.editMacroCmdList WHERE (cmd=16 AND cmdid1=" . $id . ")");
        while ($n = sql_result($ss1)) {
            $info .= '&middot; Makro ' . $n['targetid'] . ': Befehl<br>';
        }
    } else if ($db == 'editCam') {
        $ss1 = sql_call("SELECT * FROM edomiProject.editArchivCam WHERE (camid=" . $id . ")");
        while ($n = sql_result($ss1)) {
            $info .= '&middot; Kameraarchiv ' . $n['id'] . ': Kamera<br>';
        }
        $ss1 = sql_call("SELECT * FROM edomiProject.editCamView WHERE (camid=" . $id . ")");
        while ($n = sql_result($ss1)) {
            $info .= '&middot; Kameraansicht ' . $n['id'] . ': Kamera<br>';
        }
    } else if ($db == 'editArchivCam') {
        $ss1 = sql_call("SELECT * FROM edomiProject.editVisuCmdList WHERE ((cmd=12 OR cmd=52) AND cmdid1=" . $id . ")");
        while ($n = sql_result($ss1)) {
            $info .= '&middot; Visu ' . sql_getValue('edomiProject.editVisuElement', 'visuid', 'id=' . $n['targetid']) . ', Seite ' . sql_getValue('edomiProject.editVisuElement', 'pageid', 'id=' . $n['targetid']) . ', Element ' . $n['targetid'] . ': Befehl<br>';
        }
        $ss1 = sql_call("SELECT * FROM edomiProject.editLogicCmdList WHERE ((cmd=12 OR cmd=52) AND cmdid1=" . $id . ")");
        while ($n = sql_result($ss1)) {
            $info .= '&middot; Logik, Seite ' . sql_getValue('edomiProject.editLogicElement', 'pageid', 'id=' . $n['targetid']) . ', LBS ' . $n['targetid'] . ': Befehl<br>';
        }
        $ss1 = sql_call("SELECT * FROM edomiProject.editSequenceCmdList WHERE ((cmd=12 OR cmd=52) AND cmdid1=" . $id . ")");
        while ($n = sql_result($ss1)) {
            $info .= '&middot; Sequenz ' . $n['targetid'] . ': Befehl<br>';
        }
        $ss1 = sql_call("SELECT * FROM edomiProject.editMacroCmdList WHERE ((cmd=12 OR cmd=52) AND cmdid1=" . $id . ")");
        while ($n = sql_result($ss1)) {
            $info .= '&middot; Makro ' . $n['targetid'] . ': Befehl<br>';
        }
    } else if ($db == 'editSequence') {
        $ss1 = sql_call("SELECT * FROM edomiProject.editVisuCmdList WHERE (cmd=11 AND cmdid1=" . $id . ")");
        while ($n = sql_result($ss1)) {
            $info .= '&middot; Visu ' . sql_getValue('edomiProject.editVisuElement', 'visuid', 'id=' . $n['targetid']) . ', Seite ' . sql_getValue('edomiProject.editVisuElement', 'pageid', 'id=' . $n['targetid']) . ', Element ' . $n['targetid'] . ': Befehl<br>';
        }
        $ss1 = sql_call("SELECT * FROM edomiProject.editLogicCmdList WHERE (cmd=11 AND cmdid1=" . $id . ")");
        while ($n = sql_result($ss1)) {
            $info .= '&middot; Logik, Seite ' . sql_getValue('edomiProject.editLogicElement', 'pageid', 'id=' . $n['targetid']) . ', LBS ' . $n['targetid'] . ': Befehl<br>';
        }
        $ss1 = sql_call("SELECT * FROM edomiProject.editSequenceCmdList WHERE (cmd=11 AND cmdid1=" . $id . ")");
        while ($n = sql_result($ss1)) {
            $info .= '&middot; Sequenz ' . $n['targetid'] . ': Befehl<br>';
        }
        $ss1 = sql_call("SELECT * FROM edomiProject.editMacroCmdList WHERE (cmd=11 AND cmdid1=" . $id . ")");
        while ($n = sql_result($ss1)) {
            $info .= '&middot; Makro ' . $n['targetid'] . ': Befehl<br>';
        }
    } else if ($db == 'editMacro') {
        $ss1 = sql_call("SELECT * FROM edomiProject.editTimerData WHERE (cmdid=" . $id . ")");
        while ($n = sql_result($ss1)) {
            $info .= '&middot; Zeitschaltuhr ' . $n['targetid'] . ': Schaltzeit<br>';
        }
        $ss1 = sql_call("SELECT * FROM edomiProject.editAgendaData WHERE (cmdid=" . $id . ")");
        while ($n = sql_result($ss1)) {
            $info .= '&middot; Terminschaltuhr ' . $n['targetid'] . ': Schaltzeit<br>';
        }
        $ss1 = sql_call("SELECT * FROM edomiProject.editTimerMacroList WHERE (targetid=" . $id . ")");
        while ($n = sql_result($ss1)) {
            $info .= '&middot; Zeitschaltuhr ' . $n['timerid'] . ': Makro-Vorgabe<br>';
        }
        $ss1 = sql_call("SELECT * FROM edomiProject.editAgendaMacroList WHERE (targetid=" . $id . ")");
        while ($n = sql_result($ss1)) {
            $info .= '&middot; Terminschaltuhr ' . $n['agendaid'] . ': Makro-Vorgabe<br>';
        }
        $ss1 = sql_call("SELECT * FROM edomiProject.editVisuCmdList WHERE (cmd=17 AND cmdid1=" . $id . ")");
        while ($n = sql_result($ss1)) {
            $info .= '&middot; Visu ' . sql_getValue('edomiProject.editVisuElement', 'visuid', 'id=' . $n['targetid']) . ', Seite ' . sql_getValue('edomiProject.editVisuElement', 'pageid', 'id=' . $n['targetid']) . ', Element ' . $n['targetid'] . ': Befehl<br>';
        }
        $ss1 = sql_call("SELECT * FROM edomiProject.editLogicCmdList WHERE (cmd=17 AND cmdid1=" . $id . ")");
        while ($n = sql_result($ss1)) {
            $info .= '&middot; Logik, Seite ' . sql_getValue('edomiProject.editLogicElement', 'pageid', 'id=' . $n['targetid']) . ', LBS ' . $n['targetid'] . ': Befehl<br>';
        }
        $ss1 = sql_call("SELECT * FROM edomiProject.editSequenceCmdList WHERE (cmd=17 AND cmdid1=" . $id . ")");
        while ($n = sql_result($ss1)) {
            $info .= '&middot; Sequenz ' . $n['targetid'] . ': Befehl<br>';
        }
        $ss1 = sql_call("SELECT * FROM edomiProject.editMacroCmdList WHERE (cmd=17 AND cmdid1=" . $id . ")");
        while ($n = sql_result($ss1)) {
            $info .= '&middot; Makro ' . $n['targetid'] . ': Befehl<br>';
        }
    } else if ($db == 'editEmail') {
        $ss1 = sql_call("SELECT * FROM edomiProject.editVisuCmdList WHERE (cmd=20 AND cmdid1=" . $id . ")");
        while ($n = sql_result($ss1)) {
            $info .= '&middot; Visu ' . sql_getValue('edomiProject.editVisuElement', 'visuid', 'id=' . $n['targetid']) . ', Seite ' . sql_getValue('edomiProject.editVisuElement', 'pageid', 'id=' . $n['targetid']) . ', Element ' . $n['targetid'] . ': Befehl<br>';
        }
        $ss1 = sql_call("SELECT * FROM edomiProject.editLogicCmdList WHERE (cmd=20 AND cmdid1=" . $id . ")");
        while ($n = sql_result($ss1)) {
            $info .= '&middot; Logik, Seite ' . sql_getValue('edomiProject.editLogicElement', 'pageid', 'id=' . $n['targetid']) . ', LBS ' . $n['targetid'] . ': Befehl<br>';
        }
        $ss1 = sql_call("SELECT * FROM edomiProject.editSequenceCmdList WHERE (cmd=20 AND cmdid1=" . $id . ")");
        while ($n = sql_result($ss1)) {
            $info .= '&middot; Sequenz ' . $n['targetid'] . ': Befehl<br>';
        }
        $ss1 = sql_call("SELECT * FROM edomiProject.editMacroCmdList WHERE (cmd=20 AND cmdid1=" . $id . ")");
        while ($n = sql_result($ss1)) {
            $info .= '&middot; Makro ' . $n['targetid'] . ': Befehl<br>';
        }
    } else if ($db == 'editPhoneBook') {
        $ss1 = sql_call("SELECT * FROM edomiProject.editVisuCmdList WHERE (cmd=22 AND cmdid1=" . $id . ")");
        while ($n = sql_result($ss1)) {
            $info .= '&middot; Visu ' . sql_getValue('edomiProject.editVisuElement', 'visuid', 'id=' . $n['targetid']) . ', Seite ' . sql_getValue('edomiProject.editVisuElement', 'pageid', 'id=' . $n['targetid']) . ', Element ' . $n['targetid'] . ': Befehl<br>';
        }
        $ss1 = sql_call("SELECT * FROM edomiProject.editLogicCmdList WHERE (cmd=22 AND cmdid1=" . $id . ")");
        while ($n = sql_result($ss1)) {
            $info .= '&middot; Logik, Seite ' . sql_getValue('edomiProject.editLogicElement', 'pageid', 'id=' . $n['targetid']) . ', LBS ' . $n['targetid'] . ': Befehl<br>';
        }
        $ss1 = sql_call("SELECT * FROM edomiProject.editSequenceCmdList WHERE (cmd=22 AND cmdid1=" . $id . ")");
        while ($n = sql_result($ss1)) {
            $info .= '&middot; Sequenz ' . $n['targetid'] . ': Befehl<br>';
        }
        $ss1 = sql_call("SELECT * FROM edomiProject.editMacroCmdList WHERE (cmd=22 AND cmdid1=" . $id . ")");
        while ($n = sql_result($ss1)) {
            $info .= '&middot; Makro ' . $n['targetid'] . ': Befehl<br>';
        }
    } else if ($db == 'editArchivPhone') {
        $ss1 = sql_call("SELECT * FROM edomiProject.editVisuCmdList WHERE (cmd=53 AND cmdid1=" . $id . ")");
        while ($n = sql_result($ss1)) {
            $info .= '&middot; Visu ' . sql_getValue('edomiProject.editVisuElement', 'visuid', 'id=' . $n['targetid']) . ', Seite ' . sql_getValue('edomiProject.editVisuElement', 'pageid', 'id=' . $n['targetid']) . ', Element ' . $n['targetid'] . ': Befehl<br>';
        }
        $ss1 = sql_call("SELECT * FROM edomiProject.editLogicCmdList WHERE (cmd=53 AND cmdid1=" . $id . ")");
        while ($n = sql_result($ss1)) {
            $info .= '&middot; Logik, Seite ' . sql_getValue('edomiProject.editLogicElement', 'pageid', 'id=' . $n['targetid']) . ', LBS ' . $n['targetid'] . ': Befehl<br>';
        }
        $ss1 = sql_call("SELECT * FROM edomiProject.editSequenceCmdList WHERE (cmd=53 AND cmdid1=" . $id . ")");
        while ($n = sql_result($ss1)) {
            $info .= '&middot; Sequenz ' . $n['targetid'] . ': Befehl<br>';
        }
        $ss1 = sql_call("SELECT * FROM edomiProject.editMacroCmdList WHERE (cmd=53 AND cmdid1=" . $id . ")");
        while ($n = sql_result($ss1)) {
            $info .= '&middot; Makro ' . $n['targetid'] . ': Befehl<br>';
        }
    }
    return $info;
}

function db_folderDelete($id, $firstcall = true)
{
    $folder = sql_getValues('edomiProject.editRoot', '*', 'id>=1000 && id=' . $id);
    if ($folder !== false) {
        if ($firstcall) {
            $allow = dbRoot_getAllow($folder['id']);
            if ($allow === false || !($allow & 64)) {
                return false;
            }
            sql_call("UPDATE edomiProject.editRoot SET tmp=0");
        }
        $ss1 = sql_call("SELECT id FROM edomiProject." . $folder['namedb'] . " WHERE (folderid=" . $folder['id'] . ")");
        while ($item = sql_result($ss1)) {
            db_itemDelete($folder['namedb'], $item['id']);
        }
        sql_close($ss1);
        if (sql_getCount('edomiProject.' . $folder['namedb'], 'folderid=' . $folder['id']) == 0) {
            $ok = true;
            $r = sql_call("UPDATE edomiProject.editRoot SET tmp=1 WHERE id=" . $folder['id']);
            if (!$r) {
                $ok = false;
            }
            $ss1 = sql_call("SELECT id FROM edomiProject.editRoot WHERE parentid=" . $folder['id']);
            while ($n = sql_result($ss1)) {
                $r = db_folderDelete($n['id'], false);
                if (!$r) {
                    $ok = false;
                }
            }
            sql_close($ss1);
            if ($firstcall && $ok) {
                sql_call("DELETE FROM edomiProject.editRoot WHERE tmp=1 AND (id=" . $folder['id'] . " OR path LIKE '%/" . $folder['id'] . "/%')");
                if (sql_getCount('edomiProject.editRoot', 'tmp=1') > 0) {
                    $ok = false;
                }
            }
            return $ok;
        } else {
            return false;
        }
    }
    return false;
}

function db_folderMove($id, $targetFolderId, $firstcall = true)
{
    if (!($targetFolderId > 0)) {
        return false;
    }
    $srcFolder = sql_getValues('edomiProject.editRoot', '*', 'id=' . $id);
    if ($srcFolder !== false) {
        if ($firstcall) {
            $allow = dbRoot_getAllow($targetFolderId);
            if ($allow === false || !($allow & 16)) {
                return false;
            }
        }
        $dbId = db_itemSave('editRoot', array(1 => $srcFolder['id'], 3 => $targetFolderId));
        if ($dbId > 0) {
            $ss1 = sql_call("SELECT id FROM edomiProject.editRoot WHERE parentid=" . $srcFolder['id'] . " ORDER BY id ASC");
            while ($n = sql_result($ss1)) {
                $tmp = db_folderMove($n['id'], $dbId, false);
            }
            sql_close($ss1);
            return $dbId;
        }
    }
    return false;
}

function db_folderDuplicate($id, $targetFolderId = 0, $nameSuffix = '', $firstcall = true)
{
    if (!global_duplicateSuffix) {
        $nameSuffix = '';
    }
    $srcFolder = sql_getValues('edomiProject.editRoot', '*', 'id=' . $id);
    if ($srcFolder !== false) {
        if ($firstcall) {
            $allow = dbRoot_getAllow((($targetFolderId > 0) ? $targetFolderId : $srcFolder['id']));
            if ($allow === false || !($allow & 16) || !($allow & 32)) {
                return false;
            }
        }
        $dbId = db_itemSave('editRoot', array(1 => -1, 2 => $srcFolder['name'] . $nameSuffix, 3 => (($targetFolderId > 0) ? $targetFolderId : $srcFolder['parentid']), 4 => $srcFolder['linkid']));
        if ($dbId > 0) {
            $ss1 = sql_call("SELECT id FROM edomiProject." . $srcFolder['namedb'] . " WHERE folderid=" . $srcFolder['id'] . " ORDER BY id ASC");
            while ($n = sql_result($ss1)) {
                $tmp = db_itemDuplicate($srcFolder['namedb'], $n['id'], $dbId, '');
            }
            sql_close($ss1);
            $ss1 = sql_call("SELECT id FROM edomiProject.editRoot WHERE parentid=" . $srcFolder['id'] . " ORDER BY id ASC");
            while ($n = sql_result($ss1)) {
                $tmp = db_folderDuplicate($n['id'], $dbId, '', false);
            }
            sql_close($ss1);
            return $dbId;
        }
    }
    return false;
}

function db_itemDelete($db, $id)
{
    db_itemVisuelementHelper($db, $id, true);
    if ($db == 'editRoot') {
        $r = db_folderDelete($id);
        return $r;
    }
    if ($db == 'editLogicPage') {
        $ss1 = sql_call("SELECT b.elementid FROM edomiProject.editLogicElement AS a,edomiProject.editLogicElementVar AS b WHERE (a.pageid=" . $id . " AND b.elementid=a.id) GROUP BY b.elementid");
        while ($n = sql_result($ss1)) {
            sql_call("DELETE FROM edomiProject.editLogicElementVar WHERE (elementid=" . $n['elementid'] . ")");
        }
        $ss1 = sql_call("SELECT b.elementid FROM edomiProject.editLogicElement AS a,edomiProject.editLogicLink AS b WHERE (a.pageid=" . $id . " AND b.elementid=a.id) GROUP BY b.elementid");
        while ($n = sql_result($ss1)) {
            sql_call("DELETE FROM edomiProject.editLogicLink WHERE (elementid=" . $n['elementid'] . ")");
        }
        $ss1 = sql_call("SELECT b.targetid FROM edomiProject.editLogicElement AS a,edomiProject.editLogicCmdList AS b WHERE (a.pageid=" . $id . " AND b.targetid=a.id) GROUP BY b.targetid");
        while ($n = sql_result($ss1)) {
            sql_call("DELETE FROM edomiProject.editLogicCmdList WHERE (targetid=" . $n['targetid'] . ")");
        }
        sql_call("DELETE FROM edomiProject.editLogicElement WHERE (pageid=" . $id . ")");
        sql_call("DELETE FROM edomiProject.editLogicPage WHERE (id=" . $id . ")");
    } else if ($db == 'editLogicElementDef') {
        sql_call("DELETE FROM edomiProject.editLogicElementDef WHERE id=" . $id);
        sql_call("DELETE FROM edomiProject.editLogicElementDefIn WHERE targetid=" . $id);
        sql_call("DELETE FROM edomiProject.editLogicElementDefOut WHERE targetid=" . $id);
        sql_call("DELETE FROM edomiProject.editLogicElementDefVar WHERE targetid=" . $id);
        deleteFiles(MAIN_PATH . '/www/admin/help/lbs_' . $id . '.htm');
        deleteFiles(MAIN_PATH . '/www/admin/lbs/' . $id . '_lbs.php');
        $ss1 = sql_call("SELECT id FROM edomiProject.editLogicElement WHERE functionid=" . $id);
        while ($n = sql_result($ss1)) {
            db_itemDelete('editLogicElement', $n['id']);
        }
        sql_close($ss1);
    } else if ($db == 'editVisuElementDef') {
        vse_delete($id);
        deleteFiles(MAIN_PATH . '/www/admin/vse/' . $id . '_vse.php');
        $ss1 = sql_call("SELECT id FROM edomiProject.editVisuElement WHERE controltyp=" . $id);
        while ($n = sql_result($ss1)) {
            db_itemDelete('editVisuElement', $n['id']);
        }
        sql_close($ss1);
    } else if ($db == 'editVisu') {
        $ss2 = sql_call("SELECT id FROM edomiProject.editVisuPage WHERE (visuid=" . $id . ")");
        while ($page = sql_result($ss2)) {
            sql_call("DELETE FROM edomiProject.editVisuCmdList WHERE ((cmd=21 OR cmd=29) AND cmdid1=" . $page['id'] . ")");
            sql_call("DELETE FROM edomiProject.editLogicCmdList WHERE ((cmd=21 OR cmd=29) AND cmdid1=" . $page['id'] . ")");
            sql_call("DELETE FROM edomiProject.editSequenceCmdList WHERE ((cmd=21 OR cmd=29) AND cmdid1=" . $page['id'] . ")");
            sql_call("DELETE FROM edomiProject.editMacroCmdList WHERE ((cmd=21 OR cmd=29) AND cmdid1=" . $page['id'] . ")");
        }
        sql_call("DELETE FROM edomiProject.editVisuCmdList WHERE ((cmd=18 OR cmd=23 OR cmd=24 OR cmd=26 OR cmd=28) AND cmdid1=" . $id . ")");
        sql_call("DELETE FROM edomiProject.editLogicCmdList WHERE ((cmd=18 OR cmd=23 OR cmd=24 OR cmd=26 OR cmd=28) AND cmdid1=" . $id . ")");
        sql_call("DELETE FROM edomiProject.editSequenceCmdList WHERE ((cmd=18 OR cmd=23 OR cmd=24 OR cmd=26 OR cmd=28) AND cmdid1=" . $id . ")");
        sql_call("DELETE FROM edomiProject.editMacroCmdList WHERE ((cmd=18 OR cmd=23 OR cmd=24 OR cmd=26 OR cmd=28) AND cmdid1=" . $id . ")");
        $ss1 = sql_call("SELECT id FROM edomiProject.editVisuPage WHERE (visuid=" . $id . ")");
        while ($page = sql_result($ss1)) {
            $ss2 = sql_call("SELECT b.targetid FROM edomiProject.editVisuElement AS a,edomiProject.editVisuCmdList AS b WHERE (a.pageid=" . $page['id'] . " AND b.targetid=a.id) GROUP BY b.targetid");
            while ($n = sql_result($ss2)) {
                sql_call("DELETE FROM edomiProject.editVisuCmdList WHERE (targetid=" . $n['targetid'] . ")");
            }
            $ss2 = sql_call("SELECT b.targetid FROM edomiProject.editVisuElement AS a,edomiProject.editVisuElementDesign AS b WHERE (a.pageid=" . $page['id'] . " AND b.targetid=a.id) GROUP BY b.targetid");
            while ($n = sql_result($ss2)) {
                sql_call("DELETE FROM edomiProject.editVisuElementDesign WHERE (targetid=" . $n['targetid'] . ")");
            }
        }
        sql_call("DELETE FROM edomiProject.editRoot WHERE (link='22' AND linkid=" . $id . ")");
        sql_call("DELETE FROM edomiProject.editVisuUserList WHERE (visuid=" . $id . ")");
        sql_call("DELETE FROM edomiProject.editVisuElement WHERE (visuid=" . $id . ")");
        sql_call("DELETE FROM edomiProject.editVisuPage WHERE (visuid=" . $id . ")");
        sql_call("DELETE FROM edomiProject.editVisu WHERE (id=" . $id . ")");
    } else if ($db == 'editVisuPage') {
        $ss1 = sql_call("SELECT b.targetid FROM edomiProject.editVisuElement AS a,edomiProject.editVisuCmdList AS b WHERE (a.pageid=" . $id . " AND b.targetid=a.id) GROUP BY b.targetid");
        while ($n = sql_result($ss1)) {
            sql_call("DELETE FROM edomiProject.editVisuCmdList WHERE (targetid=" . $n['targetid'] . ")");
        }
        $ss1 = sql_call("SELECT b.targetid FROM edomiProject.editVisuElement AS a,edomiProject.editVisuElementDesign AS b WHERE (a.pageid=" . $id . " AND b.targetid=a.id) GROUP BY b.targetid");
        while ($n = sql_result($ss1)) {
            sql_call("DELETE FROM edomiProject.editVisuElementDesign WHERE (targetid=" . $n['targetid'] . ")");
        }
        sql_call("DELETE FROM edomiProject.editVisuElement WHERE (pageid=" . $id . ")");
        sql_call("DELETE FROM edomiProject.editVisuPage WHERE (id=" . $id . ")");
        sql_call("UPDATE edomiProject.editVisuPage SET includeid=0 WHERE (includeid=" . $id . ")");
        sql_call("UPDATE edomiProject.editVisu SET sspageid=0 WHERE (sspageid=" . $id . ")");
        sql_call("UPDATE edomiProject.editVisu SET defaultpageid=NULL WHERE (defaultpageid=" . $id . ")");
        sql_call("UPDATE edomiProject.editVisu SET sspageid=NULL WHERE (sspageid=" . $id . ")");
        sql_call("UPDATE edomiProject.editVisuUserList SET defaultpageid=NULL WHERE (defaultpageid=" . $id . ")");
        sql_call("UPDATE edomiProject.editVisuUserList SET sspageid=NULL WHERE (sspageid=" . $id . ")");
        sql_call("UPDATE edomiProject.editVisuElement SET gotopageid=NULL WHERE (gotopageid=" . $id . ")");
        sql_call("UPDATE edomiProject.editVisuElement SET closepopupid=NULL WHERE (closepopupid=" . $id . ")");
        sql_call("DELETE FROM edomiProject.editVisuCmdList WHERE ((cmd=21 OR cmd=29) AND cmdid1=" . $id . ")");
        sql_call("DELETE FROM edomiProject.editLogicCmdList WHERE ((cmd=21 OR cmd=29) AND cmdid1=" . $id . ")");
        sql_call("DELETE FROM edomiProject.editSequenceCmdList WHERE ((cmd=21 OR cmd=29) AND cmdid1=" . $id . ")");
        sql_call("DELETE FROM edomiProject.editMacroCmdList WHERE ((cmd=21 OR cmd=29) AND cmdid1=" . $id . ")");
    } else if ($db == 'editVisuUser') {
        sql_call("DELETE FROM edomiProject.editVisuUserList WHERE (targetid=" . $id . ")");
        sql_call("DELETE FROM edomiProject.editVisuUser WHERE (id=" . $id . ")");
        sql_call("DELETE FROM edomiProject.editVisuUserList WHERE (targetid=" . $id . ")");
        sql_call("DELETE FROM edomiProject.editVisuCmdList WHERE ((cmd=18 OR cmd=21 OR cmd=23 OR cmd=28 OR cmd=29) AND cmdid2=" . $id . ") OR ((cmd=25 OR cmd=27) AND cmdid1=" . $id . ")");
        sql_call("DELETE FROM edomiProject.editLogicCmdList WHERE ((cmd=18 OR cmd=21 OR cmd=23 OR cmd=28 OR cmd=29) AND cmdid2=" . $id . ") OR ((cmd=25 OR cmd=27) AND cmdid1=" . $id . ")");
        sql_call("DELETE FROM edomiProject.editSequenceCmdList WHERE ((cmd=18 OR cmd=21 OR cmd=23 OR cmd=28 OR cmd=29) AND cmdid2=" . $id . ") OR ((cmd=25 OR cmd=27) AND cmdid1=" . $id . ")");
        sql_call("DELETE FROM edomiProject.editMacroCmdList WHERE ((cmd=18 OR cmd=21 OR cmd=23 OR cmd=28 OR cmd=29) AND cmdid2=" . $id . ") OR ((cmd=25 OR cmd=27) AND cmdid1=" . $id . ")");
    } else if ($db == 'editVisuElementDesignDef') {
        sql_call("DELETE FROM edomiProject.editVisuElementDesignDef WHERE (id=" . $id . ")");
        sql_call("DELETE FROM edomiProject.editVisuElementDesign WHERE (defid=" . $id . ")");
    } else if ($db == 'editVisuBGcol') {
        sql_call("DELETE FROM edomiProject.editVisuBGcol WHERE (id=" . $id . ")");
        sql_call("UPDATE edomiProject.editVisuPage SET bgcolorid=NULL WHERE (bgcolorid=" . $id . ")");
        sql_call("UPDATE edomiProject.editVisuElementDesign SET s9=NULL WHERE (s9=" . $id . ")");
        sql_call("UPDATE edomiProject.editVisuElementDesign SET s44=NULL WHERE (s44=" . $id . ")");
        sql_call("UPDATE edomiProject.editVisuElementDesign SET s45=NULL WHERE (s45=" . $id . ")");
        sql_call("UPDATE edomiProject.editVisuElementDesignDef SET s9=NULL WHERE (s9=" . $id . ")");
        sql_call("UPDATE edomiProject.editVisuElementDesignDef SET s44=NULL WHERE (s44=" . $id . ")");
        sql_call("UPDATE edomiProject.editVisuElementDesignDef SET s45=NULL WHERE (s45=" . $id . ")");
        sql_call("UPDATE edomiProject.editVisuFormat SET bgid=NULL WHERE (bgid=" . $id . ")");
    } else if ($db == 'editVisuFGcol') {
        sql_call("DELETE FROM edomiProject.editVisuFGcol WHERE (id=" . $id . ")");
        sql_call("UPDATE edomiProject.editVisuElementDesign SET s15=NULL WHERE (s15=" . $id . ")");
        sql_call("UPDATE edomiProject.editVisuElementDesign SET s22=NULL WHERE (s22=" . $id . ")");
        sql_call("UPDATE edomiProject.editVisuElementDesign SET s27=NULL WHERE (s27=" . $id . ")");
        sql_call("UPDATE edomiProject.editVisuElementDesign SET s28=NULL WHERE (s28=" . $id . ")");
        sql_call("UPDATE edomiProject.editVisuElementDesign SET s29=NULL WHERE (s29=" . $id . ")");
        sql_call("UPDATE edomiProject.editVisuElementDesign SET s30=NULL WHERE (s30=" . $id . ")");
        sql_call("UPDATE edomiProject.editVisuElementDesign SET s37=NULL WHERE (s37=" . $id . ")");
        sql_call("UPDATE edomiProject.editVisuElementDesign SET s42=NULL WHERE (s42=" . $id . ")");
        sql_call("UPDATE edomiProject.editVisuElementDesign SET s43=NULL WHERE (s43=" . $id . ")");
        sql_call("UPDATE edomiProject.editVisuElementDesignDef SET s15=NULL WHERE (s15=" . $id . ")");
        sql_call("UPDATE edomiProject.editVisuElementDesignDef SET s22=NULL WHERE (s22=" . $id . ")");
        sql_call("UPDATE edomiProject.editVisuElementDesignDef SET s27=NULL WHERE (s27=" . $id . ")");
        sql_call("UPDATE edomiProject.editVisuElementDesignDef SET s28=NULL WHERE (s28=" . $id . ")");
        sql_call("UPDATE edomiProject.editVisuElementDesignDef SET s29=NULL WHERE (s29=" . $id . ")");
        sql_call("UPDATE edomiProject.editVisuElementDesignDef SET s30=NULL WHERE (s30=" . $id . ")");
        sql_call("UPDATE edomiProject.editVisuElementDesignDef SET s37=NULL WHERE (s37=" . $id . ")");
        sql_call("UPDATE edomiProject.editVisuElementDesignDef SET s42=NULL WHERE (s42=" . $id . ")");
        sql_call("UPDATE edomiProject.editVisuElementDesignDef SET s43=NULL WHERE (s43=" . $id . ")");
        sql_call("UPDATE edomiProject.editChartList SET s1=NULL WHERE (s1=" . $id . ")");
        sql_call("UPDATE edomiProject.editChartList SET ss1=NULL WHERE (ss1=" . $id . ")");
        sql_call("UPDATE edomiProject.editVisu SET indicolor=NULL WHERE (indicolor=" . $id . ")");
        sql_call("UPDATE edomiProject.editVisu SET indicolor2=NULL WHERE (indicolor2=" . $id . ")");
        sql_call("UPDATE edomiProject.editVisuFormat SET fgid=NULL WHERE (fgid=" . $id . ")");
    } else if ($db == 'editVisuAnim') {
        sql_call("DELETE FROM edomiProject.editVisuAnim WHERE (id=" . $id . ")");
        sql_call("UPDATE edomiProject.editVisuElementDesign SET s39=NULL,s40=NULL,s41=NULL WHERE (s39=" . $id . ")");
        sql_call("UPDATE edomiProject.editVisuElementDesignDef SET s39=NULL,s40=NULL,s41=NULL WHERE (s39=" . $id . ")");
    } else if ($db == 'editVisuImg') {
        deleteFiles(MAIN_PATH . '/www/data/project/visu/img/img-' . $id . '.*');
        sql_call("DELETE FROM edomiProject.editVisuImg WHERE (id=" . $id . ")");
        sql_call("UPDATE edomiProject.editVisuPage SET bgimg=0 WHERE (bgimg=" . $id . ")");
        sql_call("UPDATE edomiProject.editVisuElementDesign SET s10=NULL WHERE (s10=" . $id . ")");
        sql_call("UPDATE edomiProject.editVisuElementDesign SET s46=NULL WHERE (s46=" . $id . ")");
        sql_call("UPDATE edomiProject.editVisuElementDesign SET s47=NULL WHERE (s47=" . $id . ")");
        sql_call("UPDATE edomiProject.editVisuElementDesignDef SET s10=NULL WHERE (s10=" . $id . ")");
        sql_call("UPDATE edomiProject.editVisuElementDesignDef SET s46=NULL WHERE (s46=" . $id . ")");
        sql_call("UPDATE edomiProject.editVisuElementDesignDef SET s47=NULL WHERE (s47=" . $id . ")");
        sql_call("UPDATE edomiProject.editVisuFormat SET imgid=NULL WHERE (imgid=" . $id . ")");
    } else if ($db == 'editVisuSnd') {
        deleteFiles(MAIN_PATH . '/www/data/project/visu/etc/snd-' . $id . '.mp3');
        sql_call("DELETE FROM edomiProject.editVisuSnd WHERE (id=" . $id . ")");
        sql_call("DELETE FROM edomiProject.editVisuCmdList WHERE ((cmd=24 OR cmd=25) AND cmdid2=" . $id . ")");
        sql_call("DELETE FROM edomiProject.editLogicCmdList WHERE ((cmd=24 OR cmd=25) AND cmdid2=" . $id . ")");
        sql_call("DELETE FROM edomiProject.editSequenceCmdList WHERE ((cmd=24 OR cmd=25) AND cmdid2=" . $id . ")");
        sql_call("DELETE FROM edomiProject.editMacroCmdList WHERE ((cmd=24 OR cmd=25) AND cmdid2=" . $id . ")");
    } else if ($db == 'editVisuFont') {
        deleteFiles(MAIN_PATH . '/www/data/project/visu/etc/font-' . $id . '.ttf');
        sql_call("DELETE FROM edomiProject.editVisuFont WHERE (id=" . $id . ")");
        sql_call("UPDATE edomiProject.editVisuElementDesign SET s13=NULL WHERE (s13=" . $id . ")");
        sql_call("UPDATE edomiProject.editVisuElementDesignDef SET s13=NULL WHERE (s13=" . $id . ")");
    } else if ($db == 'editKo') {
        sql_call("DELETE FROM edomiProject.editKo WHERE (id=" . $id . ")");
        sql_call("UPDATE edomiProject.editLogicLink SET linktyp=2,linkid=NULL WHERE (linktyp=0 AND linkid=" . $id . ")");
        sql_call("DELETE FROM edomiProject.editLogicCmdList WHERE (cmd>0 AND cmd<10 AND cmdid1=" . $id . ") OR ((cmd=3 OR cmd=6 OR cmd=42) AND cmdid2=" . $id . ")");
        sql_call("DELETE FROM edomiProject.editVisuCmdList WHERE (cmd>0 AND cmd<10 AND cmdid1=" . $id . ") OR ((cmd=3 OR cmd=6 OR cmd=42) AND cmdid2=" . $id . ")");
        sql_call("UPDATE edomiProject.editVisuElement SET gaid=NULL WHERE (gaid=" . $id . ")");
        sql_call("UPDATE edomiProject.editVisuElement SET gaid2=NULL WHERE (gaid2=" . $id . ")");
        sql_call("UPDATE edomiProject.editVisuElement SET gaid3=NULL WHERE (gaid3=" . $id . ")");
        sql_call("UPDATE edomiProject.editAws SET gaid=NULL WHERE (gaid=" . $id . ")");
        sql_call("DELETE FROM edomiProject.editSequenceCmdList WHERE (cmd>0 AND cmd<10 AND cmdid1=" . $id . ") OR ((cmd=3 OR cmd=6 OR cmd=42) AND cmdid2=" . $id . ")");
        sql_call("DELETE FROM edomiProject.editMacroCmdList WHERE (cmd>0 AND cmd<10 AND cmdid1=" . $id . ") OR ((cmd=3 OR cmd=6 OR cmd=42) AND cmdid2=" . $id . ")");
        sql_call("DELETE FROM edomiProject.editSceneList WHERE (gaid=" . $id . ")");
        sql_call("UPDATE edomiProject.editSceneList SET learngaid=NULL WHERE (learngaid=" . $id . ")");
        sql_call("UPDATE edomiProject.editSceneList SET valuegaid=NULL WHERE (valuegaid=" . $id . ")");
        sql_call("UPDATE edomiProject.editTimer SET gaid=NULL WHERE (gaid=" . $id . ")");
        sql_call("UPDATE edomiProject.editTimer SET gaid2=NULL WHERE (gaid2=" . $id . ")");
        sql_call("UPDATE edomiProject.editAgenda SET gaid=NULL WHERE (gaid=" . $id . ")");
        sql_call("UPDATE edomiProject.editAgenda SET gaid2=NULL WHERE (gaid2=" . $id . ")");
        sql_call("UPDATE edomiProject.editArchivCam SET outgaid=NULL WHERE (outgaid=" . $id . ")");
        sql_call("UPDATE edomiProject.editArchivCam SET outgaid2=NULL WHERE (outgaid2=" . $id . ")");
        sql_call("UPDATE edomiProject.editCam SET dvrgaid=NULL WHERE (dvrgaid=" . $id . ")");
        sql_call("UPDATE edomiProject.editCam SET dvrgaid2=NULL WHERE (dvrgaid2=" . $id . ")");
        sql_call("UPDATE edomiProject.editArchivKo SET outgaid=NULL WHERE (outgaid=" . $id . ")");
        sql_call("UPDATE edomiProject.editArchivMsg SET outgaid=NULL WHERE (outgaid=" . $id . ")");
        sql_call("UPDATE edomiProject.editArchivPhone SET outgaid=NULL WHERE (outgaid=" . $id . ")");
    } else if ($db == 'editScene') {
        sql_call("DELETE FROM edomiProject.editSceneList WHERE (targetid=" . $id . ")");
        sql_call("DELETE FROM edomiProject.editScene WHERE (id=" . $id . ")");
        sql_call("DELETE FROM edomiProject.editVisuCmdList WHERE (cmd=10 AND cmdid1=" . $id . ")");
        sql_call("DELETE FROM edomiProject.editLogicCmdList WHERE (cmd=10 AND cmdid1=" . $id . ")");
        sql_call("DELETE FROM edomiProject.editSequenceCmdList WHERE (cmd=10 AND cmdid1=" . $id . ")");
        sql_call("DELETE FROM edomiProject.editMacroCmdList WHERE (cmd=10 AND cmdid1=" . $id . ")");
    } else if ($db == 'editSceneList') {
        sql_call("DELETE FROM edomiProject.editSceneList WHERE (id=" . $id . ")");
    } else if ($db == 'editArchivKo') {
        sql_call("DELETE FROM edomiProject.editArchivKo WHERE (id=" . $id . ")");
        sql_call("DELETE FROM edomiProject.editVisuCmdList WHERE ((cmd=13 OR cmd=40 OR cmd=42 OR cmd=50) AND cmdid1=" . $id . ")");
        sql_call("DELETE FROM edomiProject.editLogicCmdList WHERE ((cmd=13 OR cmd=40 OR cmd=42 OR cmd=50) AND cmdid1=" . $id . ")");
        sql_call("DELETE FROM edomiProject.editSequenceCmdList WHERE ((cmd=13 OR cmd=40 OR cmd=42 OR cmd=50) AND cmdid1=" . $id . ")");
        sql_call("DELETE FROM edomiProject.editMacroCmdList WHERE ((cmd=13 OR cmd=40 OR cmd=42 OR cmd=50) AND cmdid1=" . $id . ")");
        sql_call("DELETE FROM edomiProject.editChartList WHERE (archivkoid=" . $id . ")");
    } else if ($db == 'editArchivMsg') {
        sql_call("DELETE FROM edomiProject.editArchivMsg WHERE (id=" . $id . ")");
        sql_call("DELETE FROM edomiProject.editVisuCmdList WHERE ((cmd=14 OR cmd=41 OR cmd=51) AND cmdid1=" . $id . ")");
        sql_call("DELETE FROM edomiProject.editLogicCmdList WHERE ((cmd=14 OR cmd=41 OR cmd=51) AND cmdid1=" . $id . ")");
        sql_call("DELETE FROM edomiProject.editSequenceCmdList WHERE ((cmd=14 OR cmd=41 OR cmd=51) AND cmdid1=" . $id . ")");
        sql_call("DELETE FROM edomiProject.editMacroCmdList WHERE ((cmd=14 OR cmd=41 OR cmd=51) AND cmdid1=" . $id . ")");
    } else if ($db == 'editIp') {
        sql_call("DELETE FROM edomiProject.editIp WHERE (id=" . $id . ")");
        sql_call("DELETE FROM edomiProject.editVisuCmdList WHERE (cmd=15 AND cmdid1=" . $id . ")");
        sql_call("DELETE FROM edomiProject.editLogicCmdList WHERE (cmd=15 AND cmdid1=" . $id . ")");
        sql_call("DELETE FROM edomiProject.editSequenceCmdList WHERE (cmd=15 AND cmdid1=" . $id . ")");
        sql_call("DELETE FROM edomiProject.editMacroCmdList WHERE (cmd=15 AND cmdid1=" . $id . ")");
    } else if ($db == 'editIr') {
        sql_call("DELETE FROM edomiProject.editIr WHERE (id=" . $id . ")");
        sql_call("DELETE FROM edomiProject.editVisuCmdList WHERE (cmd=16 AND cmdid1=" . $id . ")");
        sql_call("DELETE FROM edomiProject.editLogicCmdList WHERE (cmd=16 AND cmdid1=" . $id . ")");
        sql_call("DELETE FROM edomiProject.editSequenceCmdList WHERE (cmd=16 AND cmdid1=" . $id . ")");
        sql_call("DELETE FROM edomiProject.editMacroCmdList WHERE (cmd=16 AND cmdid1=" . $id . ")");
    } else if ($db == 'editCam') {
        sql_call("DELETE FROM edomiProject.editCam WHERE (id=" . $id . ")");
        sql_call("UPDATE edomiProject.editArchivCam SET camid=0 WHERE (camid=" . $id . ")");
        sql_call("UPDATE edomiProject.editCamView SET camid=0 WHERE (camid=" . $id . ")");
    } else if ($db == 'editCamView') {
        sql_call("DELETE FROM edomiProject.editCamView WHERE (id=" . $id . ")");
    } else if ($db == 'editArchivCam') {
        sql_call("DELETE FROM edomiProject.editArchivCam WHERE (id=" . $id . ")");
        sql_call("DELETE FROM edomiProject.editVisuCmdList WHERE ((cmd=12 OR cmd=52) AND cmdid1=" . $id . ")");
        sql_call("DELETE FROM edomiProject.editLogicCmdList WHERE ((cmd=12 OR cmd=52) AND cmdid1=" . $id . ")");
        sql_call("DELETE FROM edomiProject.editSequenceCmdList WHERE ((cmd=12 OR cmd=52) AND cmdid1=" . $id . ")");
        sql_call("DELETE FROM edomiProject.editMacroCmdList WHERE ((cmd=12 OR cmd=52) AND cmdid1=" . $id . ")");
    } else if ($db == 'editVisuFormat') {
        sql_call("DELETE FROM edomiProject.editVisuFormat WHERE (id=" . $id . ")");
        sql_call("UPDATE edomiProject.editVisuCmdList SET cmdid2=0 WHERE ((cmd=14 OR cmd=41) AND cmdid2=" . $id . ")");
        sql_call("UPDATE edomiProject.editLogicCmdList SET cmdid2=0 WHERE ((cmd=14 OR cmd=41) AND cmdid2=" . $id . ")");
        sql_call("UPDATEedomiProject.editSequenceCmdList SET cmdid2=0 WHERE ((cmd=14 OR cmd=41) AND cmdid2=" . $id . ")");
        sql_call("UPDATE edomiProject.editMacroCmdList SET cmdid2=0 WHERE ((cmd=14 OR cmd=41) AND cmdid2=" . $id . ")");
    } else if ($db == 'editSequence') {
        sql_call("DELETE FROM edomiProject.editSequenceCmdList WHERE (targetid=" . $id . ")");
        sql_call("DELETE FROM edomiProject.editSequence WHERE (id=" . $id . ")");
        sql_call("DELETE FROM edomiProject.editVisuCmdList WHERE (cmd=11 AND cmdid1=" . $id . ")");
        sql_call("DELETE FROM edomiProject.editLogicCmdList WHERE (cmd=11 AND cmdid1=" . $id . ")");
        sql_call("DELETE FROM edomiProject.editSequenceCmdList WHERE (cmd=11 AND cmdid1=" . $id . ")");
        sql_call("DELETE FROM edomiProject.editMacroCmdList WHERE (cmd=11 AND cmdid1=" . $id . ")");
    } else if ($db == 'editMacro') {
        sql_call("DELETE FROM edomiProject.editMacroCmdList WHERE (targetid=" . $id . ")");
        sql_call("DELETE FROM edomiProject.editMacro WHERE (id=" . $id . ")");
        sql_call("UPDATE edomiProject.editTimerData SET cmdid=0 WHERE (cmdid=" . $id . ")");
        sql_call("UPDATE edomiProject.editAgendaData SET cmdid=0 WHERE (cmdid=" . $id . ")");
        sql_call("DELETE FROM edomiProject.editTimerMacroList WHERE (targetid=" . $id . ")");
        sql_call("DELETE FROM edomiProject.editAgendaMacroList WHERE (targetid=" . $id . ")");
        sql_call("DELETE FROM edomiProject.editVisuCmdList WHERE (cmd=17 AND cmdid1=" . $id . ")");
        sql_call("DELETE FROM edomiProject.editLogicCmdList WHERE (cmd=17 AND cmdid1=" . $id . ")");
        sql_call("DELETE FROM edomiProject.editSequenceCmdList WHERE (cmd=17 AND cmdid1=" . $id . ")");
        sql_call("DELETE FROM edomiProject.editMacroCmdList WHERE (cmd=17 AND cmdid1=" . $id . ")");
    } else if ($db == 'editTimer') {
        sql_call("DELETE FROM edomiProject.editTimerData WHERE (targetid=" . $id . ")");
        sql_call("DELETE FROM edomiProject.editTimer WHERE (id=" . $id . ")");
        sql_call("DELETE FROM edomiProject.editTimerMacroList WHERE (timerid=" . $id . ")");
    } else if ($db == 'editAgenda') {
        sql_call("DELETE FROM edomiProject.editAgendaData WHERE (targetid=" . $id . ")");
        sql_call("DELETE FROM edomiProject.editAgenda WHERE (id=" . $id . ")");
        sql_call("DELETE FROM edomiProject.editAgendaMacroList WHERE (agendaid=" . $id . ")");
    } else if ($db == 'editAws') {
        sql_call("DELETE FROM edomiProject.editAwsList WHERE (targetid=" . $id . ")");
        sql_call("DELETE FROM edomiProject.editAws WHERE (id=" . $id . ")");
    } else if ($db == 'editEmail') {
        sql_call("DELETE FROM edomiProject.editEmail WHERE (id=" . $id . ")");
        sql_call("DELETE FROM edomiProject.editVisuCmdList WHERE (cmd=20 AND cmdid1=" . $id . ")");
        sql_call("DELETE FROM edomiProject.editLogicCmdList WHERE (cmd=20 AND cmdid1=" . $id . ")");
        sql_call("DELETE FROM edomiProject.editSequenceCmdList WHERE (cmd=20 AND cmdid1=" . $id . ")");
        sql_call("DELETE FROM edomiProject.editMacroCmdList WHERE (cmd=20 AND cmdid1=" . $id . ")");
    } else if ($db == 'editPhoneBook') {
        sql_call("DELETE FROM edomiProject.editPhoneBook WHERE (id=" . $id . ")");
        sql_call("DELETE FROM edomiProject.editVisuCmdList WHERE (cmd=22 AND cmdid1=" . $id . ")");
        sql_call("DELETE FROM edomiProject.editLogicCmdList WHERE (cmd=22 AND cmdid1=" . $id . ")");
        sql_call("DELETE FROM edomiProject.editSequenceCmdList WHERE (cmd=22 AND cmdid1=" . $id . ")");
        sql_call("DELETE FROM edomiProject.editMacroCmdList WHERE (cmd=22 AND cmdid1=" . $id . ")");
    } else if ($db == 'editPhoneCall') {
        sql_call("DELETE FROM edomiProject.editPhoneCall WHERE (id=" . $id . ")");
    } else if ($db == 'editArchivPhone') {
        sql_call("DELETE FROM edomiProject.editArchivPhone WHERE (id=" . $id . ")");
        sql_call("DELETE FROM edomiProject.editVisuCmdList WHERE (cmd=53 AND cmdid1=" . $id . ")");
        sql_call("DELETE FROM edomiProject.editLogicCmdList WHERE (cmd=53 AND cmdid1=" . $id . ")");
        sql_call("DELETE FROM edomiProject.editSequenceCmdList WHERE (cmd=53 AND cmdid1=" . $id . ")");
        sql_call("DELETE FROM edomiProject.editMacroCmdList WHERE (cmd=53 AND cmdid1=" . $id . ")");
    } else if ($db == 'editChart') {
        sql_call("DELETE FROM edomiProject.editChartList WHERE (targetid=" . $id . ")");
        sql_call("DELETE FROM edomiProject.editChart WHERE (id=" . $id . ")");
    } else if ($db == 'editHttpKo') {
        sql_call("DELETE FROM edomiProject.editHttpKo WHERE (id=" . $id . ")");
    } else if ($db == 'editVisuElementDesign') {
        sql_call("DELETE FROM edomiProject.editVisuElementDesign WHERE (id=" . $id . ")");
    } else if ($db == 'editLogicCmdList' || $db == 'editVisuCmdList' || $db == 'editSequenceCmdList' || $db == 'editMacroCmdList') {
        sql_call("DELETE FROM edomiProject." . $db . " WHERE (id=" . $id . ")");
    } else if ($db == 'editChartList') {
        sql_call("DELETE FROM edomiProject.editChartList WHERE (id=" . $id . ")");
    } else if ($db == 'editAwsList') {
        sql_call("DELETE FROM edomiProject.editAwsList WHERE (id=" . $id . ")");
    } else if ($db == 'editVisuUserList') {
        sql_call("DELETE FROM edomiProject.editVisuUserList WHERE (id=" . $id . ")");
    } else if ($db == 'editTimerData') {
        sql_call("DELETE FROM edomiProject.editTimerData WHERE (id=" . $id . ")");
    } else if ($db == 'editTimerMacroList') {
        sql_call("DELETE FROM edomiProject.editTimerMacroList WHERE (id=" . $id . ")");
    } else if ($db == 'editAgendaData') {
        sql_call("DELETE FROM edomiProject.editAgendaData WHERE (id=" . $id . ")");
    } else if ($db == 'editAgendaMacroList') {
        sql_call("DELETE FROM edomiProject.editAgendaMacroList WHERE (id=" . $id . ")");
    } else if ($db == 'editLogicElement') {
        sql_call("DELETE FROM edomiProject.editLogicElement WHERE (id=" . $id . ")");
        sql_call("UPDATE edomiProject.editLogicLink SET linktyp=2,linkid=null,ausgang=null WHERE (linktyp=1 AND linkid=" . $id . ")");
        sql_call("DELETE FROM edomiProject.editLogicLink WHERE (elementid=" . $id . ")");
        sql_call("DELETE FROM edomiProject.editLogicElementVar WHERE (elementid=" . $id . ")");
        sql_call("DELETE FROM edomiProject.editLogicCmdList WHERE (targetid=" . $id . ")");
    } else if ($db == 'editVisuElement') {
        $tmp = sql_getValue('edomiProject.editVisuElement', 'id', 'id=' . $id . ' AND controltyp=0');
        if (!isEmpty($tmp)) {
            sql_call("UPDATE edomiProject.editVisuElement SET groupid=0 WHERE groupid=" . $id);
        }
        sql_call("DELETE FROM edomiProject.editVisuElement WHERE (id=" . $id . ")");
        sql_call("DELETE FROM edomiProject.editVisuElementDesign WHERE (targetid=" . $id . ")");
        sql_call("DELETE FROM edomiProject.editVisuCmdList WHERE (targetid=" . $id . ")");
        $ss1 = sql_call("SELECT id,linkid FROM edomiProject.editVisuElement AS a WHERE a.linkid>0 HAVING a.linkid NOT IN (SELECT id FROM edomiProject.editVisuElement WHERE id=a.linkid)");
        while ($tmp = sql_result($ss1)) {
            sql_call("UPDATE edomiProject.editVisuElement SET linkid=0 WHERE id=" . $tmp['id']);
        }
        sql_close($ss1);
    }
}

function db_itemDuplicate($db, $id, $targetFolderId = 0, $nameSuffix = '')
{
    if (!global_duplicateSuffix) {
        $nameSuffix = '';
    }
    if ($db == 'editPhoneCall') {
        $ss1 = sql_call("SELECT * FROM edomiProject.editPhoneCall WHERE (id=" . $id . ")");
        if ($original = sql_result($ss1)) {
            sql_call("INSERT INTO edomiProject.editPhoneCall (name,folderid,phoneid1,phoneid2,gaid1,gaid2,gaid3,typ) VALUES (
				'" . sql_encodeValue($original['name'] . $nameSuffix) . "',
				'" . (($targetFolderId > 0) ? $targetFolderId : $original['folderid']) . "',
				'" . $original['phoneid1'] . "',
				'" . $original['phoneid2'] . "',
				'" . $original['gaid1'] . "',
				'" . $original['gaid2'] . "',
				'" . $original['gaid3'] . "',
				'" . $original['typ'] . "'
			)");
            $dbId = sql_insertId();
            return $dbId;
        }
    } else if ($db == 'editVisuFormat') {
        $ss1 = sql_call("SELECT * FROM edomiProject.editVisuFormat WHERE (id=" . $id . ")");
        if ($original = sql_result($ss1)) {
            sql_call("INSERT INTO edomiProject.editVisuFormat (name,folderid,fgid,bgid,imgid) VALUES (
				'" . sql_encodeValue($original['name'] . $nameSuffix) . "',
				'" . (($targetFolderId > 0) ? $targetFolderId : $original['folderid']) . "',
				'" . $original['fgid'] . "',
				'" . $original['bgid'] . "',
				'" . $original['imgid'] . "'
			)");
            $dbId = sql_insertId();
            return $dbId;
        }
    } else if ($db == 'editEmail') {
        $ss1 = sql_call("SELECT * FROM edomiProject.editEmail WHERE (id=" . $id . ")");
        if ($original = sql_result($ss1)) {
            sql_call("INSERT INTO edomiProject.editEmail (name,folderid,mailaddr,subject,body) VALUES (
				'" . sql_encodeValue($original['name'] . $nameSuffix) . "',
				'" . (($targetFolderId > 0) ? $targetFolderId : $original['folderid']) . "',
				" . sql_encodeValue($original['mailaddr'], true) . ",
				" . sql_encodeValue($original['subject'], true) . ",
				" . sql_encodeValue($original['body'], true) . "
			)");
            $dbId = sql_insertId();
            return $dbId;
        }
    } else if ($db == 'editIr') {
        $ss1 = sql_call("SELECT * FROM edomiProject.editIr WHERE (id=" . $id . ")");
        if ($original = sql_result($ss1)) {
            sql_call("INSERT INTO edomiProject.editIr (name,folderid,data,info) VALUES (
				'" . sql_encodeValue($original['name'] . $nameSuffix) . "',
				'" . (($targetFolderId > 0) ? $targetFolderId : $original['folderid']) . "',
				" . sql_encodeValue($original['data'], true) . ",
				" . sql_encodeValue($original['info'], true) . "
			)");
            $dbId = sql_insertId();
            return $dbId;
        }
    } else if ($db == 'editCam') {
        $ss1 = sql_call("SELECT * FROM edomiProject.editCam WHERE (id=" . $id . ")");
        if ($original = sql_result($ss1)) {
            sql_call("INSERT INTO edomiProject.editCam (name,folderid,cachets,url,mjpeg,dvr,dvrrate,dvrkeep,dvrgaid,dvrgaid2) VALUES (
				'" . sql_encodeValue($original['name'] . $nameSuffix) . "',
				'" . (($targetFolderId > 0) ? $targetFolderId : $original['folderid']) . "',
				" . sql_encodeValue($original['cachets'], true) . ",
				" . sql_encodeValue($original['url'], true) . ",
				'" . $original['mjpeg'] . "',
				'" . $original['dvr'] . "',
				'" . $original['dvrrate'] . "',
				'" . $original['dvrkeep'] . "',
				'" . $original['dvrgaid'] . "',
				'" . $original['dvrgaid2'] . "'
			)");
            $dbId = sql_insertId();
            return $dbId;
        }
    } else if ($db == 'editCamView') {
        $ss1 = sql_call("SELECT * FROM edomiProject.editCamView WHERE (id=" . $id . ")");
        if ($original = sql_result($ss1)) {
            sql_call("INSERT INTO edomiProject.editCamView (name,folderid,camid,srctyp,zoom,a1,a2,x,y,dstw,dsth,dsts,srcr,srcd,srcs) VALUES (
				'" . sql_encodeValue($original['name'] . $nameSuffix) . "',
				'" . (($targetFolderId > 0) ? $targetFolderId : $original['folderid']) . "',
				'" . $original['camid'] . "',
				'" . $original['srctyp'] . "',
				'" . $original['zoom'] . "',
				'" . $original['a1'] . "',
				'" . $original['a2'] . "',
				'" . $original['x'] . "',
				'" . $original['y'] . "',
				'" . $original['dstw'] . "',
				'" . $original['dsth'] . "',
				'" . $original['dsts'] . "',
				'" . $original['srcr'] . "',
				'" . $original['srcd'] . "',
				'" . $original['srcs'] . "'
			)");
            $dbId = sql_insertId();
            return $dbId;
        }
    } else if ($db == 'editIp') {
        $ss1 = sql_call("SELECT * FROM edomiProject.editIp WHERE (id=" . $id . ")");
        if ($original = sql_result($ss1)) {
            sql_call("INSERT INTO edomiProject.editIp (name,folderid,url,iptyp,httperrlog,httptimeout,udpraw,outgaid,outgaid2,data) VALUES (
				'" . sql_encodeValue($original['name'] . $nameSuffix) . "',
				'" . (($targetFolderId > 0) ? $targetFolderId : $original['folderid']) . "',
				" . sql_encodeValue($original['url'], true) . ",
				'" . $original['iptyp'] . "',
				'" . $original['httperrlog'] . "',
				'" . $original['httptimeout'] . "',
				'" . $original['udpraw'] . "',
				'" . $original['outgaid'] . "',
				'" . $original['outgaid2'] . "',
				" . sql_encodeValue($original['data'], true) . "
			)");
            $dbId = sql_insertId();
            return $dbId;
        }
    } else if ($db == 'editArchivMsg') {
        $ss1 = sql_call("SELECT * FROM edomiProject.editArchivMsg WHERE (id=" . $id . ")");
        if ($original = sql_result($ss1)) {
            sql_call("INSERT INTO edomiProject.editArchivMsg (name,folderid,keep,delay,outgaid) VALUES (
				'" . sql_encodeValue($original['name'] . $nameSuffix) . "',
				'" . (($targetFolderId > 0) ? $targetFolderId : $original['folderid']) . "',
				'" . $original['keep'] . "',
				'" . $original['delay'] . "',
				'" . $original['outgaid'] . "'
			)");
            $dbId = sql_insertId();
            return $dbId;
        }
    } else if ($db == 'editArchivKo') {
        $ss1 = sql_call("SELECT * FROM edomiProject.editArchivKo WHERE (id=" . $id . ")");
        if ($original = sql_result($ss1)) {
            sql_call("INSERT INTO edomiProject.editArchivKo (name,folderid,keep,delay,outgaid,cmode,cinterval,cts,clist,coffset,cunit) VALUES (
				'" . sql_encodeValue($original['name'] . $nameSuffix) . "',
				'" . (($targetFolderId > 0) ? $targetFolderId : $original['folderid']) . "',
				'" . $original['keep'] . "',
				'" . $original['delay'] . "',
				'" . $original['outgaid'] . "',
				'" . $original['cmode'] . "',
				'" . $original['cinterval'] . "',
				'" . $original['cts'] . "',
				" . sql_encodeValue($original['clist'], true) . ",
				'" . $original['coffset'] . "',
				'" . $original['cunit'] . "'
			)");
            $dbId = sql_insertId();
            return $dbId;
        }
    } else if ($db == 'editArchivPhone') {
        $ss1 = sql_call("SELECT * FROM edomiProject.editArchivPhone WHERE (id=" . $id . ")");
        if ($original = sql_result($ss1)) {
            sql_call("INSERT INTO edomiProject.editArchivPhone (name,folderid,keep,outgaid) VALUES (
				'" . sql_encodeValue($original['name'] . $nameSuffix) . "',
				'" . (($targetFolderId > 0) ? $targetFolderId : $original['folderid']) . "',
				'" . $original['keep'] . "',
				'" . $original['outgaid'] . "'
			)");
            $dbId = sql_insertId();
            return $dbId;
        }
    } else if ($db == 'editArchivCam') {
        $ss1 = sql_call("SELECT * FROM edomiProject.editArchivCam WHERE (id=" . $id . ")");
        if ($original = sql_result($ss1)) {
            sql_call("INSERT INTO edomiProject.editArchivCam (name,folderid,camid,keep,delay,outgaid,outgaid2) VALUES (
				'" . sql_encodeValue($original['name'] . $nameSuffix) . "',
				'" . (($targetFolderId > 0) ? $targetFolderId : $original['folderid']) . "',
				'" . $original['camid'] . "',
				'" . $original['keep'] . "',
				'" . $original['delay'] . "',
				'" . $original['outgaid'] . "',
				'" . $original['outgaid2'] . "'
			)");
            $dbId = sql_insertId();
            return $dbId;
        }
    } else if ($db == 'editKo') {
        $ss1 = sql_call("SELECT * FROM edomiProject.editKo WHERE (id=" . $id . ")");
        if ($original = sql_result($ss1)) {
            if ($original['gatyp'] == 2) {
                sql_call("INSERT INTO edomiProject.editKo (name,folderid,ga,gatyp,valuetyp,value,defaultvalue,endvalue,initscan,initsend,endsend,requestable,remanent,prio,vmin,vmax,vstep,vlist,vcsv,text) VALUES (
					'" . sql_encodeValue($original['name'] . $nameSuffix) . "',
					'" . (($targetFolderId > 0) ? $targetFolderId : $original['folderid']) . "',
					'',
					'" . $original['gatyp'] . "',
					'" . $original['valuetyp'] . "',
					" . sql_encodeValue($original['value'], true) . ",
					" . sql_encodeValue($original['defaultvalue'], true) . ",
					" . sql_encodeValue($original['endvalue'], true) . ",
					'" . $original['initscan'] . "',
					'" . $original['initsend'] . "',
					'" . $original['endsend'] . "',
					'" . $original['requestable'] . "',
					'" . $original['remanent'] . "',
					'" . $original['prio'] . "',
					" . sql_encodeValue($original['vmin'], true) . ",
					" . sql_encodeValue($original['vmax'], true) . ",
					" . sql_encodeValue($original['vstep'], true) . ",
					" . sql_encodeValue($original['vlist'], true) . ",
					" . sql_encodeValue($original['vcsv'], true) . ",
					" . sql_encodeValue($original['text'], true) . "
				)");
                $dbId = sql_insertId();
                if ($dbId > 0) {
                    sql_call("UPDATE edomiProject.editKo SET ga='" . $dbId . "' WHERE (id=" . $dbId . ")");
                }
                return $dbId;
            }
        }
    } else if ($db == 'editScene') {
        $ss1 = sql_call("SELECT * FROM edomiProject.editScene WHERE (id=" . $id . ")");
        if ($original = sql_result($ss1)) {
            sql_call("INSERT INTO edomiProject.editScene (name,folderid) VALUES (
				'" . sql_encodeValue($original['name'] . $nameSuffix) . "',
				'" . (($targetFolderId > 0) ? $targetFolderId : $original['folderid']) . "'
			)");
            $dbId = sql_insertId();
            if ($dbId > 0) {
                $ss2 = sql_call("SELECT * FROM edomiProject.editSceneList WHERE (targetid=" . $original['id'] . ") ORDER BY id ASC");
                while ($n = sql_result($ss2)) {
                    sql_call("INSERT INTO edomiProject.editSceneList (targetid,gaid,gavalue,learngaid,valuegaid) VALUES (
						'" . $dbId . "',
						'" . $n['gaid'] . "',
						" . sql_encodeValue($n['gavalue'], true) . ",
						'" . $n['learngaid'] . "',
						'" . $n['valuegaid'] . "'
					)");
                }
            }
            return $dbId;
        }
    } else if ($db == 'editChart') {
        $ss1 = sql_call("SELECT * FROM edomiProject.editChart WHERE (id=" . $id . ")");
        if ($original = sql_result($ss1)) {
            sql_call("INSERT INTO edomiProject.editChart (name,folderid,titel,datefrom,dateto,mode,xunit,xinterval,ymin,ymax,ynice,yticks) VALUES (
				'" . sql_encodeValue($original['name'] . $nameSuffix) . "',
				'" . (($targetFolderId > 0) ? $targetFolderId : $original['folderid']) . "',
				'" . sql_encodeValue($original['titel']) . "',
				'" . sql_encodeValue($original['datefrom']) . "',
 				'" . sql_encodeValue($original['dateto']) . "',
	 			'" . $original['mode'] . "',
	 			'" . $original['xunit'] . "',
 				" . sql_encodeValue($original['xinterval'], true) . ",
				" . sql_encodeValue($original['ymin'], true) . ",
				" . sql_encodeValue($original['ymax'], true) . ",
				" . sql_encodeValue($original['ynice'], true) . ",
				" . sql_encodeValue($original['yticks'], true) . "
			)");
            $dbId = sql_insertId();
            if ($dbId > 0) {
                $ss2 = sql_call("SELECT * FROM edomiProject.editChartList WHERE (targetid=" . $original['id'] . ") ORDER BY id ASC");
                while ($n = sql_result($ss2)) {
                    sql_call("INSERT INTO edomiProject.editChartList (targetid,archivkoid,titel,charttyp,ymin,ymax,ystyle,s1,s2,s3,s4,ygrid1,ygrid2,ygrid3,yshow,ynice,yticks,charttyp2,ss1,ss2,ss3,ss4,xinterval,yminmax,extend1,extend2,yshowvalue,yscale,sort) VALUES (
						'" . $dbId . "',
						'" . $n['archivkoid'] . "',
						'" . sql_encodeValue($n['titel']) . "',
						'" . $n['charttyp'] . "',
						" . sql_encodeValue($n['ymin'], true) . ",
						" . sql_encodeValue($n['ymax'], true) . ",
						'" . $n['ystyle'] . "',
						'" . $n['s1'] . "',
						'" . $n['s2'] . "',
						'" . $n['s3'] . "',
						'" . $n['s4'] . "',
						" . sql_encodeValue($n['ygrid1'], true) . ",
						" . sql_encodeValue($n['ygrid2'], true) . ",
						" . sql_encodeValue($n['ygrid3'], true) . ",
						" . sql_encodeValue($n['yshow'], true) . ",
						" . sql_encodeValue($n['ynice'], true) . ",
						" . sql_encodeValue($n['yticks'], true) . ",
						'" . $n['charttyp2'] . "',
						'" . $n['ss1'] . "',
						'" . $n['ss2'] . "',
						'" . $n['ss3'] . "',
						'" . $n['ss4'] . "',
						" . sql_encodeValue($n['xinterval'], true) . ",
						'" . $n['yminmax'] . "',
						'" . $n['extend1'] . "',
						'" . $n['extend2'] . "',
						'" . $n['yshowvalue'] . "',
						" . sql_encodeValue($n['yscale'], true) . ",
						'" . $n['sort'] . "'
					)");
                }
            }
            return $dbId;
        }
    } else if ($db == 'editAws') {
        $ss1 = sql_call("SELECT * FROM edomiProject.editAws WHERE (id=" . $id . ")");
        if ($original = sql_result($ss1)) {
            sql_call("INSERT INTO edomiProject.editAws (name,folderid,gaid,recordpointer,playpointer) VALUES (
				'" . sql_encodeValue($original['name'] . $nameSuffix) . "',
				'" . (($targetFolderId > 0) ? $targetFolderId : $original['folderid']) . "',
				'" . $original['gaid'] . "',
				" . sql_encodeValue($original['recordpointer'], true) . ",
				" . sql_encodeValue($original['playpointer'], true) . "
			)");
            $dbId = sql_insertId();
            if ($dbId > 0) {
                $ss2 = sql_call("SELECT * FROM edomiProject.editAwsList WHERE (targetid=" . $original['id'] . ") ORDER BY id ASC");
                while ($n = sql_result($ss2)) {
                    sql_call("INSERT INTO edomiProject.editAwsList (targetid,gaid,gaid2,gavalue1,gavalue2) VALUES (
						'" . $dbId . "',
						'" . $n['gaid'] . "',
						'" . $n['gaid2'] . "',
						" . sql_encodeValue($n['gavalue1'], true) . ",
						" . sql_encodeValue($n['gavalue2'], true) . "
					)");
                }
            }
            return $dbId;
        }
    } else if ($db == 'editTimer') {
        $ss1 = sql_call("SELECT * FROM edomiProject.editTimer WHERE (id=" . $id . ")");
        if ($original = sql_result($ss1)) {
            sql_call("INSERT INTO edomiProject.editTimer (name,folderid,gaid,gaid2) VALUES (
				'" . sql_encodeValue($original['name'] . $nameSuffix) . "',
				'" . (($targetFolderId > 0) ? $targetFolderId : $original['folderid']) . "',
				'" . $original['gaid'] . "',
				'" . $original['gaid2'] . "'
			)");
            $dbId = sql_insertId();
            if ($dbId > 0) {
                $ss2 = sql_call("SELECT * FROM edomiProject.editTimerData WHERE (targetid=" . $original['id'] . ") ORDER BY id ASC");
                while ($n = sql_result($ss2)) {
                    sql_call("INSERT INTO edomiProject.editTimerData (targetid,fixed,cmdid,hour,minute,day1,day2,month1,month2,year1,year2,mode,d0,d1,d2,d3,d4,d5,d6,d7) VALUES (
						'" . $dbId . "',
						'" . $n['fixed'] . "',
						'" . $n['cmdid'] . "',
						'" . sql_encodeValue($n['hour']) . "',
						'" . sql_encodeValue($n['minute']) . "',
						" . sql_encodeValue($n['day1'], true) . ",
						" . sql_encodeValue($n['day2'], true) . ",
						" . sql_encodeValue($n['month1'], true) . ",
						" . sql_encodeValue($n['month2'], true) . ",
						" . sql_encodeValue($n['year1'], true) . ",
						" . sql_encodeValue($n['year2'], true) . ",
						'" . sql_encodeValue($n['mode']) . "',
						'" . sql_encodeValue($n['d0']) . "',
						'" . sql_encodeValue($n['d1']) . "',
						'" . sql_encodeValue($n['d2']) . "',
						'" . sql_encodeValue($n['d3']) . "',
						'" . sql_encodeValue($n['d4']) . "',
						'" . sql_encodeValue($n['d5']) . "',
						'" . sql_encodeValue($n['d6']) . "',
						'" . sql_encodeValue($n['d7']) . "'
					)");
                }
                $ss2 = sql_call("SELECT * FROM edomiProject.editTimerMacroList WHERE (timerid=" . $original['id'] . ") ORDER BY id ASC");
                while ($n = sql_result($ss2)) {
                    sql_call("INSERT INTO edomiProject.editTimerMacroList (timerid,targetid,sort) VALUES (
						'" . $dbId . "',
						'" . $n['targetid'] . "',
						'" . $n['sort'] . "'
					)");
                }
            }
            return $dbId;
        }
    } else if ($db == 'editTimerData') {
        $ss1 = sql_call("SELECT * FROM edomiProject.editTimerData WHERE (id=" . $id . ")");
        if ($original = sql_result($ss1)) {
            sql_call("INSERT INTO edomiProject.editTimerData (targetid,fixed,cmdid,hour,minute,day1,day2,month1,month2,year1,year2,mode,d0,d1,d2,d3,d4,d5,d6,d7) VALUES (
				" . $original['targetid'] . ",
				'" . $original['fixed'] . "',
				'" . $original['cmdid'] . "',
				'" . sql_encodeValue($original['hour']) . "',
				'" . sql_encodeValue($original['minute']) . "',
				" . sql_encodeValue($original['day1'], true) . ",
				" . sql_encodeValue($original['day2'], true) . ",
				" . sql_encodeValue($original['month1'], true) . ",
				" . sql_encodeValue($original['month2'], true) . ",
				" . sql_encodeValue($original['year1'], true) . ",
				" . sql_encodeValue($original['year2'], true) . ",
				'" . sql_encodeValue($original['mode']) . "',
				'" . sql_encodeValue($original['d0']) . "',
				'" . sql_encodeValue($original['d1']) . "',
				'" . sql_encodeValue($original['d2']) . "',
				'" . sql_encodeValue($original['d3']) . "',
				'" . sql_encodeValue($original['d4']) . "',
				'" . sql_encodeValue($original['d5']) . "',
				'" . sql_encodeValue($original['d6']) . "',
				'" . sql_encodeValue($original['d7']) . "'
			)");
            $dbId = sql_insertId();
            return $dbId;
        }
        sql_close($ss1);
    } else if ($db == 'editAgenda') {
        $ss1 = sql_call("SELECT * FROM edomiProject.editAgenda WHERE (id=" . $id . ")");
        if ($original = sql_result($ss1)) {
            sql_call("INSERT INTO edomiProject.editAgenda (name,folderid,gaid,gaid2) VALUES (
				'" . sql_encodeValue($original['name'] . $nameSuffix) . "',
				'" . (($targetFolderId > 0) ? $targetFolderId : $original['folderid']) . "',
				'" . $original['gaid'] . "',
				'" . $original['gaid2'] . "'
			)");
            $dbId = sql_insertId();
            if ($dbId > 0) {
                $ss2 = sql_call("SELECT * FROM edomiProject.editAgendaData WHERE (targetid=" . $original['id'] . ") ORDER BY id ASC");
                while ($n = sql_result($ss2)) {
                    sql_call("INSERT INTO edomiProject.editAgendaData (targetid,name,fixed,cmdid,hour,minute,date1,date2,step,unit,d7) VALUES (
						'" . $dbId . "',
						'" . sql_encodeValue($n['name']) . "',
						'" . $n['fixed'] . "',
						'" . $n['cmdid'] . "',
						" . sql_encodeValue($n['hour'], true) . ",
						" . sql_encodeValue($n['minute'], true) . ",
						" . sql_encodeValue($n['date1'], true) . ",
						" . sql_encodeValue($n['date2'], true) . ",
						'" . sql_encodeValue($n['step']) . "',
						'" . sql_encodeValue($n['unit']) . "',
						'" . sql_encodeValue($n['d7']) . "'
					)");
                }
                $ss2 = sql_call("SELECT * FROM edomiProject.editAgendaMacroList WHERE (agendaid=" . $original['id'] . ") ORDER BY id ASC");
                while ($n = sql_result($ss2)) {
                    sql_call("INSERT INTO edomiProject.editAgendaMacroList (agendaid,targetid,sort) VALUES (
						'" . $dbId . "',
						'" . $n['targetid'] . "',
						'" . $n['sort'] . "'
					)");
                }
            }
            return $dbId;
        }
    } else if ($db == 'editAgendaData') {
        $ss1 = sql_call("SELECT * FROM edomiProject.editAgendaData WHERE (id=" . $id . ")");
        if ($original = sql_result($ss1)) {
            sql_call("INSERT INTO edomiProject.editAgendaData (targetid,name,fixed,cmdid,hour,minute,date1,date2,step,unit,d7) VALUES (
				" . $original['targetid'] . ",
				'" . sql_encodeValue($original['name']) . "',
				'" . $original['fixed'] . "',
				'" . $original['cmdid'] . "',
				" . sql_encodeValue($original['hour'], true) . ",
				" . sql_encodeValue($original['minute'], true) . ",
				" . sql_encodeValue($original['date1'], true) . ",
				" . sql_encodeValue($original['date2'], true) . ",
				'" . sql_encodeValue($original['step']) . "',
				'" . sql_encodeValue($original['unit']) . "',
				'" . sql_encodeValue($original['d7']) . "'
			)");
            $dbId = sql_insertId();
            return $dbId;
        }
        sql_close($ss1);
    } else if ($db == 'editSequence') {
        $ss1 = sql_call("SELECT * FROM edomiProject.editSequence WHERE (id=" . $id . ")");
        if ($original = sql_result($ss1)) {
            sql_call("INSERT INTO edomiProject.editSequence (name,folderid,datetime,ms,playpointer) VALUES (
				'" . sql_encodeValue($original['name'] . $nameSuffix) . "',
				'" . (($targetFolderId > 0) ? $targetFolderId : $original['folderid']) . "',
				" . sql_encodeValue($original['datetime'], true) . ",
				" . sql_encodeValue($original['ms'], true) . ",
				" . sql_encodeValue($original['playpointer'], true) . "
			)");
            $dbId = sql_insertId();
            if ($dbId > 0) {
                $ss2 = sql_call("SELECT * FROM edomiProject.editSequenceCmdList WHERE (targetid=" . $original['id'] . ") ORDER BY id ASC");
                while ($n = sql_result($ss2)) {
                    sql_call("INSERT INTO edomiProject.editSequenceCmdList (targetid,cmd,cmdid1,cmdid2,cmdoption1,cmdoption2,cmdvalue1,cmdvalue2,delay,sort) VALUES (
						'" . $dbId . "',
						'" . $n['cmd'] . "',
						" . sql_encodeValue($n['cmdid1'], true) . ",
						" . sql_encodeValue($n['cmdid2'], true) . ",
						'" . $n['cmdoption1'] . "',
						'" . $n['cmdoption2'] . "',
						" . sql_encodeValue($n['cmdvalue1'], true) . ",
						" . sql_encodeValue($n['cmdvalue2'], true) . ",
						'" . sql_encodeValue($n['delay']) . "',
						'" . $n['sort'] . "'
					)");
                }
            }
            return $dbId;
        }
    } else if ($db == 'editMacro') {
        $ss1 = sql_call("SELECT * FROM edomiProject.editMacro WHERE (id=" . $id . ")");
        if ($original = sql_result($ss1)) {
            sql_call("INSERT INTO edomiProject.editMacro (name,folderid) VALUES (
				'" . sql_encodeValue($original['name'] . $nameSuffix) . "',
				'" . (($targetFolderId > 0) ? $targetFolderId : $original['folderid']) . "'
			)");
            $dbId = sql_insertId();
            if ($dbId > 0) {
                $ss2 = sql_call("SELECT * FROM edomiProject.editMacroCmdList WHERE (targetid=" . $original['id'] . ") ORDER BY id ASC");
                while ($n = sql_result($ss2)) {
                    sql_call("INSERT INTO edomiProject.editMacroCmdList (targetid,cmd,cmdid1,cmdid2,cmdoption1,cmdoption2,cmdvalue1,cmdvalue2) VALUES (
						'" . $dbId . "',
						'" . $n['cmd'] . "',
						" . sql_encodeValue($n['cmdid1'], true) . ",
						" . sql_encodeValue($n['cmdid2'], true) . ",
						'" . $n['cmdoption1'] . "',
						'" . $n['cmdoption2'] . "',
						" . sql_encodeValue($n['cmdvalue1'], true) . ",
						" . sql_encodeValue($n['cmdvalue2'], true) . "
					)");
                }
            }
            return $dbId;
        }
    } else if ($db == 'editVisu') {
        sql_call("UPDATE edomiProject.editRoot SET tmp=NULL");
        sql_call("UPDATE edomiProject.editVisuPage SET tmp=NULL");
        $original = sql_getValues('edomiProject.editVisu', '*', 'id=' . $id);
        if ($original !== false) {
            sql_call("INSERT INTO edomiProject.editVisu (name,folderid,xsize,ysize,defaultpageid,sspageid,sstimeout,indicolor,indicolor2) VALUES (
				'" . sql_encodeValue($original['name'] . $nameSuffix) . "',
				'" . (($targetFolderId > 0) ? $targetFolderId : $original['folderid']) . "',
				'" . sql_encodeValue($original['xsize']) . "',
				'" . sql_encodeValue($original['ysize']) . "',
				'" . $original['defaultpageid'] . "',
				'" . $original['sspageid'] . "',
				'" . $original['sstimeout'] . "',
				'" . $original['indicolor'] . "',
				'" . $original['indicolor2'] . "'
			)");
            $dbId = sql_insertId();
            if ($dbId > 0) {
                $ss2 = sql_call("SELECT * FROM edomiProject.editRoot WHERE (link=22 AND linkid=" . $original['id'] . ") ORDER BY ((LENGTH(path)-LENGTH(REPLACE(path,'/','')))-1) ASC,id ASC");
                while ($originalFolder = sql_result($ss2)) {
                    $parentId = 22;
                    if ($originalFolder['parentid'] != $parentId) {
                        $parentId = sql_getValue('edomiProject.editRoot', 'id', 'tmp=' . $originalFolder['parentid']);
                    }
                    db_itemSave('editRoot', array(1 => -1, 2 => $originalFolder['name'], 3 => $parentId, 4 => $dbId, 5 => $originalFolder['id']));
                }
                sql_close($ss2);
                $ss2 = sql_call("SELECT * FROM edomiProject.editVisuPage WHERE (visuid=" . $original['id'] . ") ORDER BY id ASC");
                while ($n = sql_result($ss2)) {
                    if ($n['folderid'] == 22) {
                        db_itemDuplicate_editVisuPage(true, $n['id'], $dbId, 22, '');
                    } else {
                        $tmp = sql_getValues('edomiProject.editRoot', 'id', 'link=22 AND linkid=' . $dbId . ' AND tmp=' . $n['folderid']);
                        if ($tmp !== false) {
                            db_itemDuplicate_editVisuPage(true, $n['id'], $dbId, $tmp['id'], '');
                        }
                    }
                }
                sql_close($ss2);
                $ss2 = sql_call("SELECT * FROM edomiProject.editVisuUserList WHERE (visuid=" . $original['id'] . ")");
                while ($n = sql_result($ss2)) {
                    sql_call("INSERT INTO edomiProject.editVisuUserList (targetid,visuid,defaultpageid,sspageid,logindate,logoutdate,actiondate,loginip,sid) VALUES (
						'" . $n['targetid'] . "',
						'" . $dbId . "',
						" . sql_encodeValue($n['defaultpageid'], true) . ",
						" . sql_encodeValue($n['sspageid'], true) . ",
						NULL,NULL,NULL,NULL,NULL
					)");
                }
                sql_close($ss2);
                $n = sql_getValues('edomiProject.editVisu', '*', 'id=' . $dbId);
                if ($n !== false) {
                    if ($n['defaultpageid'] > 0) {
                        $tmp = sql_getValue('edomiProject.editVisuPage', 'id', '(visuid=' . $dbId . ' AND tmp=' . $n['defaultpageid'] . ')');
                        sql_call("UPDATE edomiProject.editVisu SET defaultpageid=" . sql_encodeValue($tmp, true) . " WHERE (id=" . $dbId . ")");
                    }
                    if ($n['sspageid'] > 0) {
                        $tmp = sql_getValue('edomiProject.editVisuPage', 'id', '(visuid=' . $dbId . ' AND tmp=' . $n['sspageid'] . ')');
                        sql_call("UPDATE edomiProject.editVisu SET sspageid=" . sql_encodeValue($tmp, true) . " WHERE (id=" . $dbId . ")");
                    }
                }
                $ss2 = sql_call("SELECT * FROM edomiProject.editVisuPage WHERE (visuid=" . $dbId . " AND includeid>0)");
                while ($n = sql_result($ss2)) {
                    $tmp = sql_getValue('edomiProject.editVisuPage', 'id', '(visuid=' . $dbId . ' AND tmp=' . $n['includeid'] . ')');
                    sql_call("UPDATE edomiProject.editVisuPage SET includeid=" . sql_encodeValue($tmp, true) . " WHERE (id=" . $n['id'] . " AND visuid=" . $dbId . ")");
                }
                sql_close($ss2);
                $ss2 = sql_call("SELECT * FROM edomiProject.editVisuElement WHERE (visuid=" . $dbId . " AND gotopageid>0)");
                while ($n = sql_result($ss2)) {
                    $tmp = sql_getValue('edomiProject.editVisuPage', 'id', '(visuid=' . $dbId . ' AND tmp=' . $n['gotopageid'] . ')');
                    sql_call("UPDATE edomiProject.editVisuElement SET gotopageid=" . sql_encodeValue($tmp, true) . " WHERE (id=" . $n['id'] . " AND visuid=" . $dbId . ")");
                }
                sql_close($ss2);
                $ss2 = sql_call("SELECT * FROM edomiProject.editVisuElement WHERE (visuid=" . $dbId . " AND closepopupid>0)");
                while ($n = sql_result($ss2)) {
                    $tmp = sql_getValue('edomiProject.editVisuPage', 'id', '(visuid=' . $dbId . ' AND tmp=' . $n['closepopupid'] . ')');
                    sql_call("UPDATE edomiProject.editVisuElement SET closepopupid=" . sql_encodeValue($tmp, true) . " WHERE (id=" . $n['id'] . " AND visuid=" . $dbId . ")");
                }
                sql_close($ss2);
                $ss2 = sql_call("SELECT * FROM edomiProject.editVisuUserList WHERE (visuid=" . $dbId . " AND defaultpageid>0)");
                while ($n = sql_result($ss2)) {
                    $tmp = sql_getValue('edomiProject.editVisuPage', 'id', '(visuid=' . $dbId . ' AND tmp=' . $n['defaultpageid'] . ')');
                    sql_call("UPDATE edomiProject.editVisuUserList SET defaultpageid=" . sql_encodeValue($tmp, true) . " WHERE (id=" . $n['id'] . " AND visuid=" . $dbId . ")");
                }
                sql_close($ss2);
                $ss2 = sql_call("SELECT * FROM edomiProject.editVisuUserList WHERE (visuid=" . $dbId . " AND sspageid>0)");
                while ($n = sql_result($ss2)) {
                    $tmp = sql_getValue('edomiProject.editVisuPage', 'id', '(visuid=' . $dbId . ' AND tmp=' . $n['sspageid'] . ')');
                    sql_call("UPDATE edomiProject.editVisuUserList SET sspageid=" . sql_encodeValue($tmp, true) . " WHERE (id=" . $n['id'] . " AND visuid=" . $dbId . ")");
                }
                sql_close($ss2);
            }
            return $dbId;
        }
    } else if ($db == 'editVisuPage') {
        $dbId = db_itemDuplicate_editVisuPage(false, $id, 0, $targetFolderId, $nameSuffix);
        return $dbId;
    } else if ($db == 'editVisuElementDesignDef') {
        $ss1 = sql_call("SELECT * FROM edomiProject.editVisuElementDesignDef WHERE (id=" . $id . ")");
        if ($original = sql_result($ss1)) {
            $insertC = '';
            $insertV = '';
            for ($tt = 1; $tt <= 48; $tt++) {
                $insertC .= "s" . $tt . ",";
                $insertV .= sql_encodeValue($original['s' . $tt], true) . ',';
            }
            sql_call("INSERT INTO edomiProject.editVisuElementDesignDef (name,folderid,styletyp," . rtrim($insertC, ',') . ") VALUES (
				'" . sql_encodeValue($original['name'] . $nameSuffix) . "',
				'" . (($targetFolderId > 0) ? $targetFolderId : $original['folderid']) . "',
				" . $original['styletyp'] . ",
				" . rtrim($insertV, ',') . "
			)");
            $dbId = sql_insertId();
            return $dbId;
        }
    } else if ($db == 'editVisuBGcol') {
        $ss1 = sql_call("SELECT * FROM edomiProject.editVisuBGcol WHERE (id=" . $id . ")");
        if ($original = sql_result($ss1)) {
            sql_call("INSERT INTO edomiProject.editVisuBGcol (name,folderid,color) VALUES (
				'" . sql_encodeValue($original['name'] . $nameSuffix) . "',
				'" . (($targetFolderId > 0) ? $targetFolderId : $original['folderid']) . "',
				'" . sql_encodeValue($original['color']) . "'
			)");
            $dbId = sql_insertId();
            return $dbId;
        }
    } else if ($db == 'editVisuFGcol') {
        $ss1 = sql_call("SELECT * FROM edomiProject.editVisuFGcol WHERE (id=" . $id . ")");
        if ($original = sql_result($ss1)) {
            sql_call("INSERT INTO edomiProject.editVisuFGcol (name,folderid,color) VALUES (
				'" . sql_encodeValue($original['name'] . $nameSuffix) . "',
				'" . (($targetFolderId > 0) ? $targetFolderId : $original['folderid']) . "',
				'" . sql_encodeValue($original['color']) . "'
			)");
            $dbId = sql_insertId();
            return $dbId;
        }
    } else if ($db == 'editVisuAnim') {
        $ss1 = sql_call("SELECT * FROM edomiProject.editVisuAnim WHERE (id=" . $id . ")");
        if ($original = sql_result($ss1)) {
            sql_call("INSERT INTO edomiProject.editVisuAnim (name,folderid,keyframes,timing,delay,direction,fillmode) VALUES (
				'" . sql_encodeValue($original['name'] . $nameSuffix) . "',
				'" . (($targetFolderId > 0) ? $targetFolderId : $original['folderid']) . "',
				'" . sql_encodeValue($original['keyframes']) . "',
				'" . $original['timing'] . "',
				'" . $original['delay'] . "',
				'" . $original['direction'] . "',
				'" . $original['fillmode'] . "'
			)");
            $dbId = sql_insertId();
            return $dbId;
        }
    } else if ($db == 'editVisuUser') {
        $ss1 = sql_call("SELECT * FROM edomiProject.editVisuUser WHERE (id=" . $id . ")");
        if ($original = sql_result($ss1)) {
            sql_call("INSERT INTO edomiProject.editVisuUser (login,folderid,pass,gaid,gaid2,gaid3,touch,preload,noerrors,noacksounds,click,touchscroll,autologout,longclick) VALUES (
				'" . sql_encodeValue($original['login'] . $nameSuffix) . "',
				'" . (($targetFolderId > 0) ? $targetFolderId : $original['folderid']) . "',
				'" . sql_encodeValue($original['pass']) . "',
				'" . $original['gaid'] . "',
				'" . $original['gaid2'] . "',
				'" . $original['gaid3'] . "',
				'" . $original['touch'] . "',
				'" . $original['preload'] . "',
				'" . $original['noerrors'] . "',
				'" . $original['noacksounds'] . "',
				'" . $original['click'] . "',
				'" . $original['touchscroll'] . "',
				'" . $original['autologout'] . "',
				'" . $original['longclick'] . "'
			)");
            $dbId = sql_insertId();
            return $dbId;
        }
    } else if ($db == 'editVisuElementDesign') {
        $ss1 = sql_call("SELECT * FROM edomiProject.editVisuElementDesign WHERE id=" . $id . " AND styletyp=1");
        if ($original = sql_result($ss1)) {
            $insertC = '';
            $insertV = '';
            for ($t = 1; $t <= 48; $t++) {
                $insertC .= "s" . $t . ",";
                $insertV .= sql_encodeValue($original['s' . $t], true) . ',';
            }
            sql_call("INSERT INTO edomiProject.editVisuElementDesign (targetid,defid,styletyp," . rtrim($insertC, ',') . ") VALUES (
				" . $original['targetid'] . ",
				'" . $original['defid'] . "',
				" . $original['styletyp'] . ",
				" . rtrim($insertV, ',') . "
			)");
            $dbId = sql_insertId();
            return $dbId;
        }
        sql_close($ss1);
    }
    return false;
}

function db_itemDuplicate_editVisuPage($mode, $pageId, $visuId, $folderId, $nameSuffix = '')
{
    if (!global_duplicateSuffix) {
        $nameSuffix = '';
    }
    $ss1 = sql_call("SELECT * FROM edomiProject.editVisuPage WHERE (id=" . $pageId . ")");
    if ($original = sql_result($ss1)) {
        if ($mode) {
            $newVisuId = $visuId;
            sql_call("INSERT INTO edomiProject.editVisuPage (name,folderid,visuid,includeid,globalinclude,pagetyp,xpos,ypos,xsize,ysize,autoclose,bgmodal,bganim,bgdark,bgshadow,bgcolorid,bgimg,xgrid,ygrid,outlinecolorid,tmp) VALUES (
				'" . sql_encodeValue($original['name'] . $nameSuffix) . "',
				'" . $folderId . "',
				'" . $visuId . "',
				'" . $original['includeid'] . "',
				'" . $original['globalinclude'] . "',
				'" . $original['pagetyp'] . "',
				" . sql_encodeValue($original['xpos'], true) . ",
				" . sql_encodeValue($original['ypos'], true) . ",
				" . sql_encodeValue($original['xsize'], true) . ",
				" . sql_encodeValue($original['ysize'], true) . ",
				'" . $original['autoclose'] . "',
				'" . $original['bgmodal'] . "',
				'" . $original['bganim'] . "',
				'" . $original['bgdark'] . "',
				'" . $original['bgshadow'] . "',
				'" . $original['bgcolorid'] . "',
				'" . $original['bgimg'] . "',
				'" . sql_encodeValue($original['xgrid']) . "',
				'" . sql_encodeValue($original['ygrid']) . "',
				'" . $original['outlinecolorid'] . "',
				'" . $original['id'] . "'
			)");
        } else {
            $newVisuId = $original['visuid'];
            sql_call("INSERT INTO edomiProject.editVisuPage (name,folderid,visuid,includeid,globalinclude,pagetyp,xpos,ypos,xsize,ysize,autoclose,bgmodal,bganim,bgdark,bgshadow,bgcolorid,bgimg,xgrid,ygrid,outlinecolorid) VALUES (
				'" . sql_encodeValue($original['name'] . $nameSuffix) . "',
				'" . (($folderId > 0) ? $folderId : $original['folderid']) . "',
				'" . $original['visuid'] . "',
				'" . $original['includeid'] . "',
				'" . $original['globalinclude'] . "',
				'" . $original['pagetyp'] . "',
				" . sql_encodeValue($original['xpos'], true) . ",
				" . sql_encodeValue($original['ypos'], true) . ",
				" . sql_encodeValue($original['xsize'], true) . ",
				" . sql_encodeValue($original['ysize'], true) . ",
				'" . $original['autoclose'] . "',
				'" . $original['bgmodal'] . "',
				'" . $original['bganim'] . "',
				'" . $original['bgdark'] . "',
				'" . $original['bgshadow'] . "',
				'" . $original['bgcolorid'] . "',
				'" . $original['bgimg'] . "',
				'" . sql_encodeValue($original['xgrid']) . "',
				'" . sql_encodeValue($original['ygrid']) . "',
				'" . $original['outlinecolorid'] . "'
			)");
        }
        $dbId = sql_insertId();
        if ($dbId > 0) {
            $elementIds = array();
            $ss1 = sql_call("SELECT id FROM edomiProject.editVisuElement WHERE (visuid=" . $original['visuid'] . " AND pageid=" . $original['id'] . ") ORDER BY id ASC");
            while ($n = sql_result($ss1)) {
                $elementIds[] = $n['id'];
            }
            sql_close($ss1);
            db_itemDuplicate_editVisuElement($newVisuId, $dbId, $elementIds, '');
        }
        return $dbId;
    }
}

function db_itemDuplicate_editVisuElement($visuId, $pageId, $elementIds, $nameSuffix = '')
{
    if (!global_duplicateSuffix) {
        $nameSuffix = '';
    }
    $r = array();
    sql_call("UPDATE edomiProject.editVisuElement SET tmp=NULL,tmp2=NULL");
    foreach ($elementIds as $element) {
        if ($element > 0) {
            $n = sql_getValues('edomiProject.editVisuElement', '*', 'id=' . $element);
            if ($n !== false) {
                if ($n['controltyp'] == 0) {
                    $dbId = db_itemSave('editVisuElement', array(1 => -1, 6 => 0, 7 => $n['gaid'], 18 => $n['text'], 20 => $n['name'] . $nameSuffix, 97 => $visuId, 98 => $pageId, 99 => $n['id'], 100 => 0));
                    if ($dbId > 0) {
                        $r[] = $dbId;
                    }
                } else {
                    $dbId = db_itemSave('editVisuElement', array(1 => -1, 2 => $n['xpos'], 3 => $n['ypos'], 6 => $n['controltyp'], 95 => $n['dynstylemode'], 7 => $n['gaid'], 8 => $n['zindex'], 13 => $n['gotopageid'], 94 => $n['closepopupid'], 14 => $n['closepopup'], 15 => $n['xsize'], 16 => $n['ysize'], 17 => $n['gaid2'], 23 => $n['gaid3'], 18 => $n['text'], 19 => $n['initonly'], 20 => ((!isEmpty($n['name'])) ? $n['name'] . $nameSuffix : ''), 21 => $n['groupid'], 22 => $n['linkid'], 31 => $n['var1'], 32 => $n['var2'], 33 => $n['var3'], 34 => $n['var4'], 35 => $n['var5'], 36 => $n['var6'], 37 => $n['var7'], 38 => $n['var8'], 39 => $n['var9'], 40 => $n['var10'], 41 => $n['var11'], 42 => $n['var12'], 43 => $n['var13'], 44 => $n['var14'], 45 => $n['var15'], 46 => $n['var16'], 47 => $n['var17'], 48 => $n['var18'], 49 => $n['var19'], 50 => $n['var20'], 96 => $n['galive'], 97 => $visuId, 98 => $pageId, 99 => 0, 100 => $n['id']));
                    if ($dbId > 0) {
                        $ss1 = sql_call("SELECT * FROM edomiProject.editVisuCmdList WHERE (targetid=" . $element . ") ORDER BY id ASC");
                        while ($nn = sql_result($ss1)) {
                            sql_call("INSERT INTO edomiProject.editVisuCmdList (targetid,cmd,cmdid1,cmdid2,cmdoption1,cmdoption2,cmdvalue1,cmdvalue2) VALUES (
								'" . $dbId . "',
								'" . $nn['cmd'] . "',
								" . sql_encodeValue($nn['cmdid1'], true) . ",
								" . sql_encodeValue($nn['cmdid2'], true) . ",
								'" . $nn['cmdoption1'] . "',
								'" . $nn['cmdoption2'] . "',
								" . sql_encodeValue($nn['cmdvalue1'], true) . ",
								" . sql_encodeValue($nn['cmdvalue2'], true) . "
							)");
                        }
                        sql_close($ss1);
                        $ss1 = sql_call("SELECT * FROM edomiProject.editVisuElementDesign WHERE (targetid=" . $element . ") ORDER BY id ASC");
                        while ($nn = sql_result($ss1)) {
                            $insertC = '';
                            $insertV = '';
                            for ($tt = 1; $tt <= 48; $tt++) {
                                $insertC .= "s" . $tt . ",";
                                $insertV .= sql_encodeValue($nn['s' . $tt], true) . ',';
                            }
                            sql_call("INSERT INTO edomiProject.editVisuElementDesign (targetid,defid,styletyp," . rtrim($insertC, ',') . ") VALUES ('" . $dbId . "','" . $nn['defid'] . "'," . $nn['styletyp'] . "," . rtrim($insertV, ',') . ")");
                        }
                        sql_close($ss1);
                        $r[] = $dbId;
                    }
                }
            }
        }
    }
    $ss1 = sql_call("SELECT * FROM edomiProject.editVisuElement WHERE (pageid=" . $pageId . " AND controltyp=0 AND tmp>0)");
    while ($n = sql_result($ss1)) {
        sql_call("UPDATE edomiProject.editVisuElement SET groupid=" . $n['id'] . ",tmp=NULL WHERE (pageid=" . $pageId . " AND groupid=" . $n['tmp'] . " AND tmp=0 AND controltyp<>0)");
    }
    sql_close($ss1);
    sql_call("UPDATE edomiProject.editVisuElement set groupid=0 WHERE (pageid=" . $pageId . " AND controltyp<>0 AND tmp=0)");
    $ss1 = sql_call("SELECT * FROM edomiProject.editVisuElement WHERE (pageid=" . $pageId . " AND linkid>0 AND tmp2>0)");
    while ($n = sql_result($ss1)) {
        $tmp = sql_getValues('edomiProject.editVisuElement', 'id', 'pageid=' . $pageId . ' AND tmp2=' . $n['linkid']);
        if ($tmp !== false) {
            sql_call("UPDATE edomiProject.editVisuElement SET linkid=" . $tmp['id'] . " WHERE id=" . $n['id']);
        } else {
            sql_call("UPDATE edomiProject.editVisuElement SET linkid=0 WHERE id=" . $n['id']);
        }
    }
    sql_close($ss1);
    sql_call("UPDATE edomiProject.editVisuElement SET tmp=NULL,tmp2=NULL");
    return $r;
}

function db_itemDuplicate_editLogicElement($pageId, $elementIds)
{
    $r = array();
    $copyIds = array();
    for ($t = 0; $t < count($elementIds); $t++) {
        $dbId = 0;
        $n = sql_getValues('edomiProject.editLogicElement', '*', 'id=' . $elementIds[$t]);
        if ($n !== false) {
            if ($pageId == $n['pageid']) {
                $offset = 15;
            } else {
                $offset = 0;
            }
            $dbId = sql_save('edomiProject.editLogicElement', null, array('functionid' => $n['functionid'], 'pageid' => $pageId, 'xpos' => ($n['xpos'] + $offset), 'ypos' => ($n['ypos'] + $offset), 'name' => "'" . sql_encodeValue($n['name']) . "'"));
            if ($dbId > 0) {
                $ss1 = sql_call("SELECT * FROM edomiProject.editLogicElementVar WHERE (elementid=" . $elementIds[$t] . ")");
                while ($nn = sql_result($ss1)) {
                    sql_call("INSERT INTO edomiProject.editLogicElementVar (elementid,varid,value,remanent) VALUES (
						'" . $dbId . "',
						'" . $nn['varid'] . "',
						" . sql_encodeValue($nn['value'], true) . ",
						'" . $nn['remanent'] . "'
					)");
                }
                sql_close($ss1);
                $ss1 = sql_call("SELECT * FROM edomiProject.editLogicCmdList WHERE (targetid=" . $elementIds[$t] . ") ORDER BY id ASC");
                while ($nn = sql_result($ss1)) {
                    sql_call("INSERT INTO edomiProject.editLogicCmdList (targetid,cmd,cmdid1,cmdid2,cmdoption1,cmdoption2,cmdvalue1,cmdvalue2) VALUES (
						'" . $dbId . "',
						'" . $nn['cmd'] . "',
						" . sql_encodeValue($nn['cmdid1'], true) . ",
						" . sql_encodeValue($nn['cmdid2'], true) . ",
						'" . $nn['cmdoption1'] . "',
						'" . $nn['cmdoption2'] . "',
						" . sql_encodeValue($nn['cmdvalue1'], true) . ",
						" . sql_encodeValue($nn['cmdvalue2'], true) . "
					)");
                }
                sql_close($ss1);
                $r[] = $dbId;
            }
        }
        $copyIds[$t] = $dbId;
    }
    for ($t = 0; $t < count($elementIds); $t++) {
        $ss1 = sql_call("SELECT * FROM edomiProject.editLogicLink WHERE (elementid=" . $elementIds[$t] . ")");
        while ($n = sql_result($ss1)) {
            if ($n['linktyp'] == '1') {
                $id = array_search($n['linkid'], $elementIds);
                if ($id === false) {
                    sql_call("INSERT INTO edomiProject.editLogicLink (elementid,eingang,linktyp,linkid,ausgang,refresh,value) VALUES (
						'" . $copyIds[$t] . "',
						'" . $n['eingang'] . "',
						'2',null,null,
						'" . $n['refresh'] . "',
						" . sql_encodeValue($n['value'], true) . "
					)");
                } else {
                    sql_call("INSERT INTO edomiProject.editLogicLink (elementid,eingang,linktyp,linkid,ausgang,refresh,value) VALUES (
						'" . $copyIds[$t] . "',
						'" . $n['eingang'] . "',
						'" . $n['linktyp'] . "',
						" . sql_encodeValue($copyIds[$id], true) . ",
						" . sql_encodeValue($n['ausgang'], true) . ",
						'" . $n['refresh'] . "',
						" . sql_encodeValue($n['value'], true) . "
					)");
                }
            } else {
                sql_call("INSERT INTO edomiProject.editLogicLink (elementid,eingang,linktyp,linkid,ausgang,refresh,value) VALUES (
					'" . $copyIds[$t] . "',
					'" . $n['eingang'] . "',
					'" . $n['linktyp'] . "',
					" . sql_encodeValue($n['linkid'], true) . ",
					" . sql_encodeValue($n['ausgang'], true) . ",
					'" . $n['refresh'] . "',
					" . sql_encodeValue($n['value'], true) . "
				)");
            }
        }
    }
    return $r;
} ?>
