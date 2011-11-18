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

$ath = Loader::helper('attribute_tool', 'mesch_project'); 
$nh = Loader::helper('navigation'); 

echo '<table class="mesch-project-table">';
echo '<thead>';
echo '<th>' . t('#') . '</th>';
echo '<th>' . t('Project') . '</th>';
echo '<th>' . t('Issue') . '</th>';
echo '<th>' . t('Priority') . '</th>';
echo '<th>' . t('State') . '</th>';
echo '<th>' . t('Last Update') . '</th>';
echo '<th>' . t('Due Date') . '</th>';
echo '</thead>';
echo '<tbody>';
foreach ($issues as $issue) {
   $parentPage = Page::getByID($issue->getCollectionParentID());
   
   $projectLink = $nh->getLinkToCollection($parentPage); 
   $issueLink = $nh->getLinkToCollection($issue); 
   
   echo '<tr>';
   echo "<td><a href=\"{$issueLink}\">{$issue->getCollectionID()}</a></td>";
   echo "<td><a href=\"{$projectLink}\">{$parentPage->getCollectionName()}</a></td>";
   echo "<td><a href=\"{$issueLink}\">{$issue->getCollectionName()}</a></td>";
   
   echo "<td>{$ath->getAttributeDisplay($issue,'mesch_project_priority',false)}</a>";
   echo "<td>{$ath->getAttributeDisplay($issue,'mesch_project_state',false)}</a>";
   echo "<td>{$ath->getAttributeDisplay($issue,'mesch_project_update',false)}</a>";   
   echo "<td>{$ath->getAttributeDisplay($issue,'mesch_project_due_date',false)}</a>";   

   echo '</tr>';
}
echo '</tbody>';
echo '</table>';
?>