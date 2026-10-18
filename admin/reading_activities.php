<?php
include './globals/checks.php';
require_once '../src/connection.php';
if (empty($_SESSION['ra_admin_csrf'])) $_SESSION['ra_admin_csrf'] = bin2hex(random_bytes(32));
$csrf = $_SESSION['ra_admin_csrf'];
function rah($v){ return htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8'); }
function ra_col_exists($cols, $names){ foreach($names as $n) if(isset($cols[$n])) return $n; return null; }
$table = 'reading_activity_table';
$columns = [];
$cr = $mysqli->query("SHOW COLUMNS FROM `$table`");
if (!$cr) { http_response_code(500); exit('Could not read reading_activity_table. Verify the table exists.'); }
while($c=$cr->fetch_assoc()) $columns[$c['Field']]=$c;
$pk = null;
foreach($columns as $name=>$meta) if(strpos($meta['Extra']??'', 'auto_increment')!==false){$pk=$name;break;}
if(!$pk) $pk=ra_col_exists($columns,['id','activity_id','reading_activity_id']);
if(!$pk){http_response_code(500);exit('Could not identify reading_activity_table primary key.');}
$notice=''; $error='';
if($_SERVER['REQUEST_METHOD']==='POST'){
  if(empty($_POST['csrf_token']) || !hash_equals($csrf,(string)$_POST['csrf_token'])) $error='Security token expired. Refresh and try again.';
  else {
    $id=(string)($_POST['activity_id']??''); $action=$_POST['action']??'';
    if(!ctype_digit($id)) $error='Invalid activity ID.';
    else {
      $idn=(int)$id;
      if($action==='toggle'){
        $s=$mysqli->prepare("UPDATE `$table` SET status=IF(status='active','inactive','active') WHERE `$pk`=?");
        if($s){$s->bind_param('i',$idn);$s->execute();$notice='Activity status updated.';$s->close();} else $error='Could not prepare status update.';
      } elseif($action==='delete'){
        $s=$mysqli->prepare("DELETE FROM `$table` WHERE `$pk`=?");
        if($s){$s->bind_param('i',$idn);$s->execute();if($s->affected_rows>0)$notice='Activity deleted.';else $error='Activity was not found or could not be deleted. Check whether progress records reference it.';$s->close();} else $error='Could not prepare delete request.';
      }
    }
  }
}
$q=trim((string)($_GET['q']??''));$statusFilter=(string)($_GET['status']??'');$departmentFilter=trim((string)($_GET['department']??''));
$where=[];$params=[];$types='';
if($q!==''){$where[]='(title LIKE ? OR description LIKE ? OR department LIKE ? OR year_level LIKE ? OR section LIKE ?)';$like='%'.$q.'%';for($i=0;$i<5;$i++){$params[]=$like;$types.='s';}}
if(in_array($statusFilter,['active','inactive'],true)){$where[]='status=?';$params[]=$statusFilter;$types.='s';}
if($departmentFilter!==''){$where[]='department=?';$params[]=$departmentFilter;$types.='s';}
$sql="SELECT `$pk` AS activity_id,title,file_url,file_type,department,year_level,section,description,required_reading_minutes,start_date,due_date,status FROM `$table`";
if($where)$sql.=' WHERE '.implode(' AND ',$where);$sql.=" ORDER BY start_date DESC, `$pk` DESC";
$items=[];$s=$mysqli->prepare($sql);if($s){if($params)$s->bind_param($types,...$params);$s->execute();$rr=$s->get_result();while($r=$rr->fetch_assoc())$items[]=$r;$s->close();}
$departments=[];$dr=$mysqli->query("SELECT DISTINCT department FROM `$table` WHERE department IS NOT NULL AND department<>'' ORDER BY department");if($dr)while($r=$dr->fetch_assoc())$departments[]=$r['department'];
$sr=$mysqli->query("SELECT COUNT(*) total, COALESCE(SUM(status='active'),0) active_count, COALESCE(SUM(status='inactive'),0) inactive_count FROM `$table`");$stats=$sr?$sr->fetch_assoc():['total'=>0,'active_count'=>0,'inactive_count'=>0];
$selectedId=trim((string)($_GET['monitor']??''));$monitorActivity=null;$progressRows=[];$progressIssue='';$progressTable=null;
if($selectedId!==''&&ctype_digit($selectedId)){
  $n=(int)$selectedId;$s=$mysqli->prepare("SELECT `$pk` AS activity_id,title,department,year_level,section,required_reading_minutes FROM `$table` WHERE `$pk`=? LIMIT 1");$s->bind_param('i',$n);$s->execute();$monitorActivity=$s->get_result()->fetch_assoc();$s->close();
  if($monitorActivity){
    // Discover a progress table and common column names. Confirm this mapping against your actual progress schema.
    $tableNames=[];$tr=$mysqli->query('SHOW TABLES');if($tr)while($t=$tr->fetch_row())$tableNames[]=$t[0];
    $preferred=['reading_activity_progress_table','reading_activity_progress','reading_activity_tracking','reading_progress','student_reading_progress','reading_activity_attempts'];
    usort($tableNames,function($a,$b)use($preferred){$ia=array_search($a,$preferred,true);$ib=array_search($b,$preferred,true);$ia=$ia===false?999:$ia;$ib=$ib===false?999:$ib;return $ia<=>$ib;});
    foreach($tableNames as $tn){
      if($tn===$table)continue;
      $safe=$mysqli->real_escape_string($tn);$cc=$mysqli->query("SHOW COLUMNS FROM `$safe`");if(!$cc)continue;$pc=[];while($c=$cc->fetch_assoc())$pc[$c['Field']]=$c;
      $aid=ra_col_exists($pc,['activity_id','reading_activity_id','activityId']);$sid=ra_col_exists($pc,['student_id','studentID','user_id','account_id']);
      if($aid&&$sid){$progressTable=$tn;$pcols=$pc;$activityCol=$aid;$studentCol=$sid;break;}
    }
    if(!$progressTable){$progressIssue='No recognizable student progress table was found. The activity list works, but progress tracking needs the name and columns of your student reading-progress table.';}
    else {
      $minutesCol=ra_col_exists($pcols,['reading_minutes','minutes_read','time_spent_minutes','total_minutes','minutes_spent']);
      $completedCol=ra_col_exists($pcols,['completed','is_completed','completion_status','status']);
      $lastCol=ra_col_exists($pcols,['last_accessed','last_activity_at','updated_at','completed_at','created_at']);
      $progressPk=ra_col_exists($pcols,['id','progress_id','reading_progress_id']);
      $select="p.`$studentCol` AS student_key";
      $select.=$minutesCol?", p.`$minutesCol` AS minutes_value":", NULL AS minutes_value";
      $select.=$completedCol?", p.`$completedCol` AS completion_value":", NULL AS completion_value";
      $select.=$lastCol?", p.`$lastCol` AS last_value":", NULL AS last_value";
      $select.=$progressPk?", p.`$progressPk` AS progress_key":", NULL AS progress_key";
      // Account join is optional; supports student_id first, then id fallback.
      $accountJoin='';$studentName="CAST(p.`$studentCol` AS CHAR)";
      $ac=[];$ar=$mysqli->query('SHOW COLUMNS FROM accounts');if($ar)while($c=$ar->fetch_assoc())$ac[$c['Field']]=$c;
      $accountKey=isset($ac['student_id'])?'student_id':(isset($ac['id'])?'id':null);
      if($accountKey){$accountJoin=" LEFT JOIN accounts a ON CAST(a.`$accountKey` AS CHAR)=CAST(p.`$studentCol` AS CHAR)";if(isset($ac['first_name'])&&isset($ac['last_name']))$studentName="COALESCE(NULLIF(TRIM(CONCAT(a.first_name,' ',a.last_name)),''),CAST(p.`$studentCol` AS CHAR))";elseif(isset($ac['name']))$studentName="COALESCE(a.name,CAST(p.`$studentCol` AS CHAR))";}
      $sqlp="SELECT $select, $studentName AS student_name FROM `$progressTable` p $accountJoin WHERE p.`$activityCol`=? ORDER BY ".($lastCol?"p.`$lastCol` DESC":"p.`$studentCol` ASC");
      $ps=$mysqli->prepare($sqlp);if($ps){$ps->bind_param('i',$n);$ps->execute();$pr=$ps->get_result();while($x=$pr->fetch_assoc())$progressRows[]=$x;$ps->close();}else $progressIssue='Found progress table '.$progressTable.', but its columns could not be queried automatically.';
    }
  }
}
?>
<!doctype html><html lang="en"><?php include 'globals/head.php'; ?>
<style>
.ra-page{max-width:1500px;margin:auto}.ra-header{display:flex;justify-content:space-between;align-items:center;gap:16px;margin-bottom:22px}.ra-header h2{margin:0;font-size:1.6rem;font-weight:750;color:#1f2937}.ra-header p{margin:6px 0 0;color:#6b7280}.ra-btn{display:inline-flex;align-items:center;justify-content:center;gap:6px;padding:8px 12px;border:1px solid #d1d5db;border-radius:8px;background:#fff;color:#374151;text-decoration:none;font-size:.84rem;font-weight:600;cursor:pointer;white-space:nowrap}.ra-primary{background:#4f46e5;border-color:#4f46e5;color:#fff}.ra-danger{color:#dc2626;border-color:#fecaca}.ra-stats{display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:14px;margin-bottom:18px}.ra-stat,.ra-card{background:#fff;border:1px solid #e5e7eb;border-radius:13px;box-shadow:0 2px 8px #0f172a08}.ra-stat{padding:17px}.ra-stat span{font-size:.83rem;color:#6b7280}.ra-stat strong{display:block;font-size:1.55rem;color:#111827;margin-top:5px}.ra-filter{display:flex;gap:9px;flex-wrap:wrap;padding:15px;border-bottom:1px solid #e5e7eb}.ra-filter input,.ra-filter select{min-height:41px;padding:8px 11px;border:1px solid #d1d5db;border-radius:8px;background:#fff}.ra-filter input{flex:1;min-width:200px}.ra-scroll{overflow:auto}.ra-table{width:100%;min-width:940px;border-collapse:collapse}.ra-table th{background:#f9fafb;text-align:left;color:#6b7280;font-size:.72rem;text-transform:uppercase;letter-spacing:.03em;padding:12px}.ra-table td{padding:13px 12px;border-top:1px solid #f0f1f4;color:#374151;font-size:.84rem;vertical-align:middle}.ra-title{font-weight:700;color:#111827}.ra-sub{font-size:.76rem;color:#6b7280;margin-top:4px}.ra-pill{display:inline-block;border-radius:99px;padding:4px 8px;font-size:.73rem;font-weight:700}.ra-active{background:#dcfce7;color:#166534}.ra-inactive{background:#f3f4f6;color:#4b5563}.ra-actions{display:flex;gap:5px;flex-wrap:wrap}.ra-inline{display:inline}.ra-alert{padding:12px 15px;border-radius:9px;margin-bottom:14px}.ra-ok{background:#ecfdf3;color:#166534}.ra-err{background:#fef2f2;color:#b91c1c}.ra-info{background:#eff6ff;color:#1d4ed8}.ra-foot{padding:12px 15px;border-top:1px solid #e5e7eb;color:#6b7280;font-size:.8rem}.ra-monitor{margin-top:25px}.ra-monitor-head{padding:17px 19px;border-bottom:1px solid #e5e7eb;display:flex;justify-content:space-between;gap:12px;align-items:center}.ra-monitor-head h3{margin:0;font-size:1.15rem;font-weight:750}.ra-monitor-body{padding:18px}.ra-progress{height:8px;border-radius:99px;background:#e5e7eb;overflow:hidden;min-width:90px}.ra-progress span{display:block;height:100%;background:#4f46e5}.ra-empty{text-align:center;padding:35px;color:#6b7280}
@media(max-width:768px){.ra-header{align-items:flex-start;flex-direction:column}.ra-header>.ra-btn{width:100%}.ra-stats{grid-template-columns:1fr}.ra-filter{flex-direction:column}.ra-filter>*{width:100%}.ra-monitor-head{align-items:flex-start;flex-direction:column}}
</style><body>
<?php include 'globals/sidebar.php'; ?><?php include 'globals/topbar.php'; ?>
<main class="main-content"><div class="content-wrapper ra-page">
<div class="ra-header"><div><h2>Reading Activities & Student Progress</h2><p>Manage reading assignments and review recorded student activity.</p></div><a class="ra-btn ra-primary" href="reading_activity_create.php"><i class="bi bi-plus-lg"></i> Create Reading Activity</a></div>
<?php if($notice):?><div class="ra-alert ra-ok"><?=rah($notice)?></div><?php endif;?><?php if($error):?><div class="ra-alert ra-err"><?=rah($error)?></div><?php endif;?>
<div class="ra-stats"><div class="ra-stat"><span>Total activities</span><strong><?=number_format((int)($stats['total']??0))?></strong></div><div class="ra-stat"><span>Active activities</span><strong><?=number_format((int)($stats['active_count']??0))?></strong></div><div class="ra-stat"><span>Inactive activities</span><strong><?=number_format((int)($stats['inactive_count']??0))?></strong></div></div>
<section class="ra-card"><form class="ra-filter" method="GET"><input type="search" name="q" value="<?=rah($q)?>" placeholder="Search title, description, department, year, section"><select name="department"><option value="">All departments</option><?php foreach($departments as $d):?><option value="<?=rah($d)?>" <?=$departmentFilter===$d?'selected':''?>><?=rah($d)?></option><?php endforeach;?></select><select name="status"><option value="">All statuses</option><option value="active" <?=$statusFilter==='active'?'selected':''?>>Active</option><option value="inactive" <?=$statusFilter==='inactive'?'selected':''?>>Inactive</option></select><button class="ra-btn ra-primary" type="submit">Search</button><a class="ra-btn" href="reading_activities.php">Reset</a></form>
<div class="ra-scroll"><table class="ra-table"><thead><tr><th>Activity</th><th>Assigned group</th><th>Schedule</th><th>Required time</th><th>Status</th><th>Actions</th></tr></thead><tbody>
<?php if(!$items):?><tr><td colspan="6"><div class="ra-empty">No reading activities found.</div></td></tr><?php else:foreach($items as $a):?><tr><td><div class="ra-title"><?=rah($a['title'])?></div><div class="ra-sub"><?=rah(strtoupper($a['file_type']??'file'))?> · <a href="<?=rah($a['file_url'])?>" target="_blank" rel="noopener">Open material</a></div></td><td><?=rah($a['department'])?><div class="ra-sub">Year <?=rah($a['year_level'])?> · <?=rah(($a['section']??'')===''?'All sections':$a['section'])?></div></td><td>From <?=rah($a['start_date'])?><div class="ra-sub">Due <?=rah($a['due_date'])?></div></td><td><?=number_format((int)$a['required_reading_minutes'])?> min</td><td><span class="ra-pill <?=$a['status']==='active'?'ra-active':'ra-inactive'?>"><?=rah(ucfirst($a['status']))?></span></td><td><div class="ra-actions"><a class="ra-btn" href="reading_activity_edit.php?id=<?=rah($a['activity_id'])?>"><i class="bi bi-pencil-square"></i> Edit</a><a class="ra-btn" href="?<?=http_build_query(array_merge($_GET,['monitor'=>$a['activity_id']]))?>#student-progress">Monitor</a><form class="ra-inline" method="POST" onsubmit="return confirm('Toggle this activity status?')"><input type="hidden" name="csrf_token" value="<?=rah($csrf)?>"><input type="hidden" name="activity_id" value="<?=rah($a['activity_id'])?>"><input type="hidden" name="action" value="toggle"><button class="ra-btn" type="submit"><?=$a['status']==='active'?'Deactivate':'Activate'?></button></form><form class="ra-inline" method="POST" onsubmit="return confirm('Permanently delete this activity? Check student progress records first.')"><input type="hidden" name="csrf_token" value="<?=rah($csrf)?>"><input type="hidden" name="activity_id" value="<?=rah($a['activity_id'])?>"><input type="hidden" name="action" value="delete"><button class="ra-btn ra-danger" type="submit" title="Delete"><i class="bi bi-trash"></i></button></form></div></td></tr><?php endforeach;endif;?>
</tbody></table></div><div class="ra-foot">Showing <?=count($items)?> matching activities.</div></section>
<?php if($selectedId!==''):?><section id="student-progress" class="ra-card ra-monitor"><div class="ra-monitor-head"><div><h3>Student Progress Monitor</h3><div class="ra-sub"><?=rah($monitorActivity['title']??'Selected activity not found')?></div></div><a class="ra-btn" href="reading_activities.php#student-progress">Close monitor</a></div><div class="ra-monitor-body">
<?php if(!$monitorActivity):?><div class="ra-alert ra-err">The selected activity could not be found.</div><?php elseif($progressIssue):?><div class="ra-alert ra-info"><?=rah($progressIssue)?></div><p class="ra-sub">For an exact integration, connect this page to the table that stores students' reading minutes and completion status. This page does not invent progress values.</p><?php elseif(!$progressRows):?><div class="ra-empty">No student progress records were found for this activity in <strong><?=rah($progressTable)?></strong>.</div><?php else:?><p class="ra-sub">Progress source table: <strong><?=rah($progressTable)?></strong>. Confirm its column mapping before relying on these figures.</p><div class="ra-scroll"><table class="ra-table" style="min-width:650px"><thead><tr><th>Student</th><th>Reading time</th><th>Completion</th><th>Last recorded activity</th></tr></thead><tbody><?php foreach($progressRows as $p):$mins=is_numeric($p['minutes_value'])?(int)$p['minutes_value']:null;$done=$p['completion_value'];$isDone=($done===1||$done==='1'||$done===true||in_array(strtolower((string)$done),['completed','complete','done','yes'],true));$percent=$mins===null?null:min(100,(int)round($mins/max(1,(int)$monitorActivity['required_reading_minutes'])*100));?><tr><td><strong><?=rah($p['student_name'])?></strong><div class="ra-sub">Student ID: <?=rah($p['student_key'])?></div></td><td><?php if($mins!==null):?><strong><?=$mins?> min</strong><div class="ra-progress"><span style="width:<?=$percent?>%"></span></div><div class="ra-sub"><?=$percent?>% of required time</div><?php else:?>Not recorded<?php endif;?></td><td><span class="ra-pill <?=$isDone?'ra-active':'ra-inactive'?>"><?=$isDone?'Completed':($done===null?'Unknown':rah(ucfirst((string)$done)))?></span></td><td><?=rah($p['last_value']??'—')?></td></tr><?php endforeach;?></tbody></table></div><?php endif;?></div></section><?php endif;?>
</div></main><?php include 'globals/scripts.php'; ?></body></html>
