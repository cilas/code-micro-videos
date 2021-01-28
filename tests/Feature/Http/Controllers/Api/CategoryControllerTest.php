<?php

namespace Tests\Feature\Http\Controllers\Api;

use App\Models\Category;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;
use Tests\Traits\TestSaves;
use Tests\Traits\TestValidations;

class CategoryControllerTest extends TestCase
{
    use DatabaseMigrations, TestValidations, TestSaves;
    private $category;
    protected function setUp(): void
    {
        parent::setUp();
        $this->category = factory(Category::class)->create();
    }

    public function testIndex()
    {
        $response = $this->get(route("categories.index"));
        $response
            ->assertStatus(200)
            ->assertJson([$this->category->toArray()]);
    }

    public function testShow()
    {
        $response = $this->get(route("categories.show", ['category'=> $this->category->id]));
        $response
            ->assertStatus(200)
            ->assertJson($this->category->toArray());
    }

    public function testInvalidationDataOnCreate()
    {
        $data = [
            'name' => ''
        ];
        $this->assertInvalidationInStoreAction($data, 'required');

        $data = [
            'name'=> str_repeat('a', 256),
        ];
        $this->assertInvalidationInStoreAction($data, 'max.string', ['max' => 255]);

        $data = [
            'is_active' => 'not_bool'
        ];
        $this->assertInvalidationInStoreAction($data, 'boolean');

    }

    public function testInvalidationDataOnUpdate()
    {

        $data = [
            'name' => ''
        ];
        $this->assertInvalidationInUpdateAction($data, 'required');

        $data = [
            'name'=> str_repeat('a', 256),
        ];
        $this->assertInvalidationInUpdateAction($data, 'max.string', ['max' => 255]);

        $data = [
            'is_active' => 'not_bool'
        ];
        $this->assertInvalidationInUpdateAction($data, 'boolean');
    }

    public function testStore()
    {
        $data = [
            'name'=>'test'
        ];

        $this->assertStore($data, $data + ['description'=>null, 'is_active'=>true, 'deleted_at' => null]);

        $data = [
            'name' => 'test',
            'description' => 'description',
            'is_active' => false
        ];

        $this->assertStore($data, $data + ['description'=>null, 'is_active'=>true]);
    }

    public function testUpdate()
    {
        $this->category = factory(Category::class)->create([
            'description' => 'description',
            'is_active' => false
        ]);

        $data = [
            'name' => 'test',
            'description' => 'description',
        ];
        $response = $this->assertUpdate($data, $data + ['deleted_at'=>null]);
        $response->assertJsonStructure([
            'created_at', 'updated_at'
        ]);

        $data = [
            'name' => 'test',
            'description' => '',
        ];
        $this->assertUpdate($data, array_merge($data, ['description'=>null]));

        $data['description'] = 'test';
        $this->assertUpdate($data, array_merge($data, ['description'=>'test']));
    }

    public function testDestroy()
    {
        $this->assertDestroy();
        $this->category->refresh();
        $this->assertNotNull($this->category->deleted_at);

    }

    protected function routeStore(){
        return route('categories.store');
    }
    protected function routeUpdate(){
        return route('categories.update', ['category'=> $this->category->id]);
    }
    protected function routeDestroy(){
        return route('categories.destroy', ['category'=> $this->category->id]);
    }
    protected function model()
    {
        return Category::class;
    }

}
