<?php
// Shared helpers for the ETS-Async exam module.
if (!isset($db) || !$db instanceof mysqli) {
    require_once '../src/connection.php';
    $db = $mysqli ?? null;
}
if (!$db instanceof mysqli) {
    http_response_code(500);
    exit('Database connection is unavailable.');
}
$db->set_charset('utf8mb4');
// Upgrade older installations safely for multi-topic exam selection.
$topicColumn = $db->query("SHOW COLUMNS FROM exams LIKE 'topic_ids_json'");
if ($topicColumn && $topicColumn->num_rows === 0) {
    if (!$db->query('ALTER TABLE exams ADD COLUMN topic_ids_json MEDIUMTEXT NULL AFTER topic_id')) {
        throw new RuntimeException('Could not upgrade exams table for topic selection: ' . $db->error);
    }
}
if ($topicColumn) $topicColumn->free();
if (session_status() !== PHP_SESSION_ACTIVE) session_start();
if (empty($_SESSION['exam_csrf'])) $_SESSION['exam_csrf'] = bin2hex(random_bytes(32));
$examCsrf = $_SESSION['exam_csrf'];
function ex_e($v): string
{
    return htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8');
}
function ex_rows(mysqli $db, string $sql, string $types = '', array $args = []): array
{
    $st = $db->prepare($sql);
    if (!$st) throw new Exception($db->error);
    if ($types !== '') $st->bind_param($types, ...$args);
    if (!$st->execute()) {
        $err = $st->error;
        $st->close();
        throw new Exception($err);
    }
    $r = $st->get_result()->fetch_all(MYSQLI_ASSOC);
    $st->close();
    return $r;
}
function ex_exec(mysqli $db, string $sql, string $types = '', array $args = []): void
{
    $st = $db->prepare($sql);
    if (!$st) throw new Exception($db->error);
    if ($types !== '') $st->bind_param($types, ...$args);
    if (!$st->execute()) {
        $err = $st->error;
        $st->close();
        throw new Exception($err);
    }
    $st->close();
}
function ex_code(mysqli $db): string
{
    $alphabet = 'ABCDEFGHJKLMNPQRSTUVWXYZ23456789';
    do {
        $code = '';
        for ($i = 0; $i < 8; $i++) $code .= $alphabet[random_int(0, strlen($alphabet) - 1)];
        $found = ex_rows($db, 'SELECT id FROM exams WHERE test_code=?', 's', [$code]);
    } while ($found);
    return $code;
}
function ex_media_url(string $type, string $url): string
{
    $p = parse_url($url);
    if (!$p) return '';
    $host = strtolower($p['host'] ?? '');
    $path = $p['path'] ?? '';
    if ($type === 'image' && in_array($host, ['drive.google.com', 'www.drive.google.com'], true)) {
        $id = '';
        if (preg_match('~/file/d/([A-Za-z0-9_-]+)~', $path, $m)) $id = $m[1];
        else {
            parse_str($p['query'] ?? '', $q);
            if (!empty($q['id'])) $id = (string)$q['id'];
        }
        return $id !== '' ? 'https://drive.google.com/file/d/' . rawurlencode($id) . '/preview' : '';
    }
    if ($type === 'youtube') {
        $id = '';
        if ($host === 'youtu.be') $id = explode('/', trim($path, '/'))[0] ?? '';
        else {
            parse_str($p['query'] ?? '', $q);
            if (str_starts_with($path, '/watch')) $id = (string)($q['v'] ?? '');
            elseif (preg_match('~/(?:embed|shorts)/([A-Za-z0-9_-]+)~', $path, $m)) $id = $m[1];
        }
        return preg_match('/^[A-Za-z0-9_-]{11}$/', $id) ? 'https://www.youtube-nocookie.com/embed/' . rawurlencode($id) : '';
    }
    return '';
}
function ex_render_media(mysqli $db, int $qid): void
{
    $items = ex_rows($db, 'SELECT media_type,media_url FROM question_media WHERE question_id=? ORDER BY display_order,id', 'i', [$qid]);
    if (!$items) return;
    echo '<div class="exam-media">';
    foreach ($items as $it) {
        $src = ex_media_url((string)$it['media_type'], (string)$it['media_url']);
        if ($src === '') continue;
        echo '<div class="exam-media-item"><div class="ratio ratio-16x9"><iframe src="' . ex_e($src) . '" title="Question media" loading="lazy" allow="autoplay; encrypted-media; picture-in-picture" allowfullscreen></iframe></div></div>';
    }
    echo '</div>';
}
function ex_rich(string $html): string
{
    $html = strip_tags($html, '<p><br><strong><b><em><i><u><s><sub><sup><ol><ul><li><blockquote><pre><code><span><div><h1><h2><h3><h4><a>');
    $html = preg_replace('/\s+on[a-z]+\s*=\s*("[^"]*"|\'[^\']*\'|[^\s>]+)/i', '', $html);
    $html = preg_replace('/(href|src)\s*=\s*(["\'])\s*javascript:[^"\']*\2/i', '$1="#"', $html);
    return $html;
}
function ex_page_start(string $title, bool $admin = false): void
{
    if ($admin) { ?>
        <!DOCTYPE html>
        <html lang="en">
        <?php include './globals/head.php'; ?>
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/katex@0.19.0/dist/katex.min.css">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/highlight.js/11.9.0/styles/github-dark.min.css">
        <style>
            .exam-admin-wrap {
                max-width: 1400px;
                margin: 0 auto
            }

            .exam-wrap {
                max-width: 1000px;
                margin: 28px auto;
                padding: 0 15px
            }

            .exam-card {
                background: #fff;
                border: 1px solid #e5e7eb;
                border-radius: 14px;
                box-shadow: 0 2px 8px rgba(15, 23, 42, .04);
                margin-bottom: 22px;
                overflow: hidden
            }

            .exam-card-head {
                padding: 18px 22px;
                background: #f9fafb;
                border-bottom: 1px solid #e5e7eb
            }

            .exam-card-body {
                padding: 22px
            }

            .form-label {
                font-weight: 650;
                color: #374151
            }

            .question-card {
                padding: 20px;
                border: 1px solid #e3e8f0;
                border-radius: 12px;
                margin-bottom: 18px;
                background: #fff
            }

            .choice-row {
                display: flex;
                gap: 10px;
                padding: 12px;
                border: 1px solid #e2e7ef;
                border-radius: 9px;
                margin: 8px 0;
                cursor: pointer
            }

            .choice-row:hover {
                background: #f8faff;
                border-color: #a5b4fc
            }

            .exam-media {
                display: grid;
                grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
                gap: 12px;
                margin: 14px 0
            }

            .exam-media-item {
                border: 1px solid #e5e7eb;
                border-radius: 10px;
                overflow: hidden
            }

            .text-rich img {
                max-width: 100%;
                height: auto
            }

            .text-rich table {
                max-width: 100%;
                border-collapse: collapse
            }

            .text-rich td,
            .text-rich th {
                border: 1px solid #ddd;
                padding: 5px
            }

            .text-rich pre,
            .text-rich pre.ql-syntax {
                display: block;
                max-width: 100%;
                overflow: auto;
                white-space: pre;
                tab-size: 4;
                background: #111827;
                color: #e5e7eb;
                border: 1px solid #374151;
                border-radius: 10px;
                padding: 16px;
                margin: 14px 0;
                font: 13px/1.65 ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, "Liberation Mono", monospace;
                font-weight: 400
            }

            .text-rich pre code,
            .text-rich code {
                font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, "Liberation Mono", monospace;
                font-weight: 400
            }

            .text-rich :not(pre)>code {
                background: #eef2f7;
                color: #be123c;
                padding: .15em .35em;
                border-radius: 4px;
                font-size: .92em
            }

            .text-rich pre code {
                background: transparent !important;
                color: inherit !important;
                padding: 0 !important;
                border-radius: 0 !important;
                white-space: pre;
                font-size: inherit
            }

            .text-rich pre code.hljs {
                padding: 0 !important
            }

            .text-rich .ql-syntax {
                font-weight: 400 !important;
                line-height: 1.65
            }

            .text-rich pre * {
                font-weight: inherit
            }

            .topic-checklist {
                display: grid;
                grid-template-columns: repeat(auto-fill, minmax(230px, 1fr));
                gap: 8px;
                max-height: 270px;
                overflow: auto;
                padding: 12px;
                border: 1px solid #e5e7eb;
                border-radius: 10px;
                background: #fff
            }

            .topic-check {
                display: flex;
                align-items: flex-start;
                gap: 9px;
                padding: 9px 10px;
                border-radius: 8px;
                background: #f9fafb;
                cursor: pointer
            }

            .topic-check[hidden] {
                display: none !important
            }

            .topic-check input {
                margin-top: 4px
            }

            .editor-toolbar {
                display: flex;
                gap: 6px;
                flex-wrap: wrap;
                margin-bottom: 8px
            }

            .editor-toolbar button {
                min-width: 36px
            }

            .exam-instructions-editor {
                min-height: 150px;
                background: #fff;
                border: 1px solid #ced4da;
                border-radius: 8px;
                padding: 12px
            }

            .exam-instructions-editor:focus {
                outline: 0;
                border-color: #6366f1;
                box-shadow: 0 0 0 3px rgba(99, 102, 241, .12)
            }

            @media(max-width:700px) {
                .exam-card-body {
                    padding: 16px
                }

                .exam-card-head {
                    padding: 16px
                }

                .question-card {
                    padding: 14px
                }
            }
        </style>

        <body>
            <?php include './globals/sidebar.php'; ?>
            <?php include './globals/topbar.php'; ?>
            <main class="main-content">
                <div class="content-wrapper exam-admin-wrap">
                <?php } else { ?>
                    <!doctype html>
                    <html lang="en">

                    <head>
                        <meta charset="utf-8">
                        <meta name="viewport" content="width=device-width,initial-scale=1">
                        <title><?= ex_e($title) ?></title>
                        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
                        <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
                        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/katex@0.19.0/dist/katex.min.css">
                        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/highlight.js/11.9.0/styles/github-dark.min.css">
                        <style>
                            body {
                                background: #f5f7fb;
                                color: #172033
                            }

                            .exam-wrap {
                                max-width: 1000px;
                                margin: 28px auto;
                                padding: 0 15px
                            }

                            .exam-card {
                                background: #fff;
                                border: 1px solid #e4e8f0;
                                border-radius: 15px;
                                box-shadow: 0 5px 20px #15294b0a;
                                margin-bottom: 18px;
                                overflow: hidden
                            }

                            .exam-card-head {
                                padding: 18px 22px;
                                background: #f9fafc;
                                border-bottom: 1px solid #e8ebf1
                            }

                            .exam-card-body {
                                padding: 22px
                            }

                            .form-label {
                                font-weight: 650
                            }

                            .question-card {
                                padding: 20px;
                                border: 1px solid #e3e8f0;
                                border-radius: 12px;
                                margin-bottom: 18px;
                                background: #fff
                            }

                            .choice-row {
                                display: flex;
                                gap: 10px;
                                padding: 12px;
                                border: 1px solid #e2e7ef;
                                border-radius: 9px;
                                margin: 8px 0;
                                cursor: pointer
                            }

                            .choice-row:hover {
                                background: #f8faff;
                                border-color: #a5b4fc
                            }

                            .exam-media {
                                display: grid;
                                grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
                                gap: 12px;
                                margin: 14px 0
                            }

                            .exam-media-item {
                                border: 1px solid #e5e7eb;
                                border-radius: 10px;
                                overflow: hidden
                            }

                            .timer-pill {
                                position: sticky;
                                top: 10px;
                                z-index: 10;
                                background: #111827;
                                color: #fff;
                                padding: 10px 15px;
                                border-radius: 999px;
                                display: inline-block;
                                font-weight: 750
                            }

                            .text-rich img {
                                max-width: 100%;
                                height: auto
                            }

                            .text-rich table {
                                max-width: 100%;
                                border-collapse: collapse
                            }

                            .text-rich td,
                            .text-rich th {
                                border: 1px solid #ddd;
                                padding: 5px
                            }

                            .text-rich pre,
                            .text-rich pre.ql-syntax {
                                display: block;
                                max-width: 100%;
                                overflow: auto;
                                white-space: pre;
                                tab-size: 4;
                                background: #111827;
                                color: #e5e7eb;
                                border: 1px solid #374151;
                                border-radius: 10px;
                                padding: 16px;
                                margin: 14px 0;
                                font: 13px/1.65 ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, "Liberation Mono", monospace;
                                font-weight: 400
                            }

                            .text-rich pre code,
                            .text-rich code {
                                font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, "Liberation Mono", monospace;
                                font-weight: 400
                            }

                            .text-rich :not(pre)>code {
                                background: #eef2f7;
                                color: #be123c;
                                padding: .15em .35em;
                                border-radius: 4px;
                                font-size: .92em
                            }

                            .text-rich pre code {
                                background: transparent !important;
                                color: inherit !important;
                                padding: 0 !important;
                                border-radius: 0 !important;
                                white-space: pre;
                                font-size: inherit
                            }

                            .text-rich pre code.hljs {
                                padding: 0 !important
                            }

                            .text-rich .ql-syntax {
                                font-weight: 400 !important;
                                line-height: 1.65
                            }

                            .text-rich pre * {
                                font-weight: inherit
                            }

                            @media(max-width:600px) {
                                .exam-card-body {
                                    padding: 16px
                                }

                                .exam-card-head {
                                    padding: 16px
                                }

                                .question-card {
                                    padding: 14px
                                }
                            }
                        </style>
                    </head>

                    <body>
                        <main class="exam-wrap">
                        <?php }
                }
                function ex_page_end(bool $admin = false): void
                {
                    if ($admin) { ?>
                </div>
            </main>
            <script src="https://cdnjs.cloudflare.com/ajax/libs/highlight.js/11.9.0/highlight.min.js"></script>
            <script src="https://cdn.jsdelivr.net/npm/katex@0.19.0/dist/katex.min.js"></script>
            <script defer src="https://cdn.jsdelivr.net/npm/katex@0.19.0/dist/contrib/auto-render.min.js"></script>
            <script>
                <?php echo ex_math_script(); ?>
            </script>
            <?php include './globals/scripts.php'; ?>
        </body>

        </html>
    <?php } else { ?>
        </main>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/highlight.js/11.9.0/highlight.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/katex@0.19.0/dist/katex.min.js"></script>
        <script defer src="https://cdn.jsdelivr.net/npm/katex@0.19.0/dist/contrib/auto-render.min.js"></script>
        <script>
            <?php echo ex_math_script(); ?>
        </script>
        </body>

        </html>
<?php }
                }
                function ex_math_script(): string
                {
                    return <<<'JS'
document.addEventListener('DOMContentLoaded',function(){
 if(window.hljs){document.querySelectorAll('.text-rich pre code').forEach(function(el){try{window.hljs.highlightElement(el);}catch(e){}});}
 if(window.hljs){document.querySelectorAll('.text-rich pre.ql-syntax').forEach(function(el){if(!el.querySelector('code')){var code=document.createElement('code');code.className='language-plaintext';while(el.firstChild)code.appendChild(el.firstChild);el.appendChild(code);}try{window.hljs.highlightElement(el.querySelector('code'));}catch(e){}});}
 document.querySelectorAll('.text-rich .ql-formula').forEach(function(el){var latex=el.getAttribute('data-value');if(latex&&window.katex){try{window.katex.render(latex,el,{throwOnError:false,displayMode:false});}catch(e){}}});
 if(typeof window.renderMathInElement==='function'){document.querySelectorAll('.text-rich').forEach(function(el){try{window.renderMathInElement(el,{delimiters:[{left:'$$',right:'$$',display:true},{left:'\\[',right:'\\]',display:true},{left:'\\(',right:'\\)',display:false},{left:'$',right:'$',display:false}],throwOnError:false,ignoredTags:['script','noscript','style','textarea','pre','code'],ignoredClasses:['ql-formula','katex','tex2jax_ignore']});}catch(e){}});}
});
JS;
                }
