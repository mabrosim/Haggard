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

function sortListPrio(a, b) {
    var aprio = $(a).data("prio");
    var bprio = $(b).data("prio");

    if (aprio === bprio) {
        return sortListChanged(a, b);
    }

    return (aprio < bprio) ? 1 : -1;
}

function sortListChanged(a, b) {
    var achange = $(a).data("changed");
    var bchange = $(b).data("changed");

    return (achange < bchange) ? 1 : -1;
}

function sortListCreated(a, b) {
    var acreated = $(a).data("created");
    var bcreated = $(b).data("created");

    return (acreated < bcreated) ? 1 : -1;
}

function sortListName(a, b) {
    var aname = $(a).find('#ticket_title').text();
    var bname = $(b).find('#ticket_title').text();

    return (aname > bname) ? 1 : -1;
}

function sortListWIP(a, b) {
    var awip = $(a).data("wip");
    var bwip = $(b).data("wip");

    if (awip == bwip) {
        return sortListChanged(a, b);
    }

    return (awip < bwip) ? 1 : -1;
}

function sortListComponent(a, b) {
    var acomp = $(a).data("comp");
    var bcomp = $(b).data("comp");

    if (acomp == bcomp) {
        return sortListChanged(a, b);
    }

    return (acomp > bcomp) ? 1 : -1;
}

function sortListResponsible(a, b) {
    var aresp = $(a).data("resp");
    var bresp = $(b).data("resp");

    if (aresp == bresp) {
        return sortListChanged(a, b);
    }

    return (aresp > bresp) ? 1 : -1;
}

function sort(list) {
    $.post("./lib/dyn_content.php?jquery=jquery.setting_value.php", {"settingvalue": "USE_PRIORITIES"}, function (data) {
        if (data == "1") {
            $('li', list).sort(sortListPrio).appendTo(list);
        }
        else {
            $('li', list).sort(sortListChanged).appendTo(list);
        }
    });

}
;


