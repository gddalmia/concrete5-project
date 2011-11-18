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

class AccessData extends Object {
   private $data;
   
   public function AccessData($data) {
      $this->data = $data;
   }
   
   public function __get($key) {
      return $this->data[$key];
   }   
   
	public static function getByID($aID) {
      $db = Loader::db();
      $row = $db->GetRow('SELECT accessdataTypeId, cID, name, userName, userPassword, serverName, databaseName FROM MeschProjectAccessdata WHERE accessdataId=?', array($aID));
      $ad = new AccessData($row);
      return $ad;
	}
   
   public static function getByCollectionID($cID) {
      $db = Loader::db();
      $ret = array();
      $table = $db->GetAll('SELECT accessdataId, cID, accessdataTypeId, name, userName, userPassword, serverName, databaseName FROM MeschProjectAccessdata WHERE cID=?', array($cID));
      foreach ($table as $row) {
         $ret[] = new AccessData($row);
      }
      return $ret;
   }
   
   public function getActionLink() {
      switch ($this->data['accessdataTypeId']) {
         case 'ftp':
            return "<a target=\"_blank\" href=\"ftp://{$this->data['userName']}:{$this->data['userPassword']}@{$this->data['serverName']}\">Go</a>";
            break;
         case 'c5_user':
            if ($this->data['serverName'] == '') {
               return '<span title="Server Name ist leer!" style="color:red;">Go</span>';
            }
            else {
               return "<a href=\"\" class=\"mesch-management-go-c5\">Go</a> <form method=\"post\" target=\"_blank\" action=\"http://{$this->data['serverName']}/index.php/login/do_login/\"><input type=\"hidden\" name=\"uName\" value=\"{$this->data['userName']}\"/><input type=\"hidden\" name=\"uPassword\" value=\"{$this->data['userPassword']}\"/></form>";        
            }
            break;
      }
   }
   
}
?>