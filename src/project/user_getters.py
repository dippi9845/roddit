from cassandra.cluster import Session
from uuid import UUID

def user_exists(cs : Session, user_id):
    row = cs.execute("SELECT * FROM users WHERE ID = %s", (UUID(user_id),))
    
    if row:
        return True
    else:
        return False


def get_user_info(cs: Session, user_id):
    row = cs.execute("SELECT * FROM users WHERE ID = %s", (UUID(user_id),))
    return {
        "id": row[0].id,
        "name": row[0].nickname,
        "bio": row[0].bio,
        "picture": row[0].profileimagepath
    }

def get_user_photo_by_nickname(cs: Session, nickname):
    row = cs.execute("SELECT ProfileImagePath FROM users WHERE Nickname = %s ALLOW FILTERING", (nickname,))
    return row[0].profileimagepath

def get_user_id_by_nickname(cs: Session, nickname):
    row = cs.execute("SELECT ID FROM users WHERE Nickname = %s ALLOW FILTERING", (nickname,))
    return row[0].id

def is_post_liked_by(cs: Session, post_id, user_id):
    
    if type(user_id) is str:
        user_id = UUID(user_id)
    
    if type(post_id) is str:
        post_id = UUID(post_id)

    row = cs.execute("SELECT * FROM likes WHERE User = %s AND Post = %s ALLOW FILTERING", (user_id, post_id,))
    if row:
        return True
    else:
        return False
    
def get_users_posts(cs : Session, nickname : str):
    result = cs.execute("SELECT ID, Subreddit, Creator, Titolo, Testo, PathToFile, MediaType FROM post WHERE Creator = %s ALLOW FILTERING", (nickname,))
    rtr = []
    for row in result:
        tmp = row._asdict()
        likes = cs.execute("SELECT likes FROM post_like WHERE post = %s", (row.id,))
        comments = cs.execute("SELECT comments FROM post_comment WHERE post = %s", (row.id,))
        tmp["likes"] = likes[0].likes if likes else 0
        tmp["comments"] = comments[0].comments if comments else 0
        rtr.append(tmp)
    return rtr