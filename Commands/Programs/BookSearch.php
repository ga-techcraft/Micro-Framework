<?php

namespace Commands\Programs;

use Commands\AbstractCommand;
use Commands\Argument;
use Database\MySQLWrapper;

class BookSearch extends AbstractCommand
{
    // 使用するコマンド名を設定
    protected static ?string $alias = 'book-search';

    // 引数を割り当て
    public static function getArguments(): array
    {
        return [
          new Argument('title')->description('Retrieve book information from Open Library with title')->required(false),
          new Argument('isbn')->description('Retrieve book information from Open Library with isbn')->required(false)
        ];
    }

    public function execute(): int
    {
        // titleまたはisbnがどちらもfalseだったら例外を投げる
        $title = $this->getArgumentValue('title');
        $isbn = $this->getArgumentValue('isbn');

        if ($title) {
          $arg = ['title' => $title];
        } else if ($isbn) {
          $arg = ['isbn' => $isbn];
        } else {
          throw new \Exception("ISBN or title is not provided");
        }

        // キャッシュにデータがあるか確認する
        $cachedData = $this->isCached($arg);

        // キャッシュにデータが無ければ、open libraryに接続し情報を取得し出力する
        if ($cachedData == false) {
          // 最初のデータのみ取得する
          $searchedData = json_decode($this->search($arg), true)["docs"][0];
          // データベースには著者名のみ格納する
          $author_name = $searchedData["author_name"][0];
          $this->addCache($arg, json_encode($author_name));
          $this->log("キャッシュにデータを保存しました。");
        } else {
          // キャッシュにあるデータを出力する
          $this->log($cachedData);
        }

        return 0;
    }

    // cachesテーブルのcacheKeyカラムの作成
    private function createCacheKey(array $arg): string {
      foreach ($arg as $key => $value) {
        $cacheKey = "book-search-$key-$value";
      }
      return $cacheKey;
    }

    // キャッシュされているか確認する
    private function isCached(array $arg): bool | string {
      // cacheKeyの作成
      $cacheKey = $this->createCacheKey($arg);

      // cacheデータの取得
      $mysqli = new MySQLWrapper();
      $result = $mysqli->query("
        SELECT cacheValue from caches WHERE cacheKey = '$cacheKey';
      ");
      $mysqli->close();

      if ($result->num_rows == 0) {
        return false;
      } else {
        $this->log("キャッシュにあるデータです。");
        return $result->fetch_row()[0];
      }
    }

    // open libraryに接続し情報を取得する
    private function search(array $arg): bool | string {
      $url = "https://openlibrary.org/search.json?";

      foreach ($arg as $key => $value) {
        if ($key === 'title') {
          $url .= "q=" . "$value";
        } else {
          $url .= "isbn=" . "$value";
        }
      }

      $result = file_get_contents($url);

      return $result;
    }

    // キャッシュテーブルにデータを保存する
    private function addCache(array $arg, string $searchedData): void {
      // cacheKeyの作成
      $cacheKey = $this->createCacheKey($arg);

      $mysqli = new MySQLWrapper();

      $escapedSearchedData = $mysqli->real_escape_string($searchedData);

      $result = $mysqli->query("
        INSERT INTO caches (cacheKey, cacheValue) VALUES('$cacheKey', '$escapedSearchedData');
      ");

      if ($result === false) {
        throw new \Exception("Could not execute insert caches query.");
      } else {
          print("Successfully ran SQL for inserting cache.".PHP_EOL);
      }

      $mysqli->close();
    }

    // 一定時間キャッシュへの接続がなかったら削除する
    private function deleteCache(): void {
      // 今後実装予定
    }
}