<?php

use Dom\HTMLElement;
use Helpers\DatabaseHelper;
use Models\ComputerPart;
use Helpers\Authenticate;
use Models\User;
use Helpers\ValidationHelper;
use Response\HTTPRenderer;
use Response\Render\HTMLRenderer;
use Response\Render\JSONRenderer;
use Response\Render\RedirectRenderer;
use Database\DataAccess\DAOFactory;
use Types\ValueType;
use Response\FlashData;

return [
      'random/part'=>function(): HTTPRenderer{
        $partDao = DAOFactory::getComputerPartDAO();
        $part = $partDao->getRandom();

        if($part === null) throw new Exception('No parts are available!');

        return new HTMLRenderer('component/computer-part-card', ['part'=>$part]);
    },
    'parts'=>function(): HTTPRenderer{
        // IDの検証
        $id = ValidationHelper::integer($_GET['id']??null);

        $partDao = DAOFactory::getComputerPartDAO();
        $part = $partDao->getById($id);

        if($part === null) throw new Exception('Specified part was not found!');

        return new HTMLRenderer('component/computer-part-card', ['part'=>$part]);
    },
    'api/random/part'=>function(): HTTPRenderer{
        $part = DatabaseHelper::getRandomComputerPart();
        return new JSONRenderer(['part'=>$part]);
    },
    'api/parts'=>function(){
        $id = ValidationHelper::integer($_GET['id']??null);
        $part = DatabaseHelper::getComputerPartById($id);
        return new JSONRenderer(['part'=>$part]);
    },
    'parts/newest'=>function(): HTMLRenderer{
        return new HTMLRenderer('component/parts-newest');
    },
    'api/parts/newest'=>function(){
        // データベースから最新のパーツを取得して、それを返す
        $part = DatabaseHelper::getNewestComputerPart();
        return new JSONRenderer(['part'=>$part]);
    },
    'types' =>function(): HTMLRenderer{
      return new HTMLRenderer('component/parts');
    },
    'api/types'=>function(){
      $limit = 10; // 最大表示数
      $page = (int)$_GET['page']??1; 
      $type = $_GET['type']??null;
      $offset = ($page - 1) * $limit; // 取得するデータの開始位置
      $parts = DatabaseHelper::getComputerPartByType($type, $limit, $offset);

      $total = DatabaseHelper::getCountComputerPartByType($type); // レコード数
      $totalPages = ceil($total / $limit);

      return new JSONRenderer(['page'=>$page, 'parts'=>$parts, 'totalPages'=>$totalPages]);
    },
    'random/computer'=>function(){
        return new HTMLRenderer('component/random-create');
    },
    'api/top-performance'=>function(){
        $parts = DatabaseHelper::getTopPerformanceComputerPart();
        return new JSONRenderer(['parts'=>$parts]);
    },
    'top-performance'=>function(): HTMLRenderer{
        return new HTMLRenderer('component/top-performance');
    },
    'api/random/computer'=>function(){
        $total = DatabaseHelper::getCountComputerPart();
        $randomId = rand(1, $total);
        $part = DatabaseHelper::getComputerPartById($randomId);
        return new JSONRenderer(['part'=>$part]);
    },
    'update/part' => function(): HTMLRenderer {
      $part = null;
      $partDao = DAOFactory::getComputerPartDAO();
      if(isset($_GET['id'])){
          $id = ValidationHelper::integer($_GET['id']);
          $part = $partDao->getById($id);
      }
      return new HTMLRenderer('component/update-computer-part',['part'=>$part]);
    },
    'form/update/part' => function(): HTTPRenderer {
        try {
            // リクエストメソッドがPOSTかどうかをチェックします
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                throw new Exception('Invalid request method!');
            }

            $required_fields = [
                'name' => ValueType::STRING,
                'type' => ValueType::STRING,
                'brand' => ValueType::STRING,
                'modelNumber' => ValueType::STRING,
                'releaseDate' => ValueType::DATE,
                'description' => ValueType::STRING,
                'performanceScore' => ValueType::INT,
                'marketPrice' => ValueType::FLOAT,
                'rsm' => ValueType::FLOAT,
                'powerConsumptionW' => ValueType::FLOAT,
                'lengthM' => ValueType::FLOAT,
                'widthM' => ValueType::FLOAT,
                'heightM' => ValueType::FLOAT,
                'lifespan' => ValueType::INT,
            ];

            $partDao = DAOFactory::getComputerPartDAO();

            // 入力に対する単純なバリデーション。実際のシナリオでは、要件を満たす完全なバリデーションが必要になることがあります。
            $validatedData = ValidationHelper::validateFields($required_fields, $_POST);

            if(isset($_POST['id'])) $validatedData['id'] = ValidationHelper::integer($_POST['id']);

            // 名前付き引数を持つ新しいComputerPartオブジェクトの作成＋アンパッキング
            $part = new ComputerPart(...$validatedData);

            error_log(json_encode($part->toArray(), JSON_PRETTY_PRINT));

            // 新しい部品情報でデータベースの更新を試みます。
            // 別の方法として、createOrUpdateを実行することもできます。
            if(isset($validatedData['id'])) $success = $partDao->update($part);
            else $success = $partDao->create($part);

            if (!$success) {
                throw new Exception('Database update failed!');
            }

            return new JSONRenderer(['status' => 'success', 'message' => 'Part updated successfully']);
        } catch (\InvalidArgumentException $e) {
            error_log($e->getMessage()); // エラーログはPHPのログやstdoutから見ることができます。
            return new JSONRenderer(['status' => 'error', 'message' => 'Invalid data.']);
        } catch (Exception $e) {
            error_log($e->getMessage());
            return new JSONRenderer(['status' => 'error', 'message' => 'An error occurred.']);
        }
    },
    'register'=>function(): HTTPRenderer{
        return new HTMLRenderer('page/register');
    },
    'form/register' => function(): HTTPRenderer {
        // ユーザが現在ログインしている場合、登録ページにアクセスすることはできません。
        if(Authenticate::isLoggedIn()){
            FlashData::setFlashData('error', 'Cannot register as you are already logged in.');
            return new RedirectRenderer('random/part');
        }

        try {
            // リクエストメソッドがPOSTかどうかをチェックします
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') throw new Exception('Invalid request method!');

            $required_fields = [
                'username' => ValueType::STRING,
                'email' => ValueType::STRING,
                'password' => ValueType::STRING,
                'confirm_password' => ValueType::STRING,
                'company' => ValueType::STRING,
            ];

            $userDao = DAOFactory::getUserDAO();

            // シンプルな検証
            $validatedData = ValidationHelper::validateFields($required_fields, $_POST);

            if($validatedData['confirm_password'] !== $validatedData['password']){
                FlashData::setFlashData('error', 'Invalid Password!');
                return new RedirectRenderer('register');
            }

            // Eメールは一意でなければならないので、Eメールがすでに使用されていないか確認します
            if($userDao->getByEmail($validatedData['email'])){
                FlashData::setFlashData('error', 'Email is already in use!');
                return new RedirectRenderer('register');
            }

            // 新しいUserオブジェクトを作成します
            $user = new User(
                username: $validatedData['username'],
                email: $validatedData['email'],
                company: $validatedData['company']
            );

            // データベースにユーザーを作成しようとします
            $success = $userDao->create($user, $validatedData['password']);

            if (!$success) throw new Exception('Failed to create new user!');

            // ユーザーログイン
            Authenticate::loginAsUser($user);

            FlashData::setFlashData('success', 'Account successfully created.');
            return new RedirectRenderer('random/part');
        } catch (\InvalidArgumentException $e) {
            error_log($e->getMessage());

            FlashData::setFlashData('error', 'Invalid Data.');
            return new RedirectRenderer('register');
        } catch (Exception $e) {
            error_log($e->getMessage());

            FlashData::setFlashData('error', 'An error occurred.');
            return new RedirectRenderer('register');
        }
    },
    'logout'=>function(): HTTPRenderer{
        if(!Authenticate::isLoggedIn()){
            FlashData::setFlashData('error', 'Already logged out.');
            return new RedirectRenderer('random/part');
        }

        Authenticate::logoutUser();
        FlashData::setFlashData('success', 'Logged out.');
        return new RedirectRenderer('random/part');
    },

];


