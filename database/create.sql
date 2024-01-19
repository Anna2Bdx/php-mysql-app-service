CREATE TABLE Temperatures ( 
    idMaison TINYINT NOT NULL, 
    dateYMD DATE NOT NULL, 
    dateYear SMALLINT NOT NULL, 
    dateMonth TINYINT NOT NULL, 
    dateDay TINYINT NOT NULL, 
    minT DECIMAL(4,2),
    maxT DECIMAL(4,2), 
    avgT DECIMAL(4,2),
    minH DECIMAL(4,2),
    maxH DECIMAL(4,2),
    avgH DECIMAL(4,2) 
    );