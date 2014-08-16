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

$args = filter_input_array(INPUT_POST);
if (isset($args['send_email'])) {
    $s_email = $GLOBALS['db']->escape($args['send_email']);
}

if (isset($args['show_resp'])) {
    $resp = $GLOBALS['db']->escape($args['show_resp']);
}

if (isset($args['show_comp'])) {
    $comp = $GLOBALS['db']->escape($args['show_comp']);
}

if (isset($args['show_wip'])) {
    $wip = $GLOBALS['db']->escape($args['show_wip']);
}

if (isset($args['show_ref'])) {
    $ref = $GLOBALS['db']->escape($args['show_ref']);
}

if (isset($args['show_info'])) {
    $info = $GLOBALS['db']->escape($args['show_info']);
}

if (isset($args['show_created'])) {
    $created = $GLOBALS['db']->escape($args['show_created']);
}

if (isset($args['show_changed'])) {
    $changed = $GLOBALS['db']->escape($args['show_changed']);
}

if (isset($args['hide_extra'])) {
    $hide = $GLOBALS['db']->escape($args['hide_extra']);
}


$send_email = 0;
$show_resp = 0;
$show_comp = 0;
$show_wip = 0;
$show_ref = 0;
$show_info = 0;
$show_created = 0;
$show_changed = 0;
$hide_extra_info = 0;
$timezone = $GLOBALS['db']->escape($args['timezone']);
$alias = $GLOBALS['db']->escape($args['alias']);

if (isset($s_email) && $s_email != "") {
    $send_email = 1;
}
if (isset($resp) && $resp != "") {
    $show_resp = 1;
}
if (isset($comp) && $comp != "") {
    $show_comp = 1;
}
if (isset($wip) && $wip != "") {
    $show_wip = 1;
}
if (isset($ref) && $ref != "") {
    $show_ref = 1;
}
if (isset($info) && $info != "") {
    $show_info = 1;
}
if (isset($created) && $created != "") {
    $show_created = 1;
}
if (isset($changed) && $changed != "") {
    $show_changed = 1;
}
if (isset($hide) && $hide != "") {
    $hide_extra_info = 1;
}

$user = new User($_SESSION['userid']);
$user->setTimezone($timezone);
$user->setAlias($alias);
$user->setSetting('send_email', $send_email);
$user->setSetting('show_ticket_responsible', $show_resp);
$user->setSetting('show_ticket_component', $show_comp);
$user->setSetting('show_ticket_wip', $show_wip);
$user->setSetting('show_ticket_reference', $show_ref);
$user->setSetting('show_ticket_info', $show_info);
$user->setSetting('show_ticket_created', $show_created);
$user->setSetting('show_ticket_changed', $show_changed);
$user->setSetting('hide_extra_info', $hide_extra_info);
?>
