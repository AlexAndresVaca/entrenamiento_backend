<?php

namespace Tests\Feature;

// use Illuminate\Foundation\Testing\RefreshDatabase;

use App\Models\Book;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BooksApiTest extends TestCase
{
    use RefreshDatabase;
    // AGREGAR DE MANERA OBLIGATORIA O EL PREFIJO test en cada funcion
    /** @test */
    function can_get_all_books()
    {
        # code...
        // USAR FACTORY
        $books = Book::factory(4)->create();
        // COMPROBAR RUTA INDEX
        // LA PETICION VA CON HEADERS TIPO JSON
        // COMPROBAMOS QUE LA INFORMACION SEA LA CORRECTA
        $this->getJson(route('books.index'))->assertJsonFragment([
            'title' => $books[0]->title,
        ])->assertJsonFragment([
            'title' => $books[1]->title,
        ]);
    }
    /** @test */
    function can_get_one_book()
    {
        # code...
        $book = Book::factory()->create();
        $this->getJson(route('books.show', $book))->assertJsonFragment([
            'title' => $book->title,
        ]);
    }
    /** @test */
    function can_create_books()
    {
        // TEST DE REGRESION
        // FORZAR COMPROBACION DE VALIDACION DE 'TITLE'
        // CON ESTE METODO COMPROBAMOS QUE ESTE VALIDANDO EL CAMPO TITLE
        $this->postJson(route('books.store'), [])->assertJsonValidationErrorFor('title');

        // INGRESAR UN NUEVO BOOK Y COMPROBAR LA CREACION
        $this->postJson(route('books.store'), [
            'title' => 'My new book'
        ])->assertJsonFragment([
            'title' => 'My new book'
        ]);
        // VERIFICAR EXISTENCIA EN BDD
        $this->assertDatabaseHas('books', ['title' => 'My new book']);
    }
    // _TEST
    function test_can_edit_books()
    {
        // CREAMOS UN REGISTRO
        $book = Book::factory()->create();
        // VERIFICAMOS QUE SE CUMPLA LA VALIDACION
        $this->patchJson(route('books.update', $book), [])->assertJsonValidationErrorFor('title');
        // EDITAMOS UN BOOK Y COMPROBAR LOS DATOS
        $this->patchJson(route('books.update', $book), [
            'title' => 'Edited Book'
        ])->assertJsonFragment([
            'title' => 'Edited Book'
        ]);
        // VERIFICAR EXISTENCIA EN BDD
        $this->assertDatabaseHas('books', ['title' => 'Edited Book']);
    }
    function test_can_delete_books()
    {
        // CREAMOS UN REGISTRO
        $book = Book::factory()->create();
        // ELIMINAMOS EL REGISTRO Y ESPERAMOS LA RESPUESTA 204 'NO CONTENT'
        $this->deleteJson(route('books.destroy', $book))->assertNoContent();
        // VERIFICAMOS EN BASE DE DATOS
        $this->assertDatabaseCount('books', 0);
    }
}
