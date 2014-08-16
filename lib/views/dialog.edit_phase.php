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
if (!isset($GLOBALS['cur_user']) || !$GLOBALS['cur_user']->getPermission('manage_phases')) {
    echo '<h2>No access</h2>';
    return;
}

$get_id = filter_input(INPUT_GET, 'id');
$id = $GLOBALS['db']->escape($get_id);
echo '<div id="pop_up">';
echo '<form id="phase_form">';

$phase = $GLOBALS['db']->get_row("SELECT * FROM phase WHERE id = '" . $id . "' LIMIT 1");

echo '<table border="0" width="100%" cellspacing="5">';

echo '<tr><td style="text-align:right;">Name</td><td style="text-align:left;"><input value="' . $phase->name . '" name="name" type="text" size="30" maxlength="100" placeholder="Phase name" /></td></tr>';

if ($GLOBALS['board']->getSettingValue("USE_WIP")) {
    echo '<tr><td style="text-align:right;">WIP limit</td><td style="text-align:left;"><input value="' . $phase->wip_limit . '" name="wip_limit" type="text" size="3" maxlength="3" placeholder="Phase WIP limit"/></td></tr>';
}

echo '<tr><td style="text-align:right;">Ticket limit (0 = no limit)</td><td style="text-align:left;"><input value="' . $phase->ticket_limit . '" name="ticket_limit" type="text" size="3" maxlength="3" placeholder="Phase ticket limit"/></td></tr>';

echo '<tr><td style="text-align:right;">Force comment</td><td style="text-align:left;"><input value="' . $phase->force_comment . '" name="force_comment" type="text" size="30" maxlength="150" placeholder="Question"/></td></tr>';

echo '<tr><td style="text-align:right;" valign="top">Help text</td><td style="text-align:left;"><textarea name="help_text" rows="10" cols="30" placeholder="Phase help text">' . $phase->help . '</textarea></td></tr>';
echo '<tr><td style="text-align:right;" valign="top">E-mail notifications (multiple selection)</td>';

echo '<td style="text-align:left;"><select id="phase_email_notifications" multiple="multiple">';

$groups = $GLOBALS['db']->get_results("SELECT id, name FROM user_group WHERE board_id = '" . $GLOBALS['board']->getBoardId() . "'");

if ($groups) {
    foreach ($groups as $group) {
        $phase_email = $GLOBALS['db']->get_var("SELECT group_id FROM phase_email_notification WHERE group_id = '" . $group->id . "' AND phase_id = '" . $id . "'");
        if ($phase_email) {
            echo '<option value="' . $group->id . '" selected="selected">' . $group->name . '</option>';
        } else {
            echo '<option value="' . $group->id . '">' . $group->name . '</option>';
        }
    }
}

echo '</select>';
echo '</td></tr>';

echo '<tr><td style="text-align:right;" width="50%">Notify if no tickets in phase</td><td style="text-align:left;">';
if ($phase->notify_empty) {
    echo '<input type="checkbox" name="notify_empty" value="notify_empty" checked="checked">';
} else {
    echo '<input type="checkbox" name="notify_empty" value="notify_empty">';
}
echo '</td></tr>';

echo '</table>';

echo '<p style="text-align: center;"><input type="submit" value="Save"><input type="reset" value="Reset"></p>';

echo '</form>';
echo '</div>';
?>
