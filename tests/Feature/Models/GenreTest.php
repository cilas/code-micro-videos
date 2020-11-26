<?php

namespace Tests\Feature\Models;

use App\Models\Genre;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Ramsey\Uuid\Uuid;
use Tests\TestCase;

class GenreTest extends TestCase
{
    use DatabaseMigrations;

    public function testList()
    {
        factory(Genre::class, 1)->create();
        $genres = Genre::all();
        $this->assertCount(1, $genres);
        $genreKey = array_keys($genres->first()->getAttributes());
        $this->assertEqualsCanonicalizing([
            'id',
            'name',
            'is_active',
            'created_at',
            'updated_at',
            'deleted_at'
            ],
            $genreKey
        );

    }
    public function testCreate()
    {
        $genre = Genre::create([
            'name' => 'test 1'
        ]);
        $genre->refresh();
        $this->assertEquals('test 1', $genre->name);
        $this->assertTrue($genre->is_active);
    }

    public function testIfIdIsUuid()
    {
        $genre = Genre::create([
            'name' => 'test 1'
        ]);
        $genre->refresh();
        $this->assertIsString($genre->id);
        $this->assertTrue(Uuid::isValid($genre->id));
    }

    public function testCreateWhenIsActiveIsFalse()
    {
        $genre = Genre::create([
            'name' => 'test 1',
            'is_active' => false
        ]);
        $this->assertFalse($genre->is_active);
    }

    public function testCreateWhenIsActiveIsTrue()
    {
        $genre = Genre::create([
            'name' => 'test 1',
            'is_active' => true
        ]);
        $this->assertTrue($genre->is_active);
    }

    public function testUpdate()
    {
        $genre = factory(Genre::class)->create()->first();

        $data = [
            'name' => 'test_updated_name',
            'is_active' => false
        ];

        $genre->update($data);

        foreach ($data as $key => $value) {
            $this->assertEquals($value, $genre->{$key});
        }
    }

    public function testDelete()
    {
        $genre = Genre::create([
            'name' => 'test 1',
        ]);
        $genre->delete();
        $this->assertNotNull($genre->deleted_at);
        $this->assertSoftDeleted('genres', [
            'id' => $genre->id,
            'name' => $genre->name,
        ]);
    }
}
