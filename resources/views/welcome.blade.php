<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Documentation</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/github-markdown-css/5.2.0/github-markdown.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600&display=swap" rel="stylesheet">
    <style>
        :root {
            --background-color: #f4f4f4;
            --text-color: #333;
            --accent-color: #0366d6;
            --code-background: #f6f8fa;
            --border-color: #e1e4e8;
            --pre-background: #f8f9fa;
        }

        html, body {
            height: 100%;
            margin: 0;
            line-height: 1.6;
            scroll-behavior: smooth;
        }

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Oxygen, Ubuntu, Cantarell, "Open Sans", "Helvetica Neue", sans-serif;
            background-color: var(--background-color);
            color: var(--text-color);
            display: flex;
            justify-content: center;
            align-items: flex-start;
            padding: 20px 10px;
            box-sizing: border-box;
            min-height: 100vh;
        }

        .markdown-body {
            max-width: 800px;
            width: 100%;
            background-color: white;
            padding: 40px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
            line-height: 1.7;
        }

        .markdown-body h1,
        .markdown-body h2 {
            border-bottom: 1px solid var(--border-color);
            padding-bottom: 10px;
            margin-top: 24px;
            margin-bottom: 16px;
        }

        .markdown-body h1 {
            font-size: 2.5em;
            color: var(--accent-color);
        }

        .markdown-body h2 {
            font-size: 1.8em;
        }

        .markdown-body code {
            background-color: var(--code-background);
            border-radius: 4px;
            padding: 2px 4px;
            font-size: 0.9em;
        }

        .markdown-body pre {
            background-color: var(--pre-background);
            border: 1px solid var(--border-color);
            border-radius: 6px;
            padding: 15px;
            overflow-x: auto;
            box-shadow: inset 0 1px 3px rgba(0,0,0,0.05);
            position: relative;
            z-index: 1;
        }

        .markdown-body pre code {
            background-color: transparent;
            padding: 0;
            font-size: 0.95em;
            color: #333;
        }

        @media screen and (max-width: 600px) {
            .markdown-body {
                padding: 20px;
                margin: 0;
                box-shadow: 0 0 15px rgba(0, 0, 0, 0.08);
                border-radius: 0;
            }
        }

        :target {
            scroll-margin-top: 20px;
        }
    </style>
           <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/prism/1.29.0/themes/prism-tomorrow.min.css">
</head>
<body>
    <article class="markdown-body">
        {!! $content !!}
    </article>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/prism/1.29.0/prism.min.js"></script>


</body>
</html>
