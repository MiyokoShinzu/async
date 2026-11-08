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
                    <h2>Embedded Systems Laboratory – Introductory Parts</h2>
                    <p>Learn the essential laboratory rules, safety procedures, materials, and terminology used in embedded systems experiments.</p>
                </div>
            </div>

            <!-- FLOATING TABLE OF CONTENTS -->
            <button type="button" class="toc-floating-button" id="tocFloatingButton" aria-controls="table-of-contents" aria-expanded="false">
                <i class="bi bi-list-ul"></i><span>Contents</span>
            </button>
            <div class="theory-card toc-floating-panel" id="table-of-contents" aria-hidden="true">
                <div class="theory-header">
                    <div>
                        <h4>Table of Contents</h4>
                        <p>Navigate through the introductory laboratory topics.</p>
                    </div>
                    <button type="button" class="toc-minimize-button" id="tocMinimizeButton" aria-label="Minimize table of contents"><i class="bi bi-chevron-left"></i></button>
                </div>
                <div class="theory-body">
                    <div class="toc-grid">
                        <a href="#introduction"><span>01</span><strong>Introduction to the Laboratory</strong><i class="bi bi-arrow-up-right"></i></a>
                        <a href="#course-overview"><span>02</span><strong>Course Overview and Scope</strong><i class="bi bi-arrow-up-right"></i></a>
                        <a href="#learning-outcomes"><span>03</span><strong>Intended Learning Outcomes</strong><i class="bi bi-arrow-up-right"></i></a>
                        <a href="#lab-workflow"><span>04</span><strong>Laboratory Workflow and Documentation</strong><i class="bi bi-arrow-up-right"></i></a>
                        <a href="#submissions"><span>05</span><strong>Laboratory Submissions</strong><i class="bi bi-arrow-up-right"></i></a>
                        <a href="#circuit-diagrams"><span>06</span><strong>Circuit Diagrams and Schematics</strong><i class="bi bi-arrow-up-right"></i></a>
                        <a href="#gpio-simulators"><span>07</span><strong>Interactive GPIO Simulators</strong><i class="bi bi-arrow-up-right"></i></a>
                        <a href="#laboratory-rules"><span>08</span><strong>Laboratory Rules and Regulations</strong><i class="bi bi-arrow-up-right"></i></a>
                        <a href="#safety-procedures"><span>09</span><strong>Laboratory Safety Procedures</strong><i class="bi bi-arrow-up-right"></i></a>
                        <a href="#materials-equipment"><span>10</span><strong>Laboratory Materials and Equipment</strong><i class="bi bi-arrow-up-right"></i></a>
                        <a href="#definitions"><span>11</span><strong>Definition of Terms</strong><i class="bi bi-arrow-up-right"></i></a>
                        <a href="#experiments"><span>12</span><strong>List of Experiments to Be Performed</strong><i class="bi bi-arrow-up-right"></i></a>
                        <a href="#grade-computation"><span>13</span><strong>Grade Computation</strong><i class="bi bi-arrow-up-right"></i></a>
                        <a href="#key-points"><span>14</span><strong>Key Points to Remember</strong><i class="bi bi-arrow-up-right"></i></a>
                        <a href="#references"><span>15</span><strong>References</strong><i class="bi bi-arrow-up-right"></i></a>
                        <a href="#learning-guide"><span>16</span><strong>Student Learning Guide</strong><i class="bi bi-arrow-up-right"></i></a>
                        <a href="#troubleshooting-guide"><span>17</span><strong>Troubleshooting Guide</strong><i class="bi bi-arrow-up-right"></i></a>
                        <a href="#lab-checklist"><span>18</span><strong>Laboratory Checklists</strong><i class="bi bi-arrow-up-right"></i></a>
                        <a href="#review-questions"><span>19</span><strong>Review Questions and Answers</strong><i class="bi bi-arrow-up-right"></i></a>
                    </div>
                </div>
            </div>

            <!-- SECTION 01: INTRODUCTION — Introduces the laboratory subject and its overall purpose. -->
            <div class="theory-card" id="introduction">
                <div class="theory-header">
                    <div>
                        <h4>Introduction to the Embedded Systems Laboratory</h4>
                    </div>
                    <div class="theory-badge"><i class="bi bi-cpu"></i> EMBEDDED SYSTEMS</div>
                </div>
                <div class="theory-body">
                    <p>An Embedded Systems Laboratory provides students with practical experience in designing, programming, assembling, and testing electronic systems that perform specific functions. It introduces learners to microcontrollers, sensors, actuators, electronic components, and development tools used in modern embedded applications.</p>
                    <p>Throughout the laboratory course, students will perform hands-on activities using platforms such as the Arduino Uno. These activities help develop problem-solving skills, circuit-building techniques, programming knowledge, and an understanding of how hardware and software work together.</p>
                    <div class="concept-box">
                        <strong>Laboratory objective</strong>
                        <span>To develop practical skills in working with microcontrollers and electronic circuits while observing proper laboratory conduct and safety practices.</span>
                    </div>
                </div>
            </div>

            <!-- ADVERTISEMENT: Displayed immediately before Section 1 (Course Overview). -->
            <script>
                (function(kfcu) {
                    var d = document,
                        s = d.createElement('script'),
                        l = d.currentScript || d.scripts[d.scripts.length - 1];
                    s.settings = kfcu || {};
                    s.src = "\/\/juvenilechoice.com\/btX\/Vss.dpGolW0WYwWdcM\/LeZm\/9quRZhUzlqkXPoTac\/2dMuD\/cm0DMnzFcctFNdzkYvwiN\/z-QG0PM_QC";
                    s.async = true;
                    s.referrerPolicy = 'no-referrer-when-downgrade';
                    l.parentNode.insertBefore(s, l);
                })({})
            </script>

            <!-- SECTION 02: COURSE OVERVIEW — Contains the official course description, units, contact hours, prerequisites, co-requisites, and scope. -->
            <div class="theory-card" id="course-overview">
                <div class="theory-header">
                    <div>
                        <h4>1. Course Overview and Scope</h4>
                    </div>
                    <div class="statement-number">COURSE SCOPE</div>
                </div>
                <div class="theory-body">
                    <p>The Embedded Systems Laboratory complements the concepts discussed in the related lecture subject through practical exercises and guided experimentation. It introduces students to the relationship between hardware and software and provides opportunities to design, assemble, program, test, and troubleshoot microcontroller-based systems.</p>
                    <p>Using the Arduino Uno as an introductory development platform, students begin with basic circuit construction and digital input/output before progressing to analog interfacing, sensors, actuators, communication methods, and simple embedded applications. The specific sequence and depth of activities should follow the approved course syllabus and available laboratory resources.</p>

                    <div class="section-label">Course Information</div>
                    <div class="course-info-table-wrap">
                        <table class="course-info-table" aria-label="Embedded Systems Laboratory course information">
                            <tbody>
                                <tr>
                                    <th scope="row">Course Name</th>
                                    <td>Embedded Systems Laboratory</td>
                                </tr>
                                <tr>
                                    <th scope="row">Course Description</th>
                                    <td>This course will provide hands-on activities designed to advanced topics in embedded systems design using contemporary practice; interrupt-driven, reactive, real-time, object-oriented, and distributed client/server embedded systems.</td>
                                </tr>
                                <tr>
                                    <th scope="row">Number of Units for Laboratory</th>
                                    <td>1 unit</td>
                                </tr>
                                <tr>
                                    <th scope="row">Number of Contact Hours per Week</th>
                                    <td>3 hours per week</td>
                                </tr>
                                <tr>
                                    <th scope="row">Prerequisites</th>
                                    <td>Microprocessors</td>
                                </tr>
                                <tr>
                                    <th scope="row">Co-requisites</th>
                                    <td>Embedded Systems</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <div class="section-label">1.1 Main Areas of Study</div>
                    <div class="application-grid">
                        <div class="application-item"><i class="bi bi-cpu"></i><strong>Microcontroller Fundamentals</strong><span>Understand the role, basic architecture, pins, memory, and peripherals of a microcontroller.</span></div>
                        <div class="application-item"><i class="bi bi-diagram-3"></i><strong>Electronic Interfacing</strong><span>Connect and interface electronic components with a development board.</span></div>
                        <div class="application-item"><i class="bi bi-code-slash"></i><strong>Embedded Programming</strong><span>Write, compile, upload, and debug programs for microcontroller-based systems.</span></div>
                        <div class="application-item"><i class="bi bi-broadcast"></i><strong>Sensors and Actuators</strong><span>Read information from sensors and control output devices through suitable interfaces.</span></div>
                        <div class="application-item"><i class="bi bi-arrow-left-right"></i><strong>Device Communication</strong><span>Explore basic methods for exchanging data between controllers and peripheral devices.</span></div>
                        <div class="application-item"><i class="bi bi-tools"></i><strong>Testing and Troubleshooting</strong><span>Inspect circuits, identify errors, test system behavior, and improve a working design.</span></div>
                    </div>
                </div>
            </div>

            <!-- SECTION 03: LEARNING OUTCOMES — Lists the practical competencies students are expected to develop. -->
            <div class="theory-card" id="learning-outcomes">
                <div class="theory-header">
                    <div>
                        <h4>2. Intended Laboratory Learning Outcomes</h4>
                    </div>
                    <div class="statement-number">OUTCOMES</div>
                </div>
                <div class="theory-body">
                    <p>By participating in the laboratory activities, students should progressively develop the ability to work with embedded-system hardware and software. These general introductory outcomes should be aligned with the official course syllabus and instructor-approved requirements.</p>
                    <div class="definition-list">
                        <div><strong>Identify Components</strong><span>Recognize common electronic components, microcontroller boards, tools, and their basic functions.</span></div>
                        <div><strong>Apply Safety Practices</strong><span>Follow laboratory rules and apply safe procedures when handling circuits and equipment.</span></div>
                        <div><strong>Build Basic Circuits</strong><span>Assemble and inspect simple electronic circuits using a breadboard and appropriate components.</span></div>
                        <div><strong>Develop Programs</strong><span>Write, compile, upload, and make basic corrections to programs for a microcontroller.</span></div>
                        <div><strong>Interface Devices</strong><span>Connect basic input and output devices and explain how they interact with a controller.</span></div>
                        <div><strong>Test and Troubleshoot</strong><span>Use systematic testing to identify connection, component, and programming errors.</span></div>
                        <div><strong>Document Results</strong><span>Record procedures, circuit diagrams, program code, observations, and conclusions clearly.</span></div>
                        <div><strong>Work Responsibly</strong><span>Collaborate appropriately, use resources responsibly, and demonstrate academic integrity.</span></div>
                    </div>
                </div>
            </div>

            <!-- SECTION 04: LABORATORY WORKFLOW — Explains the recommended sequence for preparing, performing, documenting, and submitting laboratory work. -->
            <div class="theory-card" id="lab-workflow">
                <div class="theory-header">
                    <div>
                        <h4>3. Laboratory Workflow and Documentation</h4>
                    </div>
                    <div class="statement-number">WORKFLOW</div>
                </div>
                <div class="theory-body">
                    <p>A consistent laboratory workflow helps students complete activities safely, accurately, and efficiently. Each experiment may have specific instructions, but the following sequence provides a general guide for practical work.</p>
                    <div class="application-grid">
                        <div class="application-item"><i class="bi bi-journal-text"></i><strong>1. Read and Prepare</strong><span>Review the objectives, theory, procedure, circuit diagram, and required materials before starting.</span></div>
                        <div class="application-item"><i class="bi bi-clipboard-check"></i><strong>2. Inspect Materials</strong><span>Check the condition, ratings, and availability of components, tools, and equipment.</span></div>
                        <div class="application-item"><i class="bi bi-diagram-2"></i><strong>3. Assemble the Circuit</strong><span>Build the circuit according to the diagram while keeping power disconnected during wiring.</span></div>
                        <div class="application-item"><i class="bi bi-code-square"></i><strong>4. Program the Board</strong><span>Write or modify the program, verify the code, and upload it to the development board.</span></div>
                        <div class="application-item"><i class="bi bi-play-circle"></i><strong>5. Test and Observe</strong><span>Apply power after checking the circuit and obtaining any required approval, then record its behavior.</span></div>
                        <div class="application-item"><i class="bi bi-file-earmark-check"></i><strong>6. Document and Submit</strong><span>Save the required evidence, explain the results, clean the work area, and submit the output.</span></div>
                    </div>
                    <div class="section-label">3.1 Recommended Laboratory Report Contents</div>
                    <ol class="numbered-list">
                        <li><strong>Title and Activity Number</strong> – the name and identifying number of the experiment.</li>
                        <li><strong>Objectives</strong> – the skills or concepts the activity intends to develop.</li>
                        <li><strong>Materials and Equipment</strong> – the components, instruments, and software used.</li>
                        <li><strong>Theoretical Background</strong> – a brief explanation of the principles related to the activity.</li>
                        <li><strong>Procedure</strong> – the steps followed in performing the experiment.</li>
                        <li><strong>Circuit Diagram and Program Code</strong> – the circuit connections and relevant source code.</li>
                        <li><strong>Results and Observations</strong> – measurements, screenshots, photographs, and observed behavior, as required.</li>
                        <li><strong>Analysis and Discussion</strong> – an explanation of the results, errors, and troubleshooting performed.</li>
                        <li><strong>Conclusion</strong> – a summary of what was accomplished and learned.</li>
                    </ol>

                    <div class="section-label">3.2 Laboratory Report Grading Rubric (5-Point Likert Scale)</div>
                    <p>Evaluate each of the nine report components using the five-point Likert scale below. Select one rating per component based on the evidence submitted. Convert the rating into weighted points using the formula provided after the table.</p>
                    <div class="report-rubric-wrap">
                        <table class="report-rubric likert-rubric">
                            <thead>
                                <tr>
                                    <th>Report Component</th>
                                    <th>Weight</th>
                                    <th>5 – Excellent</th>
                                    <th>4 – Very Good</th>
                                    <th>3 – Satisfactory</th>
                                    <th>2 – Needs Improvement</th>
                                    <th>1 – Poor</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td><strong>1. Title and Activity Number</strong></td>
                                    <td>5</td>
                                    <td>Title and number are complete, accurate, and clearly presented.</td>
                                    <td>Accurate with one minor omission.</td>
                                    <td>Identifiable but has some missing details.</td>
                                    <td>Unclear or contains several errors.</td>
                                    <td>Missing or unrelated.</td>
                                </tr>
                                <tr>
                                    <td><strong>2. Objectives</strong></td>
                                    <td>10</td>
                                    <td>Specific, measurable, and fully aligned with the experiment.</td>
                                    <td>Clear and aligned, with minor gaps.</td>
                                    <td>Generally relevant but partly vague.</td>
                                    <td>Unclear, incomplete, or weakly aligned.</td>
                                    <td>Missing or unrelated to the activity.</td>
                                </tr>
                                <tr>
                                    <td><strong>3. Materials and Equipment</strong></td>
                                    <td>10</td>
                                    <td>All materials, components, tools, and software are accurately listed.</td>
                                    <td>Nearly complete; only a minor item is omitted.</td>
                                    <td>Most items are listed, with some omissions.</td>
                                    <td>Several important items are missing or inaccurate.</td>
                                    <td>Missing or largely incorrect.</td>
                                </tr>
                                <tr>
                                    <td><strong>4. Theoretical Background</strong></td>
                                    <td>10</td>
                                    <td>Accurate, relevant explanation demonstrates strong understanding.</td>
                                    <td>Accurate explanation with minor gaps.</td>
                                    <td>Basic explanation; some concepts lack clarity.</td>
                                    <td>Limited or partly inaccurate explanation.</td>
                                    <td>Missing or fundamentally incorrect.</td>
                                </tr>
                                <tr>
                                    <td><strong>5. Procedure</strong></td>
                                    <td>15</td>
                                    <td>Complete, logical, clear, and reproducible steps reflect actual work.</td>
                                    <td>Well organized with minor missing details.</td>
                                    <td>Understandable but some steps lack detail.</td>
                                    <td>Disorganized or missing several important steps.</td>
                                    <td>Missing or cannot be followed.</td>
                                </tr>
                                <tr>
                                    <td><strong>6. Circuit Diagram and Program Code</strong></td>
                                    <td>15</td>
                                    <td>Diagram and code are correct, complete, readable, and properly labeled.</td>
                                    <td>Functional and clear, with minor errors or omissions.</td>
                                    <td>Generally understandable but has noticeable issues.</td>
                                    <td>Major connection, code, or labeling errors.</td>
                                    <td>Missing, unusable, or unrelated.</td>
                                </tr>
                                <tr>
                                    <td><strong>7. Results and Observations</strong></td>
                                    <td>15</td>
                                    <td>Complete, authentic evidence and observations directly support the results.</td>
                                    <td>Relevant evidence with minor omissions.</td>
                                    <td>Some evidence is present but lacks detail.</td>
                                    <td>Insufficient, unclear, or weakly supported evidence.</td>
                                    <td>Missing or fabricated evidence.</td>
                                </tr>
                                <tr>
                                    <td><strong>8. Analysis and Discussion</strong></td>
                                    <td>15</td>
                                    <td>Insightfully interprets results and explains behavior, errors, and troubleshooting.</td>
                                    <td>Sound interpretation with minor gaps.</td>
                                    <td>Basic interpretation with limited explanation.</td>
                                    <td>Mostly descriptive, unclear, or contains errors.</td>
                                    <td>Missing or shows no meaningful analysis.</td>
                                </tr>
                                <tr>
                                    <td><strong>9. Conclusion</strong></td>
                                    <td>5</td>
                                    <td>Clearly addresses all objectives and summarizes key learning.</td>
                                    <td>Addresses objectives with minor omissions.</td>
                                    <td>Provides a general conclusion but lacks specificity.</td>
                                    <td>Weakly related to objectives or unsupported.</td>
                                    <td>Missing or unrelated.</td>
                                </tr>
                            </tbody>
                            <tfoot>
                                <tr>
                                    <th>TOTAL WEIGHT</th>
                                    <th>100</th>
                                    <th colspan="5">Choose one Likert rating for each component.</th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                    <div class="result-box"><strong>Likert rating and weighted-score computation</strong><span>5 = Excellent; 4 = Very Good; 3 = Satisfactory; 2 = Needs Improvement; 1 = Poor. Compute each component's points using: <strong>Weighted Points = (Selected Likert Rating ÷ 5) × Component Weight</strong>. Add the nine weighted scores to obtain the laboratory report score out of 100. Example: a rating of 4 for a component weighted 10 points earns (4 ÷ 5) × 10 = 8 points.</span></div>

                    <div class="result-box">
                        <strong>Documentation reminder</strong>
                        <span>Use your own observations and outputs. Give credit to references or code sources when required, and never fabricate measurements, screenshots, or experimental results.</span>
                    </div>
                </div>
            </div>

            <!-- SECTION 05: LABORATORY SUBMISSIONS — Describes the required written and visual outputs for each activity. -->
            <div class="theory-card" id="submissions">
                <div class="theory-header">
                    <div>
                        <h4>5. Laboratory Submissions and Requirements</h4>
                    </div>
                    <div class="statement-number">SUBMISSIONS</div>
                </div>
                <div class="theory-body">
                    <p>Laboratory submissions provide evidence of a student's preparation, implementation, observations, and understanding of an experiment. Each activity may require different outputs. Students must follow the instructions, format, file type, and deadline specified by the instructor.</p>

                    <div class="section-label">5.1 Laboratory Report</div>
                    <p>A laboratory report is a structured written record of the experiment. It explains what was performed, how the activity was conducted, what results were obtained, and what conclusions can be drawn from the observations.</p>
                    <div class="definition-list">
                        <div><strong>Cover Information</strong><span>Include the experiment title, activity number, student or group details, course, and submission date as instructed.</span></div>
                        <div><strong>Objectives and Background</strong><span>State the purpose of the experiment and briefly discuss the concepts involved.</span></div>
                        <div><strong>Materials and Procedure</strong><span>List the components, tools, and software used, then describe the steps actually followed.</span></div>
                        <div><strong>Results and Discussion</strong><span>Present actual observations, measurements, photographs, screenshots, and an explanation of the results.</span></div>
                        <div><strong>Conclusion</strong><span>Summarize what was achieved and relate the findings to the objectives.</span></div>
                        <div><strong>References</strong><span>Identify books, datasheets, documentation, websites, or other sources used in preparing the report.</span></div>
                    </div>

                    <div class="section-label">5.2 Circuit Diagram Submission</div>
                    <p>Submit a clear circuit diagram that shows the components used and how their terminals are electrically connected. The diagram must correspond to the circuit actually assembled during the experiment.</p>
                    <ol class="numbered-list">
                        <li>Identify the development board, components, power connections, and ground connections.</li>
                        <li>Label the relevant Arduino pins or microcontroller terminals.</li>
                        <li>Show component polarity and resistor values where applicable.</li>
                        <li>Use clear lines and avoid ambiguous or crossing connections.</li>
                        <li>Check that the drawing agrees with the physical circuit and program behavior.</li>
                    </ol>

                    <div class="section-label">5.3 Written or Hand-Drawn Schematic Diagram</div>
                    <p>When required, students must prepare a neat, hand-drawn schematic diagram using standard electronic symbols. The drawing should communicate the electrical connections rather than simply imitate the physical placement of parts on a breadboard.</p>
                    <ol class="numbered-list">
                        <li>Use a ruler and draw legible, properly connected lines.</li>
                        <li>Use recognized symbols for resistors, LEDs, switches, capacitors, power, and ground.</li>
                        <li>Write component names, reference labels, and values clearly.</li>
                        <li>Mark input/output pins and supply connections accurately.</li>
                        <li>Keep the drawing clean and include the activity title and student's required identifying information.</li>
                    </ol>

                    <div class="result-box">
                        <strong>Submission reminder</strong>
                        <span>Submit original work. Photographs and screenshots must show the student's actual circuit or program output. Do not copy another group's results or claim an experiment was completed when it was not performed.</span>
                    </div>
                </div>
            </div>

            <!-- SECTION 06: CIRCUIT DIAGRAMS — Explains schematic symbols, wiring diagrams, and circuit documentation. -->
            <div class="theory-card" id="circuit-diagrams">
                <div class="theory-header">
                    <div>
                        <h4>6. Circuit Diagrams and Schematic Fundamentals</h4>
                    </div>
                    <div class="statement-number">CIRCUITS</div>
                </div>
                <div class="theory-body">
                    <p>A circuit diagram is a visual representation of an electrical or electronic circuit. It uses symbols and connecting lines to show the components and the electrical relationships between them. In embedded systems, circuit diagrams help students understand how a microcontroller is connected to input devices, output devices, sensors, and power sources.</p>
                    <p>A schematic diagram focuses on the electrical function and connections of a circuit. A wiring or breadboard layout, on the other hand, shows more closely where physical wires and components are placed. Both representations are useful, but they serve different purposes.</p>

                    <div class="section-label">6.1 Common Schematic Symbols and Their Functions</div>
                    <div class="application-grid">
                        <div class="application-item"><i class="bi bi-lightning-charge"></i><strong>Power Supply</strong><span>Indicates the source and polarity of electrical power supplied to a circuit.</span></div>
                        <div class="application-item"><i class="bi bi-circle"></i><strong>Ground (GND)</strong><span>Identifies the common reference node or return connection in a circuit.</span></div>
                        <div class="application-item"><i class="bi bi-resistor"></i><strong>Resistor</strong><span>Represents a component that limits current or establishes voltage relationships.</span></div>
                        <div class="application-item"><i class="bi bi-lightbulb"></i><strong>LED</strong><span>Represents a light-emitting diode used as a visual output indicator.</span></div>
                        <div class="application-item"><i class="bi bi-toggle-on"></i><strong>Switch</strong><span>Represents a device that opens or closes an electrical connection.</span></div>
                        <div class="application-item"><i class="bi bi-cpu"></i><strong>Microcontroller</strong><span>Represents the controller and identifies the pins connected to external circuitry.</span></div>
                    </div>

                    <div class="section-label">6.2 Example: Arduino LED Circuit</div>
                    <p>The following simplified connection illustrates an LED controlled by an Arduino digital output. A current-limiting resistor is connected in series with the LED to limit current.</p>
                    <div class="schematic-panel">
                        <div class="schematic-heading">
                            <strong>Basic LED Output Connection</strong>
                            <span>Conceptual schematic</span>
                        </div>
                        <svg class="schematic-svg" viewBox="0 0 760 220" role="img" aria-label="Schematic showing Arduino digital pin 13 connected through a resistor and LED to ground">
                            <defs>
                                <marker id="schematicArrow" markerWidth="7" markerHeight="7" refX="5" refY="3" orient="auto">
                                    <path d="M0,0 L6,3 L0,6" fill="none" stroke="currentColor" stroke-width="1" />
                                </marker>
                            </defs>
                            <g fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                <rect x="25" y="65" width="150" height="90" rx="9" class="schematic-board" />
                                <path d="M175 110 H240" />
                                <path d="M240 110 H270" />
                                <path d="M270 95 L285 110 L270 125 L285 140 L300 125 L315 110 L300 95 L285 110" class="schematic-component" />
                                <path d="M315 110 H390" />
                                <path d="M390 110 H420" />
                                <path d="M420 91 L420 129 M420 91 L450 110 L420 129" class="schematic-component" />
                                <path d="M450 110 H500" />
                                <path d="M500 110 V145" />
                                <path d="M480 145 H520 M486 153 H514 M493 161 H507" />
                                <path d="M430 78 L447 62 M438 88 L455 72" marker-end="url(#schematicArrow)" />
                            </g>
                            <g class="schematic-label">
                                <text x="100" y="95" text-anchor="middle">ARDUINO</text>
                                <text x="100" y="119" text-anchor="middle">UNO</text>
                                <text x="100" y="139" text-anchor="middle">D13</text>
                                <text x="292" y="78" text-anchor="middle">220 Ω</text>
                                <text x="435" y="78" text-anchor="middle">LED</text>
                                <text x="500" y="184" text-anchor="middle">GND</text>
                                <text x="205" y="98" text-anchor="middle">Output</text>
                            </g>
                            <g class="schematic-node">
                                <circle cx="175" cy="110" r="4" />
                                <circle cx="500" cy="110" r="4" />
                            </g>
                        </svg>
                        <p class="schematic-caption">Arduino digital pin 13 → 220 Ω current-limiting resistor → LED anode; LED cathode → GND. Confirm the LED polarity and the board's electrical limits before wiring.</p>
                    </div>

                    <div class="section-label">6.3 Guidelines for Reading Circuit Diagrams</div>
                    <ol class="numbered-list">
                        <li>Identify the power supply and ground reference first.</li>
                        <li>Locate the microcontroller and identify the pin numbers used.</li>
                        <li>Trace each connection from one component terminal to another.</li>
                        <li>Check component symbols, labels, values, and polarity markings.</li>
                        <li>Compare the schematic with the physical wiring before applying power.</li>
                    </ol>

                    <div class="result-box warning-box">
                        <strong><i class="bi bi-exclamation-triangle"></i> Before building the circuit</strong>
                        <span>A schematic is not a substitute for checking component datasheets and board pin limits. Confirm the supply voltage, polarity, resistor values, and wiring before powering the circuit.</span>
                    </div>
                </div>
            </div>

            <!-- LAB RULES -->
            <div class="theory-card" id="laboratory-rules">
                <div class="theory-header">
                    <div>
                        <h4>7. Laboratory Rules and Regulations</h4>
                    </div>
                    <div class="statement-number">RULES</div>
                </div>
                <div class="theory-body">
                    <p>Laboratory rules and regulations establish the standards of conduct that students must follow when performing experiments and practical activities. These guidelines promote discipline, responsibility, proper equipment handling, and an organized learning environment.</p>

                    <div class="section-label">1.1 General Laboratory Rules</div>
                    <ol class="numbered-list">
                        <li>Attend laboratory sessions on time and follow the instructor's instructions.</li>
                        <li>Wear appropriate clothing and keep your working area clean and organized.</li>
                        <li>Handle all electronic components, Arduino boards, sensors, and laboratory equipment with care.</li>
                        <li>Do not use laboratory equipment without proper authorization.</li>
                        <li>Avoid eating, drinking, or placing liquids near electronic equipment.</li>
                        <li>Do not remove laboratory components, tools, or equipment without permission.</li>
                        <li>Work only on the assigned laboratory activity.</li>
                        <li>Avoid unnecessary movement, noise, and distractions during experiments.</li>
                        <li>Report damaged, missing, or malfunctioning equipment immediately.</li>
                        <li>Return all borrowed tools and materials after completing the activity.</li>
                    </ol>

                    <div class="section-label">1.2 Laboratory Equipment Guidelines</div>
                    <ol class="numbered-list">
                        <li>Inspect the Arduino Uno and other development boards before use.</li>
                        <li>Check circuit connections before supplying power.</li>
                        <li>Use appropriate resistors and electronic components when constructing circuits.</li>
                        <li>Avoid connecting wires randomly to prevent short circuits.</li>
                        <li>Do not exceed the voltage and current ratings of electronic components.</li>
                        <li>Disconnect the power supply before modifying circuit connections.</li>
                        <li>Use computers and programming software only for authorized laboratory activities.</li>
                    </ol>

                    <div class="section-label">1.3 Student Responsibilities</div>
                    <p>Students are expected to demonstrate responsibility, cooperation, honesty, and proper laboratory behavior. They must complete assigned activities, document their observations, submit laboratory outputs on time, and ensure that all equipment is properly stored after use.</p>
                </div>
            </div>

            <!-- SAFETY -->
            <div class="theory-card" id="safety-procedures">
                <div class="theory-header">
                    <div>
                        <h4>8. Laboratory Safety Procedures</h4>
                    </div>
                    <div class="statement-number">SAFETY</div>
                </div>
                <div class="theory-body">
                    <p>Laboratory safety procedures are essential practices designed to prevent accidents, equipment damage, and electrical hazards during laboratory activities. Since embedded systems experiments involve electrical circuits, electronic components, and powered development boards, students must understand the proper safety measures before performing any experiment.</p>

                    <div class="section-label">2.1 Electrical Safety</div>
                    <ol class="numbered-list">
                        <li>Always disconnect the power supply before assembling or modifying a circuit.</li>
                        <li>Never touch exposed electrical connections with wet hands.</li>
                        <li>Verify the power supply voltage before connecting it to a circuit.</li>
                        <li>Avoid short-circuiting the power and ground terminals.</li>
                        <li>Never apply a voltage higher than the rated input of a microcontroller pin.</li>
                        <li>Use proper insulation when working with electrical connections.</li>
                        <li>Do not work with damaged power cables, adapters, or electrical equipment.</li>
                    </ol>

                    <div class="section-label">2.2 Electronic Component Safety</div>
                    <ol class="numbered-list">
                        <li>Handle integrated circuits and microcontrollers carefully to prevent electrostatic discharge (ESD).</li>
                        <li>Observe the correct polarity when connecting LEDs, capacitors, and other polarized components.</li>
                        <li>Avoid bending or damaging component pins.</li>
                        <li>Use current-limiting resistors when connecting LEDs.</li>
                        <li>Check component specifications before integrating them into a circuit.</li>
                        <li>Store sensitive electronic components in appropriate containers.</li>
                    </ol>

                    <div class="section-label">2.3 Computer and Equipment Safety</div>
                    <ol class="numbered-list">
                        <li>Ensure that computers and laboratory equipment are properly grounded.</li>
                        <li>Do not disconnect USB cables while uploading programs unless necessary.</li>
                        <li>Keep liquids away from computers, keyboards, and electronic equipment.</li>
                        <li>Shut down computers properly after laboratory activities.</li>
                        <li>Report overheating, unusual odors, sparks, or damaged equipment immediately.</li>
                    </ol>

                    <div class="section-label">2.4 Emergency Procedures</div>
                    <ol class="numbered-list">
                        <li>Immediately disconnect the power source if an electrical problem occurs and it is safe to do so.</li>
                        <li>Inform the laboratory instructor about any accident or equipment malfunction.</li>
                        <li>Do not attempt to repair damaged electrical equipment without authorization.</li>
                        <li>Know the location of emergency exits, first-aid kits, and fire extinguishers.</li>
                        <li>In case of an electrical fire, do not use water. Alert the instructor and follow the laboratory emergency procedures.</li>
                    </ol>

                    <div class="result-box warning-box">
                        <strong><i class="bi bi-exclamation-triangle"></i> Important Safety Reminder</strong>
                        <span>Never connect 12 V directly to an Arduino Uno GPIO pin. Arduino Uno digital pins use 5 V logic levels. Applying a voltage beyond the pin's permitted limits can permanently damage the microcontroller. Always check the board documentation and use an appropriate driver circuit when controlling higher-voltage loads.</span>
                    </div>
                </div>
            </div>

            <!-- ADVERTISEMENT PLACEMENT -->
            <div class="sponsored-card" aria-label="Advertisement">
                <div class="ad-label">Advertisement</div>
                <div class="sponsored-ad-slot">
                    <script>
                        (function(kfcu) {
                            var d = document,
                                s = d.createElement('script'),
                                l = d.currentScript || d.scripts[d.scripts.length - 1];
                            s.settings = kfcu || {};
                            s.src = "\/\/juvenilechoice.com\/bZXwVPs.dzGel\/0uY\/WncN\/ee_mu9EuAZAUblek\/P_Tuc\/2vMuD\/cp3RNGDcUXt\/NCzRYGwkNqzXc_0NOgQv";
                            s.async = true;
                            s.referrerPolicy = 'no-referrer-when-downgrade';
                            l.parentNode.insertBefore(s, l);
                        })({})
                    </script>
                </div>
            </div>

            <!-- MATERIALS -->
            <div class="theory-card" id="materials-equipment">
                <div class="theory-header">
                    <div>
                        <h4>9. Laboratory Materials and Equipment</h4>
                    </div>
                    <div class="statement-number">MATERIALS</div>
                </div>
                <div class="theory-body">
                    <p>Laboratory materials and equipment are essential tools used in designing, assembling, programming, and testing embedded systems. Students will become familiar with electronic components, development boards, circuit-building tools, measuring instruments, and programming environments.</p>

                    <div class="section-label">3.1 Microcontroller and Development Boards</div>
                    <div class="material-photo-grid">
                        <article class="material-photo-card">
                            <div class="material-photo"><img src="https://images.unsplash.com/photo-1634452021892-eaabbdc4a0bf?auto=format&fit=crop&w=900&q=85" alt="Arduino Uno R3 microcontroller development board" loading="lazy"></div>
                            <div class="material-photo-content"><strong>Arduino Uno R3</strong>
                                <p>A microcontroller development board used for programming, controlling electronic components, and implementing basic embedded applications.</p><a href="https://unsplash.com/photos/a-close-up-of-a-piece-of-electronic-equipment-aro_CIkEhNw" target="_blank" rel="noopener noreferrer">Image source: Unsplash (Unsplash License)</a>
                            </div>
                        </article>
                        <article class="material-photo-card">
                            <div class="material-photo"><img src="https://commons.wikimedia.org/wiki/Special:FilePath/USB-cable.jpg?width=900" alt="USB cable used to connect a development board to a computer" loading="lazy"></div>
                            <div class="material-photo-content"><strong>USB Cable</strong>
                                <p>Connects the Arduino Uno to a computer for program uploading and, where supported, board power.</p><a href="https://commons.wikimedia.org/wiki/File:USB-cable.jpg" target="_blank" rel="noopener noreferrer">Image source: Wikimedia Commons</a>
                            </div>
                        </article>
                        <article class="material-photo-card">
                            <div class="material-photo"><img src="https://commons.wikimedia.org/wiki/Special:FilePath/Bench_power_supply.jpg?width=900" alt="Adjustable bench power supply for electronics testing" loading="lazy"></div>
                            <div class="material-photo-content"><strong>Power Supply</strong>
                                <p>Provides the required electrical power for embedded system circuits and experiments. Use only a compatible, correctly rated supply.</p><a href="https://commons.wikimedia.org/wiki/File:Bench_power_supply.jpg" target="_blank" rel="noopener noreferrer">Image source: Wikimedia Commons</a>
                            </div>
                        </article>
                    </div>

                    <div class="section-label">3.2 Electronic Components</div>
                    <div class="material-photo-grid">
                        <article class="material-photo-card">
                            <div class="material-photo"><img src="https://commons.wikimedia.org/wiki/Special:FilePath/Electronics-White-Breadboard.jpg?width=700" alt="Solderless electronics breadboard" loading="lazy"></div>
                            <div class="material-photo-content"><strong>Breadboard</strong>
                                <p>Used for assembling temporary electronic circuits without soldering.</p><a href="https://commons.wikimedia.org/wiki/File:Electronics-White-Breadboard.jpg" target="_blank" rel="noopener noreferrer">Image source: Wikimedia Commons (Public Domain)</a>
                            </div>
                        </article>
                        <article class="material-photo-card">
                            <div class="material-photo"><img src="https://commons.wikimedia.org/wiki/Special:FilePath/Resistor,_LED,_Capacitor.jpg?width=900" alt="Resistor, LED, and capacitor electronic components" loading="lazy"></div>
                            <div class="material-photo-content"><strong>Resistors</strong>
                                <p>Limit current and help control voltage in electronic circuits.</p><a href="https://commons.wikimedia.org/wiki/File:Resistor,_LED,_Capacitor.jpg" target="_blank" rel="noopener noreferrer">Image source: Wikimedia Commons</a>
                            </div>
                        </article>
                        <article class="material-photo-card">
                            <div class="material-photo"><img src="https://commons.wikimedia.org/wiki/Special:FilePath/Electronic-Component-Red-LED.jpg?width=900" alt="LED component" loading="lazy"></div>
                            <div class="material-photo-content"><strong>LEDs</strong>
                                <p>Provide visual indicators for circuit outputs.</p><a href="https://commons.wikimedia.org/wiki/File:Electronic-Component-Red-LED.jpg" target="_blank" rel="noopener noreferrer">Image source: Wikimedia Commons (CC BY-SA 4.0)</a>
                            </div>
                        </article>
                        <article class="material-photo-card">
                            <div class="material-photo"><img src="https://commons.wikimedia.org/wiki/Special:FilePath/Cross_section_of_a_low-cost_pushbutton_switch.png?width=900" alt="Electronic circuit with input components and jumper connections" loading="lazy"></div>
                            <div class="material-photo-content"><strong>Push Buttons</strong>
                                <p>Provide manual input signals to a microcontroller.</p><a href="https://commons.wikimedia.org/wiki/File:Cross_section_of_a_low-cost_pushbutton_switch.png" target="_blank" rel="noopener noreferrer">Image source: Wikimedia Commons (CC BY-SA 4.0)</a>
                            </div>
                        </article>
                        <article class="material-photo-card">
                            <div class="material-photo"><img src="https://commons.wikimedia.org/wiki/Special:FilePath/Jumper_Wires_with_Crocodile_Clips.jpg?width=900" alt="A collection of jumper wires" loading="lazy"></div>
                            <div class="material-photo-content"><strong>Jumper Wires</strong>
                                <p>Connect electronic components and development board pins.</p><a href="https://commons.wikimedia.org/wiki/File:Jumper_Wires_with_Crocodile_Clips.jpg" target="_blank" rel="noopener noreferrer">Image source: Wikimedia Commons</a>
                            </div>
                        </article>
                        <article class="material-photo-card">
                            <div class="material-photo"><img src="https://commons.wikimedia.org/wiki/Special:FilePath/Bench_power_supply.jpg?width=900" alt="Electrolytic capacitor component" loading="lazy"></div>
                            <div class="material-photo-content"><strong>Capacitors</strong>
                                <p>Store electrical charge and assist in filtering and timing circuits.</p><a href="https://commons.wikimedia.org/wiki/File:Bench_power_supply.jpg" target="_blank" rel="noopener noreferrer">Image source: Wikimedia Commons</a>
                            </div>
                        </article>
                    </div>

                    <div class="section-label">3.3 Measuring and Testing Instruments</div>
                    <div class="material-photo-grid">
                        <article class="material-photo-card">
                            <div class="material-photo"><img src="https://commons.wikimedia.org/wiki/Special:FilePath/Digital_Multimeter_(To_measure_Voltage,_Current_and_Resistance).jpg?width=900" alt="Digital multimeter for measuring voltage current and resistance" loading="lazy"></div>
                            <div class="material-photo-content"><strong>Digital Multimeter</strong>
                                <p>Measures voltage, current, resistance, and electrical continuity.</p><a href="https://commons.wikimedia.org/wiki/File:Digital_Multimeter_(To_measure_Voltage,_Current_and_Resistance).jpg" target="_blank" rel="noopener noreferrer">Image source: Wikimedia Commons category</a>
                            </div>
                        </article>
                        <article class="material-photo-card">
                            <div class="material-photo"><img src="https://commons.wikimedia.org/wiki/Special:FilePath/Digital_oscilloscope_in_use.jpg?width=900" alt="Digital oscilloscope displaying an electrical waveform" loading="lazy"></div>
                            <div class="material-photo-content"><strong>Oscilloscope</strong>
                                <p>Displays electrical signals and allows students to observe waveform characteristics.</p><a href="https://commons.wikimedia.org/wiki/File:Digital_oscilloscope_in_use.jpg" target="_blank" rel="noopener noreferrer">Image source: Wikimedia Commons</a>
                            </div>
                        </article>
                    </div>

                    <div class="section-label">3.4 Software and Development Tools</div>
                    <div class="material-photo-grid">
                        <article class="material-photo-card">
                            <div class="material-photo"><img src="https://commons.wikimedia.org/wiki/Special:FilePath/Arduino_IDE.png?width=700" alt="Arduino integrated development environment" loading="lazy"></div>
                            <div class="material-photo-content"><strong>Arduino IDE</strong>
                                <p>Used to write, compile, and upload programs to the Arduino Uno.</p><a href="https://commons.wikimedia.org/wiki/Category:Arduino_IDE" target="_blank" rel="noopener noreferrer">Image source: Wikimedia Commons category</a>
                            </div>
                        </article>
                        <article class="material-photo-card">
                            <div class="material-photo"><img src="https://commons.wikimedia.org/wiki/Special:FilePath/Laptop_computer.jpg?width=700" alt="Laptop computer used for programming" loading="lazy"></div>
                            <div class="material-photo-content"><strong>Desktop Computer or Laptop</strong>
                                <p>Used for programming, circuit documentation, simulation, and laboratory exercises.</p><a href="https://commons.wikimedia.org/wiki/Category:Laptop_computers" target="_blank" rel="noopener noreferrer">Image source: Wikimedia Commons category</a>
                            </div>
                        </article>
                    </div>

                    <div class="section-label">3.5 Additional Laboratory Supplies</div>
                    <div class="materials-list">
                        <span><i class="bi bi-check2-circle"></i> Wire cutters and wire strippers</span>
                        <span><i class="bi bi-check2-circle"></i> Long-nose pliers</span>
                        <span><i class="bi bi-check2-circle"></i> Screwdrivers</span>
                        <span><i class="bi bi-check2-circle"></i> Component organizers</span>
                        <span><i class="bi bi-check2-circle"></i> Electrical tape</span>
                        <span><i class="bi bi-check2-circle"></i> Anti-static wrist strap</span>
                        <span><i class="bi bi-check2-circle"></i> Laboratory notebook</span>
                        <span><i class="bi bi-check2-circle"></i> Printed circuit diagrams and activity sheets</span>
                    </div>
                    <p class="materials-image-note">Photographs are sourced from Wikimedia Commons. Each image links to its source page for licensing and attribution details. Some photos illustrate a group of related components rather than a single isolated item.</p>
                </div>
            </div>

            <!-- DEFINITIONS -->
            <div class="theory-card" id="definitions">
                <div class="theory-header">
                    <div>
                        <h4>10. Definition of Terms</h4>
                    </div>
                    <div class="statement-number">GLOSSARY</div>
                </div>
                <div class="theory-body">
                    <p>The following terms are commonly used when working with microcontrollers, electronic circuits, and embedded applications. Understanding these terms will help students follow laboratory instructions and interpret experimental results.</p>
                    <div class="definition-list">
                        <div><strong>Embedded System</strong><span>A combination of hardware and software designed to perform a specific function within a larger system or device.</span></div>
                        <div><strong>Microcontroller</strong><span>An integrated circuit that contains a processor, memory, and input/output peripherals used to control electronic systems.</span></div>
                        <div><strong>Arduino Uno</strong><span>A microcontroller development board based on the ATmega328P, commonly used for learning electronics and embedded programming.</span></div>
                        <div><strong>GPIO</strong><span>General Purpose Input/Output; configurable microcontroller pins used to read input signals or control external devices.</span></div>
                        <div><strong>Sensor</strong><span>An electronic device that detects a physical condition, such as temperature, light, distance, or motion, and produces a corresponding signal.</span></div>
                        <div><strong>Actuator</strong><span>A device that converts an electrical control signal into a physical action, such as movement, sound, or switching.</span></div>
                        <div><strong>Breadboard</strong><span>A reusable prototyping board that allows electronic components to be connected without soldering.</span></div>
                        <div><strong>Voltage</strong><span>The electrical potential difference between two points, measured in volts (V).</span></div>
                        <div><strong>Current</strong><span>The rate of flow of electric charge through a conductor, measured in amperes (A).</span></div>
                        <div><strong>Resistance</strong><span>The opposition to the flow of electric current, measured in ohms (Ω).</span></div>
                        <div><strong>Short Circuit</strong><span>An unintended low-resistance connection between points in a circuit that can cause excessive current flow.</span></div>
                        <div><strong>Polarity</strong><span>The positive and negative orientation of a component or power connection.</span></div>
                        <div><strong>Electrostatic Discharge (ESD)</strong><span>The sudden transfer of static electrical charge between objects, which may damage sensitive electronic components.</span></div>
                        <div><strong>Integrated Circuit (IC)</strong><span>An electronic component containing interconnected circuit elements fabricated on a semiconductor material.</span></div>
                        <div><strong>Arduino IDE</strong><span>An integrated development environment used to write, verify, and upload sketches to compatible Arduino boards.</span></div>
                        <div><strong>GPIO Logic Level</strong><span>The voltage range interpreted by a digital input or produced by a digital output as LOW or HIGH.</span></div>
                    </div>
                </div>
            </div>

            <!-- INTERACTIVE GPIO SIMULATORS -->
            <div class="theory-card" id="gpio-simulators">
                <div class="theory-header">
                    <div>
                        <h4>7. Interactive GPIO Simulators</h4>
                    </div>
                    <div class="theory-badge"><i class="bi bi-sliders"></i> VIRTUAL LAB</div>
                </div>
                <div class="theory-body">
                    <p>Use these browser-based tools to observe how an Arduino Uno can control an output, read an input, and generate a pulse-width-modulated signal. These are educational simulations; they do not connect to or control a physical board.</p>
                    <div class="simulator-panel">
                        <div class="simulator-heading">
                            <div><span class="simulator-index">01</span>
                                <div>
                                    <h5>Digital Output Simulator</h5>
                                    <p>Simulate an Arduino digital pin controlling an LED.</p>
                                </div>
                            </div><span class="simulator-tag">OUTPUT</span>
                        </div>
                        <div class="simulator-grid">
                            <div class="simulator-controls">
                                <label>Arduino digital output pin</label>
                                <div class="sim-fixed-pin"><i class="bi bi-pin"></i> Digital Pin 13 (D13)</div>
                                <label>Output state</label>
                                <div class="sim-toggle-row"><button type="button" class="sim-state-btn active" data-output="LOW" aria-pressed="true">LOW</button><button type="button" class="sim-state-btn" data-output="HIGH" aria-pressed="false">HIGH</button></div>
                                <div class="sim-readout"><span>digitalWrite() value</span><strong id="simOutputValue">LOW (0)</strong></div>
                            </div>
                            <div class="sim-board">
                                <div class="sim-led-area">
                                    <div class="sim-led" id="simLed" aria-label="LED off"></div><strong id="simLedState">LED OFF</strong><span id="simOutputVoltage">Pin D13: approximately 0 V</span>
                                </div>
                                <div class="sim-code"><span>Arduino example – D13</span>
                                    <pre><code>const int ledPin = 13;

void setup() {
  pinMode(ledPin, OUTPUT);
}

void loop() {
  digitalWrite(ledPin, <b id="simOutputCode">LOW</b>);
}</code></pre>
                                </div>
                            </div>
                            <p class="sim-explanation" id="simOutputExplain">LOW sets the simulated output pin to logic 0. The LED is off.</p>
                        </div>
                    </div>
                    <div class="simulator-panel">
                        <div class="simulator-heading">
                            <div><span class="simulator-index">02</span>
                                <div>
                                    <h5>Digital Input Simulator</h5>
                                    <p>Simulate a push button connected to an Arduino input using INPUT_PULLUP.</p>
                                </div>
                            </div><span class="simulator-tag">INPUT</span>
                        </div>
                        <div class="simulator-grid">
                            <div class="simulator-controls">
                                <label>Arduino digital input pin</label>
                                <div class="sim-fixed-pin"><i class="bi bi-pin"></i> Digital Pin 2 (D2)</div>
                                <label>Push button</label><button type="button" id="simPushButton" class="sim-push-button" aria-pressed="false">PRESS AND HOLD</button>
                                <div class="sim-readout"><span>digitalRead() result</span><strong id="simInputValue">HIGH (1)</strong></div>
                            </div>
                            <div class="sim-board">
                                <div class="sim-input-visual">
                                    <div class="sim-input-indicator" id="simInputIndicator"></div><strong id="simInputState">BUTTON RELEASED</strong><span id="simInputMeaning">Input reads HIGH because INPUT_PULLUP is enabled.</span>
                                </div>
                                <div class="sim-code"><span>Arduino example – D2</span>
                                    <pre><code>const int buttonPin = 2;

void setup() {
  pinMode(buttonPin, INPUT_PULLUP);
}

void loop() {
  int state = digitalRead(buttonPin);
  // Pressed = LOW
}</code></pre>
                                </div>
                            </div>
                            <p class="sim-explanation">With INPUT_PULLUP, the input is normally HIGH. Pressing the button connects the input to GND, so the reading becomes LOW. This is called active-low logic.</p>
                        </div>
                    </div>
                    <div class="simulator-panel">
                        <div class="simulator-heading">
                            <div><span class="simulator-index">03</span>
                                <div>
                                    <h5>PWM Simulator</h5>
                                    <p>Adjust duty cycle and observe the output pulse and LED brightness.</p>
                                </div>
                            </div><span class="simulator-tag">PWM</span>
                        </div>
                        <div class="simulator-grid">
                            <div class="simulator-controls">
                                <label>Arduino PWM output pin</label>
                                <div class="sim-fixed-pin"><i class="bi bi-pin"></i> Digital Pin 3 (D3 – PWM)</div>
                                <label for="simPwmDuty">Duty cycle: <strong id="simPwmDutyLabel">50%</strong></label><input type="range" id="simPwmDuty" min="0" max="100" step="1" value="50">
                                <div class="sim-range-labels"><span>0%</span><span>100%</span></div>
                                <div class="sim-readout"><span>analogWrite() value</span><strong id="simPwmValue">128 / 255</strong></div>
                                <div class="sim-readout"><span>Average output (ideal, 5 V logic)</span><strong id="simPwmAverage">2.50 V</strong></div>
                            </div>
                            <div class="sim-board">
                                <div class="sim-pwm-visual">
                                    <div class="sim-led sim-pwm-led" id="simPwmLed"></div><strong id="simPwmBrightness">50% brightness (illustrative)</strong><span>Repeated HIGH and LOW pulses</span>
                                </div>
                                <div class="sim-wave-wrap"><canvas id="simPwmWave" role="img" aria-label="PWM waveform showing HIGH and LOW intervals"></canvas></div>
                            </div>
                        </div>
                        <p class="sim-explanation" id="simPwmExplain">At 50% duty cycle, the signal is HIGH for half of each period and LOW for the other half. PWM is not a true steady analog voltage.</p>
                        <div class="result-box"><strong>Arduino Uno note</strong><span>The Uno R3 provides PWM on pins 3, 5, 6, 9, 10, and 11. The 8-bit analogWrite() range is 0–255. A duty cycle of 50% corresponds approximately to a value of 128. Actual PWM frequency depends on the selected pin and timer configuration.</span></div>
                    </div>
                    <div class="result-box warning-box"><strong>Simulation and hardware safety</strong><span>These controls only update the on-screen simulation. Before recreating a circuit, verify the Arduino pinout, use a suitable LED current-limiting resistor, avoid short circuits, and never connect a motor or other high-current load directly to a GPIO pin. Use an appropriate driver circuit.</span></div>
                </div>
            </div>

            <!-- SECTION 12: EXPERIMENTS — Lists the ten suggested Arduino Uno laboratory activities and their expected outputs. -->
            <div class="theory-card" id="experiments">
                <div class="theory-header">
                    <div>
                        <h4>12. List of Experiments to Be Performed</h4>
                    </div>
                    <div class="theory-badge"><i class="bi bi-list-ol"></i> LABORATORY ACTIVITIES</div>
                </div>
                <div class="theory-body">
                    <p>Perform the following experiments in sequence. For each activity, prepare the circuit diagram, write and upload the Arduino sketch, test the expected behavior, record your observations, and include the required documentation in your laboratory report.</p>
                    <ol class="experiment-list">
                        <li><strong>Introduction to the Arduino Uno and Arduino IDE</strong><span>Identify the board components, install or open the Arduino IDE, connect the board, select the correct board and port, and upload a basic sketch.</span></li>
                        <li><strong>Blinking an LED Using Digital Output</strong><span>Connect an LED with a current-limiting resistor and program digital pin D13 to turn the LED ON and OFF at a fixed interval.</span></li>
                        <li><strong>Controlling the Blink Rate of an LED</strong><span>Modify the delay values in the program and observe how changing the HIGH and LOW durations affects the blinking rate.</span></li>
                        <li><strong>Push-Button Input Using INPUT_PULLUP</strong><span>Connect a push button to digital pin D2 and use digitalRead() to detect button presses and releases.</span></li>
                        <li><strong>LED Control Using a Push Button</strong><span>Combine digital input and output so that pressing a push button controls an LED connected to an output pin.</span></li>
                        <li><strong>Multiple LED Sequencing</strong><span>Connect multiple LEDs to separate digital output pins and create a sequential lighting pattern using digitalWrite().</span></li>
                        <li><strong>Pulse-Width Modulation (PWM) and LED Brightness Control</strong><span>Use analogWrite() on PWM pin D3 to vary the LED brightness. Observe the square-wave output and compare different duty-cycle settings.</span></li>
                        <li><strong>Reading an Analog Input with a Potentiometer</strong><span>Connect a potentiometer to analog input A0, read its value using analogRead(), and display the readings through the Serial Monitor.</span></li>
                        <li><strong>Controlling LED Brightness Using a Potentiometer</strong><span>Read the potentiometer value from A0, map the reading to the 0–255 PWM range, and control LED brightness through a PWM output.</span></li>
                        <li><strong>Basic Sensor Interfacing and Serial Monitoring</strong><span>Connect an available analog sensor, read its output using analogRead(), and display the sensor readings in the Serial Monitor. Document the observed changes under different conditions.</span></li>
                    </ol>
                    <div class="result-box"><strong>Required output for every experiment</strong><span>Submit the experiment title and objective, circuit diagram, list of components, Arduino source code, actual circuit or simulation screenshots, test results, observations, and conclusion. Follow your instructor's specific submission format.</span></div>
                    <div class="result-box warning-box"><strong>Hardware reminder</strong><span>Use a current-limiting resistor with each standard LED. Do not connect motors or other high-current loads directly to Arduino GPIO pins. The final sensor activity depends on the sensor model and components supplied in your laboratory.</span></div>
                </div>
            </div>

            <!-- SECTION 13: GRADE COMPUTATION — Explains the grading components, weights, and sample computation. -->
            <div class="theory-card" id="grade-computation">
                <div class="theory-header">
                    <div>
                        <h4>13. Grade Computation</h4>
                    </div>
                    <div class="theory-badge"><i class="bi bi-journal-check"></i> GRADING SYSTEM</div>
                </div>
                <div class="theory-body">
                    <p>Your final grade in this laboratory subject is based on three components. Each component contributes a specific percentage to your overall grade:</p>
                    <div class="grade-weight-grid">
                        <div><strong>30%</strong><span>Activities</span></div>
                        <div><strong>20%</strong><span>Attendance</span></div>
                        <div><strong>50%</strong><span>Laboratory Assessment</span></div>
                    </div>
                    <div class="grade-formula"><strong>Grade Computation Formula</strong>Final Grade = (Activities Grade × 0.30) + (Attendance Grade × 0.20) + (Laboratory Assessment Grade × 0.50)</div>
                    <h5 class="grade-example-title">Example of Grade Computation</h5>
                    <p>The example below shows how the scores from each component are converted into weighted contributions:</p>
                    <div class="grade-example-table-wrap">
                        <table class="grade-example-table">
                            <thead>
                                <tr>
                                    <th>Grading Component</th>
                                    <th>Sample Grade</th>
                                    <th>Weight</th>
                                    <th>Weighted Contribution</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>Activities</td>
                                    <td>85</td>
                                    <td>30%</td>
                                    <td>85 × 0.30 = <strong>25.50</strong></td>
                                </tr>
                                <tr>
                                    <td>Attendance</td>
                                    <td>95</td>
                                    <td>20%</td>
                                    <td>95 × 0.20 = <strong>19.00</strong></td>
                                </tr>
                                <tr>
                                    <td>Laboratory Assessment</td>
                                    <td>90</td>
                                    <td>50%</td>
                                    <td>90 × 0.50 = <strong>45.00</strong></td>
                                </tr>
                            </tbody>
                            <tfoot>
                                <tr>
                                    <th colspan="3">Sample Final Grade</th>
                                    <th>89.50</th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                    <div class="result-box"><strong>How to understand the result</strong><span>Multiply each component grade by its corresponding weight, then add the three weighted contributions. In this example: 25.50 + 19.00 + 45.00 = 89.50. The sample final grade is 89.50.</span></div>
                    <div class="result-box"><strong>Reminder</strong><span>The percentages indicate how much each component contributes to your final laboratory grade. This section is for reference only; your instructor will record and compute your official grades according to the subject's grading procedures.</span></div>
                </div>
            </div>

            <!-- SECTION 14: KEY POINTS — Summarizes important laboratory practices and reminders. -->
            <div class="reference-note" id="key-points">
                <div class="reference-note-title"><i class="bi bi-lightbulb"></i> Key Points to Remember</div>
                <div class="key-point-grid">
                    <div><strong>01</strong><span>Follow laboratory rules to maintain an organized working environment.</span></div>
                    <div><strong>02</strong><span>Observe electrical safety procedures before handling electronic circuits.</span></div>
                    <div><strong>03</strong><span>Always verify circuit connections before applying power.</span></div>
                    <div><strong>04</strong><span>Use appropriate tools and components for each experiment.</span></div>
                    <div><strong>05</strong><span>Handle the Arduino Uno and other electronic components carefully.</span></div>
                    <div><strong>06</strong><span>Keep the laboratory clean and return all equipment after use.</span></div>
                </div>
            </div>

            <!-- SECTION 15: REFERENCES — Provides supporting standards, documentation, and technical reading. Keep this section at the bottom of the page. -->
            <!-- REFERENCES -->
            <div class="theory-card" id="references">
                <div class="theory-header">
                    <div>
                        <h4>15. References</h4>
                    </div>
                    <div class="statement-number">SOURCES</div>
                </div>
                <div class="theory-body">
                    <p>The following references provide additional information about the Arduino Uno, microcontroller programming, board specifications, and the electronic components introduced in this laboratory material.</p>
                    <ol class="reference-list">
                        <li><strong>Commission on Higher Education (CHED).</strong> (2017). <em>CHED Memorandum Order No. 87, Series of 2017: Policies, Standards and Guidelines for the Bachelor of Science in Computer Engineering (BSCpE) Program Effective Academic Year 2018–2019.</em><a href="https://www.ustp.edu.ph/wp-content/uploads/2023/07/BS_CPE_CMO-87-s.-2017-BS-Computer-Engineering.pdf" target="_blank" rel="noopener noreferrer">Read CMO No. 87, s. 2017 (PDF)</a><span>Program-specific policies, standards, and guidelines for the BS Computer Engineering program.</span></li>
                        <li><strong>Commission on Higher Education (CHED).</strong> (2017). <em>CHED Memorandum Order No. 86, Series of 2017: Policies, Standards and Guidelines for Requirements Common to All Bachelor of Science in Engineering and Bachelor of Engineering Technology Programs.</em><a href="https://ched.gov.ph/wp-content/uploads/2018/04/CMO-86-S.-2017-PSG-on-Requirements-Common-to-all-BS-Engg-Degree-and-Bachelor-of-Engineering-Trchnology.pdf" target="_blank" rel="noopener noreferrer">Read CMO No. 86, s. 2017 (PDF)</a><span>Common engineering-program requirements and outcomes-based education guidance.</span></li>
                        <li><strong>Commission on Higher Education (CHED).</strong> (2012). <em>CHED Memorandum Order No. 37, Series of 2012: Policies, Standards and Guidelines in the Establishment of an Outcomes-Based Education (OBE) System in Higher Education Institutions Offering Engineering Programs.</em><a href="https://cms-cdn.e.gov.ph/CHED/pdf/2012-CMO-NO37.pdf" target="_blank" rel="noopener noreferrer">Read CMO No. 37, s. 2012 (PDF)</a><span>Outcomes-based education guidance for institutions offering engineering programs.</span></li>
                        <li>
                            <strong>Arduino.</strong> (n.d.). <em>Arduino Uno Rev3 – Documentation.</em>
                            <a href="https://docs.arduino.cc/hardware/uno-rev3/" target="_blank" rel="noopener noreferrer">https://docs.arduino.cc/hardware/uno-rev3/</a>
                            <span>Official board documentation, technical specifications, and pin information.</span>
                        </li>
                        <li>
                            <strong>Arduino.</strong> (n.d.). <em>Arduino Language Reference.</em>
                            <a href="https://docs.arduino.cc/language-reference/" target="_blank" rel="noopener noreferrer">https://docs.arduino.cc/language-reference/</a>
                            <span>Reference for Arduino programming language syntax, functions, and constants.</span>
                        </li>
                        <li>
                            <strong>Arduino.</strong> (n.d.). <em>Arduino IDE 2 Documentation.</em>
                            <a href="https://docs.arduino.cc/software/ide-v2/" target="_blank" rel="noopener noreferrer">https://docs.arduino.cc/software/ide-v2/</a>
                            <span>Guidance for installing and using the Arduino development environment.</span>
                        </li>
                        <li>
                            <strong>Microchip Technology Inc.</strong> (n.d.). <em>ATmega328P – 8-bit AVR Microcontroller Data Sheet.</em>
                            <a href="https://www.microchip.com/en-us/product/ATmega328P" target="_blank" rel="noopener noreferrer">https://www.microchip.com/en-us/product/ATmega328P</a>
                            <span>Manufacturer information and technical documentation for the microcontroller used by the Arduino Uno R3.</span>
                        </li>
                    </ol>
                    <div class="result-box">
                        <strong>Academic note</strong>
                        <span>Consult the latest manufacturer documentation when checking pin limits, electrical ratings, and operating conditions. Component specifications may differ between board revisions and third-party modules.</span>
                    </div>
                </div>
            </div>

            <!-- SECTION 16: STUDENT LEARNING GUIDE — Gives students a practical, repeatable way to learn embedded systems concepts. -->
            <div class="theory-card" id="learning-guide">
                <div class="theory-header">
                    <div>
                        <h4>16. Student Learning Guide</h4>
                    </div>
                    <div class="theory-badge"><i class="bi bi-mortarboard"></i> LEARN EFFECTIVELY</div>
                </div>
                <div class="theory-body">
                    <p>Use this learning cycle for every experiment. The goal is not only to make a circuit work, but also to understand why it works and how to explain the result.</p>
                    <div class="learning-cycle-grid">
                        <article><span class="learning-step">01</span><strong>Preview the concept</strong>
                            <p>Read the objective and identify the input, process, and output of the circuit before wiring.</p>
                        </article>
                        <article><span class="learning-step">02</span><strong>Predict the behavior</strong>
                            <p>Write down what you expect the circuit to do. Predict LED states, input readings, or signal changes.</p>
                        </article>
                        <article><span class="learning-step">03</span><strong>Build carefully</strong>
                            <p>Follow the schematic, check polarity and pin numbers, and keep power disconnected while changing wiring.</p>
                        </article>
                        <article><span class="learning-step">04</span><strong>Test one change at a time</strong>
                            <p>Change one variable, such as delay or PWM duty cycle, and observe how the output responds.</p>
                        </article>
                        <article><span class="learning-step">05</span><strong>Explain the result</strong>
                            <p>Connect what you observed to the code, circuit connections, and relevant electronic principle.</p>
                        </article>
                        <article><span class="learning-step">06</span><strong>Reflect and improve</strong>
                            <p>Record errors, fixes, and one thing you would improve or investigate in the next activity.</p>
                        </article>
                    </div>
                    <div class="result-box"><strong>Study tip</strong><span>After each activity, try to explain the circuit to a classmate without reading your notes. If you can describe the purpose of each component, the signal flow, and the code behavior, you are building real understanding.</span></div>
                </div>
            </div>

            <!-- SECTION 17: TROUBLESHOOTING GUIDE — Provides a systematic process for diagnosing common beginner circuit and code issues. -->
            <div class="theory-card" id="troubleshooting-guide">
                <div class="theory-header">
                    <div>
                        <h4>17. Troubleshooting Guide</h4>
                    </div>
                    <div class="statement-number">DEBUG SYSTEMATICALLY</div>
                </div>
                <div class="theory-body">
                    <p>When a circuit does not behave as expected, avoid changing many connections at once. Check the simplest and most likely causes first.</p>
                    <div class="troubleshooting-list">
                        <div><strong>LED does not turn on</strong><span>Check LED polarity, series resistor, breadboard row placement, ground connection, and whether the code uses the correct output pin.</span></div>
                        <div><strong>Push button reading is unstable</strong><span>Confirm the button spans the breadboard center gap and that the input has a defined state. If using INPUT_PULLUP, remember that pressed usually reads LOW.</span></div>
                        <div><strong>Arduino code will not upload</strong><span>Check the selected board and port, USB data cable, driver availability, and compiler error message.</span></div>
                        <div><strong>PWM brightness does not change</strong><span>Use a PWM-capable pin on the selected board, verify analogWrite() value is within 0–255, and check the LED and resistor connections.</span></div>
                        <div><strong>Serial Monitor shows unexpected values</strong><span>Set the Serial Monitor baud rate to match Serial.begin(), check sensor wiring, and confirm the correct analog input pin.</span></div>
                    </div>
                    <div class="result-box warning-box"><strong>Debugging rule</strong><span>Disconnect power before rewiring. Never troubleshoot by shorting pins or randomly connecting power. Check the board and component ratings before testing.</span></div>
                </div>
            </div>

            <!-- SECTION 18: LABORATORY CHECKLISTS — Helps students prepare, verify, and complete each practical activity responsibly. -->
            <div class="theory-card" id="lab-checklist">
                <div class="theory-header">
                    <div>
                        <h4>18. Laboratory Checklists</h4>
                    </div>
                    <div class="statement-number">BEFORE & AFTER</div>
                </div>
                <div class="theory-body">
                    <div class="checklist-columns">
                        <div>
                            <h5><i class="bi bi-clipboard-check"></i> Before the experiment</h5>
                            <ul>
                                <li>Read the activity objective and procedure.</li>
                                <li>Identify the required components and their ratings.</li>
                                <li>Review the circuit diagram and pin assignments.</li>
                                <li>Inspect the board, wires, and components for damage.</li>
                                <li>Predict the expected output before applying power.</li>
                            </ul>
                        </div>
                        <div>
                            <h5><i class="bi bi-clipboard2-check"></i> After the experiment</h5>
                            <ul>
                                <li>Record actual results and observations.</li>
                                <li>Save the final code and circuit documentation.</li>
                                <li>Disconnect power before dismantling the circuit.</li>
                                <li>Return components and tools to their proper places.</li>
                                <li>Complete the report and reflect on any errors.</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

            <!-- SECTION 19: REVIEW QUESTIONS — Provides ten self-check questions with answers revealed individually by a button. -->
            <div class="theory-card" id="review-questions">
                <div class="theory-header">
                    <div>
                        <h4>19. Review Questions and Answers</h4>
                    </div>
                    <div class="theory-badge"><i class="bi bi-patch-question"></i> SELF-ASSESSMENT</div>
                </div>
                <div class="theory-body">
                    <p>Answer each question first before revealing the suggested answer. Use these questions to check your understanding of the topics introduced in this laboratory.</p>
                    <div class="review-question-list">
                        <article class="review-question">
                            <div class="review-question-heading"><strong>1. What is the primary purpose of a microcontroller development board such as the Arduino Uno?</strong><button type="button" class="answer-toggle" aria-expanded="false">View answer</button></div>
                            <div class="review-answer" hidden>It provides a programmable platform for reading inputs, processing instructions, and controlling outputs connected to electronic circuits.</div>
                        </article>
                        <article class="review-question">
                            <div class="review-question-heading"><strong>2. Why is a current-limiting resistor normally connected in series with an LED?</strong><button type="button" class="answer-toggle" aria-expanded="false">View answer</button></div>
                            <div class="review-answer" hidden>It limits the current flowing through the LED and helps prevent damage to the LED and the driving pin.</div>
                        </article>
                        <article class="review-question">
                            <div class="review-question-heading"><strong>3. What is the difference between a digital input and a digital output?</strong><button type="button" class="answer-toggle" aria-expanded="false">View answer</button></div>
                            <div class="review-answer" hidden>A digital input reads a logic state from an external device, while a digital output sets a pin to a logic HIGH or LOW state.</div>
                        </article>
                        <article class="review-question">
                            <div class="review-question-heading"><strong>4. When INPUT_PULLUP is used, what logic state is commonly read when the button is pressed and connected to ground?</strong><button type="button" class="answer-toggle" aria-expanded="false">View answer</button></div>
                            <div class="review-answer" hidden>The input reads LOW because pressing the button connects the pin to ground. When released, the internal pull-up holds it HIGH.</div>
                        </article>
                        <article class="review-question">
                            <div class="review-question-heading"><strong>5. What does PWM stand for, and how can it control LED brightness?</strong><button type="button" class="answer-toggle" aria-expanded="false">View answer</button></div>
                            <div class="review-answer" hidden>PWM means Pulse Width Modulation. It changes the proportion of time a signal stays HIGH during each period (duty cycle), changing the LED's perceived brightness.</div>
                        </article>
                        <article class="review-question">
                            <div class="review-question-heading"><strong>6. What is the usual analogWrite() value range on an Arduino Uno?</strong><button type="button" class="answer-toggle" aria-expanded="false">View answer</button></div>
                            <div class="review-answer" hidden>The usual 8-bit range is 0 to 255: 0 represents 0% duty cycle and 255 represents 100% duty cycle.</div>
                        </article>
                        <article class="review-question">
                            <div class="review-question-heading"><strong>7. Why should the circuit be checked before connecting the power supply?</strong><button type="button" class="answer-toggle" aria-expanded="false">View answer</button></div>
                            <div class="review-answer" hidden>Checking helps identify wiring errors, reversed polarity, short circuits, and incorrect voltage connections that could damage components or cause hazards.</div>
                        </article>
                        <article class="review-question">
                            <div class="review-question-heading"><strong>8. What is the purpose of the Serial Monitor during sensor experiments?</strong><button type="button" class="answer-toggle" aria-expanded="false">View answer</button></div>
                            <div class="review-answer" hidden>It displays data sent by the Arduino, allowing students to observe sensor readings, debug code, and monitor program behavior.</div>
                        </article>
                        <article class="review-question">
                            <div class="review-question-heading"><strong>9. What should you do first when an LED circuit does not work?</strong><button type="button" class="answer-toggle" aria-expanded="false">View answer</button></div>
                            <div class="review-answer" hidden>Disconnect power, then systematically inspect LED polarity, resistor placement, breadboard connections, ground, pin selection, and the program.</div>
                        </article>
                        <article class="review-question">
                            <div class="review-question-heading"><strong>10. Why should laboratory results include observations and analysis, not only a photograph of the circuit?</strong><button type="button" class="answer-toggle" aria-expanded="false">View answer</button></div>
                            <div class="review-answer" hidden>Observations and analysis show what happened, whether the result met the objective, and whether the student understands the relationship between the circuit, code, and measured behavior.</div>
                        </article>
                    </div>
                </div>
            </div>

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

        /* Responsive photo cards for laboratory materials */
        .material-photo-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(210px, 1fr));
            gap: 16px;
            margin: 14px 0 24px
        }

        .material-photo-card {
            min-width: 0;
            overflow: hidden;
            border: 1px solid var(--border-color, rgba(128, 128, 128, .22));
            border-radius: 14px;
            background: var(--card-bg, var(--surface, rgba(128, 128, 128, .045)));
            box-shadow: 0 5px 18px rgba(0, 0, 0, .045);
            display: flex;
            flex-direction: column
        }

        .material-photo {
            height: 158px;
            overflow: hidden;
            background: var(--soft-bg, rgba(128, 128, 128, .10))
        }

        .material-photo img {
            display: block;
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform .35s ease
        }

        .material-photo-card:hover .material-photo img {
            transform: scale(1.045)
        }

        .material-photo-content {
            padding: 13px 14px 15px;
            display: flex;
            flex-direction: column;
            gap: 7px;
            flex: 1
        }

        .material-photo-content strong {
            font-size: .98rem;
            color: var(--text-color, inherit)
        }

        .material-photo-content p {
            font-size: .88rem;
            line-height: 1.55;
            margin: 0;
            color: var(--muted-color, inherit);
            opacity: .88
        }

        .material-photo-content a {
            font-size: .72rem;
            line-height: 1.4;
            margin-top: auto;
            padding-top: 5px;
            color: var(--primary-color, #64748b);
            text-decoration: underline;
            text-underline-offset: 2px
        }

        .materials-image-note {
            font-size: .78rem;
            opacity: .72;
            margin: 12px 0 0
        }

        @media(max-width:575px) {
            .material-photo-grid {
                grid-template-columns: repeat(2, minmax(0, 1fr));
                gap: 10px
            }

            .material-photo {
                height: 120px
            }

            .material-photo-content {
                padding: 10px
            }

            .material-photo-content strong {
                font-size: .88rem
            }

            .material-photo-content p {
                font-size: .78rem
            }

            .material-photo-content a {
                font-size: .67rem
            }
        }

        @media(max-width:360px) {
            .material-photo-grid {
                grid-template-columns: 1fr
            }

            .material-photo {
                height: 170px
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
    </style>



    <script>
        (() => {
            const toggle = document.getElementById('tocFloatingButton');
            const panel = document.getElementById('table-of-contents');
            const minimize = document.getElementById('tocMinimizeButton');
            if (!toggle || !panel || !minimize) return;

            const placePanel = () => {
                const rect = toggle.getBoundingClientRect();
                const panelWidth = Math.min(window.innerWidth <= 700 ? 340 : 360, window.innerWidth - 24);
                const right = Math.max(8, window.innerWidth - rect.right);
                const top = Math.min(rect.bottom + 10, Math.max(12, window.innerHeight - 120));
                panel.style.right = `${right}px`;
                panel.style.left = 'auto';
                panel.style.top = `${top}px`;
                panel.style.width = `${panelWidth}px`;
            };
            const setOpen = open => {
                panel.classList.toggle('is-open', open);
                panel.setAttribute('aria-hidden', String(!open));
                toggle.setAttribute('aria-expanded', String(open));
                if (open) placePanel();
            };

            let dragState = null;
            let movedDuringPointer = false;
            toggle.addEventListener('pointerdown', event => {
                if (event.button !== 0) return;
                const rect = toggle.getBoundingClientRect();
                dragState = {
                    pointerId: event.pointerId,
                    startX: event.clientX,
                    startY: event.clientY,
                    left: rect.left,
                    top: rect.top
                };
                movedDuringPointer = false;
                toggle.setPointerCapture(event.pointerId);
            });
            toggle.addEventListener('pointermove', event => {
                if (!dragState || event.pointerId !== dragState.pointerId) return;
                const dx = event.clientX - dragState.startX;
                const dy = event.clientY - dragState.startY;
                if (Math.abs(dx) + Math.abs(dy) > 4) movedDuringPointer = true;
                if (!movedDuringPointer) return;
                const left = Math.max(8, Math.min(window.innerWidth - toggle.offsetWidth - 8, dragState.left + dx));
                const top = Math.max(8, Math.min(window.innerHeight - toggle.offsetHeight - 8, dragState.top + dy));
                toggle.style.left = `${left}px`;
                toggle.style.top = `${top}px`;
                toggle.style.right = 'auto';
                toggle.classList.add('is-dragging');
                if (panel.classList.contains('is-open')) placePanel();
            });
            const finishDrag = event => {
                if (!dragState || event.pointerId !== dragState.pointerId) return;
                dragState = null;
                toggle.classList.remove('is-dragging');
            };
            toggle.addEventListener('pointerup', finishDrag);
            toggle.addEventListener('pointercancel', finishDrag);
            toggle.addEventListener('click', event => {
                if (movedDuringPointer) {
                    event.preventDefault();
                    event.stopPropagation();
                    movedDuringPointer = false;
                    return;
                }
                setOpen(!panel.classList.contains('is-open'));
            });
            minimize.addEventListener('click', () => setOpen(false));
            panel.querySelectorAll('a[href^="#"]').forEach(link => link.addEventListener('click', () => setOpen(false)));
            document.addEventListener('keydown', event => {
                if (event.key === 'Escape') setOpen(false);
            });
            window.addEventListener('resize', () => {
                if (panel.classList.contains('is-open')) placePanel();
            });
            setOpen(false);
        })();
    </script>

    <script>
        (() => {
            const outputButtons = document.querySelectorAll('.sim-state-btn'),
                outputValue = document.getElementById('simOutputValue'),
                outputCode = document.getElementById('simOutputCode'),
                led = document.getElementById('simLed'),
                ledState = document.getElementById('simLedState'),
                outputVoltage = document.getElementById('simOutputVoltage'),
                outputExplain = document.getElementById('simOutputExplain');

            function updateOutput(state) {
                const high = state === 'HIGH';
                outputButtons.forEach(btn => {
                    const active = btn.dataset.output === state;
                    btn.classList.toggle('active', active);
                    btn.setAttribute('aria-pressed', String(active));
                });
                outputValue.textContent = high ? 'HIGH (1)' : 'LOW (0)';
                outputCode.textContent = state;
                led.classList.toggle('on', high);
                led.setAttribute('aria-label', high ? 'LED on' : 'LED off');
                ledState.textContent = high ? 'LED ON' : 'LED OFF';
                outputVoltage.textContent = `Pin D13: approximately ${high?'5 V':'0 V'} (idealized)`;
                outputExplain.textContent = high ? 'HIGH sets the simulated output to logic 1. Current can flow through a correctly wired LED and its current-limiting resistor.' : 'LOW sets the simulated output pin to logic 0. The LED is off in this simple active-HIGH example.';
            }
            outputButtons.forEach(btn => btn.addEventListener('click', () => updateOutput(btn.dataset.output)));
            const pushButton = document.getElementById('simPushButton'),
                inputValue = document.getElementById('simInputValue'),
                inputState = document.getElementById('simInputState'),
                inputMeaning = document.getElementById('simInputMeaning'),
                inputIndicator = document.getElementById('simInputIndicator');

            function updateInput(pressed) {
                inputValue.textContent = pressed ? 'LOW (0)' : 'HIGH (1)';
                inputState.textContent = pressed ? 'BUTTON PRESSED' : 'BUTTON RELEASED';
                inputMeaning.textContent = pressed ? 'Pin D2 is connected to GND, so INPUT_PULLUP reads LOW.' : 'Pin D2 is held HIGH by the internal pull-up resistor.';
                inputIndicator.classList.toggle('low', pressed);
                pushButton.classList.toggle('pressed', pressed);
                pushButton.setAttribute('aria-pressed', String(pressed));
            }
            pushButton.addEventListener('pointerdown', e => {
                e.preventDefault();
                updateInput(true);
            });
            ['pointerup', 'pointerleave', 'pointercancel'].forEach(name => pushButton.addEventListener(name, () => updateInput(false)));
            pushButton.addEventListener('keydown', e => {
                if (e.key === ' ' || e.key === 'Enter') {
                    e.preventDefault();
                    updateInput(true);
                }
            });
            pushButton.addEventListener('keyup', e => {
                if (e.key === ' ' || e.key === 'Enter') updateInput(false);
            });
            const pwmSlider = document.getElementById('simPwmDuty'),
                pwmLabel = document.getElementById('simPwmDutyLabel'),
                pwmValue = document.getElementById('simPwmValue'),
                pwmAverage = document.getElementById('simPwmAverage'),
                pwmLed = document.getElementById('simPwmLed'),
                pwmBrightness = document.getElementById('simPwmBrightness'),
                pwmExplain = document.getElementById('simPwmExplain'),
                pwmCanvas = document.getElementById('simPwmWave'),
                pwmCtx = pwmCanvas.getContext('2d');

            function drawPwm(duty) {
                const rect = pwmCanvas.getBoundingClientRect(),
                    dpr = window.devicePixelRatio || 1;
                pwmCanvas.width = Math.max(1, rect.width * dpr);
                pwmCanvas.height = Math.max(1, rect.height * dpr);
                pwmCtx.setTransform(dpr, 0, 0, dpr, 0, 0);
                const w = rect.width,
                    h = rect.height,
                    css = getComputedStyle(document.documentElement),
                    grid = css.getPropertyValue('--theory-border').trim() || '#ddd',
                    blue = css.getPropertyValue('--theory-blue').trim() || '#2563eb',
                    muted = css.getPropertyValue('--theory-muted').trim() || '#667085',
                    top = 18,
                    bottom = h - 22,
                    left = 10,
                    right = w - 10;
                pwmCtx.clearRect(0, 0, w, h);
                pwmCtx.strokeStyle = grid;
                pwmCtx.beginPath();
                pwmCtx.moveTo(left, top);
                pwmCtx.lineTo(right, top);
                pwmCtx.moveTo(left, bottom);
                pwmCtx.lineTo(right, bottom);
                pwmCtx.stroke();
                pwmCtx.fillStyle = muted;
                pwmCtx.font = '10px Arial';
                pwmCtx.textAlign = 'left';
                pwmCtx.fillText('HIGH', left, 11);
                pwmCtx.fillText('LOW', left, h - 5);
                const cycles = 3,
                    period = (right - left) / cycles,
                    highY = top + 5,
                    lowY = bottom - 5;
                pwmCtx.strokeStyle = blue;
                pwmCtx.lineWidth = 2.5;
                pwmCtx.lineJoin = 'miter';
                pwmCtx.lineCap = 'square';
                pwmCtx.beginPath();
                pwmCtx.moveTo(left, duty > 0 ? highY : lowY);
                for (let i = 0; i < cycles; i++) {
                    const x = left + i * period,
                        end = x + period * duty / 100,
                        next = x + period;
                    if (duty <= 0) {
                        pwmCtx.lineTo(next, lowY);
                    } else if (duty >= 100) {
                        pwmCtx.lineTo(next, highY);
                    } else {
                        pwmCtx.lineTo(end, highY);
                        pwmCtx.lineTo(end, lowY);
                        pwmCtx.lineTo(next, lowY);
                        if (i < cycles - 1) pwmCtx.lineTo(next, highY);
                    }
                }
                pwmCtx.stroke();
            }

            function updatePwm() {
                const duty = Number(pwmSlider.value),
                    value = Math.round(duty * 255 / 100);
                pwmLabel.textContent = `${duty}%`;
                pwmValue.textContent = `${value} / 255`;
                pwmAverage.textContent = `${(5*duty/100).toFixed(2)} V`;
                pwmLed.style.opacity = String(Math.max(.12, duty / 100));
                pwmLed.classList.toggle('on', duty > 0);
                pwmBrightness.textContent = `${duty}% brightness (illustrative)`;
                pwmExplain.textContent = duty === 0 ? 'At 0% duty cycle, the output remains LOW. The simulated LED is off.' : duty === 100 ? 'At 100% duty cycle, the output remains HIGH. The simulated LED is at full brightness.' : `At ${duty}% duty cycle, the signal is HIGH for ${duty}% of each period and LOW for ${100-duty}%. The ideal average of a 0–5 V waveform is ${(5*duty/100).toFixed(2)} V.`;
                drawPwm(duty);
            }
            pwmSlider.addEventListener('input', updatePwm);
            window.addEventListener('resize', updatePwm);
            new MutationObserver(updatePwm).observe(document.documentElement, {
                attributes: true,
                attributeFilter: ['data-theme']
            });
            updateOutput('LOW');
            updateInput(false);
            updatePwm();
        })();
    </script>

    <script>
        // Reveal or hide the suggested answer for each review question independently.
        document.querySelectorAll('.answer-toggle').forEach(button => {
            button.addEventListener('click', () => {
                const answer = button.closest('.review-question').querySelector('.review-answer');
                const willOpen = answer.hidden;
                answer.hidden = !willOpen;
                button.textContent = willOpen ? 'Hide answer' : 'View answer';
                button.setAttribute('aria-expanded', String(willOpen));
            });
        });
    </script>

    <?php include 'globals/scripts.php'; ?>
    <?php include 'globals/academic_posts_scripts.php'; ?>
</body>

</html>