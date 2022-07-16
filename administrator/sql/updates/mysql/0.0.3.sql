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
(20, 'Buat Tugas', 'Dosen membuat Tugas'),
(30, 'Kirim Tugas','Tugas sudah dikirm ke Mahasiswa'),
(40, 'Koreksi Jawaban', 'Mahasiswa sudah submit jawaban'),
(50, 'Pending Nilai', 'Nilai dipending Dosen'),
(60, 'Selesai', 'Dosen sudah memberikan nilai');