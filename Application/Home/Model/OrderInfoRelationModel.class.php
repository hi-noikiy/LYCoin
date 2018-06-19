<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/2/5
 * Time: 15:11
 */
namespace Home\Model;
use Think\Model\RelationModel;
class OrderInfoRelationModel extends RelationModel{
    Protected $tableName = 'order_info';
    Protected $_link = array(
        'package_type' => array(
            'mapping_type'=> self::BELONGS_TO,
            'foreign_key' =>'package_id',
            'as_fields' => 'en_title:package_title_en,title:package_title,price:package_price,bv_price:package_bv_price'
        )

    );
}