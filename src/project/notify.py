from cassandra.cluster import Session as CassandraSession

def notify_user(cs : CassandraSession, user_id, titolo, messaggio):
    cs.execute("INSERT INTO notification (ID, UserID, Titolo, Testo, Inserimento) VALUES (uuid(), %s, %s, %s, toTimestamp(now()))", (user_id, titolo, messaggio,))

def get_post_creator(cs : CassandraSession, post_id):
    row_post = cs.execute("SELECT Creator FROM post WHERE ID = %s", (post_id,))
    row_user = cs.execute("SELECT ID FROM users WHERE Nickname = %s", (row_post[0].creator,))
    return row_user[0].id