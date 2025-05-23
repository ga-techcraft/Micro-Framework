<?php

namespace Routing;

use Response\HTTPRenderer;
use Response\Render\HTMLRenderer;
use Response\Render\JSONRenderer;
use Response\Render\BinaryRenderer;
use Response\Render\RedirectRenderer;

use Models\ORM\Image;
use Helpers\ValidationHelper;
use Types\ValueType;
use Database\DataAccess\DAOFactory;
use Helpers\Authenticate;
use Models\User;
use Response\FlashData;
use Exception;


return [
  '' => function (): HTTPRenderer {
    if (!Authenticate::isLoggedIn()) {
      return new RedirectRenderer('login');
    }
    return new HTMLRenderer('component/file_upload', []);
  },
  'register' => function (): HTMLRenderer {
    return new HTMLRenderer('component/register', []);
  },
  'form/register' => function (): RedirectRenderer {
    if (Authenticate::isLoggedIn()) {
      FlashData::setFlashData('error', 'You are already logged in.');
      return new RedirectRenderer('/');
    }

    try {
      // リクエストメソッドがPOSTかどうかをチェックします
      if ($_SERVER['REQUEST_METHOD'] !== 'POST') throw new Exception('Invalid request method!');

      $required_fields = [
          'username' => ValueType::STRING,
          'email' => ValueType::EMAIL,
          'password' => ValueType::PASSWORD,
          'confirm_password' => ValueType::PASSWORD,
      ];

      $userDaoImpl = DAOFactory::getUserDAOImpl();

      // シンプルな検証
      $validatedData = ValidationHelper::validateFields($required_fields, $_POST);

      if($validatedData['confirm_password'] !== $validatedData['password']){
          FlashData::setFlashData('error', 'Invalid Password!');
          return new RedirectRenderer('register');
      }

      // Eメールは一意でなければならないので、Eメールがすでに使用されていないか確認します
      if($userDaoImpl->getByEmail($validatedData['email'])){
          FlashData::setFlashData('error', 'Email is already in use!');
          return new RedirectRenderer('register');
      }

      // 新しいUserオブジェクトを作成します
      $user = new User(
          username: $validatedData['username'],
          email: $validatedData['email'],
      );

      // データベースにユーザーを作成しようとします
      $success = $userDaoImpl->create($user, $validatedData['password']);

      if (!$success) throw new Exception('Failed to create new user!');

      // ユーザーログイン
      Authenticate::loginAsUser($user);

      FlashData::setFlashData('success', 'Account successfully created.');
      return new RedirectRenderer('/');
  } catch (\InvalidArgumentException $e) {
      error_log($e->getMessage());

      FlashData::setFlashData('error', 'Invalid Data.');
      return new RedirectRenderer('component/register');
  } catch (Exception $e) {
      error_log($e->getMessage());

      FlashData::setFlashData('error', 'An error occurred.');
      return new RedirectRenderer('component/register');
  }

    return new RedirectRenderer('/');
  },
  'login' => function (): HTTPRenderer {
    if(Authenticate::isLoggedIn()) {
      FlashData::setFlashData('error', 'You are already logged in.');
      return new RedirectRenderer('');
    }
    return new HTMLRenderer('component/login', []);
  },
  'form/login'=>function(): HTTPRenderer{
    if(Authenticate::isLoggedIn()){
        FlashData::setFlashData('error', 'You are already logged in.');
        return new RedirectRenderer('');
    }

    try {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') throw new Exception('Invalid request method!');

        $required_fields = [
            'email' => ValueType::EMAIL,
            'password' => ValueType::STRING,
        ];

        $validatedData = ValidationHelper::validateFields($required_fields, $_POST);

        Authenticate::authenticate($validatedData['email'], $validatedData['password']);

        FlashData::setFlashData('success', 'Logged in successfully.');
        return new RedirectRenderer('');
    } catch (\Exception $e) {
        error_log($e->getMessage());

        FlashData::setFlashData('error', 'Failed to login, wrong email and/or password.');
        return new RedirectRenderer('login');
    } catch (\InvalidArgumentException $e) {
        error_log($e->getMessage());

        FlashData::setFlashData('error', 'Invalid Data.');
        return new RedirectRenderer('login');
    } catch (Exception $e) {
        error_log($e->getMessage());

        FlashData::setFlashData('error', 'An error occurred.');
        return new RedirectRenderer('login');
    }
},

  'logout' => function (): RedirectRenderer {
    Authenticate::logoutUser();
    FlashData::setFlashData('success', 'Logged out successfully.');
    return new RedirectRenderer('');
},

  // 画像アップロード
  'api/images/upload' => function (): HTTPRenderer {
    if (!Authenticate::isLoggedIn()) {
      return new RedirectRenderer('login');
    }
    try {
      if (!isset($_FILES['image'])) {
        throw new \InvalidArgumentException("No file uploaded.");
      }

      // 画像のバリデーション
      $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif'];

      $fileExtension = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
    
      if (!in_array($fileExtension, $allowedExtensions)) {
        throw new \InvalidArgumentException("The provided file type is not allowed.");
      }

      // 画像の保存（ストレージ）
      $image = $_FILES['image'];
      $uniqueString = bin2hex(random_bytes(16));
      $filePath = __DIR__ . '/../storage/images/' . $uniqueString;
      file_put_contents($filePath, file_get_contents($image['tmp_name']));

      // 画像のパスの保存（DB）
      // $images = new Image($image['name'], $uniqueString);
      // $ImageDAOImpl = DAOFactory::getImagesDAO();
      // $ImageDAOImpl->create($images);

      Image::create([
        'image_name' => $image['name'],
        'unique_string' => $uniqueString,
      ]);

      // // 画像のパスの保存（キャッシュ）
      // $ImageDAOMemcachedImpl = new ImagesDAOMemcachedImpl();
      // $ImageDAOMemcachedImpl->create($images);

      return new JSONRenderer([
        'uniqueString' => $uniqueString,
      ]);
    } catch (\Exception $e) {
      return new JSONRenderer([
        'error' => $e->getMessage(),
      ]);
    }
  },
  // 画像本体データを返す
  'api/images/view' => function (): BinaryRenderer {
    $uniqueString = $_GET['uniqueString'];
    $binaryPath = __DIR__ . '/../storage/images/' . $uniqueString;
    $mimeType = mime_content_type($binaryPath);
    return new BinaryRenderer($mimeType, $uniqueString);
  },
  // 画像の削除
  'api/images/delete' => function (): HTMLRenderer {
    $uniqueString = $_GET['uniqueString'];
    try {
      ValidationHelper::validateFields([
        'uniqueString' => ValueType::STRING,
      ], $_GET);
    } catch (\Exception $e) {
      return new HTMLRenderer('component/result', [
        'result' => $e->getMessage(),
      ]);
    }

    // DBに指定された画像が存在するか確認
    $ImageDAOImpl = DAOFactory::getImagesDAO();
    $image = $ImageDAOImpl->getByUniqueString($uniqueString);
    
    if($image === null){
      return new HTMLRenderer('component/result', [
        'result' => 'Image not found',
      ]);
    } else {
      // DBから画像を削除
      $ImageDAOImpl->delete($image->getUniqueString());

      // 画像ファイルをディレクトリから削除
      unlink(__DIR__ . '/../storage/images/' . $uniqueString);

      return new HTMLRenderer('component/result', [
        'result' => 'Image deleted',
      ]);
    }
  },
];