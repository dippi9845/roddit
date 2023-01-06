/**
 * This function is called when the page is loaded.
 * It loads the first few posts and then it waits for the user to scroll down to load more posts.
 */
$(document).ready( function() {
    window.lastPostVisualized = 0;
    window.postsPerRequest = 5;

    let query = getUrlVars()['query'];

    posts = ajaxLoadPosts("/html-snippets/post-drawer.php", query, window.lastPostVisualized, window.postsPerRequest);

    window.lastPostVisualized += window.postsPerRequest;

    $("#posts-container").append(posts);
});

/**
 * This function is called when the user scrolls to the bottom of the page.
 * It loads more posts if the user has reached the bottom of the page.
 */
$(window).scroll(function() {
    if($(window).scrollTop() + $(window).height() != $(document).height()) {
        return;
    }
    let query = getUrlVars()['query'];
    const postCount = ajaxGetRawOutput("/profile/get-posts-count.php", query);
    if (window.lastPostVisualized >= postCount) {
        return;
    }
    posts = ajaxLoadCards("/html-snippets/post-drawer.php", query, window.visualizedPostCount, window.cardsPerRequest);
    $("#posts-container").append(posts);

    window.lastPostVisualized += window.postsPerRequest;
 });

 /**
  * This function is called when the page is loaded.
  * It scrolls the page to the top.
  */
 $(document).ready(function(){
    $(this).scrollTop(0);
});

/**
 * This function returns the output of a php file given a query input.
 * @param {string} phpUrl the url of the php file that will be called
 * @param {string} queryData the query data that will be sent to the php file
 * @returns a string containing the output of the php file
 */
function ajaxGetRawOutput(phpUrl, queryData) {
    return $.ajax({
        url: phpUrl,
        type: "POST",
        data: {query: queryData},
        async: false
    }).responseText;
}

/**
 * This function creates all the cards
 * @param {string} phpUrl the url of the php file that will be called
 * @param {string} queryData the query data that will be sent to the php file
 * @param {int} offset the last post that was loaded
 * @param {int} perPage the number of posts that will be loaded
 * @returns a string containing the cards
 */
function ajaxLoadCards(phpUrl, queryData, offset, perPage) {
    return $.ajax({
        url: phpUrl,
        type: "POST",
        data: {query: queryData, offset: offset, limit: perPage},
        async: false
    }).responseText;
}

/**
 * This function returns an array containing all the get variables.
 * @returns an array containing all the get variables
 */
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
