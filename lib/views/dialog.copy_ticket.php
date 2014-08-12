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

echo '<div id="pop_up">';
$ticket_id = filter_input(INPUT_GET, 'ticket_id');
if (!isset($ticket_id)) {
    return;
}

$ticket = new Ticket($GLOBALS['db']->escape($ticket_id));

if (!isset($GLOBALS['cur_user']) || !$GLOBALS['cur_user']->getPermission('move_ticket_board', $ticket->getBoard()->getBoardId())) {
    echo '<p>No access</p>';
    return;
}

$boards = $GLOBALS['cur_user']->getUserBoards();

echo '<form id="copy_ticket_form" style="margin:0">';
echo '<table border="0" width="100%" cellspacing="5">';
echo '<tr><td style="text-align:right;">Copy to</td><td style="text-align:left;"><select name="board" style="font-size: 12px;">';
echo '<option value="0"></option>';
foreach ($boards as $b) {
    echo '<option value="' . $b->getBoardId() . '">' . $b->getBoardName() . '</option>';
}
echo '</select></td></tr>';

echo '<tr><td style="text-align:right;">Responsible</td><td style="text-align:left;"><select name="responsible" style="font-size: 12px;">';
echo '</select></td></tr>';

echo '</table>';

echo '<p style="text-align: center; margin:0; margin-top: 10px;"><input type="submit" class="form_button" value="Copy"></p>';
echo '</form>';
echo '</div>';
?>
