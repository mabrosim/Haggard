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
if (!isset($GLOBALS['cur_user']) || !$GLOBALS['cur_user']->getPermission('manage_components')) {
    echo '<h2>No access</h2>';
    return;
}

echo '<script type="text/javascript" src="./js/component_settings.js"></script>';
echo '<h1>Components</h1>';

echo '<div class="add_component"><button>Add new component</button></div>';

echo '<div class="components_table">';
echo '<table border="0" class="settings_table">';

echo '<tr><th>Name</th>';
echo '<th colspan="2">Actions</th></tr>';

$components = $GLOBALS['board']->getComponents();
foreach ($components as $component) {
    echo '<tr id="' . $component->getId() . '">';
    echo '<td>' . $component->getName() . '</td>';
    echo '<td><div class="delete_component"><a data-id="' . $component->getId() . '">Delete</a></div></td>';
    echo '<td><div class="edit_component"><a data-id="' . $component->getId() . '">Edit</a></div>';
    echo '</tr>';
}

echo '</table>';
echo '</div>';
?>
