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