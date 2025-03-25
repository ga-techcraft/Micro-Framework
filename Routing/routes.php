<?php

use Dom\HTMLElement;
use Helpers\DatabaseHelper;
use Helpers\ValidationHelper;
use Response\HTTPRenderer;
use Response\Render\HTMLRenderer;
use Response\Render\JSONRenderer;

return [
    'random/part'=>function(): HTTPRenderer{
        $part = DatabaseHelper::getRandomComputerPart();

        return new HTMLRenderer('component/part', ['part'=>$part]);
    },
    'parts'=>function(): HTTPRenderer{
        // IDの検証
        $id = ValidationHelper::integer($_GET['id']??null);

        $part = DatabaseHelper::getComputerPartById($id);
        return new HTMLRenderer('component/part', ['part'=>$part]);
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
    'types'=>function(): HTTPRenderer{
      $type = $_GET['type']??null;
      $parts = DatabaseHelper::getComputerPartByType($type);
      return new HTMLRenderer('component/parts', ['parts'=>$parts]);
    }
];