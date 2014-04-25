//vars
//var ncrip_hash = '';
//var user;
//var room = 'master';
//var last_id_group;


//functions
function refresh() {
    //to do only one petition
    $.ajaxSetup({
        async: false
    });

    $.post("http://jt.test.local/slimchat2/refresh", {
        last_id_group: localStorage.last_id_group,
        room: localStorage.room
    },
    function(data) {
        if (typeof data[0] != 'undefined') {
            localStorage.last_id_group = data[0].id;
            data.reverse();
            $.each(data, function(key, val) {
                add_msn(val);
            });
        }
    }, "json");
}
function check_input(inp) {
    if (inp.indexOf("::") !== -1) {
        //help
        if (inp.indexOf('::help') !== -1)
            show_help();
        //user
        if (inp.indexOf('::user') !== -1)
            change_user(inp);
        //room
        if (inp.indexOf('::room') !== -1)
            change_room(inp);
        //ncrip
        if (inp.indexOf('::ncrip') !== -1)
            change_ncrip(inp);
        //dcrip
        if (inp.indexOf('::dcrip') !== -1)
            dcrip_msns(inp);
    } else {
        send_msn(inp);
    }
}
function send_msn(inp) {
    //if ncript
    if (typeof localStorage.ncrip_hash != 'undefined')
        inp = ncript(inp);

    var now = new Date();
    var strDateTime = [[AddZero(now.getDate()), AddZero(now.getMonth() + 1), now.getFullYear()].join("/"), [AddZero(now.getHours()), AddZero(now.getMinutes())].join(":"), now.getHours() >= 12 ? "PM" : "AM"].join(" ");
    function AddZero(num) {
        return (num >= 0 && num < 10) ? "0" + num : num + "";
    }

    $.post("http://jt.test.local/slimchat2/send_msn", {
        user: localStorage.user,
        room: localStorage.room,
        msn: inp,
        enviat: strDateTime
    },
    function(data) {
        refresh();
    }, "json");
}
function change_user(inp) {
    patt = /::user (\w*)+/i;
    localStorage.user = patt.exec(inp)[1];
    data = {
        user: 'System',
        msn: 'Username changed to ' + user,
        enviat: get_hour()
    };
    add_msn(data);
}
function change_room(inp) {
    patt = /::room (\w*)+/i;
    localStorage.room = patt.exec(inp)[1];
    data = {
        user: 'System',
        msn: 'Room changed to ' + localStorage.room,
        enviat: get_hour()
    };
    add_msn(data);
}
function change_ncrip(inp) {
    patt = /::ncrip (\w*)+/i;
    localStorage.ncrip_hash = patt.exec(inp)[1];
    data = {
        user: 'System',
        msn: 'Encriptation token changed',
        enviat: get_hour()
    };
    add_msn(data);
}
function show_help() {
    $('.help_info').clone().prependTo('#board_chat');
}
function ncript(st) {
    try {
        st = CryptoJS.AES.encrypt(st, localStorage.ncrip_hash);
    } catch (err) {
        console.log(err);
    }
    return st.toString();
}
function dcript(st) {
    try {
        st = CryptoJS.AES.decrypt(st, localStorage.ncrip_hash);
        st = st.toString(CryptoJS.enc.Utf8);
    } catch (err) {
        console.log(err);
    }
    return st;
}
function dcrip_msns(inp) {
    patt = /::dcrip (\w*)+/i;
    n_msns = patt.exec(inp)[1];
    n_msns--;

    $('.msn.room_' + localStorage.room + '  > .content').html(function(index, oldhtml) {
        console.log(index);
        t_cont = dcript(oldhtml);
        console.log(t_cont);
        $(this).html(t_cont);

        if (n_msns == index)
            return false;
    });
}
function get_hour() {
    d = new Date();
    return d.getHours().toString() + ':' + d.getMinutes().toString();
}
function add_msn(data) {
    if (localStorage.ncrip_hash != '' && data.user != 'System') {
        data.msn = dcript(data.msn);
        console.log(data.msn);
    }
    msn = $('<div style="display:none" class="msn user_' + data.user + ' room_' + localStorage.room + '" >\n\
                    <b>' + data.user + ': </b><span class="content">' + data.msn +
            '</span><span class="pull-right muted" >&nbsp; ' + data.enviat + '</span></div>');
    msn.prependTo('#board_chat').slideDown(100);
}


document.addEventListener('DOMContentLoaded', function() {
    if (localStorage.user == '')
        localStorage.user = 'Anon' + Math.random().toString(20).substring(14);
    if (localStorage.room == '')
        localStorage.room = 'master';
    
    $('#wellcome_user').html(localStorage.user);

    $('#msn').focus();

    refresh();
    //refresh timer
    setInterval(function() {
        refresh()
    }, 600);

    //events
    $('#msn').keypress(function(e) {
        if (e.which == 13) {
            check_input($('#msn').val());
            $('#msn').val('');
        }
    });

/*
    var opt = {
        type: "list",
        title: "Primary Title",
        message: "Primary message to display",
        iconUrl: "icon.png",
        items: [{title: "Item1", message: "This is item 1."},
            {title: "Item2", message: "This is item 2."},
            {title: "Item3", message: "This is item 3."}]
    }
    chrome.notifications.create("test", opt, callback);

    function callback() {
        console.log('test');
    }
    */

});