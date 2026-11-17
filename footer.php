<?php
$showFooter=basename((string)($_SERVER['SCRIPT_NAME']??''))==='index.php';
if($showFooter):
?>
<footer class="site-footer"><a class="footer-brand" href="<?=url()?>">Marwadi University</a><div><a href="<?=url()?>">Home</a><a href="<?=url('events.php')?>">Events</a><a href="<?=url('student/applications.php')?>">My Applications</a><a href="<?=url('about.php')?>">About</a></div><span>© 2026 Marwadi University. All rights reserved.</span></footer>
<?php endif;?></body></html>
