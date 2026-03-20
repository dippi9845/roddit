from datetime import datetime, timezone
import uuid

def notify_user(db, user_id, titolo, messaggio):
    notification = {
        "ID": str(uuid.uuid4()),
        "UserID": str(user_id),
        "Titolo": titolo,
        "Testo": messaggio,
        "Inserimento": datetime.now(timezone.utc) 
    }
    db.notification.insert_one(notification)
    
def get_post_creator(db, post_id):
    post = db.post.find_one({"ID": str(post_id)}, {"Creator": 1})
    if not post:
        return None
    user = db.users.find_one({"Nickname": post['Creator']}, {"ID": 1})
    if not user:
        return None
    return str(user['ID'])