let modal = new bootstrap.Modal("#modal-comment");
let currentCommentButton = null;

function showReplyForm(commentId) {
    let container = $('#reply-form-container-' + commentId);
    if (container.html() !== "") {
        container.html("");
        return;
    }
    let html = `
        <div class="mt-2 ms-5">
            <textarea id="reply-text-${commentId}" class="form-control" rows="2" placeholder="Scrivi una risposta..."></textarea>
            <button class="btn btn-sm btn-primary mt-2" onclick="sendReply('${commentId}')">Invia Risposta</button>
        </div>
    `;
    container.html(html);
}

function sendReply(parentCommentId) {
    let testo = $('#reply-text-' + parentCommentId).val();
    if (testo == '') return;
    let rootPostId = $('#post-id-for-comment').val();
    $.ajax({
        url: "ajax/put-comment",
        type: 'GET',
        data: {
            text: testo, 
            postID: parentCommentId, 
            rootPostID: rootPostId,
            type: 'Comment'
        },
        dataType: 'json',
        success: function (mydata) {
            let replyHtml = `
                <div class="d-flex align-items-center ms-5 mt-2 border-start ps-3">
                    <div class="flex-shrink-0">
                        <img style="max-width: 40px;" src="${mydata.ProfileImage}">
                    </div>
                    <div class="flex-grow-1 ms-3">
                        <h6 class="mt-0">${mydata.User}</h6>
                        <small>${testo}</small>
                    </div>
                </div>`;
            $('#nested-replies-' + parentCommentId).append(replyHtml);
            $('#reply-form-container-' + parentCommentId).html("");

            let num = Number(currentCommentButton.text());
            let commentImage = '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-chat-fill" viewBox="0 0 16 16"><path d="M8 15c4.418 0 8-3.134 8-7s-3.582-7-8-7-8 3.134-8 7c0 1.76.743 3.37 1.97 4.6-.097 1.016-.417 2.13-.771 2.966-.079.186.074.394.273.362 2.256-.37 3.597-.938 4.18-1.234A9.06 9.06 0 0 0 8 15z"></path></svg>';
            currentCommentButton.html(commentImage + " " + String(num + 1));
        }
    });
}

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
        let cID = comments[i].ID;
        html += '<div class="mb-4" id="comment-main-' + cID + '">';
        html += '  <div class="d-flex align-items-center">';
        html += '    <div class="flex-shrink-0"><img class="mr-3" style="max-width: 64px;" src="' + comments[i].ProfileImage + '"></div>';
        html += '    <div class="flex-grow-1 ms-3">';
        html += '      <h5 class="mt-0">' + comments[i].User + '</h5>';
        html += '      <div>' + comments[i].Text + '</div>';
        html += '      <button class="btn btn-sm btn-link p-0" onclick="showReplyForm(\'' + cID + '\')">Rispondi</button>';
        html += '    </div>';
        html += '  </div>';
        html += '  <div id="reply-form-container-' + cID + '"></div>';
        html += '  <div id="nested-replies-' + cID + '">';
        
        if (comments[i].Replies && comments[i].Replies.length > 0) {
            for (var j = 0; j < comments[i].Replies.length; j++) {
                let rep = comments[i].Replies[j];
                html += `
                    <div class="d-flex align-items-center ms-5 mt-2 border-start ps-3">
                        <div class="flex-shrink-0"><img style="max-width: 40px;" src="${rep.ProfileImage}"></div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="mt-0">${rep.User}</h6>
                            <small>${rep.Text}</small>
                        </div>
                    </div>`;
            }
        }
        html += '  </div>';
        html += '</div>';
    }    
    $('#modal-comment-body > div').html(html);}
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