CREATE TABLE users(
	userName VARCHAR(64) PRIMARY KEY,
	password VARCHAR(64),
	phoneNumber INTEGER,
	isAdmin BOOLEAN NOT NULL
);

CREATE TABLE items(
	itemID SERIAL PRIMARY KEY,
	owner VARCHAR(64) references users(userName) ON DELETE CASCADE,
	category VARCHAR(64),
	itemName VARCHAR(128),
	minBid NUMERIC,
	autobuy NUMERIC
);

CREATE TABLE transaction(
	tID SERIAL PRIMARY KEY,
	location VARCHAR(128),
	pickupDate DATE NOT NULL CHECK (pickupDate >= current_date)
	returnDate DATE NOT NULL CHECK (returnDate >= pickupDate)
	itemID INTEGER REFERENCES items(itemID) ON DELETE CASCADE
);
//Note: date is yyyy-mm-dd

CREATE TABLE bids(
	biddingStatus VARCHAR(8) NOT NULL CHECK (biddingStatus IN ('REJECTED', 'PENDING', 'ACCEPTED')),
	biddingPrice NUMERIC,
	bidderName VARCHAR(64) references users(username) ON DELETE CASCADE,
tid INTEGER references transaction(tid) ON DELETE CASCADE,
PRIMARY KEY(biddername, tid)
);

Queries:
Login (Might not be done by us?)

Admin-only options
	Specify table
		Then what to delete/modify/add


Be able to filter items up for bid (added)

CREATE OR REPLACE FUNCTION filter_transactions(requested_category VARCHAR(64))
RETURNS TABLE (tid INTEGER, itemName VARCHAR(64), location VARCHAR(128), pickupDate DATE, returnDate DATE, owner VARCHAR(64), category VARCHAR(64), minBid NUMERIC, autobuy NUMERIC, highBid NUMERIC) AS $$
	BEGIN
		RETURN QUERY (
SELECT t.tid, i.itemname, t.location, t.pickupDate, t.returnDate, i.owner, i.category, i.minBid, i.autobuy, max(b.biddingPrice)
FROM transaction t natural join items i natural join bids b
WHERE i.category  = requested_category
AND t.tid NOT IN (
		SELECT B.tid
	FROM bids B
	WHERE B.biddingStatus = 'ACCEPTED'
)
GROUP BY t.tid, i.itemname, t.location, t.pickupDate, t.returnDate, i.owner, i.category, i.minBid, i.autobuy
);
	END
	$$ LANGUAGE plpgsql;

-- And make bid on item (added already)
CREATE OR REPLACE FUNCTION make_bid(newBid NUMERIC, newtid INTEGER, username VARCHAR(64))
	RETURNS void as $$
BEGIN
	IF (username IN (SELECT bidderName FROM bids WHERE tid = newtid))
	THEN 
IF (newBid >= (SELECT minBid FROM transaction natural join items WHERE newtid = tid) AND ((SELECT count(bidderName) FROM bids WHERE newtid = tid) = 0 OR newBid > (SELECT max(biddingPrice) FROM bids WHERE newtid = tid)))
THEN 
PERFORM retract_bid(username,newtid);
END IF;
	END IF;
IF (newBid >= (SELECT minBid FROM transaction natural join items WHERE newtid = tid) AND ((SELECT count(bidderName) FROM bids WHERE newtid = tid) = 0 OR newBid > (SELECT max(biddingPrice) FROM bids WHERE newtid = tid)))
	THEN
	INSERT INTO bids VALUES ('PENDING', newBid, username, newtid);
	END IF;
END
$$ LANGUAGE plpgsql;


	And retract bid on item (added already)
CREATE OR REPLACE FUNCTION retract_bid(username VARCHAR(64), targetTid INTEGER)
RETURNS void AS $$
	BEGIN
		DELETE FROM bids
		WHERE bidderName = username
		AND tid = targetTid
AND (biddingStatus = 'REJECTED' OR
		biddingStatus = 'PENDING');
	END
	$$ LANGUAGE plpgsql;

LENDING

--Create new item and transaction details for loan (added)
CREATE OR REPLACE FUNCTION add_item_for_bidding(userName VARCHAR(64), category VARCHAR(64), itemName VARCHAR(128), minBid NUMERIC, autobuy NUMERIC, location VARCHAR(128), pickupDate DATE,returnDate DATE)
	RETURNS void AS $$
	BEGIN
		WITH rows AS (
		INSERT INTO items VALUES (DEFAULT, userName, category, itemName, minBid, autobuy) RETURNING itemID)
		INSERT INTO transaction VALUES (DEFAULT, location, pickupDate, returnDate, (select itemID from rows));
	END
	$$ LANGUAGE plpgsql;

-- Create new transaction for existing item (added)
CREATE OR REPLACE FUNCTION add_transaction_on_existing_item (newLocation VARCHAR(64), newPickUpDate DATE, newReturnDate DATE, newItemID INTEGER)
	RETURNS void AS $$
	BEGIN
		INSERT INTO transaction VALUES (DEFAULT, newLocation, newPickUpDate, newReturnDate, newItemID);
	END
	$$ LANGUAGE plpgsql;

--See all currently loaned out items of a certain user (added)
CREATE OR REPLACE FUNCTION all_current_loans_accepted (ownerName VARCHAR(64))
	RETURNS TABLE (itemName VARCHAR(64), bidderName V




ARCHAR(64), returnDate Date) AS $$
	BEGIN
		RETURN Query (
			SELECT I1.itemName, B1.bidderName, T1.returnDate
FROM bids B1 natural join transaction T1 natural join items I1
WHERE I1.owner = ownerName
AND B1.biddingStatus = 'ACCEPTED'
);
END
$$ LANGUAGE plpgsql;

-- See all items that are currently on offer (added)
CREATE OR REPLACE FUNCTION all_current_loans_pending (userName VARCHAR(64))
RETURNS TABLE (itemID INTEGER, itemName VARCHAR(64), category VARCHAR(64), minBid NUMERIC, autobuy NUMERIC, location VARCHAR(64), pickupDate DATE, returnDate DATE, bidderName VARCHAR(64), maxBid NUMERIC) AS $$
BEGIN
	RETURN Query (
		SELECT F1.itemID, F1.itemName, F1.category, F1.minBid, F1.autobuy, F1.location, F1.pickupDate, F1.returnDate,F1.bidderName,F1.biddingPrice
		FROM (items natural join transaction natural join bids) AS F1
		WHERE F1.owner = userName
		AND F1.biddingStatus = 'PENDING' 
		AND F1.bidderName = (SELECT B.bidderName
					FROM bids B
					WHERE B.tid = F1.tid
					ORDER BY B.biddingPrice DESC
					LIMIT 1
					)
);
END
$$ LANGUAGE plpgsql;


-- Be able to edit transaction details (added)
CREATE OR REPLACE FUNCTION edit_transactions (transactionID INTEGER, newPickupDate DATE, newReturnDate DATE)
	RETURNS void AS $$
	BEGIN
		UPDATE transaction
		SET pickupDate = newPickupDate,
		returnDate = newReturnDate
		WHERE tID = transactionID;
	END
$$ LANGUAGE plpgsql;

-- List items under user (added)
CREATE OR REPLACE FUNCTION check_bid(username VARCHAR(64))
RETURNS TABLE (itemID INTEGER, itemName VARCHAR(64), category VARCHAR(64), minBid NUMERIC, autobuy NUMERIC) AS $$
	BEGIN
		RETURN QUERY (
SELECT I.itemID, I.itemName, I.category, I.minBid, I.autobuy
FROM items I
WHERE owner = username
);
	END
	$$ LANGUAGE plpgsql;

--Edit items details (added)
CREATE OR REPLACE FUNCTION edit_items (itemRequest INTEGER, newCategory VARCHAR(64), newItemName VARCHAR(64), newMinBid NUMERIC, newAutobuy NUMERIC)
	RETURNS void AS $$
	BEGIN
		UPDATE items
		SET category = newCategory,
		itemName = newItemName,
		minBid = newMinBid,
		autobuy = newAutobuy
		WHERE itemID = itemRequest;
	END
$$ LANGUAGE plpgsql;


-- Accept a bid (added)
CREATE OR REPLACE FUNCTION accept_loan (newTID INTEGER, newBidderName VARCHAR(64))
	RETURNS void AS $$
	BEGIN
		UPDATE bids
		SET biddingStatus = 'ACCEPTED'
		WHERE tID = newTID
		AND bidderName = newBidderName;
	END
	$$ LANGUAGE plpgsql;

Delete item (added)
CREATE OR REPLACE FUNCTION delete_item (newItemID INTEGER)
	RETURNS void AS $$
	BEGIN
		DELETE FROM items
		WHERE itemID = newItemID;
	END
	$$ LANGUAGE plpgsql;

-- Reject a bid (added)
CREATE OR REPLACE FUNCTION reject_loan (newTID INTEGER, newBidderName VARCHAR(64))
	RETURNS TRIGGER AS $$
	BEGIN
		UPDATE bids
		SET biddingStatus = 'REJECTED'
		WHERE tID = newTID
		AND bidderName = newBidderName;
	END
	$$ LANGUAGE plpgsql;		



--Checking if price is lower than min bid, reject
Check if price higher than max bid, auto accept
Triggers to update the bids

CREATE OR REPLACE FUNCTION checkBids()
RETURNS TRIGGER AS $$
BEGIN
	If NEW.biddingPrice >= 
(SELECT autobuy 
FROM (transaction natural join items) AS F1 
WHERE F1.tID = NEW.tID)
	THEN
		UPDATE bids
SET biddingStatus = 'ACCEPTED'
WHERE NEW.tID = tID AND bidderName = NEW.bidderName;
END IF;

UPDATE bids
SET biddingStatus = 'REJECTED'
WHERE NEW.tID = tID AND bidderName <> NEW.bidderName;

RETURN NULL;
END;
$$ LANGUAGE plpgsql;
	
CREATE TRIGGER checkBids
AFTER INSERT
ON bids
FOR EACH ROW
EXECUTE PROCEDURE checkBids();

-- admin find all user
CREATE OR REPLACE FUNCTION admin_select_user()
RETURNS TABLE (userName VARCHAR(64), phoneNumber INTEGER, isAdmin BOOLEAN) AS $$
BEGIN
	RETURN QUERY (
		SELECT * FROM users
	);
END;
$$ LANGUAGE plpgsql;

-- admin find all items
CREATE OR REPLACE FUNCTION admin_select_items()
RETURNS TABLE (itemID INTEGER, owner VARCHAR(64), category VARCHAR(64), itemName VARCHAR(64), minBid NUMERIC, autobuy NUMERIC) AS $$
BEGIN
	RETURN QUERY (
	SELECT * FROM items
);
END;
$$ LANGUAGE plpgsql;

-- admin find all transactions
CREATE OR REPLACE FUNCTION admin_select_transaction()
RETURNS TABLE (tid INTEGER, location VARCHAR(128), pickupDate DATE, returnDate DATE, itemID INTEGER) AS $$
BEGIN
RETURN QUERY (
		SELECT * FROM transaction
	);
END;
$$ LANGUAGE plpgsql;

-- admin find all bids
CREATE OR REPLACE FUNCTION admin_select_bids()
RETURNS TABLE (biddingStatus VARCHAR(64), biddingPrice VARCHAR(64), bidderName VARCHAR(64), tid INTEGER) AS $$
BEGIN
	RETURN QUERY (
	SELECT * FROM bids
);
END;
$$ LANGUAGE plpgsql;-- admin add user
CREATE OR REPLACE FUNCTION admin_add_user (userName VARCHAR(64), password VARCHAR(64), phoneNumber INTEGER, isAdmin BOOLEAN)
RETURNS void AS $$
BEGIN
	INSERT INTO users VALUES (userName, password, phoneNumber, isAdmin);
END;
$$ LANGUAGE plpgsql;

-- admin remove user
CREATE OR REPLACE FUNCTION admin_remove_user (newUserName VARCHAR(64))
	RETURNS void AS $$
	BEGIN
		DELETE FROM users
	WHERE userName = newUserName;
END;
$$ LANGUAGE plpgsql;

-- admin edit user
CREATE OR REPLACE FUNCTION admin_edit_user (newUserName VARCHAR(64), newPhoneNumber INTEGER, NewIsAdmin BOOLEAN)
	RETURNS void AS $$
	BEGIN
	UPDATE users
	SET phoneNumber = newPhoneNumber,
	isAdmin = newIsAdmin
	WHERE userName = newUserName;
END;
$$ LANGUAGE plpgsql;

-- admin add items
CREATE OR REPLACE FUNCTION admin_add_items (itemID INTEGER, owner VARCHAR(64), category VARCHAR(64), itemName VARCHAR(64), minBid INTEGER, autobuy INTEGER)
RETURNS void AS $$
BEGIN
	INSERT INTO items VALUES (itemID, owner, category, itemName, minBid, autobuy);
END;
$$ LANGUAGE plpgsql;

-- admin remove items
CREATE OR REPLACE FUNCTION admin_remove_items (itemRequest INTEGER)
	RETURNS void AS $$
	BEGIN
		IF owner in (SELECT username FROM users)
		THEN
		DELETE FROM items
	WHERE itemID = itemRequest;
	END IF;
END;
$$ LANGUAGE plpgsql;

-- admin edit items
CREATE OR REPLACE FUNCTION admin_edit_items (itemRequest INTEGER, newCategory VARCHAR(64), newItemName VARCHAR(64), newMinBid NUMERIC, newAutobuy NUMERIC, owner VARCHAR(64))
	RETURNS void AS $$
	BEGIN
		IF owner in (SELECT username FROM users)
		THEN
UPDATE items
		SET category = newCategory,
		itemName = newItemName,
		minBid = newMinBid,
		autobuy = newAutobuy
		WHERE itemID = itemRequest;
		END IF;
	END;
$$ LANGUAGE plpgsql;


-- admin add bids
CREATE OR REPLACE FUNCTION admin_add_bids (biddingStatus VARCHAR(8), biddingPrice NUMERIC, bidderName VARCHAR(64))
RETURNS void AS $$
BEGIN
	IF bidderName IN (SELECT userName FROM users)
THEN
	INSERT INTO bids VALUES (biddingStatus, biddingPrice, bidderName);
	END IF;
END;
$$ LANGUAGE plpgsql;

-- admin remove bids
CREATE OR REPLACE FUNCTION admin_remove_bids (newBiddingStatus VARCHAR(8), newBiddingPrice NUMERIC, newBidderName VARCHAR(64))
	RETURNS void AS $$
	BEGIN
		DELETE FROM bids
	WHERE userName = newUserName;
END;
$$ LANGUAGE plpgsql;

-- admin edit bids
CREATE OR REPLACE FUNCTION admin_edit_bids (newTID INTEGER, newBiddingStatus VARCHAR(8), newBiddingPrice NUMERIC, newBidderName VARCHAR(64))
	RETURNS void AS $$
	BEGIN
IF bidderName IN (SELECT userName FROM users)
THEN
	UPDATE bids
	SET biddingStatus = newBiddingStatus,
	biddingPrice = newBiddingPrice,
	bidderName = newBidderName
	WHERE tID = newTID;
END IF;
END;
$$ LANGUAGE plpgsql;
-- admin add transaction
CREATE OR REPLACE FUNCTION admin_add_transaction (location VARCHAR(64), pickupDate DATE, returnDate DATE, itemID INTEGER)
RETURNS void AS $$
BEGIN
	IF itemID IN (SELECT itemID FROM items)
THEN
	INSERT INTO transaction VALUES (location, pickupDate, returnDate, itemID);
	END IF;
END;
$$ LANGUAGE plpgsql;

-- admin remove transaction
CREATE OR REPLACE FUNCTION admin_remove_transaction (newLocation VARCHAR(64), newPickupDate DATE, newReturnDate DATE, newItemID INTEGER)
	RETURNS void AS $$
	BEGIN
		DELETE FROM items
	WHERE itemID = newItemID;
END;
$$ LANGUAGE plpgsql;

-- admin edit transaction
CREATE OR REPLACE FUNCTION admin_edit_transaction (newTID INTEGER, newLocation VARCHAR(64), newPickupDate DATE, newReturnDate DATE, newItemID INTEGER)
	RETURNs void AS $$
	BEGIN
IF newItemID IN (SELECT itemID FROM items)
THEN
	UPDATE items
	SET location = newLocation,
	pickupDate = newPickupDate,
	returnDate = newReturnDate
	WHERE itemID = newItemID
AND tID = newTID;
END IF;
END;
$$ LANGUAGE plpgsql;

