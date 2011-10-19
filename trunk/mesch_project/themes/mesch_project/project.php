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

echo '<h1>' . $c->getCollectionName() . '</h1>';

$collectionDescription = $c->getCollectionDescription();
if ($collectionDescription) {
   $collectionDescription = nl2br($collectionDescription);
   echo "<p>{$collectionDescription}</p>";
}

// display sub projects if available
if (!empty($projects)) {
   echo "<ul>";
   foreach ($projects as $project) {
      $projectLink = $nh->getLinkToCollection($project); 
      echo "<li><a href=\"$projectLink\">{$project->getCollectionName()}</a></li>";
   }
   echo "</ul>";   
} 

$b = new Area('Project Description');
$b->display($c);

// display project issues
echo '<table class="mesch-project-list">';
   echo '<thead>';
      echo '<tr>
            <th>#</th>
            <th>' . t('Subject') . '</th>
            <th>' . t('Assignee') . '</th>
            <th>' . t('Priority') . '</th>
            <th>' . t('State') . '</th>
            <th>' . t('Updated on') . '</th>
            <th>' . t('Created on') . '</th>
         </tr>';
   echo '</thead>';
   echo '<tbody>';
      foreach ($issues as $issue) {
         $issueLink = $nh->getLinkToCollection($issue); 
         
         echo "<tr>";
         echo "<td><a href=\"{$issueLink}\">{$issue->getCollectionID()}</a>";
			echo "<td><a href=\"{$issueLink}\">{$issue->getCollectionName()}</a>";
         echo "<td>{$ath->getAttributeDisplay($issue,'mesch_project_assignee',false)}</a>";
         echo "<td>{$ath->getAttributeDisplay($issue,'mesch_project_priority',false)}</a>";
         echo "<td>{$ath->getAttributeDisplay($issue,'mesch_project_state',false)}</a>";
         echo "<td>{$ath->getAttributeDisplay($issue,'mesch_project_update',false)}</a>";
         echo "<td>{$issue->getCollectionDatePublic(DATE_APP_GENERIC_MDYT)}</a>";
         echo "</tr>";
      }
   echo '</tbody>';
echo '</table>';

echo $this->controller->getPagination();

// form to add new issue

echo "<form method=\"post\" action=\"{$this->action('new_issue')}\">
   <label>Subject</label> <input type=\"text\" name=\"subject\"/><br/>
   " . $ath->getAttributeForm(null,'mesch_project_assignee',true) . "<br/>
   " . $ath->getAttributeForm(null,'mesch_project_priority',true) . "<br/>
   " . $ath->getAttributeForm(null,'mesch_project_state',true) . "<br/>
   " . $ath->getAttributeForm(null,'mesch_project_due_date',true) . "<br/>
   <label>Message</label> <textarea style=\"width:500px;height:200px;\" name=\"text\"></textarea><br/>
   <input type=\"submit\" value=\"".t('New Issue')."\"/>
</form>";

?>            

</section>

<?php $this->inc('elements/footer.php'); ?>