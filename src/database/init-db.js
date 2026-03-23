db = db.getSiblingDB('roddit');

if (!db.getUser("flask")) {
    db.createUser({
        user: "flask",
        pwd: "password123",
        roles: [{ role: "readWrite", db: "roddit" }]
    });
}

db.users.dropIndexes();
db.subreddit.dropIndexes();
db.following.dropIndexes();
db.post.dropIndexes();
db.comment.dropIndexes();
db.likes.dropIndexes();
db.notification.dropIndexes();
db.post_like.dropIndexes();
db.post_comment.dropIndexes();


db.users.createIndex({ "ID": 1 }, { unique: true });
db.users.createIndex({ "Nickname": 1 });
db.users.createIndex({ "Email": 1 });
 
db.subreddit.createIndex({ "Name": 1 }, { unique: true });
 
db.following.createIndex({ "User": 1, "Subreddit": 1 }, { unique: true });
 
db.post.createIndex({ "ID": 1, "Creazione": -1 });
db.post.createIndex({ "Subreddit": 1 });
db.post.createIndex({ "Creator": 1 });
 
db.comment.createIndex({ "ID": 1 });
db.comment.createIndex({ "entityID": 1 });
 
db.likes.createIndex({ "User": 1, "Post": 1 }, { unique: true });
 
db.notification.createIndex({ "ID": 1, "Inserimento": -1 });
db.notification.createIndex({ "UserID": 1 });
 
db.post_like.createIndex({ "post": 1 });
db.post_comment.createIndex({ "post": 1 });
 

db.users.createIndex({ "ID": "hashed" });
db.subreddit.createIndex({ "Name": "hashed" });
db.post.createIndex({ "Subreddit": 1, "Creazione": 1 });
db.comment.createIndex({ "entityID": "hashed" });
db.likes.createIndex({ "User": "hashed" });
db.following.createIndex({ "User": "hashed" });
db.notification.createIndex({ "UserID": "hashed" });
db.post_like.createIndex({ "post": "hashed" });
db.post_comment.createIndex({ "post": "hashed" });
 

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