<?php
// 商品类型模型
class FiledModel extends Model
{

    PUBLIC function getFiledDefaultValue($filed_id)
    {
        $defaultValue=$this->oneRowCol("u8", "id={$filed_id}")['u8'];
        $defaultValueArray=explode("\r\n", $defaultValue);
        return $defaultValueArray;
    }
}