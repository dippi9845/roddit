from flask import Flask, render_template, request, redirect, session, make_response
import json
from globals import *
from cassandra.cluster import Cluster
from cassandra.auth import PlainTextAuthProvider



app = Flask(__name__)
app.secret_key = "CAMBIA_QUESTA_CHIAVE"

@app.route("/")
def index():

    if not is_user_logged_in(True):
        return redirect("/login")
    
    query = request.args.get("query", "")

    # TODO Qui puoi caricare i post dal DB

    return render_template("index.html", query=query)

@app.route("/profile")
def profile():
    if not is_user_logged_in(force=True):
        return redirect("/login")

    conn = get_db()

    visited_user = request.args.get("user", session["userID"])

    if not user_exists(conn, visited_user):
        return redirect("/404")

    profile_data = {
        "id": visited_user,
        "name": get_user_name_by_id(conn, visited_user),
        "bio": get_user_biography(conn, visited_user),
        "picture": get_user_profile_picture(conn, visited_user),
        "followers_count": get_user_follower_count(conn, visited_user),
        "following_count": get_user_following_count(conn, visited_user),
        "followers": get_user_followers(conn, visited_user),
        "following": get_following_users(conn, visited_user),
        "is_me": visited_user == session["userID"],
        "is_following": is_following(conn, visited_user, session["userID"])
    }

    posts = get_users_posts(conn, visited_user)

    for p in posts:
        p["liked"] = is_liked(conn, p["ID"], session["userID"])

    conn.close()

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

@app.route("/register", methods=["GET", "POST"])
def register():
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

                # Se tutto OK → inserimento DB
                if not err:

                    nickname = html.escape(form["nickname"])
                    email = html.escape(form["email"])

                    salt = uuid.uuid4().hex
                    password_hash = generate_password_hash(form["password"] + salt)

                    cursor.execute(
                        "INSERT INTO users (Nickname, Email, Password, Salt) VALUES (%s, %s, %s, %s)",
                        (nickname, email, password_hash, salt)
                    )


                    return redirect("/login")

    return render_template("register.html", err=err, text_err=text_err, form=form)

@app.route("/settings", methods=["GET", "POST"])
def settings():

    if "user_id" not in session:
        return redirect("/login")

    err = False
    text_err = ""

    # Carica config DB
    with open("../setup.json") as f:
        data = json.load(f)

    conn = mysql.connector.connect(
        host="localhost",
        user=data["dbName"],
        password=data["dbPassword"],
        database=data["dbUserName"]
    )

    cursor = conn.cursor()

    user_id = session["user_id"]

    # -------------------------
    # Cambio email
    # -------------------------
    new_email = request.form.get("new-email")
    if new_email:
        if re.match(r"[^@]+@[^@]+\.[^@]+", new_email):
            new_email = html.escape(new_email)
            cursor.execute(
                "UPDATE users SET Email=%s WHERE ID=%s",
                (new_email, user_id)
            )
            conn.commit()
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
            cursor.execute(
                "UPDATE users SET Nickname=%s WHERE ID=%s",
                (new_nickname, user_id)
            )
            conn.commit()

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
            password_hash = generate_password_hash(new_password + salt)

            cursor.execute(
                "UPDATE users SET Password=%s, Salt=%s WHERE ID=%s",
                (password_hash, salt, user_id)
            )
            conn.commit()

    # -------------------------
    # Cambio bio
    # -------------------------
    new_bio = request.form.get("new-biography")
    if new_bio:
        new_bio = html.escape(new_bio)
        cursor.execute(
            "UPDATE users SET Bio=%s WHERE ID=%s",
            (new_bio, user_id)
        )
        conn.commit()

    # -------------------------
    # Cambio immagine
    # -------------------------
    if "new-photo" in request.files:
        file = request.files["new-photo"]
        if file and file.filename != "":
            path = save_image(file)
            if path:
                cursor.execute(
                    "UPDATE users SET ProfileImagePath=%s WHERE ID=%s",
                    (path, user_id)
                )
                conn.commit()

    # -------------------------
    # Recupera dati utente
    # -------------------------
    cursor.execute(
        "SELECT ProfileImagePath, Email, Nickname, Bio FROM users WHERE ID=%s",
        (user_id,)
    )

    row = cursor.fetchone()
    cursor.close()
    conn.close()

    return render_template(
    "settings.html",
    user={
        "photo": row[0],
        "email": row[1],
        "nickname": row[2],
        "bio": row[3]
    },
    err=err,
    text_err=text_err
)

@app.route("/ajax/login", methods=["POST"])
def ajax_login():
    
    def is_form_valid():
        return 'email' in request.form and 'password' in request.form

    def get_user_id(conn, email, password):
        pass # TODO to implement

    
    def main(conn):
        if not is_form_valid():
            print("Invalid form")
            return False, None

        user_id = get_user_id(conn, request.form['email'], request.form['password'])
        if not user_id:
            print("Invalid credentials")
            return False, None

        create_session(user_id)

        response = make_response()

        if 'remember' in request.form:
            create_cookie(response, user_id)

        return True, response

    ok, response = main(conn)

    if ok:
        response.headers["Location"] = "/"
        response.status_code = 302
        return response
    else:
        return redirect("/login")


if __name__ == "__main__":
    app.run(debug=True)



