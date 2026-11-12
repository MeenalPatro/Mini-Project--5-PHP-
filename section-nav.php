<?php
$sectionTabs=[
    'volunteers'=>[['Manage Volunteers','admin/volunteers.php'],['Manage Opportunities','admin/volunteers.php?view=opportunities'],['Verify Hours','admin/volunteers.php?view=hours']],
    'cultural'=>[['Manage Activities','admin/cultural.php'],['Add Activity','admin/cultural.php?action=create']],
    'users'=>[['Students','admin/users.php?role=student'],['Committee Members','admin/users.php?role=committee'],['Admin Accounts','admin/admin-accounts.php']],
    'reports'=>[['Event Reports','admin/reports.php'],['Volunteer Reports','admin/reports.php?type=volunteer'],['Analytics','admin/reports.php?type=analytics']],
    'settings'=>[['General Settings','admin/settings.php'],['Notifications','admin/announcements.php']],
];
$tabs=$sectionTabs[$adminSection]??[];
?><nav class="admin-event-nav" aria-label="<?=$adminSection==='users'?'User':ucfirst($adminSection)?> pages"><?php foreach($tabs as [$tabLabel,$tabPath]):$parts=parse_url($tabPath);parse_str($parts['query']??'',$tabQuery);$currentQuery=$_GET;$isActive=basename((string)($_SERVER['SCRIPT_NAME']??''))===basename($parts['path']??'');foreach(['view','action','type','role'] as $key){if(isset($tabQuery[$key])||isset($currentQuery[$key])){if(($tabQuery[$key]??null)!==($currentQuery[$key]??null))$isActive=false;}}?>
<a class="<?=$isActive?'active':''?>" href="<?=url($tabPath)?>"><?=e($tabLabel)?></a><?php endforeach;?></nav>
