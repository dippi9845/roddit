from uuid import UUID
def user_exists(db, user_id):
    user = db.users.find_one({"ID": str(user_id)})
    return True if user else False

# Reiterato con Gemini per errore causato dalla route profile(Pagina vuota)
#def get_user_info(db, user_id):
#    # Recuperiamo le informazioni dell'utente
#    user = db.users.find_one({"ID": str(user_id)})
#    if not user:
#        return None
#    return {
#        "id": str(user['ID']),
#        "name": user['Nickname'],
#        "bio": user.get('Bio', ""), # .get evita errori se il campo bio non è presente
#        "picture": user['ProfileImagePath']
#    }
# Versione fatta con Gemini in Quick (eventuale source di problemi [Causa errori nel get profile])
def get_user_info(db, identifier):
    user = db.users.find_one({
        "$or": [
            {"ID": str(identifier)},
            {"Nickname": str(identifier)}
        ]
    })
    if not user:
        return None
    return {
        "id": str(user['ID']),
        "name": user['Nickname'],
        "bio": user.get('Bio', ""),
        "picture": user.get('ProfileImagePath', "/static/uploads/images/default_profile_picture.jpg")
    }

def get_user_photo_by_nickname(db, nickname):
    user = db.users.find_one({"Nickname": nickname}, {"ProfileImagePath": 1})
    return user['ProfileImagePath'] if user else '/static/uploads/images/default_profile_picture.jpg'

def get_user_id_by_nickname(db, nickname):
    user = db.users.find_one({"Nickname": nickname}, {"ID": 1})
    return str(user['ID']) if user else None

def is_post_liked_by(db, post_id, user_id):
    like = db.likes.find_one({
        "User": str(user_id), 
        "Post": str(post_id)
    })
    return True if like else False
    

def get_users_posts(db, nickname: str):
    cursor = db.post.find({"Creator": nickname})
    rtr = []
    for row in cursor:
        post_id = str(row['ID'])
        tmp = {
            "id": post_id,
            "subreddit": row['Subreddit'],
            "creator": row['Creator'],
            "titolo": row['Titolo'],
            "testo": row['Testo'],
            "pathtofile": row.get('PathToFile'),
            "mediatype": row.get('MediaType')
        }
        tmp["likes"] = db.post_like.count_documents({"post": post_id})
        tmp["comments"] = db.post_comment.count_documents({"post": post_id})
        rtr.append(tmp)
    return rtr