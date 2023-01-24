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
            $('#modal-comment-body').html(html);
        }
    });
    modal.show();
}