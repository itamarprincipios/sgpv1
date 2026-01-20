-- Add is_vice_director column to users table
ALTER TABLE users ADD COLUMN is_vice_director TINYINT(1) DEFAULT 0 AFTER is_admin_semed;

-- Verify the changes
SELECT id, name, email, role, is_vice_director FROM users WHERE role = 'director';
