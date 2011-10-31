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

class TimeTrackingController extends Controller {
   
   public function view() {
      $hh = Loader::helper('html');
      Loader::model('collection_types'); 
      
      Loader::model('page_list');      
      
      $projectPageType = CollectionType::getByHandle('project');
      
      $projectList = new PageList();
      $projectList->filterByCollectionTypeID($projectPageType->getCollectionTypeID());
      $projectList->sortByName();
      $projects = $projectList->get();
      $this->set('projects', $projects);
      
      $projectArray = array();
      foreach ($projects as $project) {
         $projectArray[] = "{cID:'{$project->getCollectionID()}',name:'{$project->getCollectionName()}'}";
      }
      
      $this->set('projectArray', join($projectArray, ','));
      
      $this->addHeaderItem($hh->css('jquery.autocomplete.css', 'mesch_project'));
      $this->addHeaderItem($hh->css('jquery.jgrowl.css', 'mesch_project'));
      $this->addHeaderItem($hh->javascript('jquery.autocomplete.min.js', 'mesch_project'));
      $this->addHeaderItem($hh->javascript('jquery.jgrowl_minimized.js', 'mesch_project'));
      $this->addHeaderItem($hh->javascript('jquery.dataselector.js', 'mesch_project'));
   }
   
   public function getIssues($cID) {   
      Loader::model('collection_types'); 
      Loader::model('page_list');      
      
      $issuePageType   = CollectionType::getByHandle('issue');
      
      $issueList = new PageList();
      $issueList->filterByCollectionTypeID($issuePageType->getCollectionTypeID());
      $issueList->sortByName();
      $issueList->filterByAttribute('mesch_project_state','Closed','!=');
      $issueList->filterByParentID($cID);
      $issues = $issueList->get();
      $this->set('issues', $issues);
      
      $issueArray['entries'] = array();
      foreach ($issues as $issue) {
         $issueArray['entries'][] = array("cID" => $issue->getCollectionID(), "name" => $issue->getCollectionName());
      }     
      
      echo json_encode($issueArray);
      die();
   }
   
   public function getTimeEntries() {
      $db = Loader::db();
      $u = new User();
      
      $result = $db->Execute("SELECT timeEntryID, projectID, cv_project.cvName projectName, mpte.cID, cv_issue.cvName issueName, hours, comment FROM MeschProjectTimeEntries mpte 
         INNER JOIN CollectionVersions cv_project ON mpte.projectID=cv_project.cID and cv_project.cvIsApproved=1
         INNER JOIN CollectionVersions cv_issue ON mpte.cID=cv_issue.cID and cv_issue.cvIsApproved=1
         WHERE spentOn=str_to_date(?,'%Y-%m-%d') AND uID=?", 
         array(
            $_REQUEST['date'], 
            $u->getUserID()
         )
      );
      
      $ret['entries'] = Array();
      
      while ($row = $result->FetchRow()) {
         $ret['entries'][] = $row;
      }
      $ret['sumHours'] = $db->GetOne("SELECT sum(hours) FROM MeschProjectTimeEntries WHERE spentOn=str_to_date(?,'%Y-%m-%d') AND uID=?", array($_REQUEST['date'], $u->getUserID()));
      echo json_encode($ret);
      die();      
   }
   
   public function saveTimeEntries() {
      $db = Loader::db();
      $u = new User();
      $date = $_REQUEST['date'];
      $ret = array();
      
      foreach ($_REQUEST['entries'] as $entry) {
         if ($entry['timeEntryID'] == '') {         
            $db->Execute("INSERT INTO MeschProjectTimeEntries (projectID, uID, cID, hours, spentOn, createdOn, comment) VALUES (?,?,?,?,str_to_date(?,'%Y-%m-%d'),now(), ?)",
               array(
                  $entry['pID'],
                  $u->getUserID(),
                  $entry['cID'],
                  $entry['hours'],
                  $date,
                  $entry['comment']               
               )); 
               
            $ret['entries'][] = array('timeEntryID' => $db->Insert_ID());
         }
         else {
            $db->Execute("UPDATE MeschProjectTimeEntries SET projectID=?, uID=?, cID=?, hours=?, spentOn=str_to_date(?,'%Y-%m-%d'), comment=? WHERE timeEntryID=?",
               array(
                  $entry['pID'],
                  $u->getUserID(),
                  $entry['cID'],
                  $entry['hours'],
                  $date,
                  $entry['comment'],
                  $entry['timeEntryID']
               )); 
                        
            $ret['entries'][] = array('timeEntryID' => $entry['timeEntryID']);
         }
      }
      $ret['sumHours'] = $db->GetOne("SELECT sum(hours) FROM MeschProjectTimeEntries WHERE spentOn=str_to_date(?,'%Y-%m-%d') AND uID=?", array($date, $u->getUserID()));
      echo json_encode($ret);
      die();
   }
      
}
?>