<?php

/*
Copyright (c) 2013-2014, Microsoft Mobile
All rights reserved.

Redistribution and use in source and binary forms, with or without
modification, are permitted provided that the following conditions are met:

* Redistributions of source code must retain the above copyright notice, this
  list of conditions and the following disclaimer.

* Redistributions in binary form must reproduce the above copyright notice,
  this list of conditions and the following disclaimer in the documentation
  and/or other materials provided with the distribution.

* Neither the name of the {organization} nor the names of its
  contributors may be used to endorse or promote products derived from
  this software without specific prior written permission.

THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS"
AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE
IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE
DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT HOLDER OR CONTRIBUTORS BE LIABLE
FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL
DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR
SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER
CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY,
OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE
OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
*/

if (!isset($_SESSION['username'])) {
    return;
}
if (!isset($GLOBALS['cur_user']) || !$GLOBALS['cur_user']->getPermission('manage_cycles')) {
    return;
}

$args = filter_input_array(INPUT_POST);
if ($args['func'] == "done_cycle") {
    $id = $GLOBALS['db']->escape($args['id']);
    $GLOBALS['db']->query("UPDATE cycle SET active = '0' WHERE id = '" . $id . "' LIMIT 1");
    $GLOBALS['db']->query("UPDATE ticket SET active = '0' WHERE cycle = '" . $id . "'");
} else if ($args['func'] == "delete_cycle") {
    $id = $GLOBALS['db']->escape($args['id']);
    $GLOBALS['db']->query("DELETE FROM cycle WHERE id = '" . $id . "' LIMIT 1");
    $GLOBALS['db']->query("UPDATE ticket SET deleted = '1' WHERE cycle = '" . $id . "'");
} else if ($args['func'] == "add_cycle") {
    $name = $GLOBALS['db']->escape($args['name']);
    $wip_person = 0;
    $wip_total = 0;
    if ($GLOBALS['board']->getSettingValue("USE_WIP")) {
        $wip_person = $GLOBALS['db']->escape($args['wip_person']);
        $wip_total = $GLOBALS['db']->escape($args['wip_total']);
    }
    $s_date = $GLOBALS['db']->escape($args['s_date']);
    $weeks = $GLOBALS['db']->escape($args['weeks']);

    $dat = date("Y-m-d H:i:s", strtotime($s_date));
    $res = $GLOBALS['db']->get_results("SELECT * FROM cycle WHERE stop >= '" . $dat . "' AND start <= '" . $dat . "' AND active='1' AND board_id = '" . $GLOBALS['board']->getBoardId() . "' LIMIT 1");

    if ($GLOBALS['db']->num_rows > 0) {
        echo "Start date is already included in cycle. Please check!";
        return;
    }

    $e_date = date("Y-m-d H:i:s", strtotime($s_date) + ($weeks * 7 * 24 * 60 * 60) + (23 * 60 * 60) + (59 * 60));

    $GLOBALS['db']->query("INSERT INTO cycle (board_id, data, start, stop, wip_limit, active) VALUES ('" . $GLOBALS['board']->getBoardId() . "', '" . $name . "', '" . $dat . "', '" . $e_date . "', '" . $wip_total . "', '1')");
} else if ($args['func'] == "edit_cycle") {
    $id = $GLOBALS['db']->escape($args['id']);
    $name = strip_tags($GLOBALS['db']->escape($args['name']));
    $wip_limit = 0;
    if ($GLOBALS['board']->getSettingValue("USE_WIP")) {
        $wip_limit = $GLOBALS['db']->escape($args['wip_limit']);
    }
    $s_date = date("Y-m-d H:i:s", strtotime($GLOBALS['db']->escape($args['s_date'])));
    $e_date = date("Y-m-d H:i:s", strtotime($GLOBALS['db']->escape($args['e_date'])));

    $res = $GLOBALS['db']->get_results("SELECT * FROM cycle WHERE start < '" . $s_date . "' AND stop > '" . $e_date . "' AND active='1' AND id != '" . $id . "' AND board_id = '" . $GLOBALS['board']->getBoardId() . "' LIMIT 1");

    if ($GLOBALS['db']->num_rows > 0) {
        echo "Cycle overlaps with another cycle. Please check!";
        return;
    }

    $GLOBALS['db']->query("UPDATE cycle SET data='" . $name . "', wip_limit='" . $wip_limit . "', start='" . $s_date . "', stop='" . $e_date . "' WHERE id = '" . $id . "' AND board_id = '" . $GLOBALS['board']->getBoardId() . "' LIMIT 1") or die(mysql_error());
}
?>
