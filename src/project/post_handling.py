def get_all_post_of_followed_subreddit_count(db, user_id):
    subs_cursor = db.following.find({"User": str(user_id)}, {"Subreddit": 1})
    subs_list = [doc['Subreddit'] for doc in subs_cursor]
    if not subs_list:
        return 0
    count = db.post.count_documents({"Subreddit": {"$in": subs_list}})
    return count

def get_all_post_by_content_count(db, query):
    search_filter = {
        "$or": [
            {"Titolo": {"$regex": query, "$options": "i"}}, 
            {"Testo": {"$regex": query, "$options": "i"}}
        ]
    }
    count = db.post.count_documents(search_filter)
    return count

# Non sono convinto che la conversione di questa query sia corretta, da ricontrollare.
def get_post_likes_count(db, post_id):
    count = db.post_like.count_documents({"post": str(post_id)})
    return count

def get_post_comments_count(db, post_id):
    count = db.post_comment.count_documents({"post": str(post_id)})
    return count