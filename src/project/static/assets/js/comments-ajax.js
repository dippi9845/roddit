let modal = new bootstrap.Modal("#modal-comment");
let currentCommentButton = null;

function getComments(postId) {
    
    $.ajax({
        url: "ajax/comments",
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
    currentCommentButton = $('#button-section-' + postId + ' > button');

    $('#post-id-for-comment').val(postId);
    modal.show();
}

$('#send-comment').on('click', function (e) {
    e.preventDefault();
    let testo = $('#comment-textarea').val();
    
    if (testo == '') return;

    let IDpost = $('#post-id-for-comment').val();
    $.ajax({
        url: "ajax/put-comment",
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
            let num = Number(currentCommentButton.text());
            let commentImage = '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-chat-fill" viewBox="0 0 16 16"><path d="M8 15c4.418 0 8-3.134 8-7s-3.582-7-8-7-8 3.134-8 7c0 1.76.743 3.37 1.97 4.6-.097 1.016-.417 2.13-.771 2.966-.079.186.074.394.273.362 2.256-.37 3.597-.938 4.18-1.234A9.06 9.06 0 0 0 8 15z"></path></svg>';
            
            currentCommentButton.html(commentImage + " " + String(num + 1));
            $('#comment-textarea').val('');
        }
    });
});