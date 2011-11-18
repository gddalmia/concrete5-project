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

$dtt = Loader::helper('form/date_time');
?>
<script type="text/javascript">

function getSelectedDate() {
   var selectedDate = $('#mesch-project-time-date').datepicker('getDate');
   return (selectedDate.getYear()+1900) + "-" + (selectedDate.getMonth()+1) + "-" + selectedDate.getDate();   
}

function loadEntries() {
   // clear everything
   $("#mesch-time-tracking input").val("").removeData("cID").removeData("pID").removeData("toBeInitialized");
   
   // fetch entries
   var date  = getSelectedDate();
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
            max: 500,
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
   
   var projects = [<?php echo $projectArray; ?>];
   $(".mesch-project-time-project-list").autocomplete(projects, {
      autoFill: false,
      max: 500,
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
      
      
      
      // build object structure
      var hasMissingValues = false;
      $("#mesch-time-tracking-form tbody tr").each(function(e,v) {
         var timeEntryID = $(this).find(".mesch-project-time-id").val();
         var pID = $(this).find(".mesch-project-time-project-list").data("cID");
         var cID = $(this).find(".mesch-project-time-issue-list").data("cID");
         var hours = $(this).find(".mesch-project-time-hour").val();
         var comment = $(this).find(".mesch-project-time-comment").val();
         
         // check if mandatory fields have a value         
         $(this).find("input").css({"background": "white"});

         if (pID != void null || cID != void null || hours != "" || comment != "") {
            if (pID == void null || cID == void null || hours == "") {
               $(this).find("input").css({"background": "#FFAAAA"});
               hasMissingValues = true;
            }
         }         
          
         if (pID != void null && cID != void null && hours != 0) {
            entries.push({"timeEntryID": timeEntryID, "pID": pID, "cID": cID, "hours": hours, "comment": comment});
         }
      });
      
      if (hasMissingValues) {
         $.jGrowl("<?php echo t('Time entries NOT saved due to missing values!') ?>");     
      }
      else {         
         //var data = {"date": $("#mesch-project-time-date").val(), "entries": entries};
         var data = {"date": getSelectedDate(), "entries": entries};
         var dataSaved = false;
         $.post("<?php echo View::url('/time_tracking/saveTimeEntries/')?>", data, function(response) {
         
            $("#mesch-project-time-sum-day").html(response.sumHours);
            
            var currentResponseID = 0;
            for (var timeEntryID in response.entries) {
               $("#mesch-project-time-id-" + currentResponseID++).val(response.entries[timeEntryID].timeEntryID);
            }
            
            dataSaved = true;
            $.jGrowl("<?php echo t('Time entries saved!') ?>");         
         
         }, "json");
         
         /*if (!dataSaved) {            
            $.jGrowl("<?php echo t('Time entries NOT saved, please contact development team!') ?>",{theme:'mesch-project-growl-error', sticky: true});

         }*/
      }
   });

});

</script>


<div style="float:left;padding-top:5px;">
   <?php
   print $dtt->date('mesch-project-time-date', null, true);
   ?>

   <script type="text/javascript">
   $(document).ready(function() {

      regExp = /(\d{4})-(\d{2})-(\d{2})/g;
      dateArray = regExp.exec("<?php echo $date?>"); 

      $('#mesch-project-time-date').datepicker('setDate', new Date(dateArray[1],dateArray[2]-1,dateArray[3]));
      
      
      $('#mesch-project-time-date').change(function() {
         
         window.location = "<?php echo View::url('/time_tracking') ?>" + getSelectedDate(); 
         
         // this is tricky because autocomplete stays initialized
         // couldn't find a method to remove existing autocomplete
         // complete reload seems easier..
         
         //loadEntries();
      });

      
      loadEntries();
   });
   </script>   
</div>
<div style="float:left;padding:5px;">
   <span id="mesch-project-time-sum-day"></span> <?php echo t('h')?>
</div>
<div style="float:left;padding:5px 0px 0px 295px">
   <button accesskey="s" class="mesch-project-button" id="mesch-project-time-save" title="<?php echo t('Save time entries (Alt+S)')?>"><?php echo t('Save')?></button>

</div>
<div style="clear:both;"></div>

<form id="mesch-time-tracking-form">
<div style="display:none;">
   <?php echo t('Date:')?> <input type="text" id="mesch-project-time-dateXXX" name="mesch-project-time-dateXXX"/>
</div>
<br/>
<table id="mesch-time-tracking" class="mesch-project-table" style="width:auto ! important;">
   <thead>
      <tr>
         <th><?php echo t('Project')?></th>
         <th><?php echo t('Issue')?></th>
         <th><?php echo t('Hours')?></th>
         <th><?php echo t('Comment')?></th>
      </tr>
   </thead>
   <tbody>
      <?php for($i=0;$i<20;$i++) { ?>
         <tr class="mesch-project-time-row" id="mesch-project-time-row-<?php echo $i?>">
            <td>
               <input type="hidden" name="timeEntryID[]" class="mesch-project-time-id" id="mesch-project-time-id-<?php echo $i?>"/>
               <input type="text" name="project[]" class="mesch-project-time-project-list"/>
            </td>
            <td>
               <input type="text" name="issue[]" class="mesch-project-time-issue-list"/>
            </td>
            <td>
               <input type="text" name="hours[]" class="mesch-project-time-hour"/>
            </td>
            <td>
               <input type="text" name="comments[]" class="mesch-project-time-comment"/>
            </td>
         </tr>  
      <?php } ?>
   </tbody>
</table>

</form>