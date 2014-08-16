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

if (!isset($GLOBALS['cur_user']) || !$GLOBALS['cur_user']->hasAccessToBoard()) {
    return;
}

echo '<div id="settings_menu" class="left_menu">';
echo '<ul>';
echo '<li><a id="my_settings">My settings</a></li>';
echo '<li><a id="my_tickets">My tickets</a></li>';
if ($GLOBALS['cur_user']->getPermission('manage_board_settings')) {
    echo '<li><a id="board_settings">Board settings</a></li>';
}

if ($GLOBALS['cur_user']->getPermission('manage_cycles') && $GLOBALS['board']->getSettingValue("USE_CYCLES") == "1") {
    echo '<li><a id="cycles">Cycles</a></li>';
}

if ($GLOBALS['cur_user']->getPermission('manage_components')) {
    echo '<li><a id="components">Components</a></li>';
}

if ($GLOBALS['cur_user']->getPermission('manage_users')) {
    echo '<li><a id="users">Users</a></li>';
}

if ($GLOBALS['cur_user']->getPermission('manage_user_groups')) {
    echo '<li><a id="user_groups">User groups</a></li>';
}

if ($GLOBALS['cur_user']->getPermission('manage_phases')) {
    echo '<li><a id="phases">Phases</a></li>';
}

if ($GLOBALS['cur_user']->getPermission('manage_releases')) {
    echo '<li><a id="releases">Releases</a></li>';
}
echo '</ul>';

echo '</div>';
echo '<div class="ui-widget" id="saveSettings" style="display: none; margin-top: 13px; margin-left: 160px;">
    <div class="ui-state-highlight ui-corner-all" style="padding: 0 .7em;">
        <p>
            <span class="ui-icon ui-icon-alert"
                style="float: left; margin-right: .3em;"></span>
            <strong>Settings saved!</strong> Settings were saved succesfully!
        </p>
    </div>
</div>';
echo '<div id="sub_content">';
include_once 'page.personal_settings.php';
echo '</div>';

echo '<script type="text/javascript" src="./js/settings_navigation.js"></script>' . PHP_EOL;
?>