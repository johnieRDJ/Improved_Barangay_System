USE `barangay_db`;

ALTER TABLE `developer_profile`
  ADD COLUMN `user_id` INT(11) DEFAULT NULL AFTER `id`;

UPDATE `developer_profile` dp
JOIN `users` u ON u.`email` = dp.`email`
SET dp.`user_id` = u.`user_id`
WHERE dp.`user_id` IS NULL;

ALTER TABLE `developer_profile`
  ADD KEY `user_id` (`user_id`);

ALTER TABLE `developer_profile`
  ADD CONSTRAINT `developer_profile_ibfk_1`
  FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;

DELETE dp1
FROM `developer_profile` dp1
JOIN `developer_profile` dp2
  ON dp1.`user_id` = dp2.`user_id`
 AND dp1.`id` > dp2.`id`
WHERE dp1.`user_id` IS NOT NULL;

ALTER TABLE `developer_profile`
  DROP INDEX `user_id`,
  ADD UNIQUE KEY `user_id` (`user_id`);
