from flask import session, request
from cassandra.cluster import Session
import uuid
from os import path

USER_ID_IN_SESSION = "userID"

def create_session(user_id):
    session[USER_ID_IN_SESSION] = user_id

def try_login_cookie(conn : Session):

    token = request.cookies.get("login_token")
    if not token:
        return False

    row = conn.execute("SELECT * FROM cookies WHERE IDToken = ?", (token))
    
    if not row:
        return False

    session[USER_ID_IN_SESSION] = row.UserID
    return True

def is_user_logged_in(db_connection : Session, login_if_cookie_exists=False):

    # === Se già in sessione ===
    if USER_ID_IN_SESSION in session:
        return True

    # === Se dobbiamo provare con cookie ===
    if login_if_cookie_exists:

        if try_login_cookie(db_connection):
            return True

    return False

def save_image(file):
    UPLOAD_FOLDER = "static/uploads"
    try:
        img = Image.open(file)
        ext = file.filename.split(".")[-1]
        filename = uuid.uuid4().hex + "." + ext
        path = path.join(UPLOAD_FOLDER, filename)
        img.save(path)
        return "/" + path.replace("\\", "/")
    except:
        return False