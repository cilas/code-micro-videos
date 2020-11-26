<?php

namespace Tests\Unit;

use App\Models\Genre;
use App\Traits\Uuid;
use Illuminate\Database\Eloquent\SoftDeletes;
use PHPUnit\Framework\TestCase;

class GenreTest extends TestCase
{

    public function testFillable()
    {
        $category = new Genre();
        $this->assertEquals(
            ['name','is_active'],
            $category->getFillable()
        );
    }

    public function testIfUseTraits()
    {
        $traits = [
            SoftDeletes::class, Uuid::class
        ];
        $categoryTraits = array_keys(class_uses(Genre::class));
        $this->assertEquals($traits, $categoryTraits);
    }

    public function testDatesAttribute()
    {
        $dates = [
            'deleted_at', 'created_at', 'updated_at'
        ];
        $category = new Genre();
        $this->assertEquals($dates, array_values($category->getDates()));
    }

    public function testeCastAttribute()
    {
        $dates = [
            'id' => 'string'
        ];
        $category = new Genre();
        $this->assertEquals($dates, $category->getCasts());
    }

    public function testIncrementing()
    {
        $category = new Genre();
        $this->assertFalse($category->incrementing);
    }


}
