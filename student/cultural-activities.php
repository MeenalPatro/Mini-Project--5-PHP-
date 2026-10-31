<?php
require __DIR__.'/../config/app.php';
$me=require_role('student');
$pageTitle='My Cultural Activities';
$activePage='cultural-activities';
$bodyClass='dashboard-page student-cultural-page';
$sidebarItems=[['Dashboard','student/dashboard.php'],['Explore Events','events.php'],['My Applications','student/applications.php'],['My Volunteering','student/volunteering.php'],['My Cultural Activities','student/cultural-activities.php'],['My Profile','student/profile.php'],['Notifications','notifications.php'],['Change Password','student/settings.php']];
$activities=[];
try{
    $q=db()->prepare('SELECT c.id,c.title,c.description,c.activity_date,c.venue,r.created_at AS registered_at,(SELECT COUNT(*) FROM activity_registrations allr WHERE allr.activity_id=c.id) AS registration_count FROM cultural_activities c LEFT JOIN activity_registrations r ON r.activity_id=c.id AND r.user_id=? ORDER BY c.activity_date ASC,c.id DESC');
    $q->execute([$me['id']]);$activities=$q->fetchAll();
}catch(Throwable $t){}
require __DIR__.'/../includes/header.php';
?>
<div class="dash-layout">
    <?php require __DIR__.'/../includes/sidebar.php'; ?>
    <main class="content-page admin-content student-cultural-content">
        <header class="student-cultural-heading"><span class="student-cultural-mark"><?=eventhub_icon('palette')?></span><div><span class="admin-eyebrow">CAMPUS CULTURE</span><h1>My Cultural Activities</h1><p>Discover campus activities and keep track of the ones you join.</p></div></header>
        <section class="student-cultural-section"><div class="student-cultural-section-heading"><div><h2>Activities</h2><p><?=count($activities)?> activities from Marwadi University</p></div></div>
            <?php if($activities):?><div class="student-cultural-grid">
                <?php foreach($activities as $activity):$isRegistered=!empty($activity['registered_at']);$isPast=strtotime($activity['activity_date'])<strtotime(date('Y-m-d'));?>
                    <article class="student-cultural-card <?=$isRegistered?'is-registered':''?>">
                        <div class="student-cultural-card-top"><span class="student-cultural-icon"><?=eventhub_icon('music')?></span><span class="student-cultural-count"><?=eventhub_icon('people')?> <?= (int)$activity['registration_count']?> joined</span></div>
                        <div class="student-cultural-card-copy"><span class="student-cultural-date"><?=date('d M Y',strtotime($activity['activity_date']))?><?= $isPast?' · Past':''?></span><h3><?=e($activity['title'])?></h3><p class="student-cultural-venue"><?=eventhub_icon('location')?> <?=e($activity['venue'])?></p><?php if($activity['description']):?><p class="student-cultural-description"><?=e($activity['description'])?></p><?php else:?><p class="student-cultural-description">Join this campus cultural activity and take part in university life.</p><?php endif;?></div>
                        <div class="student-cultural-card-action"><?php if($isRegistered):?><span class="student-cultural-joined"><?=eventhub_icon('check')?> Registered</span><form method="post" action="<?=url('student/action.php')?>" onsubmit="return confirm('Withdraw your registration for this activity?')"><input type="hidden" name="_csrf" value="<?=csrf()?>"><input type="hidden" name="type" value="cultural_cancel"><input type="hidden" name="activity_id" value="<?=$activity['id']?>"><input type="hidden" name="return" value="student/cultural-activities.php"><button class="student-cultural-cancel" type="submit">Withdraw</button></form><?php elseif(!$isPast):?><form method="post" action="<?=url('student/action.php')?>"><input type="hidden" name="_csrf" value="<?=csrf()?>"><input type="hidden" name="type" value="cultural_register"><input type="hidden" name="activity_id" value="<?=$activity['id']?>"><input type="hidden" name="return" value="student/cultural-activities.php"><button class="btn primary" type="submit">Join Activity →</button></form><?php else:?><span class="student-cultural-past">Activity ended</span><?php endif;?></div>
                    </article>
                <?php endforeach;?>
            </div><?php else:?><div class="student-cultural-empty"><span><?=eventhub_icon('music')?></span><h2>No cultural activities yet</h2><p>New activities published by the university will appear here.</p></div><?php endif;?>
        </section>
        <section class="student-cultural-my"><div><span class="student-cultural-my-icon"><?=eventhub_icon('star')?></span><div><h2>Your participation</h2><p>Activities you join will be marked as Registered above. You can withdraw before the activity date.</p></div></div><a class="btn outline" href="<?=url('events.php?category=cultural')?>">Explore cultural events</a></section>
    </main>
</div>
<?php require __DIR__.'/../includes/footer.php'; ?>
