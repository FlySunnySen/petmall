<?php

namespace app\common\model;

use think\Db;
use think\Model;

class Good extends Model {
	public function GoodsImages() {
		return $this->hasMany('GoodsImages');
	}

	public function getDiscountAttr($value, $data) {
		if ($data['market_price'] == 0) {
			$discount = 10;
		} else {
			$discount = round($data['shop_price'] / $data['market_price'], 2) * 10;
		}
		return $discount;
	}

	/**
	 * 获取商品评价
	 * 好评数差评数中评数及其百分比,和总评数
	 * @param $value
	 * @param $data
	 * @return array|false|\PDOStatement|string|Model
	 */
	public function getCommentStatisticsAttr($value, $data) {
		$comment_where = ['is_show' => 1, 'goods_id' => $data['goods_id'], 'user_id' => ['gt', 0]]; //公共条件
		$field = "sum(case when img !='' and img not like 'N;%' then 1 else 0 end) as img_sum,"
			. "sum(case when goods_rank >= 4 and goods_rank <= 5 then 1 else 0 end) as high_sum," .
			"sum(case when goods_rank >= 3 and goods_rank <4 then 1 else 0 end) as center_sum," .
			"sum(case when goods_rank < 3 then 1 else 0 end) as low_sum,count(comment_id) as total_sum";
		$comment_statistics = Db::name('comment')->field($field)->where($comment_where)->group('goods_id')->find();
		if ($comment_statistics) {
			$comment_statistics['high_rate'] = ceil($comment_statistics['high_sum'] / $comment_statistics['total_sum'] * 100); // 好评率
			$comment_statistics['center_rate'] = ceil($comment_statistics['center_sum'] / $comment_statistics['total_sum'] * 100); // 好评率
			$comment_statistics['low_rate'] = ceil($comment_statistics['low_sum'] / $comment_statistics['total_sum'] * 100); // 好评率
		} else {
			$comment_statistics = ['img_sum' => 0, 'high_sum' => 0, 'high_rate' => 100, 'center_sum' => 0, 'center_rate' => 0, 'low_sum' => 0, 'low_rate' => 0, 'total_sum' => 0];
		}
		return $comment_statistics;
	}

	public function getPriceLadderAttr($value) {
		if (!empty($value)) {
			return json_decode($value, true);
		} else {
			return $value;
		}
	}

	public function setVirtualIndateAttr($value) {
		$virtual_time = strtotime($value);
		if ($virtual_time > time()) {
			return $virtual_time;
		} else {
			return 0;
		}
	}
	public function setExchangeIntegralAttr($value, $data) {
		if ($data['is_virtual'] == 1) {
			return 0;
		} else {
			return $value;
		}
	}
	public function setCatIdAttr($value, $data) {
		if ($data['cat_id_3']) {
			return $data['cat_id_3'];
		}
		if ($data['cat_id_2']) {
			return $data['cat_id_2'];
		}
		return $value;
	}

	public function setExtendCatIdAttr($value, $data) {
		if ($data['extend_cat_id_3']) {
			return $data['extend_cat_id_3'];
		}
		if ($data['extend_cat_id_2']) {
			return $data['extend_cat_id_2'];
		}
		return $value;
	}
	public function setSpecTypeAttr($value, $data) {
		return $data['goods_type'];
	}

	public function setPriceLadderAttr($value, $data) {
		if ($data['ladder_amount'][0] > 0) {
			$price_ladder = array();
			foreach ($data['ladder_amount'] as $key => $value) {
				$price_ladder[$key]['amount'] = intval($data['ladder_amount'][$key]);
				$price_ladder[$key]['price'] = floatval($data['ladder_price'][$key]);
			}
			$price_ladder = array_values(array_sort($price_ladder, 'amount', 'asc'));
			return json_encode($price_ladder);
		} else {
			return '';
		}
	}
}
