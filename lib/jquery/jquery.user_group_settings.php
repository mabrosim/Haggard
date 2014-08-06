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
if (!isset($GLOBALS['cur_user']) || !$GLOBALS['cur_user']->getPermission('manage_user_groups')) {
    return;
}

require_once 'group.class.php';

$args = filter_input_array(INPUT_POST);

if ($args['func'] == "create_group") {
    $name = $GLOBALS['db']->escape($args['name']);
    $desc = $GLOBALS['db']->escape($args['desc']);

    $GLOBALS['db']->query("INSERT INTO user_group (board_id, name, description) VALUES ('" . $GLOBALS['board']->getBoardId() . "', '" . $name . "', '" . $desc . "')");
} else if ($args['func'] == "delete_group") {
    $id = $GLOBALS['db']->escape($args['id']);
    $GLOBALS['db']->query("DELETE FROM user_group_link WHERE group_id = '" . $id . "'");
    $GLOBALS['db']->query("DELETE FROM group_permission WHERE group_id = '" . $id . "'");
    $GLOBALS['db']->query("DELETE FROM user_group WHERE id = '" . $id . "' LIMIT 1");
} else if ($args['func'] == "edit_group") {
    $id = $GLOBALS['db']->escape($args['id']);
    $name = $GLOBALS['db']->escape($args['name']);
    $desc = $GLOBALS['db']->escape($args['desc']);

    $GLOBALS['db']->query("UPDATE user_group SET name = '" . $name . "', description = '" . $desc . "' WHERE id = '" . $id . "' LIMIT 1");
} else if ($args['func'] == "group_permissions") {
    $permission_str = $GLOBALS['db']->escape($args['permissions']);
    $id = $GLOBALS['db']->escape($args['id']);
    $group = new Group($id);

    $group->clearPermissions();

    $permissions = explode(",", $permission_str);
    foreach ($permissions as $permission) {
        $group->setPermission($permission);
    }
} else if ($args['func'] == "add_to_group") {
    $gid = $GLOBALS['db']->escape($args['gid']);
    $pid = $GLOBALS['db']->escape($args['pid']);

    echo $gid;
    echo ' - ' . $pid;

    $group_in_this_board = $GLOBALS['db']->get_var("SELECT ug.id FROM user_group ug LEFT JOIN user_group_link ul ON ug.id = ul.group_id LEFT JOIN user u ON u.id = ul.user_id WHERE u.id = '" . $p_id . "' AND ug.board_id = '" . $GLOBALS['board']->getBoardId() . "'");
    if ($group_in_this_board) {
        $GLOBALS['db']->query("DELETE FROM user_group_link WHERE user_id = '" . $pid . "' AND group_id = '" . $group_in_this_board . "' LIMIT 1");
    }
    $GLOBALS['db']->query("INSERT INTO user_group_link (user_id, group_id) VALUES ('" . $pid . "', '" . $gid . "')");
} else if ($args['func'] == "remove_from_group") {
    $gid = $GLOBALS['db']->escape($args['gid']);
    $pid = $GLOBALS['db']->escape($args['pid']);

    $GLOBALS['db']->query("DELETE FROM user_group_link WHERE user_id = '" . $pid . "' AND group_id = '" . $gid . "' LIMIT 1");
}
?>
