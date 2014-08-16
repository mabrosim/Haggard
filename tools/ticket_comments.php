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

require_once '/var/www/haggard/config/database.config.php';
require_once '/var/www/haggard/config/global.config.php';

require_once '/var/www/haggard/3rdparty/ezSQL/shared/ez_sql_core.php';
require_once '/var/www/haggard/3rdparty/ezSQL/mysql/ez_sql_mysql.php';

/* Connect to database */
$GLOBALS['db'] = new ezSQL_mysql($database['username'], $database['password'], $database['database'], $database['host']);

$comments = $GLOBALS['db']->get_results("SELECT * FROM ticket_comment WHERE data LIKE 'Ticket moved from%' OR data LIKE 'Ticket created' OR data LIKE 'Ticket updated.%' OR data LIKE '% created ticket' OR data LIKE 'Added sub-ticket:%' OR data LIKE 'Deleted child ticket%' OR data LIKE 'Created sub-ticket:%' OR data LIKE 'Ticket%created' OR data LIKE '% created new child ticket:%' OR data LIKE '% deleted child ticket %' OR data LIKE '% moved ticket %'");

foreach ($comments as $comment) {
    $GLOBALS['db']->query("INSERT INTO ticket_history (ticket_id, user_id, data, created) VALUES ('" . $comment->ticket_id . "', '" . $comment->user_id . "', '" . $GLOBALS['db']->escape($comment->data) . "', '" . $comment->created . "')");
    $GLOBALS['db']->query("DELETE FROM ticket_comment WHERE id = '" . $comment->id . "' LIMIT 1");
}
?>
