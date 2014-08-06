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

$columns = array("A", "B", "C", "D", "E", "F", "G", "H", "I", "J", "K", "L", "M", "N", "O", "P", "Q", "R", "S", "T", "U", "V", "X", "Y", "Z");

session_start();
require_once '../config/database.config.php';
require_once '../config/board.config.php';
require_once '../config/global.config.php';
require_once '../3rdparty/ezSQL/shared/ez_sql_core.php';
require_once '../3rdparty/ezSQL/mysql/ez_sql_mysql.php';
require_once '../3rdparty/PHPExcel/PHPExcel.php';
require_once '../3rdparty/PHPExcel/PHPExcel/Writer/Excel2007.php';
require_once './board.class.php';
require_once './log.class.php';
require_once './email.class.php';
require_once './cycle.class.php';
require_once './component.class.php';
require_once './phase.class.php';
require_once './benchmark.class.php';
require_once './reference.class.php';
require_once './priority.class.php';
require_once './ticket.class.php';

$GLOBALS['benchmark'] = new Benchmark();
if (isset($_SESSION['userid'])) {
    /* Connect to database */
    $GLOBALS['db'] = new ezSQL_mysql($database['username'], $database['password'], $database['database'], $database['host']);

    $GLOBALS['board'] = new Board($GLOBALS['board_name']);
    $GLOBALS['logger'] = new Log();
    $GLOBALS['email'] = new Email();
    $GLOBALS['mem'] = null;

    date_default_timezone_set($GLOBALS['board']->getBoardTimezone());

    $GLOBALS['cur_user'] = new User($_SESSION['userid']);

    $type = mysql_real_escape_string($_GET['type']);

    $excel = new PHPExcel();

    // Set properties
    $excel->getProperties()->setCreator($GLOBALS['cur_user']->getName());
    $excel->getProperties()->setLastModifiedBy($GLOBALS['cur_user']->getName());
    $excel->setActiveSheetIndex(0);

    $query = "SELECT * FROM ticket";

    if ($type == 'current') {
        $excel->getProperties()->setTitle("Haggard current tickets " . $GLOBALS['board']->getBoardName() . ' ' . date("d.m.Y"));
        $excel->getProperties()->setSubject("Haggard current tickets " . $GLOBALS['board']->getBoardName() . ' ' . date("d.m.Y"));
        $query = "SELECT * FROM ticket WHERE board_id = '" . $GLOBALS['board']->getBoardID() . "' AND active = '1' AND deleted = '0'";
    } else if ($type == 'archived') {
        $excel->getProperties()->setTitle("Haggard archived tickets " . $GLOBALS['board']->getBoardName() . ' ' . date("d.m.Y"));
        $excel->getProperties()->setSubject("Haggard archived tickets " . $GLOBALS['board']->getBoardName() . ' ' . date("d.m.Y"));
        $query = "SELECT * FROM ticket WHERE board_id = '" . $GLOBALS['board']->getBoardID() . "' AND active = '0' AND deleted = '0'";
    } else if ($type == 'deleted') {
        $excel->getProperties()->setTitle("Haggard deleted tickets " . $GLOBALS['board']->getBoardName() . ' ' . date("d.m.Y"));
        $excel->getProperties()->setSubject("Haggard deleted tickets " . $GLOBALS['board']->getBoardName() . ' ' . date("d.m.Y"));
        $query = "SELECT * FROM ticket WHERE board_id = '" . $GLOBALS['board']->getBoardID() . "' AND deleted = '1'";
    } else if ($type == 'my_tickets') {
        $excel->getProperties()->setTitle("Haggard tickets " . $_SESSION['username'] . ' ' . date("d.m.Y"));
        $excel->getProperties()->setSubject("Haggard tickets " . $_SESSION['username'] . ' ' . date("d.m.Y"));
        $query = "SELECT * FROM ticket WHERE responsible = '" . $_SESSION['userid'] . "' AND deleted = '0'";
    }

    $GLOBALS['db']->query("SELECT * FROM ticket WHERE ID = '0' LIMIT 1");
    $column = 0;
    $sheet = $excel->getActiveSheet();
    $sheet->setCellValueByColumnAndRow($column, 1, 'Board');
    $sheet->getStyle($columns[$column] . '1')->getFont()->setBold(true);
    $sheet->getColumndimension($columns[$column])->setWidth(30);

    $column++;
    foreach ($GLOBALS['db']->col_info as $info) {
        if ($info->name == 'id' || $info->name == 'board_id' || $info->name == 'active' || $info->name == 'deleted')
            continue;

        $sheet->setCellValueByColumnAndRow($column, 1, ucfirst($info->name));
        $sheet->getStyle($columns[$column] . '1')->getFont()->setBold(true);
        $sheet->getColumndimension($columns[$column])->setWidth(30);
        $column++;
    }

    $tickets = $GLOBALS['db']->get_results($query);

    $row = 2;
    foreach ($tickets as $ticket) {
        $t = new Ticket($ticket->id);
        $col = 0;
        $sheet->setCellValueByColumnAndRow($col++, $row, $t->getBoard()->getBoardName());
        $sheet->setCellValueByColumnAndRow($col++, $row, $ticket->data);
        $sheet->setCellValueByColumnAndRow($col++, $row, $ticket->info);
        $resp = new User($ticket->responsible);
        $sheet->setCellValueByColumnAndRow($col, $row, $resp->getName());
        $sheet->getCell($columns[$col] . $row)->getHyperlink()->setUrl('mailto:' . $resp->getEmail());
        $col++;

        if ($t->getBoard()->getSettingValue("USE_WIP")) {
            $sheet->setCellValueByColumnAndRow($col++, $row, $ticket->wip);
        } else {
            $col++;
        }

        if ($t->getBoard()->getSettingValue("USE_CYCLES")) {
            $cycle = new Cycle($ticket->cycle);
            $sheet->setCellValueByColumnAndRow($col++, $row, $cycle->getName());
        } else {
            $col++;
        }

        $phase = new Phase($ticket->phase);
        $sheet->setCellValueByColumnAndRow($col++, $row, $phase->getName());

        $prio = new Priority($ticket->priority);
        $sheet->setCellValueByColumnAndRow($col++, $row, $prio->getName());

        $ref = new Reference($ticket->reference_id);
        if ($ref->getURL()) {
            $sheet->setCellValueByColumnAndRow($col, $row, $ref->getId());
            $sheet->getCell($columns[$col] . $row)->getHyperlink()->setUrl($ref->getURL());
        }
        $col++;

        $comp = new Component($ticket->component);
        $sheet->setCellValueByColumnAndRow($col++, $row, $comp->getName());
        $sheet->setCellValueByColumnAndRow($col++, $row, $ticket->last_change);
        $sheet->setCellValueByColumnAndRow($col++, $row, $ticket->created);
        $row++;
    }

    $filename = date("Y-m-d") . '_haggard_report.xlsx';
    header("Pragma: public");
    header("Expires: 0");
    header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
    header("Content-Type: application/force-download");
    header("Content-Type: application/octet-stream");
    header("Content-Type: application/download");
    header("Content-Disposition: attachment;filename=" . $filename);
    header("Content-Transfer-Encoding: binary ");


    $writer = PHPExcel_IOFactory::createWriter($excel, 'Excel2007');
    ob_end_clean();
    $writer->save('php://output');
    $excel->disconnectWorksheets();
    unset($excel);
}
?>
