ETS-Async Exam Manager - installation

FILES
- exam_system.sql       Creates exam metadata, draft questions, A/B/C sets, attempts, and answers tables.
- exam_common.php       Shared helper functions and responsive page styling.
- exam_manager.php      Admin creation/editing, question replacement, preview/finalize, and publish/close controls.
- exam_preview.php      Admin-only preview of a draft or published exam.
- exam_take.php         Student code entry, instructions, timed attempt, server-side grading, and submission.
- exam_monitor.php      Admin attempt monitoring with refresh and CSV export.

INSTALL
1. BACK UP the database first.
2. Copy all PHP files into the same admin/student web directory where question_management.php and question_practice.php currently live. The code uses the existing ../src/connection.php and the globals/checks.php admin guard.
3. Import exam_system.sql in phpMyAdmin into the SAME database used by question_bank, question_choices, subjects, subject_topics, and question_media.
4. Open exam_manager.php as an administrator.
5. Create a draft, choose the subject/topic filters, set a question count (minimum 2), and time limit.
6. Review the randomly selected questions. Replace individual unwanted questions using the dropdowns. Use Preview exam to see the layout.
7. Click Finalize & Generate A/B/C. This locks the question list and stores three variants with shuffled question and answer-choice order. The exam becomes accessible by test code.
8. Give students the URL to exam_take.php and the displayed test code. Monitor their attempts in exam_monitor.php.

IMPORTANT NOTES
- A/B/C contain the same questions in different orders, with choices shuffled separately. This reduces copying but is not a complete anti-cheating solution.
- Finalizing locks the question set. To change questions after finalization, create a new draft exam.
- The timer is checked server-side at submission. The browser auto-submits when the timer reaches zero; students should not refresh/close the page during an exam.
- Monitoring refreshes every 15 seconds and shows last-seen activity, status, remaining time, set, and score. It does not provide screen surveillance or guarantee that a disconnected browser is actively taking the test.
- This initial version expects question_bank.status, subjects.status, and subject_topics.status to use the value 'active', and question_choices to have question_id, choice_label, choice_text, is_correct.
- Use HTTPS on a networked deployment. Test locally before giving students access. If you use a separate student/admin directory, adjust the connection and admin-guard include paths.

UPDATED UI / FORMATTING NOTES
- Admin pages exam_manager.php, exam_preview.php, and exam_monitor.php use your existing globals/head.php, globals/sidebar.php, globals/topbar.php, and globals/scripts.php layout.
- Exam topics are selected with checkboxes. Leave them all unchecked to use all active topics for the chosen subject (or all subjects if no subject is selected).
- The manager supports rich formatted instructions and a LaTeX insertion button. Questions and choices from the question bank support existing rich HTML and Quill formula embeds. KaTeX is used for display in preview and student pages.
- If this is an existing installation, exam_common.php adds the topic_ids_json column automatically when possible; the updated SQL also includes it for new installs. Back up the database first.
