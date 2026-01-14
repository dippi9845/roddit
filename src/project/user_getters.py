from cassandra.cluster import Session

def user_exists(cs : Session, user_id):
    row = cs.execute("SELECT * FROM users WHERE ID = ?", (user_id))
    
    if row:
        return True
    else:
        return False


def get_user_info(cs: Session, user_id):
    row = cs.execute("SELECT * FROM users WHERE ID = ?", user_id)
    return {
        "id": row.ID,
        "name": row.Nickname,
        "bio": row.Bio,
        "picture": row.ProfileImagePath
    }