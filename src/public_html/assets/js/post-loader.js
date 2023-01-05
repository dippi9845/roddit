let lastPostVisualized = 0;
const postsPerRequest = 5;

$(document).ready( function() {
    let query = getUrlVars()['query'];

    posts = ajaxLoadPosts("/html-snippets/post-drawer.php", query, lastPostVisualized, postsPerRequest);

    lastPostVisualized += postsPerRequest;

    $("#posts-container").append(posts);
});

$(window).scroll(function() {
    if($(window).scrollTop() + $(window).height() != $(document).height()) {
        return;
    }
    let query = getUrlVars()['query'];
    const postsID = ajaxGetPostsCount("/profile/get-posts-count.php", query);
    if (lastPostVisualized >= postsID) {
        return;
    }
    posts = ajaxLoadPosts("/html-snippets/post-drawer.php", query, lastPostVisualized, postsPerRequest);
    $("#posts-container").append(posts);

    lastPostVisualized += postsPerRequest;
 });

 $(document).ready(function(){
    $(this).scrollTop(0);
});

function ajaxGetPostsCount(phpUrl, queryData) {
    return $.ajax({
        url: phpUrl,
        type: "POST",
        data: {query: queryData},
        async: false
    }).responseText;
}

function ajaxLoadPosts(phpUrl, queryData, offset, perPage) {
    return $.ajax({
        url: phpUrl,
        type: "POST",
        data: {query: queryData, offset: offset, limit: perPage},
        async: false
    }).responseText;
}

function getUrlVars() {
    let vars = [], hash;
    let hashes = window.location.href.slice(window.location.href.indexOf('?') + 1).split('&');
    for(let i = 0; i < hashes.length; i++) {
        hash = hashes[i].split('=');
        vars.push(hash[0]);
        vars[hash[0]] = hash[1];
    }
    return vars;
}
