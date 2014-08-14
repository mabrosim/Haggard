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

if (isset($_SESSION['username'])) {
    if (!isset($GLOBALS['cur_user']) || !$GLOBALS['cur_user']->getPermission('manage_user_groups')) {
        echo '<h2>No access</h2>';
        return;
    }

    echo '<div id="pop_up">';

    echo '<form id="person_form">';
    echo '<table border="0" width="100%" cellspacing="5">';
    echo '<tr><td style="text-align:right;">Person to add</td><td style="text-align:left;"><select name="person">';

    $users = $GLOBALS['db']->get_results("SELECT u.id, u.name FROM user u LEFT JOIN user_board ub ON u.id = ub.user_id WHERE ub.board_id = '" . $GLOBALS['board']->getBoardId() . "'");

    foreach ($users as $user) {
        echo '<option selected value="' . $user->id . '">' . $user->name . '</option>';
    }

    echo '</td></tr>';
    echo '</table>';

    echo '<p style="text-align: center;"><input type="submit" value="Add"></p>';

    echo '</form>';
    echo '</div>';
}
?>
