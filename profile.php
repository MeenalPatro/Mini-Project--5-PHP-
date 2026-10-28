<?php
require __DIR__.'/config/app.php';
$me=require_login();
ensure_profile_schema();
$error='';
if($_SERVER['REQUEST_METHOD']==='POST'){
    csrf_check();
    $name=trim((string)($_POST['name']??''));
    $length=preg_match_all('/./us',$name);
    if($name===''||$length===false||$length>120){$error='Enter your name (up to 120 characters).';}
    else{
        try{
            $avatarPath=$me['avatar_path']??null;
            $file=$_FILES['profile_photo']??null;
            if($file&&$file['error']!==UPLOAD_ERR_NO_FILE){
                if($file['error']!==UPLOAD_ERR_OK) throw new RuntimeException('Photo upload failed. Please try again.');
                if($file['size']>5*1024*1024) throw new RuntimeException('Choose a photo smaller than 5 MB.');
                $mime=(new finfo(FILEINFO_MIME_TYPE))->file($file['tmp_name']);
                $extensions=['image/jpeg'=>'jpg','image/png'=>'png','image/webp'=>'webp'];
                if(!isset($extensions[$mime])||!getimagesize($file['tmp_name'])) throw new RuntimeException('Upload a JPG, PNG, or WebP image.');
                $directory=__DIR__.'/assets/uploads/avatars';
                if(!is_dir($directory)&&!mkdir($directory,0755,true)&&!is_dir($directory)) throw new RuntimeException('Could not prepare photo storage.');
                $filename=bin2hex(random_bytes(18)).'.'.$extensions[$mime];
                if(!move_uploaded_file($file['tmp_name'],$directory.'/'.$filename)) throw new RuntimeException('Could not save the selected photo.');
                $avatarPath='assets/uploads/avatars/'.$filename;
            }
            db()->prepare('UPDATE users SET name=?,avatar_path=? WHERE id=?')->execute([$name,$avatarPath,$me['id']]);
            if(($me['role']??'')==='student'){
                $degree=trim((string)($_POST['degree']??'BCA'));
                $year=trim((string)($_POST['year_label']??'1st Year'));
                if($degree===''||mb_strlen($degree)>80||$year===''||mb_strlen($year)>40) throw new RuntimeException('Check your course and year details.');
                db()->prepare('INSERT INTO student_profiles(user_id,degree,year_label) VALUES(?,?,?) ON DUPLICATE KEY UPDATE degree=VALUES(degree),year_label=VALUES(year_label)')->execute([$me['id'],$degree,$year]);
            }
            $q=db()->prepare('SELECT id,name,email,role,avatar_path FROM users WHERE id=?');$q->execute([$me['id']]);$_SESSION['user']=$q->fetch();
            flash('Profile updated successfully.');go('profile.php');
        }catch(Throwable $t){$error=$t instanceof RuntimeException?$t->getMessage():'Could not save your profile. Please try again.';}
    }
}
$me=user();$degree='BCA';$year='1st Year';
if(($me['role']??'')==='student'){try{$q=db()->prepare('SELECT degree,year_label FROM student_profiles WHERE user_id=?');$q->execute([$me['id']]);$studentProfile=$q->fetch()?:[];$degree=$studentProfile['degree']??$degree;$year=$studentProfile['year_label']??$year;}catch(Throwable $t){}}
$pageTitle='My Profile';$activePage='profile';$bodyClass=($me['role']==='admin'?'admin-page':($me['role']==='committee'?'committee-page':'dashboard-page'));$hideSiteNav=true;
if($me['role']==='student')$sidebarItems=[['Dashboard','student/dashboard.php'],['Explore Events','events.php'],['My Applications','student/applications.php'],['My Volunteering','student/volunteering.php'],['My Cultural Activities','student/cultural-activities.php'],['My Profile','profile.php'],['Notifications','notifications.php'],['Change Password','student/settings.php']];
if($me['role']==='committee')$sidebarItems=[];
require __DIR__.'/includes/header.php';
?><div class="dash-layout <?=$me['role']==='admin'?'admin-layout':''?>"><?php require __DIR__.'/includes/sidebar.php';?><main class="content-page profile-page admin-content"><h1>My Profile</h1><p>Update your personal details and profile photo.</p><?php if($error):?><div class="inline-error"><?=e($error)?></div><?php endif;?><form class="form-card profile-form" method="post" enctype="multipart/form-data"><input type="hidden" name="_csrf" value="<?=csrf()?>"><div class="profile-photo-editor"><span class="avatar profile-preview"><?php if(avatar_source($me['name'])):?><img class="avatar-photo" src="<?=e(avatar_source($me['name']))?>" alt="Current profile photo"><?php else:?><?=e(initials($me['name']))?><?php endif;?></span><label class="photo-picker">Profile photo<input type="file" name="profile_photo" accept="image/jpeg,image/png,image/webp"><small>JPG, PNG or WebP · up to 5 MB</small></label></div><label>Full name<input required maxlength="120" name="name" value="<?=e($me['name'])?>"></label><label>Email address<input value="<?=e($me['email'])?>" readonly></label><?php if($me['role']==='student'):?><div class="profile-fields"><label>Course<input maxlength="80" name="degree" value="<?=e($degree)?>"></label><label>Year<input maxlength="40" name="year_label" value="<?=e($year)?>"></label></div><?php endif;?><p class="profile-role">Account type: <strong><?=e(ucfirst($me['role']))?></strong></p><button class="btn primary" type="submit">Save profile</button></form></main></div><?php require __DIR__.'/includes/footer.php';
