<?php
/**
 * Created by PhpStorm.
 * User: tsingsun
 * Date: 2017/5/18
 * Time: 下午3:10
 */

namespace yii\graphql;

use Yii;
use yii\base\Action;
use yii\web\Response;
use yii\base\InvalidParamException;

/**
 * GraphQLAction实现graph服务端的接入方法，并返回json格式的查询结果
 * 在controller中配置actions
 * ```php
 * function actions()
 * {
 *     return [
 *          'index'=>['class'=>'yii\graphql\GraphQLAction']
 *     ]
 * }
 * ```
 * @package yii\graphql
 */
class GraphQLAction extends Action
{
    const INTROSPECTIONQUERY = '__schema';
    /**
     * @var GraphQL
     */
    private $graphQL;
    private $schemaArray;
    private $query;
    private $variables;

    public function init()
    {
        parent::init();

        $request = Yii::$app->getRequest();
        if ($request->isGet) {
            $this->query = $request->get('query');
            $this->variables = $request->get('variables');
        } else {
            $body = $request->getBodyParams();
            $this->query = $body['query'] ?? $body;
            $this->variables = $body['variables'] ?? [];
        }
        if (empty($this->query)) {
            throw new InvalidParamException('invalid query,query document not found');
        }
        if (is_string($this->variables)) {
            $this->variables = json_decode($this->variables, true);
        }

        /** @var GraphQLModuleTrait $module */
        $module = $this->controller->module;
        $this->graphQL = $module->getGraphQL();

        $this->schemaArray = $this->graphQL->parseRequestQuery($this->query);
    }

    /**
     * 返回本次查询的所有graphql action,如果本次查询为introspection时，则为查询的
     * @return array
     */
    public function getGraphQLActions()
    {
        if ($this->schemaArray === true) {
            return [self::INTROSPECTIONQUERY => 'true'];
        }
        return array_merge($this->schemaArray[0], $this->schemaArray[1]);
    }

    /**
     * @return array
     */
    public function run()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        if (YII_DEBUG) {
            //调度状态下将执行构建查询
            $this->controller->module->enableValidation();
        }
        $schema = $this->graphQL->buildSchema($this->schemaArray === true ? null : $this->schemaArray);
        $val = $this->graphQL->execute($schema, null, Yii::$app, $this->variables, null);
        $result = $this->graphQL->getResult($val);
        return $result;
    }
}