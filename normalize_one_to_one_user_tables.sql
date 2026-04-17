USE `barangay_db`;

SELECT 'user_auth' AS table_name, user_id, COUNT(*) AS row_count
FROM `user_auth`
WHERE user_id IS NOT NULL
GROUP BY user_id
HAVING COUNT(*) > 1
UNION ALL
SELECT 'user_profiles', user_id, COUNT(*)
FROM `user_profiles`
WHERE user_id IS NOT NULL
GROUP BY user_id
HAVING COUNT(*) > 1
UNION ALL
SELECT 'residency', user_id, COUNT(*)
FROM `residency`
WHERE user_id IS NOT NULL
GROUP BY user_id
HAVING COUNT(*) > 1
UNION ALL
SELECT 'password_resets', user_id, COUNT(*)
FROM `password_resets`
WHERE user_id IS NOT NULL
GROUP BY user_id
HAVING COUNT(*) > 1;

ALTER TABLE `user_auth`
  ADD UNIQUE KEY `unique_user_auth_user` (`user_id`);

ALTER TABLE `user_profiles`
  ADD UNIQUE KEY `unique_user_profiles_user` (`user_id`);

ALTER TABLE `residency`
  ADD UNIQUE KEY `unique_residency_user` (`user_id`);

ALTER TABLE `password_resets`
  ADD UNIQUE KEY `unique_password_resets_user` (`user_id`);
