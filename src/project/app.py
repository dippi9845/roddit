from flask import Flask, render_template, request, redirect, session
import json
import pymysql

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

    # Qui puoi caricare i post dal DB

    return render_template("index.html", query=query)

if __name__ == "__main__":
    app.run(debug=True)



