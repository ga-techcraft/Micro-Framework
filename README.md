# プロジェクトを進めていく中での学び(2025/3/11更新)
### ただただ箇条書きですが、最終的にはまとめます。
- これまでもGitを使用していたが、以前はこまめにコミットせずに進めてしまうことが多かった。しかし、プロジェクトが進行する中でフォルダやファイルが増え、どこまで完成していて、どこから着手すべきかが分からなくなってしまうことに気づいた。そのため、現在では作業を小さく区切ってコミットし、コミットメッセージは可能な限り詳細に記載するように心掛けるようにした。この方法により、作業の進捗管理が格段にしやすくなった。
- __DIR__は現在のスクリプトが存在するディレクトリを示し、__FILE__は現在実行中のファイルのフルパスを示す。
- クエリを実行して結果がfalseだったら接続エラー。
- phpでは名前付き引数を渡せる
- mysql -u {your_username} -p practice_db < Database/Examples/cars-setup.sql  でシェルからsqlコマンドを実行できる。
- パスワードはハッシュ化されると長くなるから255文字で設定する
- UNIQUEは一意の値のみ挿入できるが、NULLは許容される。また複数のカラムに適用できて、自動的にインデックスも作成される。
- KEYは値の重複を許容する。インデックスを作成する。NULLを許容する。
- PRIMARY KEYは値はUNIQUEでありNOT NULLである。
- 文字列の中で\$を使った場合、phpは変数だと解釈するため例えばincludeで読み込めないことがあった。でも\でエスケープすることで$をそのまま文字列として読み込めた。
- RecurtionでOSのファイルシステムについて学んだことで、ファイルの読み書きのコードを書いている時に、ファイル記述子テーブルやオープンファイルテーブルなどをイメージできて、納得しながらコードを書けるようになっている。
- キャッシュについて、テーブルを作成してkeyとvalueのカラムを用意すれば実装できる。意外とシンプルだと気づいた。
- file_get_content()はファイルの中身を文字列をして取得。require/includeはphpファイルの実行結果を取得。
- var_export()は連想配列だとphpが認識できる文字列に変換してくれる
- SQLインジェクションについて、ユーザーが入力したデータがSQL文の一部となると危ない。途中でSQL文がコメントになるように仕向けられることがある。prepare関数やbind_param関数で安全に処理をすること。
- ヘッダーインジェクションという攻撃があるみたい。だからヘッダーもチェックが必要
- 基本的にブラウザで複数のオリジンにアクセスはできない。header("Access-Control-Allow-Origin: *");を書いておくと接続できる。またlocalhostと127.0.0.1は異なるオリジンと認識される。
 