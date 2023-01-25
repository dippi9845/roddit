let modal = new bootstrap.Modal("#modal-comment");

function getComments(postId) {
    
    $.ajax({
        url: "ajax/comments.php",
        type: 'GET',
        data: {post: postId},
        dataType: 'json',
        async: false,
        success: function (comments) {
            let html = '';
            for (var i = 0; i < comments.length; i++) {
                html += '<div class="d-flex align-items-center">';
                html += '<div class="flex-shrink-0">';
                html += '<img class="mr-3" style="max-width: 64px;" src="' + comments[i].ProfileImage + '" alt="Profile Image">';
                html += '</div>';
                html += '<div class="flex-grow-1 ms-3">';
                html += '<h5 class="mt-0">' + comments[i].User + '</h5>';
                html += comments[i].Text;
                html += '</div>';
                html += '</div>';
            }
            $('#modal-comment-body > div').html(html);
        }
    });
    $('#post-id-for-comment').val(postId);
    modal.show();
}

$('#send-comment').on('click', function (e) {
    e.preventDefault();
    let testo = $('#comment-textarea').val();
    
    if (testo == '') return;

    let IDpost = $('#post-id-for-comment').val();
    $.ajax({
        url: "ajax/put-comment.php",
        type: 'GET',
        data: {text: testo, postID: IDpost},
        dataType: 'json',
        success: function (mydata) {
            let elem = $('#modal-comment-body > div');
            let html = elem.html();
            html += '<div class="d-flex align-items-center">';
            html += '<div class="flex-shrink-0">';
            html += '<img class="mr-3" style="max-width: 64px;" src="' + mydata.ProfileImage + '" alt="Profile Image">';
            html += '</div>';
            html += '<div class="flex-grow-1 ms-3">';
            html += '<h5 class="mt-0">' + mydata.User + '</h5>';
            html += testo;
            html += '</div>';
            html += '</div>';
            elem.html(html);
            $('#comment-textarea').val('');
        }
    });
});