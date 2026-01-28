/**
 * This function is called when the page is loaded.
 * It loads the first few posts and then it waits for the user to scroll down to load more posts.
 */
$(document).ready( function() {
    window.visualizedPostCount = 0;
    window.visualizedUserCount = 0;
    window.cardsPerRequest = 5;
    let query = getUrlVars()['query'];
    window.searchedUsersCount = ajaxGetRawOutput("/ajax/get-users-count", query);

    cards = "";

    $.getScript('/static/assets/js/btn-ajax-form.js');

    if (window.searchedUsersCount > 0) {
        cards += ajaxLoadCards("/html-snippets/user-card-drawer", query, window.visualizedUserCount, window.cardsPerRequest);
        
        window.visualizedUserCount += window.searchedUsersCount;
    }
    
    if (window.visualizedUserCount <= window.cardsPerRequest) {
        cards += ajaxLoadCards("/html-snippets/post-drawer", query, window.visualizedPostCount, window.cardsPerRequest-window.visualizedUserCount);

        window.visualizedPostCount += window.cardsPerRequest;
    }
    
    if (cards == "") {
        cards = "<h1>No content found</h1>";
    }

    $("#posts-container").append(cards);
});

/**
 * This function is called when the user scrolls to the bottom of the page.
 * It loads more posts if the user has reached the bottom of the page.
 */
$(window).scroll(function() {
    let windowHeight = $(document).height();
    let windowPosition = $(window).scrollTop() + $(window).height();
    if(windowPosition < windowHeight * 0.7 ) {
        return;
    }
    let query = getUrlVars()['query'];
    const postCount = ajaxGetRawOutput("/ajax/get-posts-count", query);
    const userCount = ajaxGetRawOutput("/ajax/get-users-count", query);

    cards = "";

    if (window.visualizedUserCount < userCount) {
        cards += ajaxLoadCards("/html-snippets/user-card-drawer", query, window.visualizedUserCount, window.cardsPerRequest);

        window.visualizedUserCount += window.cardsPerRequest;
    }

    if (window.visualizedPostCount < postCount) {
        cards += ajaxLoadCards("/html-snippets/post-drawer", query, window.visualizedPostCount, window.cardsPerRequest);

        window.visualizedPostCount += window.cardsPerRequest;
    }

    if (cards != "") {
        $("#posts-container").append(cards);
    }
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
        data: {query: queryData, limit: perPage},
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
