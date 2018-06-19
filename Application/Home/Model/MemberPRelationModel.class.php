<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/2/5
 * Time: 15:11
 */
namespace Home\Model;
use Think\Model\RelationModel;
class MemberPRelationModel extends RelationModel{
    Protected $tableName = 'price_p_info';
    Protected $_link = array(
        'member' => array(
            'mapping_type'=> self::BELONGS_TO,
            'foreign_key' =>'admin_id',
            'as_fields' => 'username:member_username'
        )

    );
}