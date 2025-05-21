<?php

namespace Commands;

interface Command{
  public static function getAlias(): string;
  public static function help(): string;
  public static function isCommandValueRequired(): bool;
  public static function getArguments(): array;

  public function getArgumentValue(string $arg): bool | string;
  public function execute(): int;
}
