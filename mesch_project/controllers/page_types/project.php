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
   private $accessDataTypes;
   
   public function view() {
      global $c;
      
      $hh = Loader::helper('html');
      
      Loader::model('page_list');
      Loader::model('user_list'); 
      Loader::model('access_data', 'mesch_project'); 
      Loader::model('access_data_type', 'mesch_project'); 
      
      $projectPageType = CollectionType::getByHandle('project');
      $issuePageType   = CollectionType::getByHandle('issue');
            
      $this->set('title','ss');
      
      // get all issues      
      $this->issuePagelist = new PageList();
      $this->issuePagelist->filterByCollectionTypeID($issuePageType->getCollectionTypeID());
      $this->issuePagelist->filterByParentID($c->getCollectionID());
      $this->issuePagelist->filterByAttribute('mesch_project_state','Closed','!=');
            
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
      
      // get access data
      $this->accessDataTypes = AccessDataType::getList();
      $this->set('access_data', AccessData::getByCollectionID($c->getCollectionID()));
      $this->set('access_data_types', $this->accessDataTypes);

      $this->addHeaderItem($hh->css('jquery.ui.css'));
      $this->addHeaderItem($hh->css('ccm.calendar.css'));
      $this->addHeaderItem($hh->javascript('jquery.ui.js'));      
   }
   
   public function getAccessDataTypeForm($selectedValue='') {
      $ret = '<select name="mesch-project-access-data-type" id="mesch-project-access-data-type">';
      foreach ($this->accessDataTypes as $accessDataType) {
         $ret .= "<option value=\"{$accessDataType->accessdataTypeId}\">{$accessDataType->description}</option>";
      }
      $ret .= '</select>';
      return $ret;
   }  
   
   public function getPagination() {
      return $this->issuePagelist->displayPaging(false, true);
   }
      
   protected function importFile() {
      $fID = 0;
      if (is_uploaded_file($_FILES['attachment']['tmp_name'])) {
         Loader::library('file/importer');
         $fi = new FileImporter();

         $resp = $fi->import($_FILES['attachment']['tmp_name'], $_FILES['attachment']['name']);
         if (!$resp instanceof FileVersion) {
            switch ($resp) {
               case FileImporter::E_FILE_INVALID_EXTENSION:
                  $this->set('message',t('File extension of attachment not allowed!'));
                  return;
                  break;
               default:
                  $this->set('message',t('Error during upload of attachment.'));
                  return;
                  break;
            }
         }
         else {
            $fID = $resp->getFileID();
         }
      }
      return $fID;
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
      
      $fID = $this->importFile();
      	  	
      // add block to new page to hold our issue description
	  	$data['text'] 		   = $txt->sanitize($_POST['text']);	// @TODO check this, it seems to cause problems with markdown
	  	$data['createdOn'] 	= date("Y-m-d H:i:s");
	  	$data['uID'] 	      = $u->getUserID();
	  	$data['fID'] 	      = $fID;
           
	  	$newPage->addBlock(BlockType::getByHandle('mesch_project_comment'),'Issue Description',$data);	
           
      //
      $this->view();    
   }
   
   public function new_access_data() {
      global $c;
      
      $db = Loader::db();
      
      $db->Execute('INSERT INTO MeschProjectAccessdata (cID,accessdataTypeId,name,userName,userPassword,serverName,databaseName) VALUES (?,?,?,?,?,?,?)',
         array(
            $c->getCollectionID(),
            $_REQUEST['mesch-project-access-data-type'],
            $_REQUEST['mesch-project-access-data-name'],
            $_REQUEST['mesch-project-access-data-username'],
            $_REQUEST['mesch-project-access-data-password'],
            $_REQUEST['mesch-project-access-data-server'],
            $_REQUEST['mesch-project-access-data-database']
         ));
      
      $this->set('message', t('Access data added'));
      $this->view();
   }
   
   public function delete_access_data($accessdataId) {
      $db = Loader::db();
      
      $db->Execute('DELETE FROM MeschProjectAccessdata WHERE accessdataId=?', array($accessdataId));
      
      $this->set('message', t('Access data removed'));
      $this->view();
   }
}
?>