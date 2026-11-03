<?php
require __DIR__.'/../config/app.php';$me=require_role('student');
if($_SERVER['REQUEST_METHOD']!=='POST')go('student/dashboard.php');
csrf_check();$type=$_POST['type']??'';
try{
    if($type==='read'){
        db()->prepare('UPDATE notifications SET is_read=1 WHERE id=? AND user_id=?')->execute([(int)($_POST['id']??0),$me['id']]);flash('Notification marked as read.');
    }elseif($type==='profile'){
        $name=trim((string)($_POST['name']??''));db()->prepare('UPDATE users SET name=? WHERE id=?')->execute([$name,$me['id']]);$_SESSION['user']['name']=$name;flash('Profile updated.');
    }elseif($type==='password'){
        $q=db()->prepare('SELECT password_hash FROM users WHERE id=?');$q->execute([$me['id']]);if(!password_verify($_POST['current_password']??'',$q->fetchColumn()))throw new RuntimeException('Current password is incorrect.');db()->prepare('UPDATE users SET password_hash=? WHERE id=?')->execute([password_hash($_POST['new_password'],PASSWORD_DEFAULT),$me['id']]);flash('Password changed.');
    }elseif($type==='volunteer_apply'){
        $opportunityId=(int)($_POST['opportunity_id']??0);$q=db()->prepare('SELECT title FROM volunteer_opportunities WHERE id=? AND event_date>=?');$q->execute([$opportunityId,date('Y-m-d')]);$opportunity=$q->fetch();if(!$opportunity)throw new RuntimeException('This volunteer opportunity has ended or could not be found.');
        try{db()->prepare("INSERT INTO volunteer_applications(opportunity_id,user_id,status) VALUES(?,?,'Pending')")->execute([$opportunityId,$me['id']]);}catch(PDOException $t){if($t->getCode()==='23000')throw new RuntimeException('You have already applied for this opportunity.');throw $t;}
        try{$notify=db()->prepare("INSERT INTO notifications(user_id,title,message) SELECT u.id,'New volunteer application',? FROM users u WHERE u.role='admin' AND u.is_active=1 AND COALESCE((SELECT in_app_notifications FROM user_settings s WHERE s.user_id=u.id),1)=1");$notify->execute([$me['name'].' applied for '.$opportunity['title'].'.']);}catch(Throwable $ignored){}flash('Volunteer application submitted.');
    }elseif($type==='volunteer_hours'){
        $opportunityId=(int)($_POST['opportunity_id']??0);$hours=(float)($_POST['hours']??0);$notes=trim((string)($_POST['notes']??''));if($hours<=0||$hours>24||mb_strlen($notes)>255)throw new RuntimeException('Enter hours between 0 and 24 and keep notes under 255 characters.');
        $q=db()->prepare("SELECT o.id,o.title FROM volunteer_opportunities o JOIN volunteer_applications a ON a.opportunity_id=o.id AND a.user_id=? AND a.status='Approved' WHERE o.id=?");$q->execute([$me['id'],$opportunityId]);$opportunity=$q->fetch();if(!$opportunity)throw new RuntimeException('Hours can be submitted after your volunteer application is approved.');
        db()->prepare('INSERT INTO volunteer_hours(user_id,opportunity_id,hours,notes) VALUES(?,?,?,?)')->execute([$me['id'],$opportunityId,$hours,$notes]);flash('Volunteer hours submitted for admin verification.');
    }elseif($type==='cultural_register'){
        $activityId=(int)($_POST['activity_id']??0);$q=db()->prepare('SELECT id,title FROM cultural_activities WHERE id=? AND activity_date>=CURDATE()');$q->execute([$activityId]);$activity=$q->fetch();if(!$activity)throw new RuntimeException('This cultural activity is no longer open for registration.');
        try{db()->prepare('INSERT INTO activity_registrations(activity_id,user_id) VALUES(?,?)')->execute([$activityId,$me['id']]);}catch(PDOException $t){if($t->getCode()==='23000')throw new RuntimeException('You are already registered for this activity.');throw $t;}
        try{$notify=db()->prepare("INSERT INTO notifications(user_id,title,message) SELECT u.id,'New cultural activity registration',? FROM users u WHERE u.role='admin' AND u.is_active=1 AND COALESCE((SELECT in_app_notifications FROM user_settings s WHERE s.user_id=u.id),1)=1");$notify->execute([$me['name'].' registered for '.$activity['title'].'.']);}catch(Throwable $ignored){}flash('You are registered for this cultural activity.');
    }elseif($type==='cultural_cancel'){
        $activityId=(int)($_POST['activity_id']??0);$q=db()->prepare('DELETE r FROM activity_registrations r JOIN cultural_activities c ON c.id=r.activity_id WHERE r.activity_id=? AND r.user_id=? AND c.activity_date>=CURDATE()');$q->execute([$activityId,$me['id']]);if(!$q->rowCount())throw new RuntimeException('Registration not found or this activity has already ended.');flash('Your registration has been withdrawn.');
    }else{throw new RuntimeException('Unknown action.');}
}catch(Throwable $t){flash($t instanceof RuntimeException?$t->getMessage():'Could not complete that action.','error');}
$return=in_array($type,['volunteer_apply','volunteer_hours'],true)?'student/volunteering.php':(in_array($type,['cultural_register','cultural_cancel'],true)?'student/cultural-activities.php':($_POST['return']??'student/dashboard.php'));go($return);
