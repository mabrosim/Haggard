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

require_once 'user.class.php';

class Group
{

    private $id = 0;
    private $name = "";
    private $desc = "";

    public function __construct($id = 0)
    {
        $this->id = $id;
        $group = $GLOBALS['db']->get_row("SELECT * FROM user_group WHERE id = '" . $this->id . "' LIMIT 1");
        $this->name = $group->name;
        $this->desc = $group->description;
    }

    public function getName()
    {
        return $this->name;
    }

    public function getDescription()
    {
        return $this->desc;
    }

    public function getPermission($key)
    {
        /* User permissions overwrite the group permissions */
        $key = $GLOBALS['db']->escape($key);

        if ($key == null || $key == '') {
            return false;
        }

        $permission_id = $GLOBALS['db']->get_var("SELECT id FROM permission WHERE data = '" . $key . "' LIMIT 1");
        if (!$permission_id) {
            $GLOBALS['db']->query("INSERT INTO permission (data) VALUES ('" . $key . "')");

            // No such permission given yet
            return false;
        }

        $group_access = $GLOBALS['db']->get_var("SELECT id FROM group_permission WHERE permission_id = '" . $permission_id . "' AND group_id = '" . $this->id . "' LIMIT 1");
        if ($group_access) {
            return true;
        }

        return false;
    }

    public function setPermission($key)
    {
        $key = $GLOBALS['db']->escape($key);
        $permission_id = $GLOBALS['db']->get_var("SELECT id FROM permission WHERE data = '" . $key . "' LIMIT 1");
        if (!$permission_id) {
            $GLOBALS['db']->query("INSERT INTO permission (data) VALUES ('" . $key . "')");
            $permission_id = $GLOBALS['db']->insert_id;
        }

        $GLOBALS['db']->query("INSERT INTO group_permission (permission_id, group_id) VALUES ('" . $permission_id . "', '" . $this->id . "')");
    }

    public function clearPermissions()
    {
        $GLOBALS['db']->query("DELETE FROM group_permission WHERE group_id = '" . $this->id . "'");
    }

}

?>
