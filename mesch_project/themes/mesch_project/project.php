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

<script type="text/javascript">
$(document).ready(function() {
   $(".mesch-project-accessdata-edit").click(function(event) {
      event.preventDefault();
      
      /*$(this).parent().parent().find(".mesch-project-input-text").each(function(e,v) {
         var currentValue = $(this).html();
         
         var $newElement = $("<input/>");
         $newElement.val(currentValue);
         
         $(this).html($newElement);
      });*/
   });
});
</script>

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

echo "<h2>" . t('Issues') . "</h2>";

// display project issues
echo '<table class="mesch-project-list mesch-project-table">';
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
   echo '<tfoot>';
      echo '<tr>';
         echo '<td colspan="7">';
         echo $this->controller->getPagination();
         echo '</td>';
      echo '</tr>';
      echo '<tr>';
         echo '<td colspan="7">';
         
         // form to add new issue

         echo "<form method=\"post\" action=\"{$this->action('new_issue')}\" enctype=\"multipart/form-data\">
            <label>Subject</label> <input type=\"text\" name=\"subject\"/><br/>
            " . $ath->getAttributeForm(null,'mesch_project_assignee',true) . "<br/>
            " . $ath->getAttributeForm(null,'mesch_project_priority',true) . "<br/>
            " . $ath->getAttributeForm(null,'mesch_project_state',true) . "<br/>
            " . $ath->getAttributeForm(null,'mesch_project_due_date',true) . "<br/>
            <label>Message</label> <textarea style=\"width:500px;height:200px;\" name=\"text\"></textarea><br/>
            <label>Attachment</label> <input type=\"file\" name=\"attachment\"/><br/>
            <input type=\"submit\" value=\"".t('New Issue')."\"/>
         </form>";

         echo '</td>';
      echo '</tr>';      
   echo '</tfoot>';
echo '</table>';


// display access data
echo "<h2>" . t('Access data') . "</h2>";

echo '<table class="mesch-project-access-data mesch-project-table">';
   echo '<thead>';
      echo '<tr>
            <th>' . t('Type') . '</th>
            <th>' . t('Name') . '</th>
            <th>' . t('Username') . '</th>
            <th>' . t('Password') . '</th>
            <th>' . t('Server') . '</th>
            <th>' . t('Database') . '</th>
            <th>' . t('Action') . '</th>
         </tr>';
   echo '</thead>';
   echo '<tbody>';
      foreach ($access_data as $accessDataEntry) {                  
         echo "<tr>";
            echo "<td>{$accessDataEntry->accessdataTypeId}</a>";
            echo "<td class=\"mesch-project-input-text\">{$accessDataEntry->name}</a>";
            echo "<td class=\"mesch-project-input-text\">{$accessDataEntry->userName}</a>";
            echo "<td class=\"mesch-project-input-text\">{$accessDataEntry->userPassword}</a>";
            echo "<td class=\"mesch-project-input-text\">{$accessDataEntry->serverName}</a>";
            echo "<td class=\"mesch-project-input-text\">{$accessDataEntry->databaseName}</a>";
            echo "<td>";
               echo "<a href=\"{$this->action('delete_access_data',$accessDataEntry->accessdataId)}\" >" . t('Delete') . "</a> | ";
               echo "<a href=\"\" class=\"mesch-project-accessdata-edit\">{$accessDataEntry->getActionLink()}</a>";
            echo "</td>";
         echo "</tr>";
      }
   echo '</tbody>';
   echo '<tfoot>';
      echo '<form method="post" action="'.$this->action('new_access_data').'">';
      echo '<tr>';
         echo "<td>{$this->controller->getAccessDataTypeForm()}</td>";
         echo "<td><input type=\"text\" name=\"mesch-project-access-data-name\"/></td>";
         echo "<td><input type=\"text\" name=\"mesch-project-access-data-username\"/></td>";
         echo "<td><input type=\"text\" name=\"mesch-project-access-data-password\"/></td>";
         echo "<td><input type=\"text\" name=\"mesch-project-access-data-server\"/></td>";
         echo "<td><input type=\"text\" name=\"mesch-project-access-data-database\"/></td>";         
         echo "<td><input type=\"submit\" value=\"".t('Add')."\"/></td>";
      echo '</tr>';
      echo '</form>';
   echo '</tfoot>';
echo '</table>';


?>            


</section>

<?php $this->inc('elements/footer.php'); ?>