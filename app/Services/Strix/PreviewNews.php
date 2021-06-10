<?php


namespace App\Services\Strix;


use App\Helpers\DbConnections;
use Illuminate\Support\Collection;

class PreviewNews
{
    private $query;

    public function __construct(string $databaseName)
    {
        $this->query = DbConnections::setStrixConnection($databaseName)->table('news');
    }

    public function getNews($date = null): Collection
    {
//        $rawQuery = `SELECT
//                    news.id,
//                    news.title,
//                    news.lead,
//                    news.content,
//                    news.date,
//                    news.lead_image,
//                    news.keywords,
//                    users.ime AS author_firstname,
//                    users.prezime AS author_lastname,
//                    users.email AS author_email,
//                    alt_author.ime AS alt_author_firstname,
//                    alt_author.prezime AS alt_author_lastname,
//                    alt_author.email AS alt_author_email,
//                    news.original_kat,
//                    kategorija.url,
//                    kategorija.naziv,
//                    string_agg(news_document."name", ', ' ORDER BY news_document."name") as attached_files
//                    FROM
//                    news
//                    LIMIT ?
//                    join users on users.id = news.author_id
//                    left join news_document on news_document.news_id = news.id
//                    left join kategorija on kategorija.id = news.original_kat
//                    left join news_kategorija on news_kategorija.news_id = news.id
//                    left join news_author on news_author.news_id = news.id
//                    left join users AS alt_author on alt_author.id = news_author.author_id
//                    where
//                    news_kategorija.archived = 'f'
//                    GROUP BY news.id,users.ime,users.prezime,users.email,alt_author_firstname,alt_author_lastname,alt_author_email,kategorija.url,kategorija.naziv
//                    Order BY news.date DESC`;
//        -- ukoliko je potrebno samo neke kategirije dohvatiti
//    AND kategorija.url in (
//        '/aktualno/vijesti-iz-skola'
//    )
        return $this->query->select(['news.id', 'news.title', 'news.content'])
            ->when($date, function ($query, $date) {
                return $query->where('news.date', '>', $date);
            })
            ->get();
    }

    public function importNews(array $exceptNewsIds, $date = false, $all = false): Collection
    {
        $this->query->select(['news.title', 'news.content', 'news.date'])
            ->when($date, function ($query, $date) {
                return $query->where('news.date', '>', $date);
            });

        if (!$all)
            $this->query->whereNotIn('news.id', $exceptNewsIds);

        return $this->query->get();

    }

    public static function from(string $databaseName)
    {
        return new static($databaseName);
    }
}
