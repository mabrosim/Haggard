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

class Phase {

    private $id = 0;
    private $name = "";
    private $css = "";
    private $help = "";
    private $active = 1;
    private $force_comment = "";
    private $wip_limit = 0;
    private $notify_empty = 0;
    private $ticket_limit = 0;

    public function __construct($id) {
        $this->id = $id;
        $res = $GLOBALS['db']->get_row("SELECT name, css, help, active, force_comment, wip_limit, ticket_limit, notify_empty FROM phase WHERE id = '" . $this->id . "' LIMIT 1");

        if ($res) {
            $this->name = $res->name;
            $this->css = $res->css;
            $this->help = $res->help;
            $this->active = $res->active;
            $this->force_comment = $res->force_comment;
            $this->wip_limit = $res->wip_limit;
            $this->notify_empty = $res->notify_empty;
            $this->ticket_limit = $res->ticket_limit;
        }
    }

    public function getName() {
        return $this->name;
    }

    public function getId() {
        return $this->id;
    }

    public function getCSS() {
        return $this->css;
    }

    public function getHelp() {
        return $this->help;
    }

    public function isActive() {
        return $this->active;
    }

    public function getForceComment() {
        return $this->force_comment;
    }

    public function getWIPLimit() {
        return $this->wip_limit;
    }

    public function getTicketLimit() {
        return $this->ticket_limit;
    }

    public function getNotifyEmpty() {
        return $this->notify_empty;
    }

    public function getCurrentWIP() {
        $wip = 0;
        $tickets = $GLOBALS['db']->get_var("SELECT SUM(wip) FROM ticket WHERE phase = '" . $this->id . "' AND active = '1' AND deleted = '0'");
        if ($tickets) {
            $wip = $tickets;
        }
        return $wip;
    }

    public function getNumTickets() {
        $tickets = $GLOBALS['db']->get_var("SELECT COUNT(id) FROM ticket WHERE phase = '" . $this->id . "' AND active = '1' AND deleted = '0'");
        return $tickets;
    }

}

?>
