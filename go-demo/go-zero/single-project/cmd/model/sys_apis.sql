CREATE TABLE `sys_apis` (
                            `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
                            `created_at` datetime(3) DEFAULT NULL,
                            `updated_at` datetime(3) DEFAULT NULL,
                            `deleted_at` datetime(3) DEFAULT NULL,
                            `path` varchar(191) DEFAULT NULL COMMENT 'api路径',
                            `description` varchar(191) DEFAULT NULL COMMENT 'api中文描述',
                            `api_group` varchar(191) DEFAULT NULL COMMENT 'api组',
                            `method` varchar(191) DEFAULT 'POST' COMMENT '方法',
                            PRIMARY KEY (`id`),
                            KEY `idx_sys_apis_deleted_at` (`deleted_at`)
) ENGINE=InnoDB AUTO_INCREMENT=99 DEFAULT CHARSET=utf8mb4