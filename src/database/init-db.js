db = db.getSiblingDB('roddit');

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
