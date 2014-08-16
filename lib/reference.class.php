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

class Reference {

    private $ref = "";

    public function __construct($ref) {
        $this->ref = $ref;
    }

    public function getRef() {
        return $this->ref;
    }

    public function getType() {
        $type = "URL";
        $identifier = substr($this->ref, 0, 2);
        switch ($identifier) {
            case "M:":
                $type = "Mzilla";
                break;

            case "C:":
                $type = "Coverity";
                break;
        }

        if ($type == "URL" && strstr($this->ref, 'gerrit')) {
            $type = "GERRIT";
        }

        return $type;
    }

    public function getId() {
        $id = "";
        switch ($this->getType()) {
            case 'Mzilla':
            case 'Coverity':
                $id = substr($this->ref, 2);
                break;

            default:
                $id = $this->ref;
                break;
        }

        return $id;
    }

    public function getURL() {
        $url = "";
        switch ($this->getType()) {
            case "Mzilla":
                $url = 'https://mzilla/show_bug.cgi?id=' . $this->getId();
                break;

            case 'Coverity':
                $url = "http://coverity/index.html";
                break;

            default:
                $url = $this->ref;
                break;
        }

        return $url;
    }

}

?>
