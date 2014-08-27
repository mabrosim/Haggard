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

echo '<script type="text/javascript" src="./js/user_settings.js"></script>';
echo '<h1>Users</h1>';

echo '<div class="add_user"><button>Add new user</button></div>';

echo '<h2>Active users</h2>';
echo '<table border="0" class="settings_table">';

echo '<tr><th>Name</th><th>Displayed name</th><th>E-mail</th><th>Last login</th><th>Location</th><th>Timezone</th>';
echo '<th colspan="3">Actions</th></tr>';

$users = $GLOBALS['board']->getUsers(1);

foreach ($users as $user) {
    echo '<tr id="' . $user->getId() . '">';
    echo '<td>' . $user->getRealName() . '</td>';
    echo '<td>' . $user->getName() . '</td>';
    echo '<td><a href="mailto:' . $user->getEmail() . '">' . $user->getEmail() . '</td>';
    if ($user->getLastLogin() == null) {
        echo '<td>No login since 15.01.2013</td>';
    } else {
        echo '<td>' . date('d.m.Y H:i:s', strtotime($user->getLastLogin() . ' UTC')) . '</td>';
    }
    echo '<td>' . $user->getNokiaSite() . '</td>';
    echo '<td>' . $user->getTimezone() . '</td>';
    echo '<td><div class="remove_user"><a data-id="' . $user->getId() . '">Remove completely</a></div></td>';
    echo '<td><div class="permission_user"><a data-id="' . $user->getId() . '">Permissions</a></div>';
    echo '</td>';
    echo '</tr>';
}

echo '</table>';
echo '<div style="clear:both;"></div>';
?>
