<?php
require_once '../src/connection.php'; $db=$mysqli??null; require_once __DIR__.'/exam_common.php';
$error=''; $notice=''; $exam=null; $attempt=null; $questions=[]; $view='code';
function ex_grade_attempt(mysqli $db, int $attemptId, array $answers, bool $timedOut=false): array {
    $ar=ex_rows($db,'SELECT * FROM exam_attempts WHERE id=?','i',[$attemptId]); if(!$ar) throw new Exception('Exam attempt not found.'); $a=$ar[0];
    if($a['status']!=='in_progress') return ['score'=>(int)$a['score'],'total'=>(int)$a['total_items'],'status'=>$a['status']];
    $set=ex_rows($db,'SELECT question_id FROM exam_set_questions WHERE exam_id=? AND variant=? ORDER BY position_no','is',[(int)$a['exam_id'],$a['variant']]); $score=0;
    $db->begin_transaction();
    try {
        foreach($set as $item){$qid=(int)$item['question_id'];$picked=isset($answers[$qid])?trim((string)$answers[$qid]):'';
            $valid=''; if($picked!==''){$validRows=ex_rows($db,'SELECT choice_label FROM question_choices WHERE question_id=? AND choice_label=?','is',[$qid,$picked]);if(!$validRows)$picked='';}
            $correctRows=ex_rows($db,'SELECT choice_label FROM question_choices WHERE question_id=? AND is_correct=1 LIMIT 1','i',[$qid]);$correct=(string)($correctRows[0]['choice_label']??'');$isCorrect=($picked!=='' && $picked===$correct);if($isCorrect)$score++;
            ex_exec($db,'INSERT INTO exam_attempt_answers(attempt_id,question_id,selected_choice,correct_choice,is_correct) VALUES(?,?,?,?,?) ON DUPLICATE KEY UPDATE selected_choice=VALUES(selected_choice),correct_choice=VALUES(correct_choice),is_correct=VALUES(is_correct)','iissi',[$attemptId,$qid,($picked!==''?$picked:null),($correct!==''?$correct:null),($isCorrect?1:0)]);
        }
        $status=$timedOut?'timed_out':'submitted';ex_exec($db,'UPDATE exam_attempts SET status=?,score=?,submitted_at=NOW(),last_seen_at=NOW() WHERE id=? AND status=\'in_progress\'','sii',[$status,$score,$attemptId]);$db->commit();
        return ['score'=>$score,'total'=>count($set),'status'=>$status];
    } catch(Throwable $e){$db->rollback();throw $e;}
}
try {
    if($_SERVER['REQUEST_METHOD']==='POST') {
        if(!hash_equals($examCsrf,(string)($_POST['csrf']??''))) throw new Exception('Session expired. Refresh the page and try again.');
        $action=(string)($_POST['action']??'');
        if($action==='enter_code') {
            $code=strtoupper(trim((string)($_POST['test_code']??'')));
            $found=ex_rows($db,"SELECT * FROM exams WHERE test_code=? AND status='published'",'s',[$code]);
            if(!$found) throw new Exception('That test code is invalid, or the exam is not currently accepting students.');
            $_SESSION['exam_code_exam_id']=(int)$found[0]['id']; $_SESSION['exam_code']=$code; $exam=$found[0]; $view='instructions';
        } elseif($action==='start_exam') {
            $examId=(int)($_POST['exam_id']??0);$name=trim((string)($_POST['student_name']??''));$number=trim((string)($_POST['student_number']??''));
            if($name==='') throw new Exception('Enter your full name before starting.');
            if((int)($_SESSION['exam_code_exam_id']??0)!==$examId) throw new Exception('Please enter the test code again.');
            $found=ex_rows($db,"SELECT * FROM exams WHERE id=? AND status='published'",'i',[$examId]);if(!$found)throw new Exception('This exam is no longer available.');$exam=$found[0];
            $setRows=ex_rows($db,'SELECT variant,COUNT(*) AS total FROM exam_set_questions WHERE exam_id=? GROUP BY variant','i',[$examId]);$variants=[];foreach($setRows as $sr)if((int)$sr['total']===(int)$exam['question_count'])$variants[]=$sr['variant'];if(!$variants)throw new Exception('This exam has not been finalized correctly. Contact the administrator.');
            $variant=$variants[random_int(0,count($variants)-1)];$now=new DateTimeImmutable('now');$expiry=$now->modify('+'.(int)$exam['time_limit_minutes'].' minutes');
            ex_exec($db,'INSERT INTO exam_attempts(exam_id,student_name,student_number,variant,started_at,expires_at,last_seen_at,status,total_items) VALUES(?,?,?,?,?,?,NOW(),\'in_progress\',?)','isssssi',[$examId,$name,($number!==''?$number:null),$variant,$now->format('Y-m-d H:i:s'),$expiry->format('Y-m-d H:i:s'),(int)$exam['question_count']]);
            $attemptId=(int)$db->insert_id;$_SESSION['exam_attempt_ids'][$attemptId]=true;header('Location: exam_take.php?attempt='.$attemptId);exit;
        } elseif($action==='submit_exam') {
            $attemptId=(int)($_POST['attempt_id']??0);if(empty($_SESSION['exam_attempt_ids'][$attemptId]))throw new Exception('This attempt is not available in your current session.');
            $ar=ex_rows($db,'SELECT * FROM exam_attempts WHERE id=?','i',[$attemptId]);if(!$ar)throw new Exception('Attempt not found.');$a=$ar[0];
            if($a['status']==='in_progress' && strtotime($a['expires_at'])<time()){$result=ex_grade_attempt($db,$attemptId,is_array($_POST['answers']??null)?$_POST['answers']:[],true);}
            else {$result=ex_grade_attempt($db,$attemptId,is_array($_POST['answers']??null)?$_POST['answers']:[],false);}
            header('Location: exam_take.php?attempt='.$attemptId.'&submitted=1');exit;
        }
    }
    if(isset($_GET['attempt'])) {
        $attemptId=(int)$_GET['attempt'];if(empty($_SESSION['exam_attempt_ids'][$attemptId]))throw new Exception('This exam attempt is not available in this browser session.');
        $ar=ex_rows($db,'SELECT a.*,e.title,e.instructions,e.time_limit_minutes,e.test_code,e.status AS exam_status FROM exam_attempts a JOIN exams e ON e.id=a.exam_id WHERE a.id=?','i',[$attemptId]);if(!$ar)throw new Exception('Attempt not found.');$attempt=$ar[0];$exam=$attempt;$view='attempt';
        if($attempt['status']==='in_progress' && strtotime($attempt['expires_at'])<=time()){$result=ex_grade_attempt($db,$attemptId,[],true);$ar=ex_rows($db,'SELECT a.*,e.title,e.instructions,e.time_limit_minutes,e.test_code,e.status AS exam_status FROM exam_attempts a JOIN exams e ON e.id=a.exam_id WHERE a.id=?','i',[$attemptId]);$attempt=$ar[0];$exam=$attempt;}
        if($attempt['status']==='in_progress') {
            ex_exec($db,'UPDATE exam_attempts SET last_seen_at=NOW() WHERE id=?','i',[$attemptId]);
            $questions=ex_rows($db,'SELECT s.position_no,s.question_id,s.choice_order_json,q.question_text FROM exam_set_questions s JOIN question_bank q ON q.id=s.question_id WHERE s.exam_id=? AND s.variant=? ORDER BY s.position_no','is',[(int)$attempt['exam_id'],$attempt['variant']]);
        }
    } elseif(!$exam && (int)($_SESSION['exam_code_exam_id']??0)>0) {
        $found=ex_rows($db,"SELECT * FROM exams WHERE id=? AND status='published'",'i',[(int)$_SESSION['exam_code_exam_id']]);if($found){$exam=$found[0];$view='instructions';}
    }
} catch(Throwable $e) {$error=$e->getMessage();}
if(isset($_GET['submitted']))$notice='Your exam has been submitted.';
ex_page_start('Online Examination');
?>
<div class="text-center mb-4"><div class="rounded-circle bg-primary-subtle text-primary d-inline-flex align-items-center justify-content-center mb-3" style="width:58px;height:58px;font-size:1.5rem"><i class="bi bi-shield-check"></i></div><h2 class="fw-bold">Online Examination</h2><p class="text-secondary">Enter the test code provided by your instructor.</p></div>
<?php if($error):?><div class="alert alert-danger"><?=ex_e($error)?></div><?php endif;?><?php if($notice):?><div class="alert alert-success"><?=ex_e($notice)?></div><?php endif;?>
<?php if($view==='code' && !$attempt):?>
<section class="exam-card"><div class="exam-card-head"><h5 class="fw-bold mb-0">Access your exam</h5></div><div class="exam-card-body"><form method="post"><input type="hidden" name="csrf" value="<?=ex_e($examCsrf)?>"><input type="hidden" name="action" value="enter_code"><label class="form-label">Test code</label><input class="form-control form-control-lg text-uppercase" name="test_code" required maxlength="20" placeholder="Enter test code" autocomplete="off"><button class="btn btn-primary btn-lg w-100 mt-3">Continue <i class="bi bi-arrow-right ms-1"></i></button></form></div></section>
<?php elseif($view==='instructions' && $exam):?>
<section class="exam-card"><div class="exam-card-head"><span class="badge text-bg-success mb-2">EXAM AVAILABLE</span><h3 class="fw-bold mb-1"><?=ex_e($exam['title'])?></h3><div class="text-secondary"><?=(int)$exam['question_count']?> questions · <?=(int)$exam['time_limit_minutes']?> minutes · Code <?=ex_e($exam['test_code'])?></div></div><div class="exam-card-body"><h5 class="fw-bold">Instructions</h5><div class="text-rich mb-4"><?=ex_rich((string)$exam['instructions'])?></div><div class="alert alert-warning"><strong>Please note:</strong> The timer starts when you begin. Your answers are submitted when time expires. Do not refresh or close the browser while taking the exam.</div><form method="post"><input type="hidden" name="csrf" value="<?=ex_e($examCsrf)?>"><input type="hidden" name="action" value="start_exam"><input type="hidden" name="exam_id" value="<?=(int)$exam['id']?>"><div class="mb-3"><label class="form-label">Full name</label><input class="form-control" name="student_name" maxlength="180" required autocomplete="name"></div><div class="mb-3"><label class="form-label">Student number (optional)</label><input class="form-control" name="student_number" maxlength="80"></div><button class="btn btn-primary btn-lg w-100" onclick="return confirm('Start the exam now? The timer will begin immediately.');">Start examination <i class="bi bi-arrow-right-circle ms-1"></i></button></form></div></section>
<?php elseif($view==='attempt' && $attempt):?>
<?php if($attempt['status']==='in_progress'):?><div class="d-flex justify-content-between align-items-center gap-3 flex-wrap mb-3"><div><h3 class="fw-bold mb-1"><?=ex_e($attempt['title'])?></h3><div class="text-secondary small"><?=ex_e($attempt['student_name'])?> · Set <?=ex_e($attempt['variant'])?></div></div><div class="timer-pill"><i class="bi bi-clock me-2"></i><span id="timer">--:--</span></div></div>
<form method="post" id="examForm"><input type="hidden" name="csrf" value="<?=ex_e($examCsrf)?>"><input type="hidden" name="action" value="submit_exam"><input type="hidden" name="attempt_id" value="<?=(int)$attempt['id']?>"><div class="alert alert-info">Select one answer for each question. You may leave questions unanswered.</div>
<?php foreach($questions as $index=>$q):?><section class="question-card"><div class="small text-secondary mb-2">QUESTION <?=$index+1?> OF <?=count($questions)?></div><div class="text-rich fw-semibold mb-2"><?=ex_rich((string)$q['question_text'])?></div><?php ex_render_media($db,(int)$q['question_id']);$allChoices=ex_rows($db,'SELECT choice_label,choice_text FROM question_choices WHERE question_id=?','i',[(int)$q['question_id']]);$map=[];foreach($allChoices as $c)$map[$c['choice_label']]=$c['choice_text'];$order=json_decode((string)$q['choice_order_json'],true);if(!is_array($order))$order=array_keys($map);$display='A';foreach($order as $original):if(!array_key_exists($original,$map))continue;?><label class="choice-row"><input class="form-check-input mt-1" type="radio" name="answers[<?=(int)$q['question_id']?>]" value="<?=ex_e($original)?>"><strong><?=ex_e($display)?>.</strong><span class="text-rich"><?=ex_rich((string)$map[$original])?></span></label><?php $display++;endforeach;?></section><?php endforeach;?>
<button class="btn btn-success btn-lg w-100 mb-5" onclick="return confirm('Submit your exam now? You cannot change answers after submitting.');"><i class="bi bi-send me-1"></i>Submit exam</button></form>
<script>let seconds=Math.max(0,Math.floor((new Date('<?=ex_e(str_replace(' ','T',$attempt['expires_at']))?>').getTime()-Date.now())/1000));const timer=document.getElementById('timer');const form=document.getElementById('examForm');function tick(){let m=Math.floor(seconds/60),s=seconds%60;timer.textContent=String(m).padStart(2,'0')+':'+String(s).padStart(2,'0');if(seconds<=60)timer.style.background='#b91c1c';if(seconds<=0){clearInterval(clock);alert('Time is up. Your answers will be submitted.');form.submit();return;}seconds--;}tick();const clock=setInterval(tick,1000);</script>
<?php else:?><section class="exam-card"><div class="exam-card-body text-center py-5"><i class="bi bi-check-circle-fill text-success" style="font-size:3rem"></i><h2 class="fw-bold mt-3">Exam submitted</h2><p class="text-secondary">Thank you, <?=ex_e($attempt['student_name'])?>.</p><div class="display-5 fw-bold"><?=(int)$attempt['score']?> / <?=(int)$attempt['total_items']?></div><p class="text-secondary">Your score has been recorded. Status: <?=ex_e(str_replace('_',' ',$attempt['status']))?>.</p><a href="exam_take.php" class="btn btn-outline-primary">Return to test-code page</a></div></section><?php endif;?>
<?php endif;?>
<?php ex_page_end(); ?>
