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

// TODO: Permission check
if (!isset($_SESSION['username'])) {
    return;
}
if (!isset($GLOBALS['cur_user']) || !$GLOBALS['cur_user']->getPermission('create_ticket')) {
    echo '<h2>No access</h2>';
    return;
}

require_once './user.class.php';
require_once './cycle.class.php';
require_once './priority.class.php';

echo '<div id="pop_up">';

echo '<form id="ticket_form">';

echo '<table border="0" width="100%" cellspacing="5">';
echo '<tr><td style="text-align:right;">Title</td><td style="text-align:left;"><input name="title" type="text" size="31" maxlength="255" placeholder="Ticket title"></td></tr>';
echo '<tr><td style="text-align:right;">Additional info</td><td style="text-align:left;"><textarea name="info" type="text" maxlength="1000" style="width: 182px; max-width: 182px; height: 80px; max-height: 250px;" placeholder="Ticket additional information"></textarea></td></tr>';
if ($GLOBALS['board']->getSettingValue("USE_WIP")) {
    echo '<tr><td style="text-align:right;">WIP</td><td style="text-align:left;"><input name="wip" type="text" value="5" maxlength="2" size="2"></td></tr>';
} else {
    echo '<input name="wip" type="hidden" value="0">';
}
echo '<tr><td style="text-align:right;">Responsible</td><td style="text-align:left;"><select name="resp">';

echo '<option value="0">No responsible</option>';
$users = $GLOBALS['board']->getUsers();
if (count($users) > 0) {
    foreach ($users as $user) {
        if (ucwords(strtolower($user->getName())) == $_SESSION['username']) {
            echo '<option selected value="' . $user->getId() . '">' . $user->getName() . '</option>';
        } else {
            echo '<option value="' . $user->getId() . '">' . $user->getName() . '</option>';
        }
    }
}

$components = $GLOBALS['board']->getComponents();
if (count($components) > 0) {
    echo '<tr><td style="text-align:right;">Component</td><td style="text-align:left;"><select name="comp">';
    echo '<option value="0"></option>';
    foreach ($components as $component) {
        echo '<option value="' . $component->getId() . '">' . $component->getName() . '</option>';
    }
    echo '</select></td></tr>';
} else {
    echo '<input type="hidden" value="0" name="comp">';
}

if ($GLOBALS['board']->getSettingValue("USE_PRIORITIES")) {
    echo '<tr><td style="text-align:right;">Priority</td><td style="text-align:left;"><select name="prio">';
    echo '<option value="0">Low</option>';
    echo '<option value="1">Medium</option>';
    echo '<option value="2">Major</option>';
    echo '<option value="3">Showstopper</option>';
    echo '</select></td></tr>';
} else {
    $type1 = new Priority(0);
    $type2 = new Priority(1);
    $type3 = new Priority(2);
    $type4 = new Priority(3);

    /* TODO: Permission check */
    echo '<tr><td style="text-align:right;">Ticket type</td><td style="text-align:left;"><select name="prio">';
    echo '<option value="0">' . $type1->getName() . '</option>';
    echo '<option value="1">' . $type2->getName() . '</option>';
    echo '<option value="2">' . $type3->getName() . '</option>';
    echo '<option value="3">' . $type4->getName() . '</option>';

    echo '</select></td></tr>';
}

$phases = $GLOBALS['board']->getPhases();
echo '<tr><td style="text-align:right;">Phase</td><td style="text-align:left;"><select name="phase">';
foreach ($phases as $phase) {
    echo '<option value="' . $phase->getId() . '">' . $phase->getName() . '</option>';
}
echo '</select>';

echo '<tr><td width="30%" style="text-align:right; word-wrap:break-word">Reference</td><td style="text-align:left;"><input name="reference_id" type="text" maxlength="1000" size="31" placeholder="URL or identifier"></td></tr>';

if ($GLOBALS['board']->getSettingValue("USE_LINKING")) {
    echo '<tr><td style="text-align:right;">Parent ticket</td><td style="text-align:left;"><select name="parent">';

    $tickets = $GLOBALS['board']->getTickets();

    echo '<option value="0"> </option>';
    foreach ($tickets as $ticket) {
        echo '<option value="' . $ticket->getId() . '">' . $ticket->getTitle() . '</option>';
    }
    echo '</select></td></tr>';
} else {
    echo '<input type="hidden" name="parent" value="0">';
}

echo '</table>';

echo '<p style="text-align: center;"><input class="form_button" type="submit" value="Create"><input type="reset" value="Reset" class="form_button"></p>';
echo '</form>';

echo '</div>';
?>
