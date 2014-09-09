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

var current_page = "";

$.ajaxSetup({cache: true, global: true});

function getURLParameter(name)
{
    return decodeURI((RegExp(name + '=' + '(.+?)(&|$)').exec(location.search) || [, null])[1]);
}

$(document).ready(function()
{
    $('input, textarea').placeholder();

    var url_wiki = "https://github.com/mabrosim/Haggard/wiki";
    var timeout = 500;
    var closetimer = 0;
    var menuitem = 0;

    var sections = $("#menu li");
    var content = $("#content");

    if ($.cookie("current_page")) {
        current_page = $.cookie("current_page");
    }

    if (current_page === "")
    {
        var ticket_id = getURLParameter('ticket_id');
        var url = "./lib/dyn_content.php?page=page.board.php";
        if (ticket_id && ticket_id !== null && ticket_id !== "null")
        {
            url += "&ticket_id=" + ticket_id;
        }

        content.load(url, function()
        {
            content.show();
            current_page = 'board';
            $.cookie('current_page', 'board');
            showHidePhases();
        });
    } else {
        content.load("./lib/dyn_content.php?page=page." + current_page + ".php", function() {
            content.show();
            if (current_page === 'board') {
                showHidePhases();
            }
        });
    }

    function showHidePhases() {
        if ($.cookie('hidden_phases')) {
            var hiddenPhases = $.cookie('hidden_phases').split(",");
            hiddenPhases.forEach(function(entry) {
                $('.phase' + entry).hide();
                $('#phase_name_' + entry).find('.name').text('');
                $('#phase_name_' + entry).find('.hide_phase').css('background-image', 'url(./img/icons/11.png)');
                $('#phase_ticket_holder' + entry).css("width", "30px");
                $('#phase_name_' + entry).css("width", "30px");
            });
        }
    }

    function navigateTo(page) {
        if (current_page === page)
            return;
        content.fadeTo('fast', 0, function() {
            content.load("./lib/dyn_content.php?page=page." + page + ".php", function() {
                content.fadeTo('fast', 1);
                current_page = "./lib/dyn_content.php?page=page." + page + ".php";
                $.cookie('current_page', page);
            });
        });
    }

    sections.click(function(e)
    {
        e.preventDefault();
        if (this.id === 'login') {
            return true;
        }

        switch (this.id)
        {
            default:
            case "kanban_logo":
                navigateTo('board');
                break;
            case "log":
                navigateTo('log');
                break;
            case "statistics":
                navigateTo('statistics');
                break;
            case "settings":
                navigateTo('settings');
                break;
            case "message_board":
                navigateTo('message_board');
                break;
        }
    });

    $('#about').on('click', function(e)
    {
        e.preventDefault();
        window.open(url_wiki);
    });

    $('#logout').on('click', function(e) {
        e.preventDefault();
        $.post("./lib/dyn_content.php?jquery=jquery.login.php", {"func": "logout"}, function(data)
        {
            logged_in = false;
            location.reload(true);
        });
    });

    $('.new_notification').on('fadeBG', function() {
        $(this).animate({backgroundColor: "#FFFFFF"}, 1000);
    });

    $('.notification').livequery('click', function() {
        var type = $(this).data('type');
        if (type)
        {
            var link = $(this).data('link');
            $('#notifications').hide();
            switch (type)
            {
                case 'page':
                    content.fadeTo('fast', 0, function() {
                        content.load("./lib/dyn_content.php?page=" + link, function() {
                            content.fadeTo('fast', 1);
                            current_page = "./lib/dyn_content.php?page=" + link;
                        });
                    });
                    break;
                case 'comment':
                    openCommentWindow(link);
                    break;

                case 'message':
                    content.fadeTo('fast', 0, function() {
                        content.load("./lib/dyn_content.php?page=page.topic_messages.php&id=" + link, function() {
                            content.fadeTo('fast', 1);
                            current_page = "./lib/dyn_content.php?page=page.topic_messages.php&id=" + link;
                        });
                    });
                    break;
            }
        }
    });

    $('#notification_menu').on('click', function(e)
    {
        e.stopImmediatePropagation();
        e.preventDefault();
        $('#notifications').css('left', e.pageX - ($('#notifications').width() / 2));
        $('#notification_area').html("");
        $.post('./lib/dyn_content.php?jquery=jquery.notifications.php', {func: 'get_notifications'}, function(data)
        {
            if (data != "")
            {
                var obj = JSON.parse(data);
                jQuery.each(obj, function(i, notification) {
                    var html = '<div class="notification';
                    if (notification.status == 'unread')
                    {
                        html += ' new_notification" style="background-color: #f0f0f0;"';
                    }
                    else
                    {
                        html += '"';
                    }
                    html += 'data-type="' + notification.type + '"';
                    html += 'data-link="' + notification.link + '"';
                    html += '><div class="notification_title">';
                    html += notification.title;
                    html += '</div><div class="notification_time">' + notification.time + '</div>';
                    html += '</div>';
                    $('#notification_area').prepend(html);
                });
            }
            else
            {
                $('#notification_area').prepend('<div style="text-align:center">No notifications</div>');
            }

            var position = $('#notification_menu').offset();
            $('#notifications').toggle();

            var hide = setTimeout(function()
            {
                $.post('./lib/dyn_content.php?jquery=jquery.notifications.php', {func: 'set_as_read'}, function(data)
                {
                    $('.notification').trigger('fadeBG');
                    $('#notification_count').animate({backgroundColor: "#afafaf"}, 1000);
                    $('#notification_count').html('0');
                    clearInterval(hide);
                });

            }, 500);

            $('#see_all_notifications').click(function(e)
            {
                e.preventDefault();
                $('#notifications').hide();
                content.fadeTo('fast', 0, function() {
                    content.load("./lib/dyn_content.php?page=page.notifications.php", function() {
                        content.fadeTo('fast', 1);
                        current_page = "./lib/dyn_content.php?page=page.notifications.php";
                    });
                });
            });
        });
    });

    $('#board_select').livequery('change', function(e) {
        e.preventDefault();
        var url = $(this).find("option:selected").data('url');
        if (url.length > 0) {
            window.location.href = url;
        }
    });
});

$(document).mouseup(function(e)
{
    var container = $('#notifications');
    if (container.has(e.target).length === 0)
    {
        container.hide();
    }
});
