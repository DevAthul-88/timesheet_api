<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\File;
use Spatie\LaravelMarkdown\MarkdownRenderer;

Route::get('/', function () {
    $markdownPath = resource_path('docs/README.md');

    if (File::exists($markdownPath)) {
        $markdownContent = File::get($markdownPath);
        $htmlContent = app(MarkdownRenderer::class)->toHtml($markdownContent);
    } else {
        $htmlContent = "<h1>No documentation found</h1>";
    }

    return view('welcome', ['content' => $htmlContent]);
});
