use Math;
-- Creating Problems Table
CREATE TABLE Problems (
    ProblemID INT PRIMARY KEY AUTO_INCREMENT,
    Title VARCHAR(255) NOT NULL,
    Description TEXT,
    CreatedAt TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Creating Solutions Table
CREATE TABLE Solutions (
    SolutionID INT PRIMARY KEY AUTO_INCREMENT,
    ProblemID INT,
    SolutionText TEXT,
    FOREIGN KEY (ProblemID) REFERENCES Problems(ProblemID)
);

-- Creating Graphs Table
CREATE TABLE Graphs (
    GraphID INT PRIMARY KEY AUTO_INCREMENT,
    ProblemID INT,
    GraphType VARCHAR(50),
    FileName VARCHAR(200), -- base64 encoded
    FOREIGN KEY (ProblemID) REFERENCES Problems(ProblemID)
);

-- Creating Tags Table
CREATE TABLE Tags (
    TagID INT PRIMARY KEY AUTO_INCREMENT,
    TagName VARCHAR(100)
);

-- Creating ProblemTags Junction Table
CREATE TABLE ProblemTags (
    ProblemID INT,
    TagID INT,
    PRIMARY KEY (ProblemID, TagID),
    FOREIGN KEY (ProblemID) REFERENCES Problems(ProblemID),
    FOREIGN KEY (TagID) REFERENCES Tags(TagID)
);

-- Creating Sources Table
CREATE TABLE Sources (
    SourceID INT PRIMARY KEY AUTO_INCREMENT,
    SourceName VARCHAR(255)
);

-- Creating ProblemSources Junction Table
CREATE TABLE ProblemSources (
    ProblemID INT,
    SourceID INT,
    PRIMARY KEY (ProblemID, SourceID),
    FOREIGN KEY (ProblemID) REFERENCES Problems(ProblemID),
    FOREIGN KEY (SourceID) REFERENCES Sources(SourceID)
);
