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

$(window).scroll(function () {
    if ($("#phase_descriptors").length > 0) {
        if ($(window).scrollTop() >= ($("#phase_descriptors").offset().top)) {
            if (!$(".copy_desc").length) {
                $("#content").append($("#phase_descriptors").clone(true).addClass("copy_desc"));
                $(".copy_desc").find('h2').css('text-align', 'center');
                $(".copy_desc").find('h2').css('margin-bottom', '2px');
                $(".copy_desc").css('opacity', '0');
                $(".copy_desc").css('text-align', 'center');
                $(".copy_desc").css('position', 'fixed');
                $(".copy_desc").css('z-index', '599');
                $(".copy_desc").css('top', '0');
                $(".copy_desc").css('width', $('#ticket_table').width() + "px");
                $(".copy_desc .phase_functions").hide();
                $(".copy_desc h2").css('margin-bottom', '15px');
                $(".copy_desc").fadeTo('fast', 1);
            }
        }
        else {
            $(".copy_desc").fadeTo('fast', 0, function () {
                $(".copy_desc").remove();
            });
        }
    }

    function lastPostFunc() {
        var curDate = $('#date').val();
        $.post("./lib/dyn_content.php?page=page.log.php&func=getLastPosts&date=" + curDate + "&lastID=" + $(".wrdLatest:last").attr("id"),
            function (data) {
                if (data != "") {
                    $(".wrdLatest:last").after(data);
                }
            });
    }
    ;

    $(function () {
        $('#date').datepicker({
            dateFormat: 'dd.mm.yy',
            onSelect: function (selectedDate) {
                curDate = selectedDate;
                $('#log_table').slideUp(400, function () {
                    $('#log_table').load("log.php?func=dateSelected&date=" + selectedDate, function () {
                        $('#log_table').slideDown(400);
                    });
                });
            }
        });
    });

    if ($('#log_table').length > 0) {
        if ($(window).scrollTop() == $(document).height() - $(window).height()) {
            lastPostFunc();
        }
    }
});
