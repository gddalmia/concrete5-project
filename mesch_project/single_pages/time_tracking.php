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
$(document).ready(function() {
   var projects = [<?php echo $projectArray; ?>];
   $(".mesch-project-list").autocomplete(projects, {
      autoFill: true,
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
   
   $(".mesch-project-list").result(function(event, data, formatted) {
      $.post("<?php echo View::url('/time_tracking/getIssues/')?>" + data.cID, function(data) {
      
      //alert($(this).parent().find(".mesch-issue-list:first"));
      
         $(".mesch-issue-list").autocomplete(data, {
            autoFill: true,
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
         
      }, "json")      
   });

});

// @TODO support for multiple rows, mesch-issue-list -> mesch-issue-list1, mesch-issue-list2
</script>

<table id="mesch-time-tracking">
   <tr>
      <td><input type="text" class="mesch-project-list"/></td>
      <td><input type="text" class="mesch-issue-list"/></td>
      <td><input type="text" class="mesch-hour"/></td>
   </tr>  
   <tr>
      <td><input type="text" class="mesch-project-list"/></td>
      <td><input type="text" class="mesch-issue-list"/></td>
      <td><input type="text" class="mesch-hour"/></td>
   </tr>  
   <tr>
      <td><input type="text" class="mesch-project-list"/></td>
      <td><input type="text" class="mesch-issue-list"/></td>
      <td><input type="text" class="mesch-hour"/></td>
   </tr>  
   <tr>
      <td><input type="text" class="mesch-project-list"/></td>
      <td><input type="text" class="mesch-issue-list"/></td>
      <td><input type="text" class="mesch-hour"/></td>
   </tr>  
   <tr>
      <td><input type="text" class="mesch-project-list"/></td>
      <td><input type="text" class="mesch-issue-list"/></td>
      <td><input type="text" class="mesch-hour"/></td>
   </tr>  
   <tr>
      <td><input type="text" class="mesch-project-list"/></td>
      <td><input type="text" class="mesch-issue-list"/></td>
      <td><input type="text" class="mesch-hour"/></td>
   </tr>  
   <tr>
      <td><input type="text" class="mesch-project-list"/></td>
      <td><input type="text" class="mesch-issue-list"/></td>
      <td><input type="text" class="mesch-hour"/></td>
   </tr>  
   <tr>
      <td><input type="text" class="mesch-project-list"/></td>
      <td><input type="text" class="mesch-issue-list"/></td>
      <td><input type="text" class="mesch-hour"/></td>
   </tr>  
   <tr>
      <td><input type="text" class="mesch-project-list"/></td>
      <td><input type="text" class="mesch-issue-list"/></td>
      <td><input type="text" class="mesch-hour"/></td>
   </tr>  
   <tr>
      <td><input type="text" class="mesch-project-list"/></td>
      <td><input type="text" class="mesch-issue-list"/></td>
      <td><input type="text" class="mesch-hour"/></td>
   </tr>  
   <tr>
      <td><input type="text" class="mesch-project-list"/></td>
      <td><input type="text" class="mesch-issue-list"/></td>
      <td><input type="text" class="mesch-hour"/></td>
   </tr>   
</table>