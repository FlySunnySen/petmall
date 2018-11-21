-- phpMyAdmin SQL Dump
-- version 4.7.9
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: 2018-10-24 14:17:29
-- 服务器版本： 5.7.21
-- PHP Version: 5.6.35

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `petmall`
--

DELIMITER $$
--
-- 存储过程
--
DROP PROCEDURE IF EXISTS `p11`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `p11` ()  begin
    declare total int default 0;
    declare i int default 0;

    repeat
    set i := i+1;
    set total := total + i;
    until i>= 100 end repeat;
    select total;
end$$

DELIMITER ;

-- --------------------------------------------------------

--
-- 表的结构 `pet_admin`
--

DROP TABLE IF EXISTS `pet_admin`;
CREATE TABLE IF NOT EXISTS `pet_admin` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `admin_name` varchar(45) NOT NULL,
  `pwd` char(32) NOT NULL,
  `admin_level` int(11) DEFAULT '1' COMMENT '管理员等级(2为超管，1为普通)',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

--
-- 转存表中的数据 `pet_admin`
--

INSERT INTO `pet_admin` (`id`, `admin_name`, `pwd`, `admin_level`) VALUES
(1, 'admin', 'e10adc3949ba59abbe56e057f20f883e', 1);

-- --------------------------------------------------------

--
-- 表的结构 `pet_auth_group`
--

DROP TABLE IF EXISTS `pet_auth_group`;
CREATE TABLE IF NOT EXISTS `pet_auth_group` (
  `id` mediumint(8) NOT NULL AUTO_INCREMENT,
  `title` varchar(45) DEFAULT NULL,
  `status` tinyint(1) DEFAULT '1',
  `rules` text,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- 表的结构 `pet_auth_group_access`
--

DROP TABLE IF EXISTS `pet_auth_group_access`;
CREATE TABLE IF NOT EXISTS `pet_auth_group_access` (
  `uid` mediumint(8) NOT NULL AUTO_INCREMENT,
  `group_id` mediumint(8) DEFAULT NULL,
  PRIMARY KEY (`uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- 表的结构 `pet_auth_rule`
--

DROP TABLE IF EXISTS `pet_auth_rule`;
CREATE TABLE IF NOT EXISTS `pet_auth_rule` (
  `id` mediumint(8) NOT NULL AUTO_INCREMENT,
  `name` char(80) DEFAULT NULL,
  `title` char(40) DEFAULT NULL,
  `type` tinyint(1) DEFAULT '1',
  `status` tinyint(1) DEFAULT '1',
  `condition` char(100) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- 表的结构 `pet_brand`
--

DROP TABLE IF EXISTS `pet_brand`;
CREATE TABLE IF NOT EXISTS `pet_brand` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `brand_name` varchar(45) DEFAULT NULL,
  `brand_logo` varchar(255) DEFAULT NULL COMMENT '品牌图片',
  `brand_desc` text COMMENT '品牌简单描述',
  `brand_order` tinyint(4) DEFAULT NULL,
  `is_show` tinyint(4) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;

--
-- 转存表中的数据 `pet_brand`
--

INSERT INTO `pet_brand` (`id`, `brand_name`, `brand_logo`, `brand_desc`, `brand_order`, `is_show`) VALUES
(1, 'N3', '/upload/brand/20180915/d407ffc929449b8a0bacd7dbb93bef2e.jpg', '11', 1, 1),
(2, 'N4', '\"/upload/brand/20180917/368f67c035cf8411dfb950101115cea7.jpg\"', '111', 2, 1);

-- --------------------------------------------------------

--
-- 表的结构 `pet_cart`
--

DROP TABLE IF EXISTS `pet_cart`;
CREATE TABLE IF NOT EXISTS `pet_cart` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_Uid` int(11) NOT NULL COMMENT '用户id',
  `good_id` int(11) NOT NULL COMMENT '商品id',
  `num` int(10) DEFAULT NULL,
  `status` tinyint(4) DEFAULT NULL,
  `create_time` int(10) DEFAULT NULL,
  `update_time` int(10) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_pet_cart_pet_user_idx` (`user_Uid`),
  KEY `fk_pet_cart_pet_good1_idx` (`good_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- 表的结构 `pet_comment`
--

DROP TABLE IF EXISTS `pet_comment`;
CREATE TABLE IF NOT EXISTS `pet_comment` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `order_id` int(11) NOT NULL COMMENT '订单编号',
  `user_id` int(11) DEFAULT NULL,
  `comment_content` varchar(245) DEFAULT NULL,
  `comment_time` varchar(45) DEFAULT NULL COMMENT '评论时间',
  `comment_rank` tinyint(4) DEFAULT NULL COMMENT '评论星级',
  `is_reply` tinyint(1) DEFAULT NULL,
  `reply_time` int(11) DEFAULT NULL,
  `reply_content` varchar(245) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_pet_comment_pet_order1_idx` (`order_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- 表的结构 `pet_good`
--

DROP TABLE IF EXISTS `pet_good`;
CREATE TABLE IF NOT EXISTS `pet_good` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `sn` varchar(45) NOT NULL COMMENT '商品唯一货号',
  `goods_name` varchar(45) DEFAULT NULL,
  `good_click` varchar(45) DEFAULT '0' COMMENT '商品点击数',
  `goods_number` int(11) DEFAULT NULL COMMENT '商品库存',
  `goods_weight` decimal(5,2) DEFAULT NULL COMMENT '商品重量',
  `goods_price` decimal(10,2) DEFAULT NULL COMMENT '商品价格',
  `goods_keywords` varchar(45) DEFAULT NULL COMMENT '商品标签',
  `goods_brief` varchar(45) DEFAULT NULL COMMENT '商品简单描述',
  `goods_desc` text COMMENT '商品详细描述',
  `goods_img` varchar(225) DEFAULT NULL COMMENT '商品照片地址',
  `goods_time` int(11) DEFAULT NULL COMMENT '商品添加日期',
  `is_delete` int(2) DEFAULT '0' COMMENT '是否进入商品回收站(0为不是 1为是)',
  `sales_sum` int(45) DEFAULT '0',
  `is_on_sale` int(11) DEFAULT '1' COMMENT '是否销售',
  `type_id` int(11) NOT NULL COMMENT '商品分类id',
  `brand_id` int(11) NOT NULL COMMENT '品牌id',
  PRIMARY KEY (`id`),
  KEY `fk_pet_good_pet_good_type1_idx` (`type_id`),
  KEY `fk_pet_good_pet_brand1_idx` (`brand_id`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8;

--
-- 转存表中的数据 `pet_good`
--

INSERT INTO `pet_good` (`id`, `sn`, `goods_name`, `good_click`, `goods_number`, `goods_weight`, `goods_price`, `goods_keywords`, `goods_brief`, `goods_desc`, `goods_img`, `goods_time`, `is_delete`, `sales_sum`, `is_on_sale`, `type_id`, `brand_id`) VALUES
(7, '15390520062233', 'N3狗粮', '0', 11, '1.00', '1.00', '11', '1', '<p>1111<br/></p>', '\"/upload/good/20181009/45a487126f8c9e1e2733287e9c17aa38.jpg\"', 1539052006, 0, 0, 1, 7, 1),
(8, '15390520366328', 'N4狗粮', '0', 11, '11.00', '11.00', '11', '11', '<p>1111<br/></p>', '\"/upload/good/20181009/a6ffa69cea619e3856dd5563adce5b87.jpg\"', 1539052036, 0, 0, 1, 8, 2),
(9, '15391462777767', 'N4狗粮', '0', 11, '11.00', '11.00', 'gougou', '11', '<p>1111<br/></p>', '\"/upload/good/20181010/ccd71094c976e432828dc8bda49c8ad4.png\"', 1539146277, 0, 0, 1, 10, 2);

-- --------------------------------------------------------

--
-- 表的结构 `pet_good_images`
--

DROP TABLE IF EXISTS `pet_good_images`;
CREATE TABLE IF NOT EXISTS `pet_good_images` (
  `img_id` int(11) NOT NULL AUTO_INCREMENT,
  `good_id` int(11) NOT NULL,
  `image_url` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`img_id`),
  KEY `fk_pet_goods_images_pet_good1_idx` (`good_id`)
) ENGINE=InnoDB AUTO_INCREMENT=16 DEFAULT CHARSET=utf8;

--
-- 转存表中的数据 `pet_good_images`
--

INSERT INTO `pet_good_images` (`img_id`, `good_id`, `image_url`) VALUES
(13, 7, '/upload/goodimages/20181011/75b8bff79c55f8d8eb3ecbffafb5d866.png'),
(14, 7, '/upload/goodimages/20181011/a9d5ca7bf33828e32da975485f7980af.jpg'),
(15, 7, '/upload/goodimages/20181011/322d7671643b67138e2304c75874ec8e.jpg');

-- --------------------------------------------------------

--
-- 表的结构 `pet_good_type`
--

DROP TABLE IF EXISTS `pet_good_type`;
CREATE TABLE IF NOT EXISTS `pet_good_type` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `type_name` varchar(45) DEFAULT NULL,
  `pid` varchar(45) DEFAULT NULL COMMENT '父id',
  `parent_id_path` varchar(45) NOT NULL,
  `type_order` tinyint(4) DEFAULT NULL,
  `is_show` tinyint(4) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=22 DEFAULT CHARSET=utf8;

--
-- 转存表中的数据 `pet_good_type`
--

INSERT INTO `pet_good_type` (`id`, `type_name`, `pid`, `parent_id_path`, `type_order`, `is_show`) VALUES
(5, '主粮', '0', '0_5', 1, 1),
(6, '洗澡', '0', '0_6', 2, 1),
(7, '进口狗粮', '5', '0_5_7', 1, 1),
(8, '国产狗粮', '5', '0_5_8', 2, 1),
(9, '处方狗粮', '5', '0_5_9', 3, 1),
(10, '全犬粮', '7', '0_5_7_10', 1, 1),
(11, '专用香波', '6', '0_6_11', 1, 1),
(12, '白色毛', '11', '0_6_11_12', 1, 1),
(13, '全犬粮', '8', '0_5_8_13', 1, 1),
(14, '全犬粮', '9', '0_5_9_14', 1, 1),
(17, '服饰', '0', '0_15', 3, 1),
(20, '时尚卫衣', '17', '0_15_18', 1, 1),
(21, '带帽卫衣', '20', '0_15_18_21', 3, 1);

-- --------------------------------------------------------

--
-- 表的结构 `pet_order`
--

DROP TABLE IF EXISTS `pet_order`;
CREATE TABLE IF NOT EXISTS `pet_order` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `acti` varchar(45) DEFAULT NULL COMMENT '交易号',
  `user_Uid` int(11) NOT NULL COMMENT '用户id',
  `admin_id` varchar(45) DEFAULT NULL COMMENT '操作的管理员',
  `user_id` int(11) DEFAULT NULL,
  `address_id` int(11) DEFAULT NULL,
  `action_status` tinyint(4) DEFAULT NULL COMMENT '订单状态',
  `pay_status` tinyint(4) DEFAULT NULL COMMENT '付款状态',
  `shipping_status` tinyint(4) DEFAULT NULL COMMENT '配送状态（0为未发货，1为已发货，2为运输中，3为已收货）',
  `order_count` decimal(10,2) DEFAULT NULL,
  `action_time` varchar(45) DEFAULT NULL COMMENT '操作时间',
  `order_message` varchar(150) DEFAULT NULL COMMENT '买家留言',
  `express_type` varchar(45) DEFAULT NULL COMMENT '快递类型',
  `express_number` varchar(45) DEFAULT NULL COMMENT '快递单号',
  `create_time` varchar(45) DEFAULT NULL COMMENT '订单创建时间',
  `comeback_reason` varchar(45) DEFAULT NULL COMMENT '退货原因',
  `is_comment` varchar(45) DEFAULT NULL COMMENT '是否评论（0为未评论，1为已评论）',
  `is_delete` varchar(45) DEFAULT NULL COMMENT '订单是否删除',
  `user_address` varchar(245) DEFAULT NULL COMMENT '用户地址',
  `pay_id` int(11) NOT NULL COMMENT '付款方式',
  PRIMARY KEY (`id`),
  KEY `fk_pet_order_pet_user1_idx` (`user_Uid`),
  KEY `fk_pet_order_pet_pay1_idx` (`pay_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- 表的结构 `pet_order_good`
--

DROP TABLE IF EXISTS `pet_order_good`;
CREATE TABLE IF NOT EXISTS `pet_order_good` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `order_id` int(11) NOT NULL COMMENT '订单id',
  `action_id` int(11) DEFAULT NULL,
  `goods_id` int(11) DEFAULT NULL,
  `goods_number` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_pet_order_good_pet_order1_idx` (`order_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- 表的结构 `pet_pay`
--

DROP TABLE IF EXISTS `pet_pay`;
CREATE TABLE IF NOT EXISTS `pet_pay` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `pay_name` varchar(45) DEFAULT NULL,
  `status` tinyint(4) DEFAULT NULL COMMENT '支付方式是否可用（1可用 0为不可用）',
  `pay_desc` text COMMENT '支付方式描述',
  `pay_logo` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- 表的结构 `pet_user`
--

DROP TABLE IF EXISTS `pet_user`;
CREATE TABLE IF NOT EXISTS `pet_user` (
  `Uid` int(11) NOT NULL AUTO_INCREMENT,
  `user_email` varchar(45) NOT NULL,
  `user_pwd` char(32) NOT NULL,
  `is_delete` varchar(45) DEFAULT '0' COMMENT '用户状态',
  PRIMARY KEY (`Uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- 表的结构 `pet_user_address`
--

DROP TABLE IF EXISTS `pet_user_address`;
CREATE TABLE IF NOT EXISTS `pet_user_address` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_Uid` int(11) NOT NULL COMMENT '用户id',
  `name` varchar(45) DEFAULT NULL COMMENT '收货人姓名',
  `address_province` varchar(45) DEFAULT NULL COMMENT '收货省份',
  `address_city` varchar(45) DEFAULT NULL COMMENT '收货城市',
  `address_county` varchar(45) DEFAULT NULL,
  `address_info` varchar(45) DEFAULT NULL COMMENT '详细地址',
  `address_zip` varchar(45) DEFAULT NULL COMMENT '邮编',
  `phone` int(6) DEFAULT NULL COMMENT '手机号码',
  `is_default` varchar(45) DEFAULT '0' COMMENT '是否为默认收货地址 0为否 1为是',
  PRIMARY KEY (`id`),
  KEY `fk_pet_user_address_pet_user1_idx` (`user_Uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- 表的结构 `pet_user_details`
--

DROP TABLE IF EXISTS `pet_user_details`;
CREATE TABLE IF NOT EXISTS `pet_user_details` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_Uid` int(11) NOT NULL,
  `user_alias` varchar(45) DEFAULT NULL,
  `user_question` varchar(45) DEFAULT NULL,
  `user_answer` varchar(45) DEFAULT NULL,
  `user_sex` varchar(45) DEFAULT NULL,
  `user_money` decimal(10,2) DEFAULT NULL,
  `user_integral` int(11) DEFAULT NULL COMMENT '积分',
  `user_level` int(11) DEFAULT NULL COMMENT '会员等级',
  `user_phone` varchar(45) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_pet_user_details_pet_user1_idx` (`user_Uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- 限制导出的表
--

--
-- 限制表 `pet_cart`
--
ALTER TABLE `pet_cart`
  ADD CONSTRAINT `fk_pet_cart_pet_good1` FOREIGN KEY (`good_id`) REFERENCES `pet_good` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_pet_cart_pet_user` FOREIGN KEY (`user_Uid`) REFERENCES `pet_user` (`Uid`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- 限制表 `pet_comment`
--
ALTER TABLE `pet_comment`
  ADD CONSTRAINT `fk_pet_comment_pet_order1` FOREIGN KEY (`order_id`) REFERENCES `pet_order` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- 限制表 `pet_good`
--
ALTER TABLE `pet_good`
  ADD CONSTRAINT `fk_pet_good_pet_brand1` FOREIGN KEY (`brand_id`) REFERENCES `pet_brand` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_pet_good_pet_good_type1` FOREIGN KEY (`type_id`) REFERENCES `pet_good_type` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- 限制表 `pet_good_images`
--
ALTER TABLE `pet_good_images`
  ADD CONSTRAINT `fk_pet_goods_images_pet_good1` FOREIGN KEY (`good_id`) REFERENCES `pet_good` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- 限制表 `pet_order`
--
ALTER TABLE `pet_order`
  ADD CONSTRAINT `fk_pet_order_pet_pay1` FOREIGN KEY (`pay_id`) REFERENCES `pet_pay` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_pet_order_pet_user1` FOREIGN KEY (`user_Uid`) REFERENCES `pet_user` (`Uid`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- 限制表 `pet_order_good`
--
ALTER TABLE `pet_order_good`
  ADD CONSTRAINT `fk_pet_order_good_pet_order1` FOREIGN KEY (`order_id`) REFERENCES `pet_order` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- 限制表 `pet_user_address`
--
ALTER TABLE `pet_user_address`
  ADD CONSTRAINT `fk_pet_user_address_pet_user1` FOREIGN KEY (`user_Uid`) REFERENCES `pet_user` (`Uid`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- 限制表 `pet_user_details`
--
ALTER TABLE `pet_user_details`
  ADD CONSTRAINT `fk_pet_user_details_pet_user1` FOREIGN KEY (`user_Uid`) REFERENCES `pet_user` (`Uid`) ON DELETE NO ACTION ON UPDATE NO ACTION;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
