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

class IssuePageTypeController extends Controller {
   public function view() {
      $hh = Loader::helper('html');
      
      $this->addHeaderItem($hh->css('jquery.ui.css'));
      $this->addHeaderItem($hh->css('ccm.calendar.css'));
      $this->addHeaderItem($hh->javascript('jquery.ui.js'));               
   }
   
   public function getAssignee($returnLink=true) {
      global $c;
      $assigneeID = $c->getAttribute('mesch_project_assignee');
      if ($returnLink) {
         if (!$assigneeID) return '';
         
         $ui = UserInfo::getByID($assigneeID);
         return $ui->getUserName();
      }
      else {
         return $assigneeID;
      }     
   }
   
   public function getPriority($returnLink=true) {
      global $c;
      $priority = $c->getAttribute('mesch_project_priority');
      if ($returnLink) {
         return $priority;
      }
      else {
         return $priority;
      }     
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

   
   public function update() {
      Loader::model('collection_attributes');
      
      global $u, $c;
      
      $txt = Loader::helper('text');
      
      $fID = $this->importFile();
      
      if ($_POST['text'] != '' || $fID != '') {
         $data = array();
         $data['text'] 		   = $txt->sanitize($_POST['text']);	// @TODO check this, it seems to cause problems with markdown
         $data['createdOn'] 	= date("Y-m-d H:i:s");
         $data['uID'] 	      = $u->getUserID();	
         $data['fID'] 	      = $fID;	
                     
         $block = $c->addBlock(BlockType::getByHandle('mesch_project_comment'),'Issue Comments',$data);	
      }
         
      // @TODO this code is executed after we already printed some attributes in the
      // head.. Refreshing the page is ugly as well, check this!
      $collectionAttributes = CollectionAttributeKey::getList();
      
      foreach($collectionAttributes as $collectionAttribute) {
         if (array_key_exists($collectionAttribute->akID,$_REQUEST['akID'])) {
            $collectionAttribute->setAttribute($c, $_REQUEST[$collectionAttribute->akID]);              
         }        
      }         

      $c->reindex();
                 
      $this->set('message', t('Issue updated'));      
   }
}
?>