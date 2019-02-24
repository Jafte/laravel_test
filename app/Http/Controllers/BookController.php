<?php

namespace App\Http\Controllers;

use App\Book;
use Illuminate\Http\Request;

class BookController extends Controller
{
    public function list(Request $request) {
        $page_size = 10;
        $books_list = Book::paginate($page_size);
        $q = $request->query('q', '');

        if ($q) {
            $books_list = Book::search($q)->paginate($page_size);
        };

        return view('books_list', [
            'books_list' => $books_list,
            'q' => $q,
        ]);
    }
}
