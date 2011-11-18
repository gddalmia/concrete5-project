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

$ath = Loader::helper('attribute_tool', 'mesch_project'); 
$nh = Loader::helper('navigation'); 

$c = Page::getByID(intval($_REQUEST['parentPageID']));

// we don't want to create a sub issue, we want to create an
// issue beneath the project! If we're already on an issue
// we first get the parent (the project)
if ($c->getCollectionTypeHandle() == 'issue') {
   $c = Page::getByID($c->getCollectionParentID());
}

$formAction = $nh->getLinkToCollection($c) . 'new_issue/';

echo "<form method=\"post\" action=\"{$formAction}\" enctype=\"multipart/form-data\">
   <label>Subject</label> <input type=\"text\" name=\"subject\"/><br/>
   " . $ath->getAttributeForm(null,'mesch_project_assignee',true) . "<br/>
   " . $ath->getAttributeForm(null,'mesch_project_priority',true) . "<br/>
   " . $ath->getAttributeForm(null,'mesch_project_state',true) . "<br/>
   " . $ath->getAttributeForm(null,'mesch_project_due_date',true) . "<br/>
   <label>Message</label> <textarea style=\"width:500px;height:200px;\" name=\"text\"></textarea><br/>
   <label>Attachment</label> <input type=\"file\" name=\"attachment\"/><br/>
   <input type=\"submit\" value=\"".t('New Issue')."\"/>
</form>";
         
?>