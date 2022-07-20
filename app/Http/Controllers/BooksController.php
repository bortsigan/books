<?php

declare (strict_types=1);

namespace App\Http\Controllers;

use App\Author;
use App\Book;
use App\BookReview;
use App\Http\Requests\PostBookRequest;
use App\Http\Requests\PostBookReviewRequest;
use App\Http\Resources\BookResource;
use App\Http\Resources\BookReviewResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BooksController extends Controller
{
    const DEFAULT_PAGE = 15;

    private $book;

    private $bookReview;

    private $author;
    
    public function __construct(Book $book, BookReview $bookReview, Author $author)
    {
        $this->book = $book;
        $this->author = $author;
        $this->bookReview = $bookReview;
        $this->author = $author;

        $this->middleware('auth')->except(['index']);
    }

    public function index(Request $request)
    {
        $title = $request->input('title') ?? null;
        $authors = $request->input('authors') ?? null;
        $sortColumn = $request->input('sortColumn') ?? 'id';
        $sortDirection = $request->input('sortDirection') ?? 'ASC';

        $books = $this->book->orderBy($sortColumn, $sortDirection);

        if ($title) {
            $books->where('title', 'like', '%' . $title . '%');
        }

        if ($authors) {
            $authors = explode(",", $authors);
            $books->whereHas('authors', function($query) use ($authors) {
                return $query->whereIn('authors.id', $authors);
            });
        }

        if ($sortColumn == 'avg_review') {
            $books->withCount(['reviews as avg_review' => function($query) {
                $query->select(DB::raw('coalesce(avg(book_reviews.review),0)'));
            }]);
        }
        
        return BookResource::collection($books->paginate(self::DEFAULT_PAGE));
    }

    public function store(PostBookRequest $request)
    {
        DB::beginTransaction();
        try {
            $book = new Book();
            $book->isbn = $request->input('isbn');
            $book->title = $request->input('title');
            $book->description = $request->input('description');
            $book->published_year = $request->input('published_year');
            $book->save();
            $book->authors()->sync($request->input('authors'));
            
            DB::commit();

            return new BookResource($book);
        } catch (Exception $e) {
            DB::rollback();
            return abort(422);
        }
    }

}
