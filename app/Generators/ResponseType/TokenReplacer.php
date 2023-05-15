<?php

namespace App\Generators\ResponseType;

class TokenReplacer
{
    public function handle(GeneratorRepository $generatorRepository, string $content): string
    {
        return str($content)
            ->replace([
                '[RESOURCE_KEY]',
                '[RESOURCE_NAME]',
                '[RESOURCE_CLASS_NAME]',
                '[RESOURCE_DESCRIPTION]',
            ], [
                $generatorRepository->getKey(),
                $generatorRepository->name,
                str($generatorRepository->name)->studly(),
                $generatorRepository->description,
            ])->toString();
    }
}
