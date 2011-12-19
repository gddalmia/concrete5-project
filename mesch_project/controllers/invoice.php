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

class InvoiceController extends Controller {
   
   public function view() {
      $hh = Loader::helper('html');
      Loader::model('collection_types');
      Loader::model('page_list');
      $db = Loader::db();
      
      $unbilledProjects = $db->GetAll('SELECT 
         projectID, 
         sum(if(invoiceID is null,hours,0)) hours, 
         cv.cvName,
         (SELECT COUNT(*) FROM MeschProjectInvoices m WHERE m.projectID=mpte.projectID) invoices
      FROM MeschProjectTimeEntries mpte INNER JOIN
      CollectionVersions cv ON mpte.projectID=cv.cID AND cv.cvIsApproved=1
       GROUP BY projectID, cv.cvName
       ORDER BY cv.cvName');
      $this->set('unbilledProjects', $unbilledProjects);
	  $this->set('view', 'projectlist');
      
   }
   
   public function create($projectID) {
      $db = Loader::db();
      $unbilledHours = $db->GetAll('SELECT mpte.timeEntryID, u.uName, hours, spentOn, comment, cv1.cvName projectName, cv2.cID issueID, cv2.cvName issueName FROM MeschProjectTimeEntries mpte 
         INNER JOIN CollectionVersions cv1 ON mpte.projectID=cv1.cID AND cv1.cvIsApproved=1
         LEFT JOIN CollectionVersions  cv2 ON mpte.cID=cv2.cID AND cv2.cvIsApproved=1
         INNER JOIN Users u ON u.uID=mpte.uID
         WHERE mpte.projectID=? AND mpte.invoiceID IS NULL
         ORDER BY cv2.cID, spentOn', array(intval($projectID)));

      $this->set('projectID', $projectID);
      $this->set('unbilledHours', $unbilledHours);
      
	   $this->set('view', 'unbilledHours');
   }
   
   public function showinvoices($projectID) {
      $db = Loader::db();
      $invoices = $db->GetAll('SELECT invoiceID, name, createdOn FROM MeschProjectInvoices 
         WHERE projectID=?
         ORDER BY createdOn DESC',
         array($projectID));

      $this->set('invoices', $invoices);
	   $this->set('view', 'invoices');
   }
   
   public function show($invoiceID, $format='html') {
      $db = Loader::db();
      
      $times = $db->GetAll('SELECT mpte.timeEntryID, u.uName, hours, spentOn, comment, cv1.cvName projectName, cv2.cID issueID, cv2.cvName issueName FROM MeschProjectTimeEntries mpte 
         INNER JOIN CollectionVersions cv1 ON mpte.projectID=cv1.cID AND cv1.cvIsApproved=1
         LEFT JOIN CollectionVersions  cv2 ON mpte.cID=cv2.cID AND cv2.cvIsApproved=1
         INNER JOIN Users u ON u.uID=mpte.uID
         WHERE mpte.invoiceID=?
         ORDER BY cv2.cID, spentOn', array($invoiceID));
         
      if ($format == 'excel') {
         // TODO, replace this with something smarter like http://phpexcel.codeplex.com/
         
         header("Content-Type: application/vnd.ms-excel");
         header("Content-Disposition: inline; filename=\"invoice_{$invoiceID}.xls\"");

         echo '<table border="1">';
         echo '<tr>';
         echo "<th>".t('Issue Name')."</th>";
         echo "<th>".t('Person')."</th>";
         echo "<th>".t('Hours')."</th>";
         echo "<th>".t('Spent On')."</th>";
         echo "<th>".t('Comment')."</th>";
         echo '</tr>';
         foreach ($times as $time) {
            $time['issueName'] = utf8_decode($time['issueName']);
            $time['comment'] = utf8_decode($time['comment']);
            
            echo '<tr>';
            echo "<td>{$time['issueName']}</td>";
            echo "<td>{$time['uName']}</td>";
            echo "<td>{$time['hours']}</td>";
            echo "<td>{$time['spentOn']}</td>";
            echo "<td>{$time['comment']}</td>";
            echo '</tr>';
         }
         echo '</table>';
         die();
      }
      
      $this->set('invoiceID', $invoiceID);
      $this->set('times', $times);
	   $this->set('view', 'showinvoice');         
   }
   
   public function create_invoice() {
      $db = Loader::db();
      $db->Execute('INSERT INTO MeschProjectInvoices (projectID, name, createdOn) VALUES (?,?,now())',   
         array($_REQUEST['mesch-project-projectID'], $_REQUEST['mesch-project-invoice-name']));
      
      $invoiceID = $db->Insert_ID();
      
      foreach ($_REQUEST['mesch-project-time'] as $timeEntryID) {
         $db->Execute('UPDATE MeschProjectTimeEntries SET invoiceID=? WHERE timeEntryID=?',
            array($invoiceID, $timeEntryID));
      }
      
      $this->show($invoiceID);
   }
   
}
?>