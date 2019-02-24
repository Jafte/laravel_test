<?php

namespace App\Console\Commands;

use Symfony\Component\DomCrawler\Crawler;
use Illuminate\Support\Facades\Storage;
use Illuminate\Console\Command;
use App\Book;

class ParseBooks extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'books:parse';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Parsing some books from source site';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $url = "https://www.piter.com/collection/biblioteka-programmista?page_size=100&order=&q=&only_available=true";
        $source = 'piter.com';

        $this->line('parse some '.$source);

        $html = file_get_contents($url);
        $crawler = new Crawler($html);
        
        // тут лежать все книги
        $products_list = $crawler->filter('div.wrapper > div.main-content > div.products-list > div.book-block');

        $books = [];
        
        foreach ($products_list as $book_node) {
            $detail_url = false;
            $name = false;
            $authors = false;
            $img = false;
            $year = false;
            $price = false;
        
            foreach ($book_node->childNodes as $book_block) {
                // страхуемся от комментариев и прочего мусора, работаем только с элементами ДОМ дерева
                if ($book_block->nodeType === XML_ELEMENT_NODE) {
                    // тут у нас лежит сама книжка
                    if ($book_block->tagName == "a") {
                        $detail_url = 'https://www.piter.com/'.$book_block->getAttribute('href');
                        $name = trim($book_block->getAttribute('title'));

                        // забираем детальную информацию и парсим ее,
                        // в листинге нет данных по году выпуска книжки, поэтому приходится идти на детальную страницу.
                        $book_html = file_get_contents($detail_url);
                        $book_crawler = new Crawler($book_html);

                        $price = trim($book_crawler->filter('div.wrapper > div.product-block > div.product-variants  div.price')->text('0'));
                        $price = trim(str_replace("р.", '', $price));

                        // проверка на уникальность по названию, 
                        // в теории, можно использовать ISBN с детальной страницы, но для теста сделал так
                        $book = Book::where([
                            "name" => $name,
                            "source" => $source,
                        ])->first();

                        if (!$book) {
                            // новая книжка
                            $this->line('craete book: '.$name);

                            $authors = trim($book_crawler->filter('div.wrapper > div.product-block > div.product-info > p.author > span')->text('-'));
                            $year = trim($book_crawler->filter('div.wrapper > div.product-block > div.product-info > div > ul > li:nth-child(2) > span.grid-7')->text('-'));
                            $img = $book_crawler->filter('div.wrapper > div.product-block > div.photo > a > img')->attr('src');
                            
                            // сохраняем
                            $book = new Book;
                            $book->source = $source;
                            $book->name = $name;
                            $book->authors = $authors;
                            $book->year = $year;
                            $book->price = $price;
                            $book->save();

                            // сохраняем каритнку
                            $img_arr = explode(".", $img);
                            $file_ending = array_pop($img_arr);
                            $new_filename = $book->id.'.'.$file_ending;

                            Storage::put('public/books/'.$new_filename, file_get_contents($img));

                            $book->img = $new_filename;
                            $book->save();
                        } else {
                            // обновим цену, если книжка уже есть
                            $this->line('update book: '.$name);
                            $book->price = $price;
                            $book->save();
                        }
                        $this->line('book saved!');
                    }
                }
            }
        }
        $this->line('all done');
    }
}
