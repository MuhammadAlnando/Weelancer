-- phpMyAdmin SQL Dump
-- version 4.8.0.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Waktu pembuatan: 28 Jul 2024 pada 12.16
-- Versi server: 10.1.32-MariaDB
-- Versi PHP: 7.2.5

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `jobify`
--

-- --------------------------------------------------------

--
-- Struktur dari tabel `applications`
--

CREATE TABLE `applications` (
  `id` int(10) UNSIGNED NOT NULL,
  `job_id` int(10) UNSIGNED NOT NULL,
  `user_id` int(10) UNSIGNED NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `state` varchar(30) NOT NULL DEFAULT 'Pending'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Struktur dari tabel `companies`
--

CREATE TABLE `companies` (
  `id` int(10) UNSIGNED NOT NULL,
  `title` varchar(100) NOT NULL,
  `category` varchar(40) NOT NULL,
  `start` year(4) NOT NULL,
  `address` varchar(255) NOT NULL,
  `email` varchar(100) NOT NULL,
  `website` varchar(100) DEFAULT NULL,
  `description` text,
  `phone` varchar(15) DEFAULT NULL,
  `facebook` varchar(100) DEFAULT NULL,
  `linkedin` varchar(100) DEFAULT NULL,
  `image` varchar(255) NOT NULL DEFAULT 'assets/img/uploads/companies/default_img.png',
  `lat` varchar(50) NOT NULL,
  `long` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data untuk tabel `companies`
--

INSERT INTO `companies` (`id`, `title`, `category`, `start`, `address`, `email`, `website`, `description`, `phone`, `facebook`, `linkedin`, `image`, `lat`, `long`) VALUES
(1, 'Vitka Futsal', 'Sport and leisure', 2016, 'Tiban Ayu', 'vitkafutsal@gmail.com', '', '', '085835403479', '', '', 'assets/img/uploads/companies/0167b1a6d0e73800ebc439a15ab39ccc.png', '', '');

-- --------------------------------------------------------

--
-- Struktur dari tabel `employers`
--

CREATE TABLE `employers` (
  `id` int(10) UNSIGNED NOT NULL,
  `company_id` int(10) UNSIGNED NOT NULL,
  `username` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `hash` varchar(32) NOT NULL,
  `active` tinyint(1) NOT NULL DEFAULT '0',
  `profession` varchar(50) NOT NULL DEFAULT 'Employer'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data untuk tabel `employers`
--

INSERT INTO `employers` (`id`, `company_id`, `username`, `email`, `password`, `created_at`, `hash`, `active`, `profession`) VALUES
(1, 1, 'Vitka Futsal', 'vitkafutsal@gmail.com', '$2y$10$01wcX6lHqD6GnVDC9G4/eu6amS6PftLDXoTVtahSTwUh6PYY7gMmS', '2024-07-28 09:43:06', '72b32a1f754ba1c09b3695e0cb6cde7f', 1, 'Boss');

-- --------------------------------------------------------

--
-- Struktur dari tabel `jobs`
--

CREATE TABLE `jobs` (
  `id` int(10) UNSIGNED NOT NULL,
  `company_id` int(10) UNSIGNED NOT NULL,
  `title` varchar(100) NOT NULL,
  `category` varchar(50) NOT NULL,
  `description` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `type` varchar(15) NOT NULL,
  `salary` mediumint(8) UNSIGNED DEFAULT NULL,
  `address` varchar(255) NOT NULL,
  `lat` varchar(50) NOT NULL,
  `long` varchar(50) NOT NULL,
  `views` mediumint(8) UNSIGNED NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Struktur dari tabel `skills`
--

CREATE TABLE `skills` (
  `id` int(10) UNSIGNED NOT NULL,
  `user_id` int(10) UNSIGNED NOT NULL,
  `title` varchar(30) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Struktur dari tabel `users`
--

CREATE TABLE `users` (
  `id` int(10) UNSIGNED NOT NULL,
  `username` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `hash` varchar(32) NOT NULL,
  `active` tinyint(1) NOT NULL DEFAULT '0' COMMENT '	',
  `phone` varchar(15) DEFAULT NULL,
  `address` varchar(255) DEFAULT NULL,
  `birthdate` date DEFAULT NULL,
  `profession` varchar(50) DEFAULT NULL,
  `native_lang` varchar(30) DEFAULT NULL,
  `description` text,
  `facebook` varchar(100) DEFAULT NULL,
  `linkedin` varchar(100) DEFAULT NULL,
  `cv` varchar(150) DEFAULT NULL,
  `gender` varchar(10) NOT NULL,
  `image` varchar(255) NOT NULL DEFAULT 'assets/img/uploads/users/default_img.png',
  `lat` varchar(50) NOT NULL,
  `long` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data untuk tabel `users`
--

INSERT INTO `users` (`id`, `username`, `email`, `password`, `created_at`, `hash`, `active`, `phone`, `address`, `birthdate`, `profession`, `native_lang`, `description`, `facebook`, `linkedin`, `cv`, `gender`, `image`, `lat`, `long`) VALUES
(1, 'user', 'user@gmail.com', '$2y$10$ljfyT0Ga41ZAtisnfXqSyOJlt3VefibnrBdw3embB8Aw3ZV7ZUcN6', '2024-07-28 09:26:29', '3ef815416f775098fe977004015c6193', 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '', 'assets/img/uploads/users/default_img.png', '', ''),
(3, 'rizky', 'muhammadalnando40@gmail.com', '$2y$10$WPfBOMz3HaIgTR9Vc4G2eeIrAZ/m/WaTjakJBDVX9NZRS8d9JijM2', '2024-07-28 09:46:35', '3c59dc048e8850243be8079a5c74d079', 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '', 'assets/img/uploads/users/default_img.png', '', '');

--
-- Indexes for dumped tables
--

--
-- Indeks untuk tabel `applications`
--
ALTER TABLE `applications`
  ADD PRIMARY KEY (`job_id`,`user_id`),
  ADD UNIQUE KEY `id_UNIQUE` (`id`),
  ADD KEY `fk_applications_users1_idx` (`user_id`),
  ADD KEY `fk_applications_jobs1_idx` (`job_id`);

--
-- Indeks untuk tabel `companies`
--
ALTER TABLE `companies`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `employers`
--
ALTER TABLE `employers`
  ADD PRIMARY KEY (`id`,`company_id`),
  ADD KEY `fk_employers_companies1_idx` (`company_id`);

--
-- Indeks untuk tabel `jobs`
--
ALTER TABLE `jobs`
  ADD PRIMARY KEY (`id`,`company_id`),
  ADD KEY `fk_jobs_companies1_idx` (`company_id`);

--
-- Indeks untuk tabel `skills`
--
ALTER TABLE `skills`
  ADD PRIMARY KEY (`id`,`user_id`),
  ADD KEY `fk_skills_users1_idx` (`user_id`);

--
-- Indeks untuk tabel `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email_UNIQUE` (`email`);

--
-- AUTO_INCREMENT untuk tabel yang dibuang
--

--
-- AUTO_INCREMENT untuk tabel `applications`
--
ALTER TABLE `applications`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `companies`
--
ALTER TABLE `companies`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT untuk tabel `employers`
--
ALTER TABLE `employers`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT untuk tabel `jobs`
--
ALTER TABLE `jobs`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `skills`
--
ALTER TABLE `skills`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `users`
--
ALTER TABLE `users`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- Ketidakleluasaan untuk tabel pelimpahan (Dumped Tables)
--

--
-- Ketidakleluasaan untuk tabel `applications`
--
ALTER TABLE `applications`
  ADD CONSTRAINT `fk_applications_jobs1` FOREIGN KEY (`job_id`) REFERENCES `jobs` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_applications_users1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Ketidakleluasaan untuk tabel `employers`
--
ALTER TABLE `employers`
  ADD CONSTRAINT `fk_employers_companies1` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Ketidakleluasaan untuk tabel `jobs`
--
ALTER TABLE `jobs`
  ADD CONSTRAINT `fk_jobs_companies1` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Ketidakleluasaan untuk tabel `skills`
--
ALTER TABLE `skills`
  ADD CONSTRAINT `fk_skills_users1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
