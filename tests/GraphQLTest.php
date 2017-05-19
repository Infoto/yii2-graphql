<?php

namespace yiiunit\extensions\graphql;

use GraphQL\Schema;
use GraphQL\Type\Definition\ObjectType;
use yii\graphql\exception\SchemaNotFound;
use yii\graphql\GraphQL;
use yiiunit\extensions\graphql\objects\types\ExampleType;

/**
 * Created by PhpStorm.
 * User: tsingsun
 * Date: 2016/11/16
 * Time: 下午1:39
 */
class GraphQLTest extends TestCase
{

    /**
     * @var GraphQL
     */
    protected $graphql;

    protected function setUp()
    {
        parent::setUp(); // TODO: Change the autogenerated stub
        $this->mockWebApplication();
        $this->graphql = \Yii::$app->getModule('graphql')->getGraphQL();
    }


    /**
     * Test schema default
     *
     * @test
     */
    public function testSchema()
    {
        $schema = $this->graphql->buildSchema();

        $this->assertGraphQLSchema($schema);
        $this->assertGraphQLSchemaHasQuery($schema, 'stories');
        $this->assertGraphQLSchemaHasMutation($schema, 'updateUserPwd');
        $this->assertArrayHasKey('user', $schema->getTypeMap());
    }

    /**
     * Test schema with object
     *
     * @test
     */
    public function testSchemaWithSchemaObject()
    {
        $schemaObject = new Schema([
            'query' => new ObjectType([
                'name' => 'Query'
            ]),
            'mutation' => new ObjectType([
                'name' => 'Mutation'
            ]),
            'types' => []
        ]);
        $schema = $this->graphql->buildSchema($schemaObject);

        $this->assertGraphQLSchema($schema);
        $this->assertEquals($schemaObject, $schema);
    }

    /**
     * Test schema with wrong name
     *
     * @test
     * @expectedException SchemaNotFound
     */
    public function testSchemaWithWrongName()
    {
        $schema = $this->graphql->buildSchema('wrong');
    }

    /**
     * Test type
     *
     * @test
     */
    public function testType()
    {
        $type = GraphQL::type(ExampleType::class);
        $this->assertInstanceOf(\GraphQL\Type\Definition\ObjectType::class, $type);

        $typeOther = GraphQL::type('example');
        $this->assertFalse($type === $typeOther);

    }

    public function testParseRequestQuery()
    {
        $query = $this->queries['multiQuery'];
        $ret = $this->graphql->parseRequestQuery($query);
        $this->assertNotEmpty($ret);
    }
}
