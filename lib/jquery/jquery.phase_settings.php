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
if (!isset($GLOBALS['cur_user']) || !$GLOBALS['cur_user']->getPermission('manage_phases')) {
    return;
}

$args = filter_input_array(INPUT_POST);
if ($args['func'] == "tickets_in_phase") {
    $id = $GLOBALS['db']->escape($args['id']);

    $res = $GLOBALS['db']->query("SELECT id FROM ticket WHERE phase = '" . $id . "' AND board_id = '" . $GLOBALS['board']->getBoardId() . "'AND active = '1' LIMIT 1");

    if ($GLOBALS['db']->num_rows > 0) {
        echo '1';
    } else {
        echo '0';
    }
    return;
} else if ($args['func'] == "delete_tickets_in_phase") {
    $id = $GLOBALS['db']->escape($args['id']);
    $GLOBALS['db']->query("UPDATE ticket SET deleted = '0' WHERE phase = '" . $id . "' AND board_id = '" . $GLOBALS['board']->getBoardId() . "' LIMIT 1");
} else if ($args['func'] == "move_tickets_in_phase") {
    $id = $GLOBALS['db']->escape($args['id']);
    $to = $GLOBALS['db']->escape($args['to']);
    $GLOBALS['db']->query("UPDATE ticket SET phase = '" . $to . "' WHERE phase = '" . $id . "' AND board_id = '" . $GLOBALS['board']->getBoardId() . "' LIMIT 1");
} else if ($args['func'] == "inactivate_phase") {
    $id = $GLOBALS['db']->escape($args['id']);
    $GLOBALS['db']->query("UPDATE phase SET active = '0' WHERE id = '" . $id . "' LIMIT 1");
} else if ($args['func'] == "activate_phase") {
    $id = $GLOBALS['db']->escape($args['id']);
    $GLOBALS['db']->query("UPDATE phase SET active = '1' WHERE id = '" . $id . "' LIMIT 1");
} else if ($args['func'] == "edit_phase") {
    $wip_limit = 0;
    $ticket_limit = 0;
    $id = $GLOBALS['db']->escape($args['id']);
    $help_text = $GLOBALS['db']->escape($args['help_text']);
    $name = $GLOBALS['db']->escape($args['name']);
    $notify = 0;

    if (isset($args['notify_empty']) && $args['notify_empty'] != '') {
        $notify = 1;
    }

    if (isset($args['wip_limit'])) {
        $wip_limit = $GLOBALS['db']->escape($args['wip_limit']);
    }

    if (isset($args['ticket_limit'])) {
        $ticket_limit = $GLOBALS['db']->escape($args['ticket_limit']);
    }

    $force_comment = $GLOBALS['db']->escape($args['force_comment']);
    $notifications = $GLOBALS['db']->escape($args['notifications']);
    $not_arr = explode(",", $notifications);

    $GLOBALS['db']->query("UPDATE phase SET name = '" . $name . "', help = '" . $help_text . "', wip_limit = '" . $wip_limit . "', force_comment = '" . $force_comment . "', notify_empty = '" . $notify . "', ticket_limit = '" . $ticket_limit . "' WHERE id = '" . $id . "' LIMIT 1");

    $GLOBALS['db']->query("DELETE FROM phase_email_notification WHERE phase_id = '" . $id . "'");

    if (count($not_arr) > 0) {
        foreach ($not_arr as $notification) {
            if ($notification == 0) {
                continue;
            }
            $GLOBALS['db']->query("INSERT INTO phase_email_notification (phase_id, group_id) VALUES ('" . $id . "', '" . $notification . "')");
        }
    }
}

$GLOBALS['board']->updateClients();
?>
