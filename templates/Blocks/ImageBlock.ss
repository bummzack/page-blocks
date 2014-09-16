<%--
This is a simple template for the ImageBlock.

Don't modify this file for customization. Instead copy it to your theme/templates folder or
to your templates folder within your project (eg. mysite/templates). If you intend to 
override all block templates, feel free to copy the whole "Blocks" folder from the module into
your template or project folder and then modify there.
--%>
<% loop $Images.Sort('SortOrder') %>
<figure>
	$Tag
	<figcaption>$Title</figcaption>
</figure>
<% end_loop %>