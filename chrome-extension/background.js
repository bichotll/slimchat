//vars
//var ncrip_hash = '';
//var user;
//var room = 'master';
var last_id_group;




//functions
function refresh() {
    //to do only one petition
    $.ajaxSetup({
        async: false
    });

    $.post("http://jt.test.local/slimchat2/refresh", {
        last_id_group: last_id_group,
        room: localStorage.room
    },
    function(data) {
        if (typeof data[0] != 'undefined') {
            last_id_group = data[0].id;
            data.reverse();

            //notification opts
            var opt = {
                type: "list",
                title: "Slimchat",
                message: "Dude, you have new messages",
                iconUrl: "icon.png",
                items: []
            }

            $.each(data, function(key, val) {
                opt.items.push({title: val.user, message: val.msn});
            });

            //create notification
            chrome.notifications.create('not' + Math.floor((Math.random() * 1000000) + 1), opt, callback);

            function callback() {
            }
            ;
        }
    }, "json");
}




document.addEventListener('DOMContentLoaded', function() {
    if (typeof localStorage.user == 'undefined')
        localStorage.user = 'Anon' + Math.random().toString(20).substring(14);
    if (typeof localStorage.room == 'undefined')
        localStorage.room = 'master';
    
    last_id_group = localStorage.last_id_group;
    
    console.log('app started');
    refresh();
    //refresh timer
    setInterval(function() {
        refresh()
    }, 1200);
});