<?php
/* =========================================================
   C PROGRAMMING COMPREHENSIVE REVIEW
   ETS-Async Learning Portal
   Topics 45–58 | Review Notes and Worked Examples
   ========================================================= */

session_start();

if (
    !isset($_SESSION["logged_in"]) ||
    $_SESSION["logged_in"] !== true ||
    !isset($_SESSION["user"]) ||
    ($_SESSION["user"]["access"] ?? "") !== "student"
) {
    header("Location: ../login.php");
    exit;
}

$user = $_SESSION["user"];
require_once "../src/connection.php";

$firstName = $user["first_name"] ?? "";
$lastName = $user["last_name"] ?? "";
$middleInitial = $user["middle_initial"] ?? "";
$extensionName = $user["extension_name"] ?? "";
$fullName = trim($firstName . " " .
    ($middleInitial !== "" ? $middleInitial . ". " : "") .
    $lastName .
    ($extensionName !== "" ? " " . $extensionName : ""));
$initials = strtoupper(substr($firstName, 0, 1) . substr($lastName, 0, 1));
?>
<!DOCTYPE html>
<html lang="en">
<?php include 'globals/head.php'; ?>

<body>
    <?php include 'globals/sidebar.php'; ?>
    <?php include 'globals/topbar.php'; ?>

    <main class="main-content">
        <div class="content-wrapper">
            <div class="page-header">
                <div>
                    <h2>C Programming Review: Basic Syntax to break and continue</h2>
                    <p>A focused review of Topics 45–58, from Basic Syntax and Statements through break and continue, with examples, output explanations, and references.</p>
                </div>
            </div>


            <div class="theory-card">
                <div class="theory-header">
                    <div>
                        <h4>How to Use This Review</h4>
                        <p>Start at Topic 1 and proceed in order. Each topic includes a definition, example, and explanation to support question-bank review.</p>
                    </div>
                    <div class="theory-badge"><i class="bi bi-journal-text"></i> Review Guide</div>
                </div>
                <div class="theory-body">
                    <p>This guide covers the specific C programming topics you provided, beginning with Basic Syntax and Statements and ending with break and continue statements. For output-tracing questions, follow statements in order and record each variable's value.</p>
                    <div class="concept-box"><strong>Important</strong><span>Examples are illustrative and may be snippets rather than complete standalone programs. Add the appropriate headers and place statements inside a valid function when compiling. Outputs are only stated where they can be determined from the shown code and stated assumptions.</span></div>
                    <div class="topic-index">
                        <a href="#topic-45">45. Basic Syntax and Statements</a><a href="#topic-46">46. Variables and Constants</a><a href="#topic-47">47. Data Types in C</a><a href="#topic-48">48. Keywords and Identifiers</a><a href="#topic-49">49. Input and Output: printf and scanf</a><a href="#topic-50">50. Operators and Expressions</a><a href="#topic-51">51. Type Conversion and Type Casting</a><a href="#topic-52">52. Comments and Code Documentation</a><a href="#topic-53">53. Escape Sequences and Format Specifiers</a><a href="#topic-54">54. Decision-Making Statements</a><a href="#topic-55">55. Nested Conditional Statements</a><a href="#topic-56">56. Loops: for, while, and do-while</a><a href="#topic-57">57. Nested Loops</a><a href="#topic-58">58. break and continue Statements</a>
                    </div>
                </div>
            </div>

            <div class="theory-card" id="topic-45">
                <div class="theory-header">
                    <div>
                        <h4>45. Basic Syntax and Statements</h4>
                        <p>Concepts, syntax, and worked example</p>
                    </div>
                    <div class="statement-number">TOPIC 45</div>
                </div>
                <div class="theory-body">
                    <div class="definition-box"><strong>Explanation</strong><span>A C program uses declarations and statements inside functions. Most statements end in semicolons, and braces group blocks.</span></div>
                    <div class="section-label">Example</div>
                    <div class="code-box">
                        <pre><code>int samples = 3;
printf("Samples: %d\\n", samples);</code></pre>
                    </div>
                    <div class="result-box"><strong>What to remember</strong><span>The output is Samples: 3. This topic reinforces the basic program structure.</span></div>
                </div>
            </div>

            <div class="theory-card" id="topic-46">
                <div class="theory-header">
                    <div>
                        <h4>46. Variables and Constants</h4>
                        <p>Concepts, syntax, and worked example</p>
                    </div>
                    <div class="statement-number">TOPIC 46</div>
                </div>
                <div class="theory-body">
                    <div class="definition-box"><strong>Explanation</strong><span>Variables hold changeable values; const objects are not modifiable through that identifier. Choose meaningful names and initialize local variables before reading them.</span></div>
                    <div class="section-label">Example</div>
                    <div class="code-box">
                        <pre><code>int count = 2;
count++;
printf("%d\\n", count);</code></pre>
                    </div>
                    <div class="result-box"><strong>What to remember</strong><span>The output is 3 after the increment.</span></div>
                </div>
            </div>

            <div class="theory-card" id="topic-47">
                <div class="theory-header">
                    <div>
                        <h4>47. Data Types in C</h4>
                        <p>Concepts, syntax, and worked example</p>
                    </div>
                    <div class="statement-number">TOPIC 47</div>
                </div>
                <div class="theory-body">
                    <div class="definition-box"><strong>Explanation</strong><span>Choose integer, character, floating-point, or other suitable types based on the values and operations required. Exact sizes depend on the implementation.</span></div>
                    <div class="section-label">Example</div>
                    <div class="code-box">
                        <pre><code>double resistance = 220.0;
printf("%.0f ohms\\n", resistance);</code></pre>
                    </div>
                    <div class="result-box"><strong>What to remember</strong><span>The output is 220 ohms.</span></div>
                </div>
            </div>

            <div class="theory-card" id="topic-48">
                <div class="theory-header">
                    <div>
                        <h4>48. Keywords and Identifiers</h4>
                        <p>Concepts, syntax, and worked example</p>
                    </div>
                    <div class="statement-number">TOPIC 48</div>
                </div>
                <div class="theory-body">
                    <div class="definition-box"><strong>Explanation</strong><span>Keywords are reserved by the language; identifiers are programmer-chosen names that follow C naming rules.</span></div>
                    <div class="section-label">Example</div>
                    <div class="code-box">
                        <pre><code>int sensor_count = 4;
printf("%d\\n", sensor_count);</code></pre>
                    </div>
                    <div class="result-box"><strong>What to remember</strong><span>The output is 4. sensor_count is a valid identifier.</span></div>
                </div>
            </div>

            <div class="theory-card" id="topic-49">
                <div class="theory-header">
                    <div>
                        <h4>49. Input and Output: printf and scanf</h4>
                        <p>Concepts, syntax, and worked example</p>
                    </div>
                    <div class="statement-number">TOPIC 49</div>
                </div>
                <div class="theory-body">
                    <div class="definition-box"><strong>Explanation</strong><span>Use format specifiers that match the data type. In scanf, pass addresses for scalar variables and check the return count.</span></div>
                    <div class="section-label">Example</div>
                    <div class="code-box">
                        <pre><code>int n;
if (scanf("%d", &amp;n) == 1) printf("%d\\n", n);</code></pre>
                    </div>
                    <div class="result-box"><strong>What to remember</strong><span>For valid input 7, the program prints 7.</span></div>
                </div>
            </div>

            <div class="theory-card" id="topic-50">
                <div class="theory-header">
                    <div>
                        <h4>50. Operators and Expressions</h4>
                        <p>Concepts, syntax, and worked example</p>
                    </div>
                    <div class="statement-number">TOPIC 50</div>
                </div>
                <div class="theory-body">
                    <div class="definition-box"><strong>Explanation</strong><span>Arithmetic, relational, logical, assignment, and increment/decrement operators form expressions. Parentheses can make precedence clearer.</span></div>
                    <div class="section-label">Example</div>
                    <div class="code-box">
                        <pre><code>int x = 3;
x *= 4;
printf("%d\\n", x);</code></pre>
                    </div>
                    <div class="result-box"><strong>What to remember</strong><span>The output is 12.</span></div>
                </div>
            </div>

            <div class="theory-card" id="topic-51">
                <div class="theory-header">
                    <div>
                        <h4>51. Type Conversion and Type Casting</h4>
                        <p>Concepts, syntax, and worked example</p>
                    </div>
                    <div class="statement-number">TOPIC 51</div>
                </div>
                <div class="theory-body">
                    <div class="definition-box"><strong>Explanation</strong><span>Implicit conversions follow C's type rules; explicit casts request a conversion.</span></div>
                    <div class="section-label">Example</div>
                    <div class="code-box">
                        <pre><code>printf("%.1f\\n", (double)5 / 2);</code></pre>
                    </div>
                    <div class="result-box"><strong>What to remember</strong><span>The output is 2.5 because the cast makes the division floating-point.</span></div>
                </div>
            </div>

            <div class="theory-card" id="topic-52">
                <div class="theory-header">
                    <div>
                        <h4>52. Comments and Code Documentation</h4>
                        <p>Concepts, syntax, and worked example</p>
                    </div>
                    <div class="statement-number">TOPIC 52</div>
                </div>
                <div class="theory-body">
                    <div class="definition-box"><strong>Explanation</strong><span>Use comments to record units, assumptions, and reasoning; avoid stale or misleading comments.</span></div>
                    <div class="section-label">Example</div>
                    <div class="code-box">
                        <pre><code>/* current measured in amperes */
float current = 2.0f;</code></pre>
                    </div>
                    <div class="result-box"><strong>What to remember</strong><span>The comment helps readers understand the variable's unit and has no runtime effect.</span></div>
                </div>
            </div>

            <div class="theory-card" id="topic-53">
                <div class="theory-header">
                    <div>
                        <h4>53. Escape Sequences and Format Specifiers</h4>
                        <p>Concepts, syntax, and worked example</p>
                    </div>
                    <div class="statement-number">TOPIC 53</div>
                </div>
                <div class="theory-body">
                    <div class="definition-box"><strong>Explanation</strong><span>Escape sequences such as \n and \t control displayed text; format specifiers control how values are printed.</span></div>
                    <div class="section-label">Example</div>
                    <div class="code-box">
                        <pre><code>printf("Ready\n100%%\n");</code></pre>
                    </div>
                    <div class="result-box"><strong>What to remember</strong><span>The output is Ready on one line and 100% on the next.</span></div>
                </div>
            </div>

            <div class="theory-card" id="topic-54">
                <div class="theory-header">
                    <div>
                        <h4>54. Decision-Making Statements</h4>
                        <p>Concepts, syntax, and worked example</p>
                    </div>
                    <div class="statement-number">TOPIC 54</div>
                </div>
                <div class="theory-body">
                    <div class="definition-box"><strong>Explanation</strong><span>if, if-else, else-if, and switch choose execution paths. Conditions are false when zero and true when nonzero.</span></div>
                    <div class="section-label">Example</div>
                    <div class="code-box">
                        <pre><code>int voltage = 13;
if (voltage &gt; 12) puts("HIGH");
else puts("OK");</code></pre>
                    </div>
                    <div class="result-box"><strong>What to remember</strong><span>The output is HIGH.</span></div>
                </div>
            </div>

            <div class="theory-card" id="topic-55">
                <div class="theory-header">
                    <div>
                        <h4>55. Nested Conditional Statements</h4>
                        <p>Concepts, syntax, and worked example</p>
                    </div>
                    <div class="statement-number">TOPIC 55</div>
                </div>
                <div class="theory-body">
                    <div class="definition-box"><strong>Explanation</strong><span>Nested conditionals evaluate a second decision within the branch of a first decision.</span></div>
                    <div class="section-label">Example</div>
                    <div class="code-box">
                        <pre><code>int current = 8, temp = 60;
if (current &lt;= 10) {
    if (temp &lt;= 80) puts("SAFE");
}</code></pre>
                    </div>
                    <div class="result-box"><strong>What to remember</strong><span>The output is SAFE because both conditions are true.</span></div>
                </div>
            </div>

            <div class="theory-card" id="topic-56">
                <div class="theory-header">
                    <div>
                        <h4>56. Loops: for, while, and do-while</h4>
                        <p>Concepts, syntax, and worked example</p>
                    </div>
                    <div class="statement-number">TOPIC 56</div>
                </div>
                <div class="theory-body">
                    <div class="definition-box"><strong>Explanation</strong><span>for and while test before their body; do-while tests after the body. Use a clear update to avoid infinite loops.</span></div>
                    <div class="section-label">Example</div>
                    <div class="code-box">
                        <pre><code>int i = 1;
while (i &lt;= 3) { printf("%d ", i); i++; }</code></pre>
                    </div>
                    <div class="result-box"><strong>What to remember</strong><span>The output is 1 2 3.</span></div>
                </div>
            </div>

            <div class="theory-card" id="topic-57">
                <div class="theory-header">
                    <div>
                        <h4>57. Nested Loops</h4>
                        <p>Concepts, syntax, and worked example</p>
                    </div>
                    <div class="statement-number">TOPIC 57</div>
                </div>
                <div class="theory-body">
                    <div class="definition-box"><strong>Explanation</strong><span>The inner loop generally runs through its iterations for each outer-loop iteration. Track both loop variables to determine the exact number and order of outputs.</span></div>
                    <div class="section-label">Example</div>
                    <div class="code-box">
                        <pre><code>for (int i = 1; i &lt;= 2; i++)
    for (int j = 1; j &lt;= 2; j++)
        printf("%d%d ", i, j);</code></pre>
                    </div>
                    <div class="result-box"><strong>What to remember</strong><span>The output is 11 12 21 22.</span></div>
                </div>
            </div>

            <div class="theory-card" id="topic-58">
                <div class="theory-header">
                    <div>
                        <h4>58. break and continue Statements</h4>
                        <p>Concepts, syntax, and worked example</p>
                    </div>
                    <div class="statement-number">TOPIC 58</div>
                </div>
                <div class="theory-body">
                    <div class="definition-box"><strong>Explanation</strong><span>break exits the nearest loop or switch; continue skips the remaining body statements in the current iteration and proceeds to the next iteration.</span></div>
                    <div class="section-label">Example</div>
                    <div class="code-box">
                        <pre><code>for (int i = 1; i &lt;= 4; i++) {
    if (i == 2) continue;
    printf("%d ", i);
}</code></pre>
                    </div>
                    <div class="result-box"><strong>What to remember</strong><span>The output is 1 3 4 because the iteration where i equals 2 skips the print statement.</span></div>
                </div>
            </div>

            <div class="reference-note">
                <div class="reference-note-title"><i class="bi bi-book"></i> References</div>
                <div class="theory-body">
                    <p>Consult these sources for the C language rules, standard library behavior, and further examples.</p>
                    <ol class="reference-list">
                        <li>ISO/IEC. <em>ISO/IEC 9899:2018 — Programming Languages — C</em> (C17). International Organization for Standardization, 2018.</li>
                        <li>cppreference.com. “C language.” <a href="https://en.cppreference.com/w/c/language" target="_blank" rel="noopener noreferrer">https://en.cppreference.com/w/c/language</a></li>
                        <li>cppreference.com. “C input/output.” <a href="https://en.cppreference.com/w/c/io" target="_blank" rel="noopener noreferrer">https://en.cppreference.com/w/c/io</a></li>
                        <li>GNU Project. <em>The GNU C Reference Manual</em>. <a href="https://www.gnu.org/software/gnu-c-manual/" target="_blank" rel="noopener noreferrer">https://www.gnu.org/software/gnu-c-manual/</a></li>
                        <li>Beej's Guide to C Programming. <a href="https://beej.us/guide/bgc/" target="_blank" rel="noopener noreferrer">https://beej.us/guide/bgc/</a></li>
                    </ol>
                    <p class="reference-disclaimer">This is a study aid, not a replacement for the C standard. Compiler behavior, implementation-defined details, and undefined behavior should be checked against authoritative documentation.</p>
                </div>
            </div>
        </div>
    </main>

    <style>
        :root {
            --theory-bg: #fff;
            --theory-border: #e4e7ec;
            --theory-text: #172033;
            --theory-muted: #667085;
            --theory-secondary: #475467;
            --theory-hover: #f8fafc;
            --theory-blue: #2563eb;
            --theory-green: #16a34a;
            --theory-header: #f8fafc
        }

        html[data-theme="dark"] {
            --theory-bg: #151922;
            --theory-border: #2a3140;
            --theory-text: #f1f5f9;
            --theory-muted: #a8b0bf;
            --theory-secondary: #c2c9d3;
            --theory-hover: #1c2230;
            --theory-blue: #60a5fa;
            --theory-green: #4ade80;
            --theory-header: #1c2230
        }

        .theory-card,
        .reference-note {
            margin-bottom: 16px;
            background: var(--theory-bg);
            border: 1px solid var(--theory-border);
            border-radius: 10px;
            overflow: hidden
        }

        .theory-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 15px;
            padding: 17px 19px;
            border-bottom: 1px solid var(--theory-border)
        }

        .theory-header h4 {
            margin: 0 0 4px;
            color: var(--theory-text);
            font-size: .92rem;
            font-weight: 700
        }

        .theory-header p {
            margin: 0;
            color: var(--theory-muted);
            font-size: .68rem
        }

        .theory-badge,
        .statement-number {
            padding: 6px 9px;
            border-radius: 5px;
            background: var(--theory-hover);
            color: var(--theory-blue);
            font-size: .62rem;
            font-weight: 800;
            white-space: nowrap
        }

        .theory-badge {
            display: flex;
            align-items: center;
            gap: 5px
        }

        .theory-body {
            padding: 19px
        }

        .theory-body p {
            margin: 0 0 13px;
            color: var(--theory-secondary);
            font-family: "Times New Roman", serif;
            font-size: .82rem;
            line-height: 1.7
        }

        .theory-body strong {
            color: var(--theory-text)
        }

        .theory-body ul,
        .theory-body ol {
            color: var(--theory-secondary);
            font-family: "Times New Roman", serif;
            font-size: .79rem;
            line-height: 1.7;
            padding-left: 22px
        }

        .concept-box,
        .definition-box {
            margin: 0 0 18px;
            padding: 14px 16px;
            border: 1px solid var(--theory-border);
            border-radius: 7px;
            background: var(--theory-hover)
        }

        .concept-box {
            display: flex;
            flex-direction: column;
            gap: 5px;
            border-left: 4px solid var(--theory-blue)
        }

        .concept-box strong,
        .definition-box strong {
            display: block;
            margin-bottom: 6px;
            color: var(--theory-blue);
            font-size: .72rem
        }

        .concept-box span,
        .definition-box span {
            color: var(--theory-secondary);
            font-family: "Times New Roman", serif;
            font-size: .79rem;
            line-height: 1.7
        }

        .section-label {
            margin: 17px 0 8px;
            color: var(--theory-text);
            font-size: .73rem;
            font-weight: 700
        }

        .code-box {
            margin: 8px 0 14px;
            padding: 16px 18px;
            border: 1px solid var(--theory-border);
            border-radius: 7px;
            background: var(--theory-hover);
            overflow-x: auto
        }

        .code-box pre {
            margin: 0;
            color: var(--theory-text);
            font-family: "Courier New", monospace;
            font-size: .75rem;
            line-height: 1.7;
            white-space: pre
        }

        .code-box code {
            font-family: "Courier New", monospace
        }

        .result-box {
            display: flex;
            flex-direction: column;
            gap: 5px;
            margin-top: 14px;
            padding: 13px 16px;
            border-left: 4px solid var(--theory-green);
            border-radius: 5px;
            background: rgba(22, 163, 74, .07)
        }

        .result-box strong {
            color: var(--theory-green);
            font-size: .72rem
        }

        .result-box span {
            color: var(--theory-secondary);
            font-family: "Times New Roman", serif;
            font-size: .79rem;
            line-height: 1.7
        }

        .comparison-wrapper {
            overflow-x: auto
        }

        .comparison-table {
            width: 100%;
            border-collapse: collapse
        }

        .comparison-table th {
            padding: 11px 15px;
            background: var(--theory-header);
            border-bottom: 1px solid var(--theory-border);
            color: var(--theory-secondary);
            font-size: .67rem;
            text-align: left
        }

        .comparison-table td {
            padding: 12px 15px;
            border-bottom: 1px solid var(--theory-border);
            color: var(--theory-text);
            font-family: "Times New Roman", serif;
            font-size: .76rem;
            line-height: 1.5;
            vertical-align: top
        }

        .comparison-table td:first-child {
            min-width: 120px;
            font-family: "Courier New", monospace;
            font-weight: 700
        }

        .comparison-table tr:hover td {
            background: var(--theory-hover)
        }

        .topic-index {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 8px;
            margin-top: 16px
        }

        .topic-index a {
            display: block;
            padding: 10px 12px;
            border: 1px solid var(--theory-border);
            border-radius: 6px;
            color: var(--theory-blue);
            background: var(--theory-hover);
            font-size: .73rem;
            text-decoration: none
        }

        .topic-index a:hover {
            text-decoration: underline
        }

        .nested-flow {
            margin-top: 10px
        }

        .nested-step {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 13px 15px;
            border: 1px solid var(--theory-border);
            border-radius: 7px;
            background: var(--theory-hover)
        }

        .nested-number {
            display: flex;
            align-items: center;
            justify-content: center;
            min-width: 28px;
            height: 28px;
            border-radius: 50%;
            background: var(--theory-blue);
            color: #fff;
            font-size: .67rem;
            font-weight: 800
        }

        .nested-step strong {
            display: block;
            margin-bottom: 3px;
            color: var(--theory-text);
            font-size: .72rem
        }

        .nested-step span {
            color: var(--theory-muted);
            font-family: "Times New Roman", serif;
            font-size: .76rem;
            line-height: 1.5
        }

        .nested-arrow {
            padding: 5px 0;
            color: var(--theory-muted);
            text-align: center
        }

        .success-step {
            border-color: var(--theory-green);
            background: rgba(22, 163, 74, .07)
        }

        .success-step .nested-number {
            background: var(--theory-green)
        }

        .reference-note-title {
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 14px 17px;
            border-bottom: 1px solid var(--theory-border);
            color: var(--theory-text);
            font-size: .8rem;
            font-weight: 700
        }

        .reference-note-title i {
            color: var(--theory-blue)
        }

        .reference-list li {
            margin-bottom: 9px;
            overflow-wrap: anywhere
        }

        .reference-list a {
            color: var(--theory-blue)
        }

        .reference-disclaimer {
            padding-top: 10px;
            border-top: 1px solid var(--theory-border);
            font-size: .75rem !important
        }

        @media(max-width:850px) {
            .topic-index {
                grid-template-columns: repeat(2, minmax(0, 1fr))
            }
        }

        @media(max-width:600px) {
            .theory-header {
                align-items: flex-start;
                flex-direction: column
            }

            .topic-index {
                grid-template-columns: 1fr
            }

            .theory-body {
                padding: 15px
            }

            .code-box {
                padding: 12px
            }

            .code-box pre {
                font-size: .68rem
            }
        }
    </style>

    <?php include 'globals/scripts.php'; ?>
</body>

</html>