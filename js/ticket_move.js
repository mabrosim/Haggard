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
    var placeholder = "<li class='placeHolder'><div></div></li>";
    $(window).scroll(function () {
        if ($(window).scrollTop() >= 200) {
            if (placeholder == "<li class='placeHolder' style='position:fixed; top: 7em;'><div></div></li>")
                return;
            placeholder = "<li class='placeHolder' style='position:fixed; top: 7em;'><div></div></li>";
        }
        else {
            if (placeholder == "<li class='placeHolder'><div></div></li>")
                return;
            placeholder = "<li class='placeHolder'><div></div></li>";
        }

        $("#phase0, #phase1, #phase2, #phase3, #phase4, #phase5, #phase6, #phase7, #phase8").dragsort("destroy");
        $("#phase0, #phase1, #phase2, #phase3, #phase4, #phase5, #phase6, #phase7, #phase8").dragsort({
            scrollSpeed: 0,
            dragSelector: "div",
            dragBetween: true,
            dragEnd: saveOrder,
            placeHolderTemplate: placeholder
        });
    });

    $("#ticket_table").livequery(function (e) {
        $("#phase0, #phase1, #phase2, #phase3, #phase4, #phase5, #phase6, #phase7, #phase8").dragsort("destroy");
        $("#phase0, #phase1, #phase2, #phase3, #phase4, #phase5, #phase6, #phase7, #phase8").dragsort({
            scrollSpeed: 0,
            dragSelector: "div",
            dragBetween: true,
            dragEnd: saveOrder,
            placeHolderTemplate: placeholder
        });
    });

    updateWIP($('#current_cycle').text(), $("input[name='ticket_select_person']:checked").attr('id'));

    function sortNewPhase(ticket) {
        var p = ticket.parent();
        var order_by = "PRIORITY";
        if ($.cookie('sort')) {
            order_by = $.cookie('sort');
            $("input[name='ticket_select_sorting'][data-order=" + order_by + "]").prop("checked", true);
        }

        if (order_by !== "") {
            if (order_by === "DATA") {
                $('li', p).sort(sortListName).appendTo(p);
            }
            else if (order_by === "PRIORITY" || order_by === "TYPE") {
                $('li', p).sort(sortListPrio).appendTo(p);
            }
            else if (order_by === "COMPONENT") {
                $('li', p).sort(sortListComponent).appendTo(p);
            }
            else if (order_by === "RESPONSIBLE") {
                $('li', p).sort(sortListResponsible).appendTo(p);
            }
            else if (order_by === "WIP") {
                $('li', p).sort(sortListWIP).appendTo(p);
            }
            else if (order_by === "CHANGED") {
                $('li', p).sort(sortListChanged).appendTo(p);
            }
            else if (order_by === "CREATED") {
                $('li', p).sort(sortListCreated).appendTo(p);
            }
        }
    }

    function saveOrder() {
        var item = $(this).data("itemid");
        var phase = $(this).data("phase");
        var new_phase = $(this).parent().data('id');
        var force_comment = $(this).parent().data('forcec');
        var cycle = $('#current_cycle').text();
        var retval = true;

        if (new_phase === phase) {
            sortNewPhase($(this));
            return;
        }

        /* Have to do this synchronous so the retval can be updated accordingly */
        $.ajax({
            type: 'POST',
            async: false,
            url: './lib/dyn_content.php?jquery=jquery.ticket_move.php',
            data: {"func": "check_for_wip_limit", "phase": new_phase, "item": item},
            success: function (ret) {
                if (ret == '0') {
                    retval = false;
                }
            }

        });

        if (retval == false) {
            $("#content").load(current_page);
            alert("WIP or ticket limit of this phase is FULL!");
            return;
        }

        $.ajax({
            type: 'POST',
            async: false,
            url: './lib/dyn_content.php?jquery=jquery.ticket_move.php',
            data: {"func": "check_permission"},
            success: function (ret) {
                if (ret !== 'true') {
                    alert("You do not have permission to move tickets!");
                    $("#content").load(current_page);
                    return;
                }
            }

        });

        if (force_comment !== null && force_comment !== "") {
            var comment = prompt(force_comment, "");
            if (comment !== null) {
                if (comment !== "") {
                    $.post("./lib/dyn_content.php?jquery=jquery.ticket_move.php", {
                        "func": "comment",
                        "item": item,
                        "reason": comment
                    });
                }
            }
            else {
                $("#content").load(current_page);
                return;
            }
        }

        /* Do the actual saving of the new phase */
        var ts = Math.round((new Date()).getTime() / 1000);
        $(this).data('changed', ts);
        $(this).data('phase', new_phase);
        if (pageGenID !== null && pageGenID !== 0) {
            pageGenID++;
        }

        $.post("./lib/dyn_content.php?jquery=jquery.ticket_move.php", {
            "func": "updateData",
            "item": item,
            "phase": new_phase,
            "cycle": cycle
        }, function (dada) {
            updateWIP(cycle);
        });

        sortNewPhase($(this));
    }
});
