<script type="text/javascript" src="../js/jquery-ui-1.7.2.site.min.js" ></script>
<link href="../css/admin/jquery-ui-1.7.2.custom.css" rel="stylesheet" type="text/css" />
{literal}
<style type="text/css">
		#elements {margin-left: 5px; margin-top: 1px}
		#elements li { font-size: 12px;float: left; width: 180px; padding: 0.4em; margin: 0 0.4em 0.4em 0; text-align: center; display: block; list-style: none; border: 1px solid #CDCDCD;}
		#elements li p { margin: 0 0 0.4em; cursor: move; }

		#workarea li { float: left; width: 96px; padding: 0.4em; margin: 0 0.4em 0.4em 0; text-align: center; display: block; list-style: none; border: 1px solid #CDCDCD;}
		#trash h4 { line-height: 16px; margin: 0 0 0.4em; }
		#trash h4 .ui-icon { float: left; }
		#trash .gallery h5 { display: none; }
</style>
<script type="text/javascript">
	
	$(document).ready( function() {
		var $elements = $('#elements'), $workarea = $('#workarea');

		$('li',$elements).draggable({
			cancel: 'a.ui-icon',// clicking an icon won't initiate dragging
			revert: 'invalid', // when not dropped, the item will revert back to its initial position
			containment: [20, 155, 1150, 750], // stick to demo-frame if present
			helper: 'clone',
			cursor: 'move'
		});

		$workarea.droppable({
			accept: '#elements li',
			activeClass: 'ui-state-highlight',
			drop: function(ev, ui) {
				deleteImage(ui.draggable);
				
			}
		});

		$elements.droppable({
			accept: '#workarea li',
			activeClass: 'custom-state-active',
			drop: function(ev, ui) {
				recycleImage(ui.draggable);
			}
		});

		var recycle_icon = '<a href="link/to/recycle/script/when/we/have/js/off" title="Recycle this image" class="ui-icon ui-icon-refresh">Recycle image</a>';
		function deleteImage($item) {
			$item.fadeOut(function() {
				var $list = $('ul',$workarea).length ? $('ul',$workarea) : $('<ul class="gallery ui-helper-reset"/>').appendTo($workarea);

				$item.find('a.ui-icon-trash').remove();
				$item.append(recycle_icon).appendTo($list).fadeIn();
				$item.draggable('option', 'revert', false);
				$item.draggable('option', 'snap', true);
				$item.draggable('option', 'snapTolerance', 5);
				$item.draggable('option', 'grid', [5,5]);
				$item.draggable('option', 'helper', 'original');
				$item.draggable('option', 'containment', '#workarea');
				$item.resizable({containment: '#workarea'});

			});
		}

		var trash_icon = '<a href="link/to/trash/script/when/we/have/js/off" title="Delete this image" class="ui-icon ui-icon-trash">Delete image</a>';
		function recycleImage($item) {
			$item.fadeOut(function() {
				$item.find('a.ui-icon-refresh').remove();
				$item.attr({style: ''}).append(trash_icon).appendTo($elements).fadeIn();
				$item.draggable('option', 'revert', 'invalid');
				$item.draggable('option', 'snap', false);
				$item.draggable('option', 'snapTolerance', 20);
				$item.draggable('option', 'grid', false);
				$item.draggable('option', 'helper', 'clone');
				$item.draggable('option', 'containment', '#tableContent');
				$item.resizable('destroy');
			});
		}

		$('ul.gallery > li').click(function(ev) {
			var $item = $(this);
			var $target = $(ev.target);

			if ($target.is('a.ui-icon-trash')) {
				deleteImage($item);
			} else if ($target.is('a.ui-icon-refresh')) {
				recycleImage($item);
			}

			return false;
		});


		

	});
	{/literal}
</script>	
<div id="tableContent" style="">
	<div style="float: left; width: {$siteWidth}px;">
		<table class="header"  style="width: 990px" cellspacing="1" cellpadding="0"><tr><td class="header">Szablon strony</td></tr></table>
		<div id="workarea" style="margin-top: 1px; border: 1px solid #CDCDCD;height: 600px; width: {$siteWidth}px; position: relative;"></div>
	</div>
	<div style="float: left; width: 200px;">
		<table style="margin-left: 5px;" class="header" cellspacing="1" cellpadding="0">
			<tr>
				<td><a title="Dodaj nowy element" href="{$wwwPatchPanel}strona,1,nowyElement.html"><div style="float: left;"><img style="border: 0pt none ;" alt="" src="../img/admin/add.gif"/></div><div style="float: left;padding-left: 5px;">dodaj nowy element</div></a></td>
			</tr>
			<tr>
				<td class="header" style="width: 200px; margin-left: 5px;">Dostępne elementy</td>
			</tr>
		</table>
		<ul id="elements" class="gallery ui-helper-reset ui-helper-clearfix">
			<li><p>Default (snap: true), snaps to all other draggable elements</p></li>
			<li><p>I only snap to the big box</p></li>
			<li><p>I only snap to the outer edges of the big box</p></li>
			<li><p>I snap to a 20 x 20 grid</p></li>
		</ul>
	</div>
	<br style="clear:both;" />
</div>