<?php
/*
mesch.ch project management

Copyright 2011 mesch web consulting & design GmbH, 
all portions of this codebase are copyrighted to the people 
listed in contributors.txt.

This program is free software; you can redistribute it and/or
modify it under the terms of the GNU General Public License
as published by the Free Software Foundation; either version 2
of the License, or (at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA  02111-1307, USA.
*/

defined('C5_EXECUTE') or die(_("Access Denied."));
$this->inc('elements/header.php');

$nh = Loader::helper('navigation'); 
$ath = Loader::helper('attribute_tool', 'mesch_project'); 
      
?>

<section id="content">
<?php
if (isset($message)) {
   echo $message;
}

$parentProject = Page::getByID($c->getCollectionParentID());

echo '<h1><a href="' . $nh->getLinkToCollection($parentProject) . '">' . $parentProject->getCollectionName() . '</a> :: <a href=".">' . $c->getCollectionName() . '</a></h1>';

echo '<div class="mesch-project-issue">';

$cuID = $c->getCollectionUserID();
$ui = UserInfo::getByID($cuID);
echo "<p>Added by {$ui->getUserName()}</p>";

echo "<hr/>";

echo "<table>";
   echo "<tr>";     
   echo "<td>";
   echo $ath->getAttributeDisplay($c, 'mesch_project_priority');
   echo "</td>";              
   echo "</tr>";   
   
   echo "<tr>";     
   echo "<td>";
   echo $ath->getAttributeDisplay($c, 'mesch_project_state');
   echo "</td>";              
   echo "</tr>";    
   
   echo "<tr>";     
   echo "<td>";
   echo $ath->getAttributeDisplay($c, 'mesch_project_due_date');
   echo "</td>";              
   echo "</tr>";     
   
   echo "<tr>";     
   echo "<td>";
   echo $ath->getAttributeDisplay($c, 'mesch_project_assignee');
   echo "</td>";              
   echo "</tr>";  
   
   echo "<tr>";     
   echo "<td>";
   echo $ath->getAttributeDisplay($c, 'mesch_project_estimated_time');
   echo "</td>";              
   echo "</tr>";  
   
echo "</table>";

echo "<hr/>";


$a = new Area('Issue Description');
$a->setCustomTemplate('mesch_project_comment', 'templates/issue_description.php');
$a->display($c);
echo '</div>';

echo '<h2>' . t('History') . '</h2>';

echo '<div class="mesch-project-issue-comments">';
$b = new Area('Issue Comments');
$b->setBlockWrapperStart('<div class="mesch-project-issue-comment">');
$b->setBlockWrapperEnd('</div>');
$b->display($c);
echo '</div>';

?>            

<form method="post" action="<?php echo $this->action('update')?>" enctype="multipart/form-data">

<?php echo t('Note') ?>
<textarea name="text" style="width:100%;height:200px;"></textarea>

<?php
echo $ath->getAttributeForm($c, 'mesch_project_priority') . '<br/>';
echo $ath->getAttributeForm($c, 'mesch_project_state') . '<br/>';
echo $ath->getAttributeForm($c, 'mesch_project_due_date') . '<br/>';
echo $ath->getAttributeForm($c, 'mesch_project_assignee') . '<br/>';
echo $ath->getAttributeForm($c, 'mesch_project_estimated_time') . '<br/>';
?>
<label>Attachment</label>
<input type="file" name="attachment"/>
<br/>
<input type="submit"/>

</form>
</section>

<?php $this->inc('elements/footer.php'); ?>