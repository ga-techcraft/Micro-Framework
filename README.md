# オンライン画像ホスティングサービス

このプロジェクトは、Imgur のようにユーザーアカウントなしで画像をアップロード・共有・表示できるシンプルな画像ホスティングサービスです。アップロードされた画像には一意の URL が発行され、他者と共有できます。

---

## 機能一覧

### 1. 画像のアップロード

* ユーザーは JPEG、PNG、GIF 形式の画像をアップロードできます。
* アップロード完了後、画像に対して一意の URL（例: `https://{domain}/{media-type}/{unique-string}`）が生成されます。
* IPアドレスやセッションごとに、一定時間内にアップロードできる件数・データ量を制限します。

### 2. 画像の表示

* 生成された URL で画像を閲覧できます。
* 各画像にはビューカウンターがあり、表示回数を記録・表示します。

### 3. 画像の削除

* 画像アップロード時に、削除専用の URL も発行されます。
* ユーザーは削除 URL にアクセスすることで、対象画像を削除できます。

### 4. ストレージ構成

* 画像はスケーラブルなディレクトリ構成と命名規則で保存されます。

### 5. データ保持

* 最終アクセスから 30 日以上経過した画像は、自動的に削除されます。
* 削除処理は cron ジョブにより 1 日 1 回実行されます。

### 6. フロントエンドインターフェース

* HTML/CSS による直感的な UI を提供します。
* JavaScript による AJAX アップロードで非同期に画像を送信可能。
* 成功時に画像URLと削除URLを表示します。

### 7. エラーハンドリング

* 以下のようなエラーをわかりやすく処理します：

  * 非対応形式
  * バリデーションエラー
  * 容量超過
  * サーバー内部エラー

---

## 技術スタック

### フロントエンド

* HTML, CSS
* JavaScript（AJAXによる画像アップロード）

### バックエンド

* PHP 8.0 以上（OOP設計）
* MySQL

### インフラ・運用

* Docker環境（PHP + MySQL + Nginx）
* DBアクセスは MySQLWrapper クラスを使用
* cron による定期処理（画像の削除など）

### コード例（画像情報の登録）

```php
$mysqli = new MySQLWrapper();
$query = "INSERT INTO images (image_name, image_path) VALUES (?, ?)";
$stmt = $mysqli->prepare($query);
$stmt->bind_param("ss", $imageName, $imagePath);
$stmt->execute();
```

---

## 非機能要件

### パフォーマンス

* 複数の同時アップロードと閲覧に対応可能
* 一意URLによる高速な画像配信

### スケーラビリティ

* ディレクトリ構成と命名ルールによりストレージ拡張が容易
* サーバーノードや容量の追加にも対応

### セキュリティ

* 全入力値に対するサニタイズ・バリデーションを実装
* IPアドレス単位でのアップロード制限を実施


## 設計
### エンドポイント設計
- フォームの表示 (GET /)
- 画像のアップロード (POST /api/images)
- 画像の表示 (GET /api/images/{unique-string})
- 画像の削除 (GET /api/images/{unique-string})

### データベース設計
- images
  - id (主キー) INT AUTO_INCREMENT PRIMARY KEY
  - image_name (ファイル名) VARCHAR(255) NOT NULL 
  - image_path (ファイルパス) VARCHAR(255) NOT NULL UNIQUE
  - view_count (閲覧数) INT DEFAULT 0
  - created_at (作成日時) TIMESTAMP DEFAULT CURRENT_TIMESTAMP 
