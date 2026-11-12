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
                    <h2>Embedded Systems: History, Structure, and Design</h2>
                    <p>A college-level lecture based on Chapter 1 of Jiménez, Palomera, and Couvertier, introducing the evolution, organization, classification, life cycle, and engineering constraints of embedded systems.</p>
                </div>
            </div>
            <button type="button" class="toc-floating-button" id="tocFloatingButton" aria-controls="table-of-contents" aria-expanded="false"><i class="bi bi-list-ul"></i><span>Contents</span></button>
            <div class="theory-card toc-floating-panel" id="table-of-contents" aria-hidden="true">
                <div class="theory-header">
                    <div>
                        <h4>Lecture Contents</h4>
                        <p>Navigate through Chapter 1.</p>
                    </div><button type="button" class="toc-minimize-button" id="tocMinimizeButton" aria-label="Minimize table of contents"><i class="bi bi-chevron-left"></i></button>
                </div>
                <div class="theory-body">
                    <div class="toc-grid"><a href="#introduction"><span>01</span><strong>Introduction</strong><i class="bi bi-arrow-up-right"></i></a><a href="#history-overview"><span>02</span><strong>Embedded Systems: History and Overview</strong><i class="bi bi-arrow-up-right"></i></a><a href="#structure"><span>03</span><strong>Structure of an Embedded System</strong><i class="bi bi-arrow-up-right"></i></a><a href="#classification"><span>04</span><strong>Classification of Embedded Systems</strong><i class="bi bi-arrow-up-right"></i></a><a href="#lifecycle"><span>05</span><strong>The Life Cycle of Embedded Designs</strong><i class="bi bi-arrow-up-right"></i></a><a href="#constraints"><span>06</span><strong>Design Constraints</strong><i class="bi bi-arrow-up-right"></i></a><a href="#summary"><span>07</span><strong>Chapter Summary</strong><i class="bi bi-arrow-up-right"></i></a><a href="#lecture-notes"><span>08</span><strong>Lecture Notes and Key Terminology</strong><i class="bi bi-arrow-up-right"></i></a><a href="#exercises"><span>09</span><strong>Problems and Written Exercises</strong><i class="bi bi-arrow-up-right"></i></a><a href="#review-questions"><span>10</span><strong>Review Questions</strong><i class="bi bi-arrow-up-right"></i></a><a href="#references"><span>11</span><strong>References</strong><i class="bi bi-arrow-up-right"></i></a></div>
                </div>
            </div>
            <section class="theory-card" id="introduction">
                <div class="theory-header">
                    <div>
                        <h4>1. Introduction</h4>
                    </div>
                    <div class="theory-badge"><i class="bi bi-cpu"></i> LECTURE 01</div>
                </div>
                <div class="theory-body">
                    <p>Embedded systems are computing systems designed to operate as part of a larger product, machine, or process. They combine electronic hardware and software to carry out a defined purpose, such as measuring temperature, controlling a motor, managing a communication link, or coordinating the operation of a household appliance.</p>
                    <p>Unlike a desktop computer, which is designed to support many unrelated applications selected by its user, an embedded system is usually developed around the requirements of a particular application. Its processor, memory, input/output interfaces, firmware, power supply, and physical design are selected to meet that application’s functional and operational needs.</p>
                    <div class="concept-box"><strong>Central idea</strong><span>An embedded system is not simply a small computer. It is a purpose-built computing element integrated into a larger system, where hardware and software work together to perform a defined function.</span></div>
                    <p>Embedded systems are found in transportation, communication, manufacturing, healthcare, consumer electronics, energy management, and many other fields. Their importance comes from their ability to monitor physical conditions, process information, make decisions, and control physical processes.</p>
                    <div class="application-grid">
                        <div class="application-item"><i class="bi bi-car-front"></i><strong>Automotive</strong><span>Engine control units, anti-lock braking systems, airbag controllers, and driver-assistance features.</span></div>
                        <div class="application-item"><i class="bi bi-house-gear"></i><strong>Consumer products</strong><span>Washing machines, microwave ovens, televisions, cameras, and smart appliances.</span></div>
                        <div class="application-item"><i class="bi bi-heart-pulse"></i><strong>Healthcare</strong><span>Patient monitors, infusion pumps, portable diagnostic instruments, and medical imaging equipment.</span></div>
                        <div class="application-item"><i class="bi bi-factory"></i><strong>Industrial systems</strong><span>Motor drives, programmable controllers, process monitoring, and automated production equipment.</span></div>
                    </div>
                    <p>This lecture follows the organization of Chapter 1 of Jiménez, Palomera, and Couvertier’s <em>Introduction to Embedded Systems: Using Microcontrollers and the MSP430</em>. It introduces the historical development of embedded systems, their structure and classification, the life cycle of an embedded design, and the constraints that influence engineering decisions.</p>
                </div>
            </section>
            <section class="theory-card" id="history-overview">
                <div class="theory-header">
                    <div>
                        <h4>1.1 Embedded Systems: History and Overview</h4>
                    </div>
                    <div class="theory-badge"><i class="bi bi-cpu"></i> HISTORY AND OVERVIEW</div>
                </div>
                <div class="theory-body">
                    <p>The development of embedded systems is closely connected to the evolution of computing technology. Early machines used mechanical and electromechanical mechanisms to control specific operations. Later, electronic circuits and digital computers made it possible to perform more complex control tasks. The introduction of microprocessors and microcontrollers made programmable computing compact and economically practical for integration into products.</p>
                    <p>An embedded system is commonly characterized by a close relationship between its hardware and software, a defined application, and operating requirements that may include timing, power, size, cost, and reliability constraints. Many embedded systems interact directly with the physical world and must respond to events within an expected time.</p>
                    <div class="section-label">1.1.1 Early Forms of Embedded Systems</div>
                    <p>Before the availability of inexpensive microcontrollers, dedicated control was implemented through mechanical, electromechanical, and electronic arrangements. These early systems were not necessarily embedded computers in the modern integrated-circuit sense, but they performed specialized functions within larger machines.</p>
                    <p><strong>Mechanical control.</strong> Mechanisms such as gears, cams, levers, springs, and governors could regulate motion or maintain a physical condition. A mechanical governor, for example, can adjust a machine’s operation in response to changes in rotational speed. The control behavior is determined by the physical arrangement of the mechanism rather than by a stored computer program.</p>
                    <p><strong>Electromechanical control.</strong> Relays, switches, contactors, and timers introduced electrical control into machines. A relay circuit could start a motor when a switch closed, stop it when a limit switch was reached, or sequence operations using timed contacts. Such arrangements were useful but could become bulky and difficult to modify as the number of control conditions increased.</p>
                    <p><strong>Analog electronic control.</strong> Analog circuits used continuously varying electrical quantities, such as voltage or current, to represent physical signals. Amplifiers, comparators, oscillators, and other circuit elements could implement sensing and control functions. These systems could respond quickly, although changing their behavior often required modifying circuit components or wiring.</p>
                    <p><strong>Early electronic computers and guidance systems.</strong> Early computers demonstrated that programmable digital computation could be dedicated to a specialized mission. A well-known example discussed in the history of embedded computing is the Apollo Guidance Computer (AGC), which supported guidance and navigation functions in the Apollo spacecraft. It was a specialized computer designed around strict size, weight, power, and operational requirements. It was far less integrated than today's microcontroller-based products, but it demonstrated the value of dedicated computing within a larger system.</p>
                    <div class="result-box"><strong>Historical lesson</strong><span>The earliest systems established the essential control pattern: obtain information about a process, determine what action is required, and cause a physical response. Modern embedded systems implement this pattern using programmable digital hardware and software.</span></div>
                    <div class="section-label">1.1.2 Birth and Evolution of Modern Embedded Systems</div>
                    <p>Modern embedded systems emerged as semiconductor technology made digital processing smaller, more affordable, and more reliable. Two important developments were the microprocessor and the microcontroller. A microprocessor integrates the central processing unit (CPU) on a chip and commonly relies on external memory and peripheral devices. A microcontroller typically combines a CPU, memory, and a set of peripherals—such as timers and input/output interfaces—within a single integrated circuit.</p>
                    <p>The Intel 4004, introduced in 1971, is widely recognized as the first commercially available single-chip microprocessor. It helped demonstrate that programmable processing could be implemented in a compact integrated circuit. The Texas Instruments TMS1000, introduced in the 1970s, is an early example of a single-chip microcontroller designed for embedded control applications. These developments helped make dedicated computing practical in calculators, appliances, instruments, and industrial products.</p>
                    <div class="section-label">Pioneers in the Birth of Microprocessors and Microcontrollers</div>
                    <div class="pioneer-image-grid" style="display:grid;grid-template-columns:repeat(auto-fit,minmax(240px,1fr));gap:18px;margin:18px 0;">
                        <figure class="pioneer-figure" style="margin:0;padding:14px;border:1px solid var(--border-color, #ddd);border-radius:12px;">
                            <img src="https://computerhistory.org/wp-content/uploads/2019/08/microprocessor-gary-boone-tmx1795.jpg" alt="Gary Boone and the Texas Instruments TMX1795 chip" loading="lazy" style="display:block;width:100%;height:230px;object-fit:contain;border-radius:8px;background:#f4f4f4;">
                            <figcaption style="margin-top:10px;"><strong>Gary W. Boone</strong><br><span>Texas Instruments engineer associated with the TMX1795 and early single-chip microcomputer development.</span><br><small>Image/source: Computer History Museum</small></figcaption>
                        </figure>
                        <figure class="pioneer-figure" style="margin:0;padding:14px;border:1px solid var(--border-color, #ddd);border-radius:12px;">
                            <img src="https://commons.wikimedia.org/wiki/Special:FilePath/Federico_Faggin_(cropped).jpg" alt="Portrait of Federico Faggin" loading="lazy" style="display:block;width:100%;height:230px;object-fit:contain;border-radius:8px;background:#f4f4f4;">
                            <figcaption style="margin-top:10px;"><strong>Federico Faggin</strong><br><span>Project leader and detailed chip designer for the Intel 4004 and MCS-4 family.</span><br><small>Image/source: Wikimedia Commons</small></figcaption>
                        </figure>
                    </div>
                    <p><strong>Gary Boone and the Texas Instruments development team.</strong> Engineer Gary W. Boone, working with Michael J. Cochran at Texas Instruments (TI), helped pioneer the single-chip microcomputer. Their early work led to the TMS1000 family, introduced in the 1970s. The TMS1000 combined a processor, program memory, data memory, and input/output capabilities on one chip. This integration was especially important for embedded products because a designer could build a dedicated controller with fewer separate components. The TMS1000 family was used in products such as calculators, toys, games, and other consumer devices. Boone's contribution belongs to the development of the single-chip microcomputer/microcontroller; it should not be confused with the separate Intel 4004 project.</p>
                    <p><strong>Federico Faggin and the Intel 4004 design team.</strong> The Intel 4004 grew out of a 1969 project for the Japanese calculator company Busicom. Intel engineers Marcian “Ted” Hoff and Stanley Mazor helped develop the architecture and instruction-set concept for a compact, programmable processor solution. Federico Faggin joined Intel in 1970 and took over the detailed design leadership for the MCS-4 chip set, including the 4004. Busicom engineer Masatoshi Shima also contributed to the project and worked closely with Faggin. The 4-bit Intel 4004 was introduced in 1971 and is widely recognized as the first commercially available general-purpose microprocessor. It placed the CPU on a single chip, while the broader MCS-4 family supplied supporting memory and interface functions.</p>
                    <div class="course-info-table-wrap">
                        <table class="course-info-table">
                            <thead>
                                <tr>
                                    <th>Person / team</th>
                                    <th>Organization / project</th>
                                    <th>Historical contribution</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>Gary Boone and Michael Cochran</td>
                                    <td>Texas Instruments – TMS1000</td>
                                    <td>Pioneered the single-chip microcomputer approach that integrated processing, memory, and I/O for dedicated control applications.</td>
                                </tr>
                                <tr>
                                    <td>Ted Hoff and Stanley Mazor</td>
                                    <td>Intel – MCS-4 / 4004</td>
                                    <td>Helped formulate the architecture and instruction-set concept for the programmable processor solution.</td>
                                </tr>
                                <tr>
                                    <td>Federico Faggin</td>
                                    <td>Intel – MCS-4 / 4004</td>
                                    <td>Led the detailed chip design and implementation of the 4004 family.</td>
                                </tr>
                                <tr>
                                    <td>Masatoshi Shima</td>
                                    <td>Busicom / Intel collaboration</td>
                                    <td>Contributed calculator-system requirements and collaborated on the chip design with Intel's team.</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="concept-box"><strong>Important distinction</strong><span>The Intel 4004 was a microprocessor: it integrated the CPU and required supporting chips for a complete system. The TI TMS1000 was a microcontroller: it integrated a CPU with memory and I/O resources for dedicated control. Both were milestones, but they addressed related yet different design needs.</span></div>
                    <p>As semiconductor manufacturing improved, microcontrollers became available in different word sizes and architectures. Manufacturers introduced devices with varying amounts of memory, processing capability, peripheral functions, and power consumption. This allowed designers to select a controller suited to the needs and cost limits of a particular product.</p>
                    <p>Modern embedded development also benefited from improvements in programming languages, development environments, debugging tools, communication interfaces, and reusable software components. Instead of building every function from discrete components, designers could integrate a processor or microcontroller with sensors, memory, communication modules, and application-specific circuits.</p>
                    <div class="section-label">Visual Guide: Key Technologies Across the Stages</div>
                    <p>The images below illustrate representative technologies in the historical progression of embedded and control systems. The governor represents mechanical feedback; the transistor represents solid-state electronic control; the microprocessor and microcontroller represent programmable integrated circuits; and wireless links represent connected embedded devices.</p>
                    <div class="technology-timeline">
                        <article class="technology-row">
                            <figure class="technology-image">
                                <img src="https://miro.medium.com/v2/resize%3Afit%3A1120/1%2AccLfx799EGZrCUleKsbypQ.jpeg" alt="Watt steam engine with a centrifugal mechanical governor" loading="lazy">
                                <figcaption>Historical mechanical control</figcaption>
                            </figure>
                            <div class="technology-description">
                                <span class="technology-number">STAGE 01</span>
                                <h5>Mechanical Governor</h5>
                                <p>A centrifugal governor uses rotating weights and linkages to regulate engine speed as operating conditions change. It represents an early form of automatic feedback control in which physical motion directly adjusts a machine's operation.</p>
                                <small>Image: Robin Christen / Medium</small>
                            </div>
                        </article>
                        <article class="technology-row">
                            <figure class="technology-image">
                                <img src="https://commons.wikimedia.org/wiki/Special:FilePath/Transistor_-_1.jpg" alt="BC547 NPN transistor in a TO-92 package" loading="lazy">
                                <figcaption>Solid-state electronic component</figcaption>
                            </figure>
                            <div class="technology-description">
                                <span class="technology-number">STAGE 02</span>
                                <h5>Transistor</h5>
                                <p>A transistor is a semiconductor device used for electronic switching and amplification. It enabled smaller, faster, and more reliable electronic control circuits than many earlier vacuum-tube arrangements.</p>
                                <small>Image: Pxchanh / Wikimedia Commons, CC0</small>
                            </div>
                        </article>
                        <article class="technology-row">
                            <figure class="technology-image">
                                <img src="https://commons.wikimedia.org/wiki/Special:FilePath/Intel_4004_open.jpg" alt="Opened Intel 4004 microprocessor package" loading="lazy">
                                <figcaption>Early single-chip microprocessor</figcaption>
                            </figure>
                            <div class="technology-description">
                                <span class="technology-number">STAGE 03</span>
                                <h5>Microprocessor</h5>
                                <p>A microprocessor integrates the central processing unit (CPU) on a chip. A complete system commonly uses external memory and peripheral devices. The Intel 4004, introduced in 1971, is a landmark example.</p>
                                <small>Image: Science Museum / Wikimedia Commons, CC BY 4.0</small>
                            </div>
                        </article>
                        <article class="technology-row">
                            <figure class="technology-image">
                                <img src="https://commons.wikimedia.org/wiki/Special:FilePath/ATMEGA328P.jpg" alt="ATmega328P microcontroller used on Arduino boards" loading="lazy">
                                <figcaption>Integrated microcontroller</figcaption>
                            </figure>
                            <div class="technology-description">
                                <span class="technology-number">STAGE 04</span>
                                <h5>Microcontroller</h5>
                                <p>A microcontroller combines a CPU, memory, and peripherals in one chip for dedicated embedded control tasks. The ATmega328P, used on the Arduino Uno, is a familiar example for introductory embedded systems activities.</p>
                                <small>Image: CCDBarcodeScanner / Wikimedia Commons, CC BY-SA</small>
                            </div>
                        </article>
                        <article class="technology-row">
                            <figure class="technology-image">
                                <img src="https://bizweb.dktcdn.net/100/027/321/files/3-64ef452b-b640-4bb4-b8ae-211ffd234d8a.png?v=1598957103174" alt="Wireless network links connecting routers and computing devices" loading="lazy">
                                <figcaption>Connected embedded devices</figcaption>
                            </figure>
                            <div class="technology-description">
                                <span class="technology-number">STAGE 05</span>
                                <h5>Wireless Links</h5>
                                <p>Wireless technologies such as Wi-Fi, Bluetooth, and cellular networks allow embedded devices to exchange data without direct cable connections. Connectivity supports remote monitoring, coordination, and Internet of Things applications.</p>
                                <small>Image: IDC Group</small>
                            </div>
                        </article>
                    </div>
                    <div class="course-info-table-wrap">
                        <table class="course-info-table">
                            <thead>
                                <tr>
                                    <th>Technology stage</th>
                                    <th>Important development</th>
                                    <th>Engineering significance</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>Mechanical and electromechanical</td>
                                    <td>Gears, governors, relays, switches, and timers</td>
                                    <td>Enabled dedicated automatic operation without general-purpose digital processing.</td>
                                </tr>
                                <tr>
                                    <td>Electronic control</td>
                                    <td>Transistors and analog circuits</td>
                                    <td>Improved speed, reliability, and electronic signal processing.</td>
                                </tr>
                                <tr>
                                    <td>Microprocessor era</td>
                                    <td>Programmable CPU integrated on a chip</td>
                                    <td>Made flexible digital computation practical in products.</td>
                                </tr>
                                <tr>
                                    <td>Microcontroller era</td>
                                    <td>CPU, memory, and peripherals integrated in one device</td>
                                    <td>Reduced component count and supported compact, cost-effective control.</td>
                                </tr>
                                <tr>
                                    <td>Connected and intelligent systems</td>
                                    <td>Networking, wireless links, advanced processing, and system integration</td>
                                    <td>Enabled devices to exchange data, coordinate functions, and perform more sophisticated tasks.</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="section-label">1.1.3 Contemporary Embedded Systems</div>
                    <p>Contemporary embedded systems are often more complex than a single controller performing one isolated task. A modern product may contain several processing units, dedicated integrated circuits, memory devices, communication modules, power-management circuits, and user-interface components. Each component may itself contain embedded processing capability. The integration of these subsystems is sometimes described as <strong>multi-embedding</strong>.</p>
                    <p>Consider a modern multimedia or smart appliance. It may combine a user interface, audio or video processing, data storage, wireless communication, power management, and application control. These functions must operate together even though each has different processing, timing, and resource requirements.</p>
                    <div class="definition-list">
                        <div><strong>Power management</strong><span>Controls how energy is supplied, converted, monitored, and conserved across system components.</span></div>
                        <div><strong>Communication</strong><span>Allows the device to exchange information with other devices, networks, or cloud services.</span></div>
                        <div><strong>Audio and video processing</strong><span>Handles digital media signals, encoding, decoding, filtering, and presentation.</span></div>
                        <div><strong>Memory and storage</strong><span>Stores program instructions, temporary data, configuration information, and user content.</span></div>
                        <div><strong>User interfaces</strong><span>Provide buttons, touchscreens, indicators, displays, and other ways for people to interact with the product.</span></div>
                        <div><strong>Dedicated hardware</strong><span>Application-specific integrated circuits (ASICs) or specialized accelerators perform selected tasks efficiently.</span></div>
                    </div>
                    <p>Contemporary systems may also be connected to the Internet of Things (IoT), use wireless sensor networks, or perform data processing close to where information is collected. However, connectivity is not a requirement for every embedded system. A simple appliance controller can still be an embedded system even if it never connects to a network.</p>
                </div>
            </section>
            <div class="sponsored-card" aria-label="Advertisement">
                <div class="ad-label">Advertisement</div>
                <div class="sponsored-ad-slot">
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
                        })({});
                    </script>
                </div>
            </div>
            <section class="theory-card" id="structure">
                <div class="theory-header">
                    <div>
                        <h4>1.2 Structure of an Embedded System</h4>
                    </div>
                    <div class="theory-badge"><i class="bi bi-cpu"></i> HARDWARE AND SOFTWARE</div>
                </div>
                <div class="theory-body">
                    <p>An embedded system is built from hardware and software components that are designed to operate as one application. Hardware provides the physical computing and interfacing resources. Software defines the behavior of the system and coordinates the use of those resources.</p>
                    <div class="section-label">1.2.1 Hardware Components</div>
                    <p>The hardware structure provides the electronic infrastructure required to execute instructions, store information, communicate with external devices, and interact with the physical environment. A general embedded hardware organization includes a processor, memory, and an input/output subsystem.</p>
                    <div class="definition-list">
                        <div><strong>Central Processing Unit (CPU)</strong><span>Executes instructions and controls system operations. Its internal elements commonly include registers, an arithmetic logic unit (ALU), and a control unit.</span></div>
                        <div><strong>Registers</strong><span>Small, fast storage locations inside the processor that hold operands, addresses, status information, and intermediate results.</span></div>
                        <div><strong>Arithmetic Logic Unit (ALU)</strong><span>Performs arithmetic and logical operations such as addition, subtraction, comparisons, and bitwise operations.</span></div>
                        <div><strong>Control Unit</strong><span>Coordinates instruction execution and directs data movement among the processor, memory, and peripherals.</span></div>
                        <div><strong>Program memory</strong><span>Stores the instructions that define the embedded application. Flash memory is commonly used in microcontrollers.</span></div>
                        <div><strong>Data memory</strong><span>Stores variables, temporary values, buffers, and runtime information. RAM is commonly used for this purpose.</span></div>
                        <div><strong>Input/output subsystem</strong><span>Connects the processor to external devices through GPIO pins, communication interfaces, converters, and peripheral controllers.</span></div>
                        <div><strong>Sensors and actuators</strong><span>Sensors provide information about physical conditions; actuators perform actions such as moving a motor, switching a relay, or activating an alarm.</span></div>
                        <div><strong>Power supply and clock</strong><span>Provide the required electrical energy and timing reference for the system's operation.</span></div>
                    </div>
                    <div class="section-label">1.2.2 Software Components</div>
                    <p>Software gives the hardware its application-specific behavior. In embedded systems, the software stored in nonvolatile memory and executed by the controller is commonly called <strong>firmware</strong>. Depending on system complexity, the software may include low-level routines, device drivers, a system kernel or operating system, and application programs.</p>
                    <div class="definition-list">
                        <div><strong>Firmware</strong><span>Program instructions that control the embedded device and directly interact with its hardware resources.</span></div>
                        <div><strong>Device drivers</strong><span>Software routines that provide a consistent way for higher-level software to use hardware peripherals.</span></div>
                        <div><strong>System kernel or operating system</strong><span>When present, manages system resources, schedules tasks, and coordinates services. Small systems may operate without a full operating system.</span></div>
                        <div><strong>Application tasks</strong><span>Implement the product's intended functions, such as reading sensors, updating a display, controlling a motor, or communicating with another device.</span></div>
                        <div><strong>Services and libraries</strong><span>Reusable routines that support common functions such as timing, communication, data conversion, and diagnostics.</span></div>
                    </div>
                    <p>Hardware and software must be designed together. A software algorithm may require a particular timer, memory capacity, or communication interface. Conversely, the available hardware resources can limit the complexity, speed, and features of the software.</p>
                    <div class="code-example">
                        <div class="code-example-header"><strong>Conceptual hardware–software relationship</strong><span>SYSTEM VIEW</span></div>
                        <pre><code>Physical process
      ↓
Sensor / Input Interface
      ↓
Processor + Memory  ←→  Firmware / Application Tasks
      ↓
Output Interface / Driver
      ↓
Actuator or Display
      ↓
Physical response</code></pre>
                    </div>
                    <p>The diagram is a conceptual representation. Actual systems may include feedback paths, multiple processors, several communication buses, and many input/output devices.</p>
                </div>
            </section>
            <section class="theory-card" id="classification">
                <div class="theory-header">
                    <div>
                        <h4>1.3 Classification of Embedded Systems</h4>
                    </div>
                    <div class="theory-badge"><i class="bi bi-cpu"></i> SYSTEM CATEGORIES</div>
                </div>
                <div class="theory-body">
                    <p>Embedded systems can be classified according to their hardware organization, processing demands, application complexity, cost, and maintenance requirements. Jiménez and co-authors introduce three broad categories: small, distributed, and high-performance embedded systems. These categories help explain why different applications require different system architectures.</p>
                    <div class="section-label">1.3.1 Small Embedded Systems</div>
                    <p>Small embedded systems are commonly based on a microcontroller with a relatively low component count. They are often designed for high-volume production, low cost, and a limited set of functions. Many are single-purpose devices that operate with little user intervention and require minimal maintenance.</p>
                    <p>Examples include a basic digital thermometer, a simple electronic timer, a small appliance controller, or a basic remote-control device. An Arduino Uno classroom project can demonstrate the principles of a small microcontroller-based system, although a commercial product may use a different controller selected for cost, size, power, and production requirements.</p>
                    <div class="section-label">1.3.2 Distributed Embedded Systems</div>
                    <p>A distributed embedded system uses multiple processing units or electronic nodes that cooperate to perform a larger task. The components may be located on different boards or within different parts of a product. They exchange information through communication links and coordinate their activities.</p>
                    <p>Distributed arrangements are useful when a system contains several subsystems with distinct responsibilities. For example, a vehicle may use separate electronic control units for engine management, braking, body electronics, and infotainment. The overall product depends on communication and coordination among these embedded controllers.</p>
                    <div class="section-label">1.3.3 High-Performance Embedded Systems</div>
                    <p>High-performance embedded systems are designed for demanding computational workloads, sophisticated functionality, or stringent response requirements. They may use more capable processors, dedicated hardware accelerators, larger memory resources, and real-time operating systems. Their development and maintenance can be more complex and costly than those of small embedded systems.</p>
                    <p>Examples include advanced medical imaging equipment, radar and signal-processing systems, industrial vision equipment, aerospace systems, and complex telecommunications infrastructure. High performance does not simply mean a high clock frequency; it refers to the ability to meet the application's overall processing, throughput, timing, and reliability requirements.</p>
                    <div class="course-info-table-wrap">
                        <table class="course-info-table">
                            <thead>
                                <tr>
                                    <th>Category</th>
                                    <th>Typical organization</th>
                                    <th>Common characteristics</th>
                                    <th>Illustrative example</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>Small</td>
                                    <td>Often one MCU-based controller</td>
                                    <td>Low component count, low cost, focused task, high-volume production</td>
                                    <td>Digital thermostat</td>
                                </tr>
                                <tr>
                                    <td>Distributed</td>
                                    <td>Multiple connected controllers or boards</td>
                                    <td>Cooperating tasks, communication between nodes, integration and upgrade needs</td>
                                    <td>Vehicle electronic control network</td>
                                </tr>
                                <tr>
                                    <td>High-performance</td>
                                    <td>Powerful processor and/or specialized hardware</td>
                                    <td>Intensive computation, demanding timing, greater complexity and cost</td>
                                    <td>Radar signal-processing unit</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <p>These categories are broad engineering descriptions rather than rigid boundaries. A product can combine characteristics from more than one category, and the appropriate classification depends on the architecture and requirements being considered.</p>
                </div>
            </section>
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
                        })({});
                    </script>
                </div>
            </div>
            <section class="theory-card" id="lifecycle">
                <div class="theory-header">
                    <div>
                        <h4>1.4 The Life Cycle of Embedded Designs</h4>
                    </div>
                    <div class="theory-badge"><i class="bi bi-cpu"></i> DESIGN LIFE CYCLE</div>
                </div>
                <div class="theory-body">
                    <p>An embedded design passes through a series of stages from the recognition of a need to the eventual retirement of the product. Thinking about the full life cycle helps engineers plan not only the initial design but also manufacturing, deployment, maintenance, upgrades, and disposal.</p>
                    <div class="section-label">1.4.1 Birth</div>
                    <p>The birth stage begins when a need, problem, or market opportunity is identified. Engineers and stakeholders define what the system should accomplish and establish initial requirements. These requirements may describe functions, operating conditions, interfaces, safety expectations, cost limits, size, power consumption, and performance.</p>
                    <p>A clear specification is essential because it provides a reference for design decisions and later verification. Ambiguous or incomplete requirements can result in a product that works technically but does not solve the intended problem.</p>
                    <div class="section-label">1.4.2 Design</div>
                    <p>During the design stage, engineers develop a solution that satisfies the specifications. This may include selecting a processor or microcontroller, designing electronic circuits, planning the software architecture, choosing sensors and actuators, and defining communication interfaces.</p>
                    <p>A proof of concept or prototype can be used to test whether the proposed approach is feasible. The design is then refined for production, considering component availability, manufacturing processes, testing, safety, and long-term support.</p>
                    <div class="section-label">1.4.3 Growth</div>
                    <p>The growth stage involves production, deployment, and increasing use of the product. Manufacturing processes are established, units are tested, and the system is delivered to users or integrated into a larger product. Feedback from actual use may reveal issues that were not obvious during initial development.</p>
                    <div class="section-label">1.4.4 Maturity</div>
                    <p>At maturity, the product is established and continues to operate in its intended environment. Engineering work may focus on maintenance, repairs, software updates, replacement parts, performance improvements, and compatibility with other systems.</p>
                    <div class="section-label">1.4.5 Decline</div>
                    <p>A product may eventually become obsolete because of changing requirements, unavailable components, new technologies, security concerns, or the cost of continued support. The decline stage includes planning for replacement, decommissioning, data handling, recycling, and safe disposal.</p>
                    <div class="code-example">
                        <div class="code-example-header"><strong>Embedded design life cycle</strong><span>SEQUENCE</span></div>
                        <pre><code>Need / Opportunity
        ↓
Birth: requirements and specifications
        ↓
Design: architecture, prototype, verification
        ↓
Growth: manufacturing and deployment
        ↓
Maturity: maintenance and upgrades
        ↓
Decline: retirement and disposal</code></pre>
                    </div>
                    <p>Not every product follows an identical path or remains in each stage for the same duration. Some designs are cancelled before production, while others are updated or redesigned and remain in service for many years. The important principle is that the engineer should plan for the complete life of the product rather than focusing only on the prototype.</p>
                </div>
            </section>
            <section class="theory-card" id="constraints">
                <div class="theory-header">
                    <div>
                        <h4>1.5 Design Constraints</h4>
                    </div>
                    <div class="theory-badge"><i class="bi bi-cpu"></i> ENGINEERING TRADE-OFFS</div>
                </div>
                <div class="theory-body">
                    <p>Embedded systems are developed under constraints. Unlike a general-purpose computer, an embedded product may have strict limits on size, energy, processing capability, cost, response time, and operating environment. A successful design must balance these requirements while providing the required functionality.</p>
                    <div class="section-label">1.5.1 Functionality</div>
                    <p>Functionality describes what the system is required to do. It includes the operations, responses, interfaces, and behaviors specified for the product. Requirements should be clear and testable. For example, an automatic fan controller may be required to measure temperature, activate a fan above a defined threshold, and provide an indicator of its operating state.</p>
                    <p>Functional verification determines whether the design performs the specified tasks correctly. Because embedded systems interact with physical processes, verification may involve simulation, software testing, hardware testing, and testing under realistic operating conditions.</p>
                    <div class="section-label">1.5.2 Cost</div>
                    <p>Cost includes the resources required to conceive, design, develop, manufacture, test, deploy, and maintain the system. It is not limited to the price of the microcontroller. Engineers must also consider sensors, circuit boards, connectors, enclosures, software development, production tooling, testing equipment, support, and warranty obligations.</p>
                    <p>A design that uses a more powerful processor than necessary may increase the unit cost without improving the product in a meaningful way. Conversely, choosing components that are too inexpensive or poorly suited to the application can increase failure rates and long-term maintenance costs.</p>
                    <div class="section-label">1.5.3 Performance</div>
                    <p>Performance describes how effectively and quickly the system performs its required functions. Depending on the application, performance may be measured using processing time, throughput, response latency, sampling rate, accuracy, or the number of tasks handled concurrently.</p>
                    <p>Both hardware and software affect performance. Processor speed, memory bandwidth, peripheral design, algorithm efficiency, communication overhead, and task scheduling can all influence the system's behavior. In real-time applications, a correct result delivered too late may be considered a failure.</p>
                    <div class="section-label">1.5.4 Power and Energy</div>
                    <p>Power is the rate at which electrical energy is consumed, while energy is the total amount consumed over time. Battery-operated products must often minimize energy use to extend operating life. Even mains-powered systems may have thermal, efficiency, or regulatory requirements that make power management important.</p>
                    <p>Engineers can reduce energy consumption by selecting suitable low-power components, using sleep or standby modes, disabling unused peripherals, reducing unnecessary processing, and choosing efficient communication strategies. Power management must be balanced against responsiveness: a device that sleeps too deeply may take too long to respond to an event.</p>
                    <div class="section-label">1.5.5 Time-to-Market</div>
                    <p>Time-to-market is the time required to move from the initial concept to a product that is ready for deployment or sale. Delays can affect competitiveness, project cost, and the ability to meet customer needs. Development schedules therefore influence decisions about architecture, component selection, prototyping, testing, and reuse of existing hardware or software.</p>
                    <p>Reducing development time must not mean skipping essential verification. A product released quickly but containing serious defects may incur much greater costs through recalls, repairs, safety incidents, or loss of user confidence.</p>
                    <div class="section-label">1.5.6 Reliability and Maintainability</div>
                    <p><strong>Reliability</strong> is the ability of a system to perform its required function under stated conditions for a specified period. Reliability is influenced by component quality, circuit design, thermal conditions, electrical noise, software correctness, environmental protection, and manufacturing quality.</p>
                    <p><strong>Maintainability</strong> describes how easily a system can be inspected, repaired, updated, or restored to operation. Good documentation, modular design, diagnostic features, accessible components, software update mechanisms, and availability of replacement parts can improve maintainability.</p>
                    <p>Reliability and maintainability are related but different. A highly reliable device may rarely fail, while a maintainable device can be restored efficiently when a fault occurs. Many real applications require both.</p>
                    <div class="course-info-table-wrap">
                        <table class="course-info-table">
                            <thead>
                                <tr>
                                    <th>Constraint</th>
                                    <th>Key engineering question</th>
                                    <th>Example consideration</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>Functionality</td>
                                    <td>Does it perform the required task?</td>
                                    <td>Correctly detects a temperature threshold and controls a fan.</td>
                                </tr>
                                <tr>
                                    <td>Cost</td>
                                    <td>Can it be developed and produced within budget?</td>
                                    <td>Choose an MCU with sufficient capability without unnecessary features.</td>
                                </tr>
                                <tr>
                                    <td>Performance</td>
                                    <td>Does it process information and respond quickly enough?</td>
                                    <td>Meet the sampling and response deadlines of a control application.</td>
                                </tr>
                                <tr>
                                    <td>Power and energy</td>
                                    <td>Can it operate within the available energy budget?</td>
                                    <td>Extend battery life using sleep modes and efficient peripherals.</td>
                                </tr>
                                <tr>
                                    <td>Time-to-market</td>
                                    <td>Can it be completed and deployed on schedule?</td>
                                    <td>Use available development tools and verify the design efficiently.</td>
                                </tr>
                                <tr>
                                    <td>Reliability and maintainability</td>
                                    <td>Will it operate dependably and be serviceable?</td>
                                    <td>Provide diagnostics, robust components, documentation, and repair access.</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="result-box"><strong>Engineering trade-off</strong><span>Design constraints interact. Improving performance may increase cost or power consumption. Reducing cost may limit memory or processing capability. The engineer's task is to find a balanced solution that satisfies the most important requirements of the application.</span></div>
                </div>
            </section>
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
                        })({});
                    </script>
                </div>
            </div>
            <section class="theory-card" id="summary">
                <div class="theory-header">
                    <div>
                        <h4>1.6 Chapter Summary</h4>
                    </div>
                    <div class="theory-badge"><i class="bi bi-cpu"></i> LECTURE RECAP</div>
                </div>
                <div class="theory-body">
                    <ul class="numbered-list">
                        <li>Embedded systems combine hardware and software to perform dedicated functions within larger products or processes.</li>
                        <li>Early forms of dedicated control included mechanical mechanisms, electromechanical relays, analog circuits, and specialized early computers.</li>
                        <li>Microprocessors and microcontrollers made compact programmable embedded computing practical.</li>
                        <li>Contemporary embedded products may integrate several processors, specialized circuits, communication functions, memory, storage, and user interfaces.</li>
                        <li>The structure of an embedded system includes hardware components such as the CPU, memory, and I/O subsystem, together with firmware and other software components.</li>
                        <li>Embedded systems may be classified broadly as small, distributed, or high-performance systems.</li>
                        <li>The design life cycle includes birth, design, growth, maturity, and decline.</li>
                        <li>Major design constraints include functionality, cost, performance, power and energy, time-to-market, reliability, and maintainability.</li>
                    </ul>
                </div>
            </section>
            <section class="theory-card" id="lecture-notes">
                <div class="theory-header">
                    <div>
                        <h4>Lecture Notes and Key Terminology</h4>
                    </div>
                    <div class="theory-badge"><i class="bi bi-cpu"></i> STUDY GUIDE</div>
                </div>
                <div class="theory-body">
                    <div class="definition-list">
                        <div><strong>Embedded system</strong><span>A computer-based system integrated into a larger product to perform a dedicated function.</span></div>
                        <div><strong>Microprocessor</strong><span>An integrated circuit containing a CPU; memory and peripherals are commonly provided externally.</span></div>
                        <div><strong>Microcontroller</strong><span>An integrated circuit that typically combines a CPU, memory, and peripherals for embedded control.</span></div>
                        <div><strong>Firmware</strong><span>Software stored in a device and used to control its operation.</span></div>
                        <div><strong>Peripheral</strong><span>A hardware module that supports functions such as timing, communication, analog conversion, or digital input/output.</span></div>
                        <div><strong>Distributed embedded system</strong><span>A system in which multiple embedded nodes or controllers cooperate through communication links.</span></div>
                        <div><strong>Real-time response</strong><span>A response whose correctness depends not only on the result but also on when the result is produced.</span></div>
                        <div><strong>Design constraint</strong><span>A limit or requirement that influences the architecture and implementation of a product.</span></div>
                    </div>
                    <p>When reviewing the chapter, be prepared to explain concepts in your own words, compare related technologies, and connect each design consideration to a real embedded application. The purpose of the lecture is to develop engineering understanding, not merely to memorize terminology.</p>
                </div>
            </section>
            <section class="theory-card" id="exercises">
                <div class="theory-header">
                    <div>
                        <h4>1.7 Problems and Written Exercises</h4>
                    </div>
                    <div class="theory-badge"><i class="bi bi-cpu"></i> APPLY YOUR KNOWLEDGE</div>
                </div>
                <div class="theory-body">
                    <p>Answer the following problems in your notebook or submitted document. Use complete explanations and labeled diagrams where appropriate. These are lecture activities; no physical hardware or browser simulation is required.</p>
                    <div class="definition-list">
                        <div><strong>Problem 1 – Identify embedded systems</strong><span>Identify five products in your home, school, or community that contain embedded systems. For each, state its dedicated function and explain why it is not simply a general-purpose computer.</span></div>
                        <div><strong>Problem 2 – Historical development</strong><span>Explain how embedded control evolved from mechanical and electromechanical systems to microcontroller-based systems. Identify at least three major technological changes.</span></div>
                        <div><strong>Problem 3 – Microprocessor versus microcontroller</strong><span>Compare a microprocessor and a microcontroller in terms of integration, memory, peripherals, typical applications, and system cost. Include one example of each.</span></div>
                        <div><strong>Problem 4 – Hardware and software structure</strong><span>Draw a block diagram of an automatic room-temperature controller. Label its sensor, processor, memory, input/output interface, actuator, and firmware responsibilities.</span></div>
                        <div><strong>Problem 5 – Classification</strong><span>Classify a digital thermometer, an automobile electronic control network, and an industrial machine-vision controller as small, distributed, or high-performance embedded systems. Justify each classification.</span></div>
                        <div><strong>Problem 6 – Design life cycle</strong><span>Choose an embedded product and describe what happens during its birth, design, growth, maturity, and decline stages.</span></div>
                        <div><strong>Problem 7 – Design constraints</strong><span>Propose an embedded system for monitoring water level in a community water tank. Discuss its functionality, cost, performance, power and energy, time-to-market, reliability, and maintainability requirements.</span></div>
                        <div><strong>Problem 8 – Engineering trade-offs</strong><span>A battery-powered sensor node must transmit measurements frequently but also operate for several months. Discuss at least three design choices that could balance response time, communication needs, and energy consumption.</span></div>
                    </div>
                </div>
            </section>
            <section class="theory-card" id="review-questions">
                <div class="theory-header">
                    <div>
                        <h4>Review Questions and Answers</h4>
                    </div>
                    <div class="theory-badge"><i class="bi bi-cpu"></i> SELF-ASSESSMENT</div>
                </div>
                <div class="theory-body">
                    <p>Try to answer each question before selecting <strong>View answer</strong>.</p>
                    <div class="review-question-list">
                        <article class="review-question">
                            <div class="review-question-heading"><button type="button" class="answer-toggle" aria-expanded="false">View answer</button><strong>1. What distinguishes an embedded system from a general-purpose computer?</strong></div>
                            <div class="review-answer" hidden>An embedded system is integrated into a larger product and is designed around a dedicated function or a defined set of related functions. A general-purpose computer is designed to run many different applications selected by its user.</div>
                        </article>
                        <article class="review-question">
                            <div class="review-question-heading"><button type="button" class="answer-toggle" aria-expanded="false">View answer</button><strong>2. What were early forms of embedded control?</strong></div>
                            <div class="review-answer" hidden>Early forms included mechanical mechanisms, electromechanical devices such as relays and switches, analog electronic circuits, and specialized early computers.</div>
                        </article>
                        <article class="review-question">
                            <div class="review-question-heading"><button type="button" class="answer-toggle" aria-expanded="false">View answer</button><strong>3. How does a microcontroller differ from a microprocessor?</strong></div>
                            <div class="review-answer" hidden>A microprocessor primarily integrates the CPU and commonly requires external memory and peripheral devices. A microcontroller typically integrates the CPU, memory, and peripheral interfaces into one chip for embedded applications.</div>
                        </article>
                        <article class="review-question">
                            <div class="review-question-heading"><button type="button" class="answer-toggle" aria-expanded="false">View answer</button><strong>4. What are the two major groups of components in an embedded system?</strong></div>
                            <div class="review-answer" hidden>Hardware components provide the electronic infrastructure, while software components—such as firmware, drivers, system software, and application tasks—define the system's functionality.</div>
                        </article>
                        <article class="review-question">
                            <div class="review-question-heading"><button type="button" class="answer-toggle" aria-expanded="false">View answer</button><strong>5. What are the three broad embedded-system categories discussed in this chapter?</strong></div>
                            <div class="review-answer" hidden>Small embedded systems, distributed embedded systems, and high-performance embedded systems.</div>
                        </article>
                        <article class="review-question">
                            <div class="review-question-heading"><button type="button" class="answer-toggle" aria-expanded="false">View answer</button><strong>6. What are the five stages in the embedded design life cycle?</strong></div>
                            <div class="review-answer" hidden>Birth, design, growth, maturity, and decline.</div>
                        </article>
                        <article class="review-question">
                            <div class="review-question-heading"><button type="button" class="answer-toggle" aria-expanded="false">View answer</button><strong>7. Why are power and energy important design constraints?</strong></div>
                            <div class="review-answer" hidden>They determine how much electrical power the system requires and how much energy it consumes over time. These factors affect battery life, heat, efficiency, and the suitability of the product for its operating environment.</div>
                        </article>
                        <article class="review-question">
                            <div class="review-question-heading"><button type="button" class="answer-toggle" aria-expanded="false">View answer</button><strong>8. Differentiate reliability from maintainability.</strong></div>
                            <div class="review-answer" hidden>Reliability is the ability to operate correctly under stated conditions for a specified period. Maintainability is how easily the system can be inspected, repaired, updated, or restored after a problem.</div>
                        </article>
                    </div>
                </div>
            </section>
            <section class="theory-card" id="references">
                <div class="theory-header">
                    <div>
                        <h4>References and Further Reading</h4>
                    </div>
                    <div class="theory-badge"><i class="bi bi-cpu"></i> ACADEMIC SOURCES</div>
                </div>
                <div class="theory-body">
                    <ol class="reference-list">
                        <li><strong>Jiménez, M., Palomera, R., &amp; Couvertier, I. (2014).</strong> <em>Introduction to Embedded Systems: Using Microcontrollers and the MSP430.</em> Springer. Chapter 1: Introduction. <a href="https://doi.org/10.1007/978-1-4614-3143-5" target="_blank" rel="noopener">https://doi.org/10.1007/978-1-4614-3143-5</a></li>
                        <li><strong>Springer Nature.</strong> <em>Introduction to Embedded Systems: Using Microcontrollers and the MSP430 – Book information and table of contents.</em> <a href="https://link.springer.com/book/10.1007/978-1-4614-3143-5" target="_blank" rel="noopener">Official publisher page</a></li>
                        <li><strong>University of Puerto Rico at Mayagüez, College of Engineering.</strong> <em>Introduction to Embedded Systems – About the book and authors.</em> <a href="https://www.uprm.edu/engineering/embedded/embedded-about/" target="_blank" rel="noopener">Book information</a></li>
                        <li><strong>Arduino.</strong> <em>Arduino Documentation.</em> Reference material for the Arduino Uno platform used in the course's practical activities. <a href="https://docs.arduino.cc/" target="_blank" rel="noopener">https://docs.arduino.cc/</a></li>
                        <li><strong>Computer History Museum.</strong> <em>1974: General-Purpose Microcontroller Family is Announced.</em> Historical account of Gary Boone and Michael Cochran's work leading to the Texas Instruments TMS1000 family. <a href="https://www.computerhistory.org/siliconengine/general-purpose-microcontroller-family-is-announced/" target="_blank" rel="noopener">Computer History Museum</a></li>
                        <li><strong>Intel Corporation.</strong> <em>Announcing a New Era of Integrated Electronics: The Intel 4004.</em> Historical account of the 4004 project and Federico Faggin's design leadership, alongside Ted Hoff, Stan Mazor, and Masatoshi Shima. <a href="https://www.intel.com/content/www/us/en/history/virtual-vault/articles/the-intel-4004.html" target="_blank" rel="noopener">Intel History</a></li>
                    </ol>
                    <p><strong>Platform note:</strong> Jiménez, Palomera, and Couvertier use the Texas Instruments MSP430 as the practical microcontroller platform in their book. In this course, Arduino Uno may be used for laboratory demonstrations. The Arduino examples are platform adaptations for instruction; they do not change the theoretical organization of the textbook chapter.</p>
                    <p><em>Academic note:</em> This lecture is an original instructional synthesis aligned with the topics and conceptual organization of Chapter 1. It does not reproduce the textbook's prose or figures.</p>
                </div>
            </section>
            <footer class="async-lab-footer">
                <p><strong>Embedded Systems: History, Structure, and Design</strong></p>
                <p>Engineering and Technological Solutions</p><small>&copy; <span id="labCopyrightYear"></span> All rights reserved.</small>
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




        /* Full-width historical technology rows */
        .technology-timeline {
            display: flex;
            flex-direction: column;
            width: 100%;
            gap: 18px;
            margin: 18px 0 24px;
        }

        .technology-row {
            display: grid;
            grid-template-columns: minmax(220px, 34%) minmax(0, 1fr);
            align-items: stretch;
            width: 100%;
            min-width: 0;
            gap: 24px;
            margin: 0;
            padding: 18px;
            box-sizing: border-box;
            border: 1px solid var(--theory-border);
            border-radius: 14px;
            background: var(--theory-bg);
            color: var(--theory-text);
        }

        .technology-image {
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            min-width: 0;
            margin: 0;
            padding: 12px;
            border-radius: 10px;
            background: var(--theory-header);
            overflow: hidden;
        }

        .technology-image img {
            display: block;
            width: 100%;
            height: 220px;
            max-height: 250px;
            object-fit: contain;
            border-radius: 8px;
        }

        .technology-image figcaption {
            width: 100%;
            margin-top: 9px;
            color: var(--theory-muted);
            font-size: .75rem;
            line-height: 1.5;
            text-align: center;
        }

        .technology-description {
            display: flex;
            flex-direction: column;
            justify-content: center;
            min-width: 0;
            padding: 8px 4px;
        }

        .technology-number {
            margin-bottom: 7px;
            color: var(--theory-blue);
            font-size: .7rem;
            font-weight: 800;
            letter-spacing: .09em;
        }

        .technology-description h5 {
            margin: 0 0 9px;
            color: var(--theory-text);
            font-size: 1rem;
            font-weight: 700;
        }

        .technology-description p {
            margin: 0 0 10px;
            color: var(--theory-secondary);
            font-size: .88rem;
            line-height: 1.8;
        }

        .technology-description small {
            color: var(--theory-muted);
            font-size: .72rem;
        }

        @media (max-width: 700px) {
            .technology-row {
                grid-template-columns: minmax(0, 1fr);
                gap: 12px;
                padding: 13px;
            }

            .technology-image img {
                height: 210px;
            }

            .technology-description {
                padding: 7px 3px 3px;
            }
        }

        .pioneer-figure img {
            max-width: 100%;
        }

        .pioneer-figure figcaption {
            font-size: .92rem;
            line-height: 1.55;
        }

        @media (max-width:600px) {
            .pioneer-image-grid {
                grid-template-columns: 1fr !important;
            }

            .pioneer-figure img {
                height: 200px !important;
            }
        }
    </style>
    <script>
        (() => {
            const toggle = document.getElementById('tocFloatingButton');
            const panel = document.getElementById('table-of-contents');
            const minimize = document.getElementById('tocMinimizeButton');
            if (toggle && panel && minimize) {
                const setOpen = open => {
                    panel.classList.toggle('is-open', open);
                    panel.setAttribute('aria-hidden', String(!open));
                    toggle.setAttribute('aria-expanded', String(open));
                };
                toggle.addEventListener('click', () => setOpen(!panel.classList.contains('is-open')));
                minimize.addEventListener('click', () => setOpen(false));
                panel.querySelectorAll('a').forEach(a => a.addEventListener('click', () => setOpen(false)));
                setOpen(false);
            }
            document.querySelectorAll('.answer-toggle').forEach(button => button.addEventListener('click', () => {
                const answer = button.closest('.review-question')?.querySelector('.review-answer');
                if (!answer) return;
                const open = answer.hidden;
                answer.hidden = !open;
                button.textContent = open ? 'Hide answer' : 'View answer';
                button.setAttribute('aria-expanded', String(open));
            }));
            const year = document.getElementById('labCopyrightYear');
            if (year) year.textContent = new Date().getFullYear();
        })();
    </script>
    <?php include 'globals/scripts.php'; ?>
    <?php include 'globals/academic_posts_scripts.php'; ?>
</body>

</html>