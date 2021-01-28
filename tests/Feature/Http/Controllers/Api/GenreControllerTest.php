<?php

namespace Tests\Feature\Http\Controllers\Api;

use App\Models\Genre;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\TestResponse;
use Tests\TestCase;
use Tests\Traits\TestValidations;
use Tests\Traits\TestSaves;

class GenreControllerTest extends TestCase
{
    use DatabaseMigrations, TestValidations, TestSaves;
    private $genre;
    protected function setUp(): void
    {
        parent::setUp();
        $this->genre = factory(Genre::class)->create();
    }

    public function testIndex()
    {
        $response = $this->get(route("genres.index"));
        $response
            ->assertStatus(200)
            ->assertJson([$this->genre->toArray()]);
    }

    public function testShow()
    {
        $response = $this->get(route("genres.show", ['genre'=> $this->genre->id]));
        $response
            ->assertStatus(200)
            ->assertJson($this->genre->toArray());
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
        $response = $this->json("POST", route('genres.store'), [
            'name' => 'test',
            'is_active' => false
        ]);

        $response->assertJsonFragment([
                'is_active' => false
            ]);
        $data = [
                'name'=>'test'
            ];

        $this->assertStore($data, $data + ['is_active'=>true, 'deleted_at' => null]);

        $data = [
                'name' => 'test',
                'is_active' => false
            ];

        $this->assertStore($data, $data + ['is_active'=>true]);
    }

    public function testUpdate()
    {
        $this->genre = factory(Genre::class)->create([
            'is_active' => false
        ]);

        $data = [
            'name' => 'test',
        ];
        $response = $this->assertUpdate($data, $data + ['deleted_at'=>null]);
        $response->assertJsonStructure([
            'created_at', 'updated_at'
        ]);
    }

    public function testDestroy()
    {
        $this->assertDestroy();
        $this->genre->refresh();
        $this->assertNotNull($this->genre->deleted_at);
    }

    protected function routeStore(){
        return route('genres.store');
    }
    protected function routeUpdate(){
        return route('genres.update', ['genre'=>$this->genre->id]);
    }
    protected function routeDestroy(){
        return route('genres.destroy', ['genre'=> $this->genre->id]);
    }
    protected function model()
    {
        return Genre::class;
    }
}
