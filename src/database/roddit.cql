CREATE KEYSPACE IF NOT EXISTS roddit
WITH replication = {
  'class': 'SimpleStrategy',
  'replication_factor': 1
};

USE roddit;

CREATE ROLE flask WITH PASSWORD='cassandra123' AND LOGIN=true;
GRANT ALL PERMISSIONS ON KEYSPACE ks TO roddit;

CREATE TABLE IF NOT EXISTS comment (
  ID UUID PRIMARY KEY,
  User text,
  Testo text,
  entityType text, -- POST, COMMENTO
  entityID UUID
);

CREATE TABLE IF NOT EXISTS cookies (
  UserID UUID,
  ExpireDate timestamp,
  IDToken UUID PRIMARY KEY
);

CREATE TABLE IF NOT EXISTS follow (
  Follower UUID,
  Following UUID,
  PRIMARY KEY (Follower, Following)
);

CREATE TABLE IF NOT EXISTS likes (
  User UUID,
  Post UUID,
  PRIMARY KEY (User, Post)
);

CREATE TABLE IF NOT EXISTS notification (
  ID UUID PRIMARY KEY,
  UserID UUID,
  Titolo text,
  Testo text,
  Inserimento timestamp,
);

CREATE TABLE IF NOT EXISTS post (
  ID UUID PRIMARY KEY,
  Creator text,
  Titolo text,
  Testo text,
  Likes int,
  Comments int,
  PathToFile text,
  MediaType text -- Immagine, Video
);

CREATE TABLE IF NOT EXISTS users (-- TODO introdurre le immagini come blob perché così sono distribuite
  ID UUID PRIMARY KEY,
  Nickname text,
  Email text,
  Bio text,
  Password text,
  Salt text,
  ProfileImagePath text
);
