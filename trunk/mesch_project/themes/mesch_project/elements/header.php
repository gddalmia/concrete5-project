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
?>
<!DOCTYPE html>
<html lang="de_DE">
<head>

   <!-- created by mesch web consulting & design GmbH <?php echo date('Y') ?> --> 
   <!-- consulting - webdesign - programmierungen - cms - support --> 
   <!-- www.mesch.ch --> 
     
   <meta charset="utf-8" />

   <title>Concrete5 Theme</title>
   
   <link rel="stylesheet" media="screen" type="text/css" href="<?php echo $this->getStyleSheet('main.css')?>" />
   <link rel="stylesheet" media="screen" type="text/css" href="<?php echo $this->getStyleSheet('typography.css')?>" />
   <script src="<?php echo $this->getThemePath()?>/modernizr-1.7.min.js"></script>
      
   <?php  Loader::element('header_required'); ?>

</head>

<body>

   <div id="wrapper">

      <nav id="navigation">
         <?php
         $nh = Loader::helper('navigation');
         
         $homePage = Page::getByID(1);
         foreach ($homePage->getCollectionChildrenArray(1) as $childPageId) {
            $child = Page::getByID($childPageId);
            
            if ($child->isSystemPage()) continue;
            
            echo "<a href=\"{$nh->getLinkToCollection($child)}\">{$child->getCollectionName()}</a>";
         }
         ?>         
      </nav>
      