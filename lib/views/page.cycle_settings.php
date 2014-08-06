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

echo '<script type="text/javascript" src="./js/cycle_settings.js"></script>';
echo '<h1>Cycles</h1>';

/* TODO: Permission check */
echo '<div class="add_cycle"><button>Add new cycle</button></div>';
echo '<div class="cycles_table">';
echo '<table border="0" class="settings_table">';

echo '<tr><th>Name</th><th>Start</th><th>Stop</th>';
if ($GLOBALS['board']->getSettingValue("USE_WIP")) {
    echo '<th>WIP limit</th>';
}
/* TODO: permission check */
echo '<th colspan="2">Actions</th></tr>';

$active_cycles = $GLOBALS['board']->getCycles(1);

foreach ($active_cycles as $cycle) {
    echo '<tr id="' . $cycle->getId() . '">';
    echo '<td>' . $cycle->getName() . '</td>';
    echo '<td>' . date("d.m.Y", strtotime($cycle->getStart())) . '</td>';
    echo '<td>' . date("d.m.Y", strtotime($cycle->getStop())) . '</td>';
    if ($GLOBALS['board']->getSettingValue("USE_WIP")) {
        echo '<td>' . $cycle->getWIPLimit() . '</td>';
    }

    /* PERMISSION */
    echo '<td><div class="done_cycle"><a data-id="' . $cycle->getId() . '">Set as done</a></div></td>';
    echo '<td><div class="edit_cycle"><a data-id="' . $cycle->getId() . '">Edit</a></div>';
    echo '</tr>';
}

echo '</table>';
echo '</div>';

$done_cycles = $GLOBALS['board']->getCycles(0);
if (count($done_cycles) > 0) {
    echo '<h2>Done cycles</h2>';
    echo '<div class="cycles_table">';
    echo '<table border="0" class="settings_table">';
    echo '<tr><th>Name</th><th>Start</th><th>Stop</th><th>WIP limit</th>';

    /* PERMISSION */
    echo '<th colspan="2">Actions</th></tr>';

    foreach ($done_cycles as $cycle) {
        echo '<tr id="' . $cycle->getId() . '">';
        echo '<td>' . $cycle->getName() . '</td>';
        echo '<td>' . date("d.m.Y", strtotime($cycle->getStart())) . '</td>';
        echo '<td>' . date("d.m.Y", strtotime($cycle->getStop())) . '</td>';
        echo '<td>' . $cycle->getWIPLimit() . '</td>';
        /* PERMISSION */
        echo '<td><div class="delete_cycle"><a data-id="' . $cycle->getId() . '">Delete cycle</a></div></td>';

        echo '</tr>';
    }
    echo '</table>';
    echo '</div>';
}
?>
