-- Link employees to users table to allow employees to log in

USE nineventory;

-- Add user_id to employees table
ALTER TABLE employees
ADD COLUMN user_id INT NULL UNIQUE AFTER id,
ADD CONSTRAINT fk_user_id FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL;

-- This statement ensures that no two employees can be linked to the same user account.
-- The ON DELETE SET NULL part means that if a user account is deleted,
-- the corresponding employee's user_id will be set to NULL,
-- but the employee record itself will not be deleted.

-- You might want to link existing employees to existing user accounts manually.
-- For example, to link employee with id 1 to user with id 2:
-- UPDATE employees SET user_id = 2 WHERE id = 1;
