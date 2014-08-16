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
    echo '<h2>No access</h2>';
    return;
}

echo '<script type="text/javascript" src="./js/group_settings.js"></script>';
echo '<h1>User groups</h1>';

$groups = $GLOBALS['db']->get_results("SELECT * FROM user_group WHERE board_id = '" . $GLOBALS['board']->getBoardId() . "'");

echo '<div class="create_group"><button>Create group</button></div>';

echo '<table border="0" class="settings_table">';
echo '<tr><th>Group</th><th>Description</th><th colspan="3">Actions</th></tr>';
if ($groups) {
    foreach ($groups as $group) {
        echo '<tr id="' . $group->id . '">';
        echo '<td>' . $group->name . '</td>';
        echo '<td>' . $group->description . '</td>';
        echo '<td><div class="edit_group"><a data-id="' . $group->id . '">Edit</a></div>';
        echo '<td><div class="delete_group"><a data-id="' . $group->id . '">Delete</a></div>';
        echo '<td><div class="permission_group"><a data-id="' . $group->id . '">Permissions</a></div>';
    }
}

echo '</table>';

echo '<h2>Group members</h2>';
echo '<table border="0" class="settings_table">';

echo '<tr><th>Group</th><th colspan="2">Actions</th><th>Members</th></tr>';

if ($groups) {
    $cur = 0;
    foreach ($groups as $group) {
        echo '<tr id="' . $group->id . '">';
        echo '<td>' . $group->name . '</td>';
        echo '</td><td><div class="add_members_to_group"><a data-id="' . $group->id . '">Add members</a></div></td>';
        echo '</td><td><div class="remove_members_from_group"><a data-id="' . $group->id . '">Remove members</a></div></td>';
        $members = $GLOBALS['db']->get_results("SELECT user.name, user.email FROM user, user_group_link WHERE user.id = user_group_link.user_id AND user_group_link.group_id = '" . $group->id . "' ORDER BY user.name ASC");
        echo '<td>';
        if ($members) {
            $i = 0;
            $len = count($members);
            foreach ($members as $member) {
                echo '<a href="mailto:' . $member->email . '">' . $member->name . '</a>';
                if ($i != $len - 1) {
                    echo ', ';
                }
                $i++;
            }
        }
        echo '</td></tr>';
    }
}

echo '</table>';
?>
