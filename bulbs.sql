-- phpMyAdmin SQL Dump
-- version 5.1.1
-- https://www.phpmyadmin.net/
--
-- Gazdă: 127.0.0.1
-- Timp de generare: dec. 17, 2025 la 12:32 PM
-- Versiune server: 10.4.19-MariaDB
-- Versiune PHP: 8.0.7

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Bază de date: `api_records`
--

-- --------------------------------------------------------

--
-- Structură tabel pentru tabel `bulbs`
--

CREATE TABLE `bulbs` (
  `id` int(11) NOT NULL,
  `name` varchar(50) DEFAULT NULL,
  `status` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Eliminarea datelor din tabel `bulbs`
--

INSERT INTO `bulbs` (`id`, `name`, `status`) VALUES
(1, 'Bedroom', 0),
(2, 'Bathroom', 0),
(3, 'Living', 0),
(4, 'Kitchen', 0),
(5, 'Hall', 0),
(6, 'Balcony', 0);

--
-- Indexuri pentru tabele eliminate
--

--
-- Indexuri pentru tabele `bulbs`
--
ALTER TABLE `bulbs`
  ADD PRIMARY KEY (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
