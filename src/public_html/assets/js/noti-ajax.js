let latest = null;
let first = null;

$('#noti-drop').on('show.bs.dropdown', function () {
    $.ajax({
        url: 'ajax/get-last-notification.php',
        type: 'GET',
        dataType: 'html',
        success: function (data) {
            data = JSON.parse(data);
            console.log(data);
            if (latest == null || latest < data['ID']) {
                latest = data['ID'];
                show();
            }
        }
    });
});

function show(latestID) {
    $.ajax({
        url: 'ajax/get-my-notification.php?l=' + latestID,
        type: 'GET',
        dataType: 'html',
        success: function (data) {
            data = JSON.parse(data);
            console.log(data);
            for (var i = 0; i < data.length; i++) {
                $('#notification-list')
                .append('<li class="dropdown-item"><h4> ' + data[i]['Title'] + ' </h4><p> ' + data[i]['Message'] + ' </p></li>');
            }
        }
    });
}