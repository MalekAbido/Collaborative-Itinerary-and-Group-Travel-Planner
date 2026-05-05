CREATE TABLE Activity
(
  id           INT          NOT NULL AUTO_INCREMENT,
  itemId       VARCHAR(55)  NOT NULL,
  name         VARCHAR(150) NULL    ,
  description  TEXT         NULL    ,
  startTime    DATETIME     NULL    ,
  endTime      DATETIME     NULL    ,
  category     VARCHAR(50)  NULL    ,
  status       VARCHAR(30)  NULL    ,
  itineraryId  INT          NOT NULL,
  tripMemberId INT          NOT NULL,
  subtripId    INT          NULL    ,
  locationId   INT          NOT NULL,
  PRIMARY KEY (id)
);

ALTER TABLE Activity
  ADD CONSTRAINT UQ_id UNIQUE (id);

ALTER TABLE Activity
  ADD CONSTRAINT UQ_itemId UNIQUE (itemId);

CREATE TABLE Allergy
(
  id         INT          NOT NULL AUTO_INCREMENT,
  allergenId VARCHAR(50)  NOT NULL,
  allergen   VARCHAR(100) NULL    ,
  severity   VARCHAR(50)  NULL    ,
  reaction   TEXT         NULL    ,
  userId     INT          NOT NULL,
  PRIMARY KEY (id)
);

ALTER TABLE Allergy
  ADD CONSTRAINT UQ_id UNIQUE (id);

ALTER TABLE Allergy
  ADD CONSTRAINT UQ_allergenId UNIQUE (allergenId);

CREATE TABLE AttendanceList
(
  id                 INT NOT NULL AUTO_INCREMENT,
  totalAttendeeCount INT NULL    ,
  activityId         INT NOT NULL,
  PRIMARY KEY (id)
);

ALTER TABLE AttendanceList
  ADD CONSTRAINT UQ_id UNIQUE (id);

CREATE TABLE AttendanceMember
(
  id               INT         NOT NULL AUTO_INCREMENT,
  status           VARCHAR(30) NULL    ,
  note             TEXT        NULL    ,
  attendanceListId INT         NOT NULL,
  tripMemberId     INT         NOT NULL,
  PRIMARY KEY (id)
);

ALTER TABLE AttendanceMember
  ADD CONSTRAINT UQ_id UNIQUE (id);

CREATE TABLE EmergencyContact
(
  id           INT          NOT NULL AUTO_INCREMENT,
  contactId    VARCHAR(55)  NOT NULL,
  name         VARCHAR(100) NULL    ,
  phone        VARCHAR(20)  NULL    ,
  email        VARCHAR(100) NULL    ,
  relationship VARCHAR(50)  NULL    ,
  userId       INT          NOT NULL,
  PRIMARY KEY (id)
);

ALTER TABLE EmergencyContact
  ADD CONSTRAINT UQ_id UNIQUE (id);

ALTER TABLE EmergencyContact
  ADD CONSTRAINT UQ_contactId UNIQUE (contactId);

CREATE TABLE Expense
(
  id              INT           NOT NULL AUTO_INCREMENT,
  expenseId       VARCHAR(55)   NOT NULL,
  amount          DECIMAL(15,2) NULL    ,
  refundedAmount  DECIMAL(15,2) NULL    ,
  currencyType    VARCHAR(10)   NULL    ,
  description     TEXT          NULL    ,
  category        VARCHAR(50)   NULL    ,
  isNonCash       BOOLEAN       NULL    ,
  paidByKitty     BOOLEAN       NULL    ,
  tripFinanceId   INT           NOT NULL,
  tripMemberId    INT           NOT NULL,
  PRIMARY KEY (id)
);

ALTER TABLE Expense
  ADD CONSTRAINT UQ_id UNIQUE (id);

ALTER TABLE Expense
  ADD CONSTRAINT UQ_expenseId UNIQUE (expenseId);

CREATE TABLE ExpenseShare
(
  id           INT           NOT NULL AUTO_INCREMENT,
  shareId      VARCHAR(55)   NOT NULL,
  amount       DECIMAL(15,2) NULL    ,
  isPayer      BOOLEAN       NULL    ,
  expenseId    INT           NOT NULL,
  tripMemberId INT           NOT NULL,
  PRIMARY KEY (id)
);

ALTER TABLE ExpenseShare
  ADD CONSTRAINT UQ_id UNIQUE (id);

ALTER TABLE ExpenseShare
  ADD CONSTRAINT UQ_shareId UNIQUE (shareId);

CREATE TABLE FundContribution
(
  id             INT           NOT NULL AUTO_INCREMENT,
  contributionId VARCHAR(55)   NOT NULL,
  amount         DECIMAL(15,2) NULL    ,
  timestamp      DATETIME      NULL    ,
  groupFundId    INT           NOT NULL,
  tripMemberId   INT           NOT NULL,
  PRIMARY KEY (id)
);

ALTER TABLE FundContribution
  ADD CONSTRAINT UQ_id UNIQUE (id);

ALTER TABLE FundContribution
  ADD CONSTRAINT UQ_contributionId UNIQUE (contributionId);

CREATE TABLE GroupFund
(
  id             INT           NOT NULL AUTO_INCREMENT,
  fundId         VARCHAR(55)   NOT NULL,
  targetBalance  DECIMAL(15,2) NULL    ,
  currentBalance DECIMAL(15,2) NULL    ,
  tripFinanceId  INT           NOT NULL,
  PRIMARY KEY (id)
);

ALTER TABLE GroupFund
  ADD CONSTRAINT UQ_id UNIQUE (id);

ALTER TABLE GroupFund
  ADD CONSTRAINT UQ_fundId UNIQUE (fundId);

CREATE TABLE HistoryLog
(
  id          INT         NOT NULL AUTO_INCREMENT,
  logId       VARCHAR(55) NOT NULL,
  itineraryId INT         NOT NULL,
  PRIMARY KEY (id)
);

ALTER TABLE HistoryLog
  ADD CONSTRAINT UQ_id UNIQUE (id);

ALTER TABLE HistoryLog
  ADD CONSTRAINT UQ_logId UNIQUE (logId);

CREATE TABLE HistoryLogEntry
(
  id                 INT          NOT NULL AUTO_INCREMENT,
  entryId            VARCHAR(55)  NOT NULL,
  transactionType    VARCHAR(50)  NULL    ,
  timestamp          DATETIME     NULL    ,
  changedEntityId    VARCHAR(55)  NULL    ,
  changedEntityType  VARCHAR(100) NULL    ,
  historyLogId       INT          NOT NULL,
  tripMemberId       INT          NOT NULL,
  previousSnapshotId INT          NULL    ,
  PRIMARY KEY (id)
);

ALTER TABLE HistoryLogEntry
  ADD CONSTRAINT UQ_id UNIQUE (id);

ALTER TABLE HistoryLogEntry
  ADD CONSTRAINT UQ_entryId UNIQUE (entryId);

CREATE TABLE InventoryItem
(
  id           INT          NOT NULL AUTO_INCREMENT,
  itemId       VARCHAR(55)  NOT NULL,
  name         VARCHAR(150) NULL    ,
  quantity     INT          NULL    ,
  category     VARCHAR(50)  NULL    ,
  isPacked     BOOLEAN      NULL    ,
  activityId   INT          NOT NULL,
  tripMemberId INT          NULL    ,
  PRIMARY KEY (id)
);

ALTER TABLE InventoryItem
  ADD CONSTRAINT UQ_id UNIQUE (id);

ALTER TABLE InventoryItem
  ADD CONSTRAINT UQ_itemId UNIQUE (itemId);

CREATE TABLE Invitation
(
  id           INT          NOT NULL AUTO_INCREMENT,
  invitationId VARCHAR(55)  NOT NULL,
  secureToken  VARCHAR(255) NULL    ,
  isActive     BOOLEAN      NULL    ,
  itineraryId  INT          NOT NULL,
  PRIMARY KEY (id)
);

ALTER TABLE Invitation
  ADD CONSTRAINT UQ_id UNIQUE (id);

ALTER TABLE Invitation
  ADD CONSTRAINT UQ_invitationId UNIQUE (invitationId);

CREATE TABLE Itinerary
(
  id          INT          NOT NULL AUTO_INCREMENT,
  itineraryId VARCHAR(55)  NOT NULL,
  title       VARCHAR(150) NULL    ,
  description TEXT         NULL    ,
  startDate   DATE         NULL    ,
  endDate     DATE         NULL    ,
  PRIMARY KEY (id)
);

ALTER TABLE Itinerary
  ADD CONSTRAINT UQ_id UNIQUE (id);

ALTER TABLE Itinerary
  ADD CONSTRAINT UQ_itineraryId UNIQUE (itineraryId);

CREATE TABLE Location
(
  id         INT           NOT NULL AUTO_INCREMENT,
  placeId    VARCHAR(55)   NOT NULL,
  name       VARCHAR(150)  NULL    ,
  address    TEXT          NULL    ,
  latitude   DECIMAL(11,8) NULL    ,
  longitude  DECIMAL(11,8) NULL    ,
  timeZoneId VARCHAR(50)   NULL    ,
  PRIMARY KEY (id)
);

ALTER TABLE Location
  ADD CONSTRAINT UQ_id UNIQUE (id);

ALTER TABLE Location
  ADD CONSTRAINT UQ_placeId UNIQUE (placeId);

CREATE TABLE Poll
(
  id            INT           NOT NULL AUTO_INCREMENT,
  pollId        VARCHAR(55)   NOT NULL,
  deadline      DATETIME      NULL    ,
  status        VARCHAR(20)   NULL    ,
  isAnonymous   BOOLEAN       NULL    ,
  weightedTotal DECIMAL(15,5) NULL    ,
  activityId    INT           NOT NULL,
  PRIMARY KEY (id)
);

ALTER TABLE Poll
  ADD CONSTRAINT UQ_id UNIQUE (id);

ALTER TABLE Poll
  ADD CONSTRAINT UQ_pollId UNIQUE (pollId);

CREATE TABLE RatingChoice
(
  id       INT          NOT NULL AUTO_INCREMENT,
  choiceId VARCHAR(55)  NOT NULL,
  label    VARCHAR(50)  NULL    ,
  weight   DECIMAL(5,2) NULL    ,
  PRIMARY KEY (id)
);

ALTER TABLE RatingChoice
  ADD CONSTRAINT UQ_id UNIQUE (id);

ALTER TABLE RatingChoice
  ADD CONSTRAINT UQ_choiceId UNIQUE (choiceId);

CREATE TABLE Subtrip
(
  id           INT          NOT NULL AUTO_INCREMENT,
  subtripId    VARCHAR(55)  NOT NULL,
  name         VARCHAR(150) NULL    ,
  description  TEXT         NULL    ,
  startTime    DATETIME     NULL    ,
  endTime      DATETIME     NULL    ,
  itineraryId  INT          NOT NULL,
  tripMemberId INT          NOT NULL,
  PRIMARY KEY (id)
);

ALTER TABLE Subtrip
  ADD CONSTRAINT UQ_id UNIQUE (id);

ALTER TABLE Subtrip
  ADD CONSTRAINT UQ_subtripId UNIQUE (subtripId);

CREATE TABLE TransportDetail
(
  id              INT           NOT NULL AUTO_INCREMENT,
  transportId     VARCHAR(55)   NOT NULL,
  distance        DECIMAL(10,2) NULL    ,
  duration        INT           NULL    ,
  fromActivityId  INT           NOT NULL,
  toActivityId    INT           NOT NULL,
  transportModeId INT           NOT NULL,
  PRIMARY KEY (id)
);

ALTER TABLE TransportDetail
  ADD CONSTRAINT UQ_id UNIQUE (id);

ALTER TABLE TransportDetail
  ADD CONSTRAINT UQ_transportId UNIQUE (transportId);

CREATE TABLE TransportMode
(
  id     INT         NOT NULL AUTO_INCREMENT,
  modeId VARCHAR(55) NOT NULL,
  name   VARCHAR(50) NULL    ,
  PRIMARY KEY (id)
);

ALTER TABLE TransportMode
  ADD CONSTRAINT UQ_id UNIQUE (id);

ALTER TABLE TransportMode
  ADD CONSTRAINT UQ_modeId UNIQUE (modeId);

CREATE TABLE TripFinance
(
  id           INT           NOT NULL AUTO_INCREMENT,
  financeId    VARCHAR(55)   NOT NULL,
  baseCurrency VARCHAR(10)   NULL    ,
  budgetLimit  DECIMAL(15,2) NULL    ,
  itineraryId  INT           NOT NULL,
  PRIMARY KEY (id)
);

ALTER TABLE TripFinance
  ADD CONSTRAINT UQ_id UNIQUE (id);

ALTER TABLE TripFinance
  ADD CONSTRAINT UQ_financeId UNIQUE (financeId);

CREATE TABLE TripMember
(
  id           INT         NOT NULL AUTO_INCREMENT,
  membershipId VARCHAR(55) NOT NULL,
  role         VARCHAR(30) NULL    ,
  joinedAt     DATETIME    NULL    ,
  userId       INT         NOT NULL,
  itineraryId  INT         NOT NULL,
  PRIMARY KEY (id)
);

ALTER TABLE TripMember
  ADD CONSTRAINT UQ_id UNIQUE (id);

ALTER TABLE TripMember
  ADD CONSTRAINT UQ_membershipId UNIQUE (membershipId);

CREATE TABLE User
(
  id           INT          NOT NULL AUTO_INCREMENT,
  userId       VARCHAR(55)  NOT NULL,
  firstName    VARCHAR(50)  NULL    ,
  lastName     VARCHAR(50)  NULL    ,
  email        VARCHAR(255) NULL    ,
  passwordHash VARCHAR(255) NULL    ,
  nationality  VARCHAR(50)  NULL    ,
  policyNumber CHAR(19)     NULL    ,
  sessionToken VarCHAR(255) NULL    ,
  PRIMARY KEY (id)
);

ALTER TABLE User
  ADD CONSTRAINT UQ_id UNIQUE (id);

ALTER TABLE User
  ADD CONSTRAINT UQ_userId UNIQUE (userId);

ALTER TABLE User
  ADD CONSTRAINT UQ_email UNIQUE (email);

ALTER TABLE User
  ADD CONSTRAINT UQ_sessionToken UNIQUE (sessionToken);

CREATE TABLE Vote
(
  id             INT           NOT NULL AUTO_INCREMENT,
  voteId         VARCHAR(55)   NOT NULL,
  voteWeight     DECIMAL(10,5) NULL    ,
  timestamp      DATETIME      NULL    ,
  pollId         INT           NOT NULL,
  tripMemberId   INT           NOT NULL,
  ratingChoiceId INT           NOT NULL,
  PRIMARY KEY (id)
);

ALTER TABLE Vote
  ADD CONSTRAINT UQ_id UNIQUE (id);

ALTER TABLE Vote
  ADD CONSTRAINT UQ_voteId UNIQUE (voteId);

ALTER TABLE EmergencyContact
  ADD CONSTRAINT FK_User_TO_EmergencyContact
    FOREIGN KEY (userId)
    REFERENCES User (id);

ALTER TABLE Allergy
  ADD CONSTRAINT FK_User_TO_Allergy
    FOREIGN KEY (userId)
    REFERENCES User (id);

ALTER TABLE TripMember
  ADD CONSTRAINT FK_User_TO_TripMember
    FOREIGN KEY (userId)
    REFERENCES User (id);

ALTER TABLE TripMember
  ADD CONSTRAINT FK_Itinerary_TO_TripMember
    FOREIGN KEY (itineraryId)
    REFERENCES Itinerary (id);

ALTER TABLE Invitation
  ADD CONSTRAINT FK_Itinerary_TO_Invitation
    FOREIGN KEY (itineraryId)
    REFERENCES Itinerary (id);

ALTER TABLE TripFinance
  ADD CONSTRAINT FK_Itinerary_TO_TripFinance
    FOREIGN KEY (itineraryId)
    REFERENCES Itinerary (id);

ALTER TABLE GroupFund
  ADD CONSTRAINT FK_TripFinance_TO_GroupFund
    FOREIGN KEY (tripFinanceId)
    REFERENCES TripFinance (id);

ALTER TABLE FundContribution
  ADD CONSTRAINT FK_GroupFund_TO_FundContribution
    FOREIGN KEY (groupFundId)
    REFERENCES GroupFund (id);

ALTER TABLE FundContribution
  ADD CONSTRAINT FK_TripMember_TO_FundContribution
    FOREIGN KEY (tripMemberId)
    REFERENCES TripMember (id);

ALTER TABLE Expense
  ADD CONSTRAINT FK_TripFinance_TO_Expense
    FOREIGN KEY (tripFinanceId)
    REFERENCES TripFinance (id);

ALTER TABLE ExpenseShare
  ADD CONSTRAINT FK_Expense_TO_ExpenseShare
    FOREIGN KEY (expenseId)
    REFERENCES Expense (id);

ALTER TABLE Expense
  ADD CONSTRAINT FK_TripMember_TO_Expense
    FOREIGN KEY (tripMemberId)
    REFERENCES TripMember (id);

ALTER TABLE ExpenseShare
  ADD CONSTRAINT FK_TripMember_TO_ExpenseShare
    FOREIGN KEY (tripMemberId)
    REFERENCES TripMember (id);

ALTER TABLE HistoryLog
  ADD CONSTRAINT FK_Itinerary_TO_HistoryLog
    FOREIGN KEY (itineraryId)
    REFERENCES Itinerary (id);

ALTER TABLE HistoryLogEntry
  ADD CONSTRAINT FK_HistoryLog_TO_HistoryLogEntry
    FOREIGN KEY (historyLogId)
    REFERENCES HistoryLog (id);

ALTER TABLE HistoryLogEntry
  ADD CONSTRAINT FK_TripMember_TO_HistoryLogEntry
    FOREIGN KEY (tripMemberId)
    REFERENCES TripMember (id);

ALTER TABLE Subtrip
  ADD CONSTRAINT FK_Itinerary_TO_Subtrip
    FOREIGN KEY (itineraryId)
    REFERENCES Itinerary (id);

ALTER TABLE Activity
  ADD CONSTRAINT FK_Itinerary_TO_Activity
    FOREIGN KEY (itineraryId)
    REFERENCES Itinerary (id);

ALTER TABLE Activity
  ADD CONSTRAINT FK_TripMember_TO_Activity
    FOREIGN KEY (tripMemberId)
    REFERENCES TripMember (id);

ALTER TABLE Activity
  ADD CONSTRAINT FK_Subtrip_TO_Activity
    FOREIGN KEY (subtripId)
    REFERENCES Subtrip (id);

ALTER TABLE Subtrip
  ADD CONSTRAINT FK_TripMember_TO_Subtrip
    FOREIGN KEY (tripMemberId)
    REFERENCES TripMember (id);

ALTER TABLE AttendanceList
  ADD CONSTRAINT FK_Activity_TO_AttendanceList
    FOREIGN KEY (activityId)
    REFERENCES Activity (id);

ALTER TABLE HistoryLogEntry
  ADD CONSTRAINT FK_HistoryLogEntry_TO_HistoryLogEntry
    FOREIGN KEY (previousSnapshotId)
    REFERENCES HistoryLogEntry (id);

ALTER TABLE AttendanceMember
  ADD CONSTRAINT FK_AttendanceList_TO_AttendanceMember
    FOREIGN KEY (attendanceListId)
    REFERENCES AttendanceList (id);

ALTER TABLE AttendanceMember
  ADD CONSTRAINT FK_TripMember_TO_AttendanceMember
    FOREIGN KEY (tripMemberId)
    REFERENCES TripMember (id);

ALTER TABLE Activity
  ADD CONSTRAINT FK_Location_TO_Activity
    FOREIGN KEY (locationId)
    REFERENCES Location (id);

ALTER TABLE TransportDetail
  ADD CONSTRAINT FK_Activity_TO_TransportDetail
    FOREIGN KEY (fromActivityId)
    REFERENCES Activity (id);

ALTER TABLE TransportDetail
  ADD CONSTRAINT FK_Activity_TO_TransportDetail1
    FOREIGN KEY (toActivityId)
    REFERENCES Activity (id);

ALTER TABLE InventoryItem
  ADD CONSTRAINT FK_Activity_TO_InventoryItem
    FOREIGN KEY (activityId)
    REFERENCES Activity (id);

ALTER TABLE InventoryItem
  ADD CONSTRAINT FK_TripMember_TO_InventoryItem
    FOREIGN KEY (tripMemberId)
    REFERENCES TripMember (id);

ALTER TABLE Poll
  ADD CONSTRAINT FK_Activity_TO_Poll
    FOREIGN KEY (activityId)
    REFERENCES Activity (id);

ALTER TABLE Vote
  ADD CONSTRAINT FK_Poll_TO_Vote
    FOREIGN KEY (pollId)
    REFERENCES Poll (id);

ALTER TABLE Vote
  ADD CONSTRAINT FK_TripMember_TO_Vote
    FOREIGN KEY (tripMemberId)
    REFERENCES TripMember (id);

ALTER TABLE Vote
  ADD CONSTRAINT FK_RatingChoice_TO_Vote
    FOREIGN KEY (ratingChoiceId)
    REFERENCES RatingChoice (id);

ALTER TABLE TransportDetail
  ADD CONSTRAINT FK_TransportMode_TO_TransportDetail
    FOREIGN KEY (transportModeId)
    REFERENCES TransportMode (id);


-- 1. Independent Tables
INSERT INTO User (userId, firstName, lastName, email, passwordHash, nationality, policyNumber) VALUES
('user_00001', 'Ahmed', 'Ali', 'ahmed.ali@example.com', 'hashed_pw_1', 'Egyptian', 'POL1234567890123456'),
('user_00002', 'Yousef', 'Hassan', 'yousef.h@example.com', 'hashed_pw_2', 'Egyptian', 'POL1234567890123457'),
('user_00003', 'Hagar', 'Mahmoud', 'hagar.m@example.com', 'hashed_pw_3', 'Egyptian', 'POL1234567890123458');

INSERT INTO Itinerary (itineraryId, title, description, startDate, endDate) VALUES
('itin_00001', 'Japan Tech & Culture Tour', 'Exploring AI research hubs and cultural sites in Tokyo and Kyoto.', '2026-05-10', '2026-05-20'),
('itin_00002', 'Alexandria Weekend Getaway', 'Relaxing trip to the coast.', '2026-06-05', '2026-06-07');

INSERT INTO Location (placeId, name, address, latitude, longitude, timeZoneId) VALUES
('loc_00001', 'Narita International Airport', 'Narita, Chiba, Japan', 35.771986, 140.392850, 'Asia/Tokyo'),
('loc_00002', 'Akihabara Tech District', 'Akihabara, Tokyo, Japan', 35.698353, 139.771020, 'Asia/Tokyo'),
('loc_00003', 'Kyoto Imperial Palace', 'Kyoto, Japan', 35.025400, 135.762100, 'Asia/Tokyo'),
('loc_00004', 'Bibliotheca Alexandrina', 'Alexandria, Egypt', 31.208900, 29.909200, 'Africa/Cairo');

INSERT INTO TransportMode (modeId, name) VALUES
('mode_001', 'Flight'),
('mode_002', 'Bullet Train (Shinkansen)'),
('mode_003', 'Bus');

INSERT INTO RatingChoice (choiceId, label, weight) VALUES
('rate_001', 'Must Do', 5.00),
('rate_002', 'Interested', 3.00),
('rate_003', 'Neutral', 1.00),
('rate_004', 'Skip', 0.00);

-- 2. Level 1 Dependencies (Depend on Independent Tables)
INSERT INTO EmergencyContact (contactId, name, phone, email, relationship, userId) VALUES
('emg_001', 'Omar Ali', '+201001234567', 'omar@example.com', 'Brother', 1),
('emg_002', 'Mona Hassan', '+201009876543', 'mona@example.com', 'Mother', 2);

INSERT INTO Allergy (allergenId, allergen, severity, reaction, userId) VALUES
('alg_001', 'Peanuts', 'High', 'Anaphylaxis', 1),
('alg_002', 'Dust', 'Low', 'Sneezing', 3);

-- Assuming IDs 1, 2, 3 for Users and 1, 2 for Itineraries based on auto-increment
INSERT INTO TripMember (membershipId, role, joinedAt, userId, itineraryId) VALUES
('mem_001', 'Organizer', '2026-03-01 10:00:00', 1, 1),
('mem_002', 'Member', '2026-03-02 11:30:00', 2, 1),
('mem_003', 'Member', '2026-03-05 09:15:00', 3, 1),
('mem_004', 'Organizer', '2026-04-01 14:00:00', 1, 2),
('mem_005', 'Member', '2026-04-02 16:45:00', 2, 2);

INSERT INTO Invitation (invitationId, secureToken, isActive, itineraryId) VALUES
('inv_001', 'token_abc123', TRUE, 1),
('inv_002', 'token_xyz789', TRUE, 2);

INSERT INTO TripFinance (financeId, baseCurrency, budgetLimit, itineraryId) VALUES
('fin_001', 'JPY', 500000.00, 1),
('fin_002', 'EGP', 15000.00, 2);

INSERT INTO HistoryLog (logId, itineraryId) VALUES
('log_001', 1),
('log_002', 2);

-- 3. Level 2 Dependencies 
INSERT INTO Subtrip (subtripId, name, description, startTime, endTime, itineraryId, tripMemberId) VALUES
('sub_001', 'Kyoto Excursion', 'Visiting traditional temples', '2026-05-15 08:00:00', '2026-05-17 20:00:00', 1, 1);

INSERT INTO GroupFund (fundId, targetBalance, currentBalance, tripFinanceId) VALUES
('fund_001', 150000.00, 50000.00, 1),
('fund_002', 5000.00, 2000.00, 2);

INSERT INTO Expense (expenseId, amount, refundedAmount, currencyType, description, category, isNonCash, paidByKitty, tripFinanceId, tripMemberId) VALUES
('exp_001', 30000.00, 0.00, 'JPY', 'Flight Booking Deposit', 'Transport', FALSE, FALSE, 1, 1),
('exp_002', 15000.00, 0.00, 'JPY', 'Hotel Booking', 'Accommodation', FALSE, TRUE, 1, 2);

-- 4. Level 3 Dependencies 
-- Note: Subtrip uses ID 1, Locations use 1, 2, 3
INSERT INTO Activity (itemId, name, description, startTime, endTime, category, status, itineraryId, tripMemberId, subtripId, locationId) VALUES
('act_001', 'Flight Arrival', 'Arrive at Narita', '2026-05-10 14:00:00', '2026-05-10 15:30:00', 'Travel', 'Confirmed', 1, 1, NULL, 1),
('act_002', 'Akihabara Shopping', 'Buying electronics', '2026-05-11 10:00:00', '2026-05-11 16:00:00', 'Leisure', 'Planned', 1, 1, NULL, 2),
('act_003', 'Imperial Palace Visit', 'Historical tour', '2026-05-16 09:00:00', '2026-05-16 12:00:00', 'Sightseeing', 'Planned', 1, 2, 1, 3);

INSERT INTO FundContribution (contributionId, amount, timestamp, groupFundId, tripMemberId) VALUES
('cont_001', 25000.00, '2026-04-10 10:00:00', 1, 1),
('cont_002', 25000.00, '2026-04-11 11:00:00', 1, 2);

INSERT INTO ExpenseShare (shareId, amount, isPayer, expenseId, tripMemberId) VALUES
('shr_001', 10000.00, TRUE, 1, 1),
('shr_002', 10000.00, FALSE, 1, 2),
('shr_003', 10000.00, FALSE, 1, 3);

INSERT INTO HistoryLogEntry (entryId, transactionType, timestamp, changedEntityId, changedEntityType, historyLogId, tripMemberId, previousSnapshotId) VALUES
('hle_001', 'CREATE', '2026-03-01 10:05:00', 'act_001', 'Activity', 1, 1, NULL),
('hle_002', 'UPDATE', '2026-03-02 11:00:00', 'act_001', 'Activity', 1, 1, 1);

-- 5. Level 4 Dependencies 
INSERT INTO AttendanceList (totalAttendeeCount, activityId) VALUES
(3, 2), -- List for Akihabara Shopping
(2, 3); -- List for Kyoto Temple

INSERT INTO TransportDetail (transportId, distance, duration, fromActivityId, toActivityId, transportModeId) VALUES
('trans_001', 500.00, 150, 2, 3, 2); -- Akihabara to Kyoto via Bullet Train

INSERT INTO InventoryItem (itemId, name, quantity, category, isPacked, activityId, tripMemberId) VALUES
('inv_001', 'Camera', 1, 'Electronics', TRUE, 2, 1),
('inv_002', 'Travel Guide', 1, 'Misc', FALSE, 3, 2);

INSERT INTO Poll (pollId, deadline, status, isAnonymous, weightedTotal, activityId) VALUES
('poll_001', '2026-04-01 23:59:59', 'Closed', FALSE, 13.00, 2);

-- 6. Level 5 Dependencies 
INSERT INTO AttendanceMember (status, note, attendanceListId, tripMemberId) VALUES
('Attending', 'Will be there early', 1, 1),
('Attending', NULL, 1, 2),
('Attending', NULL, 1, 3),
('Attending', NULL, 2, 1),
('Not Attending', 'Feeling sick', 2, 3);

INSERT INTO Vote (voteId, voteWeight, timestamp, pollId, tripMemberId, ratingChoiceId) VALUES
('vote_001', 1.00, '2026-03-15 10:00:00', 1, 1, 1), 
('vote_002', 1.00, '2026-03-16 11:00:00', 1, 2, 1), 
('vote_003', 1.00, '2026-03-17 12:00:00', 1, 3, 2);