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

class Priority {

    private $priority = 0;

    public function __construct($id = 0) {
        $this->priority = $id;
    }

    public function getName() {
        $name = "";
        if ($GLOBALS['board']->getSettingValue("USE_PRIORITIES")) {
            switch ($this->priority) {
                case 0:
                    $name = "Low";
                    break;

                case 1:
                    $name = "Medium";
                    break;

                case 2:
                    $name = "Major";
                    break;

                case 3:
                    $name = "Showstopper";
                    break;
            }
        } else {
            switch ($this->priority) {
                case 0:
                    $name = $GLOBALS['board']->getSettingValue("TICKET_TYPE1");
                    break;
                case 1:
                    $name = $GLOBALS['board']->getSettingValue("TICKET_TYPE2");
                    break;
                case 2:
                    $name = $GLOBALS['board']->getSettingValue("TICKET_TYPE3");
                    break;
                case 3:
                    $name = $GLOBALS['board']->getSettingValue("TICKET_TYPE4");
                    break;
            }
        }

        return $name;
    }

    public function getCSSClass() {
        $class = "";
        switch ($this->priority) {
            default:
            case 0:
                $class = "low_priority";
                break;

            case 1:
                $class = "medium_priority";
                break;

            case 2:
                $class = "major_priority";
                break;

            case 3:
                $class = "showstopper_priority";
                break;
        }
        return $class;
    }

}

?>
