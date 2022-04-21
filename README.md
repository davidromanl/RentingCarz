# RentingCarz
RentingCarz Test Application

Esta es una prueba presentada para la vacante RentingCarz FullStack Developer

Herramientas y/o Tecnológias Utilizadas

Backend -> PHP con MVC Framework CodeIgniter V 4.0 - Base Datos MySQL
Frontend -> VUE.js con: Vuetify, Axios, vue-router

Estructura Base de Datos:

CREATE TABLE `matches` (
  `id` int(11) NOT NULL,
  `utcDate` datetime DEFAULT NULL ON UPDATE current_timestamp(),
  `status` varchar(255) DEFAULT NULL,
  `score` longtext DEFAULT NULL,
  `homeTeam` longtext DEFAULT NULL,
  `awayTeam` longtext DEFAULT NULL,
  `referees` longtext DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

