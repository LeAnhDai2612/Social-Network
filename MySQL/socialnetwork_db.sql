-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Máy chủ: 127.0.0.1
-- Thời gian đã tạo: Th4 16, 2025 lúc 03:44 PM
-- Phiên bản máy phục vụ: 10.4.32-MariaDB
-- Phiên bản PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Cơ sở dữ liệu: `socialnetwork_db`
--

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `blocked_users_table`
--

CREATE TABLE `blocked_users_table` (
  `id` int(11) NOT NULL,
  `blocker_id` int(11) NOT NULL,
  `blocked_id` int(11) NOT NULL,
  `blocked_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `blocked_users_table`
--

INSERT INTO `blocked_users_table` (`id`, `blocker_id`, `blocked_id`, `blocked_at`) VALUES
(59, 18, 16, '2025-04-11 22:27:21');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `comments_table`
--

CREATE TABLE `comments_table` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `post_id` int(11) NOT NULL,
  `content` varchar(1000) NOT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp(),
  `parent_comment_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `comments_table`
--

INSERT INTO `comments_table` (`id`, `user_id`, `post_id`, `content`, `created_at`, `updated_at`, `parent_comment_id`) VALUES
(21, 15, 16, 'hehe', '2025-04-06 22:02:11', '2025-04-06 22:02:11', NULL),
(23, 15, 16, 'heheeee', '2025-04-06 23:02:28', '2025-04-06 23:02:28', NULL),
(37, 15, 16, 'thich cuoi lam haha', '2025-04-06 23:11:29', '2025-04-06 23:11:29', 23),
(40, 15, 16, 'sao chui tui z', '2025-04-06 23:28:19', '2025-04-06 23:28:19', 22),
(41, 14, 16, 'sao z ta', '2025-04-07 01:01:16', '2025-04-07 01:01:16', NULL),
(42, 15, 16, 'thich cuoi lam haha', '2025-04-07 01:49:59', '2025-04-07 01:49:59', 41),
(43, 15, 21, 'vjp z', '2025-04-10 17:36:18', '2025-04-10 17:36:18', NULL),
(49, 16, 16, 'ua hay z', '2025-04-15 21:31:11', '2025-04-15 21:31:11', 41),
(50, 14, 32, 'ua hay qua z', '2025-04-15 21:33:59', '2025-04-15 21:33:59', NULL),
(53, 17, 32, 'ui hay qua z', '2025-04-15 21:40:10', '2025-04-15 21:40:10', NULL),
(56, 14, 16, 'helo ban', '2025-04-15 23:11:56', '2025-04-15 23:11:56', NULL);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `followers_table`
--

CREATE TABLE `followers_table` (
  `follow_id` int(11) NOT NULL,
  `follower_id` int(11) NOT NULL,
  `followed_id` int(11) NOT NULL,
  `followed_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `followers_table`
--

INSERT INTO `followers_table` (`follow_id`, `follower_id`, `followed_id`, `followed_at`) VALUES
(26, 16, 14, '2025-04-10 19:41:45'),
(39, 15, 17, '2025-04-11 17:04:13'),
(43, 17, 14, '2025-04-11 20:24:11'),
(53, 17, 16, '2025-04-15 21:10:43'),
(62, 14, 15, '2025-04-15 23:10:08');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `likes_table`
--

CREATE TABLE `likes_table` (
  `like_id` int(11) NOT NULL,
  `liker_id` int(11) NOT NULL,
  `post_id` int(11) NOT NULL,
  `liked_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `likes_table`
--

INSERT INTO `likes_table` (`like_id`, `liker_id`, `post_id`, `liked_at`) VALUES
(403, 14, 8, '2025-04-05 07:37:41'),
(406, 15, 8, '2025-04-05 10:34:17'),
(417, 15, 9, '2025-04-05 15:20:02'),
(419, 15, 10, '2025-04-05 18:45:48'),
(423, 14, 9, '2025-04-06 17:12:10'),
(428, 15, 12, '2025-04-06 21:20:30'),
(430, 15, 16, '2025-04-06 21:27:09'),
(435, 14, 16, '2025-04-07 18:44:02'),
(436, 14, 12, '2025-04-07 18:44:40'),
(438, 14, 10, '2025-04-07 18:49:27'),
(439, 16, 19, '2025-04-08 18:27:07'),
(442, 14, 19, '2025-04-09 12:33:46'),
(447, 14, 21, '2025-04-10 19:54:14'),
(454, 16, 21, '2025-04-11 21:48:25'),
(455, 16, 12, '2025-04-15 03:13:17'),
(456, 17, 8, '2025-04-15 20:25:28'),
(457, 17, 9, '2025-04-15 20:25:30'),
(458, 17, 10, '2025-04-15 20:25:32'),
(459, 17, 12, '2025-04-15 20:25:33'),
(460, 17, 16, '2025-04-15 20:25:35'),
(461, 17, 19, '2025-04-15 20:25:36'),
(462, 17, 21, '2025-04-15 20:25:37'),
(463, 17, 29, '2025-04-15 20:25:39'),
(464, 17, 31, '2025-04-15 20:25:40'),
(465, 17, 32, '2025-04-15 20:25:41'),
(466, 14, 31, '2025-04-15 20:30:40'),
(467, 14, 29, '2025-04-15 21:25:25'),
(468, 14, 32, '2025-04-15 21:25:26'),
(469, 16, 29, '2025-04-15 21:25:52'),
(470, 16, 32, '2025-04-15 21:25:53'),
(471, 16, 16, '2025-04-15 21:25:56'),
(472, 16, 9, '2025-04-15 21:25:58'),
(473, 16, 10, '2025-04-15 21:25:59'),
(474, 16, 8, '2025-04-15 21:26:02'),
(475, 14, 44, '2025-04-15 23:09:31');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `messages_table`
--

CREATE TABLE `messages_table` (
  `id` int(11) NOT NULL,
  `sender_id` int(11) NOT NULL,
  `receiver_id` int(11) NOT NULL,
  `content` text NOT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `is_read` tinyint(1) DEFAULT 0,
  `deleted_by_sender` tinyint(1) DEFAULT 0,
  `deleted_by_receiver` tinyint(1) DEFAULT 0,
  `media_path` text DEFAULT NULL,
  `media_type` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `messages_table`
--

INSERT INTO `messages_table` (`id`, `sender_id`, `receiver_id`, `content`, `created_at`, `is_read`, `deleted_by_sender`, `deleted_by_receiver`, `media_path`, `media_type`) VALUES
(1, 15, 16, 'kkk', '2025-04-07 03:16:39', 1, 0, 0, NULL, NULL),
(2, 16, 15, 'gi z cha', '2025-04-07 03:58:16', 1, 0, 0, NULL, NULL),
(3, 15, 16, 'heloo', '2025-04-07 04:19:20', 1, 0, 0, NULL, NULL),
(6, 16, 15, 'kkk', '2025-04-07 04:33:42', 1, 0, 0, NULL, NULL),
(7, 15, 16, 'kk', '2025-04-07 14:45:11', 1, 0, 0, NULL, NULL),
(8, 16, 15, 'dsasd', '2025-04-07 19:24:35', 1, 0, 0, NULL, NULL),
(9, 16, 15, 'ádasdádz', '2025-04-07 19:24:53', 1, 0, 0, NULL, NULL),
(32, 16, 14, 'xin chao nha', '2025-04-14 23:14:05', 1, 0, 0, NULL, NULL),
(34, 14, 16, 'thay gi hok', '2025-04-15 03:57:54', 1, 0, 0, '../uploads/messages/1744664273_z6318744736102_364f716c4bde50abd57b47e6bfbc53f3.jpg', 'image/jpeg'),
(36, 16, 14, 'helo nha', '2025-04-15 04:02:17', 1, 0, 0, NULL, NULL),
(43, 16, 14, 'hay ha', '2025-04-15 04:07:40', 1, 0, 0, '../uploads/messages/1744664860_bysiki7mvru2joscxvhy.mp4', 'video/mp4'),
(46, 16, 14, 'ne', '2025-04-15 04:12:04', 1, 0, 0, '../uploads/messages/1744665124_De2.doc', 'application/msword'),
(49, 17, 14, 'xem cai nay di', '2025-04-15 20:42:13', 1, 0, 0, '../uploads/messages/1744724533_6508577967209.mp4', 'video/mp4'),
(50, 17, 14, 'anh cua wuan ne', '2025-04-15 20:42:26', 1, 0, 0, '../uploads/messages/1744724546_fc645c7a-5951-48a6-8a21-8a8268e0a051.jpg', 'image/jpeg'),
(51, 14, 17, 'dep qua z', '2025-04-15 21:20:52', 0, 0, 0, NULL, NULL),
(52, 15, 16, 'hẻhe', '2025-04-15 23:12:40', 0, 0, 0, '../uploads/messages/1744733560_6508577967209.mp4', 'video/mp4'),
(53, 15, 16, 'j', '2025-04-15 23:12:46', 0, 0, 0, '../uploads/messages/1744733566_chat_ui_feedback_image.png', 'image/png');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `notifications`
--

CREATE TABLE `notifications` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `type` varchar(50) DEFAULT NULL,
  `content` text DEFAULT NULL,
  `is_read` tinyint(1) DEFAULT 0,
  `created_at` datetime DEFAULT current_timestamp(),
  `post_id` int(11) DEFAULT NULL,
  `comment_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `notifications`
--

INSERT INTO `notifications` (`id`, `user_id`, `type`, `content`, `is_read`, `created_at`, `post_id`, `comment_id`) VALUES
(1, 14, 'like', 'Người dùng ID 16 đã thích bài viết của bạn.', 1, '2025-04-15 03:13:17', 12, NULL),
(2, 14, 'reply', 'Người dùng ID 16 đã trả lời bình luận của bạn.', 1, '2025-04-15 03:25:51', 16, 41),
(3, 15, 'like', 'Người dùng ID 17 đã thích bài viết của bạn.', 1, '2025-04-15 20:25:28', 8, NULL),
(4, 15, 'like', 'Người dùng ID 17 đã thích bài viết của bạn.', 1, '2025-04-15 20:25:30', 9, NULL),
(5, 14, 'like', 'Người dùng ID 17 đã thích bài viết của bạn.', 1, '2025-04-15 20:25:32', 10, NULL),
(6, 14, 'like', 'Người dùng ID 17 đã thích bài viết của bạn.', 1, '2025-04-15 20:25:33', 12, NULL),
(7, 15, 'like', 'Người dùng ID 17 đã thích bài viết của bạn.', 1, '2025-04-15 20:25:35', 16, NULL),
(8, 14, 'like', 'Người dùng ID 17 đã thích bài viết của bạn.', 1, '2025-04-15 20:25:36', 19, NULL),
(9, 14, 'like', 'Người dùng ID 17 đã thích bài viết của bạn.', 1, '2025-04-15 20:25:37', 21, NULL),
(10, 16, 'like', 'Người dùng ID 17 đã thích bài viết của bạn.', 1, '2025-04-15 20:25:39', 29, NULL),
(11, 16, 'like', 'Người dùng ID 17 đã thích bài viết của bạn.', 1, '2025-04-15 20:25:41', 32, NULL),
(12, 17, 'like', 'Người dùng ID 14 đã thích bài viết của bạn.', 1, '2025-04-15 20:30:40', 31, NULL),
(13, 16, 'like', 'Người dùng ID 14 đã thích bài viết của bạn.', 1, '2025-04-15 21:25:25', 29, NULL),
(14, 16, 'like', 'Người dùng ID 14 đã thích bài viết của bạn.', 1, '2025-04-15 21:25:26', 32, NULL),
(15, 15, 'like', 'Người dùng ID 16 đã thích bài viết của bạn.', 1, '2025-04-15 21:25:56', 16, NULL),
(16, 15, 'like', 'Người dùng ID 16 đã thích bài viết của bạn.', 1, '2025-04-15 21:25:58', 9, NULL),
(17, 14, 'like', 'Người dùng ID 16 đã thích bài viết của bạn.', 0, '2025-04-15 21:25:59', 10, NULL),
(18, 15, 'like', 'Người dùng ID 16 đã thích bài viết của bạn.', 1, '2025-04-15 21:26:02', 8, NULL),
(19, 14, 'reply', 'Người dùng ID 16 đã trả lời bình luận của bạn.', 0, '2025-04-15 21:31:11', 16, 41),
(20, 16, 'reply', '🗨️ Ai đó đã bình luận vào bài viết của bạn.', 1, '2025-04-15 21:33:59', 32, 50),
(21, 16, 'reply', '🗨️ kakaka đã bình luận vào bài viết của bạn.', 0, '2025-04-15 21:40:08', 32, NULL),
(22, 16, 'reply', '🗨️ kakaka đã bình luận vào bài viết của bạn.', 0, '2025-04-15 21:40:10', 32, NULL),
(23, 15, 'reply', '🗨️ mwuan đã bình luận vào bài viết của bạn.', 1, '2025-04-15 23:11:12', 16, NULL),
(24, 15, 'reply', '🗨️ mwuan đã bình luận vào bài viết của bạn.', 1, '2025-04-15 23:11:53', 16, NULL),
(25, 15, 'reply', '🗨️ mwuan đã bình luận vào bài viết của bạn.', 1, '2025-04-15 23:11:56', 16, NULL);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `otp_table`
--

CREATE TABLE `otp_table` (
  `id` int(11) NOT NULL,
  `email` varchar(255) NOT NULL,
  `otp_code` varchar(6) NOT NULL,
  `expires_at` datetime NOT NULL,
  `verified` tinyint(1) DEFAULT 0,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `otp_table`
--

INSERT INTO `otp_table` (`id`, `email`, `otp_code`, `expires_at`, `verified`, `created_at`) VALUES
(16, 'leandai12@gmail.com', '625013', '2025-04-05 00:19:49', 0, '2025-04-05 05:14:49'),
(35, 'leanhdai2@gmail.com', '983546', '2025-04-05 10:27:15', 0, '2025-04-05 15:22:15'),
(43, 'leanhdai12@gmail.com', '631755', '2025-04-05 13:57:31', 1, '2025-04-05 18:52:31'),
(45, 'thienkhoa1411@gmail.com', '504759', '2025-04-10 22:01:25', 1, '2025-04-10 21:56:25'),
(46, 'daihoa0302@gmail.com', '118965', '2025-04-11 22:15:43', 1, '2025-04-11 22:10:43');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `posts_table`
--

CREATE TABLE `posts_table` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `media_path` varchar(255) DEFAULT NULL,
  `caption` varchar(2200) DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT NULL,
  `image_public_id` varchar(255) DEFAULT NULL,
  `media_type` enum('image','video') DEFAULT 'image',
  `cloudinary_public_id` varchar(255) DEFAULT NULL,
  `privacy` enum('public','followers','custom') NOT NULL DEFAULT 'public',
  `allowed_viewers` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `posts_table`
--

INSERT INTO `posts_table` (`id`, `user_id`, `media_path`, `caption`, `created_at`, `updated_at`, `image_public_id`, `media_type`, `cloudinary_public_id`, `privacy`, `allowed_viewers`) VALUES
(8, 15, 'https://res.cloudinary.com/dy6o43c27/image/upload/v1743805446/momento/posts/lpv4emx5dvc899juuiie.jpg', 'hẻhe', '2025-04-05 00:23:46', NULL, 'momento/posts/lpv4emx5dvc899juuiie', 'image', NULL, 'public', NULL),
(9, 15, 'https://res.cloudinary.com/dy6o43c27/image/upload/v1743805558/momento/posts/h9rabqgp02opj187vl9v.jpg', '1 like mai nghi hoc', '2025-04-05 00:25:38', '2025-04-05 10:43:56', 'momento/posts/h9rabqgp02opj187vl9v', 'image', NULL, 'public', NULL),
(10, 14, 'https://res.cloudinary.com/dy6o43c27/image/upload/v1743813441/momento/posts/lcixqgyj6lwr6eyl7kpg.jpg', 'mai thong minh', '2025-04-05 02:36:59', '2025-04-05 10:16:13', 'momento/posts/lcixqgyj6lwr6eyl7kpg', 'image', NULL, 'public', NULL),
(12, 14, 'https://res.cloudinary.com/dy6o43c27/image/upload/v1743863014/momento/posts/s8s7rvey1wvwotjnhr1k.jpg', 'buc minh qa di thoi, cac ban oi hay giup minh binh tinh nhe!!!', '2025-04-05 16:23:13', NULL, 'momento/posts/s8s7rvey1wvwotjnhr1k', 'image', NULL, 'public', NULL),
(16, 15, 'https://res.cloudinary.com/dy6o43c27/image/upload/v1743946231/socialnetwork/posts/c9vnkzy7l26idpd9hiq0.jpg', 'ngau` qua\' ik', '2025-04-06 20:30:06', NULL, 'socialnetwork/posts/c9vnkzy7l26idpd9hiq0', 'image', NULL, 'public', NULL),
(19, 14, 'https://res.cloudinary.com/dy6o43c27/image/upload/v1744026294/socialnetwork/posts/yivacdmsdy0glrbqqxm9.png', 'icon cua cai web nay dep hok', '2025-04-07 18:44:31', NULL, 'socialnetwork/posts/yivacdmsdy0glrbqqxm9', 'image', NULL, 'public', NULL),
(21, 14, 'https://res.cloudinary.com/dy6o43c27/video/upload/v1744277723/posts/bysiki7mvru2joscxvhy.mp4', 'hehehe', '2025-04-10 16:35:02', '2025-04-10 19:34:12', NULL, 'video', 'posts/bysiki7mvru2joscxvhy', 'public', NULL),
(29, 16, 'https://res.cloudinary.com/dy6o43c27/image/upload/v1744305838/posts/gzge7ox8eipypkuoadzo.jpg', '', '2025-04-11 00:23:37', NULL, NULL, 'image', 'posts/gzge7ox8eipypkuoadzo', 'public', NULL),
(31, 17, 'https://res.cloudinary.com/dy6o43c27/image/upload/v1744366154/posts/xtfc4ymv9f4lhdhxhr7t.jpg', 'kk', '2025-04-11 17:08:52', NULL, NULL, 'image', 'posts/xtfc4ymv9f4lhdhxhr7t', 'followers', NULL),
(32, 16, 'https://res.cloudinary.com/dy6o43c27/image/upload/v1744382683/posts/xzkbwyp25aywia1mfpsw.png', 'hehehe', '2025-04-11 21:44:22', NULL, NULL, 'image', 'posts/xzkbwyp25aywia1mfpsw', 'public', '10'),
(44, 14, 'https://res.cloudinary.com/dy6o43c27/video/upload/v1744733384/posts/1744733355_e8205e5c.mp4', 'hehe', '2025-04-15 23:09:21', NULL, NULL, 'video', 'posts/1744733355_e8205e5c', 'followers', NULL);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `reports_table`
--

CREATE TABLE `reports_table` (
  `id` int(11) NOT NULL,
  `post_id` int(11) NOT NULL,
  `reporter_id` int(11) NOT NULL,
  `reason` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `report_time` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `reports_table`
--

INSERT INTO `reports_table` (`id`, `post_id`, `reporter_id`, `reason`, `description`, `report_time`) VALUES
(1, 10, 15, 'offensive', 'kkk', '2025-04-07 00:37:16'),
(4, 16, 15, 'spam', 'haizz', '2025-04-07 02:53:39'),
(5, 16, 14, 'spam', '', '2025-04-08 20:20:34'),
(6, 8, 16, 'offensive', 'không thoải mái', '2025-04-11 21:53:28'),
(7, 32, 17, 'offensive', 'không thích', '2025-04-15 21:11:00'),
(8, 32, 15, 'offensive', 'k thich', '2025-04-15 23:14:20');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `users_table`
--

CREATE TABLE `users_table` (
  `id` int(11) NOT NULL,
  `username` varchar(30) NOT NULL,
  `full_name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `phone_number` varchar(15) NOT NULL,
  `password` varchar(255) NOT NULL,
  `profile_picture_path` varchar(255) NOT NULL,
  `display_name` varchar(255) NOT NULL,
  `bio` varchar(255) NOT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `image_public_id` varchar(255) DEFAULT NULL,
  `is_verified` tinyint(1) NOT NULL DEFAULT 0,
  `role` enum('user','admin') DEFAULT 'user',
  `is_banned` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `users_table`
--

INSERT INTO `users_table` (`id`, `username`, `full_name`, `email`, `phone_number`, `password`, `profile_picture_path`, `display_name`, `bio`, `created_at`, `image_public_id`, `is_verified`, `role`, `is_banned`) VALUES
(10, 'iadhnael', 'le anh dai', 'leanhdai@gmail.com', '123321', '$2y$10$EQ8jp1d24KQESC.GYHEdO.yJGNE2ioqS24zzS8B55SOhJ7EGKaTRa', 'https://res.cloudinary.com/dy6o43c27/image/upload/v1743795611/momento/profile-pictures/w3zfn7faksuyydkoa2rp.jpg', 'le anh dai', 'Hi I\'m le anh dai!', '2025-04-05 02:39:51', 'momento/profile-pictures/w3zfn7faksuyydkoa2rp', 0, 'user', 0),
(14, 'mwuan', 'lad', 'leanhdai2@gmail.com', '123123', '$2y$10$EDoumwJga3ZEjDU0pHQojO2y7aX84EbSesd6lNzGGgguCZxvjVNl.', 'https://res.cloudinary.com/dy6o43c27/image/upload/v1744723478/socialnetwork/profile-pictures/cghcuhtzik8lmbj05isb.jpg', 'tranminhwuan', 'thich trai dep', '2025-04-05 04:58:06', 'socialnetwork/profile-pictures/cghcuhtzik8lmbj05isb', 1, 'user', 0),
(15, 'lad1', 'lad', 'leanhdai12@gmail.com', '1231231', '$2y$10$ZZ6EXH1v8ARQddYTVUwJtevYS2pCFmY5dLCXSzFzU0obRil2Kr6w.', 'https://res.cloudinary.com/dy6o43c27/image/upload/v1743805263/momento/profile-pictures/j1boujnwuvlw1ggfbkjn.jpg', 'lad', 'Xin chào lad!', '2025-04-05 05:16:02', NULL, 1, 'admin', 0),
(16, 'MaiHnuyh', 'Nhat Mai', 'daihoa@gmail.com', '456456', '$2y$10$pJoO.Dnq103GDGvEkDFEv.NneusgC.0vXsjFWLDpNXpmSJurX3Wme', 'https://res.cloudinary.com/dy6o43c27/image/upload/v1744728133/socialnetwork/profile-pictures/1744728106_dfe810de.jpg', 'MaiHnuyh', 'helo', '2025-04-06 22:02:53', 'socialnetwork/profile-pictures/1744728106_dfe810de', 1, 'user', 0),
(17, 'kakaka', 'Thiên Khoa', 'thienkhoa1411@gmail.com', '234234', '$2y$10$ZSm.NX2WtfJPF3accKOCgeipM9w7FX82YI1glVro5aB7gUKMlNMma', 'https://res.cloudinary.com/dy6o43c27/image/upload/v1744724612/socialnetwork/profile-pictures/1744724586_179b8ae0.jpg', 'minhwuan', 'tui la nguoi cua con cua me tui', '2025-04-10 21:57:02', 'socialnetwork/profile-pictures/1744724586_179b8ae0', 1, 'user', 0),
(18, 'iadhnaelne', 'Lê Anh Đại', 'daihoa0302@gmail.com', '678678', '$2y$10$z8/qH08baPv./eKoeeyzIesXWy7S7ny5iY2gClcIpoMfuAp/1iAYG', 'uploads/avatar_67f93162c97418.71023824.jpg', 'Lê Anh Đại', 'Xin chào Lê Anh Đại!', '2025-04-11 22:11:53', NULL, 1, 'user', 0);

--
-- Chỉ mục cho các bảng đã đổ
--

--
-- Chỉ mục cho bảng `blocked_users_table`
--
ALTER TABLE `blocked_users_table`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_block` (`blocker_id`,`blocked_id`);

--
-- Chỉ mục cho bảng `comments_table`
--
ALTER TABLE `comments_table`
  ADD PRIMARY KEY (`id`),
  ADD KEY `post_id` (`post_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Chỉ mục cho bảng `followers_table`
--
ALTER TABLE `followers_table`
  ADD PRIMARY KEY (`follow_id`),
  ADD KEY `follower_id` (`follower_id`),
  ADD KEY `followed_id` (`followed_id`);

--
-- Chỉ mục cho bảng `likes_table`
--
ALTER TABLE `likes_table`
  ADD PRIMARY KEY (`like_id`),
  ADD KEY `post_id` (`post_id`),
  ADD KEY `liker_id` (`liker_id`) USING BTREE;

--
-- Chỉ mục cho bảng `messages_table`
--
ALTER TABLE `messages_table`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sender_id` (`sender_id`),
  ADD KEY `receiver_id` (`receiver_id`);

--
-- Chỉ mục cho bảng `notifications`
--
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`id`);

--
-- Chỉ mục cho bảng `otp_table`
--
ALTER TABLE `otp_table`
  ADD PRIMARY KEY (`id`);

--
-- Chỉ mục cho bảng `posts_table`
--
ALTER TABLE `posts_table`
  ADD PRIMARY KEY (`id`),
  ADD KEY `posts_table_ibfk_1` (`user_id`);

--
-- Chỉ mục cho bảng `reports_table`
--
ALTER TABLE `reports_table`
  ADD PRIMARY KEY (`id`),
  ADD KEY `post_id` (`post_id`),
  ADD KEY `reporter_id` (`reporter_id`);

--
-- Chỉ mục cho bảng `users_table`
--
ALTER TABLE `users_table`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT cho các bảng đã đổ
--

--
-- AUTO_INCREMENT cho bảng `blocked_users_table`
--
ALTER TABLE `blocked_users_table`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=62;

--
-- AUTO_INCREMENT cho bảng `comments_table`
--
ALTER TABLE `comments_table`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=57;

--
-- AUTO_INCREMENT cho bảng `followers_table`
--
ALTER TABLE `followers_table`
  MODIFY `follow_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=63;

--
-- AUTO_INCREMENT cho bảng `likes_table`
--
ALTER TABLE `likes_table`
  MODIFY `like_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=476;

--
-- AUTO_INCREMENT cho bảng `messages_table`
--
ALTER TABLE `messages_table`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=54;

--
-- AUTO_INCREMENT cho bảng `notifications`
--
ALTER TABLE `notifications`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=26;

--
-- AUTO_INCREMENT cho bảng `otp_table`
--
ALTER TABLE `otp_table`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=47;

--
-- AUTO_INCREMENT cho bảng `posts_table`
--
ALTER TABLE `posts_table`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=45;

--
-- AUTO_INCREMENT cho bảng `reports_table`
--
ALTER TABLE `reports_table`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT cho bảng `users_table`
--
ALTER TABLE `users_table`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- Các ràng buộc cho các bảng đã đổ
--

--
-- Các ràng buộc cho bảng `comments_table`
--
ALTER TABLE `comments_table`
  ADD CONSTRAINT `comments_table_ibfk_1` FOREIGN KEY (`post_id`) REFERENCES `posts_table` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `comments_table_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users_table` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Các ràng buộc cho bảng `followers_table`
--
ALTER TABLE `followers_table`
  ADD CONSTRAINT `followers_table_ibfk_1` FOREIGN KEY (`follower_id`) REFERENCES `users_table` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `followers_table_ibfk_2` FOREIGN KEY (`followed_id`) REFERENCES `users_table` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Các ràng buộc cho bảng `likes_table`
--
ALTER TABLE `likes_table`
  ADD CONSTRAINT `likes_table_ibfk_1` FOREIGN KEY (`liker_id`) REFERENCES `users_table` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `likes_table_ibfk_2` FOREIGN KEY (`post_id`) REFERENCES `posts_table` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Các ràng buộc cho bảng `messages_table`
--
ALTER TABLE `messages_table`
  ADD CONSTRAINT `messages_table_ibfk_1` FOREIGN KEY (`sender_id`) REFERENCES `users_table` (`id`),
  ADD CONSTRAINT `messages_table_ibfk_2` FOREIGN KEY (`receiver_id`) REFERENCES `users_table` (`id`);

--
-- Các ràng buộc cho bảng `posts_table`
--
ALTER TABLE `posts_table`
  ADD CONSTRAINT `posts_table_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users_table` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Các ràng buộc cho bảng `reports_table`
--
ALTER TABLE `reports_table`
  ADD CONSTRAINT `reports_table_ibfk_1` FOREIGN KEY (`post_id`) REFERENCES `posts_table` (`id`),
  ADD CONSTRAINT `reports_table_ibfk_2` FOREIGN KEY (`reporter_id`) REFERENCES `users_table` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
