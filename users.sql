-- phpMyAdmin SQL Dump
-- version 3.3.10
-- http://www.phpmyadmin.net
--
-- Servidor: localhost
-- Tiempo de generación: 12-11-2014 a las 10:07:50
-- Versión del servidor: 5.1.51
-- Versión de PHP: 5.3.7-dev

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Base de datos: `gescompeval`
--

--
-- Volcar la base de datos para la tabla `users`
--

INSERT INTO `users` (`id`, `username`, `username_canonical`, `email`, `email_canonical`, `enabled`, `salt`, `password`, `last_login`, `locked`, `expired`, `expires_at`, `confirmation_token`, `password_requested_at`, `roles`, `credentials_expired`, `credentials_expire_at`) VALUES
(16, 'admin', 'admin', 'admin@admin.com', 'admin@admin.com', 1, 'adjdb7ui240sok840o48s08g8k00ook', 'PiCXq/GZ+GJCqN8jL68bYxz851ynWeV9kUEx2DCF9WaYhw4cYK/HOUftFPT3bQLelJ3sd7F2QXhJXtH2SVIpgg==', '2014-11-03 14:09:18', 0, 0, NULL, NULL, NULL, 'a:1:{i:0;s:10:"ROLE_ADMIN";}', 0, NULL),
(17, 'user', 'user', 'user@user.es', 'user@user.es', 1, 'o2s6u0wlttwgsc8cw888kcgg48ockc8', 'o1mYrA14rswRqk/fV7z7nHj5oEDLk5rrR625pfLSH8dTrPHwVVb1TKRRd3LnFkWuAJe6XNE9q4vyNPk9Uo4/bg==', '2014-02-19 19:54:58', 0, 0, NULL, NULL, NULL, 'a:1:{i:0;s:9:"ROLE_USER";}', 0, NULL);
