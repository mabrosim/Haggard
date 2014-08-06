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
if (!isset($GLOBALS['cur_user']) || !$GLOBALS['cur_user']->getPermission('manage_users')) {
    echo '<h2>No access</h2>';
    return;
}

$get_id = filter_input(INPUT_GET, 'id');
if (!isset($get_id)) {
    echo '<h2>Fatal error</h2>';
    return;
}

$user_id = $GLOBALS['db']->escape($get_id);
$user = new User($user_id);

echo '<div id="pop_up">';

echo '<form id="permission_form">';
echo '<table border="0" width="100%" cellspacing="5">';

echo '<tr><td colspan="2"><h2>Tickets<h2></th></tr>';

echo '<tr><td style="text-align:right;" width="50%">Move ticket</td><td style="text-align:center;">';
if ($user->getPermission('move_ticket')) {
    echo '<input type="checkbox" name="permission[]" value="move_ticket" checked="checked">';
} else {
    echo '<input type="checkbox" name="permission[]" value="move_ticket">';
}
echo '</td></tr>';

echo '<tr><td style="text-align:right;">Create ticket</td><td style="text-align:center;">';
if ($user->getPermission('create_ticket')) {
    echo '<input type="checkbox" name="permission[]" value="create_ticket" checked="checked">';
} else {
    echo '<input type="checkbox" name="permission[]" value="create_ticket">';
}
echo '</td></tr>';

echo '<tr><td style="text-align:right;">Edit ticket</td><td style="text-align:center;">';
if ($user->getPermission('edit_ticket')) {
    echo '<input type="checkbox" name="permission[]" value="edit_ticket" checked="checked">';
} else {
    echo '<input type="checkbox" name="permission[]" value="edit_ticket">';
}
echo '</td></tr>';

echo '<tr><td style="text-align:right;">Archive ticket</td><td style="text-align:center;">';
if ($user->getPermission('archive_ticket')) {
    echo '<input type="checkbox" name="permission[]" value="archive_ticket" checked="checked">';
} else {
    echo '<input type="checkbox" name="permission[]" value="archive_ticket">';
}
echo '</td></tr>';

echo '<tr><td style="text-align:right;">Delete ticket</td><td style="text-align:center;">';
if ($user->getPermission('delete_ticket')) {
    echo '<input type="checkbox" name="permission[]" value="delete_ticket" checked="checked">';
} else {
    echo '<input type="checkbox" name="permission[]" value="delete_ticket">';
}
echo '</td></tr>';

echo '<tr><td style="text-align:right;">Comment ticket</td><td style="text-align:center;">';
if ($user->getPermission('comment_ticket')) {
    echo '<input type="checkbox" name="permission[]" value="comment_ticket" checked="checked">';
} else {
    echo '<input type="checkbox" name="permission[]" value="comment_ticket">';
}
echo '</td></tr>';

echo '<tr><td style="text-align:right;">Move ticket to another board</td><td style="text-align:center;">';
if ($user->getPermission('move_ticket_board')) {
    echo '<input type="checkbox" name="permission[]" value="move_ticket_board" checked="checked">';
} else {
    echo '<input type="checkbox" name="permission[]" value="move_ticket_board">';
}
echo '</td></tr>';

echo '<tr><td style="text-align:right;">Copy ticket to another board</td><td style="text-align:center;">';
if ($user->getPermission('move_ticket_board')) {
    echo '<input type="checkbox" name="permission[]" value="copy_ticket_board" checked="checked">';
} else {
    echo '<input type="checkbox" name="permission[]" value="copy_ticket_board">';
}
echo '</td></tr>';

echo '<tr><td colspan="2"><h2>Administrative</h2></th></tr>';

echo '<tr><td style="text-align:right;">Manage board settings</td><td style="text-align:center;">';
if ($user->getPermission('manage_board_settings')) {
    echo '<input type="checkbox" name="permission[]" value="manage_board_settings" checked="checked">';
} else {
    echo '<input type="checkbox" name="permission[]" value="manage_board_settings">';
}
echo '</td></tr>';

echo '<tr><td style="text-align:right;">Manage cycles</td><td style="text-align:center;">';
if ($user->getPermission('manage_cycles')) {
    echo '<input type="checkbox" name="permission[]" value="manage_cycles" checked="checked">';
} else {
    echo '<input type="checkbox" name="permission[]" value="manage_cycles">';
}
echo '</td></tr>';

echo '<tr><td style="text-align:right;">Manage components</td><td style="text-align:center;">';
if ($user->getPermission('manage_components')) {
    echo '<input type="checkbox" name="permission[]" value="manage_components" checked="checked">';
} else {
    echo '<input type="checkbox" name="permission[]" value="manage_components">';
}
echo '</td></tr>';

echo '<tr><td style="text-align:right;">Manage users</td><td style="text-align:center;">';
if ($user->getPermission('manage_users')) {
    echo '<input type="checkbox" name="permission[]" value="manage_users" checked="checked">';
} else {
    echo '<input type="checkbox" name="permission[]" value="manage_users">';
}
echo '</td></tr>';

echo '<tr><td style="text-align:right;">Manage user groups</td><td style="text-align:center;">';
if ($user->getPermission('manage_user_groups')) {
    echo '<input type="checkbox" name="permission[]" value="manage_user_groups" checked="checked">';
} else {
    echo '<input type="checkbox" name="permission[]" value="manage_user_groups">';
}
echo '</td></tr>';

echo '<tr><td style="text-align:right;">Manage phases</td><td style="text-align:center;">';
if ($user->getPermission('manage_phases')) {
    echo '<input type="checkbox" name="permission[]" value="manage_phases" checked="checked">';
} else {
    echo '<input type="checkbox" name="permission[]" value="manage_phases">';
}
echo '</td></tr>';

echo '<tr><td style="text-align:right;">Manage releases</td><td style="text-align:center;">';
if ($user->getPermission('manage_releases')) {
    echo '<input type="checkbox" name="permission[]" value="manage_releases" checked="checked">';
} else {
    echo '<input type="checkbox" name="permission[]" value="manage_releases">';
}
echo '</td></tr>';

echo '<tr><td style="text-align:right;">All permissions (!)</td><td style="text-align:center;">';
if ($user->getPermission('all')) {
    echo '<input type="checkbox" class="all_permissions" name="permission[]" value="all" checked="checked">';
} else {
    echo '<input type="checkbox" class="all_permissions" name="permission[]" value="all">';
}
echo '</td></tr>';

echo '</table>';

echo '<p style="text-align: center;"><input type="submit" value="Save"><input type="reset" value="Reset"></p>';

echo '</form>';
echo '</div>';
?>
