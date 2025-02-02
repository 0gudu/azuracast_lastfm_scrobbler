CREATE TABLE acessos (
    uniqueidclient VARCHAR(255) NOT NULL UNIQUE, 
    apikeyscrobble VARCHAR(255) NOT NULL, 
    char1 VARCHAR(255), 
    char2 VARCHAR(255), 
    char3 VARCHAR(255), 
    char4 VARCHAR(255), 
    char5 DATETIME, 
    PRIMARY KEY (uniqueidclient)
);
