from cassandra.cluster import Session as CassandraSession

def get_all_post_of_followed_subreddit_count(cs : CassandraSession, user_id):
    subs = cs.execute("SELECT Subreddit FROM following WHERE User = %s", (user_id,))
    
    count = 0
    for sub in subs :
        posts = cs.execute("SELECT ID FROM posts WHERE subreddit = %s", (sub,))
        count += sum(1 for _ in posts)
    
    return count


def get_all_post_by_content_count(cs : CassandraSession, query):
    rows = cs.execute("SELECT * FROM post WHERE Titolo CONTAINS %s OR Testo CONTAINS %s", (query,query,))
    count = sum(1 for _ in rows)
    return count