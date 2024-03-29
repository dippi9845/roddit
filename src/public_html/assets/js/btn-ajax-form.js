/**
 * This script transforms a button into an ajax form.
 */


$(".btn-ajax-form-like").click(function() {
    let button = $(this);
    
    $("#" + $(this).closest("form").attr('id')).ajaxForm({
        success: function() {
            let redHeart = '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" class="bi bi-heart-fill red-heart" viewBox="0 0 16 16"><path d="M8 1.314C12.438-3.248 23.534 4.735 8 15-7.534 4.736 3.562-3.248 8 1.314z"></path></svg>';
            let blankHeart = '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-heart" viewBox="0 0 16 16"><path d="m8 2.748-.717-.737C5.6.281 2.514.878 1.4 3.053c-.523 1.023-.641 2.5.314 4.385.92 1.815 2.834 3.989 6.286 6.357 3.452-2.368 5.365-4.542 6.286-6.357.955-1.886.838-3.362.314-4.385C13.486.878 10.4.28 8.717 2.01L8 2.748zM8 15C-7.333 4.868 3.279-3.04 7.824 1.143c.06.055.119.112.176.171a3.12 3.12 0 0 1 .176-.17C12.72-3.042 23.333 4.867 8 15z"/></svg>';
            
            if (button.children(":first").attr("class").includes("red-heart")) {
                // mette like
                let n = Number(button.text());
                button.html(blankHeart + " " + String(n - 1));
                button.closest("form").attr('action', 'ajax/like-post.php');
                
            }
            else {
                // toglie like
                let n = Number(button.text());
                button.html(redHeart + " " + String(n + 1));
                button.closest("form").attr('action', 'ajax/dislike-post.php');
            }
        }
    });
});

$(".btn-ajax-form").click(function() {
    $("#" + $(this).closest("form").attr('id')).ajaxForm({success: function() {
        location.reload();
    }});
});
