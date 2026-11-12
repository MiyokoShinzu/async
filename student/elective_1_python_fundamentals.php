<?php
session_start();

/* =========================================================
   AUTHENTICATION
========================================================== */
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

/* =========================================================
   DATABASE CONNECTION
========================================================== */
require_once "../src/connection.php";
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
                    <h2>Python Fundamentals for Data Analytics</h2>
                    <p>Understand the foundations of Python programming, explore its real-world applications, and learn how basic programming concepts support data analysis.</p>
                </div>
            </div>

            <button type="button" class="toc-floating-button" id="tocFloatingButton" aria-controls="table-of-contents" aria-expanded="false">
                <i class="bi bi-list-ul"></i><span>Contents</span>
            </button>
            <div class="theory-card toc-floating-panel" id="table-of-contents" aria-hidden="true">
                <div class="theory-header">
                    <div>
                        <h4>Table of Contents</h4>
                        <p>Navigate through the Python fundamentals lesson.</p>
                    </div>
                    <button type="button" class="toc-minimize-button" id="tocMinimizeButton" aria-label="Minimize table of contents"><i class="bi bi-chevron-left"></i></button>
                </div>
                <div class="theory-body">
                    <div class="toc-grid">
                        <a href="#introduction"><span>01</span><strong>Introduction to Python</strong><i class="bi bi-arrow-up-right"></i></a>
                        <a href="#applications"><span>02</span><strong>Python Applications</strong><i class="bi bi-arrow-up-right"></i></a>
                        <a href="#data-analytics"><span>03</span><strong>Python for Data Analytics</strong><i class="bi bi-arrow-up-right"></i></a>
                        <a href="#environment"><span>04</span><strong>Getting Started</strong><i class="bi bi-arrow-up-right"></i></a>
                        <a href="#print"><span>05</span><strong>The print() Function</strong><i class="bi bi-arrow-up-right"></i></a>
                        <a href="#variables"><span>06</span><strong>Variables</strong><i class="bi bi-arrow-up-right"></i></a>
                        <a href="#data-types"><span>07</span><strong>Python Data Types</strong><i class="bi bi-arrow-up-right"></i></a>
                        <a href="#type-conversion"><span>08</span><strong>Type Conversion</strong><i class="bi bi-arrow-up-right"></i></a>
                        <a href="#operators"><span>09</span><strong>Python Operators</strong><i class="bi bi-arrow-up-right"></i></a>
                        <a href="#input"><span>10</span><strong>User Input</strong><i class="bi bi-arrow-up-right"></i></a>
                        <a href="#if-elif-else"><span>11</span><strong>if, elif, and else</strong><i class="bi bi-arrow-up-right"></i></a>
                        <a href="#analytics-example"><span>12</span><strong>Mini Data Analysis</strong><i class="bi bi-arrow-up-right"></i></a>
                        <a href="#common-errors"><span>13</span><strong>Common Errors</strong><i class="bi bi-arrow-up-right"></i></a>
                        <a href="#key-points"><span>14</span><strong>Key Points</strong><i class="bi bi-arrow-up-right"></i></a>
                        <a href="#learning-guide"><span>15</span><strong>Student Learning Guide</strong><i class="bi bi-arrow-up-right"></i></a>
                        <a href="#virtual-labs"><span>16</span><strong>Interactive Virtual Labs</strong><i class="bi bi-arrow-up-right"></i></a>
                        <a href="#exercises"><span>17</span><strong>Practice Exercises</strong><i class="bi bi-arrow-up-right"></i></a>
                        <a href="#review-questions"><span>18</span><strong>Review Questions</strong><i class="bi bi-arrow-up-right"></i></a>
                        <a href="#references"><span>19</span><strong>References</strong><i class="bi bi-arrow-up-right"></i></a>
                    </div>
                </div>
            </div>

            <div class="theory-card" id="introduction">
                <div class="theory-header">
                    <div>
                        <h4>1. Introduction to Python Programming</h4>
                    </div>
                    <div class="theory-badge"><i class="bi bi-code-slash"></i> PYTHON FUNDAMENTALS</div>
                </div>
                <div class="theory-body">

                    <p>Python is a high-level, general-purpose programming language known for its readable syntax and wide range of applications. It allows programmers to write instructions that a computer can execute, from simple calculations to complex data-processing systems.</p>
                    <p>Python is often recommended as a first programming language because its syntax is relatively easy to read and understand. However, it is also powerful enough for professional software development, automation, scientific computing, artificial intelligence, and data analytics.</p>
                    <div class="concept-box"><strong>Core idea</strong><span>Programming is the process of giving a computer a set of clear, logical instructions. Python provides the language and tools for writing those instructions.</span></div>

                </div>
            </div>

            <div class="theory-card" id="applications">
                <div class="theory-header">
                    <div>
                        <h4>2. Where Python Is Used: Applications Across Industries</h4>
                    </div>
                    <div class="theory-badge"><i class="bi bi-bar-chart-line"></i> REAL-WORLD USE</div>
                </div>
                <div class="theory-body">

                    <p>Python is used in many fields because it can work with files, databases, websites, devices, scientific calculations, and large datasets. The same basic programming concepts can be applied to different problems.</p>
                    <div class="application-grid">
                        <div class="application-item"><i class="bi bi-bar-chart-line"></i><strong>Data Analytics</strong><span>Clean, organize, analyze, and visualize data to discover patterns and support decisions.</span></div>
                        <div class="application-item"><i class="bi bi-robot"></i><strong>Artificial Intelligence</strong><span>Build machine-learning models, recommendation systems, and intelligent applications.</span></div>
                        <div class="application-item"><i class="bi bi-globe"></i><strong>Web Development</strong><span>Create web applications, APIs, and server-side services using frameworks such as Django and Flask.</span></div>
                        <div class="application-item"><i class="bi bi-gear"></i><strong>Automation</strong><span>Automate repetitive tasks such as file organization, report generation, and data entry.</span></div>
                        <div class="application-item"><i class="bi bi-flask"></i><strong>Scientific Computing</strong><span>Perform numerical calculations, simulations, and research computations.</span></div>
                        <div class="application-item"><i class="bi bi-cpu"></i><strong>Engineering and IoT</strong><span>Support prototyping, device communication, testing, and data collection from connected systems.</span></div>
                    </div>

                </div>
            </div>

            <div class="theory-card" id="data-analytics">
                <div class="theory-header">
                    <div>
                        <h4>3. Why Python Is Relevant to Data Analytics</h4>
                    </div>
                    <div class="theory-badge"><i class="bi bi-bar-chart-line"></i> DATA-DRIVEN DECISIONS</div>
                </div>
                <div class="theory-body">

                    <p>Data analytics involves examining data to identify useful information, answer questions, and support decisions. Python is relevant because it provides a complete set of tools for preparing, processing, analyzing, and presenting data.</p>
                    <div class="definition-list">
                        <div><strong>Data collection</strong><span>Read data from CSV files, Excel workbooks, databases, APIs, and other sources.</span></div>
                        <div><strong>Data cleaning</strong><span>Identify missing values, duplicated records, inconsistent formats, and incorrect entries.</span></div>
                        <div><strong>Data transformation</strong><span>Convert data types, create new columns, group records, and calculate useful measures.</span></div>
                        <div><strong>Data analysis</strong><span>Calculate totals, averages, percentages, relationships, and trends.</span></div>
                        <div><strong>Data visualization</strong><span>Present findings through charts and graphs that make patterns easier to understand.</span></div>
                    </div>
                    <p>Common Python libraries used in data analytics include <strong>NumPy</strong> for numerical computing, <strong>pandas</strong> for tabular data manipulation, and <strong>Matplotlib</strong> or <strong>Seaborn</strong> for visualization. Students should first understand Python fundamentals because libraries build on these basic concepts.</p>
                    <div class="result-box"><strong>Example analytics question</strong><span>“What is the average examination score of the class?” Python can store the scores in a list, calculate the total, divide by the number of students, and later use pandas to perform the same analysis on a larger dataset.</span></div>

                </div>
            </div>

            <div class="theory-card" id="environment">
                <div class="theory-header">
                    <div>
                        <h4>4. Getting Started: Python Environment and First Program</h4>
                    </div>
                    <div class="theory-badge"><i class="bi bi-bar-chart-line"></i> SETUP</div>
                </div>
                <div class="theory-body">

                    <p>To write and run Python programs, you need a Python interpreter and a code editor or development environment. Common options include the official Python distribution, Visual Studio Code, IDLE, Jupyter Notebook, and Google Colab.</p>
                    <div class="numbered-list">
                        <li>Install Python or open a Python environment such as Google Colab.</li>
                        <li>Create a new Python file and save it with the <code>.py</code> extension, for example <code>first_program.py</code>.</li>
                        <li>Write a Python instruction.</li>
                        <li>Run the program and observe the output.</li>
                    </div>
                    <div class="code-example">
                        <div class="code-example-header"><strong>Your first Python program</strong><span>PYTHON</span></div>
                        <pre><code>print("Hello, Python!")
print("Welcome to Data Analytics.")</code></pre>
                    </div>
                    <p>The <code>print()</code> function displays information in the output area. It is useful for showing messages, calculated values, and results while testing a program.</p>

                </div>
            </div>

            <div class="theory-card" id="print">
                <div class="theory-header">
                    <div>
                        <h4>5. The print() Function and Output</h4>
                    </div>
                    <div class="theory-badge"><i class="bi bi-code-slash"></i> DISPLAYING INFORMATION</div>
                </div>
                <div class="theory-body">

                    <p>The built-in <code>print()</code> function displays text, numbers, variables, and the results of expressions. Text values are written inside quotation marks.</p>
                    <div class="code-example">
                        <div class="code-example-header"><strong>Examples of print()</strong><span>PYTHON</span></div>
                        <pre><code>print("Student Information")
print(25)
print(10 + 5)

student = "Alex"
score = 92
print(student)
print("Score:", score)</code></pre>
                    </div>
                    <p>Python separates multiple arguments in <code>print()</code> with a space by default. You can also use an f-string to place variable values inside a formatted message.</p>
                    <div class="code-example">
                        <div class="code-example-header"><strong>Using an f-string</strong><span>PYTHON</span></div>
                        <pre><code>name = "Alex"
average = 91.5

print(f"{name} has an average of {average}.")</code></pre>
                    </div>

                </div>
            </div>

            <div class="theory-card" id="variables">
                <div class="theory-header">
                    <div>
                        <h4>6. Variables and Naming Rules</h4>
                    </div>
                    <div class="theory-badge"><i class="bi bi-code-slash"></i> STORING DATA</div>
                </div>
                <div class="theory-body">

                    <p>A variable is a named reference used to store a value so that a program can use or update it later. In Python, you create a variable by assigning a value with the equals sign (<code>=</code>).</p>
                    <div class="code-example">
                        <div class="code-example-header"><strong>Creating variables</strong><span>PYTHON</span></div>
                        <pre><code>student_name = "Maria"
age = 20
quiz_score = 88.5
is_enrolled = True

print(student_name)
print(age)
print(quiz_score)
print(is_enrolled)</code></pre>
                    </div>
                    <div class="section-label">Rules for naming variables</div>
                    <ul class="numbered-list">
                        <li>A variable name must begin with a letter or underscore.</li>
                        <li>It may contain letters, numbers, and underscores.</li>
                        <li>It cannot contain spaces.</li>
                        <li>It cannot be a reserved Python keyword such as <code>if</code>, <code>for</code>, or <code>class</code>.</li>
                        <li>Variable names are case-sensitive: <code>score</code> and <code>Score</code> are different names.</li>
                    </ul>
                    <div class="result-box"><strong>Good practice</strong><span>Use descriptive names such as <code>total_sales</code>, <code>student_count</code>, and <code>average_score</code>. Clear names make analytics programs easier to read and maintain.</span></div>

                </div>
            </div>

            <div class="theory-card" id="data-types">
                <div class="theory-header">
                    <div>
                        <h4>7. Python Data Types</h4>
                    </div>
                    <div class="theory-badge"><i class="bi bi-code-slash"></i> CLASSIFYING VALUES</div>
                </div>
                <div class="theory-body">

                    <p>A <strong>data type</strong> identifies what kind of value a variable holds and what operations can be performed on it. Python includes simple types such as integers and strings, as well as collection types that allow a program to store many values in one variable.</p>

                    <div class="course-info-table-wrap">
                        <table class="course-info-table">
                            <thead>
                                <tr>
                                    <th>Data type</th>
                                    <th>Description</th>
                                    <th>Example</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td><code>int</code></td>
                                    <td>Whole numbers, positive or negative</td>
                                    <td><code>students = 35</code></td>
                                </tr>
                                <tr>
                                    <td><code>float</code></td>
                                    <td>Numbers with decimal places</td>
                                    <td><code>average = 89.75</code></td>
                                </tr>
                                <tr>
                                    <td><code>str</code></td>
                                    <td>Text or a sequence of characters</td>
                                    <td><code>course = "Python"</code></td>
                                </tr>
                                <tr>
                                    <td><code>bool</code></td>
                                    <td>Logical value: <code>True</code> or <code>False</code></td>
                                    <td><code>is_passed = True</code></td>
                                </tr>
                                <tr>
                                    <td><code>list</code></td>
                                    <td>Ordered and changeable collection; allows duplicates</td>
                                    <td><code>scores = [85, 90, 92]</code></td>
                                </tr>
                                <tr>
                                    <td><code>tuple</code></td>
                                    <td>Ordered and unchangeable collection; allows duplicates</td>
                                    <td><code>coordinates = (10, 20)</code></td>
                                </tr>
                                <tr>
                                    <td><code>dict</code></td>
                                    <td>Stores values as key-value pairs; keys are unique</td>
                                    <td><code>student = {"name": "Ana"}</code></td>
                                </tr>
                                <tr>
                                    <td><code>set</code></td>
                                    <td>Unordered collection of unique values</td>
                                    <td><code>sections = {"A", "B"}</code></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <div class="section-label">7.1 Checking a value's data type</div>
                    <p>Use the built-in <code>type()</code> function to identify the data type of a value. This is useful when checking data before performing calculations or processing records.</p>
                    <div class="code-example">
                        <div class="code-example-header"><strong>Using type()</strong><span>PYTHON</span></div>
                        <pre><code>score = 95
name = "Jordan"
average = 91.25
passed = True

print(type(score))    # &lt;class 'int'&gt;
print(type(name))     # &lt;class 'str'&gt;
print(type(average))  # &lt;class 'float'&gt;
print(type(passed))   # &lt;class 'bool'&gt;</code></pre>
                    </div>

                    <div class="section-label">7.2 Lists: storing multiple values</div>
                    <p>A <strong>list</strong> is an ordered, changeable collection. It is written using square brackets <code>[]</code>, and its items are separated by commas. A list can contain numbers, strings, Boolean values, or even other lists. Lists can also contain duplicate values.</p>
                    <p>Lists are especially useful in data analytics when you need to store a group of observations, such as daily temperatures, student scores, product prices, or monthly sales.</p>
                    <div class="code-example">
                        <div class="code-example-header"><strong>Creating lists</strong><span>PYTHON</span></div>
                        <pre><code>scores = [85, 90, 78, 92, 90]
students = ["Ana", "Ben", "Carlo"]
mixed_data = ["Ana", 20, 91.5, True]
empty_list = []

print(scores)
print(students)</code></pre>
                    </div>

                    <p><strong>Accessing list items using indexes:</strong> Python uses zero-based indexing. The first item is at index <code>0</code>, the second at index <code>1</code>, and so on. Negative indexes count from the end: <code>-1</code> refers to the last item.</p>
                    <div class="code-example">
                        <div class="code-example-header"><strong>Indexing and slicing</strong><span>PYTHON</span></div>
                        <pre><code>scores = [85, 90, 78, 92, 88]

print(scores[0])     # 85 - first item
print(scores[2])     # 78 - third item
print(scores[-1])    # 88 - last item
print(scores[1:4])   # [90, 78, 92]
print(scores[:3])    # first three items
print(scores[2:])    # from index 2 to the end</code></pre>
                    </div>
                    <p>A slice follows the pattern <code>list[start:stop]</code>. The start index is included, but the stop index is excluded. This makes slicing useful for selecting a portion of a dataset.</p>

                    <p><strong>Changing, adding, and removing items:</strong> Lists are mutable, which means their contents can be changed after the list is created.</p>
                    <div class="code-example">
                        <div class="code-example-header"><strong>Common list operations</strong><span>PYTHON</span></div>
                        <pre><code>scores = [85, 90, 78]

scores[1] = 95          # Change the second item
scores.append(88)       # Add one item at the end
scores.insert(0, 92)    # Insert 92 at index 0
scores.extend([80, 87]) # Add several items

scores.remove(78)       # Remove the first matching value
last_score = scores.pop() # Remove and return the last item
del scores[0]           # Delete item at index 0

print(scores)
print("Removed item:", last_score)</code></pre>
                    </div>

                    <div class="course-info-table-wrap">
                        <table class="course-info-table">
                            <thead>
                                <tr>
                                    <th>List method/function</th>
                                    <th>Purpose</th>
                                    <th>Example</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td><code>append(x)</code></td>
                                    <td>Adds one item to the end</td>
                                    <td><code>scores.append(95)</code></td>
                                </tr>
                                <tr>
                                    <td><code>insert(i, x)</code></td>
                                    <td>Inserts an item at an index</td>
                                    <td><code>scores.insert(0, 80)</code></td>
                                </tr>
                                <tr>
                                    <td><code>extend(items)</code></td>
                                    <td>Adds all items from another iterable</td>
                                    <td><code>scores.extend([80, 90])</code></td>
                                </tr>
                                <tr>
                                    <td><code>remove(x)</code></td>
                                    <td>Removes the first matching value</td>
                                    <td><code>scores.remove(80)</code></td>
                                </tr>
                                <tr>
                                    <td><code>pop()</code></td>
                                    <td>Removes and returns an item</td>
                                    <td><code>scores.pop()</code></td>
                                </tr>
                                <tr>
                                    <td><code>sort()</code></td>
                                    <td>Sorts the list in place</td>
                                    <td><code>scores.sort()</code></td>
                                </tr>
                                <tr>
                                    <td><code>reverse()</code></td>
                                    <td>Reverses the current order</td>
                                    <td><code>scores.reverse()</code></td>
                                </tr>
                                <tr>
                                    <td><code>len(list)</code></td>
                                    <td>Returns the number of items</td>
                                    <td><code>len(scores)</code></td>
                                </tr>
                                <tr>
                                    <td><code>sum(list)</code></td>
                                    <td>Adds numeric items</td>
                                    <td><code>sum(scores)</code></td>
                                </tr>
                                <tr>
                                    <td><code>min()</code> / <code>max()</code></td>
                                    <td>Finds the smallest / largest value</td>
                                    <td><code>max(scores)</code></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <p><strong>Looping through a list:</strong> A <code>for</code> loop processes each item one at a time. This is a common pattern when analyzing a collection of records.</p>
                    <div class="code-example">
                        <div class="code-example-header"><strong>Processing every score</strong><span>PYTHON</span></div>
                        <pre><code>scores = [85, 90, 78, 92]

for score in scores:
    print("Score:", score)

average = sum(scores) / len(scores)
print("Average:", average)</code></pre>
                    </div>

                    <div class="result-box"><strong>Data analytics connection</strong><span>A list can represent a column of values, such as all recorded temperatures or quiz scores. Functions such as <code>sum()</code>, <code>len()</code>, <code>min()</code>, and <code>max()</code> help produce quick summaries. For larger datasets, libraries such as pandas provide more advanced tools.</span></div>

                    <div class="section-label">7.3 Tuples: collections that should not change</div>
                    <p>A <strong>tuple</strong> is an ordered collection that cannot be changed after it is created. Tuples are written using parentheses <code>()</code>. Like lists, they support indexing, slicing, looping, and duplicate values. Unlike lists, you cannot replace, add, or remove an item after creation.</p>
                    <p>Use a tuple when a group of values belongs together and should remain fixed, such as a coordinate, a date represented by year-month-day, or fixed configuration values.</p>
                    <div class="code-example">
                        <div class="code-example-header"><strong>Creating tuples</strong><span>PYTHON</span></div>
                        <pre><code>coordinates = (14.60, 120.98)
rgb_color = (255, 120, 50)
student_record = ("Mika", 20, "BSIT")
empty_tuple = ()

# A one-item tuple needs a comma
single_item = ("Python",)

print(coordinates)
print(student_record)</code></pre>
                    </div>

                    <p><strong>Accessing tuple items:</strong> Tuples use the same zero-based indexes and slicing rules as lists.</p>
                    <div class="code-example">
                        <div class="code-example-header"><strong>Tuple indexing and slicing</strong><span>PYTHON</span></div>
                        <pre><code>days = ("Monday", "Tuesday", "Wednesday", "Thursday")

print(days[0])     # Monday
print(days[-1])    # Thursday
print(days[1:3])   # ('Tuesday', 'Wednesday')

for day in days:
    print(day)</code></pre>
                    </div>

                    <p><strong>Tuple operations and methods:</strong> Tuples have fewer methods because they cannot be modified. You can still count occurrences and find the index of an item.</p>
                    <div class="code-example">
                        <div class="code-example-header"><strong>Working with tuples</strong><span>PYTHON</span></div>
                        <pre><code>measurements = (22.5, 23.0, 22.5, 24.1)

print(measurements.count(22.5)) # Number of occurrences
print(measurements.index(24.1)) # Position of 24.1
print(len(measurements))
print(min(measurements))
print(max(measurements))

# Tuple unpacking
latitude, longitude = (14.60, 120.98)
print(latitude)
print(longitude)</code></pre>
                    </div>

                    <p><strong>Tuple unpacking:</strong> You can assign each tuple item to a separate variable in one statement. The number of variables should match the number of items, unless extended unpacking is used.</p>
                    <div class="code-example">
                        <div class="code-example-header"><strong>Unpacking a record</strong><span>PYTHON</span></div>
                        <pre><code>student = ("Ana", 19, 94)

name, age, score = student

print("Name:", name)
print("Age:", age)
print("Score:", score)</code></pre>
                    </div>

                    <div class="result-box"><strong>Important distinction</strong><span><code>tuple[0] = 100</code> causes a <code>TypeError</code> because tuple items cannot be assigned new values. If you need to update the collection, use a list or create a new tuple.</span></div>

                    <div class="section-label">7.4 Dictionaries: storing data as key-value pairs</div>
                    <p>A <strong>dictionary</strong> stores information in pairs: a <em>key</em> and its corresponding <em>value</em>. Dictionaries are written using curly braces <code>{}</code>. Each key must be unique, and values can be of different data types. Dictionaries are changeable and are useful when information needs meaningful labels instead of numeric indexes.</p>
                    <p>For example, a student record can store a name, age, course, and score. Instead of remembering that the student's name is at index zero, you can retrieve it using the key <code>"name"</code>.</p>
                    <div class="code-example">
                        <div class="code-example-header"><strong>Creating a dictionary</strong><span>PYTHON</span></div>
                        <pre><code>student = {
    "student_id": "2026-001",
    "name": "Ana Santos",
    "age": 20,
    "course": "BSIT",
    "score": 92.5,
    "enrolled": True
}

print(student)
print(student["name"])
print(student["score"])</code></pre>
                    </div>

                    <p><strong>Accessing values safely:</strong> Use square brackets with a key or the <code>get()</code> method. The <code>get()</code> method can return a default value when a key does not exist, avoiding a <code>KeyError</code>.</p>
                    <div class="code-example">
                        <div class="code-example-header"><strong>Reading dictionary values</strong><span>PYTHON</span></div>
                        <pre><code>student = {"name": "Ana", "score": 92}

print(student["name"])
print(student.get("score"))
print(student.get("section", "Not assigned"))</code></pre>
                    </div>

                    <p><strong>Adding, updating, and deleting entries:</strong> Assign a value to a key to add a new pair or update an existing pair.</p>
                    <div class="code-example">
                        <div class="code-example-header"><strong>Modifying a dictionary</strong><span>PYTHON</span></div>
                        <pre><code>student = {
    "name": "Ana",
    "score": 92
}

student["section"] = "A"  # Add a new key-value pair
student["score"] = 95     # Update an existing value

student.update({
    "course": "BSIT",
    "year_level": 2
})

del student["section"]     # Delete a key-value pair
removed = student.pop("year_level")

print(student)
print("Removed:", removed)</code></pre>
                    </div>

                    <div class="course-info-table-wrap">
                        <table class="course-info-table">
                            <thead>
                                <tr>
                                    <th>Dictionary method</th>
                                    <th>Purpose</th>
                                    <th>Example</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td><code>get(key)</code></td>
                                    <td>Gets a value; can provide a fallback</td>
                                    <td><code>student.get("name")</code></td>
                                </tr>
                                <tr>
                                    <td><code>keys()</code></td>
                                    <td>Returns the dictionary's keys</td>
                                    <td><code>student.keys()</code></td>
                                </tr>
                                <tr>
                                    <td><code>values()</code></td>
                                    <td>Returns the dictionary's values</td>
                                    <td><code>student.values()</code></td>
                                </tr>
                                <tr>
                                    <td><code>items()</code></td>
                                    <td>Returns key-value pairs</td>
                                    <td><code>student.items()</code></td>
                                </tr>
                                <tr>
                                    <td><code>update(other)</code></td>
                                    <td>Adds or updates pairs</td>
                                    <td><code>student.update({"age": 21})</code></td>
                                </tr>
                                <tr>
                                    <td><code>pop(key)</code></td>
                                    <td>Removes a key and returns its value</td>
                                    <td><code>student.pop("age")</code></td>
                                </tr>
                                <tr>
                                    <td><code>len(dict)</code></td>
                                    <td>Counts the key-value pairs</td>
                                    <td><code>len(student)</code></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <p><strong>Looping through a dictionary:</strong> Use <code>keys()</code>, <code>values()</code>, or <code>items()</code> depending on the information you need. The <code>items()</code> method is especially useful when you need both the key and value.</p>
                    <div class="code-example">
                        <div class="code-example-header"><strong>Reading all dictionary entries</strong><span>PYTHON</span></div>
                        <pre><code>student = {
    "name": "Ana",
    "course": "BSIT",
    "score": 95
}

for key, value in student.items():
    print(key, ":", value)</code></pre>
                    </div>

                    <div class="section-label">7.5 Nested collections: lists inside dictionaries</div>
                    <p>Real datasets often contain multiple records, and each record may contain several fields. Python allows collections to be placed inside other collections. A common structure is a list of dictionaries, where each dictionary represents one record.</p>
                    <div class="code-example">
                        <div class="code-example-header"><strong>A small student dataset</strong><span>PYTHON</span></div>
                        <pre><code>students = [
    {"name": "Ana", "score": 92},
    {"name": "Ben", "score": 85},
    {"name": "Carlo", "score": 78}
]

# Access one record
print(students[0]["name"])
print(students[1]["score"])

# Process every record
for student in students:
    print(student["name"], student["score"])

# Calculate the average score
total = 0
for student in students:
    total += student["score"]

average = total / len(students)
print("Class average:", average)</code></pre>
                    </div>

                    <div class="result-box"><strong>Data analytics connection</strong><span>A list of dictionaries resembles a small table: each dictionary is a row, and each key represents a field or column. This structure is useful for understanding records before moving to tabular tools such as pandas DataFrames.</span></div>

                    <div class="section-label">7.6 Comparing lists, tuples, and dictionaries</div>
                    <div class="course-info-table-wrap">
                        <table class="course-info-table">
                            <thead>
                                <tr>
                                    <th>Feature</th>
                                    <th>List</th>
                                    <th>Tuple</th>
                                    <th>Dictionary</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>Syntax</td>
                                    <td><code>[]</code></td>
                                    <td><code>()</code></td>
                                    <td><code>{key: value}</code></td>
                                </tr>
                                <tr>
                                    <td>Organization</td>
                                    <td>Ordered items</td>
                                    <td>Ordered items</td>
                                    <td>Key-value pairs</td>
                                </tr>
                                <tr>
                                    <td>Can be changed?</td>
                                    <td>Yes</td>
                                    <td>No</td>
                                    <td>Yes</td>
                                </tr>
                                <tr>
                                    <td>Access</td>
                                    <td>By index</td>
                                    <td>By index</td>
                                    <td>By key</td>
                                </tr>
                                <tr>
                                    <td>Duplicates</td>
                                    <td>Allowed</td>
                                    <td>Allowed</td>
                                    <td>Keys are unique; values may repeat</td>
                                </tr>
                                <tr>
                                    <td>Common use</td>
                                    <td>Scores, measurements, names</td>
                                    <td>Fixed coordinates or settings</td>
                                    <td>Student profiles, labeled records</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <div class="section-label">7.7 Choosing the right collection</div>
                    <ul class="numbered-list">
                        <li>Choose a <strong>list</strong> when you need an ordered group of items that may be updated, extended, or shortened.</li>
                        <li>Choose a <strong>tuple</strong> when the values belong together and should remain unchanged.</li>
                        <li>Choose a <strong>dictionary</strong> when each value needs a meaningful key or label for retrieval.</li>
                        <li>Choose a <strong>set</strong> when you need unique values and do not need positional indexing.</li>
                    </ul>
                    <div class="code-example">
                        <div class="code-example-header"><strong>Quick collection selection example</strong><span>PYTHON</span></div>
                        <pre><code># List: a changeable collection of scores
scores = [85, 90, 95]

# Tuple: fixed location coordinates
location = (14.60, 120.98)

# Dictionary: labeled student information
student = {"name": "Ana", "score": 95}

# Set: unique course codes
course_codes = {"IT101", "IT102", "IT101"}

print(scores)
print(location)
print(student["name"])
print(course_codes) # Duplicate IT101 is stored only once</code></pre>
                    </div>

                    <p><strong>Remember:</strong> Lists, tuples, and dictionaries are not interchangeable. Their differences in mutability and access methods determine which one is appropriate for a task. In data analytics, selecting a suitable structure helps keep data organized and easier to process.</p>

                </div>
            </div>

            <div class="theory-card" id="type-conversion">
                <div class="theory-header">
                    <div>
                        <h4>8. Type Conversion and Casting</h4>
                    </div>
                    <div class="theory-badge"><i class="bi bi-code-slash"></i> CONVERTING VALUES</div>
                </div>
                <div class="theory-body">

                    <p>Type conversion changes a value from one data type to another. This is common when data is collected from users or imported from files because numeric-looking values may initially be stored as strings.</p>
                    <div class="code-example">
                        <div class="code-example-header"><strong>Common conversion functions</strong><span>PYTHON</span></div>
                        <pre><code>age_text = "21"
age = int(age_text)

price_text = "49.95"
price = float(price_text)

quantity = 5
quantity_text = str(quantity)

print(age + 1)
print(price * 2)
print("Quantity: " + quantity_text)</code></pre>
                    </div>
                    <p>Common conversion functions include <code>int()</code>, <code>float()</code>, <code>str()</code>, and <code>bool()</code>. A conversion may fail if the original value cannot be interpreted as the requested type. For example, <code>int("hello")</code> raises a <code>ValueError</code>.</p>

                </div>
            </div>

            <div class="theory-card" id="operators">
                <div class="theory-header">
                    <div>
                        <h4>9. Python Operators</h4>
                    </div>
                    <div class="theory-badge"><i class="bi bi-code-slash"></i> PERFORMING OPERATIONS</div>
                </div>
                <div class="theory-body">

                    <p>Operators are symbols or keywords that perform operations on values and variables. They are essential for calculations, comparisons, and logical decisions.</p>
                    <div class="section-label">Arithmetic operators</div>
                    <div class="course-info-table-wrap">
                        <table class="course-info-table">
                            <thead>
                                <tr>
                                    <th>Operator</th>
                                    <th>Meaning</th>
                                    <th>Example</th>
                                    <th>Result</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td><code>+</code></td>
                                    <td>Addition</td>
                                    <td><code>10 + 3</code></td>
                                    <td>13</td>
                                </tr>
                                <tr>
                                    <td><code>-</code></td>
                                    <td>Subtraction</td>
                                    <td><code>10 - 3</code></td>
                                    <td>7</td>
                                </tr>
                                <tr>
                                    <td><code>*</code></td>
                                    <td>Multiplication</td>
                                    <td><code>10 * 3</code></td>
                                    <td>30</td>
                                </tr>
                                <tr>
                                    <td><code>/</code></td>
                                    <td>Division</td>
                                    <td><code>10 / 3</code></td>
                                    <td>3.333...</td>
                                </tr>
                                <tr>
                                    <td><code>//</code></td>
                                    <td>Floor division</td>
                                    <td><code>10 // 3</code></td>
                                    <td>3</td>
                                </tr>
                                <tr>
                                    <td><code>%</code></td>
                                    <td>Modulo (remainder)</td>
                                    <td><code>10 % 3</code></td>
                                    <td>1</td>
                                </tr>
                                <tr>
                                    <td><code>**</code></td>
                                    <td>Exponentiation</td>
                                    <td><code>2 ** 3</code></td>
                                    <td>8</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="section-label">Comparison operators</div>
                    <p>Comparison operators evaluate a relationship and return either <code>True</code> or <code>False</code>.</p>
                    <div class="code-example">
                        <div class="code-example-header"><strong>Comparison examples</strong><span>PYTHON</span></div>
                        <pre><code>score = 85

print(score == 85)  # Equal to
print(score != 75)  # Not equal to
print(score > 80)   # Greater than
print(score < 90)   # Less than
print(score >= 85)  # Greater than or equal
print(score <= 100) # Less than or equal</code></pre>
                    </div>
                    <div class="section-label">Logical operators</div>
                    <p><code>and</code> is True when both conditions are True. <code>or</code> is True when at least one condition is True. <code>not</code> reverses a Boolean value.</p>
                    <div class="code-example">
                        <div class="code-example-header"><strong>Logical examples</strong><span>PYTHON</span></div>
                        <pre><code>age = 20
has_id = True

print(age >= 18 and has_id)
print(age < 18 or has_id)
print(not has_id)</code></pre>
                    </div>

                </div>
            </div>

            <div class="theory-card" id="input">
                <div class="theory-header">
                    <div>
                        <h4>10. Getting User Input</h4>
                    </div>
                    <div class="theory-badge"><i class="bi bi-code-slash"></i> INTERACTIVE PROGRAMS</div>
                </div>
                <div class="theory-body">

                    <p>The <code>input()</code> function allows a program to receive information from the user. The value returned by <code>input()</code> is a string, so convert it when you need to perform numeric calculations.</p>
                    <div class="code-example">
                        <div class="code-example-header"><strong>Input and calculation</strong><span>PYTHON</span></div>
                        <pre><code>name = input("Enter your name: ")
score1 = float(input("Enter first score: "))
score2 = float(input("Enter second score: "))

average = (score1 + score2) / 2

print(f"Hello, {name}!")
print(f"Your average is {average:.2f}")</code></pre>
                    </div>
                    <p>The format specifier <code>:.2f</code> displays a floating-point number with two digits after the decimal point.</p>

                </div>
            </div>

            <div class="theory-card" id="if-elif-else">
                <div class="theory-header">
                    <div>
                        <h4>11. Conditional Statements: if, elif, and else</h4>
                    </div>
                    <div class="theory-badge"><i class="bi bi-code-slash"></i> DECISION MAKING</div>
                </div>
                <div class="theory-body">

                    <p>Conditional statements allow a program to make decisions. A block of code runs only when its condition is satisfied. Python uses indentation to identify which statements belong to each block.</p>
                    <div class="code-example">
                        <div class="code-example-header"><strong>Basic if statement</strong><span>PYTHON</span></div>
                        <pre><code>score = 90

if score >= 75:
    print("Passed")</code></pre>
                    </div>
                    <p>Use <code>if</code> to test the first condition, <code>elif</code> (else if) to test another condition when earlier conditions are False, and <code>else</code> to handle all remaining cases.</p>
                    <div class="code-example">
                        <div class="code-example-header"><strong>if, elif, and else example</strong><span>PYTHON</span></div>
                        <pre><code>score = 87

if score >= 90:
    print("Excellent")
elif score >= 85:
    print("Very Good")
elif score >= 75:
    print("Passed")
else:
    print("Needs Improvement")</code></pre>
                    </div>
                    <div class="result-box"><strong>How Python evaluates this example</strong><span>Python checks conditions from top to bottom. Since 87 is not at least 90, it checks the next condition. Because 87 is at least 85, it prints “Very Good” and skips the remaining branches.</span></div>
                    <p><strong>Important:</strong> The order of conditions matters. Put more specific or higher-threshold conditions first when evaluating score ranges.</p>

                </div>
            </div>

            <div class="theory-card" id="analytics-example">
                <div class="theory-header">
                    <div>
                        <h4>12. Putting the Fundamentals Together: A Mini Data Analysis</h4>
                    </div>
                    <div class="theory-badge"><i class="bi bi-bar-chart-line"></i> PRACTICAL EXAMPLE</div>
                </div>
                <div class="theory-body">

                    <p>This example uses variables, a list, arithmetic operations, the <code>len()</code> function, and a conditional statement to summarize a small set of examination scores.</p>
                    <div class="code-example">
                        <div class="code-example-header"><strong>Calculate and interpret class scores</strong><span>PYTHON</span></div>
                        <pre><code>scores = [85, 92, 78, 88, 95]

total = sum(scores)
number_of_students = len(scores)
average = total / number_of_students

print("Total score:", total)
print("Number of students:", number_of_students)
print(f"Class average: {average:.2f}")

if average >= 90:
    print("Class performance: Excellent")
elif average >= 85:
    print("Class performance: Very Good")
elif average >= 75:
    print("Class performance: Satisfactory")
else:
    print("Class performance: Needs Improvement")</code></pre>
                    </div>
                    <p>This is a simple example of turning raw data into a useful summary. Larger datasets can be analyzed more efficiently with libraries such as pandas, but the underlying logic remains important.</p>

                </div>
            </div>

            <div class="theory-card" id="common-errors">
                <div class="theory-header">
                    <div>
                        <h4>13. Common Beginner Errors and Good Programming Practices</h4>
                    </div>
                    <div class="theory-badge"><i class="bi bi-bar-chart-line"></i> DEBUGGING</div>
                </div>
                <div class="theory-body">

                    <div class="troubleshooting-list">
                        <div><strong>IndentationError</strong><span>Use consistent indentation after statements such as <code>if</code>. Python commonly uses four spaces.</span></div>
                        <div><strong>SyntaxError</strong><span>Check missing quotation marks, parentheses, colons, and spelling of keywords.</span></div>
                        <div><strong>TypeError</strong><span>Check whether an operation is being performed on compatible data types. Convert input values before numeric calculations.</span></div>
                        <div><strong>NameError</strong><span>Check variable spelling and make sure the variable is defined before it is used.</span></div>
                        <div><strong>Incorrect condition order</strong><span>Arrange conditional branches carefully because Python executes only the first matching branch.</span></div>
                    </div>
                    <div class="result-box"><strong>Programming habit</strong><span>Write a small part of the program, run it, inspect the output, and correct errors before adding more code. This makes debugging easier.</span></div>

                </div>
            </div>
            <div class="reference-note" id="key-points">
                <div class="reference-note-title"><i class="bi bi-lightbulb"></i> Key Points to Remember</div>
                <div class="key-point-grid">
                    <div><strong>01</strong><span>Python is a readable, general-purpose programming language.</span></div>
                    <div><strong>02</strong><span>Python is widely used in data analytics, automation, AI, web development, and science.</span></div>
                    <div><strong>03</strong><span><code>print()</code> displays output, while <code>input()</code> receives user input.</span></div>
                    <div><strong>04</strong><span>Variables store values, and data types describe the kind of values stored.</span></div>
                    <div><strong>05</strong><span>Operators perform calculations, comparisons, and logical evaluations.</span></div>
                    <div><strong>06</strong><span>Type conversion helps prepare values for the operations a program needs.</span></div>
                    <div><strong>07</strong><span><code>if</code>, <code>elif</code>, and <code>else</code> control program decisions.</span></div>
                    <div><strong>08</strong><span>Python fundamentals provide the foundation for using data analytics libraries.</span></div>
                </div>
            </div>

            <div class="theory-card" id="learning-guide">
                <div class="theory-header">
                    <div>
                        <h4>15. Student Learning Guide</h4>
                    </div>
                    <div class="theory-badge"><i class="bi bi-bar-chart-line"></i> HOW TO PRACTICE</div>
                </div>
                <div class="theory-body">

                    <div class="learning-cycle-grid">
                        <article><span class="learning-step">01</span><strong>Read the concept</strong>
                            <p>Understand what each Python instruction does before copying the example.</p>
                        </article>
                        <article><span class="learning-step">02</span><strong>Predict the output</strong>
                            <p>Read the code and write down what you think the program will display.</p>
                        </article>
                        <article><span class="learning-step">03</span><strong>Run the code</strong>
                            <p>Execute the example and compare the actual output with your prediction.</p>
                        </article>
                        <article><span class="learning-step">04</span><strong>Modify a value</strong>
                            <p>Change a variable, operator, or condition and observe how the output changes.</p>
                        </article>
                        <article><span class="learning-step">05</span><strong>Fix an error</strong>
                            <p>Intentionally introduce a small mistake, read the error message, and correct it.</p>
                        </article>
                        <article><span class="learning-step">06</span><strong>Explain your logic</strong>
                            <p>Describe how the program processes the data and reaches its result.</p>
                        </article>
                    </div>

                </div>
            </div>


            <div class="theory-card" id="virtual-labs">
                <div class="theory-header">
                    <div>
                        <h4>16. Interactive Python Virtual Labs</h4>
                        <p>Change the values, run each activity, and observe how Python logic changes the result.</p>
                    </div>
                    <div class="theory-badge"><i class="bi bi-code-square"></i> HANDS-ON PRACTICE</div>
                </div>
                <div class="theory-body">
                    <p>These browser-based labs simulate beginner Python concepts. They do not execute Python on the server; instead, they reproduce the selected Python logic using JavaScript so you can experiment immediately. Try different numbers and text values.</p>

                    <section class="python-lab" id="lab-condition">
                        <div class="python-lab-heading"><span class="python-lab-number">LAB 01</span>
                            <div>
                                <h5>Condition and Grade Classifier</h5>
                                <p>Practice <code>if</code>, <code>elif</code>, and <code>else</code>.</p>
                            </div>
                        </div>
                        <div class="python-lab-controls">
                            <label for="labGradeInput">Enter a score (0–100)</label>
                            <input type="number" id="labGradeInput" min="0" max="100" value="88" step="1">
                            <button type="button" class="python-lab-run" id="runGradeLab">Run condition</button>
                        </div>
                        <div class="python-lab-output" id="gradeLabOutput" aria-live="polite">Your result will appear here.</div>
                        <div class="python-lab-code"><strong>Python logic:</strong>
                            <pre><code>score = 88

if score >= 90:
    result = "Excellent"
elif score >= 85:
    result = "Very Good"
elif score >= 75:
    result = "Passed"
else:
    result = "Needs Improvement"

print(result)</code></pre>
                        </div>
                    </section>

                    <section class="python-lab" id="lab-print">
                        <div class="python-lab-heading"><span class="python-lab-number">LAB 02</span>
                            <div>
                                <h5>Text and print() Playground</h5>
                                <p>Change the text variables and see how <code>print()</code> displays them.</p>
                            </div>
                        </div>
                        <div class="python-lab-controls lab-two-column">
                            <div><label for="labNameInput">Name</label><input type="text" id="labNameInput" value="Alex" maxlength="60"></div>
                            <div><label for="labCourseInput">Course or subject</label><input type="text" id="labCourseInput" value="Data Analytics" maxlength="80"></div>
                            <div class="lab-full"><label for="labMessageInput">Custom message</label><input type="text" id="labMessageInput" value="I am learning Python!" maxlength="120"></div>
                            <button type="button" class="python-lab-run" id="runPrintLab">Run print()</button>
                        </div>
                        <div class="python-lab-output python-lab-preoutput" id="printLabOutput" aria-live="polite">Hello, Alex!
                            I am studying Data Analytics.
                            I am learning Python!</div>
                        <div class="python-lab-code"><strong>Python logic:</strong>
                            <pre><code>name = "Alex"
course = "Data Analytics"
message = "I am learning Python!"

print("Hello,", name + "!")
print("I am studying", course + ".")
print(message)</code></pre>
                        </div>
                    </section>

                    <section class="python-lab" id="lab-list">
                        <div class="python-lab-heading"><span class="python-lab-number">LAB 03</span>
                            <div>
                                <h5>List and Data Summary Lab</h5>
                                <p>Edit the numbers in the list to calculate the total, count, average, minimum, and maximum.</p>
                            </div>
                        </div>
                        <div class="python-lab-controls">
                            <label for="labListInput">Enter numbers separated by commas</label>
                            <input type="text" id="labListInput" value="85, 90, 78, 92, 88" placeholder="Example: 85, 90, 78">
                            <button type="button" class="python-lab-run" id="runListLab">Analyze list</button>
                        </div>
                        <div class="python-lab-output" id="listLabOutput" aria-live="polite">Your list summary will appear here.</div>
                        <div class="python-lab-code"><strong>Python logic:</strong>
                            <pre><code>scores = [85, 90, 78, 92, 88]

total = sum(scores)
count = len(scores)
average = total / count

print("Total:", total)
print("Count:", count)
print("Average:", average)
print("Lowest:", min(scores))
print("Highest:", max(scores))</code></pre>
                        </div>
                    </section>

                    <section class="python-lab" id="lab-dictionary">
                        <div class="python-lab-heading"><span class="python-lab-number">LAB 04</span>
                            <div>
                                <h5>Dictionary Student Record Lab</h5>
                                <p>Edit the text and score fields to update a Python-style key-value record.</p>
                            </div>
                        </div>
                        <div class="python-lab-controls lab-two-column">
                            <div><label for="labDictName">Student name</label><input type="text" id="labDictName" value="Jamie Cruz" maxlength="80"></div>
                            <div><label for="labDictCourse">Course</label><input type="text" id="labDictCourse" value="BSIT" maxlength="40"></div>
                            <div><label for="labDictScore">Score</label><input type="number" id="labDictScore" value="91" min="0" max="100" step="1"></div>
                            <button type="button" class="python-lab-run" id="runDictionaryLab">Update dictionary</button>
                        </div>
                        <div class="python-lab-output python-lab-preoutput" id="dictionaryLabOutput" aria-live="polite">{
                            'name': 'Jamie Cruz',
                            'course': 'BSIT',
                            'score': 91
                            }</div>
                        <div class="python-lab-code"><strong>Python logic:</strong>
                            <pre><code>student = {
    "name": "Jamie Cruz",
    "course": "BSIT",
    "score": 91
}

print(student)
print(student["name"])</code></pre>
                        </div>
                    </section>

                    <div class="python-lab-challenge">
                        <strong>Lab challenge</strong>
                        <p>Try these changes after running the labs:</p>
                        <ol>
                            <li>Enter <code>74</code>, <code>85</code>, and <code>100</code> in the condition lab. Explain why the output changes.</li>
                            <li>Replace the print lab message with a sentence about why Python is useful in data analytics.</li>
                            <li>Use at least six numbers in the list lab. Make one value much larger and observe its effect on the average.</li>
                            <li>Change the student name, course, and score in the dictionary lab. Identify each key and its value.</li>
                        </ol>
                    </div>
                </div>
            </div>

            <div class="theory-card" id="exercises">
                <div class="theory-header">
                    <div>
                        <h4>17. Practice Exercises</h4>
                    </div>
                    <div class="theory-badge"><i class="bi bi-bar-chart-line"></i> APPLY YOUR KNOWLEDGE</div>
                </div>
                <div class="theory-body">

                    <p>Complete the following exercises using Python. Write and run each program, then save your source code and output as evidence of your work.</p>
                    <div class="definition-list">
                        <div><strong>Exercise 1 – Personal Introduction</strong><span>Use <code>print()</code> to display your name, course, year level, and a short learning goal. Store at least two details in variables.</span></div>
                        <div><strong>Exercise 2 – Basic Calculator</strong><span>Ask the user for two numbers. Display their sum, difference, product, quotient, and remainder.</span></div>
                        <div><strong>Exercise 3 – Data Type Explorer</strong><span>Create one variable each for <code>int</code>, <code>float</code>, <code>str</code>, and <code>bool</code>. Use <code>type()</code> to display each data type.</span></div>
                        <div><strong>Exercise 4 – Average Score Calculator</strong><span>Ask for three examination scores, calculate the average, and display the result to two decimal places.</span></div>
                        <div><strong>Exercise 5 – Pass or Fail</strong><span>Ask for a student's score. Use <code>if</code> and <code>else</code> to display “Passed” if the score is at least 75, otherwise display “Failed”.</span></div>
                        <div><strong>Exercise 6 – Grade Description</strong><span>Use <code>if</code>, <code>elif</code>, and <code>else</code> to classify a score: 90–100 Excellent, 85–89 Very Good, 75–84 Passed, below 75 Needs Improvement. Validate that the score is between 0 and 100.</span></div>
                        <div><strong>Exercise 7 – Simple Data Summary</strong><span>Store at least five numerical values in a list. Calculate and display the total, number of values, and average. Use a conditional statement to describe the average.</span></div>
                    </div>

                </div>
            </div>

            <div class="theory-card" id="review-questions">
                <div class="theory-header">
                    <div>
                        <h4>18. Review Questions and Answers</h4>
                    </div>
                    <div class="theory-badge"><i class="bi bi-bar-chart-line"></i> SELF-ASSESSMENT</div>
                </div>
                <div class="theory-body">

                    <p>Try answering each question before selecting “View answer”.</p>
                    <div class="review-question-list">
                        <article class="review-question">
                            <div class="review-question-heading"><button type="button" class="answer-toggle" aria-expanded="false">View answer</button><strong>1. What is Python, and why is it commonly used by beginners?</strong></div>
                            <div class="review-answer" hidden>Python is a high-level, general-purpose programming language. Its readable syntax and broad ecosystem make it suitable for learning programming and developing real applications.</div>
                        </article>
                        <article class="review-question">
                            <div class="review-question-heading"><button type="button" class="answer-toggle" aria-expanded="false">View answer</button><strong>2. Why is Python useful in data analytics?</strong></div>
                            <div class="review-answer" hidden>It provides tools for collecting, cleaning, transforming, analyzing, and visualizing data, including libraries such as NumPy, pandas, and Matplotlib.</div>
                        </article>
                        <article class="review-question">
                            <div class="review-question-heading"><button type="button" class="answer-toggle" aria-expanded="false">View answer</button><strong>3. What is the difference between an integer and a float?</strong></div>
                            <div class="review-answer" hidden>An integer represents a whole number, while a float represents a number with a decimal component.</div>
                        </article>
                        <article class="review-question">
                            <div class="review-question-heading"><button type="button" class="answer-toggle" aria-expanded="false">View answer</button><strong>4. What does the input() function return by default?</strong></div>
                            <div class="review-answer" hidden>It returns the entered value as a string. Convert it with int() or float() when numeric calculations are required.</div>
                        </article>
                        <article class="review-question">
                            <div class="review-question-heading"><button type="button" class="answer-toggle" aria-expanded="false">View answer</button><strong>5. What is the purpose of elif in a conditional statement?</strong></div>
                            <div class="review-answer" hidden>elif allows a program to test another condition when the preceding if or elif condition is False.</div>
                        </article>
                    </div>

                </div>
            </div>

            <div class="theory-card" id="references">
                <div class="theory-header">
                    <div>
                        <h4>19. References and Further Reading</h4>
                    </div>
                    <div class="theory-badge"><i class="bi bi-bar-chart-line"></i> LEARNING RESOURCES</div>
                </div>
                <div class="theory-body">

                    <ol class="reference-list">
                        <li><strong>Python Software Foundation.</strong> <em>Python 3 Documentation.</em><span>Official Python language tutorial, library reference, and language reference. <a href="https://docs.python.org/3/" target="_blank" rel="noopener">https://docs.python.org/3/</a></span></li>
                        <li><strong>Python Software Foundation.</strong> <em>The Python Tutorial.</em><span>Introduction to Python syntax, data structures, control flow, and functions. <a href="https://docs.python.org/3/tutorial/" target="_blank" rel="noopener">https://docs.python.org/3/tutorial/</a></span></li>
                        <li><strong>Project Jupyter.</strong> <em>Jupyter Documentation.</em><span>Documentation for interactive notebooks used in programming, research, and data analysis. <a href="https://docs.jupyter.org/" target="_blank" rel="noopener">https://docs.jupyter.org/</a></span></li>
                        <li><strong>pandas Development Team.</strong> <em>pandas Documentation.</em><span>Documentation for data structures and data analysis tools in Python. <a href="https://pandas.pydata.org/docs/" target="_blank" rel="noopener">https://pandas.pydata.org/docs/</a></span></li>
                        <li><strong>NumPy Developers.</strong> <em>NumPy Documentation.</em><span>Documentation for numerical computing with Python. <a href="https://numpy.org/doc/" target="_blank" rel="noopener">https://numpy.org/doc/</a></span></li>
                    </ol>

                </div>
            </div>

            <footer class="async-lab-footer">
                <p><strong>Python Fundamentals – Data Analytics</strong></p>
                <p>Engineering and Technological Solutions</p>
                <small>&copy; <span id="labCopyrightYear"></span> All rights reserved.</small>
            </footer>

        </div>
    </main>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;600;700;800&display=swap');

        :root {
            --theory-bg: #fff;
            --theory-border: #e4e7ec;
            --theory-text: #172033;
            --theory-muted: #667085;
            --theory-secondary: #475467;
            --theory-hover: #f8fafc;
            --theory-blue: #2563eb;
            --theory-green: #16a34a;
            --theory-red: #dc2626;
            --theory-header: #f8fafc;
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
            --theory-red: #f87171;
            --theory-header: #1c2230;
        }

        body,
        button,
        input,
        select,
        textarea,
        .content-wrapper,
        .theory-card,
        .reference-note,
        .sponsored-card {
            font-family: 'Roboto', sans-serif;
        }

        .page-header h2 {
            font-family: 'Roboto', sans-serif;
        }

        .page-header p {
            font-size: .9rem;
            line-height: 1.7;
        }

        .theory-header h4 {
            font-size: 1rem;
        }

        .theory-header p {
            font-size: .76rem;
            line-height: 1.55;
        }

        .theory-body p {
            font-family: 'Roboto', sans-serif;
            font-size: .9rem;
            line-height: 1.85;
        }

        .numbered-list,
        .concept-box span,
        .definition-list span,
        .application-item span,
        .materials-list span,
        .result-box span,
        .key-point-grid span {
            font-family: 'Roboto', sans-serif;
            font-size: .84rem;
            line-height: 1.75;
        }

        .section-label {
            font-size: .84rem;
        }

        .application-item strong,
        .definition-list strong {
            font-size: .82rem;
        }

        .sponsored-card {
            margin: 18px 0;
            padding: 16px 18px;
            border: 1px solid var(--theory-border);
            border-radius: 10px;
            background: var(--theory-bg);
            color: var(--theory-text);
            overflow: hidden;
            scroll-margin-top: 90px;
        }

        .ad-label {
            display: block;
            margin-bottom: 9px;
            color: var(--theory-muted);
            font-size: .68rem;
            font-weight: 700;
            letter-spacing: .08em;
            text-align: right;
            text-transform: uppercase;
        }

        .sponsored-ad-slot {
            min-width: 0;
            min-height: 90px;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 8px;
            border: 1px dashed var(--theory-border);
            border-radius: 7px;
            background: var(--theory-hover);
            overflow: hidden;
        }

        .schematic-panel {
            margin: 14px 0 18px;
            padding: 16px;
            border: 1px solid var(--theory-border);
            border-radius: 9px;
            background: var(--theory-hover);
            overflow: hidden;
        }

        .schematic-heading {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 10px;
            margin-bottom: 10px;
            color: var(--theory-text);
            font-size: .78rem;
        }

        .schematic-heading span {
            color: var(--theory-muted);
            font-size: .68rem;
        }

        .schematic-svg {
            display: block;
            width: 100%;
            max-height: 230px;
            color: var(--theory-text);
        }

        .schematic-board {
            fill: var(--theory-bg);
            stroke: var(--theory-blue);
        }

        .schematic-component {
            stroke: var(--theory-text);
        }

        .schematic-label {
            fill: var(--theory-text);
            font-family: 'Roboto', sans-serif;
            font-size: 13px;
            font-weight: 600;
        }

        .schematic-node {
            fill: var(--theory-blue);
        }

        .schematic-caption {
            margin: 8px 0 0 !important;
            color: var(--theory-muted) !important;
            font-size: .76rem !important;
            line-height: 1.65 !important;
        }

        .reference-list {
            margin: 0;
            padding-left: 24px;
            color: var(--theory-secondary);
        }

        .reference-list li {
            padding: 10px 0 12px 5px;
            border-bottom: 1px solid var(--theory-border);
            font-size: .84rem;
            line-height: 1.7;
        }

        .reference-list li:last-child {
            border-bottom: 0;
        }

        .reference-list strong {
            color: var(--theory-text);
        }

        .reference-list em {
            color: var(--theory-secondary);
        }

        .reference-list a {
            display: block;
            width: fit-content;
            max-width: 100%;
            color: var(--theory-blue);
            overflow-wrap: anywhere;
            text-decoration: underline;
            text-underline-offset: 3px;
        }

        .reference-list span {
            display: block;
            margin-top: 4px;
            color: var(--theory-muted);
            font-size: .78rem;
        }

        .toc-floating-button {
            position: fixed;
            z-index: 1100;
            top: 88px;
            right: 24px;
            left: auto;
            display: flex;
            align-items: center;
            gap: 9px;
            padding: 12px 14px;
            border: 1px solid rgba(255, 255, 255, .42);
            border-radius: 14px;
            background: rgba(71, 85, 105, .42);
            color: var(--theory-text);
            box-shadow: 0 6px 20px rgba(15, 23, 42, .12), inset 0 1px 0 rgba(255, 255, 255, .28);
            backdrop-filter: blur(16px) saturate(155%);
            -webkit-backdrop-filter: blur(16px) saturate(155%);
            font: 700 .76rem 'Roboto', sans-serif;
            cursor: grab;
            user-select: none;
            touch-action: none;
            opacity: .78;
            transition: background .2s ease, opacity .2s ease, box-shadow .2s ease;
        }

        .toc-floating-button i {
            font-size: 1.05rem;
        }

        .toc-floating-button:hover {
            background: rgba(100, 116, 139, .58);
            opacity: .94;
            box-shadow: 0 8px 24px rgba(15, 23, 42, .16), inset 0 1px 0 rgba(255, 255, 255, .34);
        }

        .toc-floating-button.is-dragging {
            cursor: grabbing;
            opacity: .92;
        }

        .toc-floating-panel {
            position: fixed;
            z-index: 1099;
            top: 145px;
            right: 24px;
            left: auto;
            display: flex;
            flex-direction: column;
            width: min(360px, calc(100vw - 32px));
            max-height: min(78vh, 720px);
            margin: 0 !important;
            overflow: hidden;
            background: rgba(255, 255, 255, .76);
            border: 1px solid rgba(255, 255, 255, .58);
            box-shadow: 0 16px 44px rgba(15, 23, 42, .18), inset 0 1px 0 rgba(255, 255, 255, .72);
            backdrop-filter: blur(22px) saturate(165%);
            -webkit-backdrop-filter: blur(22px) saturate(165%);
            transform: translateY(-6px);
            opacity: 0;
            visibility: hidden;
            pointer-events: none;
            transition: transform .22s ease, opacity .22s ease, visibility .22s ease;
        }

        .toc-floating-panel.is-open {
            transform: translateY(0);
            opacity: 1;
            visibility: visible;
            pointer-events: auto;
        }

        .toc-floating-panel .theory-header {
            position: relative;
            z-index: 2;
            flex: 0 0 auto;
            background: rgba(255, 255, 255, .34);
            backdrop-filter: blur(14px);
            -webkit-backdrop-filter: blur(14px);
        }

        .toc-minimize-button {
            display: grid;
            place-items: center;
            width: 34px;
            height: 34px;
            flex: 0 0 34px;
            border: 1px solid var(--theory-border);
            border-radius: 7px;
            background: var(--theory-hover);
            color: var(--theory-text);
            cursor: pointer;
        }

        html[data-theme="dark"] .toc-floating-button {
            background: rgba(148, 163, 184, .22);
            color: #e2e8f0;
            border-color: rgba(203, 213, 225, .2);
        }

        html[data-theme="dark"] .toc-floating-button:hover {
            background: rgba(148, 163, 184, .34);
        }

        html[data-theme="dark"] .toc-floating-panel {
            background: rgba(15, 23, 42, .78);
            border-color: rgba(148, 163, 184, .25);
        }

        html[data-theme="dark"] .toc-floating-panel .theory-header,
        html[data-theme="dark"] .toc-floating-panel .theory-body {
            background: rgba(15, 23, 42, .32);
        }

        .toc-floating-panel .theory-body {
            flex: 1 1 auto;
            min-height: 0;
            max-height: calc(min(68vh, 620px) - 72px);
            padding: 12px;
            background: rgba(255, 255, 255, .18);
            overflow-y: auto;
            overflow-x: hidden;
            overscroll-behavior: contain;
            scrollbar-gutter: stable;
        }

        .toc-floating-panel .toc-grid {
            grid-template-columns: 1fr;
        }

        .toc-floating-panel .toc-grid a {
            min-height: 42px;
        }

        @media(max-width:700px) {
            .toc-floating-button {
                right: 12px;
                top: 82px;
                padding: 11px 12px;
                border-radius: 13px;
            }

            .toc-floating-button span {
                display: none;
            }

            .toc-floating-panel {
                right: 12px;
                left: auto;
                top: 138px;
                width: min(340px, calc(100vw - 24px));
            }
        }

        .async-lab-footer {
            margin: 32px 0 0;
            padding: 24px 18px;
            text-align: center;
            border-top: 1px solid var(--theory-border);
            color: var(--theory-muted);
            background: var(--theory-hover);
            border-radius: 12px
        }

        .async-lab-footer p {
            margin: 4px 0
        }

        .async-lab-footer small {
            display: block;
            margin-top: 10px
        }

        .review-question-heading {
            display: flex;
            flex-direction: column;
            align-items: flex-start;
            gap: 10px
        }

        .review-question-heading .answer-toggle {
            order: -1
        }

        .report-rubric-wrap {
            width: 100%;
            overflow-x: auto;
            margin: 14px 0;
            border: 1px solid var(--theory-border);
            border-radius: 8px;
        }

        .report-rubric {
            width: 100%;
            min-width: 620px;
            border-collapse: collapse;
            color: var(--theory-text);
            font-size: .78rem;
        }

        .likert-rubric {
            min-width: 1180px;
        }

        .likert-rubric th:not(:first-child),
        .likert-rubric td:not(:first-child) {
            min-width: 145px;
        }

        .likert-rubric th:first-child,
        .likert-rubric td:first-child {
            min-width: 180px;
        }

        .report-rubric th,
        .report-rubric td {
            padding: 11px 12px;
            border-bottom: 1px solid var(--theory-border);
            text-align: left;
            vertical-align: top;
            line-height: 1.55;
        }

        .report-rubric thead th {
            background: var(--theory-hover);
            color: var(--theory-text);
            font-weight: 800;
        }

        .report-rubric td:last-child,
        .report-rubric th:last-child {
            width: 72px;
            text-align: center;
            font-weight: 800;
            white-space: nowrap;
        }

        .report-rubric tbody tr:last-child td {
            border-bottom: 0;
        }

        .report-rubric tfoot th {
            background: var(--theory-hover);
            border-top: 2px solid var(--theory-border);
            border-bottom: 0;
        }

        .theory-card,
        .reference-note {
            margin-bottom: 16px;
            background: var(--theory-bg);
            border: 1px solid var(--theory-border);
            border-radius: 10px;
            overflow: hidden;
            scroll-margin-top: 90px;
        }

        .theory-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 15px;
            padding: 17px 19px;
            border-bottom: 1px solid var(--theory-border);
        }

        .theory-header h4 {
            margin: 0 0 4px;
            color: var(--theory-text);
            font-size: .92rem;
            font-weight: 700;
        }

        .theory-header p {
            margin: 0;
            color: var(--theory-muted);
            font-size: .68rem;
        }

        .theory-badge,
        .statement-number {
            padding: 6px 9px;
            border-radius: 5px;
            background: var(--theory-hover);
            color: var(--theory-blue);
            font-size: .62rem;
            font-weight: 800;
            white-space: nowrap;
        }

        .theory-body {
            padding: 19px;
        }

        .theory-body p {
            margin: 0 0 13px;
            color: var(--theory-secondary);
            font-family: 'Roboto', sans-serif;
            font-size: .82rem;
            line-height: 1.7;
        }

        .theory-body strong {
            color: var(--theory-text);
        }

        .concept-box,
        .definition-box {
            padding: 14px 16px;
            border-radius: 7px;
            background: var(--theory-hover);
        }

        .concept-box {
            display: flex;
            flex-direction: column;
            gap: 5px;
            margin-top: 16px;
            border-left: 4px solid var(--theory-blue);
        }

        .concept-box strong,
        .definition-box strong {
            font-size: .73rem;
        }

        .concept-box span,
        .definition-box span {
            color: var(--theory-muted);
            font-family: 'Roboto', sans-serif;
            font-size: .79rem;
            line-height: 1.7;
        }

        .section-label {
            margin: 21px 0 10px;
            padding-bottom: 8px;
            border-bottom: 1px solid var(--theory-border);
            color: var(--theory-blue);
            font-size: .76rem;
            font-weight: 800;
        }

        .numbered-list {
            margin: 0 0 16px;
            padding-left: 24px;
            color: var(--theory-secondary);
            font-family: 'Roboto', sans-serif;
            font-size: .8rem;
            line-height: 1.8;
        }

        .numbered-list li {
            padding-left: 5px;
            margin-bottom: 3px;
        }

        .toc-grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 9px;
        }

        .toc-grid a {
            display: flex;
            align-items: center;
            gap: 11px;
            padding: 13px 14px;
            border: 1px solid var(--theory-border);
            border-radius: 7px;
            background: var(--theory-hover);
            color: var(--theory-text);
            text-decoration: none;
            transition: .2s ease;
        }

        .toc-grid a:hover {
            border-color: var(--theory-blue);
            transform: translateY(-1px);
        }

        .toc-grid a span {
            color: var(--theory-blue);
            font-size: .68rem;
            font-weight: 800;
        }

        .toc-grid a strong {
            flex: 1;
            font-size: .72rem;
        }

        .toc-grid a i {
            color: var(--theory-muted);
            font-size: .75rem;
        }

        .application-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
        }

        .application-item {
            display: flex;
            flex-direction: column;
            gap: 6px;
            padding: 18px;
            border-right: 1px solid var(--theory-border);
            border-bottom: 1px solid var(--theory-border);
        }

        .application-item:nth-child(3n) {
            border-right: 0;
        }

        .application-item:nth-last-child(-n+3) {
            border-bottom: 0;
        }

        .application-item i {
            color: var(--theory-blue);
            font-size: 1.1rem;
        }

        .application-item strong {
            font-size: .72rem;
        }

        .application-item span {
            color: var(--theory-muted);
            font-family: 'Roboto', sans-serif;
            font-size: .74rem;
            line-height: 1.5;
        }

        .result-box {
            display: flex;
            flex-direction: column;
            gap: 6px;
            margin-top: 15px;
            padding: 14px 16px;
            border-left: 4px solid var(--theory-green);
            border-radius: 5px;
            background: rgba(22, 163, 74, .07);
        }

        .result-box strong {
            color: var(--theory-green);
            font-size: .75rem;
        }

        .result-box span {
            color: var(--theory-secondary);
            font-family: 'Roboto', sans-serif;
            font-size: .78rem;
            line-height: 1.65;
        }

        .warning-box {
            border-left-color: var(--theory-red);
            background: rgba(220, 38, 38, .06);
        }

        .warning-box strong {
            color: var(--theory-red);
        }

        .materials-list {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 10px;
            margin-top: 12px;
        }

        .materials-list span {
            display: flex;
            align-items: flex-start;
            gap: 9px;
            padding: 10px 12px;
            border: 1px solid var(--theory-border);
            border-radius: 6px;
            color: var(--theory-secondary);
            font-family: 'Roboto', sans-serif;
            font-size: .77rem;
        }

        .materials-list i {
            color: var(--theory-green);
        }

        .definition-list {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 10px;
        }

        .definition-list>div {
            display: flex;
            flex-direction: column;
            gap: 5px;
            padding: 13px 15px;
            border: 1px solid var(--theory-border);
            border-radius: 7px;
            background: var(--theory-hover);
        }

        .definition-list strong {
            color: var(--theory-blue);
            font-size: .74rem;
        }

        .definition-list span {
            color: var(--theory-secondary);
            font-family: 'Roboto', sans-serif;
            font-size: .77rem;
            line-height: 1.6;
        }

        .reference-note-title {
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 14px 17px;
            border-bottom: 1px solid var(--theory-border);
            color: var(--theory-text);
            font-size: .8rem;
            font-weight: 700;
        }

        .reference-note-title i {
            color: var(--theory-blue);
        }

        .key-point-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
        }

        .key-point-grid>div {
            display: flex;
            align-items: flex-start;
            gap: 10px;
            padding: 14px 16px;
            border-right: 1px solid var(--theory-border);
            border-bottom: 1px solid var(--theory-border);
        }

        .key-point-grid>div:nth-child(3n) {
            border-right: 0;
        }

        .key-point-grid>div:nth-last-child(-n+3) {
            border-bottom: 0;
        }

        .key-point-grid strong {
            color: var(--theory-blue);
            font-size: .67rem;
        }

        .key-point-grid span {
            color: var(--theory-muted);
            font-family: 'Roboto', sans-serif;
            font-size: .74rem;
            line-height: 1.5;
        }

        .back-to-top {
            display: flex;
            justify-content: flex-end;
            margin: 4px 0 20px;
        }

        .back-to-top a {
            display: inline-flex;
            align-items: center;
            gap: 7px;
            padding: 9px 12px;
            border: 1px solid var(--theory-border);
            border-radius: 6px;
            color: var(--theory-blue);
            font-size: .72rem;
            font-weight: 700;
            text-decoration: none;
        }

        @media (max-width: 950px) {
            .application-grid {
                grid-template-columns: repeat(2, 1fr);
            }

            .application-item:nth-child(3n) {
                border-right: 1px solid var(--theory-border);
            }

            .application-item:nth-child(2n) {
                border-right: 0;
            }

            .application-item:nth-last-child(-n+3) {
                border-bottom: 1px solid var(--theory-border);
            }

            .application-item:nth-last-child(-n+2) {
                border-bottom: 0;
            }

            .key-point-grid {
                grid-template-columns: repeat(2, 1fr);
            }

            .key-point-grid>div:nth-child(3n) {
                border-right: 1px solid var(--theory-border);
            }

            .key-point-grid>div:nth-child(2n) {
                border-right: 0;
            }

            .key-point-grid>div:nth-last-child(-n+3) {
                border-bottom: 1px solid var(--theory-border);
            }

            .key-point-grid>div:nth-last-child(-n+2) {
                border-bottom: 0;
            }
        }

        @media (max-width: 650px) {
            .sponsored-ad-slot {
                min-height: 90px;
            }

            .theory-header {
                align-items: flex-start;
                flex-direction: column;
            }

            .toc-grid,
            .definition-list,
            .materials-list,
            .application-grid,
            .key-point-grid {
                grid-template-columns: 1fr;
            }

            .application-item,
            .application-item:nth-child(2n),
            .application-item:nth-child(3n),
            .key-point-grid>div,
            .key-point-grid>div:nth-child(2n),
            .key-point-grid>div:nth-child(3n) {
                border-right: 0;
                border-bottom: 1px solid var(--theory-border);
            }

            .application-item:last-child,
            .key-point-grid>div:last-child {
                border-bottom: 0;
            }
        }


        /* COURSE OVERVIEW - COURSE INFORMATION TABLE */
        .course-info-table-wrap {
            width: 100%;
            overflow-x: auto;
            margin: 12px 0 20px;
            border: 1px solid var(--theory-border);
            border-radius: 9px;
        }

        .course-info-table {
            width: 100%;
            border-collapse: collapse;
            color: var(--theory-text);
            font-size: .82rem;
            line-height: 1.55;
        }

        .course-info-table th,
        .course-info-table td {
            padding: 12px 14px;
            border-bottom: 1px solid var(--theory-border);
            text-align: left;
            vertical-align: middle;
        }

        .course-info-table th {
            width: 28%;
            min-width: 175px;
            background: var(--theory-hover);
            color: var(--theory-secondary);
            font-weight: 750;
        }

        .course-info-table td {
            background: var(--theory-bg);
        }

        .course-info-table tr:last-child th,
        .course-info-table tr:last-child td {
            border-bottom: 0;
        }

        @media(max-width:600px) {
            .course-info-table {
                font-size: .76rem;
            }

            .course-info-table th,
            .course-info-table td {
                padding: 10px;
            }

            .course-info-table th {
                min-width: 125px;
                width: 36%;
            }
        }

        /* GRADE COMPUTATION - INFORMATIONAL DISPLAY */
        .grade-weight-grid {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 10px;
            margin: 16px 0;
        }

        .grade-weight-grid>div {
            display: flex;
            flex-direction: column;
            gap: 4px;
            padding: 14px;
            border: 1px solid var(--theory-border);
            border-radius: 9px;
            background: var(--theory-hover);
        }

        .grade-weight-grid strong {
            color: var(--theory-blue);
            font-size: 1.35rem;
        }

        .grade-weight-grid span {
            color: var(--theory-muted);
            font-size: .75rem;
        }

        .grade-formula {
            padding: 13px 15px;
            border-left: 3px solid var(--theory-blue);
            border-radius: 6px;
            background: var(--theory-hover);
            color: var(--theory-text);
            font-size: .83rem;
            line-height: 1.7;
        }

        .grade-formula strong {
            display: block;
            margin-bottom: 3px;
        }

        .grade-example-title {
            margin: 18px 0 7px;
            color: var(--theory-text);
            font-size: .95rem;
        }

        .grade-example-table-wrap {
            width: 100%;
            overflow-x: auto;
            margin: 12px 0 16px;
        }

        .grade-example-table {
            width: 100%;
            min-width: 540px;
            border-collapse: collapse;
            color: var(--theory-text);
            font-size: .78rem;
        }

        .grade-example-table th,
        .grade-example-table td {
            padding: 11px 12px;
            border: 1px solid var(--theory-border);
            text-align: left;
        }

        .grade-example-table thead th,
        .grade-example-table tfoot th {
            background: var(--theory-hover);
            color: var(--theory-secondary);
            font-weight: 700;
        }

        .grade-example-table tbody td strong,
        .grade-example-table tfoot th:last-child {
            color: var(--theory-blue);
        }

        @media(max-width:650px) {
            .grade-weight-grid {
                grid-template-columns: 1fr;
            }
        }

        /* INTERACTIVE GPIO SIMULATORS */
        .simulator-panel {
            padding: 17px;
            margin: 18px 0;
            border: 1px solid var(--theory-border);
            border-radius: 10px;
            background: var(--theory-bg);
        }

        .simulator-heading {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            gap: 12px;
            margin-bottom: 15px;
        }

        .simulator-heading>div {
            display: flex;
            align-items: flex-start;
            gap: 11px;
        }

        .simulator-index {
            display: grid;
            place-items: center;
            flex: 0 0 32px;
            height: 32px;
            border-radius: 8px;
            background: var(--theory-hover);
            color: var(--theory-blue);
            font-size: .72rem;
            font-weight: 800;
        }

        .simulator-heading h5 {
            margin: 0 0 4px;
            color: var(--theory-text);
            font-size: .9rem;
        }

        .simulator-heading p {
            margin: 0;
            color: var(--theory-muted);
            font-size: .72rem;
        }

        .simulator-tag {
            padding: 5px 8px;
            border-radius: 5px;
            background: var(--theory-hover);
            color: var(--theory-blue);
            font-size: .62rem;
            font-weight: 800;
        }

        .simulator-grid {
            display: grid;
            grid-template-columns: minmax(210px, .75fr) minmax(0, 1.4fr);
            gap: 14px;
        }

        .simulator-controls,
        .sim-board {
            min-width: 0;
            padding: 15px;
            border: 1px solid var(--theory-border);
            border-radius: 8px;
            background: var(--theory-hover);
        }

        .simulator-controls label {
            display: block;
            margin: 0 0 7px;
            color: var(--theory-text);
            font-size: .72rem;
            font-weight: 700;
        }

        .simulator-controls select {
            width: 100%;
            margin-bottom: 14px;
            padding: 9px 10px;
            border: 1px solid var(--theory-border);
            border-radius: 6px;
            background: var(--theory-bg);
            color: var(--theory-text);
        }

        .sim-fixed-pin {
            display: flex;
            align-items: center;
            gap: 8px;
            min-height: 39px;
            margin: 0 0 14px;
            padding: 9px 11px;
            border: 1px solid var(--theory-border);
            border-radius: 6px;
            background: var(--theory-bg);
            color: var(--theory-blue);
            font-size: .75rem;
            font-weight: 800;
        }

        .sim-fixed-pin i {
            font-size: .9rem;
        }

        .sim-toggle-row {
            display: flex;
            gap: 7px;
            margin-bottom: 15px;
        }

        .sim-state-btn {
            flex: 1;
            padding: 10px;
            border: 1px solid var(--theory-border);
            border-radius: 6px;
            background: var(--theory-bg);
            color: var(--theory-text);
            font-weight: 800;
            cursor: pointer;
        }

        .sim-state-btn.active {
            border-color: var(--theory-blue);
            background: var(--theory-blue);
            color: #fff;
        }

        .sim-readout {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 10px;
            margin-top: 10px;
            padding: 10px;
            border: 1px solid var(--theory-border);
            border-radius: 6px;
            background: var(--theory-bg);
        }

        .sim-readout span {
            color: var(--theory-muted);
            font-size: .68rem;
        }

        .sim-readout strong {
            color: var(--theory-blue);
            font-size: .75rem;
            text-align: right;
        }

        .sim-board {
            display: grid;
            grid-template-columns: minmax(110px, .65fr) minmax(0, 1.35fr);
            gap: 13px;
            align-items: stretch;
        }

        .sim-led-area,
        .sim-input-visual,
        .sim-pwm-visual {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            gap: 9px;
            min-height: 150px;
            padding: 12px;
            border: 1px solid var(--theory-border);
            border-radius: 7px;
            background: var(--theory-bg);
            text-align: center;
        }

        .sim-led {
            width: 44px;
            height: 44px;
            border-radius: 50%;
            border: 5px solid #9ca3af;
            background: #d1d5db;
            box-shadow: inset 0 0 0 5px rgba(255, 255, 255, .25);
            transition: background .15s ease, box-shadow .15s ease;
        }

        .sim-led.on {
            border-color: #f59e0b;
            background: #facc15;
            box-shadow: 0 0 10px rgba(250, 204, 21, .65), 0 0 28px rgba(250, 204, 21, .45);
        }

        .sim-led-area strong,
        .sim-input-visual strong,
        .sim-pwm-visual strong {
            color: var(--theory-text);
            font-size: .72rem;
        }

        .sim-led-area span,
        .sim-input-visual span,
        .sim-pwm-visual span {
            color: var(--theory-muted);
            font-size: .64rem;
            line-height: 1.5;
        }

        .sim-code {
            min-width: 0;
            padding: 12px;
            border: 1px solid var(--theory-border);
            border-radius: 7px;
            background: var(--theory-bg);
            overflow-x: auto;
        }

        .sim-code>span {
            display: block;
            margin-bottom: 8px;
            color: var(--theory-muted);
            font-size: .64rem;
            font-weight: 700;
            text-transform: uppercase;
        }

        .sim-code pre {
            margin: 0;
            color: var(--theory-text);
            font: .68rem/1.65 "Courier New", monospace;
        }

        .sim-code b {
            color: var(--theory-blue);
        }

        .sim-explanation {
            margin: 12px 0 0 !important;
            padding: 11px 13px;
            border-radius: 6px;
            background: var(--theory-hover);
            color: var(--theory-secondary) !important;
            font-size: .74rem !important;
        }

        .sim-push-button {
            width: 100%;
            min-height: 48px;
            margin: 3px 0 5px;
            border: 1px solid var(--theory-blue);
            border-radius: 7px;
            background: var(--theory-bg);
            color: var(--theory-blue);
            font-size: .72rem;
            font-weight: 800;
            cursor: pointer;
            touch-action: manipulation;
        }

        .sim-push-button:active,
        .sim-push-button.pressed {
            transform: translateY(1px);
            background: var(--theory-blue);
            color: #fff;
        }

        .sim-input-indicator {
            width: 24px;
            height: 24px;
            border: 4px solid var(--theory-green);
            border-radius: 50%;
            background: #bbf7d0;
        }

        .sim-input-indicator.low {
            border-color: var(--theory-red);
            background: #fecaca;
        }

        #simPwmDuty {
            width: 100%;
            accent-color: var(--theory-blue);
            cursor: pointer;
        }

        .sim-range-labels {
            display: flex;
            justify-content: space-between;
            color: var(--theory-muted);
            font-size: .64rem;
        }

        .sim-wave-wrap {
            height: 150px;
            min-width: 0;
            padding: 8px;
            border: 1px solid var(--theory-border);
            border-radius: 7px;
            background: var(--theory-bg);
        }

        #simPwmWave {
            width: 100%;
            height: 100%;
            display: block;
        }

        @media(max-width:800px) {
            .simulator-grid {
                grid-template-columns: 1fr;
            }
        }

        @media(max-width:520px) {
            .sim-board {
                grid-template-columns: 1fr;
            }

            .simulator-heading {
                flex-direction: column;
            }
        }

        .experiment-list {
            display: grid;
            gap: 12px;
            padding-left: 24px;
            margin: 18px 0;
        }

        .experiment-list li {
            padding: 12px 14px;
            border: 1px solid var(--theory-border);
            border-radius: 8px;
            background: var(--theory-bg);
            color: var(--theory-text);
        }

        .experiment-list li::marker {
            color: var(--theory-blue);
            font-weight: 700;
        }

        .experiment-list li strong {
            display: block;
            margin-bottom: 5px;
            font-size: .9rem;
        }

        .experiment-list li span {
            display: block;
            color: var(--theory-muted);
            font-size: .82rem;
            line-height: 1.6;
        }

        /* Image preview buttons and modal for Section 9 materials */
        .material-reference-list {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(230px, 1fr));
            gap: 9px;
            margin: 12px 0 22px
        }

        .material-reference-list>span {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            padding: 10px 12px;
            border: 1px solid var(--theory-border);
            border-radius: 10px;
            background: var(--theory-hover);
            min-width: 0
        }

        .material-image-trigger {
            flex: 0 0 auto;
            width: 34px;
            height: 34px;
            border: 1px solid var(--theory-border);
            border-radius: 9px;
            background: var(--theory-bg);
            color: var(--theory-blue);
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 1rem;
            transition: background .2s, transform .2s
        }

        .material-image-trigger:hover {
            background: color-mix(in srgb, var(--theory-blue) 12%, var(--theory-bg));
            transform: translateY(-1px)
        }

        .material-image-citations {
            margin: 18px 0;
            padding: 14px 16px;
            border: 1px solid var(--theory-border);
            border-radius: 12px;
            background: var(--theory-hover);
            font-size: .82rem
        }

        .material-image-citations p {
            margin: 6px 0 8px;
            color: var(--theory-muted);
            line-height: 1.55
        }

        .material-image-citations ol {
            margin: 0;
            padding-left: 22px;
            columns: 2;
            column-gap: 28px
        }

        .material-image-citations li {
            break-inside: avoid;
            margin: 4px 0
        }

        .material-image-citations a {
            color: var(--theory-blue);
            text-decoration: underline;
            text-underline-offset: 2px
        }

        .material-image-modal {
            width: min(92vw, 760px);
            max-width: 760px;
            max-height: 88vh;
            padding: 0;
            border: 1px solid var(--theory-border);
            border-radius: 16px;
            background: var(--theory-bg);
            color: var(--theory-text);
            box-shadow: 0 24px 80px rgba(0, 0, 0, .35)
        }

        .material-image-modal::backdrop {
            background: rgba(0, 0, 0, .72);
            backdrop-filter: blur(3px)
        }

        .material-image-modal-header,
        .material-image-modal-footer {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            padding: 12px 16px
        }

        .material-image-modal-header {
            border-bottom: 1px solid var(--theory-border)
        }

        .material-image-modal-body {
            padding: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 180px
        }

        .material-image-modal-body img {
            display: block;
            max-width: 100%;
            max-height: 65vh;
            object-fit: contain;
            border-radius: 8px
        }

        .material-modal-close {
            width: 34px;
            height: 34px;
            border: 0;
            border-radius: 8px;
            background: var(--theory-hover);
            color: var(--theory-text);
            cursor: pointer
        }

        .material-image-modal-footer {
            border-top: 1px solid var(--theory-border);
            justify-content: flex-end;
            font-size: .8rem
        }

        .material-image-modal-footer a {
            color: var(--theory-blue)
        }

        @media(max-width:575px) {
            .material-reference-list {
                grid-template-columns: 1fr
            }

            .material-image-citations ol {
                columns: 1
            }

            .material-image-modal {
                width: 94vw
            }
        }

        /* Student learning support sections and answer-reveal review questions */
        .learning-cycle-grid {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 14px;
            margin: 18px 0
        }

        .learning-cycle-grid article,
        .checklist-columns>div {
            border: 1px solid var(--theory-border);
            background: var(--theory-hover);
            border-radius: 14px;
            padding: 16px;
            min-width: 0
        }

        .learning-step {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 30px;
            height: 30px;
            border-radius: 9px;
            background: color-mix(in srgb, var(--theory-blue) 12%, transparent);
            color: var(--theory-blue);
            font-weight: 800;
            font-size: .78rem;
            margin-bottom: 10px
        }

        .learning-cycle-grid strong,
        .troubleshooting-list strong {
            display: block;
            color: var(--theory-text);
            margin-bottom: 6px
        }

        .learning-cycle-grid p,
        .troubleshooting-list span {
            display: block;
            color: var(--theory-muted);
            font-size: .9rem;
            line-height: 1.6;
            margin: 0
        }

        .troubleshooting-list {
            display: grid;
            gap: 0;
            border: 1px solid var(--theory-border);
            border-radius: 14px;
            overflow: hidden;
            margin: 16px 0
        }

        .troubleshooting-list>div {
            padding: 14px 16px;
            background: var(--theory-bg);
            border-bottom: 1px solid var(--theory-border)
        }

        .troubleshooting-list>div:last-child {
            border-bottom: 0
        }

        .checklist-columns {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 14px
        }

        .checklist-columns h5 {
            margin: 0 0 10px;
            color: var(--theory-text);
            font-size: 1rem
        }

        .checklist-columns ul {
            margin: 0;
            padding-left: 20px;
            color: var(--theory-secondary);
            line-height: 1.8
        }

        .review-question-list {
            display: grid;
            gap: 12px;
            margin-top: 16px
        }

        .review-question {
            border: 1px solid var(--theory-border);
            border-radius: 13px;
            background: var(--theory-bg);
            overflow: hidden
        }

        .review-question-heading {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 14px;
            padding: 15px 16px
        }

        .review-question-heading strong {
            color: var(--theory-text);
            line-height: 1.55;
            font-size: .94rem
        }

        .guide-questions,
        .guide-question,
        .guide-questions *,
        .guide-question *,
        .question-list,
        .question-list *,
        .question-item,
        .question-item * {
            text-align: left !important
        }

        #review-questions .theory-body,
        #review-questions .review-question-list,
        #review-questions .review-question,
        #review-questions .review-question-heading,
        #review-questions .review-question-heading strong,
        #review-questions .review-answer {
            text-align: left !important
        }

        /* Keep every question prompt, instruction, and answer consistently left-aligned. */
        .review-question,
        .review-question *,
        .guide-questions,
        .guide-questions *,
        .guide-question,
        .guide-question *,
        .question-list,
        .question-list *,
        .question-item,
        .question-item *,
        .question-section,
        .question-section *,
        .question-card,
        .question-card *,
        .lab-question,
        .lab-question *,
        .experiment-question,
        .experiment-question * {
            text-align: left !important
        }

        .circuit-reference-image {
            width: 100%;
            height: 520px;
            border: 1px solid var(--theory-border);
            border-radius: 10px;
            overflow: hidden;
            background: var(--theory-bg)
        }

        .circuit-reference-image iframe {
            display: block;
            width: 100%;
            height: 100%;
            border: 0
        }

        .circuit-guidelines-list {
            line-height: 1.7
        }

        .circuit-guidelines-list li {
            margin-bottom: 9px
        }

        @media(max-width:600px) {
            .circuit-reference-image {
                height: 390px
            }
        }

        .answer-toggle {
            flex: 0 0 auto;
            border: 1px solid var(--theory-border);
            border-radius: 9px;
            background: var(--theory-hover);
            color: var(--theory-secondary);
            padding: 8px 12px;
            font: 600 .82rem inherit;
            cursor: pointer;
            transition: background .2s, border-color .2s
        }

        .answer-toggle:hover {
            border-color: var(--theory-blue);
            color: var(--theory-blue)
        }

        .review-answer {
            padding: 0 16px 16px 16px;
            color: var(--theory-secondary);
            font-size: .92rem;
            line-height: 1.65
        }

        .review-answer:not([hidden]) {
            display: block
        }

        @media(max-width:850px) {
            .learning-cycle-grid {
                grid-template-columns: repeat(2, minmax(0, 1fr))
            }
        }

        @media(max-width:600px) {

            .learning-cycle-grid,
            .checklist-columns {
                grid-template-columns: 1fr
            }

            .review-question-heading {
                align-items: flex-start;
                flex-direction: column
            }

            .answer-toggle {
                align-self: flex-start
            }
        }


        .schematic-reference-trigger {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            margin: 8px 0 18px;
            padding: 12px 17px;
            border: 1px solid var(--theory-blue);
            border-radius: 11px;
            background: var(--theory-blue);
            color: #fff;
            font: 600 .92rem inherit;
            cursor: pointer;
            transition: filter .2s, transform .2s
        }

        .schematic-reference-trigger:hover {
            filter: brightness(1.08);
            transform: translateY(-1px)
        }

        .schematic-reference-modal {
            width: min(1100px, 94vw);
            height: min(820px, 90vh);
            max-width: none;
            max-height: none;
            padding: 0;
            border: 1px solid var(--theory-border);
            border-radius: 16px;
            background: var(--theory-bg);
            color: var(--theory-text);
            box-shadow: 0 24px 80px rgba(0, 0, 0, .35);
            overflow: hidden
        }

        .schematic-reference-modal::backdrop {
            background: rgba(8, 15, 30, .72);
            backdrop-filter: blur(4px)
        }

        .schematic-modal-header,
        .schematic-modal-footer {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 14px;
            padding: 13px 17px;
            background: var(--theory-card);
            border-bottom: 1px solid var(--theory-border)
        }

        .schematic-modal-header>div {
            display: grid;
            gap: 3px
        }

        .schematic-modal-header strong {
            font-size: 1rem
        }

        .schematic-modal-header span,
        .schematic-modal-footer span {
            font-size: .82rem;
            color: var(--theory-secondary)
        }

        .schematic-modal-close {
            width: 36px;
            height: 36px;
            flex: 0 0 auto;
            border: 1px solid var(--theory-border);
            border-radius: 9px;
            background: var(--theory-bg);
            color: var(--theory-text);
            cursor: pointer
        }

        .schematic-modal-content {
            height: calc(100% - 112px);
            background: #fff
        }

        .schematic-modal-content iframe {
            display: block;
            width: 100%;
            height: 100%;
            border: 0;
            background: #fff
        }

        .schematic-modal-footer {
            border-top: 1px solid var(--theory-border);
            border-bottom: 0;
            flex-wrap: wrap
        }

        .schematic-modal-footer a {
            color: var(--theory-blue);
            font-weight: 600;
            text-decoration: none
        }

        @media(max-width:600px) {
            .schematic-reference-modal {
                width: 96vw;
                height: 92vh;
                border-radius: 12px
            }

            .schematic-modal-footer {
                align-items: flex-start;
                flex-direction: column
            }

            .schematic-modal-content {
                height: calc(100% - 145px)
            }
        }
    </style>

    <style>
        .code-example {
            border: 1px solid var(--theory-border);
            border-radius: 12px;
            overflow: hidden;
            margin: 16px 0;
            background: var(--theory-header)
        }

        .code-example-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 12px;
            padding: 11px 15px;
            border-bottom: 1px solid var(--theory-border);
            color: var(--theory-text);
            font-size: 12px
        }

        .code-example-header span {
            font-size: 10px;
            letter-spacing: .08em;
            color: var(--theory-blue);
            font-weight: 800
        }

        .code-example pre {
            margin: 0;
            padding: 17px;
            overflow-x: auto;
            color: var(--theory-text);
            font-size: 13px;
            line-height: 1.7;
            tab-size: 4
        }

        .code-example code {
            font-family: Consolas, Monaco, 'Courier New', monospace;
            white-space: pre
        }

        .theory-body code {
            font-family: Consolas, Monaco, 'Courier New', monospace;
            background: var(--theory-header);
            border: 1px solid var(--theory-border);
            border-radius: 4px;
            padding: 1px 4px;
            font-size: .92em
        }

        .code-example code {
            background: transparent;
            border: 0;
            padding: 0
        }

        .course-info-table {
            width: 100%;
            border-collapse: collapse
        }

        .course-info-table th,
        .course-info-table td {
            padding: 10px 12px;
            border-bottom: 1px solid var(--theory-border);
            text-align: left;
            vertical-align: top
        }

        .course-info-table th {
            color: var(--theory-secondary);
            font-size: 12px
        }

        .course-info-table td {
            color: var(--theory-text);
            font-size: 13px
        }

        .reference-list li {
            margin: 0 0 13px;
            padding-left: 4px
        }

        .reference-list span {
            display: block;
            color: var(--theory-muted);
            font-size: 13px;
            margin-top: 3px
        }
    </style>


    <style>
        /* Interactive Python virtual labs */
        .python-lab {
            border: 1px solid var(--theory-border);
            border-radius: 14px;
            padding: 18px;
            margin: 22px 0;
            background: var(--theory-bg)
        }

        .python-lab-heading {
            display: flex;
            align-items: flex-start;
            gap: 13px;
            margin-bottom: 16px
        }

        .python-lab-number {
            flex: 0 0 auto;
            border-radius: 8px;
            padding: 7px 9px;
            background: var(--theory-header);
            color: var(--theory-blue);
            font-size: 10px;
            font-weight: 800;
            letter-spacing: .08em
        }

        .python-lab-heading h5 {
            margin: 0 0 4px;
            color: var(--theory-text);
            font-size: 16px
        }

        .python-lab-heading p {
            margin: 0;
            color: var(--theory-muted);
            font-size: 13px
        }

        .python-lab-controls {
            display: grid;
            grid-template-columns: minmax(0, 1fr) auto;
            gap: 10px;
            align-items: end;
            margin: 12px 0
        }

        .python-lab-controls label {
            display: block;
            grid-column: 1/-1;
            margin-bottom: -5px;
            color: var(--theory-secondary);
            font-size: 12px;
            font-weight: 700
        }

        .python-lab-controls input {
            width: 100%;
            min-width: 0;
            min-height: 42px;
            border: 1px solid var(--theory-border);
            border-radius: 9px;
            padding: 9px 11px;
            background: var(--theory-header);
            color: var(--theory-text);
            font: inherit;
            font-size: 14px;
            outline: none
        }

        .python-lab-controls input:focus {
            border-color: var(--theory-blue);
            box-shadow: 0 0 0 3px color-mix(in srgb, var(--theory-blue) 14%, transparent)
        }

        .python-lab-run {
            min-height: 42px;
            border: 0;
            border-radius: 9px;
            padding: 9px 16px;
            background: var(--theory-blue);
            color: #fff;
            font-weight: 700;
            cursor: pointer;
            white-space: nowrap
        }

        .python-lab-run:hover {
            filter: brightness(1.08)
        }

        .lab-two-column {
            grid-template-columns: repeat(2, minmax(0, 1fr))
        }

        .lab-two-column>div {
            min-width: 0
        }

        .lab-two-column>div label {
            margin: 0 0 6px
        }

        .lab-two-column .lab-full {
            grid-column: 1/-1
        }

        .lab-two-column .python-lab-run {
            justify-self: start
        }

        .python-lab-output {
            min-height: 54px;
            margin: 14px 0;
            padding: 13px 15px;
            border: 1px solid var(--theory-border);
            border-left: 4px solid var(--theory-blue);
            border-radius: 9px;
            background: var(--theory-header);
            color: var(--theory-text);
            font-size: 14px;
            line-height: 1.7;
            overflow-wrap: anywhere
        }

        .python-lab-preoutput {
            white-space: pre-wrap;
            font-family: Consolas, Monaco, 'Courier New', monospace
        }

        .python-lab-output.is-error {
            border-left-color: #d97757
        }

        .python-lab-code {
            margin-top: 14px;
            color: var(--theory-secondary);
            font-size: 12px
        }

        .python-lab-code pre {
            margin: 8px 0 0;
            padding: 13px;
            border: 1px solid var(--theory-border);
            border-radius: 9px;
            overflow-x: auto;
            background: var(--theory-header);
            color: var(--theory-text);
            font-size: 12px;
            line-height: 1.6
        }

        .python-lab-code code {
            background: transparent;
            border: 0;
            padding: 0
        }

        .python-lab-challenge {
            margin-top: 20px;
            padding: 16px;
            border: 1px dashed var(--theory-blue);
            border-radius: 12px;
            background: var(--theory-header);
            color: var(--theory-text)
        }

        .python-lab-challenge p {
            margin: 6px 0
        }

        .python-lab-challenge li {
            margin: 7px 0;
            color: var(--theory-secondary)
        }

        @media(max-width:600px) {
            .python-lab {
                padding: 13px
            }

            .python-lab-controls,
            .lab-two-column {
                grid-template-columns: 1fr
            }

            .python-lab-controls label {
                grid-column: auto
            }

            .lab-two-column .lab-full {
                grid-column: auto
            }

            .python-lab-run {
                width: 100%
            }

            .python-lab-heading {
                gap: 9px
            }
        }
    </style>

    <script>
        (() => {
            const toggle = document.getElementById('tocFloatingButton');
            const panel = document.getElementById('table-of-contents');
            const minimize = document.getElementById('tocMinimizeButton');
            if (!toggle || !panel || !minimize) return;
            const setOpen = (open) => {
                panel.classList.toggle('is-open', open);
                panel.setAttribute('aria-hidden', String(!open));
                toggle.setAttribute('aria-expanded', String(open));
            };
            toggle.addEventListener('click', () => setOpen(!panel.classList.contains('is-open')));
            minimize.addEventListener('click', () => setOpen(false));
            panel.querySelectorAll('a').forEach(a => a.addEventListener('click', () => setOpen(false)));
            setOpen(false);
        })();
        document.querySelectorAll('.answer-toggle').forEach(button => {
            button.addEventListener('click', () => {
                const answer = button.closest('.review-question')?.querySelector('.review-answer');
                if (!answer) return;
                const open = answer.hidden;
                answer.hidden = !open;
                button.textContent = open ? 'Hide answer' : 'View answer';
                button.setAttribute('aria-expanded', String(open));
            });
        });

        // Interactive virtual labs: each activity mirrors the beginner Python example.
        (() => {
            const byId = id => document.getElementById(id);
            const show = (id, message, error = false) => {
                const el = byId(id);
                if (!el) return;
                el.textContent = message;
                el.classList.toggle('is-error', error);
            };
            const gradeButton = byId('runGradeLab');
            if (gradeButton) gradeButton.addEventListener('click', () => {
                const raw = byId('labGradeInput').value;
                const score = Number(raw);
                if (raw.trim() === '' || !Number.isFinite(score) || score < 0 || score > 100) {
                    show('gradeLabOutput', 'Please enter a valid score from 0 to 100.', true);
                    return;
                }
                let result;
                if (score >= 90) result = 'Excellent';
                else if (score >= 85) result = 'Very Good';
                else if (score >= 75) result = 'Passed';
                else result = 'Needs Improvement';
                show('gradeLabOutput', `score = ${score}\nResult: ${result}`);
            });
            const printButton = byId('runPrintLab');
            if (printButton) printButton.addEventListener('click', () => {
                const name = byId('labNameInput').value;
                const course = byId('labCourseInput').value;
                const message = byId('labMessageInput').value;
                show('printLabOutput', `Hello, ${name}!\nI am studying ${course}.\n${message}`);
            });
            const listButton = byId('runListLab');
            if (listButton) listButton.addEventListener('click', () => {
                const raw = byId('labListInput').value.trim();
                const parts = raw.split(',').map(v => v.trim());
                const values = parts.map(Number);
                if (!raw || parts.some(v => v === '') || values.some(v => !Number.isFinite(v))) {
                    show('listLabOutput', 'Enter valid numbers separated by commas. Example: 85, 90, 78', true);
                    return;
                }
                const total = values.reduce((a, b) => a + b, 0);
                const average = total / values.length;
                const fmt = n => Number.isInteger(n) ? String(n) : n.toFixed(2);
                show('listLabOutput', `scores = [${values.join(', ')}]\nTotal: ${fmt(total)}\nCount: ${values.length}\nAverage: ${fmt(average)}\nLowest: ${fmt(Math.min(...values))}\nHighest: ${fmt(Math.max(...values))}`);
            });
            const dictButton = byId('runDictionaryLab');
            if (dictButton) dictButton.addEventListener('click', () => {
                const name = byId('labDictName').value;
                const course = byId('labDictCourse').value;
                const raw = byId('labDictScore').value;
                const score = Number(raw);
                if (raw.trim() === '' || !Number.isFinite(score) || score < 0 || score > 100) {
                    show('dictionaryLabOutput', 'Enter a valid score from 0 to 100.', true);
                    return;
                }
                const quote = value => String(value).replace(/\\/g, '\\\\').replace(/'/g, "\\'").replace(/\n/g, '\\n');
                show('dictionaryLabOutput', `student = {\n    'name': '${quote(name)}',\n    'course': '${quote(course)}',\n    'score': ${score}\n}\n\nStudent name: ${name}\nScore: ${score}`);
            });
            // Run once on page load so every lab has a meaningful initial result.
            if (gradeButton) gradeButton.click();
            if (printButton) printButton.click();
            if (listButton) listButton.click();
            if (dictButton) dictButton.click();
        })();

        const year = document.getElementById('labCopyrightYear');
        if (year) year.textContent = new Date().getFullYear();
    </script>

    <?php include 'globals/scripts.php'; ?>
    <?php include 'globals/academic_posts_scripts.php'; ?>
</body>

</html>