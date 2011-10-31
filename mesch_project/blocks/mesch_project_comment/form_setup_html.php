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

$uh = Loader::helper('form/user_selector');
$al = Loader::helper('concrete/asset_library');

$bf = null;
if ($fID > 0) { 
   $bf = File::getByID($fID);
}
   
echo '<div class="ccm-block-field-group">';
echo '<h2>' . t('Text') . '</h2>';
echo $form->textarea('text', $text, array('class' => 'ccm-advanced-editor'));
echo '</div>';

echo '<br/><br/>User:';
echo $uh->selectUser('uID', $uID);


echo '<br/><br/>File:';
echo $al->file('attachment', 'fID', t('Choose File'), $bf);


?>