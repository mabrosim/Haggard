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

$args = filter_input_array(INPUT_GET);
if (!isset($args['term'])) {
    exit;
}

$name = $args['term'];

$conn = ldap_connect($GLOBALS['ldap_domain_controllers'][0]);
if (!$conn || !ldap_bind($conn, $GLOBALS['ldap_admin'], $GLOBALS['ldap_password'])) {
    return;
}

$results = array();
ldap_set_option($conn, LDAP_OPT_SIZELIMIT, 10);
$res = @ldap_search($conn, 'o=Nokia', '(|(uid=' . $name . '*) (cn=' . $name . '*) (mail=' . $name . '*))', array('mail', 'cn'));

$info = @ldap_get_entries($conn, $res);
if ($info['count'] == 0) {
    return;
}

$count = (int) $info['count'];
if ($count > 10) {
    $count = 10;
}

for ($i = 0; $i < $count; $i++) {
    $results[] = array('label' => $info[$i]['cn'][0],
        'value' => $info[$i]['mail'][0]);
}

echo json_encode($results);
?>
