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

class BooksReviewController extends Controller
{
    private $bookReview;

    private $book;

    public function __construct(BookReview $bookReview, Book $book)
    {
        $this->bookReview = $bookReview;
        $this->book = $book;

        $this->middleware('auth');
    }

    public function store(int $bookId, PostBookReviewRequest $request)
    {
        $this->findBookById($bookId);

        DB::beginTransaction();
        try {
            $bookReview = new BookReview();
            $bookReview->book_id = $bookId;
            $bookReview->user_id = auth()->user()->id;
            $bookReview->review = $request->input('review');
            $bookReview->comment = $request->input('comment');
            $bookReview->save();

            DB::commit();

            return new BookReviewResource($bookReview);
        } catch(Exception $e) {
            return abort(422);
        }
    }

    public function destroy(int $bookId, int $reviewId, Request $request)
    {
        $this->findBookById($bookId);
        $this->bookReview->destroy($reviewId);

        return response()->noContent();
    }

    private function findBookById(int $bookId)
    {
        return $this->book->find($bookId) ?? abort(404);
    }
}
