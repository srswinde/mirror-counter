

/*to run:
 psql -f setup.sql
 */

CREATE DATABASE counter;
\connect counter
CREATE ROLE counter with password 'count' login;
SET ROLE counter;
CREATE TABLE IF NOT EXISTS pulses 
(
	time	timestamp,
	state	boolean
);


