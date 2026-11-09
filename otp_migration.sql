-- =========================================================
-- OTP LOGIN MIGRATION
-- Run this SQL in phpMyAdmin if your database already exists
-- to add OTP code and expiration fields to the users table.
-- =========================================================

ALTER TABLE users
    ADD COLUMN otp_code VARCHAR(10) DEFAULT NULL,
    ADD COLUMN otp_expiry DATETIME DEFAULT NULL;
