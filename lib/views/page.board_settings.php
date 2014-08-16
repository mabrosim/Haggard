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
if (!isset($GLOBALS['cur_user']) || !$GLOBALS['cur_user']->getPermission('manage_board_settings')) {
    echo '<h2>No access</h2>';
    return;
}

echo '<script type="text/javascript" src="js/board_settings.js"></script>';
echo '<h1>' . $GLOBALS['board']->getBoardName() . ' board settings</h1>';
echo '<h2>General settings</h2>';
echo '<form id="personal_settings">';
echo '<table border="0" width="50%" cellspacing="2" class="settings_table">';

echo '<tr><th colspan="2">Setting</th><th colspan="2">Value</th></tr>';

$use_wip = $GLOBALS['board']->getSettingValue('USE_WIP');
$use_cycles = $GLOBALS['board']->getSettingValue('USE_CYCLES');
$use_linking = $GLOBALS['board']->getSettingValue('USE_LINKING');
$use_statistics = $GLOBALS['board']->getSettingValue('USE_STATISTICS');
$use_logging = $GLOBALS['board']->getSettingValue('USE_LOGGING');
$use_priorities = $GLOBALS['board']->getSettingValue('USE_PRIORITIES');
$show_ticket_help = $GLOBALS['board']->getSettingValue('SHOW_TICKET_HELP');
$use_firstname = $GLOBALS['board']->getSettingValue('USE_FIRSTNAME');
$private = $GLOBALS['board']->getSettingValue('PRIVATE_BOARD');
$send_email = $GLOBALS['board']->getSettingValue("SEND_EMAIL");

$color1 = $GLOBALS['board']->getSettingValue('TICKET_COLOR1');
$color2 = $GLOBALS['board']->getSettingValue('TICKET_COLOR2');
$color3 = $GLOBALS['board']->getSettingValue('TICKET_COLOR3');
$color4 = $GLOBALS['board']->getSettingValue('TICKET_COLOR4');

$color1_name = $GLOBALS['board']->getSettingValue('TICKET_TYPE1');
$color2_name = $GLOBALS['board']->getSettingValue('TICKET_TYPE2');
$color3_name = $GLOBALS['board']->getSettingValue('TICKET_TYPE3');
$color4_name = $GLOBALS['board']->getSettingValue('TICKET_TYPE4');

if ($GLOBALS['board']->getCreated() != null && $GLOBALS['board']->getCreated() != "") {
    echo '<tr><td colspan="2" style="text-align:right; width: 50%;">Board created</td><td colspan="2">';
    echo $GLOBALS['board']->getCreated();
    echo '</td></tr>';
}

echo '<tr><td colspan="2" style="text-align:right; width: 50%;">Team owning the board</td><td colspan="2">';
echo '<input type="text" name="board_team" value="' . $GLOBALS['board']->getSettingValue("BOARD_TEAM") . '" placeholder="Team owning the board"/>';
echo '</td></tr>';

echo '<tr><td colspan="2" style="text-align:right; width: 50%;">Team email</td><td colspan="2">';
echo '<input type="text" name="board_team_email" value="' . $GLOBALS['board']->getSettingValue("BOARD_TEAM_EMAIL") . '" placeholder="Teams email address"/>';
echo '</td></tr>';

echo '<tr><td colspan="2" style="text-align:right; width: 50%;">Private board (only users with access rights)</td><td colspan="2">';
if ($private == 1) {
    echo '<input type="checkbox" checked="yes" name="private_board" value="private_board" />';
} else {
    echo '<input type="checkbox" name="private_board" value="private_board" />';
}
echo '</td></tr>';

echo '<tr><td colspan="2" style="text-align:right; width: 50%;">Send email notifications</td><td colspan="2">';
if ($send_email == 1) {
    echo '<input type="checkbox" checked="yes" name="send_email" value="send_email" />';
} else {
    echo '<input type="checkbox" name="send_email" value="send_email" />';
}
echo '</td></tr>';

echo '<tr><td colspan="2" style="text-align:right; width: 50%;">Use WIP</td><td colspan="2">';
if ($use_wip == 1) {
    echo '<input type="checkbox" checked="yes" name="use_wip" value="use_wip" />';
} else {
    echo '<input type="checkbox" name="use_wip" value="use_wip" />';
}
echo '</td></tr>';

echo '<tr><td colspan="2" style="text-align:right;">Use Cycles</td><td colspan="2">';
if ($use_cycles == 1) {
    echo '<input type="checkbox" checked="yes" name="use_cycles" value="use_cycles" />';
} else {
    echo '<input type="checkbox" name="use_cycles" value="use_cycles" />';
}
echo '</td></tr>';

echo '<tr><td colspan="2" style="text-align:right;">Use parent/child linking</td><td colspan="2">';
if ($use_linking == 1) {
    echo '<input type="checkbox" checked="yes" name="use_linking" value="use_linking" />';
} else {
    echo '<input type="checkbox" name="use_linking" value="use_linking" />';
}
echo '</td></tr>';

echo '<tr><td colspan="2" style="text-align:right;">Use statistics</td><td colspan="2">';
if ($use_statistics == 1) {
    echo '<input type="checkbox" checked="yes" name="use_statistics" value="use_statistics" />';
} else {
    echo '<input type="checkbox" name="use_statistics" value="use_statistics" />';
}
echo '</td></tr>';

echo '<tr><td colspan="2" style="text-align:right;">Use event logging</td><td colspan="2">';
if ($use_logging == 1) {
    echo '<input type="checkbox" checked="yes" name="use_logging" value="use_logging" />';
} else {
    echo '<input type="checkbox" name="use_logging" value="use_logging" />';
}
echo '</td></tr>';

echo '<tr><td colspan="2" style="text-align:right;">Show ticket help</td><td colspan="2">';
if ($show_ticket_help == 1) {
    echo '<input type="checkbox" checked="yes" name="show_ticket_help" value="show_ticket_help" />';
} else {
    echo '<input type="checkbox" name="show_ticket_help" value="show_ticket_help" />';
}
echo '</td></tr>';

echo '<tr><td colspan="2" style="text-align:right; width: 50%;">Use first names only</td><td colspan="2">';
if ($use_firstname == 1) {
    echo '<input type="checkbox" checked="yes" name="use_firstname" value="use_firstname" />';
} else {
    echo '<input type="checkbox" name="use_firstname" value="use_firstname" />';
}
echo '</td></tr>';

echo '<tr><td colspan="2" style="text-align:right;">Use priorities instead of types</td><td colspan="2">';
if ($use_priorities == 1) {
    echo '<input type="checkbox" class="use_priorities" checked="yes" name="use_priorities" value="use_priorities" />';
} else {
    echo '<input type="checkbox" class="use_priorities" name="use_priorities" value="use_priorities" />';
}
echo '</td></tr>';

echo '<tr class="ticket_color_change"';
if ($use_priorities == 1) {
    echo ' style="display:none"';
}
echo '><td style="text-align:right;">Ticket type 1:</td><td style="text-align:left;"><input value="' . $color1_name . '" name="ticket_type1" type="text" size="10" maxlength="20" /></td>';
echo '<td><div class="color1" style="width: 10px; height: 10px; background-color: ' . $color1 . '"></div></td><td><a class="color_pick" data-colorid="1">Change color</td></tr>';

echo '<tr class="ticket_color_change"';
if ($use_priorities == 1) {
    echo ' style="display:none"';
}
echo '><td style="text-align:right;">Ticket type 2:</td><td style="text-align:left;"><input value="' . $color2_name . '" name="ticket_type2" type="text" size="10" maxlength="20" /></td>';
echo '<td><div class="color2" style="width: 10px; height: 10px; background-color: ' . $color2 . '"></div></td><td><a class="color_pick" data-colorid="2">Change color</td></tr>';

echo '<tr class="ticket_color_change"';
if ($use_priorities == 1) {
    echo ' style="display:none"';
}
echo '><td style="text-align:right;">Ticket type 3:</td><td style="text-align:left;"><input value="' . $color3_name . '" name="ticket_type3" type="text" size="10" maxlength="20" /></td>';
echo '<td><div class="color3" style="width: 10px; height: 10px; background-color: ' . $color3 . '"></div></td><td><a class="color_pick" data-colorid="3">Change color</td></tr>';

echo '<tr class="ticket_color_change"';
if ($use_priorities == 1) {
    echo ' style="display:none"';
}
echo '><td style="text-align:right;">Ticket type 4:</td><td style="text-align:left;"><input value="' . $color4_name . '" name="ticket_type4" type="text" size="10" maxlength="20" /></td>';
echo '<td><div class="color4" style="width: 10px; height: 10px; background-color: ' . $color4 . '"></div></td><td><a class="color_pick" data-colorid="4">Change color</td></tr>';

echo '<input type="hidden" value="' . $color1 . '" name="color1">';
echo '<input type="hidden" value="' . $color2 . '" name="color2">';
echo '<input type="hidden" value="' . $color3 . '" name="color3">';
echo '<input type="hidden" value="' . $color4 . '" name="color4">';

echo '<tr><td colspan="4">';

echo '<p style="text-align: center;"><input type="submit" class="form_button" value="Save"><input type="reset" class="form_button" value="Reset"></p>';
echo '</td></tr>';

echo '</table>';

echo '</form>';

echo '<div style="clear: both;"></div>';
echo '<h2>Administrative tools</h2>';
echo '<table border="0" width="50%" class="settings_table">';

echo '<tr><th>Description</th><th>Actions</th></tr>';

echo '<tr><td>Guest account</td>';
if ($GLOBALS['board']->getSettingValue("GUEST_PASSWORD") == "") {
    echo '<td><div class="enable_guest_account"><a>Enable</a></div>';
    echo '<div id="guest_password"></div>';
} else {
    echo '<td><div class="disable_guest_account"><a>Disable</a></div>';
    echo '<div id="guest_password" style="padding-top:10px">Password is ' . $GLOBALS['board']->getSettingValue("GUEST_PASSWORD") . '</div>';
}
echo '</td></tr>';

echo '<tr><td>Setup ticket auto-archive</td>';
echo '<td><div class="auto_archive"><a>Setup</a></div></td></tr>';

echo '<tr><td>Mass archive phase tickets</td>';
echo '<td><div class="mass_archive"><a>Select phase and range</a></div></td></tr>';

echo '<tr><td>Remove all archived tickets</td>';
echo '<td><div class="remove_archived_tickets"><a>Remove</a></div></td></tr>';

echo '<tr><td>Remove all tickets (!)</td>';
echo '<td><div class="remove_all_tickets"><a>Remove</a></div></td></tr>';

echo '<tr><td>Import data from old board version</td>';
echo '<td><div class="import_data"><a>Import</a></div></td></tr>';

if ($GLOBALS['cur_user']->getType() == "SYSTEM_ADMIN") {
    echo '<tr><td>Permanently delete all board data</td>';
    echo '<td><div class="delete_all_data"><a>Delete</a></div></td></tr>';
}

echo '</table>';
?>
