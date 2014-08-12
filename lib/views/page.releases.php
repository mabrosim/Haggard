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
if (!isset($GLOBALS['cur_user']) || !$GLOBALS['cur_user']->getPermission('manage_releases')) {
    echo '<h2>No access</h2>';
    return;
}

echo '<script type="text/javascript" src="./js/phase_settings.js"></script>';
echo '<div class="phases_table">';

$releases = $GLOBALS['db']->get_results("SELECT * FROM phase_release WHERE board_id = '" . $GLOBALS['board']->getBoardId() . "' ORDER BY released DESC");

echo '<h1>Releases</h1>';
echo '<div class="do_release" style="margin-bottom: 20px;"><a>Make release</a></div>';
if (count($releases) > 0) {
    echo '<table border="0" class="settings_table">';
    echo '<tr><th>Release tag</th><th>Released</th></tr>';
    foreach ($releases as $release) {
        echo '<tr><td><a href="./lib/release_note.php?release=' . $release->id . '">' . $release->name . '</a></td>';
        echo '<td>' . date('d.m.Y H:i', strtotime($release->released . ' UTC')) . '</td></tr>';
    }
    echo '</table>';
} else {
    echo '<p>No releases done</p>';
}
?>
