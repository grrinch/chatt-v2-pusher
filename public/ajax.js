// debugowanie
var __DEBUG = false;
var __DISPLAY_DEBUG = false;

// ustawienia danych użytkownika i pokoju na potrzeby JS
var myUser = {login: "", room: "", hash: ""};

// ustawienia timeOut-ów
var timeOuts = new Array();
var _heightHelperTimer = 5000;
var _listenHeartTimer = 3 * 1000 * 60; // 3 minuty (3 * 1000 milisekund * 60 sekund)
var _getUsersTimer = 10000;
var _errorBoxTimer = 5000;
var _scrollIfNeededTimer = 5000;

// Pusher
var pusher = new Pusher('c40d70faadb30d3c0316');
var channel = '';

function login() {
    if (__DEBUG)
        console.log('uruchamiam ' + arguments.callee.name);
    $('#chat').hide('scale', null, 500, function() {
        $('#login_ol').show('scale', null, 1000, doThaBox);
    });
    clearLoginBox();
    if (__DEBUG)
        console.log('koniec login');
}
;

function clearLoginBox() {
    if (__DEBUG)
        console.log('uruchamiam ' + arguments.callee.name);
    $('#login_form_notif').hide();
    $('#main_login').show();
    $('input:not([type="submit"])').val('');
    $('#room').next().html('');
    if (__DEBUG)
        console.log('koniec clearloginbox');
}

function wlh(hash) {
    if (__DEBUG)
        console.log('uruchamiam ' + arguments.callee.name);
    window.location.hash = '#!' + hash;
    if (__DEBUG)
        console.log('koniec ' + arguments.callee.name);
}

function gAj() {
    if (__DEBUG)
        console.log('uruchamiam ' + arguments.callee.name);
    return '<img src="/images/ajax-loader.gif" alt="" /> ';
    if (__DEBUG)
        console.log('koniec ' + arguments.callee.name);
}
;

var pcgAJ = function() {
    if (__DEBUG)
        console.log('uruchamiam ' + arguments.callee.name);
    return '<p class="talk_wait">' + gAj() + '</p>';
    if (__DEBUG)
        console.log('koniec ' + arguments.callee.name);
};

function okImg() {
    if (__DEBUG)
        console.log('uruchamiam ' + arguments.callee.name);
    return '<br /><img src="/images/ok.png" alt="" /> ';
    if (__DEBUG)
        console.log('koniec ' + arguments.callee.name);
}
;

function errImg() {
    if (__DEBUG)
        console.log('uruchamiam ' + arguments.callee.name);
    return '<br /><img src="/images/error.png" alt="" /> ';
    if (__DEBUG)
        console.log('koniec ' + arguments.callee.name);
}
;

function clearAllTimeouts() {
    if (__DEBUG)
        console.log('uruchamiam ' + arguments.callee.name);
    for (key in timeOuts) {
        clearTimeout(timeOuts[key]);
    }
    if (__DEBUG)
        console.log('koniec ' + arguments.callee.name);
}

//
function checkSession() {
    if (__DEBUG)
        console.log('uruchamiam ' + arguments.callee.name);
    var an = false;
    var url = '/check-session';
    $.ajax({
        type: 'post',
        async: false,
        url: url,
        success: function(data) {
            var ans = data;
            if (ans.ans === true) {
                myUser = ans.body;
                an = true;
            }
            else {
                $('#login_form_notif').hide();
                $('#main_login').show();
                errorBox(ans.body);
                an = false;
            }
        },
        beforeSend: function() {
            wlh(url);
            $('#main_login').hide();
            $('#login_form_notif').html(gAj()).show();
        },
        error: function() {
            errorBox('Nie można nawiązać połączenia');
            $('#login_form_notif').hide();
            $('#main_login').show();
            an = false;
            return an;
        }
    });
    if (__DEBUG)
        console.log('koniec ' + arguments.callee.name);
    return an;
}
;

var listenHeart = function() {
    if (__DEBUG)
        console.log('uruchamiam ' + arguments.callee.name);
    var url = '/heart-beat';
    $.ajax({
        type: 'POST',
        data: myUser,
        url: url,
        success: function(data) {
            var ans = data;
            if (ans.ans === true) {
                if (typeof ans.body.beats)
                    myUser.beats = ans.body.beats;

            }
            else {
                eval(ans.body);
            }

        },
        error: function() {
            errorBox('Nie można odczytać nowych wiadomości.');
        }
    });

    timeOuts['listenHeart'] = setTimeout(listenHeart, _listenHeartTimer);

    if (__DEBUG)
        console.log('koniec ' + arguments.callee.name);
};

function heightHelper() {
    if (__DEBUG)
        console.log('uruchamiam ' + arguments.callee.name);
    var height = $('#chat').height() - 100;
    //if (__DEBUG)
    //$('#talk').append('<br />' + height + 'val:' + $('#scrollToBottom').is(':checked'));
    $('#talk').css('height', height + 'px');
    $('#users div').css('height', height + 5 - $('#users h2').height() + 'px');

    timeOuts['heightHelper'] = setTimeout(heightHelper, _heightHelperTimer);

    if (__DEBUG)
        console.log('koniec ' + arguments.callee.name);
}
;

var roomName = function() {
    if (__DEBUG)
        console.log('uruchamiam ' + arguments.callee.name);
    var url = '/room-name';
    $.ajax({
        type: 'POST',
        data: {room: myUser.room, hash: myUser.hash},
        url: url,
        success: function(data) {
            var ans = data;
            if (ans.ans === true) {
                $('.talk_wait').remove();
                $('#talk').append(ans.body);
            }
            else {
                eval(ans.body);
            }
        },
        beforeSend: function() {
            $('#talk').append(pcgAJ());
        },
        error: function() {
            $('.talk_wait').remove();
            errorBox('Nie można nawiązać połączenia');
        }
    });
    if (__DEBUG)
        console.log('koniec ' + arguments.callee.name);
};

function activate() {
    if (__DEBUG)
        console.log('uruchamiam ' + arguments.callee.name);
    var url = '/activate';
    $.ajax({
        type: 'POST',
        data: myUser,
        url: url,
        success: function(data) {
            var ans = data;
            if (ans.ans === true) {
                $('#talk').append(ans.body);

            }
            else {
                eval(ans.body);
            }

        },
        error: function() {
            errorBox('Nie można odczytać nowych wiadomości.');
        }
    });

    timeOuts['listenHeart'] = setTimeout(listenHeart, _listenHeartTimer);

    if (__DEBUG)
        console.log('koniec ' + arguments.callee.name);
}

function loadTalks() {
    if (__DEBUG)
        console.log('uruchamiam ' + arguments.callee.name);
    $('#talk').html('');
    if (__DEBUG)
        console.info('login: ' + myUser.login + 'room: ' + myUser.room + 'hash: ' + myUser.hash);
    if (myUser.login && myUser.room && myUser.hash) {
        roomName();
        $('#details').show();
        $('#logged_as').html(myUser.login);
        $('#login_ol').hide('scale', null, 500, function() {
            $('#chat').show('scale', null, 1000);
        });
        heightHelper();
        scrollIfNeeded();
        getUsers();
        listenHeart();

        activate();

        wlh('talk');
    }
    else
        clearAll();
    if (__DEBUG)
        console.log('koniec ' + arguments.callee.name);
}
;

function getUsers() {
    if (__DEBUG)
        console.log('uruchamiam ' + arguments.callee.name);
    var url = '/get-room-users';
    $.ajax({
        type: 'post',
        url: url,
        data: {roomid: myUser.room, roomhash: myUser.hash},
        success: function(data) {
            var ans = data;
            if (ans.ans === true) {
                $('#users div').css('text-align', 'left').html(ans.body);
            }
            else {
                eval(ans.body);
            }
        },
        beforeSend: function() {
            $('#users div').css('text-align', 'center').html(gAj());
        },
        error: function() {
            errorBox('Nie można odświeżyć listy użytkowników.');
        }
    });

    timeOuts['getUsers'] = setTimeout(getUsers, _getUsersTimer);

    if (__DEBUG)
        console.log('koniec ' + arguments.callee.name);
}

function logMeIn() {
    if (__DEBUG)
        console.log('uruchamiam ' + arguments.callee.name);
    if (quickLoginValidate()) {
        var url = '/log-me-in';
        $.ajax({
            type: 'post',
            data: $('#main_login').serialize(),
            url: url,
            success: function(data) {
                var ans = data;
                if (ans.ans === true) {
                    if (__DEBUG)
                        console.info('logmein ans:' + ans.ans);
                    wlh('/consulting-man');
                    myUser = ans.body;
                    loadTalks();
                }
                else {
                    $('#login_form_notif').hide();
                    $('#main_login').show();
                    errorBox(ans.body);
                }
            },
            beforeSend: function() {
                wlh(url);
                $('#main_login').hide();
                $('#login_form_notif').html(gAj()).show();
            },
            error: function() {
                wlh('/error')
                errorBox('Nie można nawiązać połączenia');
                $('#login_form_notif').hide();
                $('#main_login').show();
            }
        });
    }
    if (__DEBUG)
        console.log('koniec ' + arguments.callee.name);
}

function quickLoginValidate() {
    if (__DEBUG)
        console.log('uruchamiam ' + arguments.callee.name);
    var t = 1;
    var mess = new Array();
    mess[0] = '<b>Popraw błędy w formularzu:</b><br />';

    if (__DEBUG)
        console.info(mess);
    if (!$.trim($('#login').val())) {
        mess[t] = '&nbsp; - Wpisz login.<br />';
        t++;
    }
    if (!$.trim($('#room').val())) {
        mess[t] = '&nbsp; - Wpisz nazwę pokoju.<br />';
        t++;
    }
    if (!$.trim($('#pass').val())) {
        mess[t] = '&nbsp; - Wpisz hasło.<br />';
        t++;
    }

    if (t > 1) {
        var allmess = '';
        for (var i = 0; i < mess.length; i++) {
            allmess += mess[i];
        }
        errorBox(allmess);

        if (__DEBUG)
            console.log('koniec ' + arguments.callee.name);

        return false;
    }
    else {
        if (__DEBUG)
            console.log('koniec ' + arguments.callee.name);

        return true;
    }
}

function logout() {
    if (__DEBUG)
        console.log('uruchamiam ' + arguments.callee.name);
    var url = '/logout';
    wlh(url);
    $.ajax({
        type: 'post',
        url: url,
        success: login
    });
    if (__DEBUG)
        console.log('koniec ' + arguments.callee.name);
}
;

function checkROom() {
    if (__DEBUG)
        console.log('uruchamiam ' + arguments.callee.name);
    if ($.trim($('#room').val()) != '') {
        var url = '/room-check';
        $.ajax({
            type: 'POST',
            data: $('#room').serialize(),
            url: url,
            success: function(data) {
                var ans = data;
                if (ans.ans == true) {
                    $('#room').next().html(okImg() + ans.body);
                }
                else {
                    $('#room').next().html(errImg() + ans.body);
                }
            },
            beforeSend: function() {
                wlh(url);
                $('#room').next().html(gAj());
            },
            error: function() {
                errorBox('Nie można nawiązać połączenia');
            }
        });
    }
    if (__DEBUG)
        console.log('koniec ' + arguments.callee.name);
}
;

function clearAll() {
    if (__DEBUG)
        console.log('uruchamiam ' + arguments.callee.name);
    $('#chat').hide();
    $('#talk').html('');
    logout();
    wlh('');
    clearAllTimeouts();
    if (__DEBUG)
        console.log('koniec ' + arguments.callee.name);
}
;

function errorBox(msg) {
    if (__DEBUG)
        console.log('uruchamiam ' + arguments.callee.name);

    $('.errorbox').html(msg);
    $('.errorbox').show('scale', null, 500, function() {
        timeOuts['innerErrorBox'] = setTimeout(function() {
            $(".errorbox").hide('scale', null, 500);
            $(".errorbox").html('');
        }, _errorBoxTimer);
    });

    if (__DEBUG)
        console.log('koniec ' + arguments.callee.name);
}
;

function doThaBox() {
    if (__DEBUG)
        console.log('uruchamiam ' + arguments.callee.name);
    var wys = $('#login_form').height();
    wys = (wys / 2) + 20;
    $('#login_form').css('margin-top', -wys);
    if (__DEBUG)
        console.log('koniec ' + arguments.callee.name);
}
;

var scrollIfNeeded = function() {
    if (__DEBUG)
        console.log('uruchamiam ' + arguments.callee.name);
    if ($('#scrollToBottom').is(':checked')) {
        $("#talk").animate({scrollTop: $("#talk").prop("scrollHeight")}, 500);
    }
    timeOuts['scrollIfNeeded'] = setTimeout(scrollIfNeeded, _scrollIfNeededTimer);
    if (__DEBUG)
        console.log('koniec ' + arguments.callee.name);
};

function sendThaMessage(bd) {
    if (__DEBUG)
        console.log('uruchamiam ' + arguments.callee.name);
    $('#msgbd').val('');
    var url = '/post-message';
    $.ajax({
        type: 'POST',
        data: {roomid: myUser.room, roomhash: myUser.hash, userlogin: myUser.login, post: bd},
        url: url,
        success: function(data) {
            var ans = data;
            if (ans.ans === true) {
                $('.talk_wait').remove();
                $('#talk').append(ans.body);
            }
            else {
                eval(ans.body);
            }
        },
        beforeSend: function() {
            wlh(url);
            $('#talk').append(pcgAJ());
        },
        error: function() {
            $('.talk_wait').remove();
            errorBox('Nie można nawiązać połączenia');
        }
    });
    if (__DEBUG)
        console.log('koniec ' + arguments.callee.name);
}
;

$(document).ready(function() {
    $('#login_ol').hide();
    $('#details').hide();
    $('#login_form_notif').hide();
    $('.errorbox').hide();

    if (checkSession()) {
        loadTalks();
        channel = pusher.subscribe(myUser.hash);
        channel.bind('message-post', function(data) {
            $('#talk').append(data.body);
        }
        );
    }
    else {
        clearAll();
    }

    if (__DISPLAY_DEBUG)
        console.info($('#debug1').html());

    $('#wyloguj').click(function() {
        $('#details').fadeOut('slow');
        logout();
    });


    $('#room').blur(checkROom);

    $('#main_login').submit(function() {
        if (quickLoginValidate()) {
            logMeIn();
        }
        return false;
    });

    $('#msgform').submit(function() {
        var bd = $.trim($('#msgbd').val());
        if (bd.length > 0) {
            sendThaMessage(bd);
        }
        return false;
    });

    $('#msgbd').keypress(function(e) {
        if (e.which == 13) {
            e.preventDefault();
            $('#msgform').submit();
        }
    });
});
