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

class ProjectPageTypeController extends Controller {

	private $issuePagelist;
   
   public function view() {
      global $c;
      
      Loader::model('page_list');
      Loader::model('user_list'); 
      
      $projectPageType = CollectionType::getByHandle('project');
      $issuePageType   = CollectionType::getByHandle('issue');
            
      $this->set('title','ss');
      
      // get all issues      
      $this->issuePagelist = new PageList();
      $this->issuePagelist->filterByCollectionTypeID($issuePageType->getCollectionTypeID());
      $this->issuePagelist->filterByParentID($c->getCollectionID());
      $this->issuePagelist->filterByAttribute('mesch_project_state','Closed','!=');
      
      //$this->issuePagelist->debug();
      
      $this->issuePagelist->setNameSpace('projectIssues');
      $this->issuePagelist->setItemsPerPage(20); // @TODO configuration for this..
         
      $issues = $this->issuePagelist->getPage();
      $this->set('issues', $issues); 
      $this->set('issueList', $this->issuePagelist); 
      
      // get all sub projects      
      $projectList = new PageList();
      $projectList->filterByCollectionTypeID($projectPageType->getCollectionTypeID());
      $projectList->filterByParentID($c->getCollectionID());
      $projects = $projectList->get();
      
      $this->set('projects', $projects);
   }
   
   public function getPagination() {
      return $this->issuePagelist->displayPaging(false, true);
   }
      
   public function new_issue() {
   	global $c, $u;
      
      $ip = Loader::helper('validation/ip');
      $txt = Loader::helper('text');

		if (!$ip->check()) {
			$this->set('invalidIP', $ip->getErrorMessage());			
			return;
		}      
      
      // create new child page for issue
		$ct = CollectionType::getByHandle('issue');

		$data = array();
		$data['cName']          = $txt->sanitize($_POST['subject']);
		$data['cDescription']   = $txt->sanitize($_POST['subject']);
		$data['uID']            = $u->getUserID();
	  
	  	$newPage = $c->add($ct, $data); 	      
      
      // set page attributes
      $collectionAttributes = CollectionAttributeKey::getList();
      
      foreach($collectionAttributes as $collectionAttribute) {
         if (array_key_exists($collectionAttribute->akID,$_REQUEST['akID'])) {
            $collectionAttribute->setAttribute($newPage, $_REQUEST[$collectionAttribute->akID]);              
         }        
      }      
      	  	
      // add block to new page to hold our issue description
	  	$data['text'] 		   = $txt->sanitize($_POST['text']);	// @TODO check this, it seems to cause problems with markdown
	  	$data['createdOn'] 	= date("Y-m-d H:i:s");
	  	$data['uID'] 	      = $u->getUserID();
           
	  	$newPage->addBlock(BlockType::getByHandle('mesch_project_comment'),'Issue Description',$data);	
           
      //
      $this->view();    
   }
}
?>