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

require_once 'ticket.class.php';

class Cycle
{

    private $id = 0;
    private $board_id = 0;
    private $title = "";
    private $start = 0;
    private $stop = 0;
    private $wip_limit = 0;
    private $active = 0;

    public function __construct($id = 0)
    {
        $cycle = NULL;

        if ($id == 0) {
            $cycle = $GLOBALS['db']->get_row("SELECT id, board_id, data, start, stop, wip_limit, active FROM cycle WHERE stop >= UTC_TIMESTAMP() AND board_id = '" . $GLOBALS['board']->getBoardId() . "' ORDER BY start ASC LIMIT 1");
        } else {
            $cycle = $GLOBALS['db']->get_row("SELECT id, board_id, data, start, stop, wip_limit, active FROM cycle WHERE id = '" . $id . "' AND board_id = '" . $GLOBALS['board']->getBoardId() . "' LIMIT 1");
        }

        if ($cycle) {
            $this->id = $cycle->id;
            $this->board_id = $cycle->board_id;
            $this->title = $cycle->data;
            $this->start = $cycle->start;
            $this->stop = $cycle->stop;
            $this->wip_limit = $cycle->wip_limit;
            $this->active = $cycle->active;
        } else {
            $this->id = -1;
        }
    }

    public function getTickets()
    {
        $tickets = array();
        $res = $GLOBALS['db']->get_results("SELECT id FROM ticket WHERE board_id = '" . $GLOBALS['board']->getBoardId() . "' AND cycle = '" . $this->id . "' AND active = '1' AND deleted = '0' ORDER BY data");
        if ($res) {
            foreach ($res as $ticket) {
                $tickets[] = new Ticket($ticket->id);
            }
        }

        return $tickets;
    }

    public function getName()
    {
        return $this->title;
    }

    public function getId()
    {
        return $this->id;
    }

    public function getWIPLimit()
    {
        return $this->wip_limit;
    }

    public function getCurrentWIP()
    {
        return 0;
    }

    public function getWIPLeft()
    {
        return 0;
    }

    public function getStart()
    {
        return $this->start;
    }

    public function getStop()
    {
        return $this->stop;
    }

}

?>
