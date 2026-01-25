from cassandra.cluster import Session
from uuid import UUID

def user_exists(cs : Session, user_id):
    row = cs.execute("SELECT * FROM users WHERE ID = %s", (UUID(user_id)))
    
    if row:
        return True
    else:
        return False


def get_user_info(cs: Session, user_id):
    row = cs.execute("SELECT * FROM users WHERE ID = %s", (UUID(user_id),))
    return {
        "id": row.ID,
        "name": row.Nickname,
        "bio": row.Bio,
        "picture": row.ProfileImagePath
    }

def get_user_photo_by_nickname(cs: Session, nickname):
    row = cs.execute("SELECT ProfileImagePath FROM users WHERE Nickname = %s", (nickname,))
    return row.ProfileImagePath

def get_user_id_by_nickname(cs: Session, nickname):
    row = cs.execute("SELECT ID FROM users WHERE Nickname = %s", (nickname,))
    return row.ID

def is_post_liked_by(cs: Session, post_id, user_id):
    row = cs.execute("SELECT * FROM likes WHERE User = %s AND Post = %s", (UUID(user_id), UUID(post_id)))
    if row:
        return True
    else:
        return False
    
def get_users_posts(cs : Session, nickname : str):
    result = cs.execute("SELECT ID, Creator, Titolo, Testo, Likes, Comments, PathToFile, MediaType FROM post WHERE Creator = %s", (nickname))
    return [row._asdict() for row in result]