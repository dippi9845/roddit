from flask import session, request, make_response
from cassandra.cluster import Session
import uuid
from os import path
from datetime import datetime, timedelta, timezone


USER_ID_IN_SESSION = "userID"
USER_ID_IN_COOKIE = USER_ID_IN_SESSION


def create_session(user_id):
    session[USER_ID_IN_SESSION] = user_id


def create_cookie(user_id):
    expiration_time = datetime.now(timezone.utc) + timedelta(days=15)
    response = make_response()
    
    response.set_cookie(
        USER_ID_IN_COOKIE,
        user_id,
        expires=expiration_time,
        path="/"
    )
    


def try_login_cookie():

    token = request.cookies.get(USER_ID_IN_COOKIE)
    if not token:
        return False

    session[USER_ID_IN_SESSION] = token
    return True


def is_user_logged_in(login_if_cookie_exists=False):


    if USER_ID_IN_SESSION in session:
        return True

    if login_if_cookie_exists:

        if try_login_cookie():
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