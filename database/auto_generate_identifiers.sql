-- =========================================================================
-- SQL Scripts for Automatic Admission Number and Roll Number Generation
-- =========================================================================

-- 1. Create a Trigger to Automatically Set Admission Number and Roll Number
-- This trigger will run BEFORE INSERT on the students table.
-- If the admission_no or roll_no is not provided (or is empty), it generates them.

DELIMITER //

CREATE TRIGGER before_insert_student_auto_generate
BEFORE INSERT ON students
FOR EACH ROW
BEGIN
    DECLARE next_id INT;
    DECLARE last_num INT;
    DECLARE next_admission_no VARCHAR(50);
    DECLARE next_roll_no INT;

    -- Find the next auto-increment ID to use as a fallback base
    SELECT AUTO_INCREMENT INTO next_id
    FROM information_schema.tables
    WHERE table_name = 'students'
      AND table_schema = DATABASE();

    -- Generate Admission Number if not provided
    IF NEW.admission_no IS NULL OR NEW.admission_no = '' THEN
        -- Find the last numeric part of admission numbers matching 'NT-ENR-%'
        SELECT CAST(SUBSTRING(admission_no, 8) AS UNSIGNED) INTO last_num
        FROM students
        WHERE admission_no LIKE 'NT-ENR-%'
        ORDER BY CAST(SUBSTRING(admission_no, 8) AS UNSIGNED) DESC
        LIMIT 1;

        IF last_num IS NOT NULL THEN
            SET next_admission_no = CONCAT('NT-ENR-', LPAD(last_num + 1, 3, '0'));
        ELSE
            SET next_admission_no = CONCAT('NT-ENR-', LPAD(next_id, 3, '0'));
        END IF;

        SET NEW.admission_no = next_admission_no;
    END IF;

    -- Generate Roll Number if not provided
    IF NEW.roll_no IS NULL OR NEW.roll_no = '' THEN
        -- Find the highest numeric roll number
        SELECT CAST(roll_no AS UNSIGNED) INTO next_roll_no
        FROM students
        WHERE roll_no REGEXP '^[0-9]+$'
        ORDER BY CAST(roll_no AS UNSIGNED) DESC
        LIMIT 1;

        IF next_roll_no IS NOT NULL THEN
            SET NEW.roll_no = CAST(next_roll_no + 1 AS CHAR);
        ELSE
            SET NEW.roll_no = CAST(next_id AS CHAR);
        END IF;
    END IF;
END;
//

DELIMITER ;
