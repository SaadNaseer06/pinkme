-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jul 24, 2025 at 07:38 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `pinkme_dashboard`
--

-- --------------------------------------------------------

--
-- Table structure for table `applications`
--

CREATE TABLE `applications` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `patient_id` bigint(20) UNSIGNED NOT NULL,
  `reviewer_id` bigint(20) UNSIGNED DEFAULT NULL,
  `program_id` bigint(20) UNSIGNED DEFAULT NULL,
  `title` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `status` enum('Pending','Approved','Rejected','Under Review') NOT NULL DEFAULT 'Pending',
  `submission_date` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `decision_date` timestamp NULL DEFAULT NULL,
  `rejection_reason` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `applications`
--

INSERT INTO `applications` (`id`, `patient_id`, `reviewer_id`, `program_id`, `title`, `description`, `status`, `submission_date`, `decision_date`, `rejection_reason`, `created_at`, `updated_at`) VALUES
(1, 1, NULL, 4, 'Non id est expedita atque.', 'Quia quis ut voluptates quia. Voluptate magni nisi culpa vitae in aperiam. Sunt iste est ex sed temporibus qui. Quis iste architecto qui magnam inventore quos. Inventore ducimus qui cumque ipsum.', 'Approved', '2025-05-20 07:41:57', NULL, NULL, '2025-07-23 13:49:06', '2025-07-23 13:49:06'),
(2, 1, NULL, 9, 'Vel sapiente sit rerum.', 'Dolorum culpa rerum distinctio autem sed. Fugit repellendus qui nobis ut voluptates nulla. Ipsa rerum veniam maiores explicabo vitae deleniti quis. Sed consequatur velit ex culpa iste inventore.', 'Under Review', '2025-04-15 15:45:57', '2025-07-08 10:09:51', NULL, '2025-07-23 13:49:06', '2025-07-23 13:49:06'),
(3, 1, NULL, 6, 'Consequatur et sed similique.', 'Quo ratione vel error aliquid doloribus. Assumenda enim ipsum ut voluptatibus quo molestias aut. Sunt quidem eligendi officia et non minus nobis aut. Quia iusto et mollitia dolores perspiciatis.', 'Approved', '2025-03-18 17:28:54', NULL, NULL, '2025-07-23 13:49:06', '2025-07-23 13:49:06'),
(4, 1, NULL, 8, 'Aut sapiente pariatur.', 'Voluptatibus voluptates sit eum molestias quisquam eaque voluptatibus. Dolorem dolor eum asperiores quia quisquam consequuntur aliquam. Eveniet praesentium consequatur exercitationem aliquid ullam et est. Corporis nihil nulla velit omnis itaque quis laboriosam.', 'Under Review', '2025-07-20 07:18:47', '2025-06-21 18:03:57', 'Rerum fugit earum sed sunt.', '2025-07-23 13:49:06', '2025-07-23 13:49:06'),
(5, 2, NULL, 1, 'Ex provident et voluptatem ipsam animi.', 'Aperiam voluptas vero assumenda ea quis numquam nisi. Ut laboriosam praesentium nihil aut suscipit. Fugiat aut in omnis veniam. Amet sit eius quis adipisci impedit.', 'Under Review', '2025-02-11 01:21:08', '2025-06-28 04:17:20', NULL, '2025-07-23 13:49:07', '2025-07-23 13:49:07'),
(6, 2, NULL, 8, 'Sint fuga ea nostrum quasi ducimus.', 'Qui quo ut quasi esse vitae veritatis sapiente. Totam inventore numquam et quasi. Eos harum est ullam excepturi ullam fugiat facilis.', 'Under Review', '2025-05-19 14:00:32', NULL, NULL, '2025-07-23 13:49:07', '2025-07-23 13:49:07'),
(7, 2, NULL, 1, 'Nesciunt velit vero exercitationem.', 'Sunt iusto ut cumque. Voluptas voluptas voluptate non labore dolores. Harum sint unde assumenda doloremque iusto eum voluptatibus.', 'Approved', '2025-03-24 18:49:57', '2025-05-14 18:26:56', 'Consectetur et aliquam dolor laudantium.', '2025-07-23 13:49:07', '2025-07-23 13:49:07'),
(8, 3, NULL, 10, 'Aperiam accusantium dolorum voluptatum facere.', 'Qui qui dolorum omnis ab. Aut facilis exercitationem est odit. Provident quidem ut expedita ut est dolorum non.', 'Pending', '2025-06-09 06:41:20', NULL, 'Alias voluptatem labore possimus facere aut praesentium temporibus.', '2025-07-23 13:49:07', '2025-07-23 13:49:07'),
(9, 3, NULL, 1, 'Sint atque ad autem autem quia.', 'Quae earum ut reprehenderit adipisci nemo est illum. Qui minus sit omnis. Voluptatem similique qui totam voluptatem ipsum. Rerum libero unde est blanditiis.', 'Rejected', '2025-05-08 21:24:38', '2025-06-12 21:24:40', NULL, '2025-07-23 13:49:07', '2025-07-23 13:49:07'),
(10, 3, NULL, 8, 'Sint fuga tempore fugiat deserunt a.', 'Ratione adipisci ut est. Doloremque sint excepturi illo ducimus laudantium. Nostrum rem odio animi nihil.', 'Approved', '2025-07-13 11:50:58', '2025-04-27 14:52:27', NULL, '2025-07-23 13:49:07', '2025-07-23 13:49:07'),
(11, 3, NULL, 7, 'Ab officiis nulla libero incidunt.', 'Illum rem rerum quis voluptatem eius. Corrupti aut qui ipsa rem. Id laborum voluptatem voluptas et voluptatem laboriosam.', 'Pending', '2025-07-02 17:15:50', '2025-07-11 06:42:44', NULL, '2025-07-23 13:49:07', '2025-07-23 13:49:07'),
(12, 3, NULL, 6, 'Aut et architecto exercitationem.', 'In quisquam exercitationem sit quia voluptatum consequatur rerum magnam. Numquam rerum quae debitis quasi inventore sapiente.', 'Under Review', '2025-02-11 03:59:54', '2025-06-08 00:04:10', NULL, '2025-07-23 13:49:07', '2025-07-23 13:49:07'),
(13, 4, NULL, 6, 'Aliquam facere saepe.', 'Error ut nihil blanditiis sunt consequatur eius. Itaque repellendus qui ut omnis. Beatae aspernatur animi enim.', 'Under Review', '2025-06-06 19:58:45', '2025-05-17 10:17:51', NULL, '2025-07-23 13:49:07', '2025-07-23 13:49:07'),
(14, 5, NULL, 4, 'Est autem voluptas ea.', 'Iusto consequuntur voluptate assumenda ut ea explicabo fuga. Veritatis fugit suscipit placeat quis. Necessitatibus unde rerum voluptatem aperiam nihil reprehenderit.', 'Rejected', '2025-07-16 12:28:01', NULL, 'Ut deleniti in officiis repellat.', '2025-07-23 13:49:07', '2025-07-23 13:49:07'),
(15, 6, NULL, 9, 'Et perspiciatis quaerat voluptatibus.', 'Non molestiae sit tempore at sed qui sed. Et dolorem aut et possimus sit quia voluptas. Ut veniam sit itaque natus voluptatum ea dolorem.', 'Under Review', '2025-02-07 14:50:13', NULL, NULL, '2025-07-23 13:49:08', '2025-07-23 13:49:08'),
(16, 6, NULL, 8, 'Dolore est facere recusandae enim.', 'Qui vel eos voluptas voluptatem. Doloremque nesciunt nam deserunt provident cupiditate excepturi impedit. Omnis perspiciatis quia odit atque quidem.', 'Pending', '2025-03-04 10:57:08', '2025-04-24 20:46:23', NULL, '2025-07-23 13:49:08', '2025-07-23 13:49:08'),
(17, 6, NULL, 2, 'Enim alias accusantium.', 'Accusamus nostrum dolorum nemo iste voluptatem facilis. Ratione temporibus harum impedit omnis accusamus libero iste. Sed est placeat sed nisi voluptatem.', 'Approved', '2025-06-09 19:06:25', '2025-05-06 00:29:52', NULL, '2025-07-23 13:49:08', '2025-07-23 13:49:08'),
(18, 6, NULL, 3, 'Delectus corrupti sit non.', 'Perferendis consectetur accusamus ut exercitationem odit saepe aspernatur cum. Quo eos reprehenderit dignissimos. Odio sed et delectus eos deserunt maiores maxime. Nostrum expedita hic expedita accusamus. Assumenda quis nihil unde sunt occaecati quia voluptatem.', 'Approved', '2025-02-02 07:47:21', NULL, NULL, '2025-07-23 13:49:08', '2025-07-23 13:49:08'),
(19, 7, NULL, 2, 'Dolorum inventore non.', 'Consequatur id consequatur facere reprehenderit. Maxime voluptatem libero velit aut. Tenetur est aspernatur officia officia ipsam quos. Saepe ducimus sit alias est consequuntur suscipit qui explicabo.', 'Under Review', '2025-01-29 23:51:38', NULL, NULL, '2025-07-23 13:49:08', '2025-07-23 13:49:08'),
(20, 7, NULL, 6, 'Deleniti minima alias ea dolorem et.', 'Fugiat recusandae dolorum voluptatem maxime nemo eos. Neque qui laudantium laudantium assumenda fugit aliquid sit nihil. Delectus et ab porro quod odio quia aut. Sed illum velit debitis exercitationem. Aut magni fugiat alias numquam.', 'Under Review', '2025-03-06 06:03:55', NULL, 'Similique voluptatem consectetur harum qui fuga est.', '2025-07-23 13:49:08', '2025-07-23 13:49:08'),
(21, 7, NULL, 6, 'Possimus aliquid ullam consequatur cum.', 'Et aut ut quod ut. Necessitatibus hic quaerat exercitationem voluptas facilis voluptatem. Unde dolor sapiente quia quam libero dicta odio. Occaecati suscipit aut explicabo accusamus sed corrupti sit est.', 'Approved', '2025-03-05 00:58:58', '2025-06-25 18:32:12', NULL, '2025-07-23 13:49:08', '2025-07-23 13:49:08'),
(22, 7, NULL, 1, 'Aut est sed.', 'Veniam aut dolore iste cupiditate occaecati magni explicabo. Quaerat qui quam suscipit enim. Non sit ratione dolorem ipsum et porro et. Quia quis eius voluptas qui autem laboriosam quibusdam.', 'Pending', '2025-05-01 22:22:53', '2025-05-31 09:04:16', NULL, '2025-07-23 13:49:08', '2025-07-23 13:49:08'),
(23, 8, NULL, 3, 'Dignissimos sit magni.', 'Sint illo velit qui natus nobis perspiciatis. Pariatur in dolorum magni asperiores. Quia voluptatem iste eum ex consectetur. Minus non voluptas ducimus laboriosam ipsam assumenda reprehenderit.', 'Pending', '2025-07-22 20:19:54', '2025-07-01 23:27:04', NULL, '2025-07-23 13:49:08', '2025-07-23 13:49:08'),
(24, 8, NULL, 1, 'Sint quo sint.', 'Natus eligendi ut quasi ad nostrum in. Sit dolor enim vel omnis eaque voluptatem. Culpa eveniet sunt in voluptatem distinctio est.', 'Approved', '2025-07-20 22:11:33', NULL, NULL, '2025-07-23 13:49:08', '2025-07-23 13:49:08'),
(25, 9, NULL, 6, 'Nostrum error temporibus atque consectetur.', 'Nesciunt facere architecto similique optio ut ut quis. Facilis aperiam porro provident in rerum rerum. Beatae ipsum consequatur eveniet odio similique non non est.', 'Rejected', '2025-04-05 08:23:22', '2025-06-09 17:07:54', NULL, '2025-07-23 13:49:08', '2025-07-23 13:49:08'),
(26, 9, NULL, 2, 'Aut et et eum.', 'Ut eligendi molestias quibusdam fugit. Libero dolorem id similique aliquam esse autem. Sapiente minima exercitationem expedita aut.', 'Rejected', '2025-03-15 06:15:01', '2025-04-27 22:13:47', NULL, '2025-07-23 13:49:08', '2025-07-23 13:49:08'),
(27, 9, NULL, 8, 'Ea deserunt laboriosam dolorem.', 'Ullam officiis ipsum perspiciatis quia. Non id cupiditate non rerum fugiat optio. Alias molestias id natus rem dicta eveniet.', 'Rejected', '2025-02-15 23:41:58', '2025-06-11 12:00:31', 'Nisi qui sit libero est molestiae.', '2025-07-23 13:49:08', '2025-07-23 13:49:08'),
(28, 10, NULL, 6, 'Veritatis nemo aliquam atque.', 'Placeat exercitationem culpa aut laborum est deleniti odio. Labore voluptatibus voluptatem voluptatibus facere eaque. Tempora id voluptas autem similique omnis corrupti. Soluta voluptatibus perferendis facere tempore non.', 'Approved', '2025-04-23 16:55:17', NULL, NULL, '2025-07-23 13:49:09', '2025-07-23 13:49:09'),
(29, 10, NULL, 5, 'Maxime est quia tenetur nostrum.', 'In placeat totam eaque sunt repudiandae laboriosam. Dolores commodi sit ad ut. Rerum quia in dignissimos molestiae illum est eligendi repellendus.', 'Approved', '2025-02-17 14:24:00', NULL, NULL, '2025-07-23 13:49:09', '2025-07-23 13:49:09'),
(30, 11, NULL, 2, 'Aliquam doloremque et quae.', 'Consectetur architecto aut hic in voluptas ullam. Consequatur aut reprehenderit alias laboriosam at tenetur. Qui ea quia nobis est eum atque. Minus ut dolores harum soluta.', 'Under Review', '2025-04-15 19:21:19', NULL, NULL, '2025-07-23 13:49:09', '2025-07-23 13:49:09'),
(31, 11, NULL, 4, 'Repellat est accusantium.', 'In architecto incidunt tenetur. Ullam eum ut iste occaecati consequatur corporis eligendi dolorum. Error in sit voluptas impedit. Consequuntur occaecati autem delectus unde id non.', 'Rejected', '2025-05-17 15:44:43', NULL, NULL, '2025-07-23 13:49:09', '2025-07-23 13:49:09'),
(32, 12, NULL, 7, 'Atque rerum eum quod.', 'Quo unde provident officia. Eveniet qui reiciendis aut et laudantium maxime amet tempora. Iure nihil est dignissimos quas. Voluptatem autem quos ducimus perspiciatis minus iure. Est eos iste molestiae soluta dolore praesentium labore.', 'Rejected', '2025-03-18 23:48:31', NULL, 'Sunt placeat excepturi aliquid omnis error.', '2025-07-23 13:49:09', '2025-07-23 13:49:09'),
(33, 12, NULL, 7, 'Molestias atque voluptate.', 'Accusamus et rerum delectus illum sunt. Unde dolorum ipsam dicta veniam excepturi. Repudiandae consequatur est ipsam rerum ut quaerat. Sunt nihil non itaque et voluptas id qui.', 'Pending', '2025-04-13 08:47:06', '2025-07-19 14:31:05', 'Et aut hic nemo molestias quis eveniet ut.', '2025-07-23 13:49:09', '2025-07-23 13:49:09'),
(34, 12, NULL, 7, 'Repellat facilis reiciendis deserunt et debitis.', 'Velit delectus at quia quo. Maxime officia vel vero. Neque esse aut aspernatur nulla. Repellat adipisci sunt ad nobis ipsa corrupti.', 'Rejected', '2025-05-28 08:57:09', NULL, NULL, '2025-07-23 13:49:09', '2025-07-23 13:49:09'),
(35, 12, NULL, 9, 'Aliquid in ducimus sint cupiditate.', 'Eveniet et culpa est dolores. Quae quis nemo mollitia consequatur doloremque.', 'Pending', '2025-04-19 23:52:48', NULL, NULL, '2025-07-23 13:49:09', '2025-07-23 13:49:09'),
(36, 13, NULL, 1, 'Quisquam qui corrupti illo reprehenderit.', 'Expedita quaerat qui temporibus eum ratione nihil consequatur. Similique numquam sint exercitationem vitae officiis et.', 'Under Review', '2025-07-09 23:02:23', NULL, NULL, '2025-07-23 13:49:09', '2025-07-23 13:49:09'),
(37, 13, NULL, 7, 'Quos harum voluptatibus earum quo.', 'Temporibus maxime dolorem labore et aut illum ea consectetur. Et reprehenderit ut voluptatem inventore et. Suscipit ut voluptatum nesciunt a dolorum asperiores aut qui.', 'Under Review', '2025-06-26 15:25:16', '2025-07-22 23:43:38', 'Vitae accusamus laudantium facilis aperiam quasi iusto libero in.', '2025-07-23 13:49:09', '2025-07-23 13:49:09'),
(38, 13, NULL, 6, 'Itaque vel laudantium aut vel corrupti.', 'Aut tenetur incidunt aut eligendi praesentium. Tempora aut error et consequatur. Voluptatem ut officia quidem vel distinctio aut.', 'Pending', '2025-02-12 14:22:44', NULL, NULL, '2025-07-23 13:49:09', '2025-07-23 13:49:09'),
(39, 14, NULL, 4, 'Sapiente deleniti doloremque.', 'Perferendis qui nisi at. Officiis dicta ipsum voluptate id nisi et. Recusandae excepturi architecto aut et placeat.', 'Pending', '2025-02-20 04:17:29', NULL, 'Quibusdam est autem perferendis eaque.', '2025-07-23 13:49:10', '2025-07-23 13:49:10'),
(40, 14, NULL, 3, 'Odio esse qui impedit fugiat sint.', 'Pariatur quasi asperiores aut. Deleniti quisquam doloremque qui recusandae facere eum.', 'Approved', '2025-05-07 14:39:49', '2025-07-05 02:41:47', NULL, '2025-07-23 13:49:10', '2025-07-23 13:49:10'),
(41, 15, NULL, 9, 'Ea culpa et.', 'Doloribus aspernatur qui consectetur quia. Earum quibusdam aut consectetur veniam. Corrupti assumenda harum eum repellendus modi repudiandae excepturi. Aliquam atque nulla veniam nobis illo necessitatibus.', 'Approved', '2025-06-18 04:42:58', '2025-07-11 05:27:13', NULL, '2025-07-23 13:49:10', '2025-07-23 13:49:10'),
(42, 15, NULL, 7, 'Quis voluptatem doloremque maiores ut ab.', 'Sapiente non fugiat quia aut eos accusamus modi. Et assumenda non similique quis illum quia dolor. Ea qui fuga dolorum eaque autem sunt blanditiis. Ea accusantium est rerum facere. Dolores labore placeat ratione molestiae omnis.', 'Under Review', '2025-03-06 16:18:07', '2025-07-01 21:30:24', NULL, '2025-07-23 13:49:10', '2025-07-23 13:49:10'),
(43, 16, NULL, 3, 'Quia quam et.', 'Velit quasi ut et suscipit nulla. In et ullam assumenda illum. Soluta mollitia laboriosam sunt sapiente sequi recusandae eos. Non dicta ab et voluptates. Odio temporibus impedit tenetur ea atque vero distinctio aliquam.', 'Under Review', '2025-01-27 17:04:29', '2025-05-09 23:01:49', NULL, '2025-07-23 13:49:10', '2025-07-23 13:49:10'),
(44, 16, NULL, 2, 'Officia aut repellat rem.', 'Explicabo distinctio voluptatem et. Sit et accusamus aut facere consequuntur. Aut totam et distinctio et sapiente blanditiis. Voluptatem temporibus dolor illum sit dicta aperiam.', 'Pending', '2025-02-18 03:45:55', NULL, NULL, '2025-07-23 13:49:10', '2025-07-23 13:49:10'),
(45, 16, NULL, 10, 'Sint enim reiciendis nobis ullam assumenda.', 'Quibusdam dolores sint soluta nulla mollitia quia. Et expedita aut accusamus aut odio. Ea voluptas beatae itaque et cumque expedita omnis.', 'Approved', '2025-03-23 08:16:07', '2025-05-29 05:54:53', NULL, '2025-07-23 13:49:10', '2025-07-23 13:49:10'),
(46, 16, NULL, 7, 'Quia nam ut.', 'Et sit ut voluptate quia ut aliquam. Rerum natus ab incidunt natus temporibus enim reprehenderit. Dolorum laudantium tempora quia in consequatur. Laboriosam sunt autem quis facilis asperiores commodi eum suscipit.', 'Rejected', '2025-04-15 22:24:26', '2025-07-16 14:04:12', 'Natus illum rerum commodi reiciendis autem qui quia.', '2025-07-23 13:49:10', '2025-07-23 13:49:10'),
(47, 16, NULL, 9, 'Quis est minima.', 'Deserunt a unde consequatur sed nam eveniet natus. Velit hic minima rerum. Et mollitia sit deserunt reprehenderit veritatis quae saepe.', 'Approved', '2025-05-04 05:21:40', '2025-05-21 00:08:38', NULL, '2025-07-23 13:49:10', '2025-07-23 13:49:10'),
(48, 17, NULL, 3, 'Aut qui quis architecto.', 'Harum magnam cumque eligendi et cupiditate laboriosam ex dolor. Autem voluptatem dignissimos aut esse dolorem non suscipit et. Officiis officiis dolorum veritatis vitae consequatur expedita similique.', 'Under Review', '2025-02-08 21:07:18', '2025-07-15 03:40:32', 'Itaque nam voluptatem nulla ab consequatur voluptatum.', '2025-07-23 13:49:10', '2025-07-23 13:49:10'),
(49, 17, NULL, 5, 'Nobis ipsa et in necessitatibus beatae.', 'Harum quod soluta quia consequatur sint. Natus non deserunt eligendi vero laudantium sunt consequatur. Quam nesciunt eum a recusandae.', 'Pending', '2025-04-30 01:17:58', NULL, NULL, '2025-07-23 13:49:10', '2025-07-23 13:49:10'),
(50, 17, NULL, 4, 'Est omnis excepturi necessitatibus.', 'Sed maiores omnis enim provident officia voluptates. Earum nesciunt velit rem aut perspiciatis nobis.', 'Rejected', '2025-05-14 20:27:36', NULL, NULL, '2025-07-23 13:49:10', '2025-07-23 13:49:10'),
(51, 18, NULL, 2, 'Ratione voluptas perspiciatis.', 'Ipsam error blanditiis deserunt amet aut quia quod. Rerum dolorum magnam dolore ut ipsa hic cum. Deleniti ullam animi non corporis voluptas consequuntur.', 'Pending', '2025-03-15 05:34:28', NULL, NULL, '2025-07-23 13:49:11', '2025-07-23 13:49:11'),
(52, 18, NULL, 2, 'Qui excepturi temporibus ipsam quia provident.', 'Ab necessitatibus dolor fugit aut ex praesentium aut. Eaque molestias voluptatibus libero inventore. Eum amet itaque ea nam et commodi.', 'Pending', '2025-06-11 11:42:55', NULL, NULL, '2025-07-23 13:49:11', '2025-07-23 13:49:11'),
(53, 18, NULL, 6, 'Cumque repellendus et et eum.', 'Saepe exercitationem id dolore expedita voluptatem qui nulla. Expedita in omnis ea. Facilis cupiditate eum ipsum quidem.', 'Pending', '2025-02-22 14:06:47', NULL, 'Aut quia ut harum officia illum.', '2025-07-23 13:49:11', '2025-07-23 13:49:11'),
(54, 19, NULL, 3, 'Eaque tempore aliquam harum.', 'Libero non amet incidunt explicabo eius voluptas in. Eos sint architecto veniam ullam qui. Facilis sed molestiae id enim illo cum dolorum. Necessitatibus et delectus velit magnam rerum officiis modi similique.', 'Approved', '2025-05-22 15:20:12', '2025-07-02 19:53:49', 'Eum iste eius debitis est voluptates.', '2025-07-23 13:49:11', '2025-07-23 13:49:11'),
(55, 19, NULL, 1, 'Autem ut quae quia nostrum quos.', 'Consequatur tempora rerum nobis ex suscipit neque tempora. Assumenda sit qui ut quod omnis iure. Et odit accusamus laborum perferendis quod tempora sit. Nisi sequi quisquam neque eveniet consectetur. Ut aut soluta alias ipsam vel atque expedita doloremque.', 'Pending', '2025-06-14 09:10:31', NULL, NULL, '2025-07-23 13:49:11', '2025-07-23 13:49:11'),
(56, 20, NULL, 7, 'Quisquam ut nulla commodi recusandae.', 'Dolorum vel non asperiores qui nobis ad ad est. Hic aut molestiae qui quia sed aut. Ipsam commodi fugit iure et. Fugit debitis rerum voluptatem aut aspernatur beatae.', 'Rejected', '2025-06-01 16:46:43', '2025-05-02 12:10:34', NULL, '2025-07-23 13:49:11', '2025-07-23 13:49:11'),
(57, 20, NULL, 3, 'Commodi quae aut reprehenderit consequatur.', 'Sed ut eum assumenda officia iure vel distinctio et. Ut illum tenetur ut illum voluptas. Et est inventore commodi recusandae. Iure maiores laborum deserunt esse ut.', 'Under Review', '2025-07-04 06:13:30', '2025-05-08 09:29:40', NULL, '2025-07-23 13:49:11', '2025-07-23 13:49:11'),
(58, 3, NULL, 4, 'Marvin William', 'Esse id expedita et', 'Pending', '2025-07-23 16:24:32', NULL, NULL, '2025-07-23 16:24:32', '2025-07-23 16:24:32'),
(59, 3, NULL, 6, 'Cooper Gonzales', 'Pariatur Veniam in', 'Pending', '2025-07-23 16:24:49', NULL, NULL, '2025-07-23 16:24:49', '2025-07-23 16:24:49'),
(60, 3, NULL, 9, 'Patient', 'Tempore commodo sim', 'Pending', '2025-07-23 16:30:39', NULL, NULL, '2025-07-23 16:30:39', '2025-07-23 16:30:39'),
(61, 3, NULL, 4, 'Patient', 'Voluptatem aut labo', 'Pending', '2025-07-23 16:31:07', NULL, NULL, '2025-07-23 16:31:07', '2025-07-23 16:31:07');

-- --------------------------------------------------------

--
-- Table structure for table `application_documents`
--

CREATE TABLE `application_documents` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `application_id` bigint(20) UNSIGNED NOT NULL,
  `filename` varchar(255) NOT NULL,
  `filepath` varchar(255) NOT NULL,
  `filetype` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `application_documents`
--

INSERT INTO `application_documents` (`id`, `application_id`, `filename`, `filepath`, `filetype`, `created_at`, `updated_at`) VALUES
(1, 61, '۱-min (1).png', 'documents/8s31m1xk1OvQoCwpMDWoGzdV6f0H9x6Bae2fXwte.png', 'image/png', '2025-07-23 16:31:07', '2025-07-23 16:31:07');

-- --------------------------------------------------------

--
-- Table structure for table `cache`
--

CREATE TABLE `cache` (
  `key` varchar(255) NOT NULL,
  `value` mediumtext NOT NULL,
  `expiration` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `cache_locks`
--

CREATE TABLE `cache_locks` (
  `key` varchar(255) NOT NULL,
  `owner` varchar(255) NOT NULL,
  `expiration` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `events`
--

CREATE TABLE `events` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `date` datetime NOT NULL,
  `location` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `event_sponsorships`
--

CREATE TABLE `event_sponsorships` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `event_id` bigint(20) UNSIGNED NOT NULL,
  `sponsor_id` bigint(20) UNSIGNED NOT NULL,
  `amount` decimal(12,2) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `failed_jobs`
--

CREATE TABLE `failed_jobs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `uuid` varchar(255) NOT NULL,
  `connection` text NOT NULL,
  `queue` text NOT NULL,
  `payload` longtext NOT NULL,
  `exception` longtext NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `jobs`
--

CREATE TABLE `jobs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `queue` varchar(255) NOT NULL,
  `payload` longtext NOT NULL,
  `attempts` tinyint(3) UNSIGNED NOT NULL,
  `reserved_at` int(10) UNSIGNED DEFAULT NULL,
  `available_at` int(10) UNSIGNED NOT NULL,
  `created_at` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `job_batches`
--

CREATE TABLE `job_batches` (
  `id` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `total_jobs` int(11) NOT NULL,
  `pending_jobs` int(11) NOT NULL,
  `failed_jobs` int(11) NOT NULL,
  `failed_job_ids` longtext NOT NULL,
  `options` mediumtext DEFAULT NULL,
  `cancelled_at` int(11) DEFAULT NULL,
  `created_at` int(11) NOT NULL,
  `finished_at` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `messages`
--

CREATE TABLE `messages` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `sender_id` bigint(20) UNSIGNED NOT NULL,
  `receiver_id` bigint(20) UNSIGNED NOT NULL,
  `content` text NOT NULL,
  `is_read` tinyint(1) NOT NULL DEFAULT 0,
  `sent_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `migrations`
--

CREATE TABLE `migrations` (
  `id` int(10) UNSIGNED NOT NULL,
  `migration` varchar(255) NOT NULL,
  `batch` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(1, '0001_01_01_000000_create_users_table', 1),
(2, '0001_01_01_000001_create_cache_table', 1),
(3, '0001_01_01_000002_create_jobs_table', 1),
(4, '2025_07_10_173431_create_roles_table', 1),
(5, '2025_07_10_173627_create_user_profiles_table', 1),
(6, '2025_07_10_173704_create_sponsor_details_table', 1),
(7, '2025_07_10_173832_add_role_id_in_users_table', 1),
(8, '2025_07_15_215252_create_sponsorship_programs_table', 1),
(9, '2025_07_15_215312_create_sponsorships_table', 1),
(10, '2025_07_15_215353_create_sponsor_reviews_table', 1),
(11, '2025_07_15_215422_create_patients_table', 1),
(12, '2025_07_15_215451_create_applications_table', 1),
(13, '2025_07_15_215556_create_application_documents_table', 1),
(14, '2025_07_15_215902_create_messages_table', 1),
(15, '2025_07_15_215935_create_events_table', 1),
(16, '2025_07_15_220020_create_event_sponsorships_table', 1);

-- --------------------------------------------------------

--
-- Table structure for table `password_reset_tokens`
--

CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) NOT NULL,
  `token` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `patients`
--

CREATE TABLE `patients` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `marital_status` varchar(255) DEFAULT NULL,
  `blood_group` varchar(255) DEFAULT NULL,
  `diagnosis` varchar(255) DEFAULT NULL,
  `diagnosis_date` date DEFAULT NULL,
  `disease_stage` varchar(255) DEFAULT NULL,
  `disease_type` varchar(255) DEFAULT NULL,
  `genetic_test` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `patients`
--

INSERT INTO `patients` (`id`, `user_id`, `marital_status`, `blood_group`, `diagnosis`, `diagnosis_date`, `disease_stage`, `disease_type`, `genetic_test`, `created_at`, `updated_at`) VALUES
(1, 5, 'widowed', 'AB+', 'Skin Cancer', '2024-02-20', 'Stage IV', 'Metastatic', 'Positive', '2025-07-23 13:49:06', '2025-07-23 13:49:06'),
(2, 6, 'divorced', 'A+', 'Brain Tumor', '2023-09-26', 'Stage II', 'Primary', 'Not Tested', '2025-07-23 13:49:07', '2025-07-23 13:49:07'),
(3, 7, 'married', 'O-', 'Lymphoma', '2023-09-19', 'Stage IV', 'Metastatic', 'Pending', '2025-07-23 13:49:07', '2025-07-23 13:49:07'),
(4, 8, 'widowed', 'B-', 'Brain Tumor', '2023-12-22', 'Stage IV', 'Secondary', 'Positive', '2025-07-23 13:49:07', '2025-07-23 13:49:07'),
(5, 9, 'widowed', 'O-', 'Skin Cancer', '2024-04-04', 'Stage III', 'Primary', 'Positive', '2025-07-23 13:49:07', '2025-07-23 13:49:07'),
(6, 10, 'married', 'O+', 'Lung Cancer', '2023-11-18', 'Stage I', 'Secondary', 'Negative', '2025-07-23 13:49:08', '2025-07-23 13:49:08'),
(7, 11, 'widowed', 'A+', 'Colorectal Cancer', '2024-04-26', 'Stage II', 'Secondary', 'Positive', '2025-07-23 13:49:08', '2025-07-23 13:49:08'),
(8, 12, 'married', 'AB-', 'Breast Cancer', '2024-08-14', 'Stage I', 'Primary', 'Not Tested', '2025-07-23 13:49:08', '2025-07-23 13:49:08'),
(9, 13, 'divorced', 'B+', 'Skin Cancer', '2024-12-29', 'Stage III', 'Primary', 'Not Tested', '2025-07-23 13:49:08', '2025-07-23 13:49:08'),
(10, 14, 'single', 'AB+', 'Lung Cancer', '2024-01-31', 'Stage II', 'Metastatic', 'Negative', '2025-07-23 13:49:09', '2025-07-23 13:49:09'),
(11, 15, 'married', 'O+', 'Breast Cancer', '2024-11-30', 'Stage IV', 'Metastatic', 'Positive', '2025-07-23 13:49:09', '2025-07-23 13:49:09'),
(12, 16, 'married', 'AB-', 'Lymphoma', '2024-03-30', 'Stage I', 'Primary', 'Not Tested', '2025-07-23 13:49:09', '2025-07-23 13:49:09'),
(13, 17, 'widowed', 'O-', 'Leukemia', '2024-03-27', 'Stage I', 'Primary', 'Pending', '2025-07-23 13:49:09', '2025-07-23 13:49:09'),
(14, 18, 'widowed', 'B+', 'Skin Cancer', '2024-08-11', 'Stage IV', 'Secondary', 'Not Tested', '2025-07-23 13:49:10', '2025-07-23 13:49:10'),
(15, 19, 'widowed', 'O-', 'Skin Cancer', '2023-09-27', 'Stage I', 'Primary', 'Pending', '2025-07-23 13:49:10', '2025-07-23 13:49:10'),
(16, 20, 'single', 'B+', 'Brain Tumor', '2025-05-20', 'Stage II', 'Metastatic', 'Negative', '2025-07-23 13:49:10', '2025-07-23 13:49:10'),
(17, 21, 'single', 'B+', 'Breast Cancer', '2024-01-27', 'Stage IV', 'Secondary', 'Negative', '2025-07-23 13:49:10', '2025-07-23 13:49:10'),
(18, 22, 'divorced', 'AB-', 'Colorectal Cancer', '2023-11-18', 'Stage III', 'Metastatic', 'Pending', '2025-07-23 13:49:11', '2025-07-23 13:49:11'),
(19, 23, 'widowed', 'A+', 'Leukemia', '2025-07-11', 'Stage III', 'Secondary', 'Not Tested', '2025-07-23 13:49:11', '2025-07-23 13:49:11'),
(20, 24, 'single', 'A-', 'Prostate Cancer', '2024-03-02', 'Stage III', 'Secondary', 'Positive', '2025-07-23 13:49:11', '2025-07-23 13:49:11'),
(21, 3, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-07-23 15:21:32', '2025-07-23 15:21:32');

-- --------------------------------------------------------

--
-- Table structure for table `roles`
--

CREATE TABLE `roles` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `roles`
--

INSERT INTO `roles` (`id`, `name`) VALUES
(1, 'admin'),
(4, 'casemanager'),
(2, 'patient'),
(3, 'sponsor');

-- --------------------------------------------------------

--
-- Table structure for table `sessions`
--

CREATE TABLE `sessions` (
  `id` varchar(255) NOT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `payload` longtext NOT NULL,
  `last_activity` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `sessions`
--

INSERT INTO `sessions` (`id`, `user_id`, `ip_address`, `user_agent`, `payload`, `last_activity`) VALUES
('EIVIyrpet3eqS7qcfOexW39JZYg2YXsKTWZ9Oc0t', 3, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoiZk9QbkRCNzNzaE5xYTBSbTV6b0pRdUo4SER4S3MwTnRnZ01VbmVpYSI7czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6NDQ6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMC9wYXRpZW50L215LWFwcGxpY2F0aW9uIjt9czo1MDoibG9naW5fd2ViXzU5YmEzNmFkZGMyYjJmOTQwMTU4MGYwMTRjN2Y1OGVhNGUzMDk4OWQiO2k6Mzt9', 1753378637),
('Qq8bFRyvMT9Famdm5WDIObYYNpb4Gx33JjL7CTSZ', 3, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoiY1E1aGYwVDNwUVRlajlMaWFUQWp6d1NNSjNMcFJQNVM4YW9ZMUhXViI7czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6NDg6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMC9wYXRpZW50L2NyZWF0ZS1hcHBsaWNhdGlvbiI7fXM6NTA6ImxvZ2luX3dlYl81OWJhMzZhZGRjMmIyZjk0MDE1ODBmMDE0YzdmNThlYTRlMzA5ODlkIjtpOjM7fQ==', 1753314371);

-- --------------------------------------------------------

--
-- Table structure for table `sponsorships`
--

CREATE TABLE `sponsorships` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `sponsor_id` bigint(20) UNSIGNED NOT NULL,
  `sponsorship_program_id` bigint(20) UNSIGNED NOT NULL,
  `amount` decimal(12,2) NOT NULL,
  `date` date NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `sponsorship_programs`
--

CREATE TABLE `sponsorship_programs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `goal_amount` decimal(12,2) NOT NULL DEFAULT 0.00,
  `raised_amount` decimal(12,2) NOT NULL DEFAULT 0.00,
  `start_date` date DEFAULT NULL,
  `end_date` date DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `sponsorship_programs`
--

INSERT INTO `sponsorship_programs` (`id`, `title`, `description`, `goal_amount`, `raised_amount`, `start_date`, `end_date`, `created_at`, `updated_at`) VALUES
(1, 'Patient Care Assistance', 'Est qui repellat at repudiandae accusantium consequuntur. Quos facere porro minima eaque dolorem veritatis repellendus. Tenetur ut impedit esse qui ipsa. Delectus qui est et dolorem.', 351351.00, 40970.00, '2025-04-16', '2026-06-30', '2025-07-23 13:49:06', '2025-07-23 13:49:06'),
(2, 'Surgical Assistance Fund', 'Amet est ut voluptatem. Praesentium quisquam placeat perferendis neque aliquam soluta. Ut suscipit eum officiis ipsum temporibus sapiente eum. Quos iure et voluptas.', 120732.00, 114668.00, '2025-05-25', '2027-04-06', '2025-07-23 13:49:06', '2025-07-23 13:49:06'),
(3, 'Emergency Medical Aid', 'Perferendis accusamus illum qui exercitationem soluta a consectetur. Optio veniam nisi optio animi. Praesentium voluptas dolorem velit aliquid aut.', 402919.00, 54557.00, '2024-12-17', NULL, '2025-07-23 13:49:06', '2025-07-23 13:49:06'),
(4, 'Medication Support Program', 'Eaque voluptatem omnis consequatur eum ipsam. Dolorem reiciendis eum esse consequatur sed at cupiditate recusandae. Dolorem unde deleniti quae consectetur vero. Eos earum dolore repellendus cumque in repellat. Deleniti dicta possimus eveniet veniam.', 242887.00, 233486.00, '2025-03-27', '2026-12-29', '2025-07-23 13:49:06', '2025-07-23 13:49:06'),
(5, 'Chemotherapy Support Fund', 'Debitis enim nesciunt illum et rerum. Consectetur dolore nam soluta sint. Facilis distinctio omnis enim perspiciatis quo magni. Ut sed quis qui aperiam ipsum.', 67386.00, 67136.00, '2024-10-07', NULL, '2025-07-23 13:49:06', '2025-07-23 13:49:06'),
(6, 'Radiation Therapy Program', 'Omnis sunt voluptates nemo. Illum dolorem temporibus neque quam officia dolorem. Omnis dicta vel laudantium earum sunt sit. Quaerat laborum qui eligendi voluptates velit ut. Et voluptatibus distinctio facere doloremque ea.', 499141.00, 192308.00, '2025-03-30', '2026-09-20', '2025-07-23 13:49:06', '2025-07-23 13:49:06'),
(7, 'Surgical Assistance Fund', 'Iure voluptatem culpa eos debitis eius ex nesciunt in. Voluptatibus quibusdam quis ea ad voluptatem est assumenda ut. Voluptates facilis fugit et ipsum quia voluptatibus. Unde culpa aspernatur sequi vitae ut.', 236753.00, 110430.00, '2024-08-05', '2026-08-22', '2025-07-23 13:49:06', '2025-07-23 13:49:06'),
(8, 'Radiation Therapy Program', 'Eaque culpa enim et sed amet dolorum provident. Exercitationem consequatur vero repellat necessitatibus ducimus ipsa nemo. Non dolores et reiciendis quia. Excepturi maxime odio voluptates ea et rerum.', 196965.00, 125025.00, '2025-01-01', '2026-03-22', '2025-07-23 13:49:06', '2025-07-23 13:49:06'),
(9, 'Medication Support Program', 'Et esse aperiam est assumenda. Voluptates minus culpa qui explicabo et corporis. Magni soluta accusantium ullam voluptatem ex. Ut veniam voluptas inventore qui saepe aliquam. Consectetur illum illo delectus neque. Officiis rerum sed qui in cum quae.', 426816.00, 43572.00, '2024-08-06', '2027-03-24', '2025-07-23 13:49:06', '2025-07-23 13:49:06'),
(10, 'Chemotherapy Support Fund', 'Quia nulla minus non iure sit. Quibusdam delectus totam quo. Et eaque voluptate neque aut esse laborum. Autem magnam dignissimos optio ratione qui suscipit illo. Consequatur dolores qui dolorem.', 58216.00, 29308.00, '2024-11-27', '2026-09-22', '2025-07-23 13:49:06', '2025-07-23 13:49:06');

-- --------------------------------------------------------

--
-- Table structure for table `sponsor_details`
--

CREATE TABLE `sponsor_details` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `company_name` varchar(255) NOT NULL,
  `registration_number` varchar(255) DEFAULT NULL,
  `company_email` varchar(255) DEFAULT NULL,
  `company_phone` varchar(255) DEFAULT NULL,
  `company_type` varchar(255) DEFAULT NULL,
  `logo` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `sponsor_reviews`
--

CREATE TABLE `sponsor_reviews` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `sponsor_id` bigint(20) UNSIGNED NOT NULL,
  `rating` tinyint(4) NOT NULL,
  `comment` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `email` varchar(255) NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `remember_token` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `role_id` bigint(20) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `email`, `email_verified_at`, `password`, `remember_token`, `created_at`, `updated_at`, `role_id`) VALUES
(1, 'admin@example.com', NULL, '$2y$12$Q6MtYF7reedfpguYDqFxL.gaeP/M3uCnS7/3.6qs/wg/3c1dDPcS.', NULL, '2025-07-23 13:49:05', '2025-07-23 13:49:05', 1),
(2, 'sponsor@example.com', NULL, '$2y$12$n5rfAxSRwlpVUt2H/tXrF.LET0lMk8TpwCHvwNoyXCtlmtFW44oCy', NULL, '2025-07-23 13:49:05', '2025-07-23 13:49:05', 3),
(3, 'patient@example.com', NULL, '$2y$12$iuk9/NCO5ytY3QRiLRLwPuN6Mp4CRidP87.yAAgoXvvSTfljOQqVq', NULL, '2025-07-23 13:49:05', '2025-07-23 13:49:05', 2),
(4, 'manager@example.com', NULL, '$2y$12$sm1LwIzy..DuDiW7dUF8/uTgTFtRTFJ0TgwNwh5pXbLyB.lu7mnBe', NULL, '2025-07-23 13:49:05', '2025-07-23 13:49:05', 4),
(5, 'patient1@example.com', NULL, '$2y$12$XMVHKxnUB4czTjo4MoVwmOm87XYmmWm/26YAescC/iCPbKdAiuBii', NULL, '2025-07-23 13:49:06', '2025-07-23 13:49:06', 2),
(6, 'patient2@example.com', NULL, '$2y$12$KeB0icqut52htIzCVgNDB.3EY/rlXHBfMs49MAlOAa0uA3AfZSqpa', NULL, '2025-07-23 13:49:07', '2025-07-23 13:49:07', 2),
(7, 'patient3@example.com', NULL, '$2y$12$JJnylttPrTNoLiFBt6VaFuo.4WBKu.S9.jVkIGifAitYc5tCvwOHK', NULL, '2025-07-23 13:49:07', '2025-07-23 13:49:07', 2),
(8, 'patient4@example.com', NULL, '$2y$12$UFeAYAKneqP8GcnvFc4KuuiP4qrbhPBAKpRXbEnx7len1A1Ls0KrG', NULL, '2025-07-23 13:49:07', '2025-07-23 13:49:07', 2),
(9, 'patient5@example.com', NULL, '$2y$12$4B8VbUPtLiF7GQHm0R/rpOaRi7ya2bWBrYRoPogb3JKtxiI1Z3B0.', NULL, '2025-07-23 13:49:07', '2025-07-23 13:49:07', 2),
(10, 'patient6@example.com', NULL, '$2y$12$eftbTWzJeAAEAXUtjaUKb.cuUj3c/IL44EP47qqqFRXe6mFvLBofa', NULL, '2025-07-23 13:49:08', '2025-07-23 13:49:08', 2),
(11, 'patient7@example.com', NULL, '$2y$12$WEypARIS0vgRCwZO8TlEwO6eMkZaGjxXFjg0nOALu.mjBtbWMQEWi', NULL, '2025-07-23 13:49:08', '2025-07-23 13:49:08', 2),
(12, 'patient8@example.com', NULL, '$2y$12$9BObW3nU1ETfDkEmFmRgqeeZrRGLTw0AGUNZU5.yVi16IgiRTKVjm', NULL, '2025-07-23 13:49:08', '2025-07-23 13:49:08', 2),
(13, 'patient9@example.com', NULL, '$2y$12$1Cak.VkD8pF3PtxRNF0Tiu7bQmW6mxeGaAEr66GB.qnNNb2m0.tFm', NULL, '2025-07-23 13:49:08', '2025-07-23 13:49:08', 2),
(14, 'patient10@example.com', NULL, '$2y$12$GDTXp4DgP.kSz5oGKjKrMueFAsvvas.E8bfhh19HhXYyAyw/KpAaC', NULL, '2025-07-23 13:49:09', '2025-07-23 13:49:09', 2),
(15, 'patient11@example.com', NULL, '$2y$12$BoNZC87Xev6EofI6sdAkOObguVoPAT2gbGGUddCAqxzfvP421Txf.', NULL, '2025-07-23 13:49:09', '2025-07-23 13:49:09', 2),
(16, 'patient12@example.com', NULL, '$2y$12$TFposqFzg48nCF0os9NqCeSvySTQydH/PvFwLz0zXNBkndCcum.oW', NULL, '2025-07-23 13:49:09', '2025-07-23 13:49:09', 2),
(17, 'patient13@example.com', NULL, '$2y$12$5AtBgsntWKCr3CwxmZbzi.eMqNXyd5J75ZCO5FQhTuP7qlHom4AC6', NULL, '2025-07-23 13:49:09', '2025-07-23 13:49:09', 2),
(18, 'patient14@example.com', NULL, '$2y$12$bmuyqhUtPg1xxhkYdi4kduHJnRF1VYdwcQBq5bnuz.bkEFzGcKg8a', NULL, '2025-07-23 13:49:10', '2025-07-23 13:49:10', 2),
(19, 'patient15@example.com', NULL, '$2y$12$vMraxP3pTjAKAMfzvbTi9.n9IYwIlyt7PbEAzOXMHK9go4GxKzNQa', NULL, '2025-07-23 13:49:10', '2025-07-23 13:49:10', 2),
(20, 'patient16@example.com', NULL, '$2y$12$P7ak60O1lqiRxJUpNoGpJOt7KOh11MWwG9mIPuCdTA48YGYqe30nm', NULL, '2025-07-23 13:49:10', '2025-07-23 13:49:10', 2),
(21, 'patient17@example.com', NULL, '$2y$12$ESb5Hl6MJ4WMfpModsXVxeT8UQwn1Nz1W53BeQHQUkN5bLiiPuo.a', NULL, '2025-07-23 13:49:10', '2025-07-23 13:49:10', 2),
(22, 'patient18@example.com', NULL, '$2y$12$4JDJsGMHbmX3icxwXxaWceF9X/kssinXrW6W4xOohA1jgH9veyL4i', NULL, '2025-07-23 13:49:11', '2025-07-23 13:49:11', 2),
(23, 'patient19@example.com', NULL, '$2y$12$hq1sp7dSga8Ioh/usfBNd.UXYaosoMTUfc7I8AvZzHrNn4MDW7vBa', NULL, '2025-07-23 13:49:11', '2025-07-23 13:49:11', 2),
(24, 'patient20@example.com', NULL, '$2y$12$H6QVD7iu5onbMIQtQlCfjeTr0yhP8IMhUSJjt6DVJY1IDp94h4Rwq', NULL, '2025-07-23 13:49:11', '2025-07-23 13:49:11', 2);

-- --------------------------------------------------------

--
-- Table structure for table `user_profiles`
--

CREATE TABLE `user_profiles` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `full_name` varchar(255) NOT NULL,
  `phone` varchar(255) NOT NULL,
  `date_of_birth` date DEFAULT NULL,
  `gender` enum('male','female','other') DEFAULT NULL,
  `location` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `user_profiles`
--

INSERT INTO `user_profiles` (`id`, `user_id`, `full_name`, `phone`, `date_of_birth`, `gender`, `location`, `created_at`, `updated_at`) VALUES
(1, 1, 'Admin', '555-123-4334', '1969-07-23', 'other', NULL, '2025-07-23 13:49:05', '2025-07-23 13:49:05'),
(2, 2, 'Sponsor', '555-123-7455', '1986-07-23', 'other', NULL, '2025-07-23 13:49:05', '2025-07-23 13:49:05'),
(3, 3, 'Patient', '555-123-5163', '2000-07-23', 'other', NULL, '2025-07-23 13:49:05', '2025-07-23 13:49:05'),
(4, 4, 'Manager', '555-123-4110', '1971-07-23', 'other', NULL, '2025-07-23 13:49:05', '2025-07-23 13:49:05'),
(5, 5, 'Mr. Jaleel Zboncak', '1-970-879-6501', '1973-10-12', 'other', 'New Darren, Northern Mariana Islands', '2025-07-23 13:49:06', '2025-07-23 13:49:06'),
(6, 6, 'Scottie Ankunding PhD', '(978) 891-0334', '1970-04-01', 'other', 'Felicitaport, Barbados', '2025-07-23 13:49:07', '2025-07-23 13:49:07'),
(7, 7, 'Minnie Grimes', '786-889-7245', '1991-03-22', 'other', 'Arielhaven, Nepal', '2025-07-23 13:49:07', '2025-07-23 13:49:07'),
(8, 8, 'Samson Schultz', '+17746589341', '1979-04-17', 'female', 'New Alexandro, Bosnia and Herzegovina', '2025-07-23 13:49:07', '2025-07-23 13:49:07'),
(9, 9, 'Antone Buckridge', '917.582.3089', '1994-10-14', 'female', 'Jeramiefort, Myanmar', '2025-07-23 13:49:07', '2025-07-23 13:49:07'),
(10, 10, 'Dominic Fritsch III', '862-274-2190', '1978-08-17', 'other', 'Kohlertown, Philippines', '2025-07-23 13:49:08', '2025-07-23 13:49:08'),
(11, 11, 'Prof. Cassidy Hintz', '+18437798822', '1990-11-10', 'other', 'West Terrell, Ukraine', '2025-07-23 13:49:08', '2025-07-23 13:49:08'),
(12, 12, 'Rosalinda Wehner', '+1.904.414.2473', '2007-03-29', 'other', 'New Caleigh, Netherlands Antilles', '2025-07-23 13:49:08', '2025-07-23 13:49:08'),
(13, 13, 'Prof. Fanny Ferry V', '+1.515.658.5503', '1987-01-08', 'female', 'Lake Ashtyn, American Samoa', '2025-07-23 13:49:08', '2025-07-23 13:49:08'),
(14, 14, 'Prof. Hardy Goodwin', '(336) 961-9990', '1959-12-09', 'female', 'Katrinachester, China', '2025-07-23 13:49:09', '2025-07-23 13:49:09'),
(15, 15, 'Raven Murazik', '+1.660.347.3142', '1958-06-24', 'female', 'Waltermouth, Seychelles', '2025-07-23 13:49:09', '2025-07-23 13:49:09'),
(16, 16, 'Elza Schowalter IV', '+1-980-710-2574', '1990-12-31', 'other', 'Port Odieland, Ethiopia', '2025-07-23 13:49:09', '2025-07-23 13:49:09'),
(17, 17, 'Greg Schumm', '540.943.1305', '1956-05-15', 'female', 'South Austen, Turks and Caicos Islands', '2025-07-23 13:49:09', '2025-07-23 13:49:09'),
(18, 18, 'Ms. Felicia Russel', '713.651.3402', '1978-05-22', 'male', 'Morarview, Guatemala', '2025-07-23 13:49:10', '2025-07-23 13:49:10'),
(19, 19, 'Creola Koss MD', '+1-650-493-3472', '1965-09-26', 'male', 'Robelmouth, South Georgia and the South Sandwich Islands', '2025-07-23 13:49:10', '2025-07-23 13:49:10'),
(20, 20, 'Brooks Gibson I', '832-744-1116', '2007-07-14', 'other', 'Janiceland, Algeria', '2025-07-23 13:49:10', '2025-07-23 13:49:10'),
(21, 21, 'Vida Bergnaum', '(319) 464-5271', '1972-03-05', 'other', 'Altenwerthmouth, Paraguay', '2025-07-23 13:49:10', '2025-07-23 13:49:10'),
(22, 22, 'Dixie Cruickshank', '+1-212-628-3581', '1997-04-24', 'female', 'Kuhnville, France', '2025-07-23 13:49:11', '2025-07-23 13:49:11'),
(23, 23, 'Meda Bode', '+1-573-618-9058', '1969-05-17', 'male', 'Lake Ethelyn, Czech Republic', '2025-07-23 13:49:11', '2025-07-23 13:49:11'),
(24, 24, 'Alison Williamson', '(727) 592-0048', '1999-09-04', 'male', 'South Hassiebury, Niue', '2025-07-23 13:49:11', '2025-07-23 13:49:11');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `applications`
--
ALTER TABLE `applications`
  ADD PRIMARY KEY (`id`),
  ADD KEY `applications_patient_id_foreign` (`patient_id`),
  ADD KEY `applications_reviewer_id_foreign` (`reviewer_id`),
  ADD KEY `applications_program_id_foreign` (`program_id`);

--
-- Indexes for table `application_documents`
--
ALTER TABLE `application_documents`
  ADD PRIMARY KEY (`id`),
  ADD KEY `application_documents_application_id_foreign` (`application_id`);

--
-- Indexes for table `cache`
--
ALTER TABLE `cache`
  ADD PRIMARY KEY (`key`);

--
-- Indexes for table `cache_locks`
--
ALTER TABLE `cache_locks`
  ADD PRIMARY KEY (`key`);

--
-- Indexes for table `events`
--
ALTER TABLE `events`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `event_sponsorships`
--
ALTER TABLE `event_sponsorships`
  ADD PRIMARY KEY (`id`),
  ADD KEY `event_sponsorships_event_id_foreign` (`event_id`),
  ADD KEY `event_sponsorships_sponsor_id_foreign` (`sponsor_id`);

--
-- Indexes for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`);

--
-- Indexes for table `jobs`
--
ALTER TABLE `jobs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `jobs_queue_index` (`queue`);

--
-- Indexes for table `job_batches`
--
ALTER TABLE `job_batches`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `messages`
--
ALTER TABLE `messages`
  ADD PRIMARY KEY (`id`),
  ADD KEY `messages_sender_id_foreign` (`sender_id`),
  ADD KEY `messages_receiver_id_foreign` (`receiver_id`);

--
-- Indexes for table `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `password_reset_tokens`
--
ALTER TABLE `password_reset_tokens`
  ADD PRIMARY KEY (`email`);

--
-- Indexes for table `patients`
--
ALTER TABLE `patients`
  ADD PRIMARY KEY (`id`),
  ADD KEY `patients_user_id_foreign` (`user_id`);

--
-- Indexes for table `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `roles_name_unique` (`name`);

--
-- Indexes for table `sessions`
--
ALTER TABLE `sessions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sessions_user_id_index` (`user_id`),
  ADD KEY `sessions_last_activity_index` (`last_activity`);

--
-- Indexes for table `sponsorships`
--
ALTER TABLE `sponsorships`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sponsorships_sponsor_id_foreign` (`sponsor_id`),
  ADD KEY `sponsorships_sponsorship_program_id_foreign` (`sponsorship_program_id`);

--
-- Indexes for table `sponsorship_programs`
--
ALTER TABLE `sponsorship_programs`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `sponsor_details`
--
ALTER TABLE `sponsor_details`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sponsor_details_user_id_foreign` (`user_id`);

--
-- Indexes for table `sponsor_reviews`
--
ALTER TABLE `sponsor_reviews`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sponsor_reviews_sponsor_id_foreign` (`sponsor_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `users_email_unique` (`email`),
  ADD KEY `users_role_id_foreign` (`role_id`);

--
-- Indexes for table `user_profiles`
--
ALTER TABLE `user_profiles`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_profiles_user_id_foreign` (`user_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `applications`
--
ALTER TABLE `applications`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=62;

--
-- AUTO_INCREMENT for table `application_documents`
--
ALTER TABLE `application_documents`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `events`
--
ALTER TABLE `events`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `event_sponsorships`
--
ALTER TABLE `event_sponsorships`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `jobs`
--
ALTER TABLE `jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `messages`
--
ALTER TABLE `messages`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `patients`
--
ALTER TABLE `patients`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT for table `roles`
--
ALTER TABLE `roles`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `sponsorships`
--
ALTER TABLE `sponsorships`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `sponsorship_programs`
--
ALTER TABLE `sponsorship_programs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `sponsor_details`
--
ALTER TABLE `sponsor_details`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `sponsor_reviews`
--
ALTER TABLE `sponsor_reviews`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

--
-- AUTO_INCREMENT for table `user_profiles`
--
ALTER TABLE `user_profiles`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `applications`
--
ALTER TABLE `applications`
  ADD CONSTRAINT `applications_patient_id_foreign` FOREIGN KEY (`patient_id`) REFERENCES `patients` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `applications_program_id_foreign` FOREIGN KEY (`program_id`) REFERENCES `sponsorship_programs` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `applications_reviewer_id_foreign` FOREIGN KEY (`reviewer_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `application_documents`
--
ALTER TABLE `application_documents`
  ADD CONSTRAINT `application_documents_application_id_foreign` FOREIGN KEY (`application_id`) REFERENCES `applications` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `event_sponsorships`
--
ALTER TABLE `event_sponsorships`
  ADD CONSTRAINT `event_sponsorships_event_id_foreign` FOREIGN KEY (`event_id`) REFERENCES `events` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `event_sponsorships_sponsor_id_foreign` FOREIGN KEY (`sponsor_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `messages`
--
ALTER TABLE `messages`
  ADD CONSTRAINT `messages_receiver_id_foreign` FOREIGN KEY (`receiver_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `messages_sender_id_foreign` FOREIGN KEY (`sender_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `patients`
--
ALTER TABLE `patients`
  ADD CONSTRAINT `patients_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `sponsorships`
--
ALTER TABLE `sponsorships`
  ADD CONSTRAINT `sponsorships_sponsor_id_foreign` FOREIGN KEY (`sponsor_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `sponsorships_sponsorship_program_id_foreign` FOREIGN KEY (`sponsorship_program_id`) REFERENCES `sponsorship_programs` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `sponsor_details`
--
ALTER TABLE `sponsor_details`
  ADD CONSTRAINT `sponsor_details_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `sponsor_reviews`
--
ALTER TABLE `sponsor_reviews`
  ADD CONSTRAINT `sponsor_reviews_sponsor_id_foreign` FOREIGN KEY (`sponsor_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `users`
--
ALTER TABLE `users`
  ADD CONSTRAINT `users_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `user_profiles`
--
ALTER TABLE `user_profiles`
  ADD CONSTRAINT `user_profiles_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
