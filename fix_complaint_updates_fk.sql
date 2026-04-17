USE `barangay_db`;

SELECT cu.*
FROM `complaint_updates` cu
LEFT JOIN `complaints` c ON c.`complaint_id` = cu.`complaint_id`
WHERE c.`complaint_id` IS NULL;

SELECT cu.*
FROM `complaint_updates` cu
LEFT JOIN `users` u ON u.`user_id` = cu.`actor_user_id`
WHERE cu.`actor_user_id` IS NOT NULL
  AND u.`user_id` IS NULL;

ALTER TABLE `complaint_updates`
  ADD CONSTRAINT `fk_complaint_updates_complaint`
  FOREIGN KEY (`complaint_id`) REFERENCES `complaints` (`complaint_id`)
  ON DELETE CASCADE,
  ADD CONSTRAINT `fk_complaint_updates_actor`
  FOREIGN KEY (`actor_user_id`) REFERENCES `users` (`user_id`)
  ON DELETE SET NULL;
