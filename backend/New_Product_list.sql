-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: Aug 24, 2024 at 11:16 AM
-- Server version: 10.11.8-MariaDB-cll-lve
-- PHP Version: 7.2.34

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `u888950461_Product_list`
--

-- --------------------------------------------------------

--
-- Table structure for table `New_Product_list`
--

CREATE TABLE `New_Product_list` (
  `accountType` varchar(255) DEFAULT NULL,
  `name` varchar(255) NOT NULL,
  `ico` varchar(255) NOT NULL,
  `dic` varchar(255) NOT NULL,
  `address` varchar(255) NOT NULL,
  `nazev` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `phone` varchar(255) NOT NULL,
  `note` varchar(255) NOT NULL,
  `ID` int(11) NOT NULL,
  `balance` varchar(255) NOT NULL,
  `payment` varchar(255) NOT NULL,
  `debt` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `New_Product_list`
--

INSERT INTO `New_Product_list` (`accountType`, `name`, `ico`, `dic`, `address`, `nazev`, `email`, `phone`, `note`, `ID`, `balance`, `payment`, `debt`) VALUES
('', 'Abuzer - Rodi Kebap Pisek', '01849948', 'CZ01849948', 'tr. Narodni svobody 32, 397 01 Pisek', 'Abu business s.r.o.', '', '774544575', '', 4, '75245', '64162', '11083'),
('', 'Mazi Kebab - Domazlice', ' 04527674', 'CZ8351043943', 'Bozeny Nemcove 119, 344 01 Domazlice, 1-Mesto', 'Amani Ibrahimova', '', '', '', 5, '601290', '542370', '58920'),
('', 'Istanbul kebab - Klatovy', '09897429', '', 'Kpt. Jarose 150, 339 01 Klatovy I', 'Mustafa Simsek', '', '', '', 6, '156970', '156970', '0'),
('', 'Kebab Express - Kounice', '09586334', '', 'Kounice 87, 289 15 Kounice', 'Huseyin Sahin', '', '', '', 7, '112370', '112370', '0'),
('', 'Planet Food-1', '10786082', 'CZ10786082', 'Hartigova 11, 130 00 Praha 3 Zizkov', 'Planet Fastfood sro', '', '', '', 10, '170250', '170250', '0'),
('', 'Planet Food-2', '10786082', 'CZ10786082', 'Sturova 1282/12, 142 00 Praha 4 Krc', 'Planet Fastfood sro', '', '', '', 11, '31825', '31825', '0'),
('', 'Planet Food-3', '10786082', 'CZ10786082', 'U Kavalirky 4, 150 00 Praha 5 ', 'Planet Fastfood sro', '', '', '', 12, '43325', '43325', '0'),
('', 'Planet Food-4', '10786082', 'CZ10786082', 'V Olsinach 730/43, 100 00 Strasnice', 'Planet Fastfood sro', '', '', '', 13, '37875', '34125', '0'),
('', 'Azad Kebab Sokolska', '', '', 'Sokolska 29, 120 00 Nove Mesto', '', '', '', '', 14, '1920', '1920', '0'),
('', 'Kebap Pizza Burger - Brno', '76557987', '', 'Zvonarka 470, 602 00 Brno-stred', 'Adnan Kjazimi', '', '', 'ERDAL', 15, '113405', '113405', '0'),
('', 'Erdinc Kdyne', '3216632', '', 'Namesti 20, 345 06 Kdyne', 'Ariana Kaplane', '', '', '', 16, '42675', '42675', '0'),
('', 'Efes Kebab - Klatovy', '70202613', '', 'Prazska 121, 339 01 Klatovy I', 'Mustafa Simsek', '', '', '', 18, '194720', '194720', '0'),
('', 'Istanbul Kebab - Domazlice', '29076005', '', 'Namesti Miru 140, 344 01 Domazlice', 'Mansurjon Roostamov', '', '', '', 20, '400500', '385785', '14715'),
('', 'Garip - Arda Kebab Brno', '13959867', '', 'Halasovo nam. 7, 638 00 Brno-sever', 'Garip Memet', '', '', '', 21, '56220', '56220', '0'),
('', 'King Food Olomouc', '07689268', 'CZ07689268', 'Pavelcakova 7/9, 779 00 Olomouc 9', 'Kebab King Food sro', '', '', '', 22, '399727', '291106', '108621'),
('', 'Turecky Kebab Blovice', '88875962', 'CZ8509044291', 'Americka 74, 336 01 Blovice', 'Khusniddin Khudayberdiyev', '', '', '', 24, '116610', '116610', '0'),
('', 'Ahmed - Grande Kebab Bruntal', '10667709', 'CZ10667709', ' Slovenska 79/4, 792 01 Bruntal 1', '#benano s.r.o.', '', '', '', 27, '272474', '272474', '0'),
('', 'AZIZ - Kebab Horsovsky Tyn', '00756105', '', 'Jana Littrowa 122, 346 01 Horsovsky Tyn', 'Azizbek Alimov', '', '', '', 28, '138830', '133020', '5810'),
('', 'Kebab Prestice', '', '', 'Rybova 1082 334 01 Prestice', 'Zoir', '', '', '', 29, '3150', '', '3150'),
('', 'Bone\'ma kebab', '', '', 'Husova 9/10, 682 01 Vyskov', 'Sabri', '', '', '', 30, '', '', '0'),
('', 'Golden Kebab - Olomouc', '', '', 'Dolna nam. 36, 779 00 Olomouc 9', '', '', '', '', 31, '30830', '30830', '0'),
('', 'Pizza Raffaelo - Cesky Brod', '28447042', 'CZ28447042', 'Krale Jiriho 97, 282 01 Cesky Brod', 'Edvina sro', '', '', '', 33, '90280', '86620', '3660'),
('', 'Kebab Grill - Chodov Nakupni Center', '09345931', 'CZ09345931', 'CHODOV NAKUPNI CENTRUM', 'Starwidow sro', '', '', '', 34, '862055', '704373', '157682'),
('', 'Halid - Cesky Brod', '', '', 'Krale Jiriho 94, 282 01 Cesky Brod', '', '', '', '', 36, '7110', '7110', '0'),
('', 'Mr-Food Velky Prilepy', '66047889', 'CZ7312159943', 'Prazska 130, 254 64 Velke Prilepy', 'Varuzhan Virabyan', '', '', '', 37, '115525', '115525', '0'),
('', 'Indicky Food - Eliska Cerna Tabor', '63297345', 'CZ7251231581', 'Husovo nam. 537, 390 02 Tabor 2', 'Eliska Cerna', '', '', '', 38, '5464', '5464', '0'),
('', 'Pizza Mia - Pribram', '86981587', 'CZ8203103777', 'Prazska 30, 261 01 Pribram 1', 'Bekim Sejdini', '', '', '', 41, '355470', '318713', '36757'),
('', 'Pizza Martini - Dobris', '07302339', 'CZ07302339', 'Mirove nam. 190, 263 01 Dobris', 'Martini D S.r.o', '', '', '', 42, '85345', '85345', '0'),
('', 'Original Doner - Tyn Nad Vltavou', '17828333', '', 'Horni Brasov 18, 375 01 Tyn nad Vltavou 1', 'ANIL Deniz', '', '', '', 43, '120547', '98750', '21797'),
('', 'Kebab House Damasek - Susice', '5448832', '', 'Americke armady 86, 342 01 Susice 1', 'Samer Almokhalati', '', '', '', 44, '235220', '228785', '6435'),
('', 'Kebab Kudla - Ceske Budejovice', '13961322', '', 'M.Horakove 1082, 370 05 Ceske Budejovice 5', 'Jan Kudlata', '', '', '', 45, '459120', '459120', '0'),
('', 'Kebab Strancice', '09664971', 'CZ7602011274', 'Zahradni, 251 63 Strancice', 'Suleyman Kiratli', '', '', '', 46, '160470', '155590', '0'),
('', 'Bystrc Kebab - Brno', '76557987', '', 'Kubickova 1115/8, 635 00 Brno-Bystrc', 'Adnan Kjazimi', '', '', '', 47, '67070', '67070', '0'),
('', 'Tureckiy Kebab - Nyrsko', '08280100', '', 'Namesti 142, 340 22 Nyrsko', 'Umirzokov Sardorbek', '', '', '', 48, '78150', '78150', '0'),
('', 'Anatolia Market', '10921753', 'CZ10921753', 'Na Porici 1933/36, Nove Mesto 110 00, 1 Praha 1', 'Agro Rerichy sro', '', '', '', 50, '4000', '4000', '0'),
('', 'Kebab House - Jilove U Prahy', '04968859', 'CZ683849814', 'Masarykovo nam. 19, 254 01 Jilove u Prahy', 'Romeel Khairy Gerges', '', '', '', 51, '222815', '189770', '33045'),
('', 'Kebab Pizza - Bryksova', '11774851', '', 'Bryksova 756/66, 198 00 Praha 14 - Cerny Most', 'Stoyan Georgiev', '', '', '', 52, '4500', '4500', '0'),
('', 'Pizza Pizzetti - Odolena Voda', '11869534', 'CZ8305299992', 'Dolni nam. 1, 250 70 Odolena Voda', 'Besnik Martini', '', '', '', 53, '61930', '61930', '0'),
('', 'Humpolec Stanek Iveta', '45908940', '', 'Rasinova 187, 396 01 Humpolec', 'Iveta', '', '', '', 55, '187295', '187295', '0'),
('', 'Bistro Sadovka - Tynec nad Sazavou', '01188453', 'CZ9312010444', 'Ing. Frantiska Janecka 561, 257 41 Tynec nad Sazavou', 'Lukas Mechura', '', '', '', 56, '49035', '49035', '0'),
('', 'Selcuk Kaufland - Tabor', '27423816', 'CZ27423816', 'Volgogradska 3089, 390 05 Tabor 5', 'Selcuk Demir', '', '', '', 57, '251465', '113400', '138065'),
('', 'Selcuk Husovo - Tabor', '27423816', 'CZ27423816', 'Husovo nam. 583, 390 02 Tabor', 'Selcuk Demir', '', '', '', 58, '10000', '10000', '0'),
('', 'Selcuk Billa - Tabor', '27423816', 'CZ27423816', 'nam. F.Krizika 2840, 390 01 Tabor 1', 'Selcuk Demir', '', '', '', 59, '20000', '20000', '0'),
('', 'Konsolos Ali', '24235059', 'CZ24235059', 'Dukelskych Hrdinu 769, 170 00 Praha 7-Holesovice', 'Istanbul sro', '', '', '', 60, '57517', '21969', '26180'),
('', 'Ali Baba Kebab - Emrah', '', '', 'Na Porici 1918, Nove Mesto, 110 00 Praha', 'JOSH Fastfood', '', '', '', 61, '251920', '180000', '71920'),
('', 'Bursa Iskender Kebab', '', '', 'Hartigova 128/2A, 130 00 Praha 3-Zizkov', '', '', '', '', 63, '445475', '376900', '58860'),
('', 'Ali - Kebab Strasnice', '08745269', 'CZ08745269', 'Cernokostelecka 830/23, 100 00 Strasnice', 'Rahime S.r.o', '', '', '', 64, '195240', '195240', '0'),
('', 'Blansko Viet', '', '', 'Kurim Penny', '', '', '', '', 65, '34640', '34640', '0'),
('', 'Golden Bird Kebab - Kladno', '27950794', 'CZ27950794', 'P. Bezruce 3388, 272 01 Kladno 1', 'B&M Group int. sro', '', '', '', 66, '147229', '147229', '0'),
('', 'Antalya Kebab - Stara Boleslav', '87518244', '', 'Marianske nam. 6/6, Stara Boleslav 1, 250 01 Brandys nad Labem', 'Sadettin Bilgic', '', '', '', 67, '111040', '111040', '0'),
('', 'Istanbul kebab - Lysa nad Labem', '06613641', '', 'Namesti Bedricha Hrozneho 468/18, 289 22 Lysa nad Labem', 'Musa', '', '', '', 68, '362810', '270920', '91890'),
('', 'Best Turkish Kebab Milevsko', '17221803', '1014283175', 'Masarykova 252, 399 01 Milevsko', 'Ibrahim Tas', '', '', '', 69, '78241', '78241', '0'),
('', 'Selcuk Plana - Tabor', '27423816', 'CZ27423816', 'CSLA 128, 391 11 Plana nad Luznici', 'Selcuk Demir', '', '', '', 70, '6820', '6820', '0'),
('', 'Istanbul Kebab Krakowska', '28193997', 'CZ28193997', 'Krakovska 593/19, 110 00 Nove Mesto', 'Erol Kaya', '', '', '', 71, '37285', '37285', '0'),
('', 'Kebab Factory', '03517438', 'CZ03517438', 'Na Porici 36, Praha 1', 'YAMMY ICE s.r.o.', '', '', '', 72, '635080', '381280', '253800'),
('', 'Bistro Viet - Domazlice', '', '', 'Namesti Miru 43', '', '', '721021344', '', 73, '2990', '2990', '0'),
('', 'King Kebab Chocen', '', '', 'Nam. Tyrsovo 299 565 01 Chocen', '', '', '', '', 74, '49550', '43140', '6410'),
('', 'Hm Gastro', '', '', 'Vinohradska 1201/159, 100 00', 'Petr Marek', '', '', '', 75, '1680', '1080', '600'),
('', 'Ceska Lipa', '', '', 'Jindricha z lipe 90', '', '', '', '', 76, '83900', '83900', '0'),
('', 'Ozhan', '28490827', 'CZ28490827', 'Rybna 716/24, Stare Mesto, 110 00 Praha', 'Ozy food s.r.o', '', '', '', 77, '215846.4', '107923.2', '107923.2'),
('', 'Masna Kebab', '19991274', '', 'Masna 4', 'Smajil Spoll s.r.o.', '', '774245664', '', 78, '467102', '378547', '88555'),
('', 'Pizzeta Alb/C. Budejovice', '088823987', '', 'nam. Premysla Otakara II. 79/21, 37001 Ceske Budejovice', 'ALBANO s.r.o', '', '', '', 80, '31660', '31660', '0'),
('', 'Ozzy Food', '', '', 'Sapa praha', '', '', '', '', 81, '107923.2', '107923.2', '0'),
('', 'Hazal Kebab', '', '', 'Katerinska 222/19, 779 00 Olomouc 9', '', '', '', '', 82, '56450', '56450', '0'),
('', 'Anicka Brno', '', '', 'Brno Zvonarka', '', '', '', '', 83, '22850', '22850', '0'),
('', 'Marianske Lazne Hotel aura', '', '', 'Marianske Lazne 44/22', '', '', '', '', 84, '', '0', '0'),
('', 'Nedim - Vyssi Brod', '', '', 'Miru 82, 382 73 Vyssi Brod', '', '', '', '', 85, '351727', '344032', '7695'),
('', 'Lipnik Nad Becvou', '', '', '28. Rijna 41/1 Lipnik nad Becvou', '', '', '', '', 86, '18850', '18850', '0'),
('', 'Sultan Market', '', '', 'Opletalova 57, 110 00 Nove Mesto', 'Hazim', '', '', '', 88, '21000', '18500', '2500'),
('', 'Sef Ayhan', '', '', 'Vinohradska 124, 130 00 Praha 3-Vinohrady', 'Ayhan', '', '', '000443 numarali dodaci list bakiyeye dahil edilmemis neden? kontrol ediniz.', 89, '38960', '38960', '0'),
('', 'Kebab Point C. Budejovice', '', '', 'J. Boreckeho 1590, 370 02 Ceske Budejovice 5', '', '', '', '', 90, '15470', '15470', '0'),
('', 'Dogan Cam Almanya', '', '', 'Ludwing spl. 38, 94315 Straubing, Almanya', '', '', '', '', 91, '52725', '52725', '0'),
('', 'Big Food Point Prerov', '', '', 'Wilsonova 2933/2B, 750 02 Prerov 2', '', '', '', '', 92, '26000', '26000', '0'),
('', 'Mehmet Ayo Almanya', '', '', 'Bahnhofstr 27', '', '', '', '', 93, '101000', '101000', '0'),
('', 'Kebab Kompas', '', '', 'Vyskovicka 114 Ostrava', '', '', '', '', 94, '5500', '5500', '0'),
('', 'Viko - U. Janovice Pizza Nonno', '', '', 'Jungmannova 119, 285 04 Uhlirske Janovice', '', '', '', '', 95, '123790', '123790', '0'),
('', 'Viet Sapa Komsu', '', '', 'Sapa', '', '', '', '', 96, '1650', '1650', '0'),
('', 'Strasnice Alex', '', '', 'Strasnice', 'Pizza Palermo', '', '', '', 98, '23880', '23880', '0'),
('', 'Irfan Komsu', '', '', '', 'Irfan', '', '', '', 99, '196440.5', '99396.5', '68874'),
('', 'Pizza Nonna Caslav', '', '', 'Jana Ziska trucnova 80', '', '', '774999991', '', 100, '88380', '88380', '0'),
('', 'Ujezd- Kebab House', '', '', 'Vitezna 420/18 11800 Mala Strana', '', '', '773278457', '', 101, '193010', '117260', '10000'),
('', 'Indian Bistro - C.Budejovice', '', '', 'Kanovnicka 390/11, 370 01 Ceske Budejovice 1', '', '', '', '', 102, '3300', '', '3300'),
('', 'Baki Brno', '', '', 'Ceska 22 Brno', 'No Limit', '', '', '', 103, '37890', '37890', '0'),
('', 'Kebab Factory Letenske namesti', '', '', 'M. Horakove 382/73, 170 00 Praha 7-Holesovice', '', '', '', '', 104, '4400', '', '4400'),
('', 'Vrsovicka - Bospor Kebab', '', '', 'Vrsovicka 896/32, 101 00 Praha 10-Vrsovice', '', '', '', '', 105, '90995', '90995', '0'),
('', 'Kebab House uhrineves', '', '', 'Nove namesti 1257/9, 104 00 Praha, Uhrineves', '', '', '', '', 106, '107440', '107440', '0'),
('', 'Repy - Street Kebab', '', '', 'Makovskeho 1349/2A, 163 00 Praha 17', '', '', '', '', 107, '65430', '65430', '0'),
('', 'Kebab & Pizza VEIS', ' 17835976', '', 'Libusina, 391 65 Bechyne', 'Semi Islami', '', '', '', 108, '230200', '230200', '0'),
('', 'Roudnice nad labem', '', '', 'Jungmannova 1069 413 01 Roudnice nad Labem ', '', '', '', '', 109, '224327', '224327', '0'),
('', 'Go Go Gyros Hasan', '19448929', 'CZ19448929', 'Revolucni 1084/110 00', 'Megalo Food s.r.o', '', '', '', 110, '84720', '57400', '27320'),
('', 'Kebab Factory Nedima', '21523452', '', '', 'Mns s.r.o', '', '', '', 111, '184085', '184085', '0'),
('', 'Bistro U Mateje', '', '', 'K Mecholupum 777, 109 00 Praha 15', '', '', '', '', 113, '39800', '39800', '0'),
('', 'Kebab & Pizza Sambal', '04627288', 'CZ04627288', 'Hlavni 2459, 141 00 Praha 4-Zabehlice', 'Roseland Catering', '', '', '', 115, '149090', '149090', '0'),
('', 'OSOBNI', '', '', 'DEPO', '', '', '', '', 116, '3890', '3890', '0'),
('', 'ZamZam Kebab Olomouc', '19208456', '', 'Havlickova 650/9 Olomouc', 'Mostafa Elnobe Kassam', '', '', '', 117, '117715', '117215', '500'),
('', 'Nelahozeves', '', '', 'Zagarolska 161, 277 51 Nelahozeves', '', '', '', '', 118, '16900', '16900', '0'),
('', 'Happy pizza & kebab', '02209446', '', 'Butovicka 44/70, 158 00 Praha 5', 'Stefan Bodnar', '', '', '', 119, '4070', '4070', '0'),
('', 'Arslan Kebab Andel', '24826049', '', 'Bozdechova 2246/3, 150 00 Praha 5-Smichov', 'Serkan Arslan', '', '', '', 120, '13000', '13000', '0'),
('', 'Lucie Olomouc', '', '', 'Olomouc', '', '', '', '', 121, '', '', '0'),
('', 'Pizza Gyros', '', '', 'Lidicka 276 /36, 150 00 Praha 5-Smichov', '', '', '', '', 122, '220710', '211030', '0'),
('', 'Azad Osobni', '', '', '', '', '', '', '', 123, '18900', '18900', '0'),
('', 'The fresh Kebab', '', '', 'Moskevska 532/60, 101 00 Praha 10-Vrsovice', '', '', '', '', 124, '5000', '5000', '0'),
('', 'Street Kebab-Palmovka', '', '', 'Sokolovska 364, 180 00 Praha 8-Palmovka', '', '', '', '', 125, '4400', '4400', '0'),
('', 'La Piazza Husitska', '', '', 'Husitska 11 praha 3 ', '', '', '', '', 126, '1490', '1490', '0'),
('', 'Sultan kebab Ceske Budejovice', '', '', 'Prazska tr. 30 37004 Ceska Budejovice 4', '', '', '', '', 127, '261348', '261348', '0'),
('', 'Pizza Muja Pacov', '', '', 'namesti Svobody 83 Pacov', '', '', '', '', 128, '181275', '166675', '14600'),
('', 'Pizzerie Da Vinci', '', '', 'Sedlackova 28 , 30100 Plzen', '', '', '', '', 129, '700', '700', '0'),
('', 'Pizzeria Nela Jaromer', '', '', 'Svatopluka Cecha 311 Jaromer', '', '', '', '', 130, '1890', '1890', '0'),
('', 'Samuel Pizza - T. nad orlici', '08627614', '', 'Mostecka 6 Tyniste nad Orllici ', 'luxury palace s.r.o', '', '725023556', 'luxury palace s.r.o.Michle, Jaurisova 515/4, Praha 4', 132, '57385', '53025', '4360'),
('', 'Pizzeria Horice', '', '', 'Jiriho z Podebrad 343 Horice', '', '', '774433336', '', 134, '43604', '41069', '2535'),
('', 'Queens Kebab Karlin', '', '', 'Sokolovska 120/62, 186 00 Karlin', 'Sukrat', '', '', '', 135, '5210', '5210', '0'),
('', 'Ozbek Kebab Vinohrady', '', '', 'Vinohrady', '', '', '', '', 136, '2200', '2200', '0'),
('', 'Lysa- Kebab House', '09458271', 'CZ09458271', 'Masarykova 304/3, 289 22 Lysa nad Labem', 'ALHASANIN s.r.o.', '', '', '', 137, '41010', '41010', '0'),
('', 'Breznice--Pizzeria', '11637099', 'CZ11637099', 'Namesti 44 Breznice', 'Gastro Petrit s.r.o', '', '', '', 138, '53409', '53409', '0'),
('', 'The Hamdi\'s Kebab - Nehvizdy', '', '', 'Prazska 8, 250 81 Nehvizdy', '', '', '732 186 463', '', 139, '', '', '0'),
('', 'Kaprova - Pizza-Kebab', '', '', 'Kaprova 15, 110 00 Josefov', '', '', '', '', 140, '5000', '5000', '0'),
('', 'Trest - Pizza Kebab', '21225605', '', 'Rooseweltova 460/10, 589 01 Trest', 'Kristian Geshtenja', '', '', '', 141, '110370', '96375', '13995'),
('', 'Lysa - Kebab u nadrazi', '', '', 'Capkova 508, 289 22 Lysa nad Labem', '', '', '', '', 142, '31205', '31205', '0'),
('', 'Pizza Samuel - Kostelec nad Orlici', '09019537', '', 'Palackeho nam. 22, 517 41 Kostelec nad Orlicí', 'queen production s.r.o.', '', '', 'queen production s.r.o.Michle, Jaurisova 515/4, Praha 4', 143, '19045', '19045', '0'),
('', 'AMBER Pizza Kebab', '', '', 'Michelska 140 00, 140 00 Praha 4', '', '', '', '', 144, '6580', '6580', '0'),
('', 'Votice-Pizza Leo', '', '', 'Komenskeho nam. 154, 259 01 Votice', '', '', '608 404 244', '', 145, '30650', '26650', '4000'),
('', 'Nedima - Hradec Kralove', '002', '', 'hradec kralové', 'msn', '', '', '', 147, '166975', '166975', '0'),
('', 'Nedima PVL  letnany', '', '', 'PVL LETNAN', 'MNS', '', '', '', 149, '', '', '0'),
('', 'Hm Gastro 2', '', '', 'Revnicka 170 Zlicin', 'Roman Rericha', '', '775364804', '', 150, '780', '780', '0'),
('', 'PIZZA ROMA - TABOR', '', '', 'Husovo nam. 539, 390 02 Tabor 2', '', '', '', '', 151, '44000', '42500', '1500'),
('', 'Camp Kiosek U Certa', '', '', 'Peliskuv Most 1, 257 63 Tichonice-Trhovy Stapanov', '', '', '', '', 152, '45100', '41100', '4000'),
('', 'Plana nad Luznici', '', '', '', '', '', '', '', 153, '68275', '64275', '4000'),
('BARISA - GASTRO', 'Nizam ', ' 28585135', '', 'Gorkého 589, 272 01 Kladno', 'BARISA - GASTRO', '', '', '', 154, '', '', '0'),
('', 'Olomouc-Haluk', '', '', 'tr. Kosmonautu 113/27 Olomouc 9 - Hodolany', '', '', '', '', 155, '8035', '8035', '0'),
('', 'Lokanta Praha Restaurant', '17275717', 'cz17275717', 'Vladislavova 1761/18, 110 00 Nove Mesto', '', '', '', '', 156, '17440', '17440', '0'),
('', 'Votuzska- Milan', '', '', 'Votuzská 1639/19 193 00 Horni Pocernice', '', '', '', '', 157, '3000', '3000', '0'),
('', 'Kivanc Kaplice', '', '', 'Kaplice Nadrazi', '', '', '', '', 158, '49710', '63035', '-13325'),
('', 'Nedima - Tabor', '', '', 'Tabor', '', '', '', '', 159, '11955', '11955', '0'),
('', 'Ahmet Bruntal- Kaufland', '', '', 'Kaufland', 'Ahmet', '', '', '', 160, '55773', '55773', '0'),
('', 'Nedima - Jihlava', '', '', 'Jihlava', '', '', '', 'Faturali', 161, '33154', '33154', '0'),
('', 'nedima -plzen', '', '', '', '', '', '', '', 162, '66404', '66404', '0'),
('', 'Kaplice - Istanbul kebab', '', '', 'Kaplice', '', '', '', '', 163, '16305', '16305', '0'),
('', 'Krnov Ahmet - Grande Kebab', '', '', 'Opavska 253/32, 794 01 Krnov 1', '', '', '', '', 164, '10615', '10615', '0'),
('', 'S.V.G - Strakonice', '', '', 'Strakonice', '', '', '', '', 165, '82790.4', '82790.4', '0'),
('', 'Kladno Nizam', '28585135', '', 'Gorkeho 589, 272 01 Kladno', 'BARISA - GASTRO', '', '', '', 166, '2475', '', '2475'),
('', 'Nedima - Liberec', '', '', '', 'msn', '', '', '', 167, '81726', '81726', '0'),
('', 'Barakat - Opletalova', '', '', 'Opletalova 28, 110 00 Nove Mesto', '', '', '', '', 168, '33000', '30000', '3000'),
('', 'Nedima - Milovice', '', '', '', 'Mns', '', '', '', 169, '32515', '32515', '0'),
('', 'Zaza kebab mlada boleslav', '', '', 'Kosmonosy 1255, 293 06 Kosmonosy', '', '', '', '', 171, '9150', '7000', '2150'),
('', 'Kunratice Kebab', '', '', 'Kunratice', '', '', '', '', 172, '2000', '2000', '0'),
('', 'Maraba kebab', '', '', 'Belehradska 69, 120 00 Nové Město', '', '', '', '', 173, '5750', '2875', '2875'),
('', 'Big Food Point - Olomouc', '', '', 'Riegrova 8, 779 00 Olomouc 9', '', '', '', '', 174, '7500', '7500', '0'),
('', 'Quattro Gusti - Francouzska', '', '', 'Francouzská 736, 120 00 Praha 2-Vinohrady', '', '', '', '', 175, '2100', '2100', '0'),
('', 'Can Bey Kebab', '', '', 'Jecna 547, 120 00 Nove Mesto', '', '', '', '', 176, '24600', '', '24600'),
('', 'Andel Istanbul Kebab', '', '', 'Smichov Andel', '', '', '', '', 177, '1650', '', '1650'),
('', 'Restaurace a pizzeria Sofra', '', '', 'Jeremenkova 1835/106', '', '', '', '', 178, '930', '930', '0'),
('', 'Milovice-Kebab Samuel', '03832597', 'CZ03832597', 'Armadni 532, 289 24 Milovice nad Labem 3-Mlada', 'Rotomis KSM sro', '', '', '', 179, '19340', '19340', '0'),
('', 'Mnam Kebab - Opatov', '', '', 'Chilska, 149 00 Praha 11-Chodov', '', '', '', '', 180, '4875', '', '4875'),
('', 'Ostrava - Ibrahim Yenilmez', '', '', 'Ostrava', '', '', '', '', 181, '2750', '', '0');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `New_Product_list`
--
ALTER TABLE `New_Product_list`
  ADD PRIMARY KEY (`ID`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `New_Product_list`
--
ALTER TABLE `New_Product_list`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=182;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
