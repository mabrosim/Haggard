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

$(function () {
    subContent = $('#sub_content');

    function loadContent(page) {
        subContent.fadeTo('fast', 0, function () {
            subContent.load(page, function () {
                subContent.fadeTo('fast', 1);
            });
        });
    }

    $('#settings_menu ul li a').livequery('click', function (e) {
        e.preventDefault();
        switch (this.id) {
            default:
            case "my_settings":
                loadContent("./lib/dyn_content.php?page=page.personal_settings.php");
                break;
            case "my_tickets":
                loadContent("./lib/dyn_content.php?page=page.my_tickets.php");
                break;
            case "board_settings":
                loadContent("./lib/dyn_content.php?page=page.board_settings.php");
                break;
            case "cycles":
                loadContent("./lib/dyn_content.php?page=page.cycle_settings.php");
                break;
            case "components":
                loadContent("./lib/dyn_content.php?page=page.component_settings.php");
                break;
            case "users":
                loadContent("./lib/dyn_content.php?page=page.user_settings.php");
                break;
            case "user_groups":
                loadContent("./lib/dyn_content.php?page=page.user_group_settings.php");
                break;
            case "phases":
                loadContent("./lib/dyn_content.php?page=page.phase_settings.php");
                break;
            case "releases":
                loadContent("./lib/dyn_content.php?page=page.releases.php");
                break;
        }
    });
});
