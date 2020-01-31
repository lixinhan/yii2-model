<?php
namespace lixinhan\yii2\model;
/**
 *
 * @file CommonModel.php
 * @author lixinhan <lixinhan@lixinhan.com>
 * @version 1.0
 * @date 2020-01-31
 */

use yii\base\InvalidArgumentException;
use yii\db\ActiveRecord;
use yii\helpers\Inflector;
use yii\helpers\StringHelper;

class CommonModel extends ActiveRecord
{
    /**
     * 数据表名称
     * @return string
     */
    public static function tableName()
    {
        return '{{%' . substr(Inflector::camel2id(StringHelper::basename(get_called_class()), '_'),0,-6) . '}}';
    }


    /**
     * 获取第一个错误提示
     * @return mixed
     */
    public function getFirstErrorTip(){
        foreach ($this->getFirstErrors() as $value){
            return $value;
        }
    }


    /**
     * 重写验证方法，当遇到错误时停止运行
     * @param null $attributeNames
     * @param bool $clearErrors
     * @return bool
     */
    public function validate($attributeNames = null, $clearErrors = true)
    {
        if ($clearErrors) {
            $this->clearErrors();
        }

        if (!$this->beforeValidate()) {
            return false;
        }

        $scenarios = $this->scenarios();
        $scenario = $this->getScenario();
        if (!isset($scenarios[$scenario])) {
            throw new InvalidArgumentException("Unknown scenario: $scenario");
        }

        if ($attributeNames === null) {
            $attributeNames = $this->activeAttributes();
        }

        $attributeNames = (array)$attributeNames;

        foreach ($this->getActiveValidators() as $validator) {
            $validator->validateAttributes($this, $attributeNames);
            if($this->hasErrors()){
                return false;
            }
        }
        $this->afterValidate();

        return !$this->hasErrors();
    }
}
