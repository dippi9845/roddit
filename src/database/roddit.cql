CREATE KEYSPACE roddit
WITH replication = {
  'class': 'SimpleStrategy',
  'replication_factor': 1
};

USE roddit;

CREATE ROLE flask WITH PASSWORD='cassandra123' AND LOGIN=true;
GRANT ALL PERMISSIONS ON KEYSPACE ks TO roddit;

CREATE TABLE comment (
  ID UUID PRIMARY KEY,
  User text,
  Testo text,
  entityType text, -- POST, COMMENTO
  entityID UUID
);

CREATE TABLE follow (
  Follower UUID,
  Following UUID,
  PRIMARY KEY (Follower, Following)
);

CREATE TABLE likes (
  User UUID,
  Post UUID,
  PRIMARY KEY (User, Post)
);

CREATE TABLE notification (
  ID UUID PRIMARY KEY,
  UserID UUID,
  Titolo text,
  Testo text,
  Inserimento timestamp,
);

CREATE TABLE post (
  ID UUID PRIMARY KEY,
  Creator text,
  Titolo text,
  Testo text,
  Likes int,
  Comments int,
  PathToFile text,
  MediaType text -- Immagine, Video
);

CREATE TABLE users (-- TODO trovare un modo per distribuire le immagini tra vari nodi
  ID UUID PRIMARY KEY,
  Nickname text,
  Email text,
  Bio text,
  Password text,
  Salt text,
  ProfileImagePath text
);
