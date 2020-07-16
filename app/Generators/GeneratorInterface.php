<?php

namespace App\Generators;

interface GeneratorInterface
{
    public function __construct($args);

    public function setupCommandVariables(array $data): array;

    public function createFile(): array;
}
