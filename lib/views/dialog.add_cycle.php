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
if (!isset($GLOBALS['cur_user']) || !$GLOBALS['cur_user']->getPermission('manage_cycles')) {
    echo '<h2>No access</h2>';
    return;
}

echo '<div id="pop_up">';
echo '<form id="cycle_form">';

echo '<table border="0" width="100%" cellspacing="5">';

echo '<tr><td style="text-align:right;">Name</td><td style="text-align:left;"><input value="" name="name" type="text" size="30" maxlength="100" placeholder="Cycle name"/></td></tr>';

if ($GLOBALS['board']->getSettingValue("USE_WIP") == "1") {
    echo '<tr><td style="text-align:right;">WIP/person</td><td style="text-align:left;"><input value="40" id="wip" name="wip" type="text" size="3" maxlength="3" placeholder="WIP / person" /></td></tr>';
}

$time = date("d.m.Y");
$res = $GLOBALS['db']->get_row("SELECT stop FROM cycle WHERE active = '1' ORDER BY stop DESC LIMIT 1");

if ($res) {
    $time = date("d.m.Y", strtotime($res->stop) + 86401);
}

echo '<tr><td style="text-align:right;"><label for="date">Start date</label></td><td style="text-align:left;"><input type="text" value="' . $time . '" id="date" name="date"/></td></tr>';
echo '<tr><td style="text-align:right;" valign="top">Cycle length</td>';
echo '<td style="text-align:left;"><select id="cycle_length">';
echo '<option value="1">1 week</option>';
echo '<option value="2" selected>2 weeks</option>';
echo '<option value="3">3 weeks</option>';
echo '<option value="4">4 weeks</option>';
echo '</select></td></tr>';

echo '<tr><td style="text-align:right;" valign="top">Commitment</td><td></td></tr>';
$total_wip = 0;

$users = $GLOBALS['board']->getUsers(1);

if ($users) {
    foreach ($users as $user) {
        $com = 100;

        echo '<tr><td colspan="2" style="text-align:left;">' . $user->getName() . '</div></td></tr>';
        echo '<tr><td colspan="2"><div class="slider" data-value="' . $com . '" data-id="' . $user->getId() . '" style="margin: 0; width:100%"></div>';
        echo '<div style="margin: 0; display: inline" id="amount' . $user->getId() . '" class="amount">' . $com . '%</div> / ';

        $wip_amount = round(40 * $com / 100);
        $total_wip += $wip_amount;

        echo '<div style="margin: 0; display: inline" id="wip_amount' . $user->getId() . '" class="wip_amount">' . $wip_amount . '</div> WIP points</td></tr>';
    }
}

echo '<tr><td style="text-align:right;" valign="top"></td><td style="text-align:right;">Total WIP: <div id="total_wip" style="font-weight: bold; display:inline">' . $total_wip . '</div></td></tr>';

echo '</table>';
echo '<p style="text-align: center;"><input type="submit" value="Create"><input type="reset" value="Reset"></p>';

echo '</form>';
echo '</div>';
?>
