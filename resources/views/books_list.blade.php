@extends('base')

@section('title', 'Список книг')

@section('content')
    <div class="uk-margin">
        <h1>Список книг</h1>
        <form method="GET" action="">
            <div class="uk-margin">
                <input class="uk-input" type="text" name="q" value="{{ $q }}" placeholder="поиск">
            </div>
        </form>
        <table class="uk-table uk-table-hover">
            <thead>
                <tr>
                    <th>#ID</th>
                    <th>изображение</th>
                    <th>название</th>
                    <th>год издания</th>
                    <th>автор</th>
                    <th>цена</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($books_list as $book)
                <tr>
                    <td>{{ $book->id }}</td>
                    <td><img src="/storage/books/{{ $book->img }}" width="200" uk-img /></td>
                    <td>{{ $book->name }}</td>
                    <td>{{ $book->year }}</td>
                    <td>{{ $book->authors }}</td>
                    <td>{{ $book->price }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
        <hr class="uk-divider-icon">
        {{ $books_list->links() }}
    </div>
@endsection