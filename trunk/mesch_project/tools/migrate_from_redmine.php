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

// THIS SCRIPT IS NOT MEANT TO BE USED BY AN END USER
// READ THE CODE AND MAKE THE MODIFICATIONS YOU NEED
// NOT A FINISHED PRODUCT!

/**
 *
 * How does it work? You first have to copy some tables from
 * your redmine database to the c5 database. It's going to be 
 * messy afterwards!
 *
 * After that, make sure all the users you had in redmine exist
 * in concrete5, matching done by using the username. Then
 * you have to check the status and priority map to assign
 * the new field values.
 *
 * After that, you may call the migration script by using this address:
 * /index.php/tools/packages/mesch_project/migrate_from_redmine
 *
 * Please note, depending on the amount of data you have in redmine
 * this can take a long time and consumes a lot of memory. It's
 * recommended to do this on a powerful computer not used by
 * other projects.
 */

$targetPage = Page::getByPath('/mesch-projects');
$pageOwnerID = 1;

$statusMap = array(
   'Neu' => 'New',
   'Zugewiesen' => 'Assigned',
   'Erledigt' => 'Done',
   'Feedback' => 'In Progress',
   'Abgeschlossen' => 'Closed',
   'Zurückgewiesen' => 'Closed',
   'Abgerechnet' => 'Closed'
);

$priorityMap = array(
   'Tief' => 'Low',
   'Normal' => 'Normal',
   'Hoch' => 'High',
   'Dringend' => 'High',
   'Sofort' => 'Urgent'
);

$db = Loader::db();

Loader::model('collection_types'); 
Loader::model('page_list');      
            
// import redmine data
$result = $db->Execute('SELECT * FROM projects LIMIT 0,3');
while ($row = $result->FetchRow()) {
   $ctProject = CollectionType::getByHandle('project');
   $ctIssue = CollectionType::getByHandle('issue');

   $data = array();
   $data['cName']          = $row['name'];
   $data['cDescription']   = $row['description'];
   $data['cDatePublic']    = $row['created_on'];
   $data['uID']            = $pageOwnerID;
  
   $newProjectPage = $targetPage->add($ctProject, $data);    

   $resultIssues = $db->Execute('SELECT i.*, 
      (select name from issue_statuses is_ where is_.id=i.status_id) status_name,
      (select name from enumerations where type=\'IssuePriority\' and id=i.priority_id) priority_name,
      (select uu.uID from users u inner join Users uu on  u.login=uu.uName where id=i.assigned_to_id) assigneeID,
      (select uu.uID from users u inner join Users uu on  u.login=uu.uName where id=i.author_id) authorID
      FROM issues i WHERE project_id=? LIMIT 0,20', array($row['id']));
   while ($rowIssue = $resultIssues->FetchRow()) {
      $data = array();
      $data['cName']          = $rowIssue['subject'];
      $data['cDatePublic']    = $rowIssue['created_on'];
      $data['uID']            = $rowIssue['authorID'];
      
      $newIssuePage = $newProjectPage->add($ctIssue, $data);    
      
      $newIssuePage->setAttribute('mesch_project_assignee', $rowIssue['assigneeID']);
      $newIssuePage->setAttribute('mesch_project_due_date', $rowIssue['due_date']);
      $newIssuePage->setAttribute('mesch_project_estimated_time', $rowIssue['estimed_hours']);
      $newIssuePage->setAttribute('mesch_project_state', $statusMap[$rowIssue['status_name']]);
      $newIssuePage->setAttribute('mesch_project_priority', $priorityMap[$rowIssue['priority_name']]);

      $data = array();
      $data['text'] 		   = $rowIssue['description'];
      $data['createdOn'] 	= $rowIssue['created_on'];
      $data['uID'] 	      = $rowIssue['assigneeID'];
                  
      $block = $newIssuePage->addBlock(BlockType::getByHandle('mesch_project_comment'),'Issue Description',$data);	
      
      // add issue comments
      $resultJournal = $db->Execute("select 
      (select uu.uID from users u inner join Users uu on  u.login=uu.uName where u.id=j.user_id) assigneeID,
      notes, created_on from journals j where journalized_type='Issue' and journalized_id=? and j.notes is not null order by id asc", array($rowIssue['id']));
      
      while ($rowJournal = $resultJournal->FetchRow()) {
         $data = array();
         $data['text'] 		   = $rowJournal['notes'];
         $data['createdOn'] 	= $rowJournal['created_on'];
         $data['uID'] 	      = $rowJournal['assigneeID'];
                     
         $block = $newIssuePage->addBlock(BlockType::getByHandle('mesch_project_comment'),'Issue Comments',$data);	      
      }
      
      unset($newIssuePage);
   }
   
   unset($newProjectPage);
}
?>