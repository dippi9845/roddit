function setCookie(cname, cvalue, exdays) {
    const d = new Date();
    d.setTime(d.getTime() + (exdays*24*60*60*1000));
    let expires = "expires="+ d.toUTCString();
    document.cookie = cname + "=" + cvalue + ";" + expires + ";path=/";
}

function getCookie(cname) {
    let name = cname + "=";
    let decodedCookie = decodeURIComponent(document.cookie);
    let ca = decodedCookie.split(';');
    for(let i = 0; i <ca.length; i++) {
      let c = ca[i];
      while (c.charAt(0) == ' ') {
        c = c.substring(1);
      }
      if (c.indexOf(name) == 0) {
        return c.substring(name.length, c.length);
      }
    }
    return "";
}

let latest = null;

if (getCookie('latest') != "") {
    latest = Number(getCookie('latest'));
}

let showed = 0;
let badge = false;
$('#noti-drop > span').hide();
let notificationList = $('#notification-list');

$('#noti-drop').on('show.bs.dropdown', function () {
    var nty = null;
    showing = true;
    badge = false;
    $('#noti-drop > span').hide(); // hide the badge

    let tmp = getLatest();
    
    if (latest == null) {
        latest = tmp;
        setCookie('latest', latest, 7);
        nty = show(0);
        console.log(nty);
    }

    else if (latest < tmp) {
        // here, means there are new notifications
        // start from the latest notification 0,
        // and take the difference that must be 
        nty = show(0, tmp - latest);
        latest = tmp;
        setCookie('latest', latest, 7);
    }

    if (nty != null) {
        drawNotifications(nty);
        showed += nty.length;
    }
    
});


setInterval(function () {
    if (latest == null) return;
    var tmp = getLatest();
    
    if (latest < tmp) {
        badge = true;
        $('#noti-drop > span').show();
    }
}, 1000);

setInterval(function () {
    if (notificationList.hasClass("show") && (notificationList.scrollTop() + notificationList.height() + 20 >= notificationList[0].scrollHeight)) {
        let a = show(showed);
        if (a != null) {
            drawNotifications(a, false);
            showed += a.length;
        }
    }
}, 1000);


function drawNotifications(nty, preppend = true) {
    if (preppend) {
        for (var i = nty.length - 1; i >= 0 ; i--) {
            $('#notification-list').prepend('<li class="dropdown-item"><h5>' + nty[i]['Title'] + '</h5><p>' + nty[i]['Message'] + '<br><span style="font-size:10px">' + nty[i]['Inserimento'] + '</span></p></li>');
        }
    }
    
    else {
        for (var i = 0; i < nty.length; i++) {
            $('#notification-list').append('<li class="dropdown-item"><h5>' + nty[i]['Title'] + '</h5><p>' + nty[i]['Message'] + '<br><span style="font-size:10px">' + nty[i]['Inserimento'] + '</span></p></li>');
        }
    }
}

function getLatest() {
    var latest_r = null;
    $.ajax({
        async: false,
        url: 'ajax/get-last-notification',
        type: 'GET',
        dataType: 'json',
        success: function (data) {
            if (data.length > 0) {
                latest_r = data[0]['ID'];
            }
        }
    });
    
    return latest_r;
}

function show(offset = 0, limit = 5) {
    var rdata = null;
    $.ajax({
        async: false,
        url: 'ajax/get-my-notification',
        type: 'GET',
        data : { o : offset, n : limit },
        dataType: 'json',
        success: function (data) {
            rdata = data;
        }
    });
    return rdata;
}