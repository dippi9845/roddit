from flask import session, request

def get_db():
    #TODO to implement with a database
    pass

def try_login_cookie(conn):

    token = request.cookies.get("login_token")
    if not token:
        return False

    pass # TODO check che del cookie nel database

    #TODO check if the cookis is present
    #if not row:
    #    return False

    # TODO Assign the database id to session id
    #session["userID"] = row["user_id"]
    return True

def is_user_logged_in(force_cookie_login=False):

    # === Se già in sessione ===
    if "userID" in session:
        return True

    # === Se dobbiamo provare con cookie ===
    if force_cookie_login:


        conn = ...

        if try_login_cookie(conn):
            conn.close()
            return True

        conn.close()

    return False