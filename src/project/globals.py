from flask import session, request
from cassandra.cluster import Session

def get_db():
    #TODO to implement with a database
    pass

def try_login_cookie(conn : Session):

    token = request.cookies.get("login_token")
    if not token:
        return False

    row = conn.execute("SELECT * FROM cookies WHERE IDToken = ?", (token))
    
    if not row:
        return False

    session["userID"] = row.UserID
    return True

def is_user_logged_in(db_connection : Session, login_if_cookie_exists=False):

    # === Se già in sessione ===
    if "userID" in session:
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
        path = os.path.join(UPLOAD_FOLDER, filename)
        img.save(path)
        return "/" + path.replace("\\", "/")
    except:
        return False