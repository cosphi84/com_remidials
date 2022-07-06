CREATE TABLE IF NOT EXISTS `#__remidials` (
    `id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
    `catid` VARCHAR(10) NOT NULL DEFAULT '',
    `tahun_ajaran` VARCHAR(10) NOT NULL DEFAULT '',
    `nilai_id` INT(10) UNSIGNED NOT NULL,
    `state` TINYINT(4) NOT NULL DEFAULT 1,
    `dosen_id` INT(10) NOT NULL,
    `auth_fakultas` TINYINT(4) UNSIGNED NOT NULL DEFAULT 0,
    `nilai_awal` TINYINT(4) UNSIGNED NOT NULL DEFAULT 0,
    `nilai_remidial` TINYINT(4) NULL DEFAULT NULL,
    `update_master_nilai` TINYINT(2) NOT NULL DEFAULT 0,
    `input_by` INT(10) NOT NULL DEFAULT 0,
    `input_date` DATETIME NULL DEFAULT NULL,
    `checked_out` INT(10) UNSIGNED  NOT NULL DEFAULT 0,
	`checked_out_time` 	DATETIME,
	`created_by` INT(10) NULL DEFAULT NULL,
	`created_date` DATETIME	NULL DEFAULT NULL,  	
    `asset_id` INT(10) UNSIGNED NOT NULL DEFAULT 0,
	PRIMARY KEY (`id`),
    CONSTRAINT `rem_nilai` FOREIGN KEY (`nilai_id`) REFERENCES `#__siak_nilai` (`id`) ON UPDATE CASCADE ON DELETE RESTRICT
)ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 DEFAULT COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `#__remidial_status` 
(
    `id` TINYINT(4) UNSIGNED NOT NULL AUTO_INCREMENT,
    `status` TINYINT(4) UNSIGNED NOT NULL,
    `text` VARCHAR(20) NULL DEFAULT NULL,
    `desc` VARCHAR(50) NOT NULL DEFAULT '',
    `catid` INT(10) UNSIGNED NULL DEFAULT NULL,
    PRIMARY KEY(`id`)
)ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 DEFAULT COLLATE=utf8mb4_unicode_ci;

INSERT INTO `#__remidial_status` (`status`, `text`,`desc`) VALUES 
(10, 'Diajukan', 'Pengajuan Perbaikan Nilai'),
(20, 'Persiapan Materi', 'Dosen membuat Materi untuk remidial / SP'),
(30, 'Submit Materi','Dosen sudah selesai mempersiapkan materi Remidi / SP'),
(40, 'Proses Remidi / SP', 'Proses KBM pendek atau pengerjaan Tugas Remedial'),
(50, 'Pending Nilai', 'Nilai dipending Dosen'),
(60, 'Selesai', 'Dosen sudah memberikan nilai');