let latest = null;
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
        nty = show(0);
        console.log(nty);
    }

    else if (latest < tmp) {
        // here, means there are new notifications
        // start from the latest notification 0,
        // and take the difference that must be 
        nty = show(0, tmp - latest);
        latest = tmp;
    }

    if (nty != null) {
        for (var i = nty.length - 1; i >= 0 ; i--) {
            $('#notification-list').prepend('<li class="dropdown-item"><h5>' + nty[i]['Title'] + '</h5><p>' + nty[i]['Message'] + '<br><span style="font-size:10px">' + nty[i]['Inserimento'] + '</span></p></li>');
        }
        showed += nty.length;
    }
    
});

// TODO: caricamento delle precedenti
// TODO: salvare il latest nei cookies

setInterval(function () {
    if (latest == null) return;
    var tmp = getLatest();
    
    if (latest < tmp) {
        badge = true;
        $('#noti-drop > span').show();
    }
}, 1000);

setInterval(function () {
    if (notificationList.scrollTop() + notificationList.height() >= notificationList[0].scrollHeight) {
        console.log('bottom');
        show(showed);
    }
}, 1000);

function getLatest() {
    var latest_r = null;
    $.ajax({
        async: false,
        url: 'ajax/get-last-notification.php',
        type: 'GET',
        dataType: 'json',
        success: function (data) {
            latest_r = data[0]['ID'];
        }
    });
    
    return latest_r;
}

function show(offset = 0, limit = 5) {
    var rdata = null;
    $.ajax({
        async: false,
        url: 'ajax/get-my-notification.php',
        type: 'GET',
        data : { o : offset, n : limit },
        dataType: 'json',
        success: function (data) {
            //console.log(data);
            rdata = data;
        }
    });
    return rdata;
}