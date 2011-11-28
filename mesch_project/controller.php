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

class MeschProjectPackage extends Package {

	protected $pkgHandle = 'mesch_project';
	protected $appVersionRequired = '5.4';
	protected $pkgVersion = '0.0.70';

	public function getPackageDescription() {
		return t("Installs the Mesch Project Management package.");
	}

	public function getPackageName() {
		return t("Mesch Project Management");
	}

	public function install() {
		$pkg = parent::install();
		Loader::model('collection_attributes');
      Loader::model('collection_types'); 
		Loader::model('attribute/categories/collection');
		Loader::model('single_page');		
            
      // install blocks
		BlockType::installBlockTypeFromPackage('mesch_project_comment', $pkg); 
      
      // install themes
		PageTheme::add('mesch_project', $pkg);
      
      // install attributes
		$attributeCollectionKeyCategory = AttributeKeyCategory::getByHandle('collection');
		$userAttribute = AttributeType::add('user_attribute', t('Page Selector'), $pkg);
		$attributeCollectionKeyCategory->associateAttributeKeyType(AttributeType::getByHandle('user_attribute'));      
      AttributeType::add('user_attribute', t('User Selector'), $pkg);      
      
      // create page types
      $projectPageType = CollectionType::getByHandle('project');
      if(!$projectPageType || !intval($projectPageType->getCollectionTypeID())){
         $projectPageType = CollectionType::add(array('ctHandle'=>'project','ctName'=>t('Project')),$pkg);
      }
      $issuePageType = CollectionType::getByHandle('issue');
      if(!$issuePageType || !intval($issuePageType->getCollectionTypeID())){
         $issuePageType = CollectionType::add(array('ctHandle'=>'issue','ctName'=>t('Issue')),$pkg);
      }
      $issuePageType = CollectionType::getByHandle('knowhow');
      if(!$issuePageType || !intval($issuePageType->getCollectionTypeID())){
         $issuePageType = CollectionType::add(array('ctHandle'=>'knowhow','ctName'=>t('Know How')),$pkg);
      }      
      
		$nat = AttributeType::getByHandle('number');
		$dat = AttributeType::getByHandle('date_time');      
		$tat = AttributeType::getByHandle('text');      
		$fat = AttributeType::getByHandle('image_file');      
		$sat = AttributeType::getByHandle('select');      
		$uat = AttributeType::getByHandle('user_attribute');      
      
      // add attributes for projects and issues
      $testAttribute = CollectionAttributeKey::getByHandle('mesch_project_priority');
		if(!is_object($testAttribute)) {
			$cak = CollectionAttributeKey::add($sat, array('akHandle' => 'mesch_project_priority', 'akName' => t('Issue Priority'), 'akIsSearchable' => false), $pkg);               
         SelectAttributeTypeOption::add($cak, t('Low'), 0);
         SelectAttributeTypeOption::add($cak, t('Normal'), 0);
         SelectAttributeTypeOption::add($cak, t('High'), 0);
         SelectAttributeTypeOption::add($cak, t('Urgent'), 0);
		}
      $testAttribute = CollectionAttributeKey::getByHandle('mesch_project_assignee');
		if(!is_object($testAttribute)) {
			CollectionAttributeKey::add($uat, array('akHandle' => 'mesch_project_assignee', 'akName' => t('Issue Assignee'), 'akIsSearchable' => false), $pkg);               
		}
      $testAttribute = CollectionAttributeKey::getByHandle('mesch_project_update');
		if(!is_object($testAttribute)) {
			CollectionAttributeKey::add($dat, array('akHandle' => 'mesch_project_update', 'akName' => t('Issue Last Update'), 'akIsSearchable' => false), $pkg);               
		}
      $testAttribute = CollectionAttributeKey::getByHandle('mesch_project_due_date');
		if(!is_object($testAttribute)) {
			CollectionAttributeKey::add($dat, array('akHandle' => 'mesch_project_due_date', 'akName' => t('Due Update'), 'akIsSearchable' => false), $pkg);               
		}
      $testAttribute = CollectionAttributeKey::getByHandle('mesch_project_estimated_time');
		if(!is_object($testAttribute)) {
			CollectionAttributeKey::add($nat, array('akHandle' => 'mesch_project_estimated_time', 'akName' => t('Estimated Time'), 'akIsSearchable' => false), $pkg);               
		}
      $testAttribute = CollectionAttributeKey::getByHandle('mesch_project_state');
		if(!is_object($testAttribute)) {
			$cak = CollectionAttributeKey::add($sat, array('akHandle' => 'mesch_project_state', 'akName' => t('State'), 'akIsSearchable' => false), $pkg);               
         SelectAttributeTypeOption::add($cak, t('New'), 0);
         SelectAttributeTypeOption::add($cak, t('Assigned'), 0);
         SelectAttributeTypeOption::add($cak, t('In Progress'), 0);
         SelectAttributeTypeOption::add($cak, t('Done'), 0);
         SelectAttributeTypeOption::add($cak, t('Closed'), 0);
		}
      
      // install single pages
		$sp1 = SinglePage::add('/overview', $pkg);
		$sp1->update(array('cName'=>t('Overview'), 'cDescription'=>t('Overview.'))); 
      
		$sp2 = SinglePage::add('/time_tracking', $pkg);
		$sp2->update(array('cName'=>t('Time Tracking'), 'cDescription'=>t('Time Tracking.'))); 

		$sp3 = SinglePage::add('/reports', $pkg);
		$sp3->update(array('cName'=>t('Reports'), 'cDescription'=>t('Reports.'))); 

		$sp4 = SinglePage::add('/invoice', $pkg);
		$sp4->update(array('cName'=>t('Invoice'), 'cDescription'=>t('Invoice.'))); 
      
		$sp5 = SinglePage::add('/cronjob', $pkg);
		$sp5->update(array('cName'=>t('Cronjob'), 'cDescription'=>t('Cronjob.'))); 
	}
   
   public function upgrade() {		
   
      $pkg = Package::getByHandle('mesch_project');
      
		Loader::model('collection_attributes');
      Loader::model('collection_types'); 
		Loader::model('single_page');		
            
		$nat = AttributeType::getByHandle('number');
		$dat = AttributeType::getByHandle('date_time');      
		$tat = AttributeType::getByHandle('text');     
		$fat = AttributeType::getByHandle('image_file');     
		$sat = AttributeType::getByHandle('select');   
		$uat = AttributeType::getByHandle('user_attribute'); 

		$sp5 = SinglePage::add('/cronjob', $pkg);
		$sp5->update(array('cName'=>t('Cronjob'), 'cDescription'=>t('Cronjob.'))); 
   }
		
	public function uninstall() {
      return parent::uninstall();      
   }
   
   public function on_start() {
     	/*$html = Loader::helper('html');
      $uh = Loader::helper('concrete/urls');
      $v = View::getInstance();
      
      if(version_compare(APP_VERSION,'5.4.1.1', '>')){
         $ihm = Loader::helper('concrete/interface/menu');

         $ihm->addPageHeaderMenuItem('new_project', t('New Project'), 'left', array(
            'dialog-title'    => t('New Project'),
            'href'            => $uh->getToolsUrl('new_project', 'mesch_project'),
            'dialog-on-open'  => "$(\'#ccm-page-edit-nav-new_project\').removeClass(\'ccm-nav-loading\')",
            'dialog-modal'    => "false",
            'dialog-width'    => '800',
            'dialog-height'   => "500",
            'class'           => 'dialog-launch'
         ), 'mesch_project');
         
         $ihm->addPageHeaderMenuItem('new_issue', t('New Issue'), 'left', array(
            'dialog-title'    => t('New Issue'),
            'href'            => $uh->getToolsUrl('new_issue', 'mesch_project'),
            'dialog-on-open'  => "$(\'#ccm-page-edit-nav-new_issue\').removeClass(\'ccm-nav-loading\')",
            'dialog-modal'    => "false",
            'dialog-width'    => '600',
            'dialog-height'   => "400",
            'class'           => 'dialog-launch'
         ), 'mesch_project');
      }*/
   
   }

}
?>