from flask import Flask, render_template, request, redirect, session
import json
from globals import *

#from user_getters import is_user_logged_in

app = Flask(__name__)
app.secret_key = "CAMBIA_QUESTA_CHIAVE"

@app.route("/")
def index():

    # === Controllo login ===
    #if not is_user_logged_in(force=True):
    #    return redirect("/login")

    # === Query ===
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
    


if __name__ == "__main__":
    app.run(debug=True)



