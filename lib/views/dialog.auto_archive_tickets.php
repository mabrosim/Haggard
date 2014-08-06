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

if (!isset($GLOBALS['cur_user']) || !$GLOBALS['cur_user']->getPermission('manage_board_settings')) {
    echo '<h2>No access</h2>';
    return;
}

echo '<div id="pop_up">';
echo '<form id="auto_archive_form">';
echo '<table border="0" width="100%" cellspacing="5">';

echo '<tr><td style="text-align: right;">Enabled</td><td style="text-align:left;">';
echo '<input type="checkbox" name="enabled" id="enabled"';
if ($GLOBALS['board']->getSettingValue("AUTO_ARCHIVE") == "1") {
    echo ' checked="checked"';
}
echo '>';
echo '</td></tr>';

echo '<tr><td style="text-align:right;">Last updated > </td><td style="text-align:left;"><select name="last_update" class="disable"';

if ($GLOBALS['board']->getSettingValue("AUTO_ARCHIVE") != "1") {
    echo ' disabled="disabled"';
}
echo '>';
for ($i = 1; $i < 9; $i++) {
    if (((int) $GLOBALS['board']->getSettingValue("AUTO_ARCHIVE_THRESHOLD")) == $i) {
        echo '<option selected value="' . $i . '">' . $i . ' week';
    } else {
        echo '<option value="' . $i . '">' . $i . ' week';
    }
    if ($i > 1)
        echo 's';
    echo '</option>';
}
echo '</select></td></tr>';

echo '<tr><td style="text-align:right;" valign="top">Phases</td><td style="text-align:left;">';
$phases = explode(',', $GLOBALS['board']->getSettingValue("AUTO_ARCHIVE_PHASES"));
$res = $GLOBALS['db']->get_results("SELECT * FROM phase WHERE board_id = '" . $GLOBALS['board']->getBoardId() . "'");
foreach ($res as $phase) {
    echo '<input type="checkbox" name="phases" value="' . $phase->id . '" class="disable phase_select"';
    if ($GLOBALS['board']->getSettingValue("AUTO_ARCHIVE") != "1") {
        echo ' disabled="disabled"';
    }

    if (in_array($phase->id, $phases)) {
        echo ' checked="checked"';
    }

    echo '>' . $phase->name . '<br/>';
}

echo '</td></tr>';
echo '</table>';
echo '<p style="text-align: center;"><input class="form_button" type="submit"value="Save"></p>';
echo '</form>';
?>
