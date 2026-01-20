CREATE KEYSPACE roddit
WITH replication = {
  'class': 'SimpleStrategy',
  'replication_factor': 1
};

USE roddit;

CREATE ROLE flask WITH PASSWORD='cassandra123' AND LOGIN=true;
GRANT ALL PERMISSIONS ON KEYSPACE ks TO roddit;

CREATE TABLE subreddit (
  Name text PRIMARY KEY,
  Followers int
);

CREATE TABLE following (
  Subreddit text,
  User UUID,
  PRIMARY KEY (User, Subreddit)
);


CREATE TABLE comment (
  ID UUID PRIMARY KEY,
  User text,
  Testo text,
  entityType text, -- POST, COMMENTO
  entityID UUID
);

CREATE INDEX ON comment(entityID, entityType) USING 'StorageAttachedIndex';


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

CREATE INDEX ON notification(UserID) USING 'StorageAttachedIndex';


CREATE TABLE post (
  ID UUID PRIMARY KEY,
  Subreddit text,
  Creator text,
  Titolo text,
  Testo text,
  Likes int,
  Comments int,
  PathToFile text,
  MediaType text, -- Immagine, Video
  Creazione timestamp
) WITH CLUSTERING ORDER BY (Creazione DESC);

CREATE INDEX ON post(Subreddit) USING 'StorageAttachedIndex';

CREATE INDEX ON post(Creator) USING 'StorageAttachedIndex';

CREATE CUSTOM INDEX ON post(Titolo)
USING 'org.apache.cassandra.index.sasi.SASIIndex'
WITH OPTIONS = {
  'mode': 'CONTAINS'
};

CREATE CUSTOM INDEX ON post(Testo)
USING 'org.apache.cassandra.index.sasi.SASIIndex'
WITH OPTIONS = {
  'mode': 'CONTAINS'
};

CREATE TABLE users (-- TODO trovare un modo per distribuire le immagini tra vari nodi
  ID UUID PRIMARY KEY,
  Nickname text,
  Email text,
  Bio text,
  Password text,
  Salt text,
  ProfileImagePath text
);

CREATE INDEX ON users(Nickname) USING 'StorageAttachedIndex';
CREATE INDEX ON users(Email, Password) USING 'StorageAttachedIndex';
