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
    $(".new_ticket, .new_child_ticket").livequery('click', function (e) {
        e.preventDefault();
        if (e.handled !== true) {
            var parent_id = 0;
            var component_id = 0;
            var priority = 0;

            if ($(this).data("parent")) {
                parent_id = $(this).data("parent");
            }

            if ($(this).data("component")) {
                component_id = $(this).data("component");
            }

            if ($(this).data("priority")) {
                priority = $(this).data("priority");
            }

            $("#dialog").dialog({
                autoOpen: false,
                modal: true,
                width: 370,
                height: 'auto',
                title: "New ticket",
                open: function (event, ui) {
                    if (parent_id !== 0) {
                        $('[name=parent]').val(parent_id);
                    }

                    if (component_id !== 0) {
                        $('[name=comp]').val(component_id);
                    }

                    if (priority !== 0) {
                        $('[name=prio]').val(priority);
                    }

                    $('[name=reference_id]').qtip({
                        content: {
                            text: 'Mzilla - M:id<br/>Coverity - C:id<br/>or URL'
                        },
                        style: {
                            classes: 'ui-tooltip-tipsy'
                        },
                        show: {
                            event: 'focus'
                        },
                        hide: {
                            event: 'blur'
                        }
                    });

                    $('[name=wip]').keydown(function (event) {
                        if (event.keyCode == 46 || event.keyCode == 8 || event.keyCode == 9 || event.keyCode == 27 ||
                            (event.keyCode == 65 && event.ctrlKey === true) ||
                            (event.keyCode >= 35 && event.keyCode <= 39)) {
                            return;
                        }
                        else {
                            if ((event.keyCode < 48 || event.keyCode > 57) && (event.keyCode < 96 || event.keyCode > 105)) {
                                event.preventDefault();
                            }
                        }
                    });

                    $('#ticket_form').submit(function (e) {
                        $('input[type="submit"]').attr('disabled', 'disabled');
                        e.preventDefault();

                        var title = $('[name=title]').val();
                        var info = $('[name=info]').val();
                        var wip = $('[name=wip]').val();
                        var resp = $('[name=resp]').val();
                        var prio = $('[name=prio]').val();
                        var ref = $('[name=reference_id]').val();
                        var comp = $('[name=comp]').val();
                        var paren = $('[name=parent]').val();
                        var phase = $('[name=phase]').val();

                        if (title === "") {
                            $('input[type="submit"]').removeAttr('disabled');
                            alert("Please fill title!");
                            return false;
                        }

                        $.post("./lib/dyn_content.php?jquery=jquery.new_ticket.php", {
                            "func": "new_ticket",
                            "title": title,
                            "info": info,
                            "wip": wip,
                            "resp": resp,
                            "prio": prio,
                            "reference_id": ref,
                            "comp": comp,
                            "parent": paren,
                            "phase": phase
                        }, function (data) {
                            $("#content").load('./lib/dyn_content.php?page=page.board.php', function () {
                                $('input[type="submit"]').removeAttr('disabled');
                                $("#dialog").dialog("close");
                                if (pageGenID !== null && pageGenID !== 0) {
                                    pageGenID++;
                                }
                            });
                        });

                        return false;
                    });
                }
            });

            $("#dialog").load("./lib/dyn_content.php?page=dialog.new_ticket.php", function () {
                $("#dialog").dialog("open");
            });

            e.handled = true;
        }
        return false;
    });

    $(".edit_ticket").livequery('click', function (e) {
        e.preventDefault();
        if (e.handled !== true) {
            var ticket_id = $(this).data("id");

            $("#dialog").dialog(
                {
                    autoOpen: false,
                    modal: true,
                    width: 370,
                    height: 'auto',
                    title: "Edit ticket",
                    open: function (event, ui) {
                        $('[name=wip]').keydown(function (event) {
                            if (event.keyCode == 46 || event.keyCode == 8 || event.keyCode == 9 || event.keyCode == 27 ||
                                (event.keyCode == 65 && event.ctrlKey === true) ||
                                (event.keyCode >= 35 && event.keyCode <= 39)) {
                                return;
                            }
                            else {
                                if ((event.keyCode < 48 || event.keyCode > 57) && (event.keyCode < 96 || event.keyCode > 105)) {
                                    event.preventDefault();
                                }
                            }
                        });

                        $('[name=reference_id]').qtip({
                            content: {
                                text: 'Mzilla - M:id<br/>Coverity - C:id<br/>or URL'
                            },
                            style: {
                                classes: 'ui-tooltip-light ui-tooltip-shadow ui-tooltip-bootstrap'
                            },
                            show: {
                                event: 'focus'
                            },
                            hide: {
                                event: 'blur'
                            }
                        });

                        $('#ticket_form').bind('submit', function (e) {
                            $('input[type="submit"]').attr('disabled', 'disabled');
                            e.preventDefault();
                            var title = $('[name=title]').val();
                            var info = $('[name=info]').val();
                            var wip = $('[name=wip]').val();
                            var resp = $('[name=resp]').val();
                            var prio = $('[name=prio]').val();
                            var ref = $('[name=reference_id]').val();
                            var comp = $('[name=comp]').val();
                            var paren = $('[name=parent]').val();
                            var phase = $('[name=phase]').val();
                            var cycle = $('[name=cycle]').val();

                            if (title === "") {
                                $('input[type="submit"]').removeAttr('disabled');
                                alert("Please fill title!");
                                return false;
                            }

                            $.post("./lib/dyn_content.php?jquery=jquery.edit_ticket.php", {
                                "func": "edit_ticket",
                                "id": ticket_id,
                                "title": title,
                                "info": info,
                                "wip": wip,
                                "resp": resp,
                                "prio": prio,
                                "reference_id": ref,
                                "comp": comp,
                                "parent": paren,
                                "phase": phase,
                                "cycle": cycle
                            }, function (daada) {
                                $("#content").load('./lib/dyn_content.php?page=page.board.php', function () {
                                    $('input[type="submit"]').removeAttr('disabled');
                                    $("#dialog").dialog("close");
                                });
                            });

                            return false;
                        });

                        $('.show_more_options').click(function (e) {
                            e.preventDefault();
                            $('.more_options').slideToggle();
                        });

                        $('#move_ticket_board_form').bind('submit', function (e) {
                            e.preventDefault();

                            $("#dialog").load("./lib/dyn_content.php?page=dialog.move_ticket.php&ticket_id=" + ticket_id, function () {
                                $("#dialog").dialog("option", "title", "Move ticket");

                                $('[name=board]').change(function (e) {
                                    var board_id = $(this).val();
                                    $.getJSON('./lib/dyn_content.php?jquery=jquery.get_board_users.php&board_id=' + board_id, function (json) {
                                        $("[name=responsible]").empty();
                                        $.each(json, function (index, v) {
                                            var o = new Option(v.user.name, v.user.id);
                                            $(o).html(v.user.name);
                                            $("[name=responsible]").append(o);
                                        });
                                    });
                                    return true;
                                });

                                $("#move_ticket_form").bind('submit', function (e) {
                                    e.preventDefault();
                                    var board_id = $('[name=board]').val();
                                    var user_id = $('[name=responsible]').val();

                                    if (!board_id || !user_id || board_id == 0 || user_id == '') {
                                        alert("Please select board and new responsible!");
                                        return false;
                                    }

                                    $.post("./lib/dyn_content.php?jquery=jquery.move_ticket.php", {
                                        "board_id": board_id,
                                        "user_id": user_id,
                                        "ticket_id": ticket_id
                                    }, function (data) {
                                        $("#content").load('./lib/dyn_content.php?page=page.board.php', function () {
                                            $("#dialog").dialog("close");
                                        });

                                    });
                                });

                            });
                        });

                        $('#copy_ticket_board_form').bind('submit', function (e) {
                            e.preventDefault();

                            $("#dialog").load("./lib/dyn_content.php?page=dialog.copy_ticket.php&ticket_id=" + ticket_id, function () {
                                $("#dialog").dialog("option", "title", "Copy ticket");

                                $('[name=board]').change(function (e) {
                                    var board_id = $(this).val();
                                    $.getJSON('./lib/dyn_content.php?jquery=jquery.get_board_users.php&board_id=' + board_id, function (json) {
                                        $("[name=responsible]").empty();
                                        $.each(json, function (index, v) {
                                            var o = new Option(v.user.name, v.user.id);
                                            $(o).html(v.user.name);
                                            $("[name=responsible]").append(o);
                                        });
                                    });
                                    return true;
                                });

                                $("#copy_ticket_form").bind('submit', function (e) {
                                    e.preventDefault();
                                    var board_id = $('[name=board]').val();
                                    var user_id = $('[name=responsible]').val();

                                    if (!board_id || !user_id || board_id == 0 || user_id == '') {
                                        alert("Please select board and new responsible!");
                                        return false;
                                    }

                                    $.post("./lib/dyn_content.php?jquery=jquery.copy_ticket.php", {
                                        "board_id": board_id,
                                        "user_id": user_id,
                                        "ticket_id": ticket_id
                                    }, function (data) {
                                        $("#content").load('./lib/dyn_content.php?page=page.board.php', function () {
                                            $("#dialog").dialog("close");
                                        });

                                    });
                                });
                            });
                        });

                        $('#archive_ticket_form').bind('submit', function (e) {
                            e.preventDefault();
                            var archive_childs = "0";
                            $.post("./lib/dyn_content.php?jquery=jquery.delete_ticket.php", {
                                "func": "child_tickets",
                                "id": ticket_id
                            }, function (data) {
                                if (data !== "0" && data !== 0) {
                                    var arch = confirm("This ticket has " + data + " child tickets. Do you want to archive them too?");
                                    if (arch)
                                        archive_childs = "1";
                                }

                                $.post("./lib/dyn_content.php?jquery=jquery.archive_ticket.php", {
                                    "func": "archive_ticket",
                                    "id": ticket_id,
                                    "archive_children": archive_childs
                                }, function () {
                                    $("#content").load('./lib/dyn_content.php?page=page.board.php', function () {
                                        $("#dialog").dialog("close");
                                    });
                                });
                            });
                        });

                        $('#delete_ticket_form').bind('submit', function (e) {
                            e.preventDefault();
                            var delete_childs = "0";
                            var answer = confirm("Are you sure you want to delete this ticket?");
                            if (answer) {
                                $.post("./lib/dyn_content.php?jquery=jquery.delete_ticket.php", {
                                    "func": "child_tickets",
                                    "id": ticket_id
                                }, function (data) {
                                    if (data !== "0" && data !== 0) {
                                        var arch = confirm("This ticket has " + data + " child tickets. Do you want to delete them too?");
                                        if (arch)
                                            delete_childs = "1";
                                    }

                                    var reason = prompt("Reason for deleting this ticket:", "");

                                    if (reason != null && reason != "") {
                                        $.post("./lib/dyn_content.php?jquery=jquery.delete_ticket.php", {
                                            "func": "delete_ticket",
                                            "id": ticket_id,
                                            "delete_children": delete_childs,
                                            "reason": reason
                                        }, function () {
                                            $("#content").load('./lib/dyn_content.php?page=page.board.php', function () {
                                                $("#dialog").dialog("close");
                                                if (pageGenID !== null && pageGenID !== 0) {
                                                    pageGenID++;
                                                }
                                            });

                                        });
                                    }
                                });
                            }
                        });
                    }
                });

            $("#dialog").load("./lib/dyn_content.php?page=dialog.edit_ticket.php&ticket_id=" + ticket_id, function () {
                $("#dialog").dialog("open");
            });

            e.handled = true;
        }

        return false;
    });

    $(".help_text").livequery('click', function () {
        var help_id = $(this).data("id");

        $("#dialog").dialog({
            autoOpen: false,
            width: 300,
            height: 'auto',
            modal: true,
            title: "Help"
        });

        $("#dialog").load("./lib/dyn_content.php?page=dialog.phase_help.php&id=" + help_id, function () {
            $("#dialog").dialog("open");
        });

        return false;
    });

    $('.delete_comment').livequery('click', function (e) {
        e.preventDefault();
        var id = $(this).data('id');
        console.log(id);
        $.post("../lib/dyn_content.php?jquery=jquery.ticket_comment.php", {
            "func": "delete_comment",
            "id": id
        }, function () {
            location.reload(true);
        });

        return false;
    });

});

