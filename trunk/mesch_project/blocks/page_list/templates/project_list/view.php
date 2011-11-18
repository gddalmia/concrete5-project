<?php 
	defined('C5_EXECUTE') or die("Access Denied.");
	$textHelper = Loader::helper("text"); 
	// now that we're in the specialized content file for this block type, 
	// we'll include this block type's class, and pass the block to it, and get
	// the content
	
	if (count($cArray) > 0) { ?>
	<div class="ccm-page-list">
	
   <input type="text" name="mesch-project-project-filter" class="mesch-project-project-filter"/>
   
	<?php 
   echo '<ul class="mesch-project-tree-list">';
	for ($i = 0; $i < count($cArray); $i++ ) {
		$cobj = $cArray[$i]; 
		$target = $cobj->getAttribute('nav_target');

		$title = $cobj->getCollectionName(); 
      $projectCID = $cobj->getCollectionID();
      $link  = $nh->getLinkToCollection($cobj);
      if ($target != '') {
         $target = " target=\"{$target}\" ";
      }

      echo '<li>';
      echo "<a {$target} href=\"{$link}\">{$title}</a><br/>";
      
      $db = Loader::db();
      $childProjects = $db->GetAll('SELECT cID from Pages p inner join PageTypes pt on pt.ctID=p.ctID WHERE cParentID=? AND pt.ctHandle=\'project\' ORDER BY cDisplayOrder', 
            array($projectCID)
         );
      if (count($childProjects) > 0) {
         echo '<ul>';
         foreach ($childProjects as $cSubProjectID) {
            $subProject = Page::getByID($cSubProjectID);
            echo "<li><a href=\"{$nh->getLinkToCollection($subProject)}\">{$subProject->getCollectionName()}</a></li>";
         }
         echo '</ul>';
      }
      echo '</li>';
	
   } 
   echo '</ul>';
	if(!$previewMode && $controller->rss) { 
			$btID = $b->getBlockTypeID();
			$bt = BlockType::getByID($btID);
			$uh = Loader::helper('concrete/urls');
			$rssUrl = $controller->getRssUrl($b);
			?>
			<div class="ccm-page-list-rss-icon">
				<a href="<?php echo $rssUrl?>" target="_blank"><img src="<?php echo $uh->getBlockTypeAssetsURL($bt, 'rss.png')?>" width="14" height="14" /></a>
			</div>
			<link href="<?php echo BASE_URL . $rssUrl?>" rel="alternate" type="application/rss+xml" title="<?php echo $controller->rssTitle?>" />
		<?php  
	} 
	?>
</div>
<?php  } 
	
	if ($paginate && $num > 0 && is_object($pl)) {
		$pl->displayPaging();
	}
	
?>