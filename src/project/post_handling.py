from cassandra.cluster import Session as CassandraSession
from uuid import UUID

def get_all_post_of_followed_subreddit_count(cs : CassandraSession, user_id):
    
    if type(user_id) is str:
        user_id = UUID(user_id)
    
    subs = cs.execute("SELECT Subreddit FROM following WHERE User = %s", (user_id,))
    
    count = 0
    for sub in subs :
        posts = cs.execute("SELECT ID FROM post WHERE Subreddit = %s ALLOW FILTERING", (sub.subreddit,))
        count += sum(1 for _ in posts)
    
    return count


def get_all_post_by_content_count(cs : CassandraSession, query):
    rows = cs.execute("SELECT * FROM post WHERE Titolo CONTAINS %s OR Testo CONTAINS %s", (query,query,))
    count = sum(1 for _ in rows)
    return count


def get_post_likes_count(cs : CassandraSession, post_id):
    
    if type(post_id) is str:
        post_id = UUID(post_id)
    
    row = cs.execute("SELECT likes FROM post_like WHERE post = %s", (post_id,))
    if row:
        return row[0].likes
    else:
        return 0

def get_post_comments_count(cs : CassandraSession, post_id):
    
    if type(post_id) is str:
        post_id = UUID(post_id)
    
    row = cs.execute("SELECT comments FROM post_comment WHERE post = %s", (post_id,))
    if row:
        return row[0].comments
    else:
        return 0