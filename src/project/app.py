import email
from flask import Flask, render_template, render_template_string, request, redirect, session, make_response, jsonify
import os
from globals import *
from user_getters import *
import re
import html
from hashlib import sha256
from post_handling import *
from notify import *
from time import time, sleep
from urllib.parse import unquote_plus
from pymongo import MongoClient
from bson import uuid as bson_uuid 
import uuid

from datetime import datetime, timezone

mongo_uri = os.getenv("MONGO_URI", "mongodb://flask:password123@mongodb:27017/roddit")
client = MongoClient(mongo_uri)
db = client.roddit 

cassandra_host = os.getenv("CASSANDRA_HOST", "localhost")
cassandra_port = int(os.getenv("CASSANDRA_PORT", 9042))

cassandra_session = client.roddit

app = Flask(__name__)
app.secret_key = "RODDIT_SOCIAL_MEDIA_123"

@app.route("/")
def index():

    if not is_user_logged_in(True):
        return redirect("/login")
    
    query = request.args.get("query", "")

    return render_template("index.html", query=query)

#@app.route("/profile")
#def profile():
#    if not is_user_logged_in(True):
#        return redirect("/login")
#    visited_user = request.args.get("user", session[USER_ID_IN_SESSION])
#    if not user_exists(cassandra_session, visited_user):
#        return redirect("/404")
#    profile_data = get_user_info(cassandra_session, visited_user)
#    posts = get_users_posts(cassandra_session, profile_data['name'])
#    for p in posts:
#        p["liked"] = is_post_liked_by(cassandra_session, p["id"], session[USER_ID_IN_SESSION])
#    return render_template("profile.html", profile=profile_data, posts=posts)
# Versione fatta con Gemini in Quick (eventuale source di problemi [Causa errori nel get profile])
@app.route("/profile")
def profile():
    if not is_user_logged_in(True):
        return redirect("/login")
    current_user_id = session[USER_ID_IN_SESSION]
    visited_user_query = request.args.get("user", current_user_id)
    profile_data = get_user_info(db, visited_user_query)
    if not profile_data:
        return redirect("/404")
    posts = get_users_posts(db, profile_data['name'])
    for p in posts:
        p_id = p.get("ID") or str(p.get("_id"))
        p["liked"] = is_post_liked_by(db, p_id, current_user_id)
    return render_template("profile.html", profile=profile_data, posts=posts)

@app.route("/login")
def login():
    if is_user_logged_in(True):
        return redirect("/")
    return render_template("login.html")

@app.route("/new-post")
def new_post():
    if not is_user_logged_in(True):
        return redirect("/login")
    return render_template("new-post.html")

@app.route("/registration", methods=["GET", "POST"])
def registration():
    err = False
    text_err = ""
    form = {
        "nickname": "",
        "email": "",
        "password": "",
        "pass_conf": ""
    }
    if request.method == "POST":
        form["nickname"] = request.form.get("nickname", "")
        form["email"] = request.form.get("email", "")
        form["password"] = request.form.get("password", "")
        form["pass_conf"] = request.form.get("pass_conf", "")
        if "first" in request.form:
            # Privacy policy
            if request.form.get("privacy-policy") != "accept":
                err = True
                text_err = "You must accept the privacy policy"
            # Terms
            elif request.form.get("terms-conditions") != "accept":
                err = True
                text_err = "You must accept the terms and conditions"
            else:
                # Controllo campi
                if not all([form["nickname"], form["email"], form["password"], form["pass_conf"]]):
                    err = True
                    text_err = "You must fill all the fields"
                elif form["password"] != form["pass_conf"]:
                    err = True
                    text_err = "Two passwords are different"
                elif not re.match(r"[^@]+@[^@]+\.[^@]+", form["email"]):
                    err = True
                    text_err = "Email provided is not a valid email"
                elif len(form["nickname"]) > 64:
                    err = True
                    text_err = "Nickname provided is too long, (more than 64 characters)"
                elif not re.match(r"^[0-9a-zA-Z_]+$", form["nickname"]):
                    err = True
                    text_err = "Nickname provided is not valid, (only numbers, letters and underscore)"

                if db.users.find_one({"Nickname": form["nickname"]}):
                    err = True
                    text_err = "Nickname already taken"
                elif db.users.find_one({"Email": form["email"]}):
                    err = True
                    text_err = "Email already registered"
                if not err:
                    nickname = html.escape(form["nickname"])
                    email = html.escape(form["email"])
                    salt = uuid.uuid4().hex
                    password_hash = sha256(form["password"].encode() + salt.encode()).hexdigest()
                    new_user = {
                        "ID": str(uuid.uuid4()),
                        "Nickname": nickname,
                        "Email": email,
                        "Password": password_hash,
                        "Salt": salt,
                        "ProfileImagePath": '/static/uploads/images/default_profile_picture.jpg'
                    }

                    db.users.insert_one(new_user)
                    return redirect("/login")
    return render_template("register.html", err=err, text_err=text_err, form=form)

@app.route("/settings", methods=["GET", "POST"])
def settings():
    if USER_ID_IN_SESSION not in session:
        return redirect("/login")
    err = False
    text_err = ""
    user_id = str(session[USER_ID_IN_SESSION])

    # -------------------------
    # Cambio email
    # -------------------------
    new_email = request.form.get("new-email")
    if new_email:
        if re.match(r"[^@]+@[^@]+\.[^@]+", new_email):
            new_email = html.escape(new_email)
            db.users.update_one(
                {"ID": user_id},
                {"$set": {"Email": new_email}}
            )
        else:
            err = True
            text_err = "Email provided is not a valid email"

    # -------------------------
    # Cambio nickname
    # -------------------------
    new_nickname = request.form.get("new-nickname")
    if new_nickname:
        if len(new_nickname) > 64:
            err = True
            text_err = "Nickname provided is too long (more than 64 characters)"
        elif not re.match(r"^[0-9a-zA-Z_]+$", new_nickname):
            err = True
            text_err = "Nickname not valid (only numbers, letters and underscore)"
        else:
            new_nickname = html.escape(new_nickname)
            existing_user = db.users.find_one({"Nickname": new_nickname})
            if existing_user and str(existing_user['ID']) != user_id:
                err = True
                text_err = "Nickname already taken"
            else:    
                db.users.update_one(
                    {"ID": user_id},
                    {"$set": {"Nickname": new_nickname}}
                )

    # -------------------------
    # Cambio password
    # -------------------------
    new_password = request.form.get("new-password")
    confirm_pass = request.form.get("confirm-new-pass")
    if new_password and confirm_pass:
        if new_password != confirm_pass:
            err = True
            text_err = "Two passwords are different"
        else:
            salt = uuid.uuid4().hex
            password_hash = sha256(new_password.encode() + salt.encode()).hexdigest()
            db.users.update_one(
                {"ID": user_id},
                {"$set": {"Password": password_hash, "Salt": salt}}
            )

    # -------------------------
    # Cambio bio
    # -------------------------
    new_bio = request.form.get("new-biography")
    if new_bio:
        new_bio = html.escape(new_bio)
        db.users.update_one(
            {"ID": user_id},
            {"$set": {"Bio": new_bio}}
        )

    # -------------------------
    # Cambio immagine
    # -------------------------
    if "new-photo" in request.files:
        file = request.files["new-photo"]
        if file and file.filename != "":
            ext = file.filename.rsplit(".", 1)[1].lower()
            filename = f"{uuid.uuid4()}.{ext}"
            upload_dir = os.path.join(os.getcwd(), "src", "project", "static", "uploads", "images")
            os.makedirs(upload_dir, exist_ok=True)
            file_path = os.path.join(upload_dir, filename)
            file.save(file_path)
            db_path = "/static/uploads/images/" + filename
            db.users.update_one(
                {"ID": user_id},
                {"$set": {"ProfileImagePath": db_path}}
            )

    # -------------------------
    # Recupera dati utente aggiornati
    # -------------------------
    data = db.users.find_one({"ID": user_id})
    if not data:
        return redirect("/logout")
    return render_template(
        "settings.html",
        user={
            "photo": data.get("ProfileImagePath", ""),
            "email": data.get("Email", ""),
            "nickname": data.get("Nickname", ""),
            "bio": data.get("Bio", "")
        },
        err=err,
        text_err=text_err
    )

@app.route("/new-subreddit")
def create_new_subreddit():
    return render_template("new-subreddit.html")

@app.route("/404")
def not_found():
    return render_template("404.html")

@app.route("/ajax/follow", methods=["GET"])
def ajax_follow():
    if USER_ID_IN_SESSION in session:
        user_id = str(session[USER_ID_IN_SESSION])
        subreddit_name = request.args.get("subreddit")
        if subreddit_name:
            db.following.update_one(
                {"User": user_id, "Subreddit": subreddit_name},
                {"$set": {"User": user_id, "Subreddit": subreddit_name}},
                upsert=True
            )
            db.subreddit.update_one(
                {"Name": subreddit_name},
                {"$inc": {"Followers": 1}}
            )
    return jsonify({"status": "ok"})

@app.route("/ajax/create-new-post", methods=["POST"])
def ajax_create_new_post():
    title = request.form.get("title", "")
    text = request.form.get("text", "")
    subreddit = request.form.get("subreddit", "")
    db_path = None 

    if title == "" or subreddit == "":
        return redirect("/new-post")
    
    if "file" in request.files and request.files["file"].filename != "":
        file = request.files["file"]
        ext = file.filename.rsplit(".", 1)[1].lower()
        filename = f"{uuid.uuid4()}.{ext}"
        upload_dir = os.path.join(os.getcwd(), "src", "project", "static", "uploads", "images")
        os.makedirs(upload_dir, exist_ok=True)
        file_path = os.path.join(upload_dir, filename)
        file.save(file_path)
        db_path = "/static/uploads/images/" + filename

    sub_exists = db.subreddit.find_one({"Name": subreddit})
    if not sub_exists:
        return redirect("/new-post")
    
    user_info = get_user_info(db, session[USER_ID_IN_SESSION])
    if not user_info:
        return redirect("/login")
    
    post_id = str(uuid.uuid4()) 
    post_doc = {
        "ID": post_id,
        "Subreddit": subreddit,
        "Creator": user_info["name"],
        "Titolo": html.escape(title),
        "Testo": html.escape(text),
        "PathToFile": db_path,
        "Creazione": datetime.now(timezone.utc)
    }
    db.post.insert_one(post_doc)
    db.post_like.insert_one({"post": post_id, "likes": 0})
    db.post_comment.insert_one({"post": post_id, "comments": 0})
    return redirect("/")

@app.route("/ajax/create-new-subreddit", methods=["POST"])
def ajax_create_new_subreddit():
    subreddit_name = request.form.get("subreddit", "").strip()

    if subreddit_name != "":
        existing_sub = db.subreddit.find_one({"Name": subreddit_name})
        if existing_sub:
            return redirect("/new-subreddit")
        
        db.subreddit.insert_one({
            "Name": subreddit_name,
            "Followers": 0,
            "Creator": session[USER_ID_IN_SESSION] 
        })
        user_id = str(session[USER_ID_IN_SESSION])
        db.following.insert_one({
            "User": user_id,
            "Subreddit": subreddit_name
        })
        db.subreddit.update_one(
            {"Name": subreddit_name},
            {"$inc": {"Followers": 1}}
        )
        return redirect("/")
    else:
        return redirect("/new-subreddit")

@app.route("/ajax/login", methods=["POST"])
def ajax_login():
    def is_form_valid():
        return 'email' in request.form and 'password' in request.form
    
    def get_user_id(email, password):
        user = db.users.find_one({"Email": email})
        if not user:
            return None
        
        salt = user['Salt']
        password_hash = sha256(password.encode() + salt.encode()).hexdigest()
        valid_user = db.users.find_one({"Email": email, "Password": password_hash})
        if not valid_user:
            return None
        return str(valid_user['ID'])
    
    def main():
        if not is_form_valid():
            print("Invalid form")
            return False, None
        
        user_id = str(get_user_id(request.form['email'], request.form['password']))
        if not user_id:
            print("Invalid credentials")
            return False, None
        
        create_session(user_id)
        response = make_response()
        if 'remember' in request.form:
            create_cookie(user_id)
        return True, response
    
    ok, response = main()
    if ok:
        response.headers["Location"] = "/"
        response.status_code = 302
        return response
    else:
        return redirect("/login")

@app.route("/ajax/get-posts-count", methods=["POST"])
def ajax_get_posts_count():
    if not is_user_logged_in(True):
        return redirect("/login") 
    
    query = request.args.get("query", "")
    user_id = session.get(USER_ID_IN_SESSION)
    if query == "":
        post_count = get_all_post_of_followed_subreddit_count(db, user_id)
    else:
        post_count = get_all_post_by_content_count(db, query)
    return str(post_count)

@app.route("/ajax/get-users-count", methods=["POST"])
def ajax_get_users_count():
    if not is_user_logged_in(True):
        redirect("/login")
    query = request.form.get("query", "")
    user_count = 0
    if query != "":
        user_count = get_all_searched_users_count(cassandra_session, query)
    return str(user_count)

@app.route("/ajax/like-post", methods=["POST"])
def ajax_like_post():
    post_id = request.form.get("post_id")
    user_id = str(session.get(USER_ID_IN_SESSION))
    db.likes.update_one(
        {"User": user_id, "Post": post_id},
        {"$set": {"User": user_id, "Post": post_id}},
        upsert=True
    )

    db.post_like.update_one(
        {"post": post_id},
        {"$inc": {"likes": 1}},
        upsert=True
    )
    
    creator_id = get_post_creator(db, post_id)
    if creator_id:
        notify_user(db, creator_id, "New like", "A user liked your post")
        
    return jsonify({"status": "ok"})

@app.route("/ajax/logout")
def ajax_logout():
    session.clear()
    response = make_response()
    response.set_cookie(
        USER_ID_IN_COOKIE,
        "",
        expires=0,
        path="/"
    )
    return redirect("/login")
@app.route("/ajax/put-comment", methods=["GET"])
def ajax_put_comment():
    if USER_ID_IN_SESSION in session and "text" in request.args and "postID" in request.args:
        user_id = str(session[USER_ID_IN_SESSION])
        user_info = get_user_info(db, user_id)
        if not user_info:
            return jsonify({"Error": "User not found"}), 404
        
        testo = html.escape(request.args["text"])
        target_id = str(request.args["postID"])
        entity_type = request.args.get("type", "Post") 
        root_post_id = str(request.args.get("rootPostID", target_id))
        new_comment_id = str(uuid.uuid4())
        db.comment.insert_one({
            "ID": new_comment_id,
            "User": user_info["name"],
            "Testo": testo,
            "entityType": entity_type,
            "entityID": target_id
        })
        db.post_comment.update_one(
            {"post": root_post_id},
            {"$inc": {"comments": 1}},
            upsert=True
        )
        user_row = db.users.find_one({"ID": user_id})
        if user_row:
            return jsonify({
                "ProfileImage": user_row.get("ProfileImagePath", "/static/uploads/images/default_profile_picture.jpg"),
                "User": user_row.get("Nickname", "Unknown"),
                "Status": "Success"
            })
    return jsonify({"Error": "Invalid request"}), 400

@app.route("/ajax/comments", methods=["GET"])
def ajax_comments():
    post_id = request.args.get('post', "")
    if post_id == "":
        return jsonify({"Error": "No ID"}), 400
    comment_cursor = db.comment.find({
        "entityType": "Post", 
        "entityID": str(post_id)
    })
    rtr = []
    default_img = "/static/uploads/images/default_profile_picture.jpg"
    for row in comment_cursor:
        user_data = db.users.find_one({"Nickname": row['User']})
        img = user_data.get('ProfileImagePath', default_img) if user_data else default_img
        comment_id = str(row['ID'])
        replies_cursor = db.comment.find({
            "entityType": "Comment", 
            "entityID": comment_id
        })
        replies = []
        for r_row in replies_cursor:
            r_user_data = db.users.find_one({"Nickname": r_row['User']})
            r_img = r_user_data.get('ProfileImagePath', default_img) if r_user_data else default_img
            replies.append({
                "ID": str(r_row['ID']),
                "ProfileImage": r_img,
                "User": r_row['User'],
                "Text": r_row['Testo']
            })
        rtr.append({
            "ID": comment_id,
            "ProfileImage": img,
            "User" : row['User'],
            "Text" : row['Testo'],
            "Replies": replies
        })
    return jsonify(rtr)

@app.route("/ajax/dislike-post", methods=["POST"])
def ajax_dislike_post():
    post_id = request.form.get("post_id", "")
    user_id = str(session.get(USER_ID_IN_SESSION))
    if post_id != "":
        db.likes.delete_one({"User": user_id, "Post": post_id})
        db.post_like.update_one(
            {"post": post_id},
            {"$inc": {"likes": -1}}
        )
    return jsonify({"status": "ok"})

@app.route("/ajax/get-last-notification")
def ajax_get_last_notification():
    row = db.notification.find_one(
        {"Inserimento": {"$lt": datetime.now(timezone.utc)}},
        sort=[("Inserimento", -1)]
    )
    return jsonify({"ID": str(row["ID"])} if row else {"ID": None})

#@app.route("/ajax/get-my-notification")
#def ajax_get_my_notification():
#    # TODO Si potrebbe aggiungere un campo "visto" così da restituire tutte le notifiche non ancora viste
#    offset = int(request.form.get("o", time())) # TODO qua vuole un timestamp non un int
#    dt = datetime.fromtimestamp(offset, timezone.utc)
#    limit = int(request.form.get("n", 5))
#    notifications = cassandra_session.execute("SELECT ID, Titolo, Testo, Inserimento FROM notification WHERE UserID = %s AND Inserimento < %s LIMIT %s ALLOW FILTERING", (UUID(session[USER_ID_IN_SESSION]), dt,limit,))
#    rtr = []
#    for n in notifications:
#        rtr.append({
#            "ID": n.id,
#            "Title": n.titolo,
#            "Message" : n.testo,
#            "Inserimento" : n.inserimento
#        })
#    return jsonify(rtr)
@app.route("/ajax/get-my-notification", methods=["POST"])
def ajax_get_my_notification():
    user_id = str(session.get(USER_ID_IN_SESSION))
    offset = float(request.form.get("o", datetime.now().timestamp())) 
    dt = datetime.fromtimestamp(offset, timezone.utc)
    limit = int(request.form.get("n", 5))
    notifications = db.notification.find({
        "UserID": user_id,
        "Inserimento": {"$lt": dt}
    }).sort("Inserimento", -1).limit(limit)
    rtr = []
    for n in notifications:
        rtr.append({
            "ID": str(n["ID"]),
            "Title": n["Titolo"],
            "Message" : n["Testo"],
            "Inserimento" : n["Inserimento"].timestamp()
        })
    return jsonify(rtr)

@app.route("/ajax/unfollow", methods=["GET"])
def ajax_unfollow():
    if USER_ID_IN_SESSION in session:
        user_id = str(session[USER_ID_IN_SESSION])
        subreddit_name = request.args.get("subreddit")
        db.following.delete_one({
            "User": user_id, 
            "Subreddit": subreddit_name
        })
        db.subreddit.update_one(
            {"Name": subreddit_name},
            {"$inc": {"Followers": -1}}
        )
    return jsonify({"status": "ok"})

@app.route("/html-snippets/post-drawer", methods=["POST"])
def post_drawer():
    query = unquote_plus(request.form.get("query", ""))
    offset = int(request.form.get("offset", time()))
    dt = datetime.fromtimestamp(offset, timezone.utc)
    limit = int(request.form.get("limit", 10))
    posts_data = []
    if query == "":
        following_cursor = db.following.find({"User": session[USER_ID_IN_SESSION]})
        subreddits = [f['Subreddit'] for f in following_cursor]

        rows = db.post.find({
            "Subreddit": {"$in": subreddits},
            "Creazione": {"$lt": dt}
        }).limit(limit).sort("Creazione", -1)
        posts_data = list(rows)
    else:
        rows = db.post.find({
            "$or": [
                {"Titolo": query},
                {"Testo": query}
            ],
            "Creazione": {"$lt": dt}
        }).limit(limit).sort("Creazione", -1)
        posts_data = list(rows)
    posts = []
    for row in posts_data:
        posts.append({
            'id': str(row['ID']), 
            'sub': row['Subreddit'],
            'creator_id': get_user_id_by_nickname(db, row['Creator']),
            'creator_nickname': row['Creator'],
            "ProfilePicture": get_user_photo_by_nickname(db, row['Creator']),
            "titolo": row['Titolo'],
            "testo": row['Testo'],
            "likes": get_post_likes_count(db, row['ID']),
            "liked": is_post_liked_by(db, row['ID'], session[USER_ID_IN_SESSION]),
            "comments": get_post_comments_count(db, row['ID']),
            "file": row.get('PathToFile'),
            "Creazione": row['Creazione']
        })
    template = """
    {% from "components/post.html" import drawPost %}
    {% for p in posts %}
        {{ drawPost( p['id'], p['creator_id'], p['creator_nickname'], p['ProfilePicture'], p['sub'], p['titolo'], p['testo'], p['likes'], p['liked'], p['comments'], p['file']) }}
    {% endfor %}
    """
    return render_template_string(template, posts=posts)

@app.route("/html-snippets/user-card-drawer", methods=["POST"])
def post_user_card_drawer():
    query = request.form.get("query", "")
    rows = cassandra_session.execute(
        "SELECT * FROM users WHERE Nickname = %s ALLOW FILTERING",
        (query,)
    )
    users = [{
        "id": row.ID,
        "nickname": row.Nickname,
        "photo": row.ProfileImagePath,
        "current_uid": session[USER_ID_IN_SESSION]
    } for row in rows]
    template = """
    {% from "components/user.html" import draw_user_card %}
    {% for u in users %}
        {{ draw_user_card( u['id'], u['nickname'], u['photo'], u['current_uid']) }}
    {% endfor %}
    <script src="/static/assets/js/btn-ajax-form.js"></script>
    """
    return render_template_string(template, users=users)
    
if __name__ == "__main__":
    app.run(debug=True)