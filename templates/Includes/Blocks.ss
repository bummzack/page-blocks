<%--
This is an example template that shows how to list all blocks as sections (HTML5).
Sections should also have a title, so the title from the block will be used here.
--%>
<% require css('page-blocks/css/pageblocks-examples.css') %>
<% loop $Blocks %>
<section class="block $CSSClass">
	<h2>$Title</h2>
	<%-- $HTML generates the block markup by using the block-specific template --%>
	$HTML 
</section>
<% end_loop %>