<?php
$adminNav=str_contains($bodyClass??'','admin-page');
$committeeNav=str_contains($bodyClass??'','committee-page');
$sidebarIcon=static function(string $name): string {
    $paths=[
        'home'=>'<path d="m3 10 9-7 9 7v10a1 1 0 0 1-1 1h-5v-6H9v6H4a1 1 0 0 1-1-1z"/>',
        'calendar'=>'<rect x="3" y="5" width="18" height="16" rx="2"/><path d="M16 3v4M8 3v4M3 10h18M8 14h3v3H8z"/>',
        'people'=>'<path d="M16 21v-2a4 4 0 0 0-4-4H7a4 4 0 0 0-4 4v2M9.5 11a4 4 0 1 0 0-8 4 4 0 0 0 0 8ZM20 21v-2a4 4 0 0 0-3-3.87M16 3.13a4 4 0 0 1 0 7.75"/>',
        'music'=>'<path d="M9 18V5l12-2v13M9 18a3 3 0 1 1-3-3 3 3 0 0 1 3 3Zm12-2a3 3 0 1 1-3-3 3 3 0 0 1 3 3Z"/>',
        'users'=>'<path d="M16 21v-2a4 4 0 0 0-4-4H7a4 4 0 0 0-4 4v2M9.5 11a4 4 0 1 0 0-8 4 4 0 0 0 0 8ZM20 8v6M17 11h6"/>',
        'chart'=>'<path d="M3 3v18h18M8 15l4-4 3 3 6-7M17 7h4v4"/>',
        'settings'=>'<path d="M12 8a4 4 0 1 0 0 8 4 4 0 0 0 0-8Z"/><path d="m19.4 15 .1.1 1.4 1.1-1.4 2.4-1.7-.7-.2.1a8 8 0 0 1-1.5.9l-.2.1-.3 1.8h-2.8l-.3-1.8-.2-.1a8 8 0 0 1-1.5-.9l-.2-.1-1.7.7-1.4-2.4 1.4-1.1.1-.2a8 8 0 0 1 0-1.7l-.1-.2-1.4-1.1 1.4-2.4 1.7.7.2-.1a8 8 0 0 1 1.5-.9l.2-.1.3-1.8h2.8l.3 1.8.2.1a8 8 0 0 1 1.5.9l.2.1 1.7-.7 1.4 2.4-1.4 1.1-.1.2a8 8 0 0 1 0 1.7Z"/>',
        'logout'=>'<path d="M10 17l5-5-5-5M15 12H3M12 3h6a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-6"/>',
    ];
    return '<svg viewBox="0 0 24 24" aria-hidden="true" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">'.($paths[$name]??$paths['calendar']).'</svg>';
};
?>
<aside class="sidebar">
    <div class="sidebar-links">
    <?php if($adminNav):?>
        <a class="side-link admin-dashboard-link <?=$activePage==='dashboard'?'selected':''?>" href="<?=url('admin/dashboard.php')?>"><span><?=$sidebarIcon('home')?></span>Dashboard</a>
        <?php $currentRoute=basename(parse_url($_SERVER['REQUEST_URI']??'',PHP_URL_PATH)?:''); foreach([['Events','calendar',[['Add Event','admin/events.php?action=create'],['Manage Events','admin/events.php'],['Event Applications','admin/applications.php']]],['Volunteers','people',[['Manage Volunteers','admin/volunteers.php'],['Manage Opportunities','admin/volunteers.php?view=opportunities'],['Verify Hours','admin/volunteers.php?view=hours']]],['Cultural Activities','music',[['Add Activity','admin/cultural.php?action=create'],['Manage Activities','admin/cultural.php']]],['Users','users',[['Students','admin/users.php?role=student'],['Committee Members','admin/users.php?role=committee']]],['Reports','chart',[['Event Reports','admin/reports.php'],['Volunteer Reports','admin/reports.php?type=volunteer'],['Analytics','admin/reports.php?type=analytics']]],['Settings','settings',[['General Settings','admin/settings.php'],['Notifications','admin/announcements.php']]]] as [$group,$icon,$children]):$groupActive=false;foreach($children as [$childLabel,$childPath]){if($currentRoute===basename(parse_url($childPath,PHP_URL_PATH))){$groupActive=true;break;}}?>
            <details class="side-group" name="admin-sidebar" <?=$groupActive?'open':''?>><summary><span><?=$sidebarIcon($icon)?></span><?=$group?></summary><?php foreach($children as [$label,$path]):?><a class="side-child" href="<?=url($path)?>"><?=e($label)?></a><?php endforeach;?></details>
        <?php endforeach;?>
    <?php elseif($committeeNav):?>
        <?php foreach([['Dashboard','committee/dashboard.php','home'],['Assigned Events','committee/events.php','calendar'],['Event Applications','committee/applications.php','people'],['Notifications','notifications.php','calendar']] as [$label,$path,$icon]):?><a class="side-link <?=$activePage===basename($path,'.php')?'selected':''?>" href="<?=url($path)?>"><span><?=$sidebarIcon($icon)?></span><?=e($label)?></a><?php endforeach;?>
    <?php else:?>
        <?php foreach(($sidebarItems??[]) as $item):$itemKey=basename(explode('?', $item[1])[0],'.php');$itemHref=$item[0]==='My Profile'?'profile.php':$item[1];?><a class="side-link <?=$activePage===$itemKey?'selected':''?>" href="<?=url($itemHref)?>"><span><?=$sidebarIcon(match($item[0]){'Dashboard'=>'home','Explore Events'=>'calendar','My Applications'=>'calendar','My Volunteering'=>'people','My Cultural Activities'=>'music','My Profile'=>'users','Notifications'=>'calendar','Settings'=>'settings',default=>'calendar'})?></span><?=$item[0]?></a><?php endforeach;?>
    <?php endif;?>
    </div>
</aside>
