-- Add is_admin_semed column to users table
ALTER TABLE users ADD COLUMN is_admin_semed TINYINT(1) DEFAULT 0 AFTER role;

-- Update existing Admin SEMED user (semed@sgp.com)
UPDATE users SET is_admin_semed = 1 WHERE email = 'semed@sgp.com';

-- Verify the changes
SELECT id, name, email, role, is_admin_semed FROM users WHERE role = 'semed';
