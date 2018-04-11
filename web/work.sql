
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

CREATE OR REPLACE FUNCTION date_check() RETURNS trigger AS $$
	BEGIN
		IF NEW.pickupDate < current_date
THEN
	RAISE EXCEPTION 'Loan date cannot be set in the past!';
END IF;
RETURN NEW;
	END;
$$ LANGUAGE plpgsql;

CREATE TRIGGER date_check BEFORE INSERT OR UPDATE ON transaction
	FOR EACH ROW EXECUTE PROCEDURE date_check();

CREATE OR REPLACE FUNCTION item_owner_check() RETURNS trigger AS $$
	BEGIN
		IF NEW.bidderName in (
SELECT I.owner 
FROM items I
WHERE I.itemID in (
	SELECT T.itemID
	FROM transaction T 
	WHERE T.tID = NEW.tid
)
)
THEN
	RAISE EXCEPTION 'Cannot bid on your own item!';
END IF;
RETURN NEW;
	END;
$$ LANGUAGE plpgsql;

CREATE TRIGGER item_owner_check BEFORE INSERT OR UPDATE ON bids
	FOR EACH ROW EXECUTE PROCEDURE item_owner_check();

CREATE OR REPLACE FUNCTION select_active_transactions()
RETURNS TABLE (tid INTEGER, itemName VARCHAR(64), location VARCHAR(128), pickupDate DATE, returnDate DATE, owner VARCHAR(64), category VARCHAR(64), minBid NUMERIC, autobuy NUMERIC, highBid NUMERIC) AS $$
	BEGIN
		RETURN QUERY (
SELECT t.tid, i.itemname, t.location, t.pickupDate, t.returnDate, i.owner, i.category, i.minBid, i.autobuy, max(b.biddingPrice)
FROM transaction t natural join items i natural left outer join bids b
WHERE t.tid NOT IN (
		SELECT B.tid
	FROM bids B
	WHERE B.biddingStatus = 'ACCEPTED'
)
GROUP BY t.tid, i.itemname, t.location, t.pickupDate, t.returnDate, i.owner, i.category, i.minBid, i.autobuy
);
	END
	$$ LANGUAGE plpgsql;

admin_add_user('$_POST[uname]', '$password', '$_POST[phn]', 'False')


CREATE OR REPLACE FUNCTION filter_transactions(requested_category VARCHAR(64))
RETURNS TABLE (tid INTEGER, itemName VARCHAR(64), location VARCHAR(128), pickupDate DATE, returnDate DATE, owner VARCHAR(64), category VARCHAR(64), minBid NUMERIC, autobuy NUMERIC, highBid NUMERIC) AS $$
	BEGIN
		RETURN QUERY (
SELECT t.tid, i.itemname, t.location, t.pickupDate, t.returnDate, i.owner, i.category, i.minBid, i.autobuy, max(b.biddingPrice)
FROM transaction t natural join items i natural left outer join bids b
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


IF (newBid >= (SELECT autobuy FROM transaction natural join items WHERE newtid = tid))
THEN UPDATE bids
	SET biddingStatus = 'REJECTED'
		WHERE tID = newTID;
INSERT INTO bids VALUES ('ACCEPTED', newBid, username, newtid);
ELSIF (newBid >= (SELECT minBid FROM transaction natural join items WHERE newtid = tid) AND ((SELECT count(bidderName) FROM bids WHERE newtid = tid) = 0 OR newBid > (SELECT max(biddingPrice) FROM bids WHERE newtid = tid)))
	THEN
	INSERT INTO bids VALUES ('PENDING', newBid, username, newtid);
	END IF;
END
$$ LANGUAGE plpgsql;


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

CREATE OR REPLACE FUNCTION check_bid_status(username VARCHAR(64))
	RETURNS TABLE (tid INTEGER, itemName VARCHAR(64), location VARCHAR(128), pickupDate DATE, returnDate DATE, owner VARCHAR(64), category VARCHAR(64), minBid NUMERIC, autobuy NUMERIC, userbiddingPrice NUMERIC, maxbiddingPrice NUMERIC, biddingStatus VARCHAR(8)) AS $$
	BEGIN 
		RETURN QUERY (
SELECT t.tid, i.itemname, t.location, t.pickupDate, t.returnDate, i.owner, i.category, i.minBid, i.autobuy, b.biddingPrice, m.maxprice, b.biddingStatus
FROM bids b natural join transaction t natural join items i natural join
	(SELECT b2.tid, max(b2.biddingPrice) as maxprice
	FROM bids b2
	GROUP BY b2.tid
) m
WHERE b.bidderName = username
);
	END
	$$ LANGUAGE plpgsql;

CREATE OR REPLACE FUNCTION add_item_for_bidding(userName VARCHAR(64), category VARCHAR(64), itemName VARCHAR(128), minBid NUMERIC, autobuy NUMERIC, location VARCHAR(128), pickupDate DATE,returnDate DATE)
	RETURNS void AS $$
	BEGIN
		WITH rows AS (
		INSERT INTO items VALUES (DEFAULT, userName, category, itemName, minBid, autobuy) RETURNING itemID)
		INSERT INTO transaction VALUES (DEFAULT, location, pickupDate, returnDate, (select itemID from rows));
	END
	$$ LANGUAGE plpgsql;

CREATE OR REPLACE FUNCTION add_transaction_on_existing_item (newLocation VARCHAR(64), newPickUpDate DATE, newReturnDate DATE, newItemID INTEGER)
	RETURNS void AS $$
	BEGIN
		INSERT INTO transaction VALUES (DEFAULT, newLocation, newPickUpDate, newReturnDate, newItemID);
	END
	$$ LANGUAGE plpgsql;

CREATE OR REPLACE FUNCTION all_current_loans_accepted (ownerName VARCHAR(64))
	RETURNS TABLE (itemID INTEGER, itemName VARCHAR(64), bidderName VARCHAR(64), phoneNumber INTEGER, location VARCHAR(128), pickupDate Date, returnDate Date) AS $$
	BEGIN
		RETURN Query (
			SELECT I1.itemID, I1.itemName, B1.bidderName, U1.phoneNumber, T1.location, T1.pickupDate, T1.returnDate
FROM bids B1 natural join transaction T1 natural join items I1 inner join users U1 ON B1.bidderName = U1.userName
WHERE I1.owner = ownerName
AND B1.biddingStatus = 'ACCEPTED'
);
END
$$ LANGUAGE plpgsql;

CREATE OR REPLACE FUNCTION all_current_items_borrowed (borrowerName VARCHAR(64))
	RETURNS TABLE (itemName VARCHAR(64), owner VARCHAR(64), phoneNumber INTEGER, returnDate Date) AS $$
	BEGIN
		RETURN Query (
			SELECT I1.itemName, I1.owner, U1.phoneNumber, T1.returnDate
FROM bids B1 natural join transaction T1 natural join items I1 inner join users U1 ON I1.owner = U1.userName
WHERE B1.biddername  = borrowerName
AND B1.biddingStatus = 'ACCEPTED'
);
END
$$ LANGUAGE plpgsql;

CREATE OR REPLACE FUNCTION all_current_loans_pending (userName VARCHAR(64))
RETURNS TABLE (transactionID INTEGER, itemID INTEGER, itemName VARCHAR(64), category VARCHAR(64), minBid NUMERIC, autobuy NUMERIC, location VARCHAR(64), pickupDate DATE, returnDate DATE, bidderName VARCHAR(64), maxBid NUMERIC) AS $$
BEGIN
RETURN Query (
		SELECT T1.tID, I1.itemID, I1.itemName, I1.category, I1.minBid, I1.autobuy, T1.location, T1.pickupDate, T1.returnDate,B1.bidderName,B1.biddingPrice
		FROM items I1 natural join transaction T1 left outer join bids B1 on T1.tID = b1.tid
		WHERE I1.owner = userName
		AND B1.biddingStatus = 'PENDING' 
		UNION
		SELECT T1.tID, I1.itemID, I1.itemName, I1.category, I1.minBid, I1.autobuy, T1.location, T1.pickupDate, T1.returnDate,B1.bidderName,B1.biddingPrice
		FROM items I1 natural join transaction T1 left outer join bids B1 on T1.tID = b1.tid
		WHERE I1.owner = userName
		AND B1.biddingStatus IS NULL
);
END
$$ LANGUAGE plpgsql;


CREATE OR REPLACE FUNCTION edit_transactions (transactionID INTEGER, newLocation VARCHAR, newPickupDate DATE, newReturnDate DATE)
	RETURNS void AS $$
	BEGIN
		UPDATE transaction
		SET location = newLocation,
pickupDate = newPickupDate,
		returnDate = newReturnDate
		WHERE tID = transactionID;
	END
$$ LANGUAGE plpgsql;

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


CREATE OR REPLACE FUNCTION accept_loan (newTID INTEGER, newBidderName VARCHAR(64))
	RETURNS void AS $$
	BEGIN
		IF newBidderName IS NOT NULL
		THEN
		UPDATE bids
		SET biddingStatus = (CASE 
						WHEN bidderName = newBidderName THEN 'ACCEPTED'
						ELSE 'REJECTED'
					END)
WHERE tid = newTID;
		ELSE RAISE EXCEPTION 'No bidders on this item!';
		END IF;
	END
	$$ LANGUAGE plpgsql;

CREATE OR REPLACE FUNCTION delete_item (newItemID INTEGER)
	RETURNS void AS $$
	BEGIN
		DELETE FROM items
		WHERE itemID = newItemID;
	END
	$$ LANGUAGE plpgsql;

CREATE OR REPLACE FUNCTION reject_loan (newTID INTEGER, newBidderName VARCHAR(64))
	RETURNS void AS $$
	BEGIN
		IF newBidderName IS NOT NULL THEN
		UPDATE bids
		SET biddingStatus = 'REJECTED'
		WHERE tID = newTID
		AND bidderName = newBidderName;
		ELSE RAISE EXCEPTION 'No bidders on this item!';
		END IF;
	END
	$$ LANGUAGE plpgsql;		

CREATE OR REPLACE FUNCTION admin_select_user()
RETURNS TABLE (userName VARCHAR(64), phoneNumber INTEGER, isAdmin BOOLEAN) AS $$
BEGIN
	RETURN QUERY (
		SELECT * FROM users
	);
END;
$$ LANGUAGE plpgsql;

CREATE OR REPLACE FUNCTION admin_select_items()
RETURNS TABLE (itemID INTEGER, owner VARCHAR(64), category VARCHAR(64), itemName VARCHAR(64), minBid NUMERIC, autobuy NUMERIC) AS $$
BEGIN
	RETURN QUERY (
	SELECT * FROM items i
	ORDER BY i.itemID
);
END;
$$ LANGUAGE plpgsql;

CREATE OR REPLACE FUNCTION admin_select_transaction()
RETURNS TABLE (tid INTEGER, location VARCHAR(128), pickupDate DATE, returnDate DATE, itemID INTEGER) AS $$
BEGIN
RETURN QUERY (
		SELECT * FROM transaction t
		ORDER BY t.tid
	);
END;
$$ LANGUAGE plpgsql;

CREATE OR REPLACE FUNCTION admin_select_bids()
RETURNS TABLE (biddingStatus VARCHAR(64), biddingPrice NUMERIC, bidderName VARCHAR(64), tid INTEGER) AS $$
BEGIN
	RETURN QUERY (
	SELECT * FROM bids b
	ORDER BY b.tid, b.biddingStatus
);
END;
$$ LANGUAGE plpgsql;CREATE OR REPLACE FUNCTION admin_add_user (userName VARCHAR(64), password VARCHAR(64), phoneNumber INTEGER, isAdmin BOOLEAN)
RETURNS void AS $$
BEGIN
	INSERT INTO users VALUES (userName, password, phoneNumber, isAdmin);
END;
$$ LANGUAGE plpgsql;

CREATE OR REPLACE FUNCTION admin_remove_user (newUserName VARCHAR(64))
	RETURNS void AS $$
	BEGIN
		DELETE FROM users
	WHERE userName = newUserName;
END;
$$ LANGUAGE plpgsql;

CREATE OR REPLACE FUNCTION admin_edit_user (newUserName VARCHAR(64), newPhoneNumber INTEGER, NewIsAdmin BOOLEAN)
	RETURNS void AS $$
	BEGIN
	UPDATE users
	SET phoneNumber = newPhoneNumber,
	isAdmin = newIsAdmin
	WHERE userName = newUserName;
END;
$$ LANGUAGE plpgsql;

CREATE OR REPLACE FUNCTION admin_add_items (itemID INTEGER, owner VARCHAR(64), category VARCHAR(64), itemName VARCHAR(64), minBid INTEGER, autobuy INTEGER)
RETURNS void AS $$
BEGIN
	INSERT INTO items VALUES (itemID, owner, category, itemName, minBid, autobuy);
END;
$$ LANGUAGE plpgsql;

CREATE OR REPLACE FUNCTION admin_remove_items (itemRequest INTEGER)
	RETURNS void AS $$
	BEGIN
		DELETE FROM items
	WHERE itemID = itemRequest;
END;
$$ LANGUAGE plpgsql;

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

CREATE OR REPLACE FUNCTION admin_add_bids (biddingStatus VARCHAR(8), biddingPrice NUMERIC, bidderName VARCHAR(64), tid INTEGER)
RETURNS void AS $$
BEGIN
	IF bidderName IN (SELECT userName FROM users) AND tid IN (SELECT t.tID FROM transaction t)
THEN
	INSERT INTO bids VALUES (biddingStatus, biddingPrice, bidderName, tid);
	END IF;
END;
$$ LANGUAGE plpgsql;

CREATE OR REPLACE FUNCTION admin_remove_bids (newtid INTEGER, newBiddingStatus VARCHAR(8), newBiddingPrice NUMERIC, newBidderName VARCHAR(64))
	RETURNS void AS $$
	BEGIN
		DELETE FROM bids
	WHERE tid = newtid AND bidderName = newBidderName AND biddingPrice = newBiddingPrice;
END;
$$ LANGUAGE plpgsql;

CREATE OR REPLACE FUNCTION admin_edit_bids (newTID INTEGER, newBiddingStatus VARCHAR(8), newBiddingPrice NUMERIC, newBidderName VARCHAR(64))
	RETURNS void AS $$
	BEGIN
	UPDATE bids
	SET biddingStatus = newBiddingStatus,
	biddingPrice = newBiddingPrice
	WHERE tID = newTID AND bidderName = newBidderName;
END;
$$ LANGUAGE plpgsql;
CREATE OR REPLACE FUNCTION admin_add_transaction (tid INTEGER, location VARCHAR(64), pickupDate DATE, returnDate DATE, itemID INTEGER)
RETURNS void AS $$
BEGIN
	IF itemID IN (SELECT i.itemID FROM items i)
THEN
	INSERT INTO transaction VALUES (tid, location, pickupDate, returnDate, itemID);
	END IF;
END;
$$ LANGUAGE plpgsql;

CREATE OR REPLACE FUNCTION admin_remove_transaction (newTID INTEGER, newLocation VARCHAR(64), newPickupDate DATE, newReturnDate DATE, newItemID INTEGER)
	RETURNS void AS $$
	BEGIN
		DELETE FROM transaction
	WHERE tID = newTID;
END;
$$ LANGUAGE plpgsql;

CREATE OR REPLACE FUNCTION admin_edit_transaction (newTID INTEGER, newLocation VARCHAR(64), newPickupDate DATE, newReturnDate DATE, newItemID INTEGER)
	RETURNs void AS $$
	BEGIN
IF newItemID IN (SELECT itemID FROM items)
THEN
	UPDATE transaction
	SET location = newLocation,
	pickupDate = newPickupDate,
	returnDate = newReturnDate,
	itemID = newItemID
WHERE tID = newTID;
END IF;
END;
$$ LANGUAGE plpgsql;