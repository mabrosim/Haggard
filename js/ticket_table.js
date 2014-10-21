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

function updateWIP(cycle, p_id, c_id) {
    if ($("current_cycle_wip").length > 0) {
        $.post("./lib/dyn_content.php?jquery=jquery.ticket_move.php", {
            "func": "updateWIP",
            "cycle": cycle,
            "p_id": p_id,
            "c_id": c_id
        }, function (return_val) {
            if (return_val) {
                var ret = JSON.parse(return_val);
                var current_wip = ret["current"];
                var cycle_wip = ret["cycle"];
                var wip_left = ret["left"];

                $("#current_cycle_wip").text(current_wip);
                $("#current_cycle_wip_limit").text(cycle_wip);
                $("#current_cycle_wip_left").text(wip_left);

                if (current_wip <= (cycle_wip / 2)) {
                    $("#current_cycle_wip").css('color', '#1f9100');
                }
                else if (current_wip > (cycle_wip / 2) && current_wip <= ((cycle_wip / 2) + (cycle_wip / 4))) {
                    $("#current_cycle_wip").css('color', '#e18a00');
                }
                else {
                    $("#current_cycle_wip").css('color', '#c30000');
                }
            }
        });
    }
}

var tv_mode = false;
$(document).ready(function () {
    var link_filter = null;
    var tab_selected = false;

    function openCommentWindow(ticket_id) {
        $("#dialog").dialog({
            autoOpen: false,
            modal: true,
            width: 900,
            height: 650,
            position: 'center',
            title: "Ticket comments",
            open: function (event, ui) {
                $('#comment_tabs').tabs({});

                comment_window_width = $("#dialog").width();
                comment_window_height = $("#dialog").height();

                $('#new_comment').bind('submit', function (e) {
                    e.preventDefault();
                    var comment = $('[name=comment]').val();
                    $.post("./lib/dyn_content.php?jquery=jquery.ticket_comment.php", {
                        "func": "new_comment",
                        "id": ticket_id,
                        "comment": comment
                    }, function () {
                        $('[name=comment]').val("");
                        $("#comment_frame").attr("src", $("#comment_frame").attr("src"));
                    });

                    return false;
                });
            },
            resize: function (event, ui) {
                $("#comment_frame_holder").height($(this).height() - 250);
                $("#comment_line").width($(this).width() - 150);
            }
        });

        $("#dialog").load("./lib/dyn_content.php?page=dialog.ticket_comment.php&ticket_id=" + ticket_id, function () {
            $("#dialog").dialog("open");
        });
        return false;
    }

    function tvMode(state) {
        tv_mode = state;

        if (state === true) {
            $('#content').css('padding-top', '0');
            $('#menu_container').hide();
            $('#ticket_table_header').livequery(function () {
                $('#ticket_table_header').hide();
            });
        }
        else {
            $('#content').css('padding-top', '4em');
            $('#menu_container, #ticket_table_header').show();
        }
    }

    var tvModeTimer;
    if ((window.fullScreen) ||
        (window.innerWidth == screen.width && window.innerHeight == screen.height)) {
        tvMode(true);
    }

    $(window).resize(function () {

        if ((window.fullScreen) ||
            (window.innerWidth == screen.width && window.innerHeight == screen.height)) {
            tvModeTimer = setTimeout(function () {
                tvMode(true);
            }, 100);
        }
        else {
            tvModeTimer = setTimeout(function () {
                tvMode(false);
            }, 100);
        }
    });

    $.fn.qtip.inactiveEvents = ["click", "dblclick"];

    function qtipCreate(elem, content) {
        $(elem).livequery('mouseover', function (event) {
            $(this).qtip({
                overwrite: false,
                content: {
                    text: content
                },
                style: {
                    classes: 'qtip-light qtip-shadow'
                },
                show: {
                    solo: true,
                    ready: true
                },
                events: {
                    hide: function (event, api) {
                        $(this).qtip('destroy');
                    }
                }
            });
        });
    }

    qtipCreate('.help_text', 'Help');
    qtipCreate('.hide_phase', 'Toggle phase visibility');
    qtipCreate('.show_all_info', 'Toggle hidden info');
    qtipCreate('.edit_ticket', 'Edit ticket');
    qtipCreate('.comments_ticket', 'Ticket comments');
    qtipCreate('.links_ticket', 'Toggle parent/child ticket filter');
    qtipCreate('.new_child_ticket', 'Create new child ticket');
    qtipCreate('.external_reference', 'Go to external reference');
    qtipCreate('.ticket_subscribe, .ticket_subscribe_email', 'Subscribe to ticket events');
    qtipCreate('.ticket_unsubscribe', 'Unsubscribe from ticket events');
    qtipCreate('.phase_subscribe', 'Subscribe to phase events');
    qtipCreate('.phase_unsubscribe', 'Unsubscribe from phase events');

    if ($.cookie('tab_selected')) {
        tab_selected = $.cookie('tab_selected');
    }

    $('#filter_tabs').livequery(function (e) {
        $(this).tabs({
            collapsible: true,
            active: tab_selected,
            cache: true,
            cookie: {expires: 60},
            activate: function (event, ui) {
                var target = $(this).tabs('option', 'active');

                if (target === 3) {
                    event.preventDefault();
                    event.stopPropagation();
                    $(this).tabs('option', 'active', false);
                    $(this).tabs('refresh');
                    return true;
                }

                if (tab_selected === target) {
                    tab_selected = false;
                    $.cookie('tab_selected', false);
                }
                else {
                    tab_selected = target;
                    $.cookie('tab_selected', target);
                }
            }
        }).show();
    });

    var p_id = getURLParameter('p_id');
    var c_id = getURLParameter('c_id');
    var order_by = "PRIORITY";
    $('#sort_by_select').livequery(function () {
        if ($.cookie('sort')) {
            order_by = $.cookie('sort');
        } else {
            $.cookie('sort', order_by);
        }
        $("input[name='ticket_select_sorting'][data-order=" + order_by + "]").prop("checked", true);
        if (order_by === "PRIORITY" && $("label[for='SORT_BY_TYPE']").length > 0) {
            $("label[for='SORT_BY_TYPE']").addClass("ui-state-active");
        }

        $("label[for='SORT_BY_" + order_by + "']").addClass("ui-state-active");
        updateTable();
    });

    if (!p_id || p_id === '' || p_id === null || p_id === "null") {
        p_id = 'all';
        updateFilterCookies();
    }

    if (!c_id || c_id === '' || c_id === null || c_id === "null") {
        c_id = 'all';
        updateFilterCookies();
    }

    var ticket_id = getURLParameter('ticket_id');
    if (ticket_id === "null" || ticket_id === null) {
        updateTable();
    }

    function showParentChild(ticket) {
        var ticket_id = ticket.data("itemid");
        $('.ticket_holder').each(function (index) {
            if ($(this).data("parent") === ticket_id || $(this).data("child") === ticket_id ||
                $(this).data("itemid") === ticket_id || ticket.data("parent") === $(this).data("itemid")) {
                var resp = $(this).data("resp").toString();
                var comp = $(this).data("comp").toString();
                var p_arr = p_id.split(',');
                var c_arr = c_id.split(',');

                if ((p_id === 'all' && c_id === 'all') || (p_id === "p_all" && c_id === "c_all")) {
                    $(this).css('display', 'block');
                }
                else if (c_id !== 'all' && p_id !== 'all') {
                    if (p_arr.indexOf(resp) >= 0 && c_arr.indexOf(comp) >= 0)
                        $(this).css('display', 'block');
                    else
                        $(this).css('display', 'none');
                }
                else if (c_id === 'all' && p_id !== 'all') {
                    if (p_arr.indexOf(resp) >= 0)
                        $(this).css('display', 'block');
                    else
                        $(this).css('display', 'none');
                }
                else if (c_id !== 'all' && p_id === 'all') {
                    if (c_arr.indexOf(comp) >= 0)
                        $(this).css('display', 'block');
                    else
                        $(this).css('display', 'none');
                }
            }
            else {
                $(this).css('display', 'none');
            }
        });
    }

    function updateFilterCookies() {
        if ($.cookie('c_id')) {
            c_id = $.cookie('c_id');
            if (c_id === null || c_id === '' || c_id === 'null') {
                $.cookie('c_id', 'all');
                c_id = 'all';
            }
            var c_arr = c_id.split(',');
            for (var i = 0; i < c_arr.length; i++) {
                $("input[name='ticket_select_component'][data-id=" + c_arr[i] + "]").attr("checked", true);
            }
        }
        else {
            if (c_id === null || c_id === '' || c_id === 'null')
                c_id = 'all';
            $.cookie('c_id', c_id);
        }

        if ($.cookie('p_id')) {
            p_id = $.cookie('p_id');
            if (p_id === null || p_id === '' || p_id === 'null') {
                $.cookie('p_id', 'all');
                p_id = 'all';
            }

            var p_arr = p_id.split(',');
            for (var i = 0; i < p_arr.length; i++) {
                $("input[name='ticket_select_person'][data-id=" + p_arr[i] + "]").attr("checked", true);
            }
        }
        else {
            if (p_id === null || p_id === '' || p_id === 'null')
                p_id = 'all';
            $.cookie('p_id', p_id);
        }

        if ($.cookie('sort')) {
            order_by = $.cookie('sort');
            $("input[name='ticket_select_sorting'][data-order=" + order_by + "]").attr("checked", "checked");
        }
    }

    $('.ticket_holder').livequery('dblclick', function (e) {
        var ticket_id = $(this).data("itemid");
        openCommentWindow(ticket_id);
        return false;
    });

    function clearFilters() {
        $('.ticket_holder').each(function (index) {
            $(this).css('display', 'block');
        });
    }

    function updateTable() {
        updateFilterCookies();
        if (order_by !== "") {
            for (i = 0; i <= 8; i++) {
                if (order_by === "DATA") {
                    $('li', '#phase' + i).sort(sortListName).appendTo('#phase' + i);
                }
                else if (order_by === "PRIORITY" || order_by === "TYPE") {
                    $('li', '#phase' + i).sort(sortListPrio).appendTo('#phase' + i);
                }
                else if (order_by === "COMPONENT") {
                    $('li', '#phase' + i).sort(sortListComponent).appendTo('#phase' + i);
                }
                else if (order_by === "RESPONSIBLE") {
                    $('li', '#phase' + i).sort(sortListResponsible).appendTo('#phase' + i);
                }
                else if (order_by === "WIP") {
                    $('li', '#phase' + i).sort(sortListWIP).appendTo('#phase' + i);
                }
                else if (order_by === "CHANGED") {
                    $('li', '#phase' + i).sort(sortListChanged).appendTo('#phase' + i);
                }
                else if (order_by === "CREATED") {
                    $('li', '#phase' + i).sort(sortListCreated).appendTo('#phase' + i);
                }
            }
        }

        if (link_filter !== null) {
            showParentChild(link_filter);
            return;
        }

        $('.ticket_holder').each(function (index) {
            if ((p_id === 'all' && c_id === 'all') || (p_id === "p_all" && c_id === "c_all")) {
                $(this).css('display', 'block');
            }
            else {
                var resp = $(this).data("resp").toString();
                var comp = $(this).data("comp").toString();
                var p_arr = p_id.split(',');
                var c_arr = c_id.split(',');

                if (c_id !== 'all' && p_id !== 'all') {
                    if (p_arr.indexOf(resp) >= 0 && c_arr.indexOf(comp) >= 0)
                        $(this).css('display', 'block');
                    else
                        $(this).css('display', 'none');
                }
                else if (c_id === 'all' && p_id !== 'all') {
                    if (p_arr.indexOf(resp) >= 0)
                        $(this).css('display', 'block');
                    else
                        $(this).css('display', 'none');
                }
                else if (p_id === 'all' && c_id !== 'all') {
                    if (c_arr.indexOf(comp) >= 0)
                        $(this).css('display', 'block');
                    else
                        $(this).css('display', 'none');
                }
            }
        });

        search($('#search_input').val());

        if (typeof currentPage !== 'undefined' && current_page.indexOf("?id") === -1) {
            current_page = "./lib/dyn_content.php?page=page.board.php&p_id=" + p_id + "&c_id=" + c_id;
        }
        else if (typeof currentPage !== 'undefined') {
            var cycle_id = current_page.substring(current_page.indexOf("?id=") + 4);

            if (cycle_id.indexOf("&") !== -1) {
                cycle_id = cycle_id.substring(0, cycle_id.indexOf("&"));
            }

            current_page = "./lib/dyn_content.php?page=page.board.php&id=" + cycle_id + "&p_id=" + p_id + "&c_id=" + c_id;
            $('#current_cycle').html(cycle_id);
        }

        updateWIP($('#current_cycle').text(), p_id, c_id);
    }

    $(".links_ticket").livequery('click', function (e) {
        e.preventDefault();
        if (link_filter === null) {
            $(this).css('background-image', 'url(./img/icons/56.png)');
            showParentChild($(this).closest('.ticket_holder'));
            link_filter = $(this).closest('.ticket_holder');
        }
        else {
            if ($(this).data("id") === link_filter.data("itemid")) {
                $(this).css('background-image', 'url(./img/icons/55.png)');
                link_filter = null;
                updateTable();
            }
            else {
                $(link_filter).find('.links_ticket').css('background-image', 'url(./img/icons/55.png)');
                $(this).css('background-image', 'url(./img/icons/56.png');
                showParentChild($(this).closest('.ticket_holder'));
                link_filter = $(this).closest('.ticket_holder');
            }
        }
        return false;
    });

    $(".comments_ticket, .ticket_comment_log").livequery('click', function (e) {
        e.preventDefault();
        var ticket_id = $(this).data("id");
        openCommentWindow(ticket_id);
        return false;
    });

    $("#sort_by_person").livequery(function (e) {
        var ctrl = false;
        $(this).click(function (e) {
            if (e.ctrlKey) {
                ctrl = true;
            } else {
                ctrl = false;
            }
        });

        $(this).buttonset().click(function (e) {
            if (e.originalEvent.target.name === 'ticket_select_person_all') {
                $("input[name='ticket_select_person']").removeAttr('checked').button('refresh');
                $("input[name='ticket_select_person_all']").attr('checked', 'checked').button('refresh');
                p_id = 'all';
            }
            else {
                $("input[name='ticket_select_person_all']").removeAttr('checked').button('refresh');
                p_id = '';
            }

            if (p_id !== 'all') {
                if (ctrl === true) {
                    $("input[name='ticket_select_person_all']").removeAttr('checked').button('refresh');
                    p_id = '';
                    $("input[name='ticket_select_person']").each(function () {
                        p_id += (this.checked ? $(this).data('id') + "," : "");
                    });
                } else {
                    $("input[name='ticket_select_person_all']").removeAttr('checked').button('refresh');
                    $("input[name='ticket_select_person']").removeAttr('checked').button('refresh');
                    var $c = $(e.target);
                    if ($c.data('id') === undefined) {
                        p_id = '';
                    } else {
                        p_id = $c.data('id') + ",";
                        $c.attr('checked', 'checked');
                        $("input[name='ticket_select_person']").button('refresh');
                    }
                }

                if (p_id === '' || p_id === undefined) {
                    p_id = 'all';
                    $("input[name='ticket_select_person_all']").attr('checked', 'checked').button('refresh');
                }
                else {
                    p_id = p_id.substring(0, p_id.length - 1);
                }
            }

            $.cookie('p_id', p_id);
            clearFilters();
            updateTable();
        });
    });

    $("#sort_by_component").livequery(function (e) {
        var ctrl = false;
        $(this).click(function (e) {
            if (e.ctrlKey) {
                ctrl = true;
            } else {
                ctrl = false;
            }
        });

        $(this).buttonset().click(function (e) {
            if (e.originalEvent.target.name === 'ticket_select_component_all') {
                $("input[name='ticket_select_component']").removeAttr('checked').button('refresh');
                $("input[name='ticket_select_component_all']").attr('checked', 'checked').button('refresh');
                c_id = 'all';
            }
            else {
                $("input[name='ticket_select_component_all']").removeAttr('checked').button('refresh');
                c_id = '';
            }

            if (c_id !== 'all') {
                if (ctrl === true) {
                    $("input[name='ticket_select_component_all']").removeAttr('checked').button('refresh');
                    c_id = '';
                    $("input[name='ticket_select_component']").each(function () {
                        c_id += (this.checked ? $(this).data('id') + "," : "");
                    });
                } else {
                    $("input[name='ticket_select_component_all']").removeAttr('checked').button('refresh');
                    $("input[name='ticket_select_component']").removeAttr('checked').button('refresh');
                    var $c = $(e.target);
                    if ($c.data('id') === undefined) {
                        p_id = '';
                    } else {
                        c_id = $c.data('id') + ",";
                        $c.attr('checked', 'checked');
                        $("input[name='ticket_select_component']").button('refresh');
                    }
                }

                if (c_id === '' || c_id === undefined) {
                    c_id = 'all';
                    $("input[name='ticket_select_component_all']").attr('checked', 'checked').button('refresh');
                }
                else {
                    c_id = c_id.substring(0, c_id.length - 1);
                }
            }

            $.cookie('c_id', c_id);
            clearFilters();
            updateTable();
        });
    });

    $("#sort_by_select").livequery(function (e) {
        $(this).buttonset().change(function () {
            order_by = $("input[name='ticket_select_sorting']:checked").data('order');
            $.cookie('sort', order_by);
            clearFilters();
            updateTable();
        });
    });


    $("#clear_all_filters").livequery(function (e) {
        $(this).click(function (e) {
            e.preventDefault();
            $("input[name='ticket_select_component_all']").attr('checked', 'checked').button('refresh');
            $("input[name='ticket_select_person_all']").attr('checked', 'checked').button('refresh');
            $("input[name='ticket_select_component']").removeAttr('checked').button('refresh');
            $("input[name='ticket_select_person']").removeAttr('checked').button('refresh');
            $.cookie('c_id', 'all');
            $.cookie('p_id', 'all');
            $('#search_input').val('');
            updateTable();
        });
    });

    $(".show_all_info").livequery('click', function () {
        if ($(this).data('shown') === 0) {
            $(this).data('shown', 1);
            $(this).parent().find('.info_hidden').slideDown();
            $(this).css('background-image', 'url(\'./img/icons/12.png\')');
            $(this).parent().find('.info_hidden').attr('class', 'info_shown');
        }
        else {
            $(this).data('shown', 0);
            $(this).parent().find('.info_shown').slideUp();
            $(this).css('background-image', 'url(\'./img/icons/11.png\')');
            $(this).parent().find('.info_shown').attr('class', 'info_hidden');
        }
    });

    $(".hide_phase").livequery('click', function (e) {
        e.preventDefault();
        var phase_id = $(this).data("id");
        var mode_link = $(this);

        if (mode_link.css('background-image').indexOf("12.png") !== -1) {
            var hiddenPhases = [];
            if ($.cookie('hidden_phases')) {
                hiddenPhases = $.cookie('hidden_phases').split(",");
            }

            if ($.inArray(phase_id, hiddenPhases) === -1) {
                hiddenPhases.push(phase_id);
                $.cookie('hidden_phases', hiddenPhases.join(","));
            }

            mode_link.css('background-image', 'url(./img/icons/11.png)');
            $('.phase' + phase_id).slideUp('fast', function () {
                $('#phase_name_' + phase_id).find('.name').text('');

                $('#phase_ticket_holder' + phase_id).animate({
                    width: "30px"
                }, 300);

                $('#phase_name_' + phase_id).animate({
                    width: "30px"
                }, 300);
            });
        }
        else {
            var hiddenPhases = [];
            if ($.cookie('hidden_phases')) {
                hiddenPhases = $.cookie('hidden_phases').split(",");
                var index = hiddenPhases.indexOf(phase_id.toString());
                if (index > -1) {
                    hiddenPhases.splice(index, 1);
                    $.cookie('hidden_phases', hiddenPhases.join(","));
                }
            }

            mode_link.css('background-image', 'url(./img/icons/12.png)');

            var width = $('#phase_ticket_holder' + phase_id).data('orig-width');

            $('#phase_name_' + phase_id).animate({
                width: width + '%'
            }, 300, function () {
                $('#phase_name_' + phase_id).find('.name').text($('#phase_name_' + phase_id).data('name'));
            });

            $('#phase_ticket_holder' + phase_id).animate({
                width: width + '%'
            }, 300, function () {
                $('.phase' + phase_id).slideDown();
            });
        }
    });

    $('.ticket_subscribe_email').livequery('click', function () {
        var id = $(this).data('id');
        var subscribe = $(this);
        $("#dialog").dialog(
            {
                autoOpen: false,
                modal: true,
                width: 350,
                height: 'auto',
                title: "Subscribe to ticket events",
                open: function (event, ui) {
                    $('#ticket_email_subscribe').bind('submit', function (e) {
                        e.preventDefault();
                        var email = $('[name=email]').val();
                        $.post("./lib/dyn_content.php?jquery=jquery.ticket_email_subscribe.php", {
                            "id": id,
                            "email": email
                        }, function () {
                            $('#dialog').dialog("close");
                            subscribe.css('display', 'none');
                        });

                        return false;
                    });
                }
            });

        $("#dialog").load("./lib/dyn_content.php?page=dialog.ticket_email_subscribe.php", function () {
            $("#dialog").dialog("open");
        });
    });

    $('.ticket_subscribe').livequery('click', function (e) {
        e.preventDefault();
        var id = $(this).data('id');
        $(this).removeClass('ticket_subscribe').addClass('ticket_unsubscribe');
        $.post("./lib/dyn_content.php?jquery=jquery.ticket_subscribe.php", {"id": id});
    });

    $('.ticket_unsubscribe').livequery('click', function (e) {
        e.preventDefault();
        var id = $(this).data('id');
        $(this).removeClass('ticket_unsubscribe').addClass('ticket_subscribe');
        $.post("./lib/dyn_content.php?jquery=jquery.ticket_unsubscribe.php", {"id": id});
    });

    $('.phase_subscribe').livequery('click', function (e) {
        e.preventDefault();
        var id = $(this).data('id');
        $(this).removeClass('phase_subscribe').addClass('phase_unsubscribe');
        $.post("./lib/dyn_content.php?jquery=jquery.phase_subscribe.php", {"id": id});
    });

    $('.phase_unsubscribe').livequery('click', function (e) {
        e.preventDefault();
        var id = $(this).data('id');
        $(this).removeClass('phase_unsubscribe').addClass('phase_subscribe');
        $.post("./lib/dyn_content.php?jquery=jquery.phase_unsubscribe.php", {"id": id});
    });

    $('.cycle_select').livequery('change', function (e) {
        var val = $(this).val();
        $('#content').fadeTo('fast', 0, function () {
            $('#content').load("./lib/dyn_content.php?page=page.board.php&id=" + val, function () {
                $('#content').fadeTo('fast', 1);
            });
        });
    });
});
