<?php
require __DIR__.'/config/app.php';
$pageTitle='Discover events';$activePage='home';$bodyClass='home-page';$logged=user();
$homeName=$logged['name']??'Ananya Patel';$homeDegree='BCA';$homeYear='1st Year';$homeStats=[12,3,4];
if($logged&&$logged['role']==='student'){
    try{
        $profileQuery=db()->prepare('SELECT degree,year_label FROM student_profiles WHERE user_id=?');$profileQuery->execute([$logged['id']]);$profile=$profileQuery->fetch()?:[];
        $homeDegree=$profile['degree']??'BCA';$homeYear=$profile['year_label']??'1st Year';
        $statsQuery=db()->prepare("SELECT (SELECT COUNT(*) FROM event_applications WHERE user_id=? AND status='Approved'),(SELECT COUNT(*) FROM event_applications WHERE user_id=?),(SELECT COALESCE(SUM(hours),0) FROM volunteer_hours WHERE user_id=? AND is_verified=1)");
        $statsQuery->execute([$logged['id'],$logged['id'],$logged['id']]);$homeStats=array_map('intval',$statsQuery->fetch(PDO::FETCH_NUM)?:[0,0,0]);
    }catch(Throwable $t){$homeStats=[0,0,0];}
}
$homeAvatar=url('assets/images/meenal-reference.png');
$allHomeEvents=all_events();$events=array_slice($allHomeEvents,0,4);
require __DIR__.'/includes/header.php';
?>
<main class="home-layout">
    <section class="home-main">
        <section class="hero">
            <div class="hero-copy">
                <div class="eyebrow">MARWADI UNIVERSITY</div>
                <h1>Discover. Participate.<br><span>Make an Impact.</span></h1>
                <p>Be a part of exciting events, volunteer opportunities,<br>tech fests, cultural programs and more — all in one place.</p>
                <form class="hero-search" action="<?=url('events.php')?>"><span><?=eventhub_icon('search')?></span><input name="q" placeholder="Search events, roles, or keywords..."><button class="btn primary">Search</button></form>
                <div class="filters"><a href="<?=url('events.php')?>">All</a><a href="<?=url('events.php?category=cultural')?>"><?=eventhub_icon('music')?> Cultural</a><a href="<?=url('events.php?category=technical')?>"><span class="code-mark">&lt;/&gt;</span> Technical</a><a href="<?=url('events.php?category=sports')?>"><?=eventhub_icon('trophy')?> Sports</a><a href="<?=url('events.php?category=volunteer')?>"><?=eventhub_icon('people')?> Volunteer</a></div>
            </div>
            <div class="hero-art"><img class="hero-reference-art" src="<?=url('assets/images/homepage-hero-reference-clean.png')?>" alt="Marwadi University campus with the reference design illustration"></div>
        </section>
        <section class="feature-row">
            <?php foreach([['calendar','Explore Events','Find the latest events, workshops, festivals and more.','events.php','violet'],['people','Volunteer','Contribute your time and skills to make an impact.','student/volunteering.php','cyan'],['code','Showcase Your Talent','Participate in cultural and creative programs.','student/cultural-activities.php','mint'],['trophy','Build Your Network','Meet new people and create lasting connections.','about.php','orange']] as $feature):?>
            <a class="feature-card" href="<?=url($feature[3])?>"><span class="icon-orb <?=$feature[4]?>"><?php if($feature[0]==='code'):?><span class="code-mark">&lt;/&gt;</span><?php else:?><?=eventhub_icon($feature[0])?><?php endif;?></span><span><b><?=$feature[1]?></b><small><?=$feature[2]?></small></span><em>→</em></a>
            <?php endforeach;?>
        </section>
        <section class="section-block"><div class="section-heading"><h2><span class="heading-icon"><?=eventhub_icon('calendar')?></span> Upcoming Events</h2><a href="<?=url('events.php')?>">View All →</a></div><div class="event-grid home-events">
            <?php foreach($events as $event):?><article class="event-card"><div class="event-image"><img src="<?=e(image_source($event))?>" data-fallback="<?=url(image_for($event))?>" onerror="this.onerror=null;this.src=this.dataset.fallback" alt=""><span class="pill <?=e($event['category'])?>"><?=e(ucfirst($event['category']))?></span></div><div class="event-body"><h3><?=e($event['title'])?></h3><p><?=eventhub_icon('calendar')?> <?=date('d M Y',strtotime($event['event_date']))?> &nbsp;•&nbsp; <?=e(date('g:i A',strtotime($event['event_time']??'09:00')))?> – <?=e(date('g:i A',strtotime($event['end_time']??'17:00')))?></p><p><?=eventhub_icon('location')?> <?=e($event['venue'])?></p><div class="event-bottom"><span class="mini-avatars"><i></i><i></i><i></i></span><small>+<?=max(38,(int)$event['applicants']+82)?> interested</small><a class="btn <?=((int)$event['capacity']>0&&(int)$event['applicants']>=(int)$event['capacity'])?'outline':'primary'?>" href="<?=url('event.php?id='.$event['id'])?>">Apply</a></div></div></article><?php endforeach;?>
            <?php if(!$events):?><div class="empty">Events will appear here after the database is set up.</div><?php endif;?>
        </div></section>        <section class="why-join"><div class="students-art"><img src="<?=url('assets/images/eventhub-community-panorama.jpg')?>" alt="Marwadi University students together at a campus event"></div><div><h2>Why Join EventHub?</h2><div class="benefits"><span><?=eventhub_icon('calendar')?> Be Part of<br>Exciting Events</span><span><?=eventhub_icon('settings')?> Gain New<br>Skills</span><span><?=eventhub_icon('people')?> Meet New<br>People</span><span><?=eventhub_icon('people')?> Grow Your<br>Network</span></div></div></section>
    </section>
    <aside class="home-aside">
        <section class="guest-auth-card"><span class="guest-auth-icon"><?=eventhub_icon('people')?></span><div class="eyebrow">YOUR CAMPUS. YOUR MOMENT.</div><h2>Be part of it all.</h2><p>Sign in or create a Marwadi University student account to apply for events, track applications, and join campus activities.</p><div class="guest-auth-actions"><a class="btn primary full" href="<?=url('login.php')?>">Sign in</a><a class="btn outline full" href="<?=url('register.php')?>">Create student account</a></div><small class="guest-auth-note">A student account is required to join EventHub activities.</small></section>
        <div class="aside-art"><span>Same Campus</span><strong>Bigger Dreams</strong><b>♡</b><div class="mini-building"></div><p>Find your people, explore campus events, and make memories at Marwadi University.</p></div><div class="aside-motto-card"><span>✧</span><strong>Together We Create More</strong></div><div class="aside-motto-card"><span>✦</span><strong>MU Community</strong></div>
    </aside>
</main>
<?php require __DIR__.'/includes/footer.php'; ?>
