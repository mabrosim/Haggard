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

session_start();
require_once '../config/database.config.php';
require_once '../config/board.config.php';
require_once '../config/global.config.php';
require_once '../3rdparty/ezSQL/shared/ez_sql_core.php';
require_once '../3rdparty/ezSQL/mysql/ez_sql_mysql.php';
require_once 'reference.class.php';
require_once 'ticket.class.php';
require_once 'board.class.php';

$release_get = filter_input(INPUT_GET, 'release');
if (isset($_SESSION['username']) && isset($release_get)) {

    $GLOBALS['db'] = new ezSQL_mysql($database['username'], $database['password'], $database['database'], $database['host']);

    $id = $GLOBALS['db']->escape($release_get);
    $release = $GLOBALS['db']->get_row("SELECT * FROM phase_release WHERE id = '" . $id . "'");

    if ($release) {
        $filename = $release->name . '_release_note.txt';

        header("Pragma: public");
        header("Expires: 0");
        header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
        header("Content-Type: application/txt");
        header("Content-Type: application/download");
        header("Content-Disposition: attachment;filename=" . $filename);
        header("Content-Transfer-Encoding: binary ");

        $GLOBALS['board'] = new Board($release->board_id);

        echo "" . $board->getBoardName() . "\n";
        echo "Release tag: " . $release->name . "\n";
        echo "" . date('d.m.Y H:i', strtotime($release->released . ' UTC')) . "\n";
        echo "RELEASE NOTES\n";
        echo "-------------------\n\n";

        echo "Changes in this version include: \n\n";

        $tickets = $GLOBALS['db']->get_results("SELECT t.* FROM ticket t LEFT JOIN release_ticket rt ON t.id = rt.ticket_id WHERE t.board_id = '" . $board->getBoardId() . "' AND rt.release_id = '" . $release->id . "'");
        if (count($tickets) > 0) {
            foreach ($tickets as $ticket) {
                $t = new Ticket($ticket->id);
                $references = $t->getReferences();
                $comp = $t->getComponentStr();
                $resp = $t->getResponsible()->getName();

                echo "o Ticket " . $ticket->id . ":\t";
                if ($resp == '') {
                    $resp = "No responsible";
                }

                echo $ticket->data . " (" . $resp . ")";
                if ($comp != '') {
                    echo " (" . $comp . ")";
                }

                if (count($references) > 0) {
                    echo " (";
                    $refs = "";
                    foreach ($references as $ref) {
                        $refs .= $ref->getType() . ": " . $ref->getId() . ",";
                    }
                    echo substr($refs, 0, -1);
                    echo ")";
                }

                echo "\n";
            }
        } else {
            echo "No changes in this version\n";
        }

        return;
    }
}

echo "No release found";
?>
