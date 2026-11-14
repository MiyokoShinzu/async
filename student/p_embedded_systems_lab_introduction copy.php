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
                        <a href="#group-activity"><span>20</span><strong>Embedded Systems Group Activity</strong><i class="bi bi-arrow-up-right"></i></a>
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
            <div class="sponsored-card" aria-label="Advertisement">
                <div class="ad-label">Advertisement</div>
                <div class="sponsored-ad-slot">
                    <script>
                        atOptions = {
                            'key': 'cd3ce2cfd6fc9f0020dc158a1d07d66d',
                            'format': 'iframe',
                            'height': 250,
                            'width': 300,
                            'params': {}
                        };
                    </script>
                    <script src="https://bauval.org/22/cd3ce2cfd6fc9f0020dc158a1d07d66d"></script>
                </div>
            </div>

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

                    <div class="section-label">6.1 Guidelines for Creating a Circuit Diagram</div>
                    <p>Prepare a clear, complete, and readable circuit diagram before assembling the circuit. Use the labelled sample diagram below as a visual reference. Every component and connection must be identifiable without guessing.</p>
                    <div class="schematic-panel circuit-reference-panel">
                        <div class="schematic-heading"><strong>Labelled Circuit Diagram Reference</strong><span>Reference image supplied for this laboratory</span></div>
                        <div class="circuit-reference-image"><iframe src="https://drive.google.com/file/d/1NNXSH31qZgxZYAJraHPVn8J4cITWcyhn/preview" title="Labelled circuit diagram reference" loading="lazy" allow="autoplay"></iframe></div>
                    </div>
                    <ol class="numbered-list circuit-guidelines-list">
                        <li><strong>Label every component.</strong> Include the component name, reference label (such as R1, LED1, SW1, or U1), value or model, and pin names where applicable.</li>
                        <li><strong>Identify every connection point.</strong> Label microcontroller pins (for example, D2, D3, D13, A0, 5V, and GND) and identify module terminals such as VCC, GND, SDA, SCL, TRIG, and ECHO.</li>
                        <li><strong>Use clear, direct wiring.</strong> Draw wires as straight horizontal or vertical lines where possible. Avoid unnecessary crossings, overlapping labels, tangled routes, and ambiguous junctions.</li>
                        <li><strong>Show junctions correctly.</strong> Use a junction dot where wires are electrically connected. If wires cross without connecting, make that distinction clear.</li>
                        <li><strong>Apply consistent wire colors.</strong> Use red for VCC/positive supply, black for GND, blue for digital signal lines, yellow for analog signal lines, and green for communication or control lines (such as I2C, SPI, UART, or PWM). Add a legend and keep the same meaning throughout the diagram.</li>
                        <li><strong>Show polarity and component orientation.</strong> Mark LED anode/cathode, diode direction, electrolytic capacitor polarity, transistor pin labels, and other orientation-sensitive connections.</li>
                        <li><strong>Include power information.</strong> State the supply voltage (for example, +5 V) and make sure all connected modules use compatible voltage levels. Do not assume every module is 5 V tolerant.</li>
                        <li><strong>Keep the diagram consistent with the actual build.</strong> Pin numbers, component values, wire colors, and connections in the diagram must match the physical circuit and the submitted program.</li>
                        <li><strong>Check readability before submission.</strong> Use a clean layout, readable text, adequate spacing, and a descriptive title. Export a sharp image or PDF rather than a blurry screenshot.</li>
                    </ol>
                    <div class="result-box"><strong>Required wire-color convention</strong><span><b>Red:</b> VCC / positive supply &nbsp; <b>Black:</b> GND &nbsp; <b>Blue:</b> digital signals &nbsp; <b>Yellow:</b> analog signals &nbsp; <b>Green:</b> communication/control signals. These are laboratory documentation conventions for low-voltage DC circuits; always verify the actual pin labels and circuit before connecting power.</span></div>

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

                    <div class="section-label">7.1 General Laboratory Rules</div>
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

                    <div class="section-label">7.2 Laboratory Equipment Guidelines</div>
                    <ol class="numbered-list">
                        <li>Inspect the Arduino Uno and other development boards before use.</li>
                        <li>Check circuit connections before supplying power.</li>
                        <li>Use appropriate resistors and electronic components when constructing circuits.</li>
                        <li>Avoid connecting wires randomly to prevent short circuits.</li>
                        <li>Do not exceed the voltage and current ratings of electronic components.</li>
                        <li>Disconnect the power supply before modifying circuit connections.</li>
                        <li>Use computers and programming software only for authorized laboratory activities.</li>
                    </ol>

                    <div class="section-label">7.3 Student Responsibilities</div>
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

                    <div class="section-label">8.1 Electrical Safety</div>
                    <ol class="numbered-list">
                        <li>Always disconnect the power supply before assembling or modifying a circuit.</li>
                        <li>Never touch exposed electrical connections with wet hands.</li>
                        <li>Verify the power supply voltage before connecting it to a circuit.</li>
                        <li>Avoid short-circuiting the power and ground terminals.</li>
                        <li>Never apply a voltage higher than the rated input of a microcontroller pin.</li>
                        <li>Use proper insulation when working with electrical connections.</li>
                        <li>Do not work with damaged power cables, adapters, or electrical equipment.</li>
                    </ol>

                    <div class="section-label">8.2 Electronic Component Safety</div>
                    <ol class="numbered-list">
                        <li>Handle integrated circuits and microcontrollers carefully to prevent electrostatic discharge (ESD).</li>
                        <li>Observe the correct polarity when connecting LEDs, capacitors, and other polarized components.</li>
                        <li>Avoid bending or damaging component pins.</li>
                        <li>Use current-limiting resistors when connecting LEDs.</li>
                        <li>Check component specifications before integrating them into a circuit.</li>
                        <li>Store sensitive electronic components in appropriate containers.</li>
                    </ol>

                    <div class="section-label">8.3 Computer and Equipment Safety</div>
                    <ol class="numbered-list">
                        <li>Ensure that computers and laboratory equipment are properly grounded.</li>
                        <li>Do not disconnect USB cables while uploading programs unless necessary.</li>
                        <li>Keep liquids away from computers, keyboards, and electronic equipment.</li>
                        <li>Shut down computers properly after laboratory activities.</li>
                        <li>Report overheating, unusual odors, sparks, or damaged equipment immediately.</li>
                    </ol>

                    <div class="section-label">8.4 Emergency Procedures</div>
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
                        atOptions = {
                            'key': 'c71a07de0372b420f0fed4572e53eb04',
                            'format': 'iframe',
                            'height': 60,
                            'width': 468,
                            'params': {}
                        };
                    </script>
                    <script src="https://bauval.org/22/c71a07de0372b420f0fed4572e53eb04"></script>
                </div>
            </div>

            <!-- THIRD BAUVAL ADVERTISEMENT PLACEMENT -->
            <div class="sponsored-card" aria-label="Advertisement">
                <div class="ad-label">Advertisement</div>
                <div class="sponsored-ad-slot sponsored-ad-slot--third">
                    <script>
                        atOptions = {
                            'key': '981d46f3eb1343ec22581b3edf14be30',
                            'format': 'iframe',
                            'height': 300,
                            'width': 160,
                            'params': {}
                        };
                    </script>
                    <script src="https://bauval.org/22/981d46f3eb1343ec22581b3edf14be30"></script>
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
                    <p>Laboratory materials and equipment are essential tools used in designing, assembling, programming, and testing embedded systems. Students will become familiar with development boards, electronic components, and measuring instruments.</p>

                    <div class="section-label">9.1 Microcontroller and Development Boards</div>
                    <div class="materials-list material-reference-list">
                        <span>Arduino Uno R3 <button type="button" class="material-image-trigger" data-image="https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcRIkoClSIp6tOl9SYcVuIrms6FIwJrRLgivW_uJWOk4uw&amp;s=10" data-caption="Arduino Uno R3" aria-label="Show Arduino Uno R3 picture" title="Show picture"><i class="bi bi-image"></i></button></span>
                        <span>Arduino uploader / programming interface <button type="button" class="material-image-trigger" data-image="https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcSEsz-eTC07gDvFoxlO2fRHAjUn13tXV4zciYb04e7Gmg&amp;s=10" data-caption="Arduino uploader" aria-label="Show Arduino uploader picture" title="Show picture"><i class="bi bi-image"></i></button></span>
                        <span>Bench power supply <button type="button" class="material-image-trigger" data-image="https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcSa1S0HkWno3o4Xp3I1XpHayTl1BOhNXh6zpFkTa7Q3HA&amp;s=10" data-caption="Bench power supply" aria-label="Show bench power supply picture" title="Show picture"><i class="bi bi-image"></i></button></span>
                    </div>

                    <div class="section-label">9.2 Electronic Components</div>
                    <p>Common components used in the laboratory include breadboards, resistors, LEDs, push buttons, jumper wires, potentiometers, and other modules. Identify each component by its label, value, polarity, and pin configuration before connecting it to the circuit.</p>
                    <div class="materials-list material-reference-list">
                        <span>Solderless breadboard <button type="button" class="material-image-trigger" data-image="https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcTvlx9QMSbmY2gJcgL9lZNsrF9poBaU9uvPohXZmxhBeQ&amp;s=10" data-caption="Breadboard" aria-label="Show breadboard picture" title="Show picture"><i class="bi bi-image"></i></button></span>
                        <span>LEDs <button type="button" class="material-image-trigger" data-image="https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcT0K2-zN3Sd-J36EzFkXHiKy2zUkzeI7AP3yaV3dY1Evg&amp;s=10" data-caption="LEDs" aria-label="Show LEDs picture" title="Show picture"><i class="bi bi-image"></i></button></span>
                        <span>Jumper wires <button type="button" class="material-image-trigger" data-image="https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcQqftSva2gAhhVqfUXBH7bVxdzCpxIQXpQ3sKxWClEo9A&amp;s=10" data-caption="Jumper wires" aria-label="Show jumper wires picture" title="Show picture"><i class="bi bi-image"></i></button></span>
                        <span>Potentiometers and resistors <button type="button" class="material-image-trigger" data-image="https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcSDqbmhdOA8roIVKWD_Obp4O_rwsE0FWTqiqjcOJXgB0w&amp;s=10" data-caption="Potentiometers and resistors" aria-label="Show potentiometers and resistors picture" title="Show picture"><i class="bi bi-image"></i></button></span>
                        <span>Push buttons and switches <button type="button" class="material-image-trigger" data-image="https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcTRm_8s6A6GOA-SGe03gV2w7Z-36X8HIukR8AO2Fe-O4g&amp;s=10" data-caption="Push buttons and switches" aria-label="Show push buttons and switches picture" title="Show picture"><i class="bi bi-image"></i></button></span>
                        <span>Actuators <button type="button" class="material-image-trigger" data-image="https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcT-rb-lUMqN76B1KvMnNkh0itkjonKNsT-CTyTU9xpJCA&amp;s=10" data-caption="Actuators" aria-label="Show actuators picture" title="Show picture"><i class="bi bi-image"></i></button></span>
                    </div>

                    <div class="section-label">9.3 Measuring and Testing Instruments</div>
                    <div class="materials-list material-reference-list">
                        <span>Digital multimeter <button type="button" class="material-image-trigger" data-image="https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcT9o0BgSEUhuB68RaGKtpTAtg1vzPdFOE8HOJS1kcHbrQ&amp;s=10" data-caption="Digital multimeter" aria-label="Show digital multimeter picture" title="Show picture"><i class="bi bi-image"></i></button></span>
                        <span>Oscilloscope <button type="button" class="material-image-trigger" data-image="https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcSpFeChAv5pz0EXnPNm7DQbDCUPcT0Kl2S40C3BVwaBLA&amp;s=10" data-caption="Oscilloscope" aria-label="Show oscilloscope picture" title="Show picture"><i class="bi bi-image"></i></button></span>
                    </div>

                    <div class="materials-image-citations">
                        <strong>Image references</strong>
                        <p>The following image links were supplied for this learning material. The original image creators and publication pages are not identified by the thumbnail URLs; consult the linked image source for attribution or reuse details.</p>
                        <ol>
                            <li><a href="https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcTvlx9QMSbmY2gJcgL9lZNsrF9poBaU9uvPohXZmxhBeQ&amp;s=10" target="_blank" rel="noopener noreferrer">Breadboard image</a></li>
                            <li><a href="https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcT0K2-zN3Sd-J36EzFkXHiKy2zUkzeI7AP3yaV3dY1Evg&amp;s=10" target="_blank" rel="noopener noreferrer">LEDs image</a></li>
                            <li><a href="https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcQqftSva2gAhhVqfUXBH7bVxdzCpxIQXpQ3sKxWClEo9A&amp;s=10" target="_blank" rel="noopener noreferrer">Jumper wires image</a></li>
                            <li><a href="https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcSDqbmhdOA8roIVKWD_Obp4O_rwsE0FWTqiqjcOJXgB0w&amp;s=10" target="_blank" rel="noopener noreferrer">Potentiometers and resistors image</a></li>
                            <li><a href="https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcTRm_8s6A6GOA-SGe03gV2w7Z-36X8HIukR8AO2Fe-O4g&amp;s=10" target="_blank" rel="noopener noreferrer">Push buttons and switches image</a></li>
                            <li><a href="https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcT-rb-lUMqN76B1KvMnNkh0itkjonKNsT-CTyTU9xpJCA&amp;s=10" target="_blank" rel="noopener noreferrer">Actuators image</a></li>
                            <li><a href="https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcT9o0BgSEUhuB68RaGKtpTAtg1vzPdFOE8HOJS1kcHbrQ&amp;s=10" target="_blank" rel="noopener noreferrer">Digital multimeter image</a></li>
                            <li><a href="https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcSpFeChAv5pz0EXnPNm7DQbDCUPcT0Kl2S40C3BVwaBLA&amp;s=10" target="_blank" rel="noopener noreferrer">Oscilloscope image</a></li>
                            <li><a href="https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcRIkoClSIp6tOl9SYcVuIrms6FIwJrRLgivW_uJWOk4uw&amp;s=10" target="_blank" rel="noopener noreferrer">Arduino Uno R3 image</a></li>
                            <li><a href="https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcSEsz-eTC07gDvFoxlO2fRHAjUn13tXV4zciYb04e7Gmg&amp;s=10" target="_blank" rel="noopener noreferrer">Arduino uploader image</a></li>
                            <li><a href="https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcSa1S0HkWno3o4Xp3I1XpHayTl1BOhNXh6zpFkTa7Q3HA&amp;s=10" target="_blank" rel="noopener noreferrer">Bench power supply image</a></li>
                        </ol>
                    </div>

                    <dialog class="material-image-modal" id="materialImageModal" aria-labelledby="materialImageCaption">
                        <div class="material-image-modal-header">
                            <strong id="materialImageCaption">Material picture</strong>
                            <button type="button" id="closeMaterialImageModal" class="material-modal-close" aria-label="Close picture"><i class="bi bi-x-lg"></i></button>
                        </div>
                        <div class="material-image-modal-body"><img id="materialImagePreview" src="" alt=""></div>
                        <div class="material-image-modal-footer"><a id="materialImageSourceLink" href="#" target="_blank" rel="noopener noreferrer">Open image source</a></div>
                    </dialog>

                    <div class="section-label">9.4 Additional Laboratory Supplies</div>
                    <div class="materials-list">
                        <span><i class="bi bi-check2-circle"></i> Wire cutters and wire strippers</span>
                        <span><i class="bi bi-check2-circle"></i> Long-nose pliers</span>
                        <span><i class="bi bi-check2-circle"></i> Screwdrivers</span>
                        <span><i class="bi bi-check2-circle"></i> Component organizers</span>
                        <span><i class="bi bi-check2-circle"></i> Electrical tape</span>
                        <span><i class="bi bi-check2-circle"></i> Laboratory notebook</span>
                        <span><i class="bi bi-check2-circle"></i> Printed circuit diagrams and activity sheets</span>
                    </div>
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
                    </ol>
                    <div class="result-box warning-box"><strong>Instructor's note</strong><span>The experiments listed are subject to change, modification, or rearrangement by the instructor depending on course coverage, available equipment, student progress, and laboratory conditions. Additional experiments may be assigned as the course progresses.</span></div>
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
                        <li><strong>Commission on Higher Education (CHED).</strong> (2017). <em>CHED Memorandum Order No. 87, Series of 2017: Policies, Standards and Guidelines for the Bachelor of Science in Computer Engineering (BSCpE) Program Effective Academic Year 2018–2019.</em><span>Program-specific policies, standards, and guidelines for the BS Computer Engineering program.</span></li>
                        <li><strong>Commission on Higher Education (CHED).</strong> (2017). <em>CHED Memorandum Order No. 86, Series of 2017: Policies, Standards and Guidelines for Requirements Common to All Bachelor of Science in Engineering and Bachelor of Engineering Technology Programs.</em><span>Common engineering-program requirements and outcomes-based education guidance.</span></li>
                        <li><strong>Commission on Higher Education (CHED).</strong> (2012). <em>CHED Memorandum Order No. 37, Series of 2012: Policies, Standards and Guidelines in the Establishment of an Outcomes-Based Education (OBE) System in Higher Education Institutions Offering Engineering Programs.</em><span>Outcomes-based education guidance for institutions offering engineering programs.</span></li>
                        <li>
                            <strong>Arduino.</strong> (n.d.). <em>Arduino Uno Rev3 – Documentation.</em>

                            <span>Official board documentation, technical specifications, and pin information.</span>
                        </li>
                        <li>
                            <strong>Arduino.</strong> (n.d.). <em>Arduino Language Reference.</em>

                            <span>Reference for Arduino programming language syntax, functions, and constants.</span>
                        </li>
                        <li>
                            <strong>Arduino.</strong> (n.d.). <em>Arduino IDE 2 Documentation.</em>

                            <span>Guidance for installing and using the Arduino development environment.</span>
                        </li>
                        <li>
                            <strong>Microchip Technology Inc.</strong> (n.d.). <em>ATmega328P – 8-bit AVR Microcontroller Data Sheet.</em>

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

            <!-- SECTION 19: REVIEW QUESTIONS — Provides five self-check questions with answers revealed individually by a button. -->
            <div class="theory-card" id="review-questions">
                <div class="theory-header">
                    <div>
                        <h4>19. Five Review Questions and Answers</h4>
                    </div>
                    <div class="theory-badge"><i class="bi bi-patch-question"></i> SELF-ASSESSMENT</div>
                </div>
                <div class="theory-body">
                    <p>Answer each question first before revealing the suggested answer. Use these questions to check your understanding of the topics introduced in this laboratory.</p>
                    <div class="review-question-list">
                        <article class="review-question">
                            <div class="review-question-heading"><button type="button" class="answer-toggle" aria-expanded="false">View answer</button><strong>1. What is the primary purpose of a microcontroller development board such as the Arduino Uno?</strong></div>
                            <div class="review-answer" hidden>It provides a programmable platform for reading inputs, processing instructions, and controlling outputs connected to electronic circuits.</div>
                        </article>
                        <article class="review-question">
                            <div class="review-question-heading"><button type="button" class="answer-toggle" aria-expanded="false">View answer</button><strong>2. Why is a current-limiting resistor normally connected in series with an LED?</strong></div>
                            <div class="review-answer" hidden>It limits the current flowing through the LED and helps prevent damage to the LED and the driving pin.</div>
                        </article>
                        <article class="review-question">
                            <div class="review-question-heading"><button type="button" class="answer-toggle" aria-expanded="false">View answer</button><strong>3. What is the difference between a digital input and a digital output?</strong></div>
                            <div class="review-answer" hidden>A digital input reads a logic state from an external device, while a digital output sets a pin to a logic HIGH or LOW state.</div>
                        </article>
                        <article class="review-question">
                            <div class="review-question-heading"><button type="button" class="answer-toggle" aria-expanded="false">View answer</button><strong>4. When INPUT_PULLUP is used, what logic state is commonly read when the button is pressed and connected to ground?</strong></div>
                            <div class="review-answer" hidden>The input reads LOW because pressing the button connects the pin to ground. When released, the internal pull-up holds it HIGH.</div>
                        </article>
                        <article class="review-question">
                            <div class="review-question-heading"><button type="button" class="answer-toggle" aria-expanded="false">View answer</button><strong>5. What does PWM stand for, and how can it control LED brightness?</strong></div>
                            <div class="review-answer" hidden>PWM means Pulse Width Modulation. It changes the proportion of time a signal stays HIGH during each period (duty cycle), changing the LED's perceived brightness.</div>
                        </article>
                    </div>
                </div>
            </div>

            <!-- SECTION 20: GROUP ACTIVITY -->
            <div class="theory-card" id="group-activity">
                <div class="theory-header">
                    <div>
                        <h4>20. Embedded Systems Group Activity</h4>
                        <p>Form a team and create an identity connected to embedded systems.</p>
                    </div>
                    <div class="theory-badge"><i class="bi bi-people-fill"></i> TEAM FORMATION</div>
                </div>
                <div class="theory-body">
                    <div class="result-box"><strong>Activity: Build Your Embedded Systems Team</strong><span>The class will be organized into exactly eight (8) groups. Each group must work together to choose a unique team name related to embedded systems, microcontrollers, electronics, or automation.</span></div>
                    <h5>Instructions</h5>
                    <ol class="numbered-list">
                        <li>Organize the class into eight (8) groups. The instructor will guide the distribution of members.</li>
                        <li>Discuss the concepts and technologies introduced in this laboratory.</li>
                        <li>Choose one creative and appropriate group name inspired by embedded systems, such as microcontrollers, circuits, sensors, processors, robotics, or automation.</li>
                        <li>Make sure the chosen name is unique and agreed upon by all group members.</li>
                        <li>Select one representative to present the group name and briefly explain its meaning or connection to embedded systems.</li>
                    </ol>
                    <h5>Group Output</h5>
                    <ul class="numbered-list">
                        <li>Group number (1 to 8)</li>
                        <li>Chosen group name</li>
                        <li>Names of all group members</li>
                        <li>A short explanation of why the name is related to embedded systems</li>
                    </ul>
                    <p class="materials-image-note">Reminder: Each group should choose a respectful, classroom-appropriate name. The instructor may ask groups to revise duplicate or unrelated names.</p>
                </div>
            </div>

        </div>
        <footer class="async-lab-footer">
            <p><strong>Post made by Engr. Karl Stephen O. Evallo</strong></p>
            <p>In collaboration with <strong>Engineering and Technological Solutions</strong></p>
            <small>&copy; <span id="labCopyrightYear"></span> Engineering and Technological Solutions. All rights reserved.</small>
        </footer>
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

        .sponsored-ad-slot iframe {
            display: block;
            max-width: 100%;
            border: 0;
        }

        .sponsored-ad-slot--third {
            min-height: 300px;
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
        (() => {
            const modal = document.getElementById('schematicSymbolsModal');
            const openButton = document.getElementById('openSchematicSymbolsModal');
            const closeButton = document.getElementById('closeSchematicSymbolsModal');
            if (!modal || !openButton || !closeButton) return;
            openButton.addEventListener('click', () => {
                if (typeof modal.showModal === 'function') modal.showModal();
                else window.open('https://www.electronics-tutorials.ws/resources/basic-schematic-symbols.html', '_blank', 'noopener');
            });
            closeButton.addEventListener('click', () => modal.close());
            modal.addEventListener('click', event => {
                if (event.target === modal) modal.close();
            });
            document.addEventListener('keydown', event => {
                if (event.key === 'Escape' && modal.open) modal.close();
            });
        })();

        // Section 9: load the instructor-supplied material image only when its icon is clicked.
        (() => {
            const modal = document.getElementById('materialImageModal');
            const preview = document.getElementById('materialImagePreview');
            const caption = document.getElementById('materialImageCaption');
            const sourceLink = document.getElementById('materialImageSourceLink');
            const closeButton = document.getElementById('closeMaterialImageModal');
            if (!modal || !preview || !caption || !sourceLink || !closeButton) return;
            document.querySelectorAll('.material-image-trigger').forEach(button => {
                button.addEventListener('click', () => {
                    const imageUrl = button.dataset.image;
                    if (!imageUrl) return;
                    caption.textContent = button.dataset.caption || 'Material picture';
                    preview.src = imageUrl;
                    preview.alt = caption.textContent;
                    sourceLink.href = imageUrl;
                    if (typeof modal.showModal === 'function') modal.showModal();
                    else window.open(imageUrl, '_blank', 'noopener');
                });
            });
            const closeModal = () => {
                if (modal.open) modal.close();
            };
            closeButton.addEventListener('click', closeModal);
            modal.addEventListener('click', event => {
                if (event.target === modal) closeModal();
            });
            modal.addEventListener('close', () => {
                preview.removeAttribute('src');
                preview.alt = '';
            });
        })();

        const yearNode = document.getElementById('labCopyrightYear');
        if (yearNode) yearNode.textContent = new Date().getFullYear();
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