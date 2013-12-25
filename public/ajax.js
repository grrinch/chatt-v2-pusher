var __DEBUG = false;
var myUser = {login: "", room: "", hash: ""};
var timeOuts= new Array();
var pusher = new Pusher('c40d70faadb30d3c0316');

function login() {
	if(__DEBUG) alert('uruchamiam login');
	$('#chat').hide('scale', null, 500, function() {
		$('#login_ol').show('scale', null, 1000, doThaBox);
	});
	clearLoginBox();
	if(__DEBUG) alert('koniec login');
};

function clearLoginBox() {
	if(__DEBUG) alert('uruchamiam clearloginbox');
	$('#login_form_notif').hide();
	$('#main_login').show();
	$('input:not([type="submit"])').val('');
	$('#room').next().html('');
	if(__DEBUG) alert('koniec clearloginbox');
}

function wlh(hash){
	window.location.hash = '#!'+hash;
}

function gAj() {
	return '<img src="./images/ajax-loader.gif" alt="" /> ';
};

var pcgAJ = function() {
	return '<p class="talk_wait">' + gAj() + '</p>';
};

function okImg() {
	return '<br /><img src="./images/ok.png" alt="" /> ';
};

function errImg() {
	return '<br /><img src="./images/error.png" alt="" /> ';
};

function clearAllTimeouts(){  
	for(key in timeOuts ){  
		clearTimeout(timeOuts[key]);  
	}  
}  

//
function checkSession() {
	if(__DEBUG) alert('uruchamiam checksession');
	var an = false;
	$.ajax({
		type: 'post',
		async: false,
		url: './chat.php?do=checkSession',
		success: function(data) {
			var ans = $.parseJSON(data);
			if(ans.ans == true) {
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
	return an;
};

var listenHeart = function() {
	$.ajax({
		type: 'POST',
		data: myUser,
		url: './chat.php?do=heartBeat',
		success: function(data) {
			var ans = $.parseJSON(data);
			if(ans.ans === true) {
				myUser.beats = 0;
				$('#talk').append(ans.body);
			}
			else if(ans.ans === false) {
				eval(ans.body);
			}
			else {
				myUser.beats = ans.body.beats;
			}
		},
		error: function() {
			errorBox('Nie można odczytać nowych wiadomości.');
		}
	});
	timeOuts['listenHeart'] = setTimeout(listenHeart, 3000);
};
//var gg = 0;
function heightHelper() {
	var height = $('#chat').height()-100;
	if(__DEBUG) $('#talk').append('<br />' + height + ' tt: ' + gg + 'val:' + $('#scrollToBottom').is(':checked'));
	$('#talk').css('height', height + 'px');
	$('#users div').css('height', height+5-$('#users h2').height() + 'px');
	//gg++;
	timeOuts['heightHelper'] = setTimeout(heightHelper, 1000);
};

var roomName = function() {
	$.ajax({
		type: 'POST',
		data: { room: myUser.room, hash: myUser.hash },
		url: './chat.php?do=roomName',
		success: function(data) {
			var ans = $.parseJSON(data);
			if(ans.ans === true) {
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
};

function loadTalks() {
	if(__DEBUG) alert('uruchamiam loadtalks');
	$('#talk').html('');
	if(__DEBUG) alert('login: ' + myUser.login + 'room: ' + myUser.room + 'hash: ' + myUser.hash);
	if(myUser.login && myUser.room && myUser.hash) {
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
		wlh('talk');
	}
	else clearAll();
	if(__DEBUG) alert('koniec loadtalks');
};

function getUsers() {
	alert('start getusers');
	$.ajax({
		type: 'post',
		url: './chat.php?do=getRoomUsers',
		data: {roomid : myUser.room, roomhash : myUser.hash},
		success: function(data) {
			var ans = $.parseJSON(data);
			if(ans.ans == true) {
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
	timeOuts['getUsers'] = setTimeout(getUsers, 10000);
}

function logMeIn() {
	if(__DEBUG) alert('uruchamiam logmein');
	if(quickLoginValidate()) {
		$.ajax({
			type: 'post',
			data: $('#main_login').serialize(),
			url: './chat.php?do=logMeIn',
			success: function(data) {
				var ans = $.parseJSON(data);
				if(ans.ans == true) {
					if(__DEBUG) alert('logmein ans:' + ans.ans);
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
				$('#main_login').hide();
				$('#login_form_notif').html(gAj()).show();
			},
			error: function() {
				errorBox('Nie można nawiązać połączenia');
				$('#login_form_notif').hide();
				$('#main_login').show();
			}
		});
	}
	if(__DEBUG) alert('koniec logmein');
}

function quickLoginValidate() {
	var t = 1;
	var mess = new Array();
	mess[0] = '<b>Popraw błędy w formularzu:</b><br />';
	
	if(__DEBUG) alert(mess);
	if(!$.trim($('#login').val())) {
		mess[t] = '&nbsp; - Wpisz login.<br />';
		t++;
	}
	if(!$.trim($('#room').val())) {
		mess[t] = '&nbsp; - Wpisz nazwę pokoju.<br />';
		t++;
	}
	if(!$.trim($('#pass').val())) {
		mess[t] = '&nbsp; - Wpisz hasło.<br />';
		t++;
	}
	
	if(t > 1) {
		var allmess = '';
		for(var i = 0; i < mess.length; i++){
			allmess += mess[i];
		}
		errorBox(allmess);
		return false;
	}
	else {
		return true;
	}
}

function logout() {
	if(__DEBUG) alert('uruchamiam logout');
	$.ajax({
		type: 'post',
		url: './chat.php?do=logout',
		success: login
	});
	if(__DEBUG) alert('koniec logout');
};

function checkROom() {
	if($.trim($('#room').val()) != '') {
		$.ajax({
			type: 'POST',
			data: $('#room').serialize(),
			url: './chat.php?do=roomCheck',
			success: function(data) {
				var ans = $.parseJSON(data);
				if(ans.ans == true) {
					$('#room').next().html(okImg() + ans.body);
				}
				else {
					$('#room').next().html(errImg() + ans.body);
				}
			},
			beforeSend: function() {
				$('#room').next().html(gAj());
			},
			error: function() {
				errorBox('Nie można nawiązać połączenia');
			}
		});
	}
};

function clearAll(){
	if(__DEBUG) alert('uruchamiam clearall');
	$('#chat').hide();
	$('#talk').html('');
	logout();
	wlh('');
	clearAllTimeouts();
	if(__DEBUG) alert('koniec clearall');
};

function errorBox(msg) {
	$('.errorbox').html(msg);
	$('.errorbox').show('scale', null, 500, function() {
		timeOuts['innerErrorBox'] = setTimeout(function() {
			$( ".errorbox" ).hide('scale', null, 500);
			$( ".errorbox" ).html('');
		}, 5000);
	});
};

function doThaBox() {
	var wys = $('#login_form').height();
	wys = (wys/2)+20;
	$('#login_form').css('margin-top', -wys);
};

var scrollIfNeeded = function() {
	if($('#scrollToBottom').is(':checked')) {
		$("#talk").animate({ scrollTop: $("#talk").prop("scrollHeight") }, 500);
	}
	timeOuts['scrollIfNeeded'] = setTimeout(scrollIfNeeded, 1000); 
};

function sendThaMessage(bd) {
	$('#msgbd').val('');
	$.ajax({
		type: 'POST',
		data: {roomid : myUser.room, roomhash : myUser.hash, userlogin: myUser.login, post: bd},
		url: './chat.php?do=postMessage',
		success: function(data) {
			var ans = $.parseJSON(data);
			if(ans.ans === true) {
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
};

$(document).ready(function() {
	$('#login_ol').hide();
	$('#details').hide();
	$('#login_form_notif').hide();
	$('.errorbox').hide();
	
	if(checkSession()) {
		loadTalks();
	}
	else {
		clearAll();
	}
	
	$('#wyloguj').click(function() {
		$('#details').fadeOut('slow');
		logout();
	});
	
	
	$('#room').blur(checkROom);
	
	$('#main_login').submit(function() {
		if(quickLoginValidate()) {
			logMeIn();
		}
		return false;
	});
	
	$('#msgform').submit(function() {
		var bd = $.trim($('#msgbd').val());
		if(bd.length > 0) {
			sendThaMessage(bd);
		}
		return false;
	});
	
	$('#msgbd').keypress(function(e){
		if(e.which == 13) {
			e.preventDefault();
			$('#msgform').submit();
		}
	});
});
