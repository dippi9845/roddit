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
  Followers counter
);

CREATE TABLE following (
  Subreddit text,
  User UUID,
  PRIMARY KEY (User, Subreddit)
);


CREATE TABLE comment (
  ID UUID,
  User text,
  Testo text,
  entityType text, -- POST, COMMENTO
  entityID UUID,
  PRIMARY KEY (ID)
);

-- CREATE INDEX ON comment(entityID) USING 'StorageAttachedIndex';


CREATE TABLE likes (
  User UUID,
  Post UUID,
  PRIMARY KEY (User, Post)
);

CREATE TABLE notification (
  ID UUID,
  UserID UUID,
  Titolo text,
  Testo text,
  Inserimento timestamp,
  PRIMARY KEY (ID, Inserimento)
) WITH CLUSTERING ORDER BY (Inserimento DESC);

-- CREATE INDEX ON notification(UserID) USING 'StorageAttachedIndex';


CREATE TABLE post (
  ID UUID,
  Subreddit text,
  Creator text,
  Titolo text,
  Testo text,
  PathToFile text,
  MediaType text, -- Immagine, Video
  Creazione timestamp,
  PRIMARY KEY (ID, Creazione)
) WITH CLUSTERING ORDER BY (Creazione DESC);

-- CREATE INDEX ON post(Subreddit) USING 'StorageAttachedIndex';

-- CREATE INDEX ON post(Creator) USING 'StorageAttachedIndex';

CREATE TABLE post_like (
  post UUID PRIMARY KEY,
  likes counter
);

CREATE TABLE post_comment (
  post UUID PRIMARY KEY,
  comments counter
);

CREATE TABLE users (-- TODO trovare un modo per distribuire le immagini tra vari nodi
  ID UUID,
  Nickname text,
  Email text,
  Bio text,
  Password text,
  Salt text,
  ProfileImagePath text,
  PRIMARY KEY (ID)
);

-- CREATE INDEX ON users(Nickname) USING 'StorageAttachedIndex';
-- CREATE INDEX ON users(Email) USING 'StorageAttachedIndex';
-- CREATE INDEX ON users(Password) USING 'StorageAttachedIndex';
