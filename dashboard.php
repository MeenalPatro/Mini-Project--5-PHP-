<?php
require __DIR__.'/../config/app.php';
$me=require_role('student');
$pageTitle='Student Dashboard';
$bodyClass='dashboard-page';
$activePage='dashboard';
$sidebarItems=[['Dashboard','student/dashboard.php','▦'],['Explore Events','events.php','⊙'],['My Applications','student/applications.php','♙'],['My Volunteering','student/volunteering.php','♧'],['My Cultural Activities','student/cultural-activities.php','♬'],['My Profile','student/profile.php','♙'],['Notifications','notifications.php','♧'],['Change Password','student/settings.php','⚙']];
$allEvents=all_events();$events=array_slice($allEvents,0,3);$announcements=[];
$applications=[];$applicationCount=0;$joined=0;$hours=0;$culture=0;$degree='BCA';$year='1st Year';
try {
    $q=db()->prepare('SELECT a.status,a.applied_at,e.title,e.category,e.event_date FROM event_applications a JOIN events e ON e.id=a.event_id WHERE a.user_id=? ORDER BY a.applied_at DESC LIMIT 4');
    $q->execute([$me['id']]);$applications=$q->fetchAll();
    $q=db()->prepare('SELECT COUNT(*) FROM event_applications WHERE user_id=?');$q->execute([$me['id']]);$applicationCount=(int)$q->fetchColumn();
    $q=db()->prepare("SELECT COUNT(*) FROM event_applications WHERE user_id=? AND status='Approved'");$q->execute([$me['id']]);$joined=(int)$q->fetchColumn();
    $q=db()->prepare('SELECT COALESCE(SUM(hours),0) FROM volunteer_hours WHERE user_id=? AND is_verified=1');$q->execute([$me['id']]);$hours=(float)$q->fetchColumn();
    $q=db()->prepare('SELECT COUNT(*) FROM activity_registrations WHERE user_id=?');$q->execute([$me['id']]);$culture=(int)$q->fetchColumn();
    $q=db()->prepare('SELECT degree,year_label FROM student_profiles WHERE user_id=?');$q->execute([$me['id']]);$profile=$q->fetch()?:[];$degree=$profile['degree']??$degree;$year=$profile['year_label']??$year;
    $q=db()->query('SELECT title,body,created_at FROM announcements ORDER BY created_at DESC LIMIT 3');$announcements=$q->fetchAll();
} catch(Throwable $t) {}
require __DIR__.'/../includes/header.php';
?>
<div class="dash-layout">
    <?php require __DIR__.'/../includes/sidebar.php'; ?>
    <main class="dash-main">
        <section class="welcome-banner">
            <div><small>Good Morning,</small><h1><?=e($me['name'])?> 👋</h1><p><?=e($degree)?> &nbsp;|&nbsp; <?=e($year)?> &nbsp;•&nbsp; Marwadi University</p><em>“Small steps in volunteering<br>make a big difference.”</em></div>
            <div class="banner-campus"><div class="building"><img class="building-photo" src="<?=url('assets/images/student-dashboard-hero.png')?>" alt="Marwadi University campus" onload="this.parentElement.classList.add('photo-ready')" onerror="this.remove()"></div></div>
        </section>
        <div class="stat-grid">
            <?php foreach([['calendar','Upcoming Events',count($events),'events.php','cyan'],['people','My Applications',$applicationCount,'student/applications.php','violet'],['star','Volunteering Hours',$hours,'student/volunteering.php','mint'],['palette','Cultural Activities',$culture,'student/cultural-activities.php','pink']] as $stat): ?>
            <a class="stat-card <?=$stat[4]?>" href="<?=url($stat[3])?>"><span><?=eventhub_icon($stat[0])?></span><div><small><?=$stat[1]?></small><b><?=$stat[2]?></b><em>View All →</em></div></a>
            <?php endforeach; ?>
        </div>
        <section class="dash-panel"><div class="section-heading"><h2>▦ &nbsp;Upcoming Events</h2><a href="<?=url('events.php')?>">View All →</a></div><div class="event-grid dash-events">
            <?php foreach($events as $event): ?><article class="event-card"><div class="event-image"><img src="<?=e(image_source($event))?>" data-fallback="<?=url(image_for($event))?>" onerror="this.onerror=null;this.src=this.dataset.fallback" alt=""><span class="pill <?=e($event['category'])?>"><?=e(ucfirst($event['category']))?></span></div><div class="event-body"><h3><?=e($event['title'])?></h3><p>▦ &nbsp;<?=date('d M Y',strtotime($event['event_date']))?></p><p>⌖ &nbsp;<?=e($event['venue'])?></p><div class="event-bottom"><small>Campus event</small><a class="btn primary" href="<?=url('event.php?id='.$event['id'])?>">View</a></div></div></article><?php endforeach; ?>
            <?php if(!$events): ?><p class="muted">Upcoming events will appear here.</p><?php endif; ?>
        </div></section>
        <div class="bottom-panels"><section class="dash-panel"><div class="section-heading"><h2>♙ &nbsp;My Applications</h2><a href="<?=url('student/applications.php')?>">View All →</a></div>
            <?php foreach($applications as $application): ?><div class="application-row"><span class="app-thumb">▧</span><span><b><?=e($application['title'])?></b><small><?=date('d M Y',strtotime($application['applied_at']))?></small></span><span class="pill <?=strtolower(e($application['status']))?>"><?=e($application['status'])?></span></div><?php endforeach; ?>
            <?php if(!$applications): ?><p class="muted">Your event applications will show here.</p><?php endif; ?>
        </section><section class="dash-panel"><div class="section-heading"><h2>◉ &nbsp;Recent Announcements</h2><a href="<?=url('notifications.php')?>">View All →</a></div><?php foreach($announcements as $announcement): ?><div class="announcement-row"><span><?=date('d',strtotime($announcement['created_at']))?><small><?=date('M',strtotime($announcement['created_at']))?></small></span><b><?=e($announcement['title'])?><small><?=e(mb_strimwidth($announcement['body'],0,110,'…'))?></small></b></div><?php endforeach; ?><?php if(!$announcements): ?><p class="muted">Recent announcements will appear here.</p><?php endif; ?></section></div>
    </main>
    <aside class="dash-right"><section class="profile-card"><span class="avatar large"><?php if(avatar_source($me['name'])): ?><img class="avatar-photo" src="<?=e(avatar_source($me['name']))?>" alt=""><?php else: ?><?=e(initials($me['name']))?><?php endif; ?></span><h2><?=e($me['name'])?></h2><p><?=e($degree)?> | <?=e($year)?></p><a class="btn primary" href="<?=url('student/profile.php')?>">View Profile →</a></section>
        <section class="dash-panel quick-stats-panel"><h3>Quick Stats</h3><div class="quick-stats"><b><span class="quick-stat-icon events"><?=eventhub_icon('star')?></span><span class="quick-stat-value"><?=$joined?><small>Events Joined</small></span></b><b><span class="quick-stat-icon applications"><?=eventhub_icon('people')?></span><span class="quick-stat-value"><?=$applicationCount?><small>Applications</small></span></b><b><span class="quick-stat-icon hours"><?=eventhub_icon('heart')?></span><span class="quick-stat-value"><?=$hours?><small>Volunteering<br>Hours</small></span></b></div></section>
        <section class="dash-panel"><h3><?=eventhub_icon('user')?> &nbsp;Quick Links</h3><div class="quick-grid"><a href="<?=url('events.php')?>"><b><?=eventhub_icon('calendar')?></b>Browse Events</a><a href="<?=url('student/volunteering.php')?>"><b><?=eventhub_icon('heart')?></b>Volunteering</a><a href="<?=url('student/applications.php')?>"><b><?=eventhub_icon('clipboard')?></b>My Applications</a><a href="<?=url('student/cultural-activities.php')?>"><b><?=eventhub_icon('palette')?></b>Cultural Activities</a></div></section>
        <section class="dash-panel calendar-card"><div class="section-heading"><h3><?=eventhub_icon('calendar')?> &nbsp;Calendar</h3><a href="<?=url('student/calendar.php')?>">View Full Calendar →</a></div><div class="quick-grid"><a href="<?=url('events.php')?>"><b><?=eventhub_icon('calendar')?></b>Browse Events</a><a href="<?=url('student/volunteering.php')?>"><b><?=eventhub_icon('heart')?></b>Apply for Volunteering</a><a href="<?=url('student/applications.php')?>"><b><?=eventhub_icon('clipboard')?></b>My Applications</a><a href="<?=url('student/cultural-activities.php')?>"><b><?=eventhub_icon('palette')?></b>My Cultural Activities</a></div></section>
    </aside>
</div>
<?php require __DIR__.'/../includes/footer.php'; ?>
