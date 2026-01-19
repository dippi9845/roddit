from cassandra.cluster import Session as CassandraSession

def notify_user(cs : CassandraSession, user_id, titolo, messaggio):
    cs.execute("INSERT INTO notification (ID, UserID, Titolo, Testo, Inserimento) VALUES (uuid(), ?, ?, ?, toTimestamp(now()))", (user_id, titolo, messaggio,))