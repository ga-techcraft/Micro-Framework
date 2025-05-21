<?php

namespace Routing;

use Response\Render\HTMLRenderer;
use Response\Render\JSONRenderer;
use Response\Render\BinaryRenderer;

use Models\ORM\Image;
use Helpers\ValidationHelper;
use Types\ValueType;
use Database\DataAccess\DAOFactory;


return [
  '' => function (): HTMLRenderer {
    return new HTMLRenderer('file_upload', []);
  },
  // 画像アップロード
  'api/images/upload' => function (): JSONRenderer {
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
      return new HTMLRenderer('result', [
        'result' => $e->getMessage(),
      ]);
    }

    // DBに指定された画像が存在するか確認
    $ImageDAOImpl = DAOFactory::getImagesDAO();
    $image = $ImageDAOImpl->getByUniqueString($uniqueString);
    
    if($image === null){
      return new HTMLRenderer('result', [
        'result' => 'Image not found',
      ]);
    } else {
      // DBから画像を削除
      $ImageDAOImpl->delete($image->getUniqueString());

      // 画像ファイルをディレクトリから削除
      unlink(__DIR__ . '/../storage/images/' . $uniqueString);

      return new HTMLRenderer('result', [
        'result' => 'Image deleted',
      ]);
    }
  },
];