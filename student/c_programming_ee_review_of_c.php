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
                    <h2>C Programming: Complete Review Guide</h2>
                    <p>A complete review starting from Introduction to C Programming and covering all topics discussed, with examples, output explanations, and references.</p>
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
                    <p>This guide begins with the introduction to C and proceeds through language fundamentals, functions, arrays, strings, pointers, memory, files, preprocessor features, and control flow. For output-tracing questions, follow statements in order and record each variable's value.</p>
                    <div class="concept-box"><strong>Important</strong><span>Examples are illustrative and may be snippets rather than complete standalone programs. Add the appropriate headers and place statements inside a valid function when compiling. Outputs are only stated where they can be determined from the shown code and stated assumptions.</span></div>
                    <div class="topic-index">
                        <a href="#topic-1">1. Introduction to C Programming</a><a href="#topic-2">2. History and Features of C</a><a href="#topic-3">3. Structure of a C Program</a><a href="#topic-4">4. Installing a C Compiler and IDE</a><a href="#topic-5">5. Basic Syntax and Statements</a><a href="#topic-6">6. Variables and Constants</a><a href="#topic-7">7. Data Types in C</a><a href="#topic-8">8. Keywords and Identifiers</a><a href="#topic-9">9. Input and Output: printf and scanf</a><a href="#topic-10">10. Operators and Expressions</a><a href="#topic-11">11. Type Conversion and Type Casting</a><a href="#topic-12">12. Comments and Code Documentation</a><a href="#topic-13">13. Escape Sequences and Format Specifiers</a><a href="#topic-14">14. Decision-Making Statements: if, else, switch</a><a href="#topic-15">15. Nested Conditional Statements</a><a href="#topic-16">16. Loops: for, while, do-while</a><a href="#topic-17">17. Nested Loops</a><a href="#topic-18">18. break, continue, and goto</a><a href="#topic-19">19. Functions and Function Prototypes</a><a href="#topic-20">20. Function Parameters and Return Values</a><a href="#topic-21">21. Recursion</a><a href="#topic-22">22. Variable Scope and Storage Classes</a><a href="#topic-23">23. One-Dimensional Arrays</a><a href="#topic-24">24. Multidimensional Arrays</a><a href="#topic-25">25. String Manipulation</a><a href="#topic-26">26. Standard String Library: string.h</a><a href="#topic-27">27. Pointers and Memory Addresses</a><a href="#topic-28">28. Pointer Arithmetic</a><a href="#topic-29">29. Pointers and Arrays</a><a href="#topic-30">30. Pointers and Functions</a><a href="#topic-31">31. Dynamic Memory Allocation</a><a href="#topic-32">32. Structures</a><a href="#topic-33">33. Unions</a><a href="#topic-34">34. Enumerations</a><a href="#topic-35">35. typedef and Type Aliases</a><a href="#topic-36">36. File Handling and Streams</a><a href="#topic-37">37. Reading and Writing Files</a><a href="#topic-38">38. Preprocessor Directives</a><a href="#topic-39">39. Header Files and Modular Programming</a><a href="#topic-40">40. Command-Line Arguments</a><a href="#topic-41">41. Bitwise Operators</a><a href="#topic-42">42. Memory Safety and Undefined Behavior</a><a href="#topic-43">43. Error Handling and Debugging</a><a href="#topic-44">44. Testing and Program Design</a><a href="#topic-45">45. Basic Syntax and Statements</a><a href="#topic-46">46. Variables and Constants</a><a href="#topic-47">47. Data Types in C</a><a href="#topic-48">48. Keywords and Identifiers</a><a href="#topic-49">49. Input and Output: printf and scanf</a><a href="#topic-50">50. Operators and Expressions</a><a href="#topic-51">51. Type Conversion and Type Casting</a><a href="#topic-52">52. Comments and Code Documentation</a><a href="#topic-53">53. Escape Sequences and Format Specifiers</a><a href="#topic-54">54. Decision-Making Statements</a><a href="#topic-55">55. Nested Conditional Statements</a><a href="#topic-56">56. Loops: for, while, and do-while</a><a href="#topic-57">57. Nested Loops</a><a href="#topic-58">58. break and continue Statements</a>
                    </div>
                </div>
            </div>

            <div class="theory-card" id="topic-1">
                <div class="theory-header">
                    <div>
                        <h4>1. Introduction to C Programming</h4>
                        <p>Concepts, syntax, and worked example</p>
                    </div>
                    <div class="statement-number">TOPIC 1</div>
                </div>
                <div class="theory-body">
                    <div class="definition-box"><strong>Explanation</strong><span>C is a general-purpose, compiled programming language often used for systems software, embedded controllers, and performance-sensitive applications.</span></div>
                    <div class="section-label">Example</div>
                    <div class="code-box">
                        <pre><code>#include &lt;stdio.h&gt;
int main(void) {
    printf("Hello, C!\\n");
    return 0;
}</code></pre>
                    </div>
                    <div class="result-box"><strong>What to remember</strong><span>Hello, C! — execution starts in main; printf displays the text and return 0 indicates successful completion.</span></div>
                </div>
            </div>

            <div class="theory-card" id="topic-2">
                <div class="theory-header">
                    <div>
                        <h4>2. History and Features of C</h4>
                        <p>Concepts, syntax, and worked example</p>
                    </div>
                    <div class="statement-number">TOPIC 2</div>
                </div>
                <div class="theory-body">
                    <div class="definition-box"><strong>Explanation</strong><span>C was developed by Dennis Ritchie at Bell Labs in the early 1970s. It is procedural, compiled, portable when written to standard C, supports pointers and direct memory access, and provides a small core language with a standard library.</span></div>
                    <div class="section-label">Example</div>
                    <div class="code-box">
                        <pre><code>/* C is procedural: statements are organized into functions. */
int twice(int value) {
    return value * 2;
}</code></pre>
                    </div>
                    <div class="result-box"><strong>What to remember</strong><span>The function receives an integer and returns twice its value. C gives programmers low-level control, but that control requires careful memory and type handling.</span></div>
                </div>
            </div>

            <div class="theory-card" id="topic-3">
                <div class="theory-header">
                    <div>
                        <h4>3. Structure of a C Program</h4>
                        <p>Concepts, syntax, and worked example</p>
                    </div>
                    <div class="statement-number">TOPIC 3</div>
                </div>
                <div class="theory-body">
                    <div class="definition-box"><strong>Explanation</strong><span>A typical program has preprocessor directives, function declarations or definitions, a main function, declarations, statements, and a return statement. Execution begins at main.</span></div>
                    <div class="section-label">Example</div>
                    <div class="code-box">
                        <pre><code>#include &lt;stdio.h&gt;

int main(void) {
    int count = 3;
    printf("%d\\n", count);
    return 0;
}</code></pre>
                    </div>
                    <div class="result-box"><strong>What to remember</strong><span>The program prints 3. The include directive provides the declaration of printf.</span></div>
                </div>
            </div>

            <div class="theory-card" id="topic-4">
                <div class="theory-header">
                    <div>
                        <h4>4. Installing a C Compiler and IDE</h4>
                        <p>Concepts, syntax, and worked example</p>
                    </div>
                    <div class="statement-number">TOPIC 4</div>
                </div>
                <div class="theory-body">
                    <div class="definition-box"><strong>Explanation</strong><span>A compiler translates C source code into executable code. An IDE combines tools such as an editor, compiler integration, and debugger. A typical workflow is edit, compile, fix diagnostics, run, and test.</span></div>
                    <div class="section-label">Example</div>
                    <div class="code-box">
                        <pre><code>/* Save as hello.c, then compile with a C compiler. */
#include &lt;stdio.h&gt;
int main(void) { puts("Ready"); return 0; }</code></pre>
                    </div>
                    <div class="result-box"><strong>What to remember</strong><span>A successful build produces a runnable program that prints Ready. Compiler diagnostics should be read before trying to run a failed build.</span></div>
                </div>
            </div>

            <div class="theory-card" id="topic-5">
                <div class="theory-header">
                    <div>
                        <h4>5. Basic Syntax and Statements</h4>
                        <p>Concepts, syntax, and worked example</p>
                    </div>
                    <div class="statement-number">TOPIC 5</div>
                </div>
                <div class="theory-body">
                    <div class="definition-box"><strong>Explanation</strong><span>C is case-sensitive. Most statements end in a semicolon, and braces group a block. Missing punctuation or unmatched braces commonly causes compile errors.</span></div>
                    <div class="section-label">Example</div>
                    <div class="code-box">
                        <pre><code>int a = 4;
a += 2;
printf("%d\\n", a);</code></pre>
                    </div>
                    <div class="result-box"><strong>What to remember</strong><span>The output is 6. The assignment operator updates a, and the printf statement displays the new value.</span></div>
                </div>
            </div>

            <div class="theory-card" id="topic-6">
                <div class="theory-header">
                    <div>
                        <h4>6. Variables and Constants</h4>
                        <p>Concepts, syntax, and worked example</p>
                    </div>
                    <div class="statement-number">TOPIC 6</div>
                </div>
                <div class="theory-body">
                    <div class="definition-box"><strong>Explanation</strong><span>A variable stores a value that can change. const marks an object as not modifiable through that identifier. Declare variables with a suitable type and initialize them before use.</span></div>
                    <div class="section-label">Example</div>
                    <div class="code-box">
                        <pre><code>float voltage = 12.0f;
const float limit = 15.0f;
voltage += 0.5f;
printf("%.1f V\\n", voltage);</code></pre>
                    </div>
                    <div class="result-box"><strong>What to remember</strong><span>The output is 12.5 V. The variable changes; limit is declared const and should not be assigned a new value.</span></div>
                </div>
            </div>

            <div class="theory-card" id="topic-7">
                <div class="theory-header">
                    <div>
                        <h4>7. Data Types in C</h4>
                        <p>Concepts, syntax, and worked example</p>
                    </div>
                    <div class="statement-number">TOPIC 7</div>
                </div>
                <div class="theory-body">
                    <div class="definition-box"><strong>Explanation</strong><span>Common types include char, int, float, double, and void. Signedness and integer widths affect ranges; exact sizes are implementation-dependent. Use a type appropriate to the data and required range.</span></div>
                    <div class="section-label">Example</div>
                    <div class="code-box">
                        <pre><code>int count = 8;
char state = 'R';
float current = 2.5f;
printf("%d %c %.1f\\n", count, state, current);</code></pre>
                    </div>
                    <div class="result-box"><strong>What to remember</strong><span>The output is 8 R 2.5. Format specifiers must match the corresponding argument types.</span></div>
                </div>
            </div>

            <div class="theory-card" id="topic-8">
                <div class="theory-header">
                    <div>
                        <h4>8. Keywords and Identifiers</h4>
                        <p>Concepts, syntax, and worked example</p>
                    </div>
                    <div class="statement-number">TOPIC 8</div>
                </div>
                <div class="theory-body">
                    <div class="definition-box"><strong>Explanation</strong><span>Keywords such as int, if, while, and return have reserved meanings. Identifiers name variables, functions, and other entities; they may contain letters, digits, and underscores but cannot start with a digit.</span></div>
                    <div class="section-label">Example</div>
                    <div class="code-box">
                        <pre><code>int motor_speed = 1200;
/* int = 4;      invalid: keyword */
/* int 2speed;   invalid: starts with digit */</code></pre>
                    </div>
                    <div class="result-box"><strong>What to remember</strong><span>motor_speed is a valid identifier. C is case-sensitive, so Speed and speed are different names.</span></div>
                </div>
            </div>

            <div class="theory-card" id="topic-9">
                <div class="theory-header">
                    <div>
                        <h4>9. Input and Output: printf and scanf</h4>
                        <p>Concepts, syntax, and worked example</p>
                    </div>
                    <div class="statement-number">TOPIC 9</div>
                </div>
                <div class="theory-body">
                    <div class="definition-box"><strong>Explanation</strong><span>printf displays formatted output. scanf reads formatted input; for scalar variables, pass their addresses with & and check how many items were successfully read.</span></div>
                    <div class="section-label">Example</div>
                    <div class="code-box">
                        <pre><code>int age;
if (scanf("%d", &amp;age) == 1) {
    printf("Age = %d\\n", age);
}</code></pre>
                    </div>
                    <div class="result-box"><strong>What to remember</strong><span>For input 20, the program prints Age = 20. If scanf does not read one integer, the message is not printed.</span></div>
                </div>
            </div>

            <div class="theory-card" id="topic-10">
                <div class="theory-header">
                    <div>
                        <h4>10. Operators and Expressions</h4>
                        <p>Concepts, syntax, and worked example</p>
                    </div>
                    <div class="statement-number">TOPIC 10</div>
                </div>
                <div class="theory-body">
                    <div class="definition-box"><strong>Explanation</strong><span>Arithmetic operators include +, -, *, /, and %. Relational operators compare values; logical operators combine conditions. Integer division truncates the fractional part when both operands are integers.</span></div>
                    <div class="section-label">Example</div>
                    <div class="code-box">
                        <pre><code>int a = 7, b = 2;
printf("%d %d\\n", a / b, a % b);</code></pre>
                    </div>
                    <div class="result-box"><strong>What to remember</strong><span>The output is 3 1: integer division gives quotient 3 and the remainder operator gives 1.</span></div>
                </div>
            </div>

            <div class="theory-card" id="topic-11">
                <div class="theory-header">
                    <div>
                        <h4>11. Type Conversion and Type Casting</h4>
                        <p>Concepts, syntax, and worked example</p>
                    </div>
                    <div class="statement-number">TOPIC 11</div>
                </div>
                <div class="theory-body">
                    <div class="definition-box"><strong>Explanation</strong><span>C performs implicit conversions according to its type rules. An explicit cast such as (double)value requests a conversion. Casting to an integer from a floating-point value discards the fractional part toward zero.</span></div>
                    <div class="section-label">Example</div>
                    <div class="code-box">
                        <pre><code>int total = 5, count = 2;
printf("%.2f %d\\n", (double)total / count, total / count);</code></pre>
                    </div>
                    <div class="result-box"><strong>What to remember</strong><span>The output is 2.50 2. Casting one operand to double makes the first division floating-point; the second remains integer division.</span></div>
                </div>
            </div>

            <div class="theory-card" id="topic-12">
                <div class="theory-header">
                    <div>
                        <h4>12. Comments and Code Documentation</h4>
                        <p>Concepts, syntax, and worked example</p>
                    </div>
                    <div class="statement-number">TOPIC 12</div>
                </div>
                <div class="theory-body">
                    <div class="definition-box"><strong>Explanation</strong><span>Comments explain intent, assumptions, units, or non-obvious logic. // starts a line comment; /* ... */ creates a block comment. Standard C block comments do not nest.</span></div>
                    <div class="section-label">Example</div>
                    <div class="code-box">
                        <pre><code>// Voltage measured in volts
float voltage = 12.0f; /* sensor reading */</code></pre>
                    </div>
                    <div class="result-box"><strong>What to remember</strong><span>Comments do not execute as program statements. Keep them accurate and focused on useful context.</span></div>
                </div>
            </div>

            <div class="theory-card" id="topic-13">
                <div class="theory-header">
                    <div>
                        <h4>13. Escape Sequences and Format Specifiers</h4>
                        <p>Concepts, syntax, and worked example</p>
                    </div>
                    <div class="statement-number">TOPIC 13</div>
                </div>
                <div class="theory-body">
                    <div class="definition-box"><strong>Explanation</strong><span>Escape sequences represent special characters in string literals. Common examples are \n for newline, \t for tab, \\ for backslash, and \" for a quote. printf format strings use specifiers such as %d, %c, %s, and %.2f.</span></div>
                    <div class="section-label">Example</div>
                    <div class="code-box">
                        <pre><code>printf("Voltage:\t%.2f V\\n", 12.3456);
printf("Load: 100%%\\n");</code></pre>
                    </div>
                    <div class="result-box"><strong>What to remember</strong><span>The displayed voltage is rounded to two decimal places, and %% prints a literal percent sign.</span></div>
                </div>
            </div>

            <div class="theory-card" id="topic-14">
                <div class="theory-header">
                    <div>
                        <h4>14. Decision-Making Statements: if, else, switch</h4>
                        <p>Concepts, syntax, and worked example</p>
                    </div>
                    <div class="statement-number">TOPIC 14</div>
                </div>
                <div class="theory-body">
                    <div class="definition-box"><strong>Explanation</strong><span>if executes a block when a condition is true; if-else selects between two paths; else-if chains test conditions in order. switch selects a matching integral or enumeration case and often uses break to avoid fall-through.</span></div>
                    <div class="section-label">Example</div>
                    <div class="code-box">
                        <pre><code>int mode = 2;
switch (mode) {
    case 1: puts("OFF"); break;
    case 2: puts("RUN"); break;
    default: puts("UNKNOWN");
}</code></pre>
                    </div>
                    <div class="result-box"><strong>What to remember</strong><span>The output is RUN because case 2 matches and break exits the switch.</span></div>
                </div>
            </div>

            <div class="theory-card" id="topic-15">
                <div class="theory-header">
                    <div>
                        <h4>15. Nested Conditional Statements</h4>
                        <p>Concepts, syntax, and worked example</p>
                    </div>
                    <div class="statement-number">TOPIC 15</div>
                </div>
                <div class="theory-body">
                    <div class="definition-box"><strong>Explanation</strong><span>A nested if places a conditional inside another conditional. The inner decision is reached only if control flow enters the outer block. Braces and indentation help clarify which else belongs to which if.</span></div>
                    <div class="section-label">Example</div>
                    <div class="code-box">
                        <pre><code>int current = 8;
if (current &lt;= 10) {
    if (current &gt;= 2) puts("NORMAL");
} else {
    puts("OVERCURRENT");
}</code></pre>
                    </div>
                    <div class="result-box"><strong>What to remember</strong><span>The output is NORMAL because the outer and inner conditions are both true.</span></div>
                </div>
            </div>

            <div class="theory-card" id="topic-16">
                <div class="theory-header">
                    <div>
                        <h4>16. Loops: for, while, do-while</h4>
                        <p>Concepts, syntax, and worked example</p>
                    </div>
                    <div class="statement-number">TOPIC 16</div>
                </div>
                <div class="theory-body">
                    <div class="definition-box"><strong>Explanation</strong><span>for groups initialization, condition, and update; while checks its condition before each iteration; do-while runs its body before testing the condition, so its body executes at least once.</span></div>
                    <div class="section-label">Example</div>
                    <div class="code-box">
                        <pre><code>int sum = 0;
for (int i = 1; i &lt;= 4; i++) sum += i;
printf("%d\\n", sum);</code></pre>
                    </div>
                    <div class="result-box"><strong>What to remember</strong><span>The output is 10 because the loop adds 1 + 2 + 3 + 4.</span></div>
                </div>
            </div>

            <div class="theory-card" id="topic-17">
                <div class="theory-header">
                    <div>
                        <h4>17. Nested Loops</h4>
                        <p>Concepts, syntax, and worked example</p>
                    </div>
                    <div class="statement-number">TOPIC 17</div>
                </div>
                <div class="theory-body">
                    <div class="definition-box"><strong>Explanation</strong><span>A nested loop places one loop inside another. For each outer iteration, the inner loop typically completes all its iterations. Nested loops are useful for tables, grids, and matrices.</span></div>
                    <div class="section-label">Example</div>
                    <div class="code-box">
                        <pre><code>for (int row = 1; row &lt;= 2; row++) {
    for (int col = 1; col &lt;= 2; col++)
        printf("%d%d ", row, col);
}</code></pre>
                    </div>
                    <div class="result-box"><strong>What to remember</strong><span>The output is 11 12 21 22. The inner loop runs twice for each of the two outer iterations.</span></div>
                </div>
            </div>

            <div class="theory-card" id="topic-18">
                <div class="theory-header">
                    <div>
                        <h4>18. break, continue, and goto</h4>
                        <p>Concepts, syntax, and worked example</p>
                    </div>
                    <div class="statement-number">TOPIC 18</div>
                </div>
                <div class="theory-body">
                    <div class="definition-box"><strong>Explanation</strong><span>break exits the nearest loop or switch. continue skips the rest of the current loop iteration. goto transfers control to a labeled statement within the same function; use it sparingly because it can make flow harder to follow.</span></div>
                    <div class="section-label">Example</div>
                    <div class="code-box">
                        <pre><code>for (int i = 1; i &lt;= 5; i++) {
    if (i == 3) continue;
    if (i == 5) break;
    printf("%d ", i);
}</code></pre>
                    </div>
                    <div class="result-box"><strong>What to remember</strong><span>The output is 1 2 4. continue skips printing 3; break exits before 5 is printed.</span></div>
                </div>
            </div>

            <div class="theory-card" id="topic-19">
                <div class="theory-header">
                    <div>
                        <h4>19. Functions and Function Prototypes</h4>
                        <p>Concepts, syntax, and worked example</p>
                    </div>
                    <div class="statement-number">TOPIC 19</div>
                </div>
                <div class="theory-body">
                    <div class="definition-box"><strong>Explanation</strong><span>A function groups reusable behavior. A prototype declares a function's name, return type, and parameter types before use; a definition supplies the body.</span></div>
                    <div class="section-label">Example</div>
                    <div class="code-box">
                        <pre><code>int square(int value);
int main(void) { printf("%d\\n", square(4)); return 0; }
int square(int value) { return value * value; }</code></pre>
                    </div>
                    <div class="result-box"><strong>What to remember</strong><span>The output is 16. The prototype lets the compiler check the call before the definition appears.</span></div>
                </div>
            </div>

            <div class="theory-card" id="topic-20">
                <div class="theory-header">
                    <div>
                        <h4>20. Function Parameters and Return Values</h4>
                        <p>Concepts, syntax, and worked example</p>
                    </div>
                    <div class="statement-number">TOPIC 20</div>
                </div>
                <div class="theory-body">
                    <div class="definition-box"><strong>Explanation</strong><span>Parameters receive values supplied by a caller. A return statement sends a value back to the caller; a void function does not return a value.</span></div>
                    <div class="section-label">Example</div>
                    <div class="code-box">
                        <pre><code>double power(double voltage, double current) {
    return voltage * current;
}
printf("%.1f\\n", power(12.0, 2.0));</code></pre>
                    </div>
                    <div class="result-box"><strong>What to remember</strong><span>The output is 24.0. C passes ordinary arguments by value, so a function receives copies of those values.</span></div>
                </div>
            </div>

            <div class="theory-card" id="topic-21">
                <div class="theory-header">
                    <div>
                        <h4>21. Recursion</h4>
                        <p>Concepts, syntax, and worked example</p>
                    </div>
                    <div class="statement-number">TOPIC 21</div>
                </div>
                <div class="theory-body">
                    <div class="definition-box"><strong>Explanation</strong><span>Recursion occurs when a function calls itself. A recursive function needs a base case and progress toward that case to avoid unbounded calls.</span></div>
                    <div class="section-label">Example</div>
                    <div class="code-box">
                        <pre><code>int factorial(int n) {
    if (n &lt;= 1) return 1;
    return n * factorial(n - 1);
}</code></pre>
                    </div>
                    <div class="result-box"><strong>What to remember</strong><span>factorial(4) returns 24 because 4 × 3 × 2 × 1 = 24. This example assumes a nonnegative input small enough to avoid overflow.</span></div>
                </div>
            </div>

            <div class="theory-card" id="topic-22">
                <div class="theory-header">
                    <div>
                        <h4>22. Variable Scope and Storage Classes</h4>
                        <p>Concepts, syntax, and worked example</p>
                    </div>
                    <div class="statement-number">TOPIC 22</div>
                </div>
                <div class="theory-body">
                    <div class="definition-box"><strong>Explanation</strong><span>Scope determines where an identifier can be used. A local variable is limited to its block. A static local retains its value between calls; extern can declare an object or function defined elsewhere.</span></div>
                    <div class="section-label">Example</div>
                    <div class="code-box">
                        <pre><code>int next_id(void) {
    static int id = 0;
    id++;
    return id;
}</code></pre>
                    </div>
                    <div class="result-box"><strong>What to remember</strong><span>Successive calls return 1, then 2, then 3 because the static local retains its value.</span></div>
                </div>
            </div>

            <div class="theory-card" id="topic-23">
                <div class="theory-header">
                    <div>
                        <h4>23. One-Dimensional Arrays</h4>
                        <p>Concepts, syntax, and worked example</p>
                    </div>
                    <div class="statement-number">TOPIC 23</div>
                </div>
                <div class="theory-body">
                    <div class="definition-box"><strong>Explanation</strong><span>An array stores a fixed number of elements of the same type in contiguous storage. Indexing starts at 0, so an array of length n has valid indices 0 through n-1.</span></div>
                    <div class="section-label">Example</div>
                    <div class="code-box">
                        <pre><code>int readings[3] = {10, 12, 14};
printf("%d\\n", readings[1]);</code></pre>
                    </div>
                    <div class="result-box"><strong>What to remember</strong><span>The output is 12. Accessing outside the array bounds is undefined behavior.</span></div>
                </div>
            </div>

            <div class="theory-card" id="topic-24">
                <div class="theory-header">
                    <div>
                        <h4>24. Multidimensional Arrays</h4>
                        <p>Concepts, syntax, and worked example</p>
                    </div>
                    <div class="statement-number">TOPIC 24</div>
                </div>
                <div class="theory-body">
                    <div class="definition-box"><strong>Explanation</strong><span>A multidimensional array is an array whose elements are themselves arrays. A two-dimensional array is commonly used to represent rows and columns.</span></div>
                    <div class="section-label">Example</div>
                    <div class="code-box">
                        <pre><code>int grid[2][2] = {{1, 2}, {3, 4}};
printf("%d\\n", grid[1][0]);</code></pre>
                    </div>
                    <div class="result-box"><strong>What to remember</strong><span>The output is 3: row index 1, column index 0.</span></div>
                </div>
            </div>

            <div class="theory-card" id="topic-25">
                <div class="theory-header">
                    <div>
                        <h4>25. String Manipulation</h4>
                        <p>Concepts, syntax, and worked example</p>
                    </div>
                    <div class="statement-number">TOPIC 25</div>
                </div>
                <div class="theory-body">
                    <div class="definition-box"><strong>Explanation</strong><span>A C string is a character array terminated by the null character '\0'. Reserve enough space for all characters plus the terminator, and avoid writing past the array.</span></div>
                    <div class="section-label">Example</div>
                    <div class="code-box">
                        <pre><code>char name[] = "C language";
printf("%s\\n", name);</code></pre>
                    </div>
                    <div class="result-box"><strong>What to remember</strong><span>The output is C language. The string literal's terminating null character marks the end of the string.</span></div>
                </div>
            </div>

            <div class="theory-card" id="topic-26">
                <div class="theory-header">
                    <div>
                        <h4>26. Standard String Library: string.h</h4>
                        <p>Concepts, syntax, and worked example</p>
                    </div>
                    <div class="statement-number">TOPIC 26</div>
                </div>
                <div class="theory-body">
                    <div class="definition-box"><strong>Explanation</strong><span>The string.h library provides functions such as strlen, strcmp, strcpy, and strcat. Destination buffers must be large enough, and overlapping-copy or buffer-size mistakes can cause undefined behavior.</span></div>
                    <div class="section-label">Example</div>
                    <div class="code-box">
                        <pre><code>char text[] = "C code";
printf("%zu\\n", strlen(text));</code></pre>
                    </div>
                    <div class="result-box"><strong>What to remember</strong><span>The output is 6 because strlen counts characters before the null terminator, not the terminator itself.</span></div>
                </div>
            </div>

            <div class="theory-card" id="topic-27">
                <div class="theory-header">
                    <div>
                        <h4>27. Pointers and Memory Addresses</h4>
                        <p>Concepts, syntax, and worked example</p>
                    </div>
                    <div class="statement-number">TOPIC 27</div>
                </div>
                <div class="theory-body">
                    <div class="definition-box"><strong>Explanation</strong><span>A pointer stores an address of an object or function. & obtains an object's address and * dereferences a valid pointer to access the pointed-to object.</span></div>
                    <div class="section-label">Example</div>
                    <div class="code-box">
                        <pre><code>int value = 7;
int *ptr = &amp;value;
*ptr = 9;
printf("%d\\n", value);</code></pre>
                    </div>
                    <div class="result-box"><strong>What to remember</strong><span>The output is 9 because assigning through ptr modifies value.</span></div>
                </div>
            </div>

            <div class="theory-card" id="topic-28">
                <div class="theory-header">
                    <div>
                        <h4>28. Pointer Arithmetic</h4>
                        <p>Concepts, syntax, and worked example</p>
                    </div>
                    <div class="statement-number">TOPIC 28</div>
                </div>
                <div class="theory-body">
                    <div class="definition-box"><strong>Explanation</strong><span>Pointer arithmetic advances or retreats by elements of the pointed-to type, not by individual bytes. Arithmetic is only defined within an array object and one position past its end; the one-past pointer must not be dereferenced.</span></div>
                    <div class="section-label">Example</div>
                    <div class="code-box">
                        <pre><code>int values[] = {4, 8, 12};
int *ptr = values;
printf("%d\\n", *(ptr + 1));</code></pre>
                    </div>
                    <div class="result-box"><strong>What to remember</strong><span>The output is 8 because ptr + 1 points to the second integer element.</span></div>
                </div>
            </div>

            <div class="theory-card" id="topic-29">
                <div class="theory-header">
                    <div>
                        <h4>29. Pointers and Arrays</h4>
                        <p>Concepts, syntax, and worked example</p>
                    </div>
                    <div class="statement-number">TOPIC 29</div>
                </div>
                <div class="theory-body">
                    <div class="definition-box"><strong>Explanation</strong><span>In most expressions, an array expression converts to a pointer to its first element. Array indexing and pointer arithmetic are closely related, but an array itself is not an assignable pointer variable.</span></div>
                    <div class="section-label">Example</div>
                    <div class="code-box">
                        <pre><code>int values[] = {5, 10, 15};
int *p = values;
printf("%d %d\\n", values[2], *(p + 2));</code></pre>
                    </div>
                    <div class="result-box"><strong>What to remember</strong><span>The output is 15 15. Both expressions refer to the third array element.</span></div>
                </div>
            </div>

            <div class="theory-card" id="topic-30">
                <div class="theory-header">
                    <div>
                        <h4>30. Pointers and Functions</h4>
                        <p>Concepts, syntax, and worked example</p>
                    </div>
                    <div class="statement-number">TOPIC 30</div>
                </div>
                <div class="theory-body">
                    <div class="definition-box"><strong>Explanation</strong><span>Pointers can be passed to functions when the function needs to modify a caller's object or work with an array. C still passes the pointer value by value; dereferencing that copy can modify the pointed-to object.</span></div>
                    <div class="section-label">Example</div>
                    <div class="code-box">
                        <pre><code>void double_value(int *p) { *p *= 2; }
int value = 6;
double_value(&amp;value);
printf("%d\\n", value);</code></pre>
                    </div>
                    <div class="result-box"><strong>What to remember</strong><span>The output is 12. The function receives the address of value and changes the object through the pointer.</span></div>
                </div>
            </div>

            <div class="theory-card" id="topic-31">
                <div class="theory-header">
                    <div>
                        <h4>31. Dynamic Memory Allocation</h4>
                        <p>Concepts, syntax, and worked example</p>
                    </div>
                    <div class="statement-number">TOPIC 31</div>
                </div>
                <div class="theory-body">
                    <div class="definition-box"><strong>Explanation</strong><span>stdlib.h provides malloc, calloc, realloc, and free. Check allocation results, free allocated storage when no longer needed, and never use a pointer after its allocation has been freed.</span></div>
                    <div class="section-label">Example</div>
                    <div class="code-box">
                        <pre><code>int *p = malloc(3 * sizeof *p);
if (p != NULL) {
    p[0] = 10;
    printf("%d\\n", p[0]);
    free(p);
}</code></pre>
                    </div>
                    <div class="result-box"><strong>What to remember</strong><span>If allocation succeeds, the output is 10. This snippet needs #include <stdlib.h> and a surrounding valid function; allocation failure must be handled.</span></div>
                </div>
            </div>

            <div class="theory-card" id="topic-32">
                <div class="theory-header">
                    <div>
                        <h4>32. Structures</h4>
                        <p>Concepts, syntax, and worked example</p>
                    </div>
                    <div class="statement-number">TOPIC 32</div>
                </div>
                <div class="theory-body">
                    <div class="definition-box"><strong>Explanation</strong><span>A struct groups related members, possibly of different types, into one object. Use the dot operator for a structure object and the arrow operator for a pointer to a structure.</span></div>
                    <div class="section-label">Example</div>
                    <div class="code-box">
                        <pre><code>struct Sensor { int id; float voltage; };
struct Sensor s = {1, 12.5f};
printf("%d %.1f\\n", s.id, s.voltage);</code></pre>
                    </div>
                    <div class="result-box"><strong>What to remember</strong><span>The output is 1 12.5. Each member is accessed through the structure object.</span></div>
                </div>
            </div>

            <div class="theory-card" id="topic-33">
                <div class="theory-header">
                    <div>
                        <h4>33. Unions</h4>
                        <p>Concepts, syntax, and worked example</p>
                    </div>
                    <div class="statement-number">TOPIC 33</div>
                </div>
                <div class="theory-body">
                    <div class="definition-box"><strong>Explanation</strong><span>A union lets its members share the same storage. It can hold a value for one member at a time; writing one member affects the stored representation used by the others.</span></div>
                    <div class="section-label">Example</div>
                    <div class="code-box">
                        <pre><code>union Reading { int count; float voltage; };
union Reading r;
r.count = 5;
printf("%d\\n", r.count);</code></pre>
                    </div>
                    <div class="result-box"><strong>What to remember</strong><span>The output is 5. Read the member whose value was most recently stored; do not assume different union members retain independent values.</span></div>
                </div>
            </div>

            <div class="theory-card" id="topic-34">
                <div class="theory-header">
                    <div>
                        <h4>34. Enumerations</h4>
                        <p>Concepts, syntax, and worked example</p>
                    </div>
                    <div class="statement-number">TOPIC 34</div>
                </div>
                <div class="theory-body">
                    <div class="definition-box"><strong>Explanation</strong><span>An enum defines named integer constants, improving readability for a set of states or choices.</span></div>
                    <div class="section-label">Example</div>
                    <div class="code-box">
                        <pre><code>enum State { OFF, RUNNING, FAULT };
enum State state = RUNNING;
printf("%d\\n", state);</code></pre>
                    </div>
                    <div class="result-box"><strong>What to remember</strong><span>The output is 1 because enumerators start at 0 by default: OFF is 0, RUNNING is 1, and FAULT is 2.</span></div>
                </div>
            </div>

            <div class="theory-card" id="topic-35">
                <div class="theory-header">
                    <div>
                        <h4>35. typedef and Type Aliases</h4>
                        <p>Concepts, syntax, and worked example</p>
                    </div>
                    <div class="statement-number">TOPIC 35</div>
                </div>
                <div class="theory-body">
                    <div class="definition-box"><strong>Explanation</strong><span>typedef introduces an alias for an existing type. It can make complex declarations easier to read, but it does not create a distinct new type.</span></div>
                    <div class="section-label">Example</div>
                    <div class="code-box">
                        <pre><code>typedef unsigned int Counter;
Counter pulses = 100u;
printf("%u\\n", pulses);</code></pre>
                    </div>
                    <div class="result-box"><strong>What to remember</strong><span>The output is 100. Counter is an alias for unsigned int.</span></div>
                </div>
            </div>

            <div class="theory-card" id="topic-36">
                <div class="theory-header">
                    <div>
                        <h4>36. File Handling and Streams</h4>
                        <p>Concepts, syntax, and worked example</p>
                    </div>
                    <div class="statement-number">TOPIC 36</div>
                </div>
                <div class="theory-body">
                    <div class="definition-box"><strong>Explanation</strong><span>The standard I/O library uses FILE pointers and streams. fopen opens a file and returns NULL on failure; fclose closes an opened stream. Always check operations that can fail.</span></div>
                    <div class="section-label">Example</div>
                    <div class="code-box">
                        <pre><code>FILE *file = fopen("data.txt", "r");
if (file != NULL) {
    puts("Opened");
    fclose(file);
}</code></pre>
                    </div>
                    <div class="result-box"><strong>What to remember</strong><span>If data.txt opens successfully, the program prints Opened. The result depends on the file and environment.</span></div>
                </div>
            </div>

            <div class="theory-card" id="topic-37">
                <div class="theory-header">
                    <div>
                        <h4>37. Reading and Writing Files</h4>
                        <p>Concepts, syntax, and worked example</p>
                    </div>
                    <div class="statement-number">TOPIC 37</div>
                </div>
                <div class="theory-body">
                    <div class="definition-box"><strong>Explanation</strong><span>Functions such as fprintf, fscanf, fgets, fputs, fgetc, and fputc read or write through streams. Check return values and distinguish end-of-file from errors.</span></div>
                    <div class="section-label">Example</div>
                    <div class="code-box">
                        <pre><code>FILE *file = fopen("result.txt", "w");
if (file != NULL) {
    fprintf(file, "Voltage=%.1f\\n", 12.5);
    fclose(file);
}</code></pre>
                    </div>
                    <div class="result-box"><strong>What to remember</strong><span>If opening succeeds, the file receives Voltage=12.5 followed by a newline. The program should also handle write and close errors in robust applications.</span></div>
                </div>
            </div>

            <div class="theory-card" id="topic-38">
                <div class="theory-header">
                    <div>
                        <h4>38. Preprocessor Directives</h4>
                        <p>Concepts, syntax, and worked example</p>
                    </div>
                    <div class="statement-number">TOPIC 38</div>
                </div>
                <div class="theory-body">
                    <div class="definition-box"><strong>Explanation</strong><span>Preprocessor directives begin with # and are handled before compilation. Common examples include #include, #define, #if, #ifdef, and #ifndef.</span></div>
                    <div class="section-label">Example</div>
                    <div class="code-box">
                        <pre><code>#define MAX_SAMPLES 100
#if MAX_SAMPLES &gt; 0
/* compiled when the condition is true */
#endif</code></pre>
                    </div>
                    <div class="result-box"><strong>What to remember</strong><span>MAX_SAMPLES is replaced by its macro expansion where applicable. Parenthesize macro parameters and be aware that macros do not provide normal type checking.</span></div>
                </div>
            </div>

            <div class="theory-card" id="topic-39">
                <div class="theory-header">
                    <div>
                        <h4>39. Header Files and Modular Programming</h4>
                        <p>Concepts, syntax, and worked example</p>
                    </div>
                    <div class="statement-number">TOPIC 39</div>
                </div>
                <div class="theory-body">
                    <div class="definition-box"><strong>Explanation</strong><span>Header files commonly declare functions, types, and macros shared by source files. Include guards prevent a header's contents from being processed repeatedly in one translation unit.</span></div>
                    <div class="section-label">Example</div>
                    <div class="code-box">
                        <pre><code>#ifndef SENSOR_H
#define SENSOR_H
int read_sensor(void);
#endif</code></pre>
                    </div>
                    <div class="result-box"><strong>What to remember</strong><span>The guard prevents repeated inclusion of the same header contents. Function definitions are typically placed in a corresponding .c source file.</span></div>
                </div>
            </div>

            <div class="theory-card" id="topic-40">
                <div class="theory-header">
                    <div>
                        <h4>40. Command-Line Arguments</h4>
                        <p>Concepts, syntax, and worked example</p>
                    </div>
                    <div class="statement-number">TOPIC 40</div>
                </div>
                <div class="theory-body">
                    <div class="definition-box"><strong>Explanation</strong><span>A hosted C program may receive argc and argv in main. argc counts arguments and argv is an array of strings; argv[0] commonly names the program, and argv[1] is the first user-supplied argument when present.</span></div>
                    <div class="section-label">Example</div>
                    <div class="code-box">
                        <pre><code>int main(int argc, char *argv[]) {
    if (argc &gt; 1) puts(argv[1]);
    return 0;
}</code></pre>
                    </div>
                    <div class="result-box"><strong>What to remember</strong><span>If invoked with ./app test, the program prints test. Check argc before accessing an argument.</span></div>
                </div>
            </div>

            <div class="theory-card" id="topic-41">
                <div class="theory-header">
                    <div>
                        <h4>41. Bitwise Operators</h4>
                        <p>Concepts, syntax, and worked example</p>
                    </div>
                    <div class="statement-number">TOPIC 41</div>
                </div>
                <div class="theory-body">
                    <div class="definition-box"><strong>Explanation</strong><span>Bitwise operators act on integer representations: & (AND), | (OR), ^ (XOR), ~ (complement), << (left shift), and>> (right shift). They are useful for masks and hardware registers; shift counts and signed values require care.</span></div>
                    <div class="section-label">Example</div>
                    <div class="code-box">
                        <pre><code>unsigned int a = 6u, b = 3u;
printf("%u %u\\n", a &amp; b, a | b);</code></pre>
                    </div>
                    <div class="result-box"><strong>What to remember</strong><span>The output is 2 7: binary 110 AND 011 is 010; OR is 111.</span></div>
                </div>
            </div>

            <div class="theory-card" id="topic-42">
                <div class="theory-header">
                    <div>
                        <h4>42. Memory Safety and Undefined Behavior</h4>
                        <p>Concepts, syntax, and worked example</p>
                    </div>
                    <div class="statement-number">TOPIC 42</div>
                </div>
                <div class="theory-body">
                    <div class="definition-box"><strong>Explanation</strong><span>Undefined behavior means the C standard imposes no requirements on what happens. Common causes include out-of-bounds access, dereferencing invalid pointers, using uninitialized values, and signed integer overflow.</span></div>
                    <div class="section-label">Example</div>
                    <div class="code-box">
                        <pre><code>int values[2] = {1, 2};
/* Valid: values[0] and values[1]. */
/* Invalid: values[2] is out of bounds. */</code></pre>
                    </div>
                    <div class="result-box"><strong>What to remember</strong><span>Use valid indices, initialize variables, check allocation and input results, and respect object lifetimes. Do not rely on the output of a program with undefined behavior.</span></div>
                </div>
            </div>

            <div class="theory-card" id="topic-43">
                <div class="theory-header">
                    <div>
                        <h4>43. Error Handling and Debugging</h4>
                        <p>Concepts, syntax, and worked example</p>
                    </div>
                    <div class="statement-number">TOPIC 43</div>
                </div>
                <div class="theory-body">
                    <div class="definition-box"><strong>Explanation</strong><span>Compiler warnings, return values, assertions, debuggers, and focused tests help find errors. Separate syntax errors, runtime failures, and logic errors; reproduce a problem with a small input and inspect state step by step.</span></div>
                    <div class="section-label">Example</div>
                    <div class="code-box">
                        <pre><code>int result = scanf("%d", &amp;value);
if (result != 1) {
    fputs("Invalid input\\n", stderr);
    return 1;
}</code></pre>
                    </div>
                    <div class="result-box"><strong>What to remember</strong><span>The program reports an input error and exits with a nonzero status if an integer was not read. The variable value must be declared before this snippet.</span></div>
                </div>
            </div>

            <div class="theory-card" id="topic-44">
                <div class="theory-header">
                    <div>
                        <h4>44. Testing and Program Design</h4>
                        <p>Concepts, syntax, and worked example</p>
                    </div>
                    <div class="statement-number">TOPIC 44</div>
                </div>
                <div class="theory-body">
                    <div class="definition-box"><strong>Explanation</strong><span>Test normal, boundary, and invalid inputs. Break large tasks into functions with clear contracts, document assumptions, and check expected output against actual output.</span></div>
                    <div class="section-label">Example</div>
                    <div class="code-box">
                        <pre><code>/* Example boundary tests for a limit of 10: */
/* test 9, 10, and 11 */</code></pre>
                    </div>
                    <div class="result-box"><strong>What to remember</strong><span>Testing values below, at, and above a boundary can reveal incorrect comparison operators such as < versus <=.</span>
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