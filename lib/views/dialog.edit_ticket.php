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
require_once './ticket.class.php';
require_once './component.class.php';
require_once './priority.class.php';
require_once '../config/board.config.php';
require_once '../config/global.config.php';

$get_id = filter_input(INPUT_GET, 'ticket_id');
$ticket_id = $GLOBALS['db']->escape($get_id);
$ticket = new Ticket($ticket_id);

if (!isset($GLOBALS['cur_user']) || !$GLOBALS['cur_user']->getPermission('edit_ticket', $ticket->getBoard()->getBoardId())) {
    echo '<h2>No access</h2>';
    return;
}

echo '<div id="pop_up">';

echo '<form id="ticket_form" style="margin:0">';

echo '<table border="0" width="100%" cellspacing="5">';
echo '<tr><td style="text-align:right;">Title</td><td style="text-align:left;"><input name="title" value="' . $ticket->getTitle() . '" type="text" size="31" maxlength="255" placeholder="Ticket title"></td></tr>';
echo '<tr><td style="text-align:right;">Additional info</td><td style="text-align:left;"><textarea name="info" type="text" maxlength="1000" style="width: 182px; max-width: 182px; height: 80px; max-height: 250px;" placeholder="Ticket additional information">' . $ticket->getInfo() . '</textarea></td></tr>';
if ($ticket->getBoard()->getSettingValue("USE_WIP")) {
    echo '<tr><td style="text-align:right;">WIP</td><td style="text-align:left;"><input name="wip" value="' . $ticket->getWIP() . '" type="text" maxlength="3" size="2"></td></tr>';
} else {
    echo '<input name="wip" value="' . $ticket->getWIP() . '" type="hidden">';
}

echo '<tr><td style="text-align:right;">Responsible</td><td style="text-align:left;"><select name="resp">';
$users = $ticket->getBoard()->getUsers();
echo '<option selected value="0">No responsible</option>';
if ($users) {
    foreach ($users as $user) {
        if ($user->getId() == $ticket->getResponsibleId()) {
            echo '<option selected value="' . $user->getId() . '">' . $user->getName() . '</option>';
        } else {
            echo '<option value="' . $user->getId() . '">' . $user->getName() . '</option>';
        }
    }
}

echo '</select></td></tr>';

$components = $ticket->getBoard()->getComponents();
if ($components) {
    echo '<tr><td style="text-align:right;">Component</td><td style="text-align:left;"><select name="comp">';
    echo '<option value="0"></option>';
    foreach ($components as $component) {
        if ($ticket->getComponentId() == $component->getId()) {
            echo '<option selected value="' . $ticket->getComponentId() . '">' . $component->getName() . '</option>';
        } else {
            echo '<option value="' . $component->getId() . '">' . $component->getName() . '</option>';
        }
    }
    echo '</select></td></tr>';
} else {
    echo '<input type="hidden" value="0" name="comp">';
}

$prio = $ticket->getPriorityId();
if ($ticket->getBoard()->getSettingValue("USE_PRIORITIES")) {
    echo '<tr><td style="text-align:right;">Priority</td><td style="text-align:left;"><select name="prio">';
} else {
    echo '<tr><td style="text-align:right;">Ticket type</td><td style="text-align:left;"><select name="prio">';
}

$type1 = new Priority(0);
$type2 = new Priority(1);
$type3 = new Priority(2);
$type4 = new Priority(3);

if ($prio == 0) {
    echo '<option value="0" selected>' . $type1->getName() . '</option>';
} else {
    echo '<option value="0">' . $type1->getName() . '</option>';
}

if ($prio == 1) {
    echo '<option value="1" selected>' . $type2->getName() . '</option>';
} else {
    echo '<option value="1">' . $type2->getName() . '</option>';
}

if ($prio == 2) {
    echo '<option value="2" selected>' . $type3->getName() . '</option>';
} else {
    echo '<option value="2">' . $type3->getName() . '</option>';
}

if ($prio == 3) {
    echo '<option value="3" selected>' . $type4->getName() . '</option>';
} else {
    echo '<option value="3">' . $type4->getName() . '</option>';
}

echo '</select></td></tr>';

$phases = $ticket->getBoard()->getPhases();
echo '<tr><td style="text-align:right;">Phase</td><td style="text-align:left;"><select name="phase">';
foreach ($phases as $phase) {
    if ($ticket->getPhaseId() == $phase->getId()) {
        echo '<option selected value="' . $phase->getId() . '">' . $phase->getName() . '</option>';
    } else {
        echo '<option value="' . $phase->getId() . '">' . $phase->getName() . '</option>';
    }
}
echo '</select>';

if ($GLOBALS['board']->getSettingValue("USE_CYCLES")) {
    echo '<tr><td style="text-align:right;">Cycle</td><td style="text-align:left;"><select name="cycle">';
    $cycles = $GLOBALS['board']->getCycles(1);
    foreach ($cycles as $cycle) {
        if ($ticket->getCycleId() == $cycle->getId()) {
            echo '<option selected value="' . $cycle->getId() . '">' . $cycle->getName() . '</option>';
        } else {
            echo '<option value="' . $cycle->getId() . '">' . $cycle->getName() . '</option>';
        }
    }
}

echo '<tr><td width="30%" style="text-align:right; word-wrap:break-word">Reference</td><td style="text-align:left;"><input value="' . $ticket->getReferenceString() . '" name="reference_id" type="text" maxlength="1000" size="31" placeholder="URL or identifier"></td></tr>';

if ($ticket->getBoard()->getSettingValue("USE_LINKING")) {
    $parent_id = $ticket->getParentId();

    $links = $ticket->getBoard()->getTickets();

    echo '<tr><td style="text-align:right;">Parent ticket</td><td style="text-align:left;"><select name="parent">';
    echo '<option value="0"> </option>';

    if ($links) {
        foreach ($links as $link) {
            if ($link->getId() == $ticket->getId()) {
                continue;
            }
            if ($parent_id == $link->getId()) {
                echo '<option selected value="' . $link->getId() . '">' . $link->getTitle() . '</option>';
            } else {
                echo '<option value="' . $link->getId() . '">' . $link->getTitle() . '</option>';
            }
        }
    }

    echo '</select></td></tr>';
} else {
    echo '<input type="hidden" name="parent" value="0">';
}

echo '<tr><td width="30%" style="text-align:right; word-wrap:break-word">Ticket URL</td><td style="text-align:left;"><input value="' . $ticket->getURL() . '" name="ticket_url" readonly="readonly" type="text" maxlength="70" size="31" style="background-color: #f0f0f0"></td></tr>';

echo '</table>';

echo '<p style="text-align: center; margin:0; margin-top: 10px;"><input type="submit" class="form_button" value="Save changes"></p>';
echo '</form>';

if (isset($GLOBALS['cur_user']) &&
    ($GLOBALS['cur_user']->getPermission('archive_ticket', $ticket->getBoard()->getBoardId()) ||
        $GLOBALS['cur_user']->getPermission('delete_ticket', $ticket->getBoard()->getBoardId()) ||
        $GLOBALS['cur_user']->getPermission('move_ticket_board', $ticket->getBoard()->getBoardId()) ||
        $GLOBALS['cur_user']->getPermission('copy_ticket_board', $ticket->getBoard()->getBoardId()))
) {
    echo '<div class="show_more_options">';
    echo '<a href="#" style="text-decoration: none;"><div class="down_triangle"/> More options <div class="down_triangle"/></a>';
    echo '</div>';
    echo '<div style="margin-left: auto; margin-right: auto; text-align:center;" class="more_options">';
    if (isset($GLOBALS['cur_user']) && $GLOBALS['cur_user']->getPermission('archive_ticket', $ticket->getBoard()->getBoardId())) {
        echo '<form id="archive_ticket_form" style="display:inline;">';
        if ($ticket->isActive()) {
            echo '<input type="submit" class="form_button" value="Archive ticket">';
        } else {
            echo '<input type="submit" class="form_button" value="Unarchive ticket">';
        }
        echo '</form>';
    }

    if (isset($GLOBALS['cur_user']) && $GLOBALS['cur_user']->getPermission('delete_ticket', $ticket->getBoard()->getBoardId())) {
        echo '<form id="delete_ticket_form" style="display: inline;"><input class="form_button" type="submit" value="Delete ticket">';
        echo '</form>';
    }

    if (isset($GLOBALS['cur_user']) && $GLOBALS['cur_user']->getPermission('move_ticket_board', $ticket->getBoard()->getBoardId())) {
        echo '<form id="move_ticket_board_form" style="display: inline;"><input class="form_button" type="submit" value="Move ticket to another board">';
        echo '</form>';
    }

    if (isset($GLOBALS['cur_user']) && $GLOBALS['cur_user']->getPermission('copy_ticket_board', $ticket->getBoard()->getBoardId())) {
        echo '<form id="copy_ticket_board_form" style="display: inline;"><input class="form_button" type="submit" value="Copy ticket to another board">';
        echo '</form>';
    }
    echo '</div>';
}

echo '</div>';
?>
