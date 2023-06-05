<?php

namespace Tests\Feature;

use App\Models\Transformer;
use App\Transformer\Types\PdfTransformer;
use Illuminate\Support\Facades\File;
use Tests\TestCase;

class PdfTransformerTest extends TestCase
{
    use SharedSetupForPdfFile;

    protected function tearDown(): void
    {
        if (! File::exists($this->document->pathToFile())) {
            File::delete($this->document->pathToFile());
        }
        parent::tearDown(); // TODO: Change the autogenerated stub
    }

    public function test_gets_data_from_pdf()
    {
        $this->webFileDownloadSetup();
        $transformerModel = Transformer::factory()->create();
        $this->assertDatabaseCount('document_chunks', 0);
        $transformer = new PdfTransformer($this->document);
        $transformer->handle($transformerModel);
        $this->assertDatabaseCount('document_chunks', 10);

    }

    public function test_does_not_repeat()
    {
        $this->webFileDownloadSetup();
        $transformerModel = Transformer::factory()->create();
        $this->assertDatabaseCount('document_chunks', 0);
        $transformer = new PdfTransformer($this->document);
        $transformer->handle($transformerModel);
        $this->assertDatabaseCount('document_chunks', 10);
        $transformer->handle($transformerModel);
        $this->assertDatabaseCount('document_chunks', 10);
    }
}
