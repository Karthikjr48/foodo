-- =========================================================
-- RAZORPAY MIGRATION
-- Run this SQL in phpMyAdmin if your database already exists
-- and you don't want to re-import the full database.sql
-- =========================================================

ALTER TABLE orders
    ADD COLUMN razorpay_order_id VARCHAR(100) DEFAULT NULL AFTER order_status,
    ADD COLUMN razorpay_payment_id VARCHAR(100) DEFAULT NULL AFTER razorpay_order_id;
