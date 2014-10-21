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

$(document).ready(function () {
    if ($('#personal_settings').length > 0) {
        $('#personal_settings').bind('submit', function (e) {
            e.preventDefault();
            var email = $('[name=send_email]:checked').val();
            var show_resp = $('[name=show_resp]:checked').val();
            var show_comp = $('[name=show_comp]:checked').val();
            var show_ref = $('[name=show_ref]:checked').val();
            var show_wip = $('[name=show_wip]:checked').val();
            var show_info = $('[name=show_info]:checked').val();
            var show_created = $('[name=show_created]:checked').val();
            var show_changed = $('[name=show_changed]:checked').val();
            var hide_extra = $('[name=hide_extra_info]:checked').val();
            var timezone = $('#timezone').find(":selected").text();
            var alias = $('[name=alias]').val();

            $.post("./lib/dyn_content.php?jquery=jquery.personal_settings.php", {
                "func": "personal_settings",
                "send_email": email,
                "show_resp": show_resp,
                "show_comp": show_comp,
                "show_wip": show_wip,
                "show_ref": show_ref,
                "show_info": show_info,
                "show_created": show_created,
                "show_changed": show_changed,
                "timezone": timezone,
                "hide_extra": hide_extra,
                "alias": alias
            }, function (data) {
                $('#saveSettings').show().delay(5000).fadeOut();
            });

            return false;
        });
    }
});
