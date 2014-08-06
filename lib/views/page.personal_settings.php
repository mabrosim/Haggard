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
echo '<script type="text/javascript" src="./js/personal_settings.js"></script>' . PHP_EOL;
echo '<h1>My settings</h1>';

$user = new User($_SESSION['userid']);

echo '<form id="personal_settings">';
echo '<table border="0" width="40%" class="settings_table">';
echo '<tr><th colspan="2">General settings</th></tr>';
echo '<tr><td style="text-align:right;">Alias</td><td>';
echo '<input type="text" id="alias" name="alias" value="' . $user->getAlias() . '" placeholder="Name used instead real name" style="width: 250px"/></td></tr>';
echo '<tr><td style="text-align:right;">Timezone</td><td>';
echo '<select id="timezone" name="timezone">';
$zones = timezone_identifiers_list();
foreach ($zones as $zone) {
    if ($zone == $user->getTimezone()) {
        echo '<option selected>' . $zone . '</option>';
    } else {
        echo '<option>' . $zone . '</option>';
    }
}

echo '</select></td></tr>';
echo '<tr><td style="text-align:right; padding-bottom: 2em">Get e-mail notifications</td><td style="padding-bottom: 2em;">';
if ($user->getSetting('send_email') == 1) {
    echo '<input type="checkbox" checked="yes" name="send_email" value="send_email" />';
} else {
    echo '<input type="checkbox" name="send_email" value="send_email" />';
}
echo '</td></tr>';

echo '<tr><th colspan="2">Ticket settings</th></tr>';

echo '<tr><td style="text-align:right;">Show responsible</td><td>';
if ($user->getSetting('show_ticket_responsible') == 1) {
    echo '<input type="checkbox" checked="yes" name="show_resp" value="show_resp" />';
} else {
    echo '<input type="checkbox" name="show_resp" value="show_resp" />';
}
echo '</td></tr>';

echo '<tr><td style="text-align:right;">Show component</td><td>';
if ($user->getSetting('show_ticket_component') == 1) {
    echo '<input type="checkbox" checked="yes" name="show_comp" value="show_comp" />';
} else {
    echo '<input type="checkbox" name="show_comp" value="show_comp" />';
}
echo '</td></tr>';

echo '<tr><td style="text-align:right;">Show full reference link</td><td>';
if ($user->getSetting('show_ticket_reference') == 1) {
    echo '<input type="checkbox" checked="yes" name="show_ref" value="show_ref" />';
} else {
    echo '<input type="checkbox" name="show_ref" value="show_ref" />';
}
echo '</td></tr>';

echo '<tr><td style="text-align:right;">Show ticket additional info</td><td>';
if ($user->getSetting('show_ticket_info') == 1) {
    echo '<input type="checkbox" checked="yes" name="show_info" value="show_info" />';
} else {
    echo '<input type="checkbox" name="show_info" value="show_info" />';
}
echo '</td></tr>';

if ($GLOBALS['board']->getSettingValue('USE_WIP')) {
    echo '<tr><td style="text-align:right;">Show WIP</td><td>';
    if ($user->getSetting('show_ticket_wip') == 1) {
        echo '<input type="checkbox" checked="yes" name="show_wip" value="show_wip" />';
    } else {
        echo '<input type="checkbox" name="show_wip" value="show_wip" />';
    }
    echo '</td></tr>';
}

echo '<tr><td style="text-align:right;">Show ticket creation time</td><td>';
if ($user->getSetting('show_ticket_created') == 1) {
    echo '<input type="checkbox" checked="yes" name="show_created" value="show_created" />';
} else {
    echo '<input type="checkbox" name="show_created" value="show_created" />';
}
echo '</td></tr>';

echo '<tr><td style="text-align:right;">Show ticket last change time</td><td>';
if ($user->getSetting('show_ticket_changed') == 1) {
    echo '<input type="checkbox" checked="yes" name="show_changed" value="show_changed" />';
} else {
    echo '<input type="checkbox" name="show_changed" value="show_changed" />';
}
echo '</td></tr>';

echo '<tr><td style="text-align:right;">Completely hide extra info</td><td>';
if ($user->getSetting('hide_extra_info') == 1) {
    echo '<input type="checkbox" checked="yes" name="hide_extra_info" value="hide_extra_info" />';
} else {
    echo '<input type="checkbox" name="hide_extra_info" value="hide_extra_info" />';
}
echo '</td></tr>';

echo '<td colspan="2">';

echo '<p style="text-align: center;"><input type="submit" class="form_button" value="Save"><input type="reset" class="form_button" value="Reset"></p>';
echo '</td>';

echo '</table>';

echo '</form>';
?>
