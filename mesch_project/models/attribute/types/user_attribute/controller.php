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

defined('C5_EXECUTE') or die("Access Denied.");

class UserAttributeAttributeTypeController extends AttributeTypeController  {

	protected $searchIndexFieldDefinition = 'N 14 DEFAULT 0 NULL';

	public function getValue() {
		$db = Loader::db();
		$value = $db->GetOne("select value from atUserAttribute where avID = ?", array($this->getAttributeValueID()));
		return $value;	
	}

   public function getSearchIndexValue() { 
      return $this->getValue();
   }   
   
	public function searchForm($list) {
		$list->filterByAttribute($this->attributeKey->getAttributeKeyHandle(), $this->request('value'), '=');
		return $list;
	}
	
	public function search() {
		$formHelper = Loader::helper('form/user_selector');
		echo $formHelper->selectUser($this->field('value'), $this->request('value'), false);
	}
	
	public function form() {
      $value = -1;
		if (is_object($this->attributeValue)) {
			$value = $this->getAttributeValue()->getValue();
		}
      
      Loader::model('user_list');
      
      $userList = new UserList(); 
      $userList->sortBy('uName','asc');
      $userList = $userList->get();
      
      $ret = '';
      $ret .= "<select name=\"{$this->field('value')}\">";
      foreach ($userList as $user) {
         $selected = $user->getUserID() == $value ? ' selected="selected"' : '';
     
         $ret .= "<option {$selected} value=\"{$user->getUserID()}\">{$user->getUserName()}</option>";
      }
      $ret .= '</select>';
      
      echo $ret;     
      
	}
	
	public function validateForm($data) {
		return $data['value'] != 0;
	}

	public function saveValue($value) {
		$db = Loader::db();
		$db->Replace('atUserAttribute', array('avID' => $this->getAttributeValueID(), 'value' => $value), 'avID', true);
	}
	
	public function deleteKey() {
		$db = Loader::db();
		$arr = $this->attributeKey->getAttributeValueIDList();
		foreach($arr as $id) {
			$db->Execute('delete from atUserAttribute where avID = ?', array($id));
		}
	}
	
	public function saveForm($data) {
		$db = Loader::db();
		$this->saveValue($data['value']);
	}
	
	public function deleteValue() {
		$db = Loader::db();
		$db->Execute('delete from atUserAttribute where avID = ?', array($this->getAttributeValueID()));
	}
	
}