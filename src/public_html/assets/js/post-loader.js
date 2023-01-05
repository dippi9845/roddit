$(document).ready( function() {
    var query = getUrlVars()['query'];
    posts = ajaxLoadPosts("/html-snippets/post-drawer.php", query, 0, 2);

    $("#posts-container").append(posts);
});

function ajaxLoadPosts(phpUrl, queryData, offset, perPage) {
    return $.ajax({
        url: phpUrl,
        type: "POST",
        data: {query: queryData, offset: offset, limit: perPage},
        async: false
    }).responseText;
}

function getUrlVars() {
    var vars = [], hash;
    var hashes = window.location.href.slice(window.location.href.indexOf('?') + 1).split('&');
    for(var i = 0; i < hashes.length; i++) {
        hash = hashes[i].split('=');
        vars.push(hash[0]);
        vars[hash[0]] = hash[1];
    }
    return vars;
}
