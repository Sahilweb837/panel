-- Update existing tables with new columns if they do not exist

-- 1. Add profile_pic to users table
ALTER TABLE users ADD COLUMN profile_pic VARCHAR(255) NULL AFTER last_activity_at;

-- 2. Add attachment to messages table
ALTER TABLE messages ADD COLUMN attachment VARCHAR(255) NULL AFTER body;

-- 3. Ensure that all Interns are set to Active status
UPDATE students SET status = 1 WHERE student_type = 'Regular (Internship)';
