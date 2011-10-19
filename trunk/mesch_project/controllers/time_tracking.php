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
      $projects = $projectList->get();
      $this->set('projects', $projects);
      
      $projectArray = array();
      foreach ($projects as $project) {
         $projectArray[] = "{cID:'{$project->getCollectionID()}',name:'{$project->getCollectionName()}'}";
      }
      
      $this->set('projectArray', join($projectArray, ','));
      
      $this->addHeaderItem($hh->css('jquery.autocomplete.css', 'mesch_project'));
      $this->addHeaderItem($hh->javascript('jquery.autocomplete.min.js', 'mesch_project'));
   }
   
   public function getIssues($cID) {
   
      Loader::model('collection_types'); 
      Loader::model('page_list');      
      
      $issuePageType   = CollectionType::getByHandle('issue');
      
      $issueList = new PageList();
      $issueList->filterByCollectionTypeID($issuePageType->getCollectionTypeID());
      $issueList->sortByName();
      $issues = $issueList->get();
      $this->set('issues', $issues);
      
      $issueArray = array();
      foreach ($issues as $issue) {
         $issueArray[] = array("cID" => $issue->getCollectionID, "name" => $issue->getCollectionName());
      }     
      
      echo json_encode($issueArray);
      die();
   }
      
}
?>