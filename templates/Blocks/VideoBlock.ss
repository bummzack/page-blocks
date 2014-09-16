<%--
This is a simple template for the VideoBlock and creates a flexible/resizable container
that will keep its aspect-ratio.

Don't modify this file for customization. Instead copy it to your theme/templates folder or
to your templates folder within your project (eg. mysite/templates). If you intend to 
override all block templates, feel free to copy the whole "Blocks" folder from the module into
your template or project folder and then modify there.
--%>
<% if Media %>
<div class="video-box" style="padding-bottom: {$MediaHeightRatio}%;">
	$Media
</div>
<% end_if %>
