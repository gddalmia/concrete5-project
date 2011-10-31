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


?>
<script type="text/javascript">


function loadEntries() {
   var date = $("#mesch-project-time-date").val();

   var data = {"date": date};
   $.post("<?php echo View::url('/time_tracking/getTimeEntries/')?>", data, function(response) {
      var currentResponseID = 0;
      for (var timeEntryID in response.entries) {
         var $row = $("#mesch-project-time-row-" + currentResponseID++);
         
         $row.find(".mesch-project-time-id").val(response.entries[timeEntryID].timeEntryID);
         
         $row.find(".mesch-project-time-project-list").data("cID", response.entries[timeEntryID].projectID);
         
         $row.find(".mesch-project-time-issue-list").data("cID", response.entries[timeEntryID].cID);
         $row.find(".mesch-project-time-issue-list").data("pID", response.entries[timeEntryID].projectID);
         $row.find(".mesch-project-time-issue-list").data("toBeInitialized", 1);          
         
         $row.find(".mesch-project-time-project-list").val(response.entries[timeEntryID].projectName);
         $row.find(".mesch-project-time-issue-list").val(response.entries[timeEntryID].issueName);
         $row.find(".mesch-project-time-hour").val(response.entries[timeEntryID].hours);
         $row.find(".mesch-project-time-comment").val(response.entries[timeEntryID].comment);
         
      }
      
      initializeIssueLists();
      
      $("#mesch-project-time-sum-day").html(response.sumHours);
      
   }, "json");
}
 
function initializeIssueLists() {
   // build list of projects to avoid unnecessary AJAX calls
   var projectIssuesToLoad = [];
   $(".mesch-project-time-issue-list:data(toBeInitialized=1)").each(function(e,v) {
      projectIssuesToLoad.push($(this).data("pID"));      
   });
   
   projectIssuesToLoad = jQuery.unique(projectIssuesToLoad);
   
   // get issues for each project once
   for (var key in projectIssuesToLoad) {
      var pID = projectIssuesToLoad[key];
      
      $.post("<?php echo View::url('/time_tracking/getIssues/')?>" + pID, function(data) {
         
         $(".mesch-project-time-issue-list:data(toBeInitialized=1):data(pID="+pID+")").autocomplete(data.entries, {
            autoFill: false,
            minChars: 0,
            formatItem: function(row, i, max) {
               return row.name;
            },
            formatMatch: function(row, i, max) {
               return row.name;
            },
            formatResult: function(row) {
               return row.name;
            }   
         });
         
         $(".mesch-project-time-issue-list:data(toBeInitialized=1):data(pID="+pID+")").removeData("toBeInitialized");
                  
      }, "json"); 
   }
}


$(document).ready(function() {

   loadEntries();
   
   var projects = [<?php echo $projectArray; ?>];
   $(".mesch-project-time-project-list").autocomplete(projects, {
      autoFill: false,
      minChars: 0,
		formatItem: function(row, i, max) {
			return row.name;
		},
		formatMatch: function(row, i, max) {
			return row.name;
		},
		formatResult: function(row) {
			return row.name;
		}   
   });
   
   $(".mesch-project-time-project-list").result(function(event, data, formatted) {
      
      $(this).data("cID", data.cID);

      var $issueList = $(this).parent().parent().find(".mesch-project-time-issue-list:first");
      
      $issueList.data("pID", data.cID);      
      $issueList.data("toBeInitialized", 1);      
      $issueList.val("");
      
      initializeIssueLists();     
   });
   
   $(".mesch-project-time-issue-list").result(function(event, data, formatted) {
      $(this).data("cID", data.cID);
   });
   
   $("#mesch-project-time-save").click(function(event) {
      event.preventDefault();
      
      var entries = new Array();
      
      $("#mesch-time-tracking-form tbody tr").each(function(e,v) {
         var timeEntryID = $(this).find(".mesch-project-time-id").val();
         var pID = $(this).find(".mesch-project-time-project-list").data("cID");
         var cID = $(this).find(".mesch-project-time-issue-list").data("cID");
         var hours = $(this).find(".mesch-project-time-hour").val();
         var comment = $(this).find(".mesch-project-time-comment").val();
                  
         if (pID != void null && cID != void null && hours != 0) {
            entries.push({"timeEntryID": timeEntryID, "pID": pID, "cID": cID, "hours": hours, "comment": comment});
         }
      });
      var data = {"date": "2011-10-31", "entries": entries};
      $.post("<?php echo View::url('/time_tracking/saveTimeEntries/')?>", data, function(response) {
         $("#mesch-project-time-sum-day").html(response.sumHours);
         
         var currentResponseID = 0;
         for (var timeEntryID in response.entries) {
            $("#mesch-project-time-id-" + currentResponseID++).val(response.entries[timeEntryID].timeEntryID);
         }
         
         $.jGrowl("<?php echo t('Time entries saved!') ?>");         
      
      }, "json");
      
   });

});

</script>

<form id="mesch-time-tracking-form">
<?php echo t('Date:')?> <input type="text" id="mesch-project-time-date" name="date" readonly="readonly" value="<?php echo date('Y-m-d') ?>"/>
<br/>
<table id="mesch-time-tracking">
   <thead>
      <tr>
         <th colspan="2"><?php echo t('Project')?></th>
         <th><?php echo t('Issue')?></th>
         <th><?php echo t('Hours')?></th>
         <th><?php echo t('Comment')?></th>
      </tr>
   </thead>
   <tbody>
      <?php for($i=0;$i<20;$i++) { ?>
         <tr id="mesch-project-time-row-<?php echo $i?>">
            <td><input type="hidden" name="timeEntryID[]" class="mesch-project-time-id" id="mesch-project-time-id-<?php echo $i?>"/></td>
            <td><input type="text" name="project[]" class="mesch-project-time-project-list"/></td>
            <td><input type="text" name="issue[]" class="mesch-project-time-issue-list"/></td>
            <td><input type="text" name="hours[]" class="mesch-project-time-hour"/></td>
            <td><input type="text" name="comments[]" class="mesch-project-time-comment"/></td>
         </tr>  
      <?php } ?>
   </tbody>
   <tfoot>
      <td></td>
      <td></td>
      <td></td>
      <td id="mesch-project-time-sum-day"></td>
      <td></td>
   </tfoot>
</table>

<a href="" id="mesch-project-time-save">Save</a>

</form>