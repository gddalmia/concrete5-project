$(document).ready(function() {
   $(".mesch-project-project-filter").keyup(function(e) {
      var filterText = $(this).val().toLowerCase();
      $(".mesch-project-project-filter").parent().find("li").each(function(e,v) {
         var rowText = $(this).text().toLowerCase();
         
         if (rowText.indexOf(filterText) > -1) {
            $(this).show();
         }
         else {
            $(this).hide();
         }
      });
   });   
});