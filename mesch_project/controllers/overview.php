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

class OverviewController extends Controller {
   
   public function view() {
      $db = Loader::db();
      $hh = Loader::helper('html');
      Loader::model('collection_types');
      Loader::model('page_list');

      $projectPageType = CollectionType::getByHandle('project');
      $issuePageType = CollectionType::getByHandle('issue');
      
      $u = new User();
      
      $issueList = new PageList();
      $issueList->filterByCollectionTypeID($issuePageType->getCollectionTypeID());
      $issueList->filterByAttribute('mesch_project_assignee', $u->getUserID(),'=');
      $issueList->filterByAttribute('mesch_project_state','Closed','!=');
      $issueList->sortBy("CASE ak_mesch_project_priority
         WHEN 'Urgent' THEN 100
         WHEN 'High' THEN 70
         WHEN 'Normal' THEN 50
         WHEN 'Low' THEN 20
         END DESC, ak_mesch_project_update", "DESC");
      
      $issues = $issueList->get();
      $this->set('issues', $issues);
      
      // get latest activity
      $this->set('activities', $db->GetAll('SELECT cv.cID, cv.cvName, mpc.text, mpc.createdOn, u.uName
         FROM btMeschProjectComment mpc INNER JOIN CollectionVersionBlocks cvb ON mpc.bID=cvb.bID
         INNER JOIN CollectionVersions cv ON cv.cID=cvb.cID AND cv.cvID=cvb.cvID AND cv.cvIsApproved=1
         INNER JOIN Users u ON mpc.uID=u.uID
         ORDER BY cvb.bID DESC
         LIMIT 0, 20'));
      
   }
   
}
?>