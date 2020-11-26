<?php

namespace Tests\Feature\Models;

use App\Models\Category;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Ramsey\Uuid\Uuid;
use Tests\TestCase;

class CategoryTest extends TestCase
{
    use DatabaseMigrations;

    public function testList()
    {
        factory(Category::class, 1)->create();
        $categories = Category::all();
        $this->assertCount(1, $categories);
        $categoryKey = array_keys($categories->first()->getAttributes());
        $this->assertEqualsCanonicalizing([
            'id',
            'name',
            'description',
            'is_active',
            'created_at',
            'updated_at',
            'deleted_at'
            ],
            $categoryKey
        );

    }
    public function testCreate()
    {
        $category = Category::create([
            'name' => 'test 1'
        ]);
        $category->refresh();
        $this->assertEquals('test 1', $category->name);
        $this->assertNull($category->description);
        $this->assertTrue($category->is_active);
    }

    public function testIfIdIsUuid()
    {
        $category = Category::create([
            'name' => 'test 1'
        ]);
        $category->refresh();
        $this->assertIsString($category->id);
        $this->assertTrue(Uuid::isValid($category->id));
    }

    public function testCreateWhenDescriptionIsNull()
    {
        $category = Category::create([
            'name' => 'test 1',
            'description' => null
        ]);
        $this->assertNull($category->description);
    }

    public function testCreateWhenCreateDescription()
    {
        $category = Category::create([
            'name' => 'test 1',
            'description' => 'test description'
        ]);
        $this->assertEquals('test description', $category->description);
    }

    public function testCreateWhenIsActiveIsFalse()
    {
        $category = Category::create([
            'name' => 'test 1',
            'is_active' => false
        ]);
        $this->assertFalse($category->is_active);
    }

    public function testCreateWhenIsActiveIsTrue()
    {
        $category = Category::create([
            'name' => 'test 1',
            'is_active' => true
        ]);
        $this->assertTrue($category->is_active);
    }

    public function testUpdate()
    {
        $category = factory(Category::class)->create(
            ['description'=>'test description']
        )->first();

        $data = [
            'name' => 'test_updated_name',
            'description' => 'test_updated_desccription',
            'is_active' => false
        ];

        $category->update($data);

        foreach ($data as $key => $value) {
            $this->assertEquals($value, $category->{$key});
        }
    }

    public function testDelete()
    {
        $category = Category::create([
            'name' => 'test 1',
        ]);
        $category->delete();
        $this->assertNotNull($category->deleted_at);
        $this->assertSoftDeleted('categories', [
            'id' => $category->id,
            'name' => $category->name,
        ]);
    }
}
