-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Feb 17, 2025 at 11:18 AM
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
-- Database: `magicbitesdb`
--

-- --------------------------------------------------------

--
-- Table structure for table `comments`
--

CREATE TABLE `comments` (
  `comment_id` int(11) NOT NULL,
  `recipe_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `comment_text` text NOT NULL,
  `comment_date` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `comments`
--

INSERT INTO `comments` (`comment_id`, `recipe_id`, `user_id`, `comment_text`, `comment_date`) VALUES
(11, 54, 1, 'hi', '2025-02-14 13:45:15'),
(12, 57, 12, 'hiiih', '2025-02-14 14:17:56');

-- --------------------------------------------------------

--
-- Table structure for table `favorites`
--

CREATE TABLE `favorites` (
  `favorite_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `recipe_id` int(11) NOT NULL,
  `favorites` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `favorites`
--

INSERT INTO `favorites` (`favorite_id`, `user_id`, `recipe_id`, `favorites`) VALUES
(16, 12, 57, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `ratings`
--

CREATE TABLE `ratings` (
  `rating_id` int(11) NOT NULL,
  `recipe_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `rating` tinyint(4) DEFAULT NULL CHECK (`rating` between 1 and 5)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `ratings`
--

INSERT INTO `ratings` (`rating_id`, `recipe_id`, `user_id`, `rating`) VALUES
(21, 54, 1, 5),
(22, 57, 12, 4);

-- --------------------------------------------------------

--
-- Table structure for table `recipes`
--

CREATE TABLE `recipes` (
  `recipe_id` int(11) NOT NULL,
  `title` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `ingredients` text NOT NULL,
  `instructions` text NOT NULL,
  `image` varchar(255) DEFAULT 'default-recipe.jpg',
  `category` enum('MainCourse','Sides','Drinks','Desserts') DEFAULT NULL,
  `type` enum('Breakfast','Lunch','Dinner','Hot','Cold','Fried','Iced','NULL') NOT NULL,
  `user_id` int(11) NOT NULL,
  `creation_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `avg_rating` float NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `recipes`
--

INSERT INTO `recipes` (`recipe_id`, `title`, `description`, `ingredients`, `instructions`, `image`, `category`, `type`, `user_id`, `creation_date`, `avg_rating`) VALUES
(54, 'fried eggs', 'The best way to fry eggs', 'eggs\r\nsalt\r\nolive oil or butter', '1. Heat a pan: Place a non-stick skillet on the stove over medium heat.\r\n2. Add oil or butter: Once the pan is warm, add a small amount of oil or butter and let it melt.\r\n3. Crack the egg: Crack an egg into a small bowl (this prevents shell bits in the pan), then gently slide it into the skillet.\r\n4. Cook the egg: Let the egg cook for about 2-3 minutes for a runny yolk or longer if you prefer a firmer yolk.\r\n5. Optional - Flip the egg: For over-easy eggs, gently flip the egg with a spatula and cook for another 30 seconds to a minute.\r\n6. Season: Add salt and pepper to taste.\r\n7. Serve: Once cooked to your liking, remove the egg from the pan and serve.', 'download.jpeg', 'MainCourse', 'Breakfast', 1, '2025-02-14 13:44:59', 5),
(57, 'dasaas', 'sdsadsds', 'sdasdasd', 'dsadasdsad', 'download (1).jpeg', 'Desserts', 'Hot', 12, '2025-02-14 14:16:31', 4);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `user_id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `profile_picture` varchar(255) NOT NULL DEFAULT 'uploads/default_profile.jpg'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `username`, `email`, `password`, `profile_picture`) VALUES
(1, 'alis', 'alisayegh555@gmail.com', '$2y$10$byddizDAZWUwzRg693nXG.ckXKFYAKvVFzLpFUuKNwS6ewof4Txfy', 'uploads/about.jpg'),
(2, 'mhamd', 'mhamad@gmail.com', '$2y$10$DuGHlvO5Yn79KAzPmD6SReldglfDFCD7dQp0kBCvAwgD3SAfIprVC', 'uploads/6794dc2392f3e_sideBackground2.jpg'),
(3, 'allosuhi', 'sayeghali37@gmail.com', '$2y$10$TzpFnc3PVsPneq5Md00bUe2Eec8Jbe67NFvs7ZKL7b/5x2laqYa8O', 'profileimages/about.jpg'),
(4, 'samir', 'samir@gmail.com', '$2y$10$1o5eWuuTwltzi5Vw4JJAPuo1OCH7d3nj2h9EcahDSQwyXzk9Jb.ve', 'default_profile.jpg'),
(5, 'saroo5', 'alloush@gmail.com', '$2y$10$Ijt0tZ9HG7hG9mVYIaEFW.iO9XNx.js9/KcyIQUW349atKY9K8d7W', 'default_profile.jpg'),
(6, 'DEFAULT', 'default@gmail.com', '$2y$10$J0FvNfYRul68wda9zCiUaO2b/JzUypfywdgynYh.GAZoZvRdtU4We', 'default_profile.jpg'),
(7, 'fuefjiefj', 'auwheudwhu@gmail.com', '$2y$10$mub9hJDitjiI0toFWDF2F.oqdhkEtlJ34WYMyL59o0QthJnqUDO9O', 'uploads/set-of-restaurant-doodles-food-and-drinks-on-black-background-vector.jpg'),
(11, 'fsddsf', 'asdasdtsat@gmail.com', '$2y$10$GAvD5JVOZYcadI/h1vr7i.qXTFV.910QT8bSjp0eG7ovnkjjTSOGa', 'uploads/default_profile.jpg'),
(12, 'alisaywegh', 'alisayegh432@gmail.com', '$2y$10$FarlzP1AUdluBbFvgFAWeePeYAI9yU6lKUj8KB/mBi1xjrEvfXF1K', 'uploads/download (1).jpeg');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `comments`
--
ALTER TABLE `comments`
  ADD PRIMARY KEY (`comment_id`),
  ADD KEY `recipe_id` (`recipe_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `favorites`
--
ALTER TABLE `favorites`
  ADD PRIMARY KEY (`favorite_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `recipe_id` (`recipe_id`);

--
-- Indexes for table `ratings`
--
ALTER TABLE `ratings`
  ADD PRIMARY KEY (`rating_id`),
  ADD KEY `recipe_id` (`recipe_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `recipes`
--
ALTER TABLE `recipes`
  ADD PRIMARY KEY (`recipe_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `comments`
--
ALTER TABLE `comments`
  MODIFY `comment_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `favorites`
--
ALTER TABLE `favorites`
  MODIFY `favorite_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `ratings`
--
ALTER TABLE `ratings`
  MODIFY `rating_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- AUTO_INCREMENT for table `recipes`
--
ALTER TABLE `recipes`
  MODIFY `recipe_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=58;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `comments`
--
ALTER TABLE `comments`
  ADD CONSTRAINT `comments_ibfk_1` FOREIGN KEY (`recipe_id`) REFERENCES `recipes` (`recipe_id`),
  ADD CONSTRAINT `comments_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`);

--
-- Constraints for table `favorites`
--
ALTER TABLE `favorites`
  ADD CONSTRAINT `favorites_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`),
  ADD CONSTRAINT `favorites_ibfk_2` FOREIGN KEY (`recipe_id`) REFERENCES `recipes` (`recipe_id`);

--
-- Constraints for table `ratings`
--
ALTER TABLE `ratings`
  ADD CONSTRAINT `ratings_ibfk_1` FOREIGN KEY (`recipe_id`) REFERENCES `recipes` (`recipe_id`),
  ADD CONSTRAINT `ratings_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`);

--
-- Constraints for table `recipes`
--
ALTER TABLE `recipes`
  ADD CONSTRAINT `recipes_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
