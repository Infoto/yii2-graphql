<?php

namespace yiiunit\extensions\graphql;

use GraphQL\GraphQL;
use GraphQL\Schema;
use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\Type;
use GraphQL\Error;
use yii\graphql\exception\SchemaNotFound;
use yii\graphql\GraphqlFacade;
use yiiunit\extensions\graphql\objects\CustomExampleType;
use yiiunit\extensions\graphql\objects\ExamplesQuery;
use yiiunit\extensions\graphql\objects\UpdateExampleMutation;

/**
 * Created by PhpStorm.

 */
class GraphqlQueryTest extends TestCase
{
    /**
     * @var \yii\graphql\GraphQL GraphQL
     */
    protected $graphQL;


    protected function setUp()
    {
        parent::setUp(); // TODO: Change the autogenerated stub
        $this->mockWebApplication();
        $this->graphQL = \Yii::$app->getModule('graphql')->getGraphQL();
    }

    /**
     * test if work
     */
    public function testQueryValid(){
        $result = $this->graphQL->query($this->queries['hello']);
        $this->assertArrayHasKey('data', $result);
        $this->assertArrayNotHasKey('errors', $result);
    }

    /**
     * test sample object query
     */
    public function testQueryWithSingleObject(){
        $result = $this->graphQL->query($this->queries['singleObject'], null, \Yii::$app);
        $this->assertArrayHasKey('data', $result);
        $this->assertArrayNotHasKey('errors', $result);
    }

    /**
     * test multi object in a query
     */
    public function testQueryWithMultiObject(){
        $result = $this->graphQL->query($this->queries['multiObject'], null, \Yii::$app);
        $this->assertArrayHasKey('data', $result);
        $this->assertArrayNotHasKey('errors', $result);
    }

    /**
     * test active record query
     */
    public function testQueryWithAR(){
        $result = $this->graphQL->query($this->queries['userModel'], null, \Yii::$app);
        $this->assertArrayHasKey('data', $result);
        $this->assertArrayNotHasKey('errors', $result);
    }

    /**
     * Test query with params
     *
     * @test
     */
    public function testQueryAndReturnResultWithParams()
    {
        $result = $this->graphQL->query($this->queries['examplesWithParams'], [
            'index' => 0
        ]);

        $this->assertArrayHasKey('data', $result);
        $this->assertArrayNotHasKey('errors', $result);
        $this->assertEquals($result['data'], [
            'examples' => [
                $this->data[0]
            ]
        ]);
    }

    /**
     * Test query with initial root
     *
     * @test
     */
    public function testQueryAndReturnResultWithRoot()
    {
        $result = $this->graphQL->query($this->queries['hello'], [
            'root' => [
                'test' => 'root'
            ]
        ]);

        $this->assertArrayHasKey('data', $result);
        $this->assertArrayNotHasKey('errors', $result);
        $this->assertEquals($result['data'], [
            'examplesRoot' => [
                'test' => 'root'
            ]
        ]);
    }

    /**
     * Test query with context
     *
     * @test
     */
    public function testQueryAndReturnResultWithContext()
    {
        $result = $this->graphQL->query($this->queries['examplesWithContext'], null, [
            'context' => [
                'test' => 'context'
            ]
        ]);
        $this->assertArrayHasKey('data', $result);
        $this->assertArrayNotHasKey('errors', $result);
        $this->assertEquals($result['data'], [
            'examplesContext' => [
                'test' => 'context'
            ]
        ]);
    }

}
