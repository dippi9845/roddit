// 1. Modifica database da 'admin' a 'config' per il chunksize
db = db.getSiblingDB('config'); 

// Impostazione del chunksize a 1MB
db.settings.updateOne(
  { _id: "chunksize" },
  { $set: { value: 1 } },
  { upsert: true }
);

// 2. Abilitazione dello sharding sul database applicativo
sh.enableSharding("roddit");

db = db.getSiblingDB('roddit');

// Creazione utente per l'applicazione Flask
if (!db.getUser("flask")) {
    db.createUser({
        user: "flask",
        pwd: "password123",
        roles: [{ role: "readWrite", db: "roddit" }]
    });
}

// 3. Sharding delle collezioni
// Nota: Rimosso numInitialChunks per forzare il bilanciatore a lavorare sui dati reali
sh.shardCollection("roddit.users", { "ID": "hashed" });
sh.shardCollection("roddit.subreddit", { "Name": "hashed" });
sh.shardCollection("roddit.post", { "Subreddit": 1, "Creazione": 1 });
sh.shardCollection("roddit.comment", { "entityID": "hashed" });
sh.shardCollection("roddit.likes", { "User": "hashed" });
sh.shardCollection("roddit.following", { "User": "hashed" });
sh.shardCollection("roddit.notification", { "UserID": "hashed" });
sh.shardCollection("roddit.post_like", { "post": "hashed" });
sh.shardCollection("roddit.post_comment", { "post": "hashed" });

// 4. Creazione indici aggiuntivi (Rimosse le chiamate dropIndexes)
db.users.createIndex({ "Nickname": 1 });
db.users.createIndex({ "Email": 1 });
db.users.createIndex({ "ID": 1 }, { unique: true });

db.subreddit.createIndex({ "Name": 1 }, { unique: true });

db.following.createIndex({ "User": 1, "Subreddit": 1 }, { unique: true });

db.post.createIndex({ "ID": 1, "Creazione": -1 });
db.post.createIndex({ "Creator": 1 });

db.comment.createIndex({ "ID": 1 });
db.comment.createIndex({ "entityID": 1 });

db.notification.createIndex({ "ID": 1, "Inserimento": -1 });
db.notification.createIndex({ "UserID": 1 });

db.post_like.createIndex({ "post": 1 });
db.post_comment.createIndex({ "post": 1 });