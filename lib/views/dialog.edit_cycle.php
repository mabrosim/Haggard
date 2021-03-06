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

$get_id = filter_input(INPUT_GET, 'id');
$id = $GLOBALS['db']->escape($get_id);
$cycle = new Cycle($id);

echo '<div id="pop_up">';
echo '<form id="cycle_form">';

echo '<table border="0" width="100%" cellspacing="5">';

$start = date("d.m.Y", strtotime($cycle->getStart()));
$stop = date("d.m.Y", strtotime($cycle->getStop()));

echo '<tr><td style="text-align:right;">Name</td><td style="text-align:left;"><input value="' . $cycle->getName() . '" name="name" type="text" size="30" maxlength="100" placeholder="Cycle name"/></td></tr>';

if ($GLOBALS['board']->getSettingValue("USE_WIP")) {
    echo '<tr><td style="text-align:right;">WIP limit</td><td style="text-align:left;"><input value="' . $cycle->getWIPLimit() . '" name="wip_limit" type="text" size="3" maxlength="3" placeholder="WIP limit"/></td></tr>';
}

echo '<tr><td style="text-align:right;"><label for="start_date">Start date</label></td><td style="text-align:left;"><input type="text" value="' . $start . '" id="start_date" name="start_date"/></td></tr>';
echo '<tr><td style="text-align:right;"><label for="end_date">End date</label></td><td style="text-align:left;"><input type="text" value="' . $stop . '" id="end_date" name="end_date"/></td></tr>';

echo '</table>';
echo '<p style="text-align: center;"><input type="submit" value="Save"><input type="reset" value="Reset"></p>';

echo '</form>';
echo '</div>';
?>
