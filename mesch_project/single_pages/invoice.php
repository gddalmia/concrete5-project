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

$dfh = Loader::helper('date_formatter', 'mesch_project');
?>
<script type="text/javascript">

$(document).ready(function() {
   $(".mesch-project-time-issue").click(function() {
      $(".mesch-project-time[rel="+$(this).val()+"]").attr('checked', $(this).is(":checked"));
   });
});


</script>
<?php
if ($view == 'projectlist') {

   echo '<a target="_blank" href="file://///srv01/Mesch/0000 Administration/0000 Korrespondenz/0000 Vorlagen Briefschaften/Rechnung mesch gmbh.ott">Vorlage</a><br/>';
   echo '\\\\srv01\mesch\0000 Administration\0000 Korrespondenz\0000 Vorlagen Briefschaften\Rechnung mesch gmbh.ott';

	echo '<table id="mesch-project-billing" class="mesch-project-table" style="width:auto ! important;">';
	echo '<thead>
		  <tr>
			 <th>'.t('Project').'</th>
			 <th>'.t('Hours').'</th>
			 <th>'.t('Num of Invoices').'</th>
			 <th></th>
		  </tr>
	   </thead>';
	foreach ($unbilledProjects as $unbilledProject) {
	   echo "<tr>";
	   echo "<td>{$unbilledProject['cvName']}</td>";
	   echo "<td style=\"text-align:right;\">{$unbilledProject['hours']}</td>";
	   echo "<td style=\"text-align:right;\">{$unbilledProject['invoices']}</td>";
	   echo "<td>";
      echo "<a href=\"".View::url('/invoice','create',$unbilledProject['projectID'])."\">" . t('New Invoice') . '</a>';
      echo ' | ';
      echo "<a href=\"".View::url('/invoice','showinvoices',$unbilledProject['projectID'])."\">" . t('Show Invoices') . '</a>';
	   echo "</tr>";
	}

	echo '</table>';
}
if ($view == 'unbilledHours') {
   echo "<form method=\"post\" action=\"".$this->action('create_invoice')."\">";
   echo "<input type=\"hidden\" name=\"mesch-project-projectID\" value=\"{$projectID}\"/>";
	echo '<table id="mesch-project-billing" class="mesch-project-table" style="width:auto ! important;">';
	echo '<thead>
		  <tr>
			 <th></th>
			 <th>'.t('Person').'</th>
			 <th>'.t('Spent On').'</th>
			 <th>'.t('Hours').'</th>
			 <th>'.t('Issue').'</th>
			 <th>'.t('Comment').'</th>
		  </tr>
	   </thead>';
      
   $lastIssueName = 'does-not-exist';
   
	foreach ($unbilledHours as $unbilledHour) {
      
      if ($lastIssueName != $unbilledHour['issueName']) {
         echo "<tr style=\"background-color:silver;\">";
         echo "<th><input type=\"checkbox\" value=\"{$unbilledHour['issueID']}\" class=\"mesch-project-time-issue\"/></th>";
         echo "<th colspan=\"6\">{$unbilledHour['issueName']}</th>";
         echo "</tr>";
         $lastIssueName = $unbilledHour['issueName'];
      }
	   echo "<tr>";      
	   echo "<td><input name=\"mesch-project-time[]\" class=\"mesch-project-time\" rel=\"{$unbilledHour['issueID']}\" type=\"checkbox\" value=\"{$unbilledHour['timeEntryID']}\"/></td>";
	   echo "<td>{$unbilledHour['uName']}</td>";
	   echo "<td>" . $dfh->formatDate(strtotime($unbilledHour['spentOn']),DATE_APP_GENERIC_MDY) . "</td>";
	   echo "<td>{$unbilledHour['hours']}</td>";
	   echo "<td>{$unbilledHour['issueName']}</td>";
	   echo "<td>{$unbilledHour['comment']}</td>";
	   echo "</tr>";
	}

	echo '</table>';
   
   echo '<br/>';
   
   echo t('Invoice Name') . ' <input type="text" name="mesch-project-invoice-name"/>';
   echo '<button class="mesch-project-button">' . t('Create Invoice') . '</button>';
   
	echo '</form>';
}
if ($view == 'invoices') {

	echo '<table id="mesch-project-billing" class="mesch-project-table" style="width:auto ! important;">';
	echo '<thead>
		  <tr>
			 <th>'.t('Invoice').'</th>
			 <th>'.t('Created On').'</th>
			 <th></th>
		  </tr>
	   </thead>';
	foreach ($invoices as $invoice) {
	   echo "<tr>";
	   echo "<td>{$invoice['name']}</td>";
      echo "<td>" . $dfh->formatDate(strtotime($invoice['createdOn']),DATE_APP_GENERIC_MDY) . "</td>";	   
      echo "<td><a href=\"".View::url('/invoice','show',$invoice['invoiceID'])."\">" . t('Show Invoice') . '</a></td>';
	   echo "</tr>";
	}

	echo '</table>';
}
if ($view == 'showinvoice') {
   $sum = 0;
   
	echo '<table id="mesch-project-billing" class="mesch-project-table" style="width:auto ! important;">';
	echo '<thead>
		  <tr>
			 <th>'.t('Person').'</th>
			 <th>'.t('Spent On').'</th>
			 <th>'.t('Hours').'</th>
			 <th>'.t('Issue').'</th>
			 <th>'.t('Comment').'</th>
		  </tr>
	   </thead>';
      
	foreach ($times as $time) {     
	   echo "<tr>";      
	   echo "<td>{$time['uName']}</td>";
	   echo "<td>" . $dfh->formatDate(strtotime($time['spentOn']),DATE_APP_GENERIC_MDY) . "</td>";
	   echo "<td>{$time['hours']}</td>";
	   echo "<td>{$time['issueName']}</td>";
	   echo "<td>{$time['comment']}</td>";
	   echo "</tr>";
      
      $sum += $time['hours'];
	}
   
   $sum = sprintf("%10.2f", $sum);
   echo '<tfoot>';
   echo "<tr>
         <td></td>
         <td></td>
         <td>{$sum}</td>
         <td></td>
         <td></td>
      </tr>";
   echo '</tfoot>';

	echo '</table>';

   echo '<br/>';
   
   echo "<a href=\"".View::url('/invoice','show',$invoiceID,'excel')."\">";
   echo "<img src=\"".DIR_REL."/concrete/images/icons/excel.png\"/>";
}
?>