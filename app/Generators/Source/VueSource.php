<?php

namespace App\Generators\Source;

use App\Generators\Base;
use App\Generators\BaseRepository;
use App\Generators\VueGenerator;
use Facades\App\Generators\TokenReplacer;
use Illuminate\Support\Facades\File;

class VueSource extends VueGenerator
{
    protected string $generatorName = 'Source';
    protected string $plural = 'es';
}
