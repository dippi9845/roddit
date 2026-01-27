from flask import Flask, render_template, render_template_string, request, redirect, session, make_response, jsonify
import os
from globals import *
from user_getters import *
from cassandra.cluster import Cluster, Session as CassandraSession
from cassandra.auth import PlainTextAuthProvider
import re
import html
from hashlib import sha256
from post_handling import *
from notify import *
from time import time


auth_provider = PlainTextAuthProvider(
    username="flask",
    password="cassandra123"
)

cluster = Cluster(["127.0.0.1"], auth_provider=auth_provider)
cassandra_session = cluster.connect("roddit")


app = Flask(__name__)
app.secret_key = "RODDIT_SOCIAL_MEDIA_123"

@app.route("/")
def index():

    if not is_user_logged_in(True):
        return redirect("/login")
    
    query = request.args.get("query", "")

    return render_template("index.html", query=query)

@app.route("/profile")
def profile():
    if not is_user_logged_in(True):
        return redirect("/login")

    visited_user = request.args.get("user", session[USER_ID_IN_SESSION])

    if not user_exists(cassandra_session, visited_user):
        return redirect("/404")

    profile_data = get_user_info(cassandra_session, visited_user)

    posts = get_users_posts(cassandra_session, profile_data['name'])

    for p in posts:
        p["liked"] = is_post_liked_by(cassandra_session, p["ID"], session[USER_ID_IN_SESSION])

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
                
                
                row = cassandra_session.execute("SELECT * FROM users WHERE Nickname = %s ALLOW FILTERING", (form["nickname"],))
                if row:
                    err = True
                    text_err = "Nickname already taken"
                
                row = cassandra_session.execute("SELECT * FROM users WHERE Email = %s ALLOW FILTERING", (form["email"],))
                if row: 
                    err = True
                    text_err = "Email already registered"
                
                if not err:

                    nickname = html.escape(form["nickname"])
                    email = html.escape(form["email"])

                    salt = uuid.uuid4().hex
                    password_hash = sha256(form["password"].encode() + salt.encode()).hexdigest()

                    cassandra_session.execute(
                        "INSERT INTO users (ID, Nickname, Email, Password, Salt, ProfileImagePath) VALUES (uuid(), %s, %s, %s, %s, '/static/uploads/images/default_profile_picture.jpg')",
                        (nickname, email, password_hash, salt)
                    )


                    return redirect("/login")

    return render_template("register.html", err=err, text_err=text_err, form=form)

@app.route("/settings", methods=["GET", "POST"])
def settings():

    if USER_ID_IN_SESSION not in session:
        return redirect("/login")

    err = False
    text_err = ""

    user_id = session[USER_ID_IN_SESSION]

    # -------------------------
    # Cambio email
    # -------------------------
    new_email = request.form.get("new-email")
    if new_email:
        if re.match(r"[^@]+@[^@]+\.[^@]+", new_email):
            new_email = html.escape(new_email)
            cassandra_session.execute(
                "UPDATE users SET Email = %s WHERE ID = %s",
                (new_email, UUID(user_id))
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
            text_err = "Nickname provided is too long, (more than 64 characters)"
        elif not re.match(r"^[0-9a-zA-Z_]+$", new_nickname):
            err = True
            text_err = "Nickname provided is not valid, (only numbers, letters and underscore)"
        else:
            new_nickname = html.escape(new_nickname)
            
            row = cassandra_session.execute(
                "SELECT * FROM users WHERE Nickname = %s",
                (new_nickname,)
            )
            
            if row:
                err = True
                text_err = "Nickname already taken"
            
            else:    
                cassandra_session.execute(
                    "UPDATE users SET Nickname = %s WHERE ID = %s",
                    (new_nickname, UUID(user_id))
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

            cassandra_session.execute(
                "UPDATE users SET Password = %s, Salt = %s WHERE ID = %s",
                (password_hash, salt, UUID(user_id))
            )

    # -------------------------
    # Cambio bio
    # -------------------------
    new_bio = request.form.get("new-biography")
    if new_bio:
        new_bio = html.escape(new_bio)
        cassandra_session.execute(
            "UPDATE users SET Bio=%s WHERE ID=%s",
            (new_bio, UUID(user_id))
        )

    # -------------------------
    # Cambio immagine
    # -------------------------
    if "new-photo" in request.files:
        file = request.files["new-photo"]
        if file and file.filename != "":
            ext = file.filename.rsplit(".", 1)[1].lower()
            filename = f"{uuid.uuid4()}.{ext}"

            upload_dir = os.getcwd() + "/src/project/static/uploads/images"
            os.makedirs(upload_dir, exist_ok=True)
            
            file_path = os.path.join(upload_dir, filename)

            file.save(file_path)

            db_path = "/static/uploads/images/" + filename
            cassandra_session.execute(
                "UPDATE users SET ProfileImagePath = %s WHERE ID = %s",
                (db_path, UUID(user_id))
            )

    # -------------------------
    # Recupera dati utente
    # -------------------------
    data = cassandra_session.execute(
        "SELECT ProfileImagePath, Email, Nickname, Bio FROM users WHERE ID = %s",
        (UUID(user_id),)
    )

    data = data.one()

    return render_template(
    "settings.html",
    user={
        "photo": data.profileimagepath,
        "email": data.email,
        "nickname": data.nickname,
        "bio": data.bio
    },
    err=err,
    text_err=text_err
)

@app.route("/404")
def not_found():
    return render_template("404.html")


@app.route("/ajax/follow", methods=["GET"])
def ajax_follow():
    if USER_ID_IN_SESSION in session:
        cassandra_session.execute("INSERT INTO following (User, Subreddit) VALUES (%s, %s)", (session[USER_ID_IN_SESSION], request.args["subreddit"],))
        cassandra_session.execute("UPDATE subreddit SET Followers = Followers + 1 WHERE Name = %s", (request.args["subreddit"],))

@app.route("/ajax/create-new-post", methods=["POST"])
def ajax_create_new_post():
    
    title = request.form.get("title", "")
    text = request.form.get("text", "")
    subreddit = request.form.get("subreddit", "")
    file_path = ""
    
    if title == "" or subreddit == "":
        return redirect("/new-post")

    if "file" in request.files and request.files["file"].filename != "":
        file = request.files["file"]
        file_path = "/static/uploads/images/" + str(uuid.uuid4()) + file.filename.rsplit(".", 1)[1].lower()
        file.save("." + file_path)
    
    else:
        file_path = None

    row = cassandra_session.execute("SELECT * FROM subreddit WHERE Name = %s", (subreddit,))
    if not row:
        return redirect("/new-post")
    
    user_info = get_user_info(cassandra_session, session[USER_ID_IN_SESSION])

    cassandra_session.execute(
        "INSERT INTO post (ID, Subreddit, Creator, Titolo, Testo, PathToFile, Creazione, Likes, Comments) VALUES (uuid(), %s, %s, %s, %s, %s, toTimestamp(now()), 0, 0)",
        (subreddit, user_info["name"], html.escape(title), html.escape(text), file_path)
    )

    return redirect("/")


@app.route("/ajax/login", methods=["POST"])
def ajax_login():
    
    def is_form_valid():
        return 'email' in request.form and 'password' in request.form

    def get_user_id(email, password):
        salt = cassandra_session.execute("SELECT Salt FROM users WHERE Email = %s  ALLOW FILTERING", (email,))[0].salt
        row = cassandra_session.execute("SELECT ID FROM users WHERE Email = %s AND Password = %s ALLOW FILTERING", (email, sha256(password.encode() + salt.encode()).hexdigest(),))

        if not row:
            return None
        else:
            return row[0].id
    
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


@app.route("/ajax/get-posts-count", methods=["GET"])
def ajax_get_posts_count():
    
    if not is_user_logged_in(True):
        redirect("/login")
    
    query = request.args.get("query", "")

    user_id = session.get(USER_ID_IN_SESSION)

    if query == "":
        post_count = get_all_post_of_followed_subreddit_count(cassandra_session, user_id)
    else:
        post_count = get_all_post_by_content_count(cassandra_session, query)

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
    post_id = request.form.get("postID")
    user_id = session.get(USER_ID_IN_SESSION)
    cassandra_session.execute("INSERT INTO likes (User, Post) VALUES (%s, %s)", (user_id, post_id,))
    cassandra_session.execute("UPDATE post SET Likes = Likes + 1 WHERE ID = %s", (post_id,))
    notify_user(cassandra_session, get_post_creator(cassandra_session, post_id), "New like", "a new user liked your post")


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
        user_info = get_user_info(cassandra_session, session[USER_ID_IN_SESSION])
        testo = html.escape(request.args["text"])
        cassandra_session.execute("INSERT INTO comment (ID, User, Testo, entityType, entityID) VALUES (uuid(), %s, %s, 'Post', %s)", (user_info["name"], testo, request.args["postID"],))
        cassandra_session.execute("UPDATE post SET Comments=Comments + 1 WHERE ID = %s", (request.args["postID"],))
        result = cassandra_session.execute(
            "SELECT ProfileImagePath as ProfileImage, Nickname as User FROM users WHERE ID = %s",
            (session[USER_ID_IN_SESSION],)
        )

        row = result.one()

        return jsonify({
            "ProfileImage": row.profileimage,
            "User": row.user
        })


@app.route("/ajax/comments", methods=["GET"])
def ajax_comments():
    post_id = request.args.get('post', "")
        
    if post_id == "":
        return "{ \"Error\": \"No ID\" }"

    rows = cassandra_session.execute("""
        SELECT User, Testo
        FROM comment
        WHERE entityType='Post' AND entityID = %s
        """, (post_id,))
    
    rtr = []

    for row in rows:
        result = cassandra_session.execute("SELECT ProfileImagePath AS ProfileImage FROM users WHERE Nickname = %s", (row.User,))
        rtr.append({
            "ProfileImage": result.ProfileImage,
            "User" : row.User,
            "Text" : row.Testo
        })

    return jsonify(rtr)


@app.route("/ajax/dislike-post", methods=["POST"])
def ajax_dislike_post():
    post_id = request.form.get("postID", "")
    if post_id != "":
        cassandra_session.execute("DELETE FROM likes WHERE User = %s AND Post = %s", (session[USER_ID_IN_SESSION], post_id,))
        cassandra_session.execute("UPDATE post SET Likes = Likes - 1 WHERE ID = %s", (post_id,))


@app.route("/ajax/get-last-notification")
def ajax_get_last_notification():
    row = cassandra_session.execute("SELECT ID FROM notification WHERE Inserimento < toTimestamp(now()) LIMIT 1 ALLOW FILTERING")
    return jsonify({"ID": row[0].id} if row else {"ID": None})


@app.route("/ajax/get-my-notification")
def ajax_get_my_notification():
    # TODO Si potrebbe aggiungere un campo "visto" così da restituire tutte le notifiche non ancora viste
    offset = int(request.form.get("o", int(time()))) # TODO qua vuole un timestamp non un int
    dt = datetime.fromtimestamp(offset, timezone.utc)
    limit = int(request.form.get("n", 5))
    notifications = cassandra_session.execute("SELECT Titolo, Testo, Inserimento FROM notification WHERE UserID = %s AND Inserimento < %s LIMIT %s", (session[USER_ID_IN_SESSION], dt,limit,))
    rtr = []
    for n in notifications:
        rtr.append({
            "Title": n.Titolo,
            "Message" : n.Testo,
            "Inserimento" : n.Inserimento
        })
    return jsonify(rtr)

@app.route("/ajax/unfollow", methods=["GET"])
def ajax_unfollow():
    if USER_ID_IN_SESSION in session:
        cassandra_session.execute("DELETE FROM following WHERE User = %s AND Subreddit = %s", (session[USER_ID_IN_SESSION], request.args["subreddit"],))
        cassandra_session.execute("UPDATE subreddit SET Followers = Followers - 1 WHERE Name = %s", (request.args["subreddit"],))


@app.route("/html-snippets/post-drawer", methods=["POST"])
def post_drawer():
    
    query = request.form.get("query", "")
    offset = int(request.form.get("offset", int(time())))
    dt = datetime.fromtimestamp(offset, timezone.utc)
    limit = int(request.form.get("limit", 10))
    
    if query == "":
        subs = cassandra_session.execute("SELECT Subreddit FROM following WHERE User = %s", (uuid.UUID(session[USER_ID_IN_SESSION]),))
        
        posts = []
        for sub in subs:
            rows = cassandra_session.execute("SELECT * FROM post WHERE Creazione < %s AND Subreddit = %s ORDER BY Creazione DESC LIMIT %s", (dt, sub.Subreddit, limit))
            posts.extend([{
                'id': row.ID ,
                'sub' : row.Subreddit,
                'creator_id': get_user_id_by_nickname(cassandra_session, row.Creator),
                'creator_nickname' : row.Creator,
                "ProfilePicture" : get_user_photo_by_nickname(cassandra_session, row.Creator),
                "titolo" : row.Titolo,
                "testo" : row.Testo,
                "likes" : row.Likes,
                "liked": is_post_liked_by(cassandra_session, row.ID, session[USER_ID_IN_SESSION]),
                "comments" : row.Comments,
                "file" : row.PathToFile,
                "Creazione" : row.Creazione
                } for row in rows])
    else:
        rows_title = cassandra_session.execute(
            "SELECT * FROM post WHERE Titolo = %s AND Creazione < %s LIMIT %s ALLOW FILTERING",
            (query, dt, limit))
        rows_text = cassandra_session.execute(
            "SELECT * FROM post WHERE Testo = %s AND Creazione < %s LIMIT %s ALLOW FILTERING",
            (query, dt, limit))
        
        posts = [{ 
                'id': row.ID ,
                'sub' : row.Subreddit,
                'creator_id': get_user_id_by_nickname(cassandra_session, row.Creator),
                'creator_nickname' : row.Creator,
                "ProfilePicture" : get_user_photo_by_nickname(cassandra_session, row.Creator),
                "titolo" : row.Titolo,
                "testo" : row.Testo,
                "likes" : row.Likes,
                "liked": is_post_liked_by(cassandra_session, row.ID, session[USER_ID_IN_SESSION]),
                "comments" : row.Comments, 
                "file" : row.PathToFile,
                "Creazione" : row.Creazione
                } for row in rows_title]
        
        posts.extend([{ 
                'id': row.ID ,
                'sub' : row.Subreddit,
                'creator_id': get_user_id_by_nickname(cassandra_session, row.Creator),
                'creator_nickname' : row.Creator,
                "ProfilePicture" : get_user_photo_by_nickname(cassandra_session, row.Creator),
                "titolo" : row.Titolo,
                "testo" : row.Testo,
                "likes" : row.Likes,
                "liked": is_post_liked_by(cassandra_session, row.ID, session[USER_ID_IN_SESSION]),
                "comments" : row.Comments, 
                "file" : row.PathToFile,
                "Creazione" : row.Creazione
                } for row in rows_text])
    
    
    template = """
    {% from "components/post.html" import drawPost %}
    {% for p in posts %}
        {{ drawPost( p['id'], p['creator_id'], p['creator_nickname'], p['ProfilePicture'], p['titolo'], p['testo'], p['likes'], p['liked'], p['comments'], p['file']) }}
    {% endfor %}
    """

   
    return render_template_string(template, posts=posts)


@app.route("/html-snippets/user-card-drawer", methods=["POST"])
def post_user_card_drawer():
    query = request.form.get("query", "")

    rows = cassandra_session.execute(
        "SELECT * FROM users WHERE Nickname CONTAINS %s",
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



