-- =========================================================
-- PREMIUM FEATURES MIGRATION
-- Run this SQL in phpMyAdmin if your database already exists.
-- It adds discount and coupon tracking columns to the orders table.
-- =========================================================

ALTER TABLE orders
    ADD COLUMN discount_amount DECIMAL(8,2) DEFAULT 0.00 AFTER grand_total,
    ADD COLUMN coupon_code VARCHAR(50) DEFAULT NULL AFTER discount_amount;
