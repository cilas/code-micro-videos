<?php

namespace Tests\Feature\Http\Controllers\Api;

use App\Models\Category;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\TestResponse;
use Tests\TestCase;

class CategoryControllerTest extends TestCase
{
    use DatabaseMigrations;

    public function testIndex()
    {
        $category = factory(Category::class)->create();
        $response = $this->get(route("categories.index"));
        $response
            ->assertStatus(200)
            ->assertJson([$category->toArray()]);
    }

    public function testShow()
    {
        $category = factory(Category::class)->create();
        $response = $this->get(route("categories.show", ['category'=> $category->id]));
        $response
            ->assertStatus(200)
            ->assertJson($category->toArray());
    }

    public function testInvalidationDataOnCreate()
    {
        $response = $this->json("POST", route("categories.store", []));
        $this->assertInvalidationRequired($response);

        $response = $this->json("POST", route("categories.store", [
            'name'=> str_repeat('a', 256),
            'is_active' => 'not_bool'
        ]));
        $this->assertInvalidationMax($response);
        $this->assertInvalidationBoolean($response);


    }

    public function testInvalidationDataOnUpdate()
    {
        $category = factory(Category::class)->create();
        $response = $this->json("PUT", route("categories.update", ['category'=> $category->id]), []);
        $this->assertInvalidationRequired($response);

        $response = $this->json("PUT", route("categories.update",  ['category'=> $category->id]), [
            'name'=> str_repeat('a', 256),
            'is_active' => 'not_bool'
        ]);
        $this->assertInvalidationMax($response);
        $this->assertInvalidationBoolean($response);

    }

    protected function assertInvalidationRequired(TestResponse $response)
    {
        $response
            ->assertStatus(422)
            ->assertJsonValidationErrors(['name'])
            ->assertJsonMissingValidationErrors(['is_active'])
            ->assertJsonFragment([
                \Lang::get('validation.required', ['attribute'=>'name'])
            ]);
    }

    protected function assertInvalidationMax(TestResponse $response)
    {
        $response
        ->assertStatus(422)
        ->assertJsonValidationErrors(['name'])
        ->assertJsonFragment([
            \Lang::get('validation.max.string', ['attribute'=>'name', 'max'=>255])
        ]);
    }

    protected function assertInvalidationBoolean(TestResponse $response)
    {
        $response
        ->assertStatus(422)
        ->assertJsonValidationErrors(['is_active'])
        ->assertJsonFragment([
            \Lang::get('validation.boolean', ['attribute'=>'is active',])
        ]);
    }
}
