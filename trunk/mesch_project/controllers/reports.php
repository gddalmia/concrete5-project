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

class ReportsController extends Controller {
   
   public function view() {
      $db = Loader::db();
      $hh = Loader::helper('html');
      Loader::model('collection_types');
      Loader::model('page_list');
     
      /*$reports = $db->GetAll('SELECT reportID, name FROM MeschProjectReports ORDER BY name');
      
      $this->set('reports', $reports);*/
      
      $methods = get_class_methods($this);
      $reportMethods = array();
      foreach ($methods as $method) {
         if (substr($method, 0, 6) == 'report') {
            $reportMethods[] = $method;
         }
      }
      $this->set('reports', $reportMethods);
   }

   
   public function show($reportID) {
      $db = Loader::db();
      Loader::library('3rdparty/adodb/tohtml.inc');
      
      $row = $db->GetRow('SELECT name, description, query FROM MeschProjectReports WHERE reportID=?',
         array($reportID));
      
      $reportOutput = "<h1>{$row['name']}</h1>";
      
      $rs = $db->Execute($row['query']);
      $reportOutput .= rs2html($rs, false, false, false, false);
      
      $this->set('reportOutput', $reportOutput);
   }
   
   protected function buildTable($adodbResult) {
      $ret = '';
      $headerAdded = false;
      while ($row = $adodbResult->FetchRow()) {
         // build header row if necessary
         if (!$headerAdded) {
            $ret = "<table class=\"mesch-project-table\" style=\"width:auto!important;\"><thead><tr>";
            foreach ($row as $rowKey => $rowValue) {
               $ret .= "<th>{$rowKey}</th>";
            }
            $ret .= "</tr></thead><tbody>";
            $headerAdded = true;
         }         
         
         // add data rows
         $ret .= "<tr>";
         foreach ($row as $rowKey => $rowValue) {
            $ret .= "<td>{$rowValue}</td>";
         }
         $ret .= "</tr>";
      }
      if ($headerAdded) {
         $ret .= "</tbody></table>";
      }
      
      if (array_key_exists('excel', $_GET)) {
         header('Content-type: application/ms-excel');
         header('Content-Disposition: attachment; filename=report.xls'); 
         echo utf8_decode($ret);
         die();              
      }
      return $ret;
   }
      
   public function report_current_month_for_current_user($action) {
      $u = new User();
      $db = Loader::db();      
         
      $result = $db->Execute('select cvName project_name,sum(hours) hours, substr(pp.cPath,2, locate(\'/\',pp.cPath,2)-2) company
         from MeschProjectTimeEntries mpte 
         inner join Collections c on mpte.projectID=c.cID
         inner join CollectionVersions cv on c.cID=cv.cID and cv.cvIsApproved=1
         inner join PagePaths pp on pp.cID=c.cID
         where uID=? and year(spentOn)=year(now()) and month(spentOn)=month(now())
         group by projectID,cvName, pp.cPath
         order by pp.cPath, cvName', array($u->getUserID()));

      $reportOutput = '<h2>Hours for current month</h2>' . $this->buildTable($result);
      
      $this->set('reportOutput', $reportOutput);
   }

   public function report_last_month_for_current_user($action) {
      $u = new User();
      $db = Loader::db();
      
      $result = $db->Execute('select cvName project_name,sum(hours) hours, substr(pp.cPath,2, locate(\'/\',pp.cPath,2)-2) company
         from MeschProjectTimeEntries mpte 
         inner join Collections c on mpte.projectID=c.cID
         inner join CollectionVersions cv on c.cID=cv.cID and cv.cvIsApproved=1
         inner join PagePaths pp on pp.cID=c.cID
         where uID=? and spentOn BETWEEN 
          ADDDATE(LAST_DAY(DATE_SUB(NOW(),INTERVAL 2 MONTH)), INTERVAL 1 DAY) 
          AND DATE_SUB(LAST_DAY(NOW()),INTERVAL 1 MONTH)
         group by projectID,cvName, pp.cPath
         order by pp.cPath, cvName', array($u->getUserID()));

      $reportOutput = '<h2>Hours for last month</h2>' . $this->buildTable($result);
      
      $this->set('reportOutput', $reportOutput);
   }

   public function report_hours_per_month_and_year($action) {
      $u = new User();
      $db = Loader::db();
      
      $result = $db->Execute('select year(spentOn) year,month(spentOn) month,sum(hours) hours
         from MeschProjectTimeEntries mpte 
         where uID=?
         group by year(spentOn),month(spentOn)
         order by year(spentOn),month(spentOn)', array($u->getUserID()));

      $reportOutput = '<h2>Hours per Month and Year</h2>' . $this->buildTable($result);

      $this->set('reportOutput', $reportOutput);
   }   
   
   public function report_unbilled_hours_from_last_year($action) {
      $u = new User();
      $db = Loader::db();
      
      $result = $db->Execute('SELECT cv.cvName project_name,year(spentOn) year, month(spentOn) month, sum(hours) hours FROM MeschProjectTimeEntries mpte
         INNER JOIN Collections c ON mpte.projectID=c.cID
         INNER JOIN CollectionVersions cv ON cv.cID=c.cID AND cv.cvIsApproved=1
         WHERE invoiceID IS NULL and year(spentOn)<year(now())
         GROUP BY cv.cvName,year(spentOn), month(spentOn)
         ORDER BY cv.cvName');

      $reportOutput = '<h2>Unbilled hours from last year</h2>' . $this->buildTable($result);

      $this->set('reportOutput', $reportOutput);
   }   
   
   public function report_hours_with_missing_project_or_issue($action) {
      $db = Loader::db();
      
      $result = $db->Execute('SELECT timeEntryID, projectID, mpte.cID, uID, hours, spentOn, comment FROM MeschProjectTimeEntries mpte 
         WHERE NOT EXISTS (SELECT 1 from Collections c1 WHERE c1.cID=mpte.projectID)
         OR (NOT EXISTS (SELECT 1 from Collections c1 WHERE c1.cID=mpte.cID) and mpte.cID > 0)');

      $reportOutput = '<h2>Hours with missing project/issue connection</h2>' . $this->buildTable($result);

      $this->set('reportOutput', $reportOutput);
   }      

   public function report_time_entries_last_30_days($action) {
      $u = new User();
      $db = Loader::db();
      
      $result = $db->Execute('SELECT cv.cvName project_name, mpte.hours, DATE_FORMAT( mpte.spentOn, \'%d.%m.%Y\') spent_on, mpte.comment FROM  MeschProjectTimeEntries mpte 
         INNER JOIN CollectionVersions cv ON mpte.projectID=cv.cID AND cv.cvIsApproved=1
         LEFT JOIN CollectionVersions cv2 ON mpte.cID=cv2.cvID AND cv2.cvIsApproved=1
         WHERE mpte.uID=? AND spentOn BETWEEN CURDATE() - INTERVAL 30 DAY AND CURDATE()
         ORDER BY cv.cID, spentOn', array($u->getUserID()));

      $reportOutput = '<h2>Time entries for last 30 days</h2>' . $this->buildTable($result);
      
      $this->set('reportOutput', $reportOutput);
   }   
   
   
}
?>