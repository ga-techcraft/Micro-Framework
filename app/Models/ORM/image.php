<?php

namespace Models\ORM;

use Database\DataAccess\ORM;

class Image extends ORM{
    // $columnTypesにはカラム名とデータ型の連想配列を定義します。
    // 's'は文字列、'i'は整数、'd'は浮動小数点数を表します。
    // 定義しない場合は、全ての入力が必須となります。
    protected static ?array $columnTypes = [
        'image_name' => 's',
        'unique_string' => 's',
    ];
}
