<?php
$totalPages=(int)ceil(($totalRecords??0)/max(1,(int)($perPage??25)));
$currentPage=max(1,(int)($currentPage??1));
if($totalPages>1):$query=$_GET;unset($query['export']);?>
<nav class="admin-pagination" aria-label="Pagination"><span>Showing <?=min((int)$totalRecords,($currentPage-1)*(int)$perPage+1)?>–<?=min((int)$totalRecords,$currentPage*(int)$perPage)?> of <?=(int)$totalRecords?></span><div><?php for($p=max(1,$currentPage-2);$p<=min($totalPages,$currentPage+2);$p++):$query['page']=$p;?><a class="<?=$p===$currentPage?'active':''?>" href="<?=url('admin/'.basename((string)($_SERVER['SCRIPT_NAME']??'')).'?'.http_build_query($query))?>" <?=$p===$currentPage?'aria-current="page"':''?>><?=$p?></a><?php endfor;?></div></nav>
<?php endif;?>
