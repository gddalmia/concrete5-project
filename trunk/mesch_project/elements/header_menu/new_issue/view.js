$(function() {
   var href= $("#ccm-page-edit-nav-new_issue").attr("href");
   $("#ccm-page-edit-nav-new_issue").attr("href", href + "?parentPageID=" + CCM_CID);
  
   
	$("#ccm-page-edit-nav-new_issue").dialog();
});