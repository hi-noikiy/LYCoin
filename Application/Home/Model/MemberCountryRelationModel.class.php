<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/2/5
 * Time: 15:11
 */
namespace Home\Model;
use Think\Model\RelationModel;
class MemberCountryRelationModel extends RelationModel{
    Protected $tableName = 'member';
    Protected $_link = array(
        'country' => array(
            'mapping_type'=> self::BELONGS_TO,
            'foreign_key' =>'country',
            'as_fields' => 'cn_name,en_name,picture:country_picture'
        )

    );
}