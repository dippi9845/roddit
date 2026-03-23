// ============================================================
// Database setup
// ============================================================
db = db.getSiblingDB('roddit');

if (!db.getUser("flask")) {
    db.createUser({
        user: "flask",
        pwd: "password123",
        roles: [{ role: "readWrite", db: "roddit" }]
    });
}

// ============================================================
// Indexes
// ============================================================
db.users.createIndex({ "ID": 1 }, { unique: true });
db.users.createIndex({ "Nickname": 1 }, { unique: true });
db.users.createIndex({ "Email": 1 }, { unique: true });

db.subreddit.createIndex({ "Name": 1 }, { unique: true });

db.following.createIndex({ "User": 1, "Subreddit": 1 }, { unique: true });

db.post.createIndex({ "ID": 1, "Creazione": -1 }, { unique: true });
db.post.createIndex({ "Subreddit": 1 });
db.post.createIndex({ "Creator": 1 });

db.comment.createIndex({ "ID": 1 }, { unique: true });
db.comment.createIndex({ "entityID": 1 });

db.likes.createIndex({ "User": 1, "Post": 1 }, { unique: true });

db.notification.createIndex({ "ID": 1, "Inserimento": -1 }, { unique: true });
db.notification.createIndex({ "UserID": 1 });

db.post_like.createIndex({ "post": 1 }, { unique: true });
db.post_comment.createIndex({ "post": 1 }, { unique: true });


db = db.getSiblingDB('admin');

sh.enableSharding("roddit");

sh.shardCollection("roddit.users", { "ID": "hashed" });

sh.shardCollection("roddit.subreddit", { "Name": "hashed" });

sh.shardCollection("roddit.post", { "Subreddit": 1, "Creazione": 1 });

sh.shardCollection("roddit.comment", { "entityID": "hashed" });

sh.shardCollection("roddit.likes", { "User": "hashed" });

sh.shardCollection("roddit.following", { "User": "hashed" });

sh.shardCollection("roddit.notification", { "UserID": "hashed" });

sh.shardCollection("roddit.post_like", { "post": "hashed" });
sh.shardCollection("roddit.post_comment", { "post": "hashed" });