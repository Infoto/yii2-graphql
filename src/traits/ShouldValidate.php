<?php

namespace yii\graphql\traits;

use yii\base\DynamicModel;
use yii\base\InvalidParamException;
use yii\helpers\ArrayHelper;

/**
 * Trait ShouldValidate
 *
 *
 * @package yii\graphql\traits
 */
trait ShouldValidate
{

    protected function getResolver()
    {
        $resolver = parent::getResolver();
        if (!$resolver) {
            return null;
        }

        return function () use ($resolver) {
            $arguments = func_get_args();

            $rules = $this->rules();
            if (sizeof($rules)) {
                //索引1的为args参数.
                $args = ArrayHelper::getValue($arguments, 1, []);
                $defaultArgs = [];
                foreach ($this->args() as $name => $attr) {
                    $defaultArgs[$name] = null;
                }
                $val = DynamicModel::validateData(ArrayHelper::merge($defaultArgs, $args), $rules);
                if ($error = $val->getFirstErrors()) {
                    $msg = 'input argument(' . key($error) . ') has validate error:' . reset($error);
                    throw new InvalidParamException($msg);
                }
            }

            return $resolver(...$arguments);
        };
    }
}
