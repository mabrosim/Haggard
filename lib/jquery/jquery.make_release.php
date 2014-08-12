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

if (!isset($GLOBALS['cur_user']) || !$GLOBALS['cur_user']->getPermission('manage_phases')) {
    return;
}

$args = filter_input_array(INPUT_POST);
$name = $GLOBALS['db']->escape($args['name']);
$phase = $GLOBALS['db']->escape($args['phase']);
$action = $GLOBALS['db']->escape($args['action']);

if (!isset($name) || $name == '') {
    return;
}
$GLOBALS['db']->query("INSERT INTO phase_release (board_id, name, released) VALUES ('" . $GLOBALS['board']->getBoardId() . "', '" . $name . "', UTC_TIMESTAMP())");
$release_id = $GLOBALS['db']->insert_id;

if (!$release_id) {
    return;
}

$tickets = $GLOBALS['db']->get_results("SELECT * FROM ticket WHERE board_id = '" . $GLOBALS['board']->getBoardId() . "' AND phase = '" . $phase . "' AND active = '1' AND deleted = '0'");
if (count($tickets) > 0) {
    foreach ($tickets as $ticket) {
        $GLOBALS['db']->query("INSERT INTO release_ticket (release_id, ticket_id) VALUES ('" . $release_id . "', '" . $ticket->id . "')");
        if ($action == "do_nothing") {
            continue;
        } else if ($action == "archive") {
            $GLOBALS['db']->query("UPDATE ticket SET active = '0' WHERE id = '" . $ticket->id . "' LIMIT 1");
        } else {
            if (!is_numeric($action)) {
                continue;
            }

            $GLOBALS['db']->query("UPDATE ticket SET phase = '" . $action . "' WHERE id = '" . $ticket->id . "' LIMIT 1");
        }
    }
}

$msg = $_SESSION['username'] . " has made a new release " . $name . "!<br><br>";
$msg .= "To see the release log please visit: " . $GLOBALS['board']->getBoardURL() . "lib/release_note.php?release=" . $release_id . "<br>";

echo $release_id;
?>
