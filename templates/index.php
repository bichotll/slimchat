
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <title>Slimchat</title>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name="description" content="">
        <meta name="author" content="">

        <!-- CSS -->
        <link href="lib/css/bootstrap.css" rel="stylesheet">
        <link href='http://fonts.googleapis.com/css?family=Ubuntu+Mono' rel='stylesheet' type='text/css'>
        <style type="text/css">

            /* Sticky footer styles
            -------------------------------------------------- */

            html,
            body {
                height: 100%;
                /* The html and body elements cannot have any padding or margin. */
            }
            p, span, div, h1, h2, h3, h4, h5, h6{
                font-family: 'Ubuntu Mono', sans-serif;
            }

            /* Wrapper for page content to push down footer */
            #wrap {
                min-height: 100%;
                height: auto !important;
                height: 100%;
                /* Negative indent footer by it's height */
                margin: 0 auto -60px;
            }

            #board_chat{
                padding-top: 15px;
            }
            
            .msn{
                margin-bottom: 8px;
            }
            .user_System{
                background: #f7f7f7;
            }



            /* Custom page CSS
            -------------------------------------------------- */
            /* Not required for template or sticky footer method. */

            .container {
                width: auto;
                max-width: 680px;
            }
            .container .credit {
                margin: 20px 0;
            }

        </style>
        <link href="lib/css/bootstrap-responsive.css" rel="stylesheet">

        <!-- HTML5 shim, for IE6-8 support of HTML5 elements -->
        <!--[if lt IE 9]>
          <script src="../assets/js/html5shiv.js"></script>
        <![endif]-->

        <!-- Fav and touch icons -->
        <link rel="shortcut icon" href="lib/img/favicon.png">
    </head>

    <body>


        <!-- Part 1: Wrap all page content here -->
        <div id="wrap">

            <!-- Begin page content -->
            <div class="container">
                <div class="row-fluid">
                    <input name="" id="msn" class="span12" placeholder="Write and press Enter..." >
                </div>
                <div id="board_chat">
                    <br>
                    <br>
                    <div class="alert alert-info help_info">
                        <button type="button" class="close" data-dismiss="alert">&times;</button>
                        <b>::help</b> All the help!<br />
                        <b>::user</b> Set the user<br />
                        <b>::room</b> Change the group chat<br />
                        <b>::ncrip</b> Encript the conversation and messages. Only in local.<br />
                        <b>::dcrip</b> Decript the "x" (or all) last messages.<br />
                        <br /><br />
                        <dl>
                            <dt>Example</dt>
                            <dd>::user somebody ::room bathroom ::ncrip 0nedr0nKinTheStreet ::dcrip 10</dd>
                        </dl>
                    </div>
                    <p>Type <b>::help</b> to see the manual and options.</p>
                    <h5>Welcome to SlimChat <b><span id="wellcome_user" ></span></b>. Please, enjoy it!</h5>
                </div>
            </div>

            <div id="push"></div>
        </div>



        <!-- Le javascript
        ================================================== -->
        <!-- Placed at the end of the document so the pages load faster -->
        <script src="lib/js/libs/jquery-1.9.0/jquery.min.js"></script>
        <script src="lib/js/libs/aes.js"></script>
        <script src="lib/js/bootstrap.min.js"></script>
        <script type="text/javascript">
            //vars
            var ncrip_hash = '';
            var user;
            var room = 'master';
            var last_id_group;
            
            //temp
            var temp_temp;
            

            //functions
            function refresh() {
                $.post("refresh", {
                    last_id_group: last_id_group,
                    room: room
                },
                function(data) {
                    if ( typeof data[0].id != 'undefined' ){
                        last_id_group = data[0].id;
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
                if (ncrip_hash != '')
                    inp = ncript(inp);

                var now = new Date();
                var strDateTime = [[AddZero(now.getDate()), AddZero(now.getMonth() + 1), now.getFullYear()].join("/"), [AddZero(now.getHours()), AddZero(now.getMinutes())].join(":"), now.getHours() >= 12 ? "PM" : "AM"].join(" ");
                function AddZero(num) {
                    return (num >= 0 && num < 10) ? "0" + num : num + "";
                }

                $.post("send_msn", {
                    user: user,
                    room: room,
                    msn: inp,
                    enviat: strDateTime
                },
                function(data) {
                    refresh();
                }, "json");
            }
            function change_user(inp) {
                patt = /::user (\w*)+/i;
                user = patt.exec(inp)[1];
                data = {
                    user: 'System',
                    msn: 'Username changed to ' + user,
                    enviat: get_hour()
                };
                add_msn(data);
            }
            function change_room(inp) {
                patt = /::room (\w*)+/i;
                room = patt.exec(inp)[1];
                data = {
                    user: 'System',
                    msn: 'Room changed to ' + room,
                    enviat: get_hour()
                };
                add_msn(data);
            }
            function change_ncrip(inp) {
                patt = /::ncrip (\w*)+/i;
                ncrip_hash = patt.exec(inp)[1];
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
                    st = CryptoJS.AES.encrypt(st, ncrip_hash);
                } catch(err){
                    console.log(err);
                }
                return st.toString();
            }
            function dcript(st) {
                try{
                    st = CryptoJS.AES.decrypt(st, ncrip_hash);
                    st = st.toString(CryptoJS.enc.Utf8);
                } catch(err){
                    console.log(err);
                }
                return st;
            }
            function dcrip_msns(inp){
                patt = /::dcrip (\w*)+/i;
                n_msns = patt.exec(inp)[1];
                n_msns--;
                
                console.log(ncrip_hash);
                $('.msn.room_' + room + '  > .content').html(function(index, oldhtml){
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
                if ( ncrip_hash != '' && data.user != 'System' ){
                    temp_temp = data.msn;
                    data.msn = dcript(data.msn);
                    console.log(data.msn);
                }
                msn = $('<div style="display:none" class="msn user_' + data.user + ' room_' + room + '" >\n\
                    <b>' + data.user + ': </b><span class="content">' + data.msn +
                        '</span><span class="pull-right muted" >&nbsp; ' + data.enviat + '</span></div>');
                msn.prependTo('#board_chat').slideDown(100);
            }

            //bootstrap
            $(document).ready(function() {
                user = 'Anon' + Math.random().toString(20).substring(14);
                $('#wellcome_user').html(user);

                $('#msn').focus();

                refresh();
                //refresh timer
                setInterval(function(){refresh()}, 300);

                //events
                $('#msn').keypress(function(e) {
                    if (e.which == 13) {
                        check_input($('#msn').val());
                        $('#msn').val('');
                    }
                });
            });
        </script>

    </body>
</html>
