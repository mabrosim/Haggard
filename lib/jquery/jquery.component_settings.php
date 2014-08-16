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
if (!isset($GLOBALS['cur_user']) || !$GLOBALS['cur_user']->getPermission('manage_components')) {
    return;
}

$args = filter_input_array(INPUT_POST);

if ($args['func'] == "new_component") {
    if (!isset($args['title'])) {
        return;
    }
    $title = $GLOBALS['db']->escape($args['title']);

    $query = "INSERT INTO component (board_id, name) VALUES ('" . $GLOBALS['board']->getBoardId() . "', '" . $title . "')";
    $GLOBALS['db']->query($query);

    $log = $_SESSION['username'] . ' created new component: ' . $title . '.';
    $GLOBALS['logger']->log($log);
} else if ($args['func'] == "delete_component") {
    if (!isset($args['id'])) {
        return;
    }
    $id = $GLOBALS['db']->escape($args['id']);
    $query = "DELETE FROM component WHERE id = '" . $id . "' AND board_id = '" . $GLOBALS['board']->getBoardId() . "' LIMIT 1";
    $GLOBALS['db']->query($query);

    $GLOBALS['db']->query("UPDATE ticket SET component = '0' WHERE component = '" . $id . "'");

    $log = $_SESSION['username'] . ' deleted component: ' . $title . '.';
    $GLOBALS['logger']->log($log);
} else if ($args['func'] == "edit_component") {
    if (!isset($args['title']) || !isset($args['id'])) {
        return;
    }
    $title = $GLOBALS['db']->escape($args['title']);
    $id = $GLOBALS['db']->escape($args['id']);
    $query = "UPDATE component SET name = '" . $title . "' WHERE id = '" . $id . "' AND board_id = '" . $GLOBALS['board']->getBoardId() . "' LIMIT 1";
    $GLOBALS['db']->query($query);

    $log = $_SESSION['username'] . ' edited component: ' . $title . '.';
    $GLOBALS['logger']->log($log);
}
apc_delete('components' . $GLOBALS['board']->getBoardId());
?>
