-- MySQL dump 10.13  Distrib 8.0.42, for Linux (x86_64)
--
-- Host: 127.0.0.1    Database: Cepatshop_starter_with_demo
-- ------------------------------------------------------
-- Server version	8.0.42-0ubuntu0.24.04.1

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!50503 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Dumping data for table `assets`
--

LOCK TABLES `assets` WRITE;
/*!40000 ALTER TABLE `assets` DISABLE KEYS */;
INSERT INTO `assets` (`id`, `filename`, `assetable_type`, `assetable_id`,`variable`, `filepath`, `disk`, `visibility`)
VALUES (1,'EaaUixlQfaNW6tUh07XpeK8jBKc4qijZ429AzkBpS.png',NULL,NULL,NULL,'upload/images/eAW79mi3Aj3jgob01Waeql5Fq8htS5NeEtUjPg5d.png','upload','public'),(2,'LK6aAiMONVVgfmHyp0dtmSKf9F44wIhTvB1OkxYUp.png',NULL,NULL,NULL,'upload/images/HwQtnQ2pZBPwssd6ACm62HG8G3R0IHRxNvDRmPQf.png','upload','public'),(3,'Nkc3YBXG1BCu8sQim7Qejbvu76uDMSwHfiQe3osWp.png',NULL,NULL,NULL,'upload/images/9f5cj4qHXNBQ0tuor2cviax1WSI7qSfjAJOJL9bI.png','upload','public'),(4,'7PuPubQmmfpe96qVIFDq6lkhhhYCrPRmIF3FVN.jpg','Post',1,NULL,'upload/images/lL5vJtFDqtVq99jTt3RJLpIqqGnDYiNi7CzfqDDQ.jpg','upload','public'),(5,'EgTAButCFm175vE4BLWmzBmtrDZR2kXmm1YDNBbxb.jpg',NULL,NULL,NULL,'upload/images/90cQVsJngMicYIb1LNKSCWxlOM6H6bQkrMafNGZf.jpg','upload','public'),(6,'yP9SUPCH2eTb1pSWdwdWFoWTHoGn5jI0O13Cz2Adc.jpg',NULL,NULL,NULL,'upload/images/pCg6aSNGltHBDYDD0euyjkibJ6iXiBgSHAjqXjjO.jpg','upload','public'),(7,'YMrbbWrjew3VsG91lsUGk1KP8ocuLXat4P3Za9ktN.jpg',NULL,NULL,NULL,'upload/images/UqWn3pVm8bgoGJi24wy3sjn95pM1hvsSG5jQZahy.jpg','upload','public');
/*!40000 ALTER TABLE `assets` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping data for table `sliders`
--

LOCK TABLES `sliders` WRITE;
/*!40000 ALTER TABLE `sliders` DISABLE KEYS */;
INSERT INTO `sliders` (`id`, `weight`, `filename`, `post_id`, `filepath`)
 VALUES (1,1,'4lZzp9jpAY 8zS8ZyLSkI Tf1mvIsnG  JGGJ 7HSKQ pP.jpg',NULL,'upload/images/wxM8qkdScvB2tS8IjQ7OmvsFXbYj22icN3B0bxk4.jpg');
/*!40000 ALTER TABLE `sliders` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping data for table `categories`
--


LOCK TABLES `categories` WRITE;
/*!40000 ALTER TABLE `categories` DISABLE KEYS */;
INSERT INTO `categories` (`id`, `title`, `slug`, `category_id`, `filename`, `banner`, `description`, `is_front`, `weight`, `created_at`, `updated_at`, `is_background_banner`)
 VALUES (1,'Baby & Kids','fashion-anak',NULL,'FBVdFQTDNhtJ6S4f6Z69ISh9T8wn6sMZ2MrJeJ2U.png',NULL,NULL,1,1,'2024-10-21 01:57:27','2024-10-21 01:58:17',0),(2,'Makanan & Minuman','makanan-minuman',NULL,'2ncjB5YQ63z7WucI6W8d52O5739rq0xyfydJv92S.png',NULL,NULL,1,1,'2024-10-21 01:57:49','2024-10-21 01:57:49',0),(3,'Fashion','fashion',NULL,'jeQxDfpjfxbM2X2uIco3g8SGV3Jb5uECpr5xVlsF.png',NULL,NULL,1,1,'2024-10-21 01:58:06','2024-10-21 01:58:06',0);
/*!40000 ALTER TABLE `categories` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping data for table `posts`
--



LOCK TABLES `posts` WRITE;
/*!40000 ALTER TABLE `posts` DISABLE KEYS */;
INSERT INTO `posts` (`id`, `title`, `slug`, `tags`, `image`, `body`, `is_promote`, `is_listing`, `created_at`, `updated_at`, `category`, `user_id`)
VALUES (1,'Cara Pembelian','cara-pembelian',NULL,NULL,'<div>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Vestibulum ante ipsum primis in faucibus orci luctus et ultrices posuere cubilia Curae; Donec velit neque, auctor sit amet aliquam vel, ullamcorper sit amet ligula. Sed porttitor lectus nibh. Cras ultricies ligula sed magna dictum porta.</div><div><br></div><div>Vivamus suscipit tortor eget felis porttitor volutpat. Vestibulum ante ipsum primis in faucibus orci luctus et ultrices posuere cubilia Curae; Donec velit neque, auctor sit amet aliquam vel, ullamcorper sit amet ligula. Curabitur non nulla sit amet nisl tempus convallis quis ac lectus. Quisque velit nisi, pretium ut lacinia in, elementum id enim.</div><div><br></div><div>Vestibulum ante ipsum primis in faucibus orci luctus et ultrices posuere cubilia Curae; Donec velit neque, auctor sit amet aliquam vel, ullamcorper sit amet ligula. Cras ultricies ligula sed magna dictum porta. Quisque velit nisi, pretium ut lacinia in, elementum id enim. Cras ultricies ligula sed magna dictum porta.</div>',1,1,'2024-10-21 02:03:39','2024-10-21 02:03:39',NULL,1);
/*!40000 ALTER TABLE `posts` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping data for table `products`
--


LOCK TABLES `products` WRITE;
/*!40000 ALTER TABLE `products` DISABLE KEYS */;
INSERT INTO `products` (`id`, `title`, `slug`, `description`, `stock`, `price`, `sold`, `status`, `category_id`, `weight`, `created_at`, `updated_at`, `sku`, `product_type`)
VALUES (1,'Kripik Pare','kripik-pare','Cras ultricies ligula sed magna dictum porta. Curabitur non nulla sit amet nisl tempus convallis quis ac lectus. Vivamus magna justo, lacinia eget consectetur sed, convallis at tellus. Proin eget tortor risus.',0,0,0,1,2,0,'2024-10-21 01:59:55','2024-10-21 01:59:55','2548ba45-f195-4f79-80cd-9010e35864c0','Default'),(2,'Kripik Rumput Laut','kripik-rumput-laut','Praesent sapien massa, convallis a pellentesque nec, egestas non nisi. Curabitur aliquet quam id dui posuere blandit. Vivamus magna justo, lacinia eget consectetur sed, convallis at tellus. Nulla quis lorem ut libero malesuada feugiat.',1000,10000,0,1,2,500,'2024-10-21 02:00:54','2024-10-21 02:00:54','d4411358-601f-4bcf-ad5f-ff7c842c0e97','Default'),(3,'Kripik talas','kripik-talas','Mauris blandit aliquet elit, eget tincidunt nibh pulvinar a. Curabitur arcu erat, accumsan id imperdiet et, porttitor at sem. Curabitur arcu erat, accumsan id imperdiet et, porttitor at sem. Cras ultricies ligula sed magna dictum porta.',0,0,0,1,2,0,'2024-10-21 02:02:34','2024-10-21 02:02:44','c34dc9e1-d4ed-48fa-bd4f-027b59730feb','Default'),(4,'Tas Sekolah','tas-sekolah','<div>Curabitur non nulla sit amet nisl tempus convallis quis ac lectus. Vestibulum ac diam sit amet quam vehicula elementum sed sit amet dui. Cras ultricies ligula sed magna dictum porta. Curabitur aliquet quam id dui posuere blandit.</div><div><br></div>',10,150000,0,1,1,500,'2024-10-21 02:06:17','2024-10-21 02:06:17','834da766-add3-4cfd-8a9f-fde387d0e8c4','Default'),(5,'Sepatu Anak','sepatu-anak','<div>Curabitur non nulla sit amet nisl tempus convallis quis ac lectus. Vestibulum ac diam sit amet quam vehicula elementum sed sit amet dui. Cras ultricies ligula sed magna dictum porta. Curabitur aliquet quam id dui posuere blandit.</div><div><br></div>',100,75000,0,1,1,500,'2024-10-21 02:07:00','2024-10-21 02:07:00','f661ae31-4487-4647-b309-0f9892c4da65','Default'),(6,'Baju Muslim Anak','baju-muslim-anak','<div>Curabitur non nulla sit amet nisl tempus convallis quis ac lectus. Vestibulum ac diam sit amet quam vehicula elementum sed sit amet dui. Cras ultricies ligula sed magna dictum porta. Curabitur aliquet quam id dui posuere blandit.</div><div><br></div>',20,100000,0,1,1,500,'2024-10-21 02:07:35','2024-10-21 02:07:35','6b6d197f-28c4-4f28-9285-db86b7a687c8','Default');
/*!40000 ALTER TABLE `products` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping data for table `product_varians`
--


LOCK TABLES `product_varians` WRITE;
/*!40000 ALTER TABLE `product_varians` DISABLE KEYS */;
INSERT INTO `product_varians` (`id`, `product_id`, `label`, `value`, `sku`, `price`, `stock`, `weight`, `varian_id`, `has_subvarian`, `created_at`, `updated_at`)
 VALUES (1,1,'Ukuran','200 Gram','baf4294a-d229-478d-a5bb-4f59e1c72967',12000,500,500,NULL,0,'2024-10-21 01:59:55','2024-10-21 01:59:55'),(2,1,'Ukuran','500 Gram','b2d12ca8-c552-4092-93f2-5eafdaa5d707',20000,500,500,NULL,0,'2024-10-21 01:59:55','2024-10-21 01:59:55'),(3,3,'Rasa','Original','656550fa-cf34-4b15-a79d-83025d35826d',NULL,NULL,0,NULL,1,'2024-10-21 02:02:34','2024-10-21 02:02:34'),(4,3,'Ukuran','200 Gram','3219ca42-66e4-485e-8758-c35b56003304',12000,500,500,3,0,'2024-10-21 02:02:34','2024-10-21 02:02:34'),(5,3,'Ukuran','500 Gram','3ecdbb45-3888-43f0-829a-7e21cf8745e7',20000,500,500,3,0,'2024-10-21 02:02:34','2024-10-21 02:02:34'),(6,3,'Rasa','Balado','5b6e9db1-fc86-4869-954d-1f90d3d0d72d',NULL,NULL,0,NULL,1,'2024-10-21 02:02:34','2024-10-21 02:02:34'),(7,3,'Ukuran','200 Gram','242241d8-fa11-4add-8091-fba5cbd6fd9f',15000,500,500,6,0,'2024-10-21 02:02:34','2024-10-21 02:02:34'),(8,3,'Ukuran','500 Gram','0ea78de7-f87b-472a-8d24-0a3d9dba885c',20000,500,500,6,0,'2024-10-21 02:02:34','2024-10-21 02:02:34');
/*!40000 ALTER TABLE `product_varians` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping data for table `product_asset`
--

LOCK TABLES `product_asset` WRITE;
/*!40000 ALTER TABLE `product_asset` DISABLE KEYS */;
INSERT INTO `product_asset` (`id`, `product_id`, `asset_id`, `sort`) 
VALUES (1,1,1,1),(2,2,2,1),(4,3,3,1),(5,4,5,1),(6,5,6,1),(7,6,7,1);
/*!40000 ALTER TABLE `product_asset` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2025-08-03 18:19:27
