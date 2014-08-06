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

echo '<div id="menu_container">';
echo '<ul id="menu">';
echo '<li id="kanban_logo" style="margin-right: 10px;"><img src="./img/logo.png" width="90" height="56"></li>';

/* NEW TICKET MENU */
if (isset($GLOBALS['cur_user']) && $GLOBALS['board']->getNumCycles() > 0 && $GLOBALS['cur_user']->hasAccessToBoard()) {
    if (isset($GLOBALS['cur_user']) && $GLOBALS['cur_user']->getPermission('create_ticket')) {
        echo '<li><a class="new_ticket menu_item">New ticket</a></li>';
    }
}

/* LOG MENU */
if ($GLOBALS['board']->getSettingValue("USE_LOGGING") && isset($GLOBALS['cur_user']) && $GLOBALS['cur_user']->hasAccessToBoard()) {
    if (isset($_SESSION['username'])) {
        echo '<li class="menu_item" id="log"><a>Log</a></li>';
    }
}

if ($GLOBALS['board']->getSettingValue("USE_STATISTICS") && isset($GLOBALS['cur_user']) && $GLOBALS['cur_user']->hasAccessToBoard()) {
    /* STATISTICS */
    echo '<li class="menu_item" id="statistics"><a class="sub_nav_but">Statistics</a>';
    echo '</li>';
}

if (isset($GLOBALS['cur_user']) && $GLOBALS['cur_user']->hasAccessToBoard()) {
    echo '<li class="menu_item" id="message_board"><a class="sub_nav_but">Message board</a></li>';
}
/* ABOUT */
echo '<li class="menu_item" id="about"><a class="sub_nav_but">About</a>';
echo '</li>';

/* YAMMER */
echo '<li class="menu_item" id="yammer"><a class="sub_nav_but">Join Yammer</a>';
echo '</li>';

/* LOGIN HANDLING */
if (!isset($_SESSION['username'])) {
    echo '<li style="float:right" id="login"><a class="login menu_item">Log in</a></li>';
} else {
    echo '<li style="float:right" id="logout"><a class="logout menu_item">Log out ' . $GLOBALS['cur_user']->getName() . '</a></li>';
}

if (isset($GLOBALS['cur_user']) && $GLOBALS['cur_user']->hasAccessToBoard() && $GLOBALS['cur_user']->getRealName() != 'Guest') {
    echo '<li style="float:right" class="menu_item" id="settings"><a class="sub_nav_but">Settings</a>';
    echo '</li>';
}

echo '</ul>';

if (isset($_SESSION['username'])) {
    $lid = $GLOBALS['db']->get_var("SELECT id FROM user_board WHERE user_id = '" . $_SESSION['userid'] . "' AND board_id = '" . $GLOBALS['board']->getBoardId() . "'");
    if ($lid) {
        $num = $GLOBALS['db']->get_var("SELECT COUNT(id) FROM user_notification WHERE user_id = '" . $_SESSION['userid'] . "' AND status = 'unread'");

        echo '<div style="float:right" id="notification_menu"><div id="notification_count"';
        if ($num > 0) {
            if ($num > 9) {
                $num = "+";
            }
            echo ' style="background-color: #FF0000;"';
        }
        echo '>' . $num . '</div></div>';
        echo '<div id="notifications">';
        echo '<div id="arrow_up"></div>';
        echo '<h1>Notifications</h1>';
        echo '<div id="notification_area"></div>';
        echo '<div id="see_all_notifications"><a href="">See all notifications</a></div>';
        echo '</div>';
    }
}

echo '</div>';
?>
