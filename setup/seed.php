<?php
if (PHP_SAPI !== 'cli') { http_response_code(404); exit('Not found.'); }
require __DIR__.'/../config/app.php';
// Run once after importing the schema. Existing users and events are preserved.
try {
    $pdo=db();
    $pdo->beginTransaction();
    $users=[['Ashish Raun','ashishraunksmp@gmail.com','Ashish@03','admin'],['Meenal Patro','meenalpatro@gmail.com','Meenal@27','admin']];
    foreach($users as [$name,$email,$password,$role]){$s=$pdo->prepare('INSERT INTO users(name,email,password_hash,role) VALUES(?,?,?,?) ON DUPLICATE KEY UPDATE name=VALUES(name),role=VALUES(role)');$s->execute([$name,$email,password_hash($password,PASSWORD_DEFAULT),$role]);}
    $sid=(int)$pdo->query("SELECT id FROM users WHERE email='meenalpatro@gmail.com'")->fetchColumn();
    $aid=(int)$pdo->query("SELECT id FROM users WHERE email='ashishraunksmp@gmail.com'")->fetchColumn();
    $pdo->prepare('INSERT INTO user_settings(user_id) VALUES(?) ON DUPLICATE KEY UPDATE user_id=user_id')->execute([$sid]);
    $events=[
      ['Inter-College Cultural Fest "Rangmanch"','cultural','Celebrate music, dance, and creativity with students from across the region.','2026-10-25','09:00:00','18:00:00','MU Campus',800],
      ['Web Development Workshop','technical','A hands-on web development workshop for students. Bring your laptop and learn by building.','2026-10-22','10:00:00','13:00:00','Computer Lab, Block A',120],
      ['Cricket Tournament','sports','Join the campus cricket tournament. Teams and spectators are welcome.','2026-11-05','08:00:00','18:00:00','Sports Ground',300],
      ['Tree Plantation Drive','volunteer','Help plant native trees and care for our campus landscape.','2026-11-20','08:00:00','11:00:00','MU Campus',100]
    ];
    foreach($events as $row){$s=$pdo->prepare('INSERT INTO events(title,category,description,event_date,event_time,end_time,venue,capacity,created_by) SELECT ?,?,?,?,?,?,?,?,? WHERE NOT EXISTS(SELECT 1 FROM events WHERE title=?)');$s->execute([...$row,$aid,$row[0]]);}
    foreach([['Web Development Workshop','Approved'],['Inter-College Cultural Fest "Rangmanch"','Shortlisted'],['Cricket Tournament','Pending']] as [$title,$status]){$s=$pdo->prepare('INSERT IGNORE INTO event_applications(event_id,user_id,status) SELECT id,?,? FROM events WHERE title=?');$s->execute([$sid,$status,$title]);}
    $volunteer='Tree Plantation Drive';$pdo->prepare('INSERT INTO volunteer_opportunities(title,description,event_date,venue,created_by) SELECT ?,?,?,?,? WHERE NOT EXISTS(SELECT 1 FROM volunteer_opportunities WHERE title=?)')->execute([$volunteer,'Plant and care for native campus trees.','2026-11-20','MU Campus',$aid,$volunteer]);
    $announcement='Volunteer Registration Open!';$pdo->prepare('INSERT INTO announcements(title,body,created_by) SELECT ?,?,? WHERE NOT EXISTS(SELECT 1 FROM announcements WHERE title=?)')->execute([$announcement,'Registrations for the upcoming community service drive are now open. Join and make an impact.',$aid,$announcement]);
    $pdo->commit();
    echo 'EventHub seed complete. Demo users are ready. Remove or restrict setup/seed.php after setup.';
} catch(Throwable $e) { if(isset($pdo)&&$pdo->inTransaction())$pdo->rollBack(); http_response_code(500); echo 'Seed failed. Confirm the schema and database settings, then retry.'; }
